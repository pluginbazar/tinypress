<?php
/**
 * Class Functions
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TINYPRESS_Functions' ) ) {
	class TINYPRESS_Functions {

		/**
		 * @var TINYPRESS_Meta_boxes|null
		 */
		public $tinypress_metaboxes = null;

		/**
		 * @param $post_id
		 *
		 * @return mixed
		 */
		function target_url( $post_id ) {

			$url = get_post_meta( $post_id, '_target_url', true );

			return $url;
		}

		/**
		 * @param $key
		 *
		 * @return false|int|WP_Post
		 */
		function key_to_post_id( $key ) {
			$post_id = get_posts( array(
					'post_type'  => 'tinypress_url',
					'meta_key'   => '_short_string',
					'meta_value' => $key,
					'fields'     => 'ids',
				)
			);

			return reset( $post_id );

		}
	}
}

global $tinypress;

$tinypress = new TINYPRESS_Functions();