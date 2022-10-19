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

		function my_callback_function( $args ) {

			global $post;

			$default_string = Utils::get_args_option( 'default', $args );
			$short_string   = Utils::get_meta( '_short_string', $post->ID, $default_string );

			printf( '<input type="hidden" name="tinypress_url[_short_string]" value="%s">', $short_string );
			printf( '<span class="short-url-wrap"><span class="prefix">%s</span><span class="random">%s</span></span>', site_url( '/' ), $short_string );
		}

		/**
		 * Generate meta box for slider data
		 */
		function generate_tinypress_meta_box() {

			$url_slug = tinypress_create_url_slug();

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
							'id'       => '_short_string_type',
							'type'     => 'button_set',
							'title'    => esc_html__( 'Shorten Type', 'tinypress' ),
							'subtitle' => esc_html__( 'Specify shorten type.', 'tinypress' ),
							'options'  => array(
								'random' => array( 'label' => esc_html__( 'Random', 'tinypress' ) ),
								'manual' => array( 'label' => esc_html__( 'Manual', 'tinypress' ) ),
							),
							'default'  => 'random',
						),
						array(
							'id'         => '_short_string',
							'type'       => 'callback',
							'function'   => array( $this, 'my_callback_function' ),
							'title'      => esc_html__( 'Short String', 'tinypress' ),
							'subtitle'   => esc_html__( 'Short string of this URL.', 'tinypress' ),
							'default'    => $url_slug,
							'dependency' => array( '_short_string_type', '==', 'random' ),
						),
						array(
							'id'          => '_short_string',
							'type'        => 'text',
							'title'       => esc_html__( 'Short String', 'tinypress' ),
							'subtitle'    => esc_html__( 'Short string of this URL.', 'tinypress' ),
							'placeholder' => esc_attr( 'ad34o' ),
							'class'       => 'tinypress-slug-custom',
							'default'     => $url_slug,
							'dependency'  => array( '_short_string_type', '==', 'manual' ),
						),
						array(
							'id'          => '_redirection',
							'type'        => 'select',
							'title'       => esc_html__( 'Redirection', 'tinypress' ),
							'subtitle'    => esc_html__( '', 'tinypress' ),
							'placeholder' => 'Select an option',
							'options'     => array(
								307 => '307 (Temporary)',
								302 => '302 (Temporary)',
								301 => '301 (Permanent)',
							),
							'default'     => 302,
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