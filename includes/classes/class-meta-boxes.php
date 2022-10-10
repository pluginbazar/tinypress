<?php
/*
* @Author 		pluginbazar
* Copyright: 	2022 pluginbazar
*/

use \Pluginbazar\Utils;

defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'TINYPRESS_Meta_boxes' ) ) {
	/**
	 * Class TINYPRESS_Meta_boxes
	 */
	class TINYPRESS_Meta_boxes {

		/**
		 * @var string
		 */
		private $prefix_tinyurl_metabox = 'tinypress_url';

		/**
		 * TINYPRESS_Meta_boxes constructor.
		 */
		function __construct() {

			$this->generate_tinypress_meta_box();
		}


		/**
		 * Generate meta box for slider data
		 */
		function generate_tinypress_meta_box() {

			// Create a metabox for tinypress.
			PBSettings::createMetabox( $this->prefix_tinyurl_metabox,
				array(
					'title'     => esc_html__( 'TinyPress', 'tinypress' ),
					'post_type' => 'tinypress_url',
					'data_type' => 'unserialize',
					'context'   => 'normal',
					'nav'       => 'inline',
					'preview'   => true,
				),
			);

			// General Settings section.
			PBSettings::createSection( $this->prefix_tinyurl_metabox,
				array(
					'title'  => esc_html__( 'General Settings', 'tinypress' ),
					'icon'   => 'fa fa-cog',
					'fields' => array(
						array(
							'id'       => '_target_url',
							'type'     => 'text',
							'title'    => esc_html__( 'Target URL', 'tinypress' ),
							'subtitle' => esc_html__( '', 'tinypress' ),
						),
						array(
							'id'       => '_short_url',
							'type'     => 'text',
							'title'    => esc_html__( 'Short URL', 'tinypress' ),
							'subtitle' => esc_html__( '', 'tinypress' ),
						),
						array(
							'id'          => '_redirection',
							'type'        => 'select',
							'title'       => esc_html__( 'Redirection', 'tinypress' ),
							'subtitle'    => esc_html__( '', 'tinypress' ),
							'placeholder' => 'Select an option',
							'options'     => array(
								'option-1' => '307 (Temporary)',
								'option-2' => '302 (Temporary)',
								'option-3' => '301 (Permanent)',
							),
							'default'     => 'option-2'
						),
						array(
							'id'       => '_notes',
							'type'     => 'textarea',
							'title'    => esc_html__( 'Notes', 'tinypress' ),
							'subtitle' => esc_html__( '', 'tinypress' ),
						),
					),
				)
			);
		}
	}
}

tinypress()->tinypress_metaboxes = new TINYPRESS_Meta_boxes();