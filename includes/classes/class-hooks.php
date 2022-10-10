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