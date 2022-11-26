<?php
/*
* @Author 		pluginbazar
* Copyright: 	2022 pluginbazar
*/

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TINYLINKS_Meta_boxes' ) ) {
	/**
	 * Class TINYLINKS_Meta_boxes
	 */
	class TINYLINKS_Meta_boxes {

		/**
		 * @var string
		 */
		private $prefix_tinylinks_metabox = 'tinylinks_url';

		/**
		 * TINYLINKS_Meta_boxes constructor.
		 */
		function __construct() {
			$this->generate_tinylinks_meta_box();

			add_action( 'wpdk_settings_after_meta_navs', array( $this, 'add_plugin_promotional_navs' ) );
		}


		function add_plugin_promotional_navs() {
//			if ( ! liquidpoll()->is_pro() ) {
//				printf( '<li class="pbsettings-extra-nav get-pro"><a href="%s">%s</a></li>', LIQUIDPOLL_PLUGIN_LINK, esc_html__( 'Get Pro', 'wp-poll' ) );
//			}

			printf( '<li class="wpdk_settings-extra-nav "><a href="%s">%s</a></li>', '', esc_html__( 'Documentation', 'tinylinks' ) );
			printf( '<li class="wpdk_settings-extra-nav "><a href="%s">%s</a></li>', '', esc_html__( 'Community', 'tinylinks' ) );
		}


		function my_callback_function( $args ) {
			global $post;

			$default_string = Utils::get_args_option( 'default', $args );
			$short_string   = Utils::get_meta( '_short_string', $post->ID, $default_string );

			printf( '<input type="hidden" name="tinylinks_url[_short_string]" value="%s">', $short_string );
			printf( '<span class="short-url-wrap hint--top" aria-label="Click here to copy"> <span class="prefix">%s</span><span class="random">%s</span></span>', site_url( '/' ), $short_string );
			printf( '<input type="hidden" id="short-url" name="custId" value="%s%s">', site_url( '/' ), $short_string );
		}

		/**
		 * Generate meta box for slider data
		 */
		function generate_tinylinks_meta_box() {
			$url_slug = tinylinks_create_url_slug();

			// Create a metabox for tinylinks.
			WPDK_Settings::createMetabox( $this->prefix_tinylinks_metabox,
				array(
					'title'     => esc_html__( 'TinyPress', 'tinylinks' ),
					'post_type' => 'tinylinks_url',
					'data_type' => 'unserialize',
					'context'   => 'normal',
					'nav'       => 'inline',
					'preview'   => true,
				)
			);

			// General Settings section.
			WPDK_Settings::createSection( $this->prefix_tinylinks_metabox,
				array(
					'title'  => esc_html__( 'General Settings', 'tinylinks' ),
					'fields' => array(
						array(
							'id'       => 'post_title',
							'type'     => 'text',
							'title'    => esc_html__( 'Label', 'tinylinks' ),
							'wp_type'  => 'post_title',
							'subtitle' => esc_html__( 'For admin purpose only.', 'tinylinks' ),
						),
						array(
							'id'    => '_target_url',
							'type'  => 'text',
							'title' => esc_html__( 'Target URL', 'tinylinks' ),
						),
						array(
							'id'       => '_short_string',
							'type'     => 'callback',
							'function' => array( $this, 'my_callback_function' ),
							'title'    => esc_html__( 'Short String', 'tinylinks' ),
							'subtitle' => esc_html__( 'Short string of this URL.', 'tinylinks' ),
							'default'  => $url_slug,
						),
						array(
							'id'          => '_short_string',
							'type'        => 'text',
							'title'       => esc_html__( 'Custom String', 'tinylinks' ),
							'subtitle'    => esc_html__( 'Custom string of this URL.', 'tinylinks' ),
							'placeholder' => esc_attr( 'ad34o' ),
							'class'       => 'tinylinks-slug-custom',
							'default'     => $url_slug,
						),
						array(
							'id'          => '_redirection',
							'type'        => 'select',
							'title'       => esc_html__( 'Redirection', 'tinylinks' ),
							'placeholder' => 'Select an option',
							'options'     => array(
								307 => '307 (Temporary)',
								302 => '302 (Temporary)',
								301 => '301 (Permanent)',
							),
							'default'     => 302,
						),
						array(
							'id'    => '_notes',
							'type'  => 'textarea',
							'title' => esc_html__( 'Notes', 'tinylinks' ),
						),
					),
				)
			);

			// General Settings section.
			WPDK_Settings::createSection( $this->prefix_tinylinks_metabox,
				array(
					'title'  => esc_html__( 'Analytics', 'tinylinks' ),
					'fields' => array(
						array(
							'id'    => '_notesadsd',
							'type'  => 'textarea',
							'title' => esc_html__( 'Notes', 'tinylinks' ),
						),
					),
				)
			);
		}
	}
}

tinylinks()->tinylinks_metaboxes = new TINYLINKS_Meta_boxes();