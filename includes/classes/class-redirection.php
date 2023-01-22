<?php
/**
 * Class Redirection
 *
 * @author Pluginbazar
 */

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TINYPRESS_Redirection' ) ) {
	/**
	 * Class TINYPRESS_Redirection
	 */
	class TINYPRESS_Redirection {

		protected static $_instance = null;

		/**
		 * TINYPRESS_Redirection constructor.
		 */
		function __construct() {
			add_action( 'template_redirect', array( $this, 'redirect_url' ) );
		}


		/**
		 * Do the redirection
		 *
		 * @param $link_id
		 *
		 * @return void
		 */
		function do_redirection( $link_id ) {

			$tags                 = array();
			$target_url           = Utils::get_meta( 'target_url', $link_id );
			$redirection_method   = Utils::get_meta( 'redirection_method', $link_id );
			$no_follow            = Utils::get_meta( 'redirection_no_follow', $link_id );
			$sponsored            = Utils::get_meta( 'redirection_sponsored', $link_id );
			$parameter_forwarding = Utils::get_meta( 'redirection_parameter_forwarding', $link_id );

			if ( '1' == $parameter_forwarding && ! empty( $parameters = wp_unslash( $_GET ) ) ) {
				$target_url = $target_url . '?' . http_build_query( $parameters );
			}


			if ( '1' == $no_follow ) {
				$tags[] = 'noindex';
				$tags[] = 'nofollow';
			}

			if ( '1' == $sponsored ) {
				$tags[] = 'sponsored';
			}

			if ( ! empty( $tags ) ) {
				header( 'X-Robots-Tag: ' . implode( ', ', $tags ), true );
			}

			header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
			header( 'Cache-Control: post-check=0, pre-check=0', false );
			header( 'Pragma: no-cache' );
			header( 'Expires: Mon, 10 Oct 1975 08:09:15 GMT' );
			header( 'X-Redirect-Powered-By: TinyPress ' . TINYPRESS_PLUGIN_VERSION . ' https://pluginbazar.com' );

			header( "Location: $target_url", true, $redirection_method );

			die();
		}


		/**
		 * Track the redirection
		 *
		 * @param $link_id
		 *
		 * @return void
		 */
		function track_redirection( $link_id ) {

			global $wpdb;

			$get_ip_address = tinypress_get_ip_address();
			$curr_user_id   = is_user_logged_in() ? get_current_user_id() : 0;
			$get_user_data  = @file_get_contents( 'http://www.geoplugin.net/json.gp?ip=' . $get_ip_address );

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

			$wpdb->insert( TINYPRESS_TABLE_REPORTS,
				array(
					'user_id'       => $curr_user_id,
					'post_id'       => $link_id,
					'user_ip'       => $get_ip_address,
					'user_location' => json_encode( $location_info ),
				),
				array( '%d', '%d', '%s', '%s' )
			);
		}


		/**
		 * Redirect to target URL
		 *
		 * @return void
		 */
		function redirect_url() {

			$request_uri = isset( $_SERVER ['REQUEST_URI'] ) ? sanitize_text_field( $_SERVER ['REQUEST_URI'] ) : '';
			$_tiny_slug  = trim( $request_uri, '/' );
			$_tiny_slug  = explode( '?', $_tiny_slug );
			$tiny_slug   = $_tiny_slug[0] ?? '';
			$link_id     = tinypress()->tiny_slug_to_post_id( $tiny_slug );

			if ( is_404() && ! is_page( $tiny_slug ) ) {

				// Track redirection
				$this->track_redirection( $link_id );

				// Do the redirection
				$this->do_redirection( $link_id );
			}
		}


		/**
		 * @return TINYPRESS_Redirection
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}

TINYPRESS_Redirection::instance();