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
		 * @param $key
		 *
		 * @return false|mixed
		 */
		function key_to_url( $key ) {
			$post_id = get_posts( array(
					'post_type'  => 'tinypress_url',
					'meta_key'   => '_short_string',
					'meta_value' => $key,
					'fields'     => 'ids',
				)
			);

			$url = get_post_meta( reset( $post_id ), '_target_url', true );

			return $url;
		}

	}
}

global $tinypress;

$tinypress = new TINYPRESS_Functions();