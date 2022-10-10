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

	}
}

global $tinypress;

$tinypress = new TINYPRESS_Functions();