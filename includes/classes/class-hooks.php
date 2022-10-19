<?php
/**
 * Class Hooks
 *
 * @author Pluginbazar
 */

use Pluginbazar\Utils;

if ( ! class_exists( 'TINYPRESS_Hooks' ) ) {
	/**
	 * Class TINYPRESS_Hooks
	 */
	class TINYPRESS_Hooks {

		protected static $_instance = null;

		/**
		 * SLIDERXWOO_Hooks constructor.
		 */
		function __construct() {

			add_action( 'init', array( $this, 'register_everything' ) );
			add_action( 'template_redirect', array( $this, 'redirect_url' ) );
			add_action( 'tinypress_short_url', array( $this, '_tinypress_short_url_' ) );
		}

		function _tinypress_short_url_( $post_id ) {

			$domain_url = site_url( '/' );
			$short_url  = get_post_meta( $post_id, '_short_string', true );
			$full_url = $domain_url . $short_url;

			return $full_url;
		}

		function redirect_url() {

			$request_uri = isset( $_SERVER ["REQUEST_URI"] ) ? $_SERVER ["REQUEST_URI"] : '';
			$key         = trim( $request_uri, '/' );
			$post_id     = tinypress()->key_to_post_id( $key );
			$url         = tinypress()->target_url( $post_id );
			$status      = (int) get_post_meta( $post_id, '_redirection', true );
			$this->_tinypress_short_url_( $post_id );

			if ( is_404() && ! is_page( $key ) ) {
				wp_safe_redirect( $url, $status );
				exit;
			}
			do_action( 'tinypress_short_url', $post_id );

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

		/**
		 * Register Post Types and Settings
		 */
		function register_everything() {

			global $tinypress_sdk;

			/**
			 * Register Post Types
			 */
			$tinypress_sdk->utils()->register_post_type( 'tinypress_url', array(
				'singular'            => esc_html__( 'Tiny URL', 'tinypress' ),
				'plural'              => esc_html__( 'All Tiny URLs', 'tinypress' ),
				'labels'              => array(
					'menu_name' => esc_html__( 'TinyPress', 'tinypress' ),
					'add_new'   => esc_html__( 'Short an URL', 'tinypress' ),
				),
				'menu_icon'           => 'dashicons-admin-links',
				'supports'            => array( 'title' ),
				'public'              => false,
				'exclude_from_search' => true,
			) );
		}
	}
}

TINYPRESS_Hooks::instance();