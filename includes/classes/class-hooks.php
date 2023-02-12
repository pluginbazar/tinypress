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

			$tinypress_wpdk->utils()->register_taxonomy( 'tinypress_link_cat', 'tinypress_link',
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