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
			add_action( 'admin_menu', array( $this, 'user_reports' ) );

			add_action( 'template_redirect', array( $this, 'redirect_url' ) );

			add_filter( 'post_updated_messages', array( $this, 'change_url_update_message' ) );
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
		 * Redirect to target URL
		 *
		 * @return void
		 */
		function redirect_url() {
			$request_uri        = isset( $_SERVER ["REQUEST_URI"] ) ? sanitize_text_field( $_SERVER ["REQUEST_URI"] ) : '';
			$tiny_slug          = trim( $request_uri, '/' );
			$post_id            = tinypress()->tiny_slug_to_post_id( $tiny_slug );
			$target_url         = Utils::get_meta( 'target_url', $post_id );
			$redirection_method = Utils::get_meta( 'redirection_method', $post_id );
			$get_ip_address     = tinypress_get_ip_address();

			if ( is_user_logged_in() ) {
				$curr_user_id = get_current_user_id();
			} else {
				$curr_user_id = 0;
			}

			$get_user_data = @file_get_contents( 'http://www.geoplugin.net/json.gp?ip=' . $get_ip_address );

			if ( ! $get_user_data ) {
				return;
			}

			$user_data     = json_decode( $get_user_data, true );
			$location_info = array(
				'geoplugin_city',
				'geoplugin_region',
				'geoplugin_regionName',
				'geoplugin_countryCode',
				'geoplugin_countryName',
				'geoplugin_continentName',
				'geoplugin_latitude',
				'geoplugin_longitude',
			);
			$location_info = array_merge( array_fill_keys( $location_info, null ), array_intersect_key( $user_data, array_flip( $location_info ) ) );

			if ( is_404() && ! is_page( $tiny_slug ) ) {

				global $wpdb;

				$data   = array(
					'user_id'       => $curr_user_id,
					'post_id'       => $post_id,
					'user_ip'       => $get_ip_address,
					'user_location' => json_encode( $location_info ),
				);
				$format = array( '%d', '%d', '%s', '%s' );
				$wpdb->insert( TINYPRESS_TABLE_REPORTS, $data, $format );

				if ( wp_safe_redirect( $target_url, $redirection_method ) ) {
					header( "Location: $target_url", true, 301 );
				}

				die();
			}
		}


		/**
		 * Register Post Types
		 */
		function register_everything() {

			global $tinypress_wpdk;

			$tinypress_wpdk->utils()->register_post_type( 'tinypress_link', array(
				'singular'            => esc_html__( 'Link', 'tinypress' ),
				'plural'              => esc_html__( 'Links', 'tinypress' ),
				'labels'              => array(
					'menu_name' => esc_html__( 'TinyPress', 'tinypress' ),
				),
				'menu_icon'           => 'dashicons-admin-links',
				'supports'            => array( '' ),
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
			) );
		}


		/**
		 * Adds a submenu page under a custom post type parent.
		 */
		function user_reports() {
			add_submenu_page( 'edit.php?post_type=tinypress_link',
				esc_html__( 'Reports', 'tinypress' ), esc_html__( 'Reports', 'tinypress' ), 'manage_options', 'tinypress-reports',
				array( $this, 'render_menu_reports' )
			);
		}


		/**
		 * Render reports menu
		 */
		function render_menu_reports() {

			require_once 'class-user-reports.php';

			$User_Reports_Table = new User_Reports_Table();

			echo '<div class="wrap">';
			echo '<h2 class="report-table">' . esc_html__( 'All Reports', 'tinypress' ) . '</h2>';

			$User_Reports_Table->prepare_items();
			$User_Reports_Table->display();

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