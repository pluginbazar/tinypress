<?php
/**
 * Class Hooks
 *
 * @author Pluginbazar
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TINYLINKS_Hooks' ) ) {
	/**
	 * Class TINYLINKS_Hooks
	 */
	class TINYLINKS_Hooks {

		protected static $_instance = null;

		/**
		 * TINYLINKS_Hooks constructor.
		 */
		function __construct() {
			add_action( 'init', array( $this, 'register_everything' ) );
			add_action( 'admin_menu', array( $this, 'user_reports' ) );
			add_action( 'template_redirect', array( $this, 'redirect_url' ) );
			register_activation_hook( __FILE__, array( $this, 'flush_rewrite_rules' ) );
		}

		function redirect_url() {
			$request_uri    = isset( $_SERVER ["REQUEST_URI"] ) ? sanitize_text_field( $_SERVER ["REQUEST_URI"] ) : '';
			$key            = trim( $request_uri, '/' );
			$post_id        = tinylinks()->key_to_post_id( $key );
			$url            = tinylinks()->target_url( $post_id );
			$status         = (int) get_post_meta( $post_id, '_redirection', true );
			$get_ip_address = tinylinks_get_ip_address();

			if ( is_user_logged_in() ) {
				$curr_user_id = get_current_user_id();
			} else {
				$curr_user_id = 0;
			}

			$get_user_data = @file_get_contents( 'http://www.geoplugin.net/json.gp?ip=' . $get_ip_address );
			if ( ! $get_user_data ) {
				return;
			}
			$user_data = json_decode( $get_user_data, true );

			$location_info = array(
				"geoplugin_city",
				"geoplugin_region",
				"geoplugin_regionName",
				"geoplugin_countryCode",
				"geoplugin_countryName",
				"geoplugin_continentName",
				"geoplugin_latitude",
				"geoplugin_longitude",

			);

			$location_info = array_merge( array_fill_keys( $location_info, null ), array_intersect_key( $user_data, array_flip( $location_info ) ) );


			if ( is_404() && ! is_page( $key ) ) {
				global $wpdb;
				$data   = array(
					'user_id'       => $curr_user_id,
					'post_id'       => $post_id,
					'user_ip'       => $get_ip_address,
					'user_location' => json_encode( $location_info ),
				);
				$format = array( '%d', '%d', '%s', '%s' );
				$wpdb->insert( TINYLINKS_TABLE_REPORTS, $data, $format );

				if ( wp_safe_redirect( $url, $status ) ) {
					header( "Location: $url", true, 301 );
				}
				die();
			}
		}


		/**
		 * @return TINYLINKS_Hooks
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * flush_rewrite_rules
		 *
		 * @return void
		 */
		function flush_rewrite_rules() {
			global $wp_rewrite;
			$wp_rewrite->flush_rules( true );
		}

		/**
		 * Register Post Types
		 */
		function register_everything() {
			global $tinylinks_sdk;

			$tinylinks_sdk->utils()->register_post_type( 'tinylinks_url', array(
				'singular'            => esc_html__( 'Link', 'tinylinks' ),
				'plural'              => esc_html__( 'Links', 'tinylinks' ),
				'labels'              => array(
					'menu_name' => esc_html__( 'TinyLinks', 'tinylinks' ),
				),
				'menu_icon'           => 'dashicons-admin-links',
				'supports'            => array( '' ),
				'public'              => false,
				'exclude_from_search' => true,
			) );
		}

		/**
		 * Adds a submenu page under a custom post type parent.
		 */
		function user_reports() {
			add_submenu_page(
				'edit.php?post_type=tinylinks_url',
				esc_html__( 'Reports', 'tinylinks' ),
				esc_html__( 'Reports', 'tinylinks' ),
				'manage_options',
				'reports',
				array( $this, 'reports_data_table' ),
				10
			);
		}

		/**
		 * Display callback for the submenu page.
		 */
		function reports_data_table() {
			require_once 'class-user-reports.php';

			$User_Reports_Table = new User_Reports_Table();
			echo '<div class="wrap">';
			printf( '<h2 class="report-table">%s</h2>', esc_html__( 'All Reports', 'tinylinks' ) );
			$User_Reports_Table->prepare_items();
			$User_Reports_Table->display();
			echo '</div>';
		}
	}
}

TINYLINKS_Hooks::instance();