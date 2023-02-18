<?php
/**
 * Class Functions
 */

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TINYPRESS_Functions' ) ) {
	class TINYPRESS_Functions {

		public static $text_hint = null;

		public static $text_copied = null;


		/**
		 * @var TINYPRESS_Meta_boxes
		 */
		public $tinypress_metaboxes = null;

		/**
		 * @var TINYPRESS_Column_link
		 */
		public $tinypress_columns = null;


		/**
		 * TINYPRESS_Functions constructor.
		 */
		function __construct() {
			self::$text_hint   = esc_html__( 'Click to Copy.', 'tinypress' );
			self::$text_copied = esc_html__( 'Copied.', 'tinypress' );
		}


		/**
		 * @param $slug
		 *
		 * @return int
		 */
		function tiny_slug_to_post_id( $slug ) {

			if ( empty( $slug ) ) {
				return 0;
			}

			global $wpdb;

			return (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value like %s", $slug ) );
		}
	}
}

global $tinypress;

$tinypress = new TINYPRESS_Functions();