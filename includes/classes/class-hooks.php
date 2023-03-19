<?php
/**
 * Class Hooks
 *
 * @author Pluginbazar
 */

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TINYPRESS_Hooks' ) ) {
	/**
	 * Class TINYPRESS_Hooks
	 */
	class TINYPRESS_Hooks {

		protected static $_instance = null;


		/**
		 * TINYPRESS_Hooks constructor.
		 */
		function __construct() {
			add_action( 'init', array( $this, 'register_everything' ) );
			add_action( 'admin_menu', array( $this, 'links_log' ) );
			add_filter( 'post_updated_messages', array( $this, 'change_url_update_message' ) );

			add_action( 'rest_api_init', array( $this, 'register_api_endpoints' ) );
			add_action( 'admin_bar_menu', array( $this, 'handle_admin_bar_menu' ), 9999, 1 );

			add_action( 'wp_ajax_tiny_admin_popup', array( $this, 'tiny_admin_popup_form' ) );
		}


		function tiny_admin_popup_form() {
			$_form_data = isset( $_POST['form-data'] ) ? $_POST['form-data'] : '';

			parse_str( $_form_data, $form_data );

			$tiny_lebel        = Utils::get_args_option( 'tiny-label', $form_data );
			$tiny_target_url   = Utils::get_args_option( 'tiny-target-url', $form_data );
			$tiny_short_string = Utils::get_args_option( 'tiny-short-string', $form_data );

			$args = array(
				'post_type'  => 'tinypress_link',
				'post_title' => $tiny_lebel,
				'meta_input' => array(
					'meta_value' => $tiny_target_url,
					'tiny_slug'  => $tiny_short_string,
				),
			);

			$post_id = wp_insert_post( $args );
			$_url    = admin_url( "post.php?post={$post_id}&action=edit" );

			wp_send_json_success( $_url );
		}


		/**
		 * @param WP_REST_Request $request
		 *
		 * @return int|WP_Error|WP_REST_Response
		 */
		function handle_api_create( WP_REST_Request $request ) {
			$data     = $request->get_body_params();
			$auth_key = Utils::get_args_option( 'auth_key', $data );

			if ( $auth_key != Utils::get_option( 'tinypress_auth_key_chrome' ) ) {
				return new WP_REST_Response(
					array(
						'short_url' => '',
						'success'   => false,
						'message'   => esc_html__( 'Invalid authentication key.', 'tinypress' ),
					)
				);
			}

			$short_url = tinypress_create_shorten_url( array(
				'post_title'  => wp_strip_all_tags( Utils::get_args_option( 'post_title', $data ) ),
				'target_url'  => Utils::get_args_option( 'target_url', $data ),
				'tiny_slug'   => Utils::get_args_option( 'tiny_slug', $data ),
				'redirection' => Utils::get_args_option( 'redirection', $data ),
				'notes'       => Utils::get_args_option( 'notes', $data ),
			) );

			if ( is_wp_error( $short_url ) ) {
				return new WP_REST_Response(
					array(
						'short_url' => '',
						'success'   => false,
						'message'   => $short_url->get_error_message(),
					)
				);
			}

			return new WP_REST_Response(
				array(
					'short_url' => $short_url,
					'success'   => true,
					'message'   => esc_html__( 'Short url generated successfully and copied to clipboard.', 'tinypress' ),
				)
			);
		}


		/**
		 * @return void
		 */
		function register_api_endpoints() {
			register_rest_route( 'tinypress/api/v1', '/create/', array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_api_create' ),
				'permission_callback' => '__return_true',
			) );
		}


		/**
		 * Update post update message
		 *
		 * @param $messages
		 *
		 * @return mixed
		 */
		function change_url_update_message( $messages ) {
			$post_messages = Utils::get_args_option( 'post', $messages );
			$post_messages = array_map( function ( $message ) {
				return str_replace( 'Post', 'TinyPress Link', $message );
			}, $post_messages );

			$messages['post'] = $post_messages;

			return $messages;
		}

		/**
		 * Add nodes to WP Admin Bar
		 *
		 * @param WP_Admin_Bar $wp_admin_bar
		 */
		function handle_admin_bar_menu( \WP_Admin_Bar $wp_admin_bar ) {
			$wp_admin_bar->add_node(
				array(
					'id'     => 'tinypress-admin-bar',
					'title'  => esc_html__( 'TinyPress', 'tinypress' ),
					'href'   => admin_url( '#' ),
					'parent' => false,
				)
			);

			?>

            <div class="tinypress-popup">
                <div class="popup-container">
                    <a href="#" class="popup-close">Close</a>
                    <form action="" method="POST" id="form-data">
                        <p>Label <span class="tiny-required">*</span></p>
                        <label>
                            <input type="text" id="tiny-label" name="tiny-label" placeholder="URL Label" required>
                        </label>

                        <p>Target URL <span class="tiny-required">*</span></p>
                        <label>
                            <input type="url" id="tiny-target-url" name="tiny-target-url" placeholder="Target URL" required>
                        </label>

                        <p>Short String <span class="tiny-required">*</span></p>
                        <label>
                            <input type="text" id="tiny-short-string" name="tiny-short-string" placeholder="Short string of this URL" required>
                        </label>

                        <br> <br>
                        <div class="tiny-submit">
                            <div class="loader-container">
                                <div class="loader"></div>
                                <span class="#message_string">Please wait</span>
                            </div>

                            <input type="submit" value="Create" id="tiny-popup" class="tiny-popup" name="tiny-popup">
                        </div>
                    </form>
                </div>
            </div>


            <style>

                li#wp-admin-bar-tinypress-admin-bar > a,
                li#wp-admin-bar-tinypress-admin-bar > a:hover,
                li#wp-admin-bar-tinypress-admin-bar > a:focus,
                li#wp-admin-bar-tinypress-admin-bar > a:active {
                    color: #fff !important;
                    background: #009688FF !important;
                    outline: none;
                    box-shadow: none;
                    border: none;
                }


                .tinypress-popup {
                    position: fixed;
                    left: 0;
                    top: 0;
                    height: 100%;
                    z-index: 999999999;
                    width: 100%;
                    background-color: rgba(0, 0, 0, 0.76);
                    opacity: 0;
                    visibility: hidden;
                    transition: 500ms all;

                }

                .tinypress-popup.is-visible {
                    opacity: 1;
                    visibility: visible;
                    transition: 1s all;
                }

                .tinypress-popup .popup-container {
                    transform: translateY(-50%);
                    transition: 500ms all;
                    position: relative;
                    width: 25%;
                    margin: 2em auto;
                    top: 5%;
                    padding: 2rem;
                    background: #FFF;
                    border-radius: .25em .25em .4em .4em;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
                }

                .is-visible .popup-container {
                    transform: translateY(0);
                    transition: 500ms all;
                }

                .tinypress-popup .popup-container .popup-close {
                    position: absolute;
                    top: 8px;
                    font-size: 0;
                    right: 8px;
                    width: 30px;
                    height: 30px;
                }


                .popup-container .popup-close::before,
                .popup-container .popup-close::after {
                    content: '';
                    position: absolute;
                    top: 12px;
                    width: 14px;
                    height: 3px;
                    background-color: #8f9cb5;
                }

                .tinypress-popup .popup-container .popup-close::before {
                    -webkit-transform: rotate(45deg);
                    -moz-transform: rotate(45deg);
                    -ms-transform: rotate(45deg);
                    -o-transform: rotate(45deg);
                    transform: rotate(45deg);
                    left: 8px;
                }

                .tinypress-popup .popup-container .popup-close::after {
                    -webkit-transform: rotate(-45deg);
                    -moz-transform: rotate(-45deg);
                    -ms-transform: rotate(-45deg);
                    -o-transform: rotate(-45deg);
                    transform: rotate(-45deg);
                    right: 8px;
                }

                .tinypress-popup .popup-container .popup-close:hover:before,
                .tinypress-popup .popup-container .popup-close:hover:after {
                    background-color: #35a785;
                    transition: 300ms all;
                }

                .tinypress-popup span.tiny-required {
                    color: red;
                    font-size: 16px;
                }

                .tinypress-popup input[type="url"],
                .tinypress-popup input[type="text"] {
                    width: 100%;
                    height: 50px;
                    padding: 10px 8px;
                    border: 1px solid #eee;
                }

                .tinypress-popup input[type="url"]:focus,
                .tinypress-popup input[type=text]:focus {
                    border: 1px solid #617822;
                }

                .tinypress-popup input[type="submit"] {
                    background: var(--tinypress-color-green);
                    padding: 12px 16px;
                    font-size: 14px;
                    color: #fff;
                    border-radius: 3px;
                    cursor: pointer;
                    text-transform: uppercase;
                    border: none;
                }

                .tinypress-popup .tiny-submit {
                    display: flex;
                    justify-content: flex-end;
                }

                /*loader css style*/

                .tiny-submit .loader-container {
                    display: flex;
                    align-items: center;
                    visibility: hidden;
                }

                .tiny-submit .loader-container::after {
                    align-items: center;
                    justify-content: flex-end;
                    height: 50px;
                    width: 100%;
                    background-color: #f5f5f5;
                    float: right;
                }


                .tinypress-popup .loader {
                    border: 4px solid #009688;
                    border-top: 4px solid #ffffff;
                    border-radius: 50%;
                    width: 16px;
                    height: 16px;
                    animation: spin 2s linear infinite;
                    margin: 10px;

                }

                .tinypress-popup .loader-container span {
                    padding: 0 10px;
                }

                @keyframes spin {
                    0% {
                        transform: rotate(0deg);
                    }
                    100% {
                        transform: rotate(360deg);
                    }
                }


            </style>
			<?php
		}


		/**
		 * Register Post Types
		 */
		function register_everything() {
			global $tinypress_wpdk;

			$tinypress_wpdk->utils()->register_post_type( 'tinypress_link', array(
				'singular'            => esc_html__( 'Link', 'tinypress' ),
				'plural'              => esc_html__( 'All Links', 'tinypress' ),
				'labels'              => array(
					'menu_name' => esc_html__( 'TinyPress', 'tinypress' ),
				),
				'menu_icon'           => 'dashicons-admin-links',
				'supports'            => array( '' ),
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
			) );

			$tinypress_wpdk->utils()->register_taxonomy( 'tinypress_link_cat',
				'tinypress_link',
				apply_filters( 'TINYPRESS/Filters/link_cat_args',
					array(
						'singular'     => esc_html__( 'Category', 'tinypress' ),
						'plural'       => esc_html__( 'Categories', 'tinypress' ),
						'hierarchical' => true,
					)
				)
			);

//			$tinypress_wpdk->utils()->register_taxonomy( 'tinypress_link_tags', 'tinypress_link',
//				apply_filters( 'TINYPRESS/Filters/link_tags_args',
//					array(
//						'singular' => esc_html__( 'Tag', 'tinypress' ),
//						'plural'   => esc_html__( 'Tags', 'tinypress' ),
//					)
//				)
//			);
		}


		/**
		 * Adds a submenu page under a custom post type parent.
		 */
		function links_log() {
			add_submenu_page( 'edit.php?post_type=tinypress_link',
				esc_html__( 'Logs', 'tinypress' ), esc_html__( 'Logs', 'tinypress' ), 'manage_options', 'tinypress-logs',
				array( $this, 'render_menu_logs' )
			);
		}


		/**
		 * Render logs menu
		 */
		function render_menu_logs() {
			if ( ! class_exists( 'WP_List_Table_Logs' ) ) {
				require_once 'class-table-logs.php';
			}

			$table_logs = new WP_List_Table_Logs();

			echo '<div class="wrap">';
			echo '<h2 class="report-table">' . esc_html__( 'All Logs', 'tinypress' ) . '</h2>';

			$table_logs->prepare_items();
			$table_logs->display();

			echo '</div>';
		}


		/**
		 * @return TINYPRESS_Hooks
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}

TINYPRESS_Hooks::instance();