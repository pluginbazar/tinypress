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
		 * @return TINYPRESS_Meta_boxes|null
		 */
		public $tinypress_metaboxes = null;


		/**
		 * TINYPRESS_Functions constructor.
		 */
		function __construct() {
			self::$text_hint   = esc_html__( 'Click here to copy.', 'tinypress' );
			self::$text_copied = esc_html__( 'Copied.', 'tinypress' );
		}


		/**
		 * @param $slug
		 *
		 * @return false|int|WP_Post
		 */
		function tiny_slug_to_post_id( $slug ) {

			$all_posts = get_posts( array(
				'post_type'  => 'tinypress_link',
				'meta_key'   => 'tiny_slug',
				'meta_value' => $slug,
			) );
			$post_ids  = array_map( function ( WP_Post $post ) {
				return $post->ID;
			}, $all_posts );

			return reset( $post_ids );
		}
	}
}

global $tinypress;

$tinypress = new TINYPRESS_Functions();