<?php
/*
* @Author 		pluginbazar
* Copyright: 	2022 pluginbazar
*/

use \Pluginbazar\Utils;

defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'TINYPRESS_Post_meta' ) ) {
	/**
	 * Class TINYPRESS_Post_meta
	 */
	class TINYPRESS_Post_meta {

		/**
		 * TINYPRESS_Post_meta constructor.
		 */
		function __construct() {

			$this->generate_tinypress_meta_box();
		}


		/**
		 * Generate meta box for slider data
		 */
		function generate_tinypress_meta_box() {

			$prefix = 'pb_meta_fields';

			/**
			 * Create a metabox for tinypress.
			 */
			PBSettings::createMetabox(
				$prefix,
				array(
					'title'     => __( 'Tinypress Options', 'tinypress' ),
					'post_type' => 'tinypress',
					'data_type' => 'unserialize',
					'context'   => 'normal',
					'nav'       => 'inline',
					'preview'   => true,
				)
			);

			/**
			 * General Settings section.
			 */
			PBSettings::createSection( $prefix,
				array(
					'title'  => __( 'General Settings', 'tinypress' ),
					'icon'   => 'fa fa-cog',
					'fields' => array(
						array(
							'id'       => '_targetURL',
							'type'     => 'text',
							'title'    => esc_html__( 'Target URL', 'tinypress' ),
							'subtitle' => esc_html__( '', 'tinypress' ),


						),
						array(
							'id'       => '_shortURL',
							'type'     => 'text',
							'title'    => esc_html__( 'Short URL', 'tinypress' ),
							'subtitle' => esc_html__( '', 'tinypress' ),
						),
						array(
							'id'       => '_redirection',
							'type'     => 'select',
							'title'    => esc_html__( 'Redirection', 'tinypress' ),
							'subtitle' => esc_html__( '', 'tinypress' ),

							'placeholder' => 'Select an option',
							'options'     => array(
								'option-1' => '307 (Temporary)',
								'option-2' => '302 (Temporary)',
								'option-3' => '301 (Permanent)',
							),
							'default'     => 'option-2'
						),

						array(
							'id'       => '_domain',
							'type'     => 'text',
							'title'    => esc_html__( 'Domain', 'tinypress' ),
							'subtitle' => esc_html__( '', 'tinypress' ),

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


			PBSettings::createSection( $prefix,
				array(
					'title'  => esc_html__( 'Tinypress Advanced', 'tinypress' ),
					'icon'   => 'fa fa-cog',
					'fields' => array()
				)
			);
		}


	}
}

new TINYPRESS_Post_meta();