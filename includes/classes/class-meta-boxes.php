<?php
/*
* @Author 		pluginbazar
* Copyright: 	2022 pluginbazar
*/

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TINYPRESS_Meta_boxes' ) ) {
	/**
	 * Class TINYPRESS_Meta_boxes
	 */
	class TINYPRESS_Meta_boxes {

		private $tinypress_metabox_main = 'tinypress_meta_main';
		private $tinypress_metabox_side = 'tinypress_meta_side';
		private $tinypress_default_slug;


		/**
		 * TINYPRESS_Meta_boxes constructor.
		 */
		function __construct() {

			$this->tinypress_default_slug = tinypress_create_url_slug();

			$this->generate_tinypress_meta_box();

			foreach ( get_post_types() as $post_type ) {
				$this->generate_tinypress_meta_box_side( $post_type );
			}

			add_action( 'wpdk_settings_after_meta_navs', array( $this, 'add_plugin_promotional_navs' ) );
		}


		/**
		 * Add promotional nav items
		 *
		 * @return void
		 */
		function add_plugin_promotional_navs() {
			printf( '<li class="wpdk_settings-extra-nav "><a href="%s">%s</a></li>', TINYPRESS_LINK_DOC, esc_html__( 'Documentation', 'tinypress' ) );
			printf( '<li class="wpdk_settings-extra-nav "><a href="%s">%s</a></li>', TINYPRESS_LINK_SUPPORT, esc_html__( 'Support', 'tinypress' ) );
		}


		/**
		 * Generate side metabox
		 *
		 * @param $post_type
		 *
		 * @return void
		 */
		function generate_tinypress_meta_box_side( $post_type ) {

			$prefix = $this->tinypress_metabox_side . '_' . $post_type;

			WPDK_Settings::createMetabox( $prefix,
				array(
					'title'     => esc_html__( 'TinyPress', 'tinypress' ),
					'post_type' => $post_type,
					'data_type' => 'unserialize',
					'nav'       => 'inline',
					'context'   => 'side',
					'priority'  => 'high',
					'preview'   => true,
				)
			);

			WPDK_Settings::createSection( $prefix,
				array(
					'title'  => esc_html__( 'TinyPress', 'tinypress' ),
					'fields' => array(
						array(
							'id'       => 'tiny_slug',
							'title'    => ' ',
							'type'     => 'callback',
							'function' => array( $this, 'render_field_tinypress_link' ),
							'default'  => $this->tinypress_default_slug,
						),
					),
				)
			);
		}


		/**
		 * Render short URL field
		 *
		 * @param $args
		 *
		 * @return void
		 */
		function render_field_tinypress_link( $args ) {

			global $post;

			echo tinypress_get_tiny_slug_copier( $post->ID, true, $args );
		}


		/**
		 * Generate meta box for slider data
		 */
		function generate_tinypress_meta_box() {

			// Create a metabox for tinypress.
			WPDK_Settings::createMetabox( $this->tinypress_metabox_main,
				array(
					'title'     => esc_html__( 'TinyPress', 'tinypress' ),
					'post_type' => 'tinypress_link',
					'data_type' => 'unserialize',
					'context'   => 'normal',
					'nav'       => 'inline',
					'preview'   => true,
				)
			);

			// General Settings section.
			WPDK_Settings::createSection( $this->tinypress_metabox_main,
				array(
					'title'  => esc_html__( 'General Settings', 'tinypress' ),
					'fields' => array(
						array(
							'id'       => 'post_title',
							'type'     => 'text',
							'title'    => esc_html__( 'Label', 'tinypress' ),
							'wp_type'  => 'post_title',
							'subtitle' => esc_html__( 'For admin purpose only.', 'tinypress' ),
						),
						array(
							'id'    => 'target_url',
							'type'  => 'text',
							'title' => esc_html__( 'Target URL', 'tinypress' ),
						),
						array(
							'id'       => 'tiny_slug',
							'type'     => 'callback',
							'function' => array( $this, 'render_field_tinypress_link' ),
							'title'    => esc_html__( 'Short String', 'tinypress' ),
							'subtitle' => esc_html__( 'Short string of this URL.', 'tinypress' ),
							'default'  => $this->tinypress_default_slug,
						),
						array(
							'id'          => 'redirection_method',
							'type'        => 'select',
							'title'       => esc_html__( 'Redirection', 'tinypress' ),
							'placeholder' => 'Select an option',
							'options'     => array(
								307 => esc_html__( '307 (Temporary)', 'tinypress' ),
								302 => esc_html__( '302 (Temporary)', 'tinypress' ),
								301 => esc_html__( '301 (Permanent)', 'tinypress' ),
							),
							'default'     => 302,
						),
						array(
							'id'    => 'tiny_notes',
							'type'  => 'textarea',
							'title' => esc_html__( 'Notes', 'tinypress' ),
						),
					),
				)
			);

			// General Settings section.
			WPDK_Settings::createSection( $this->tinypress_metabox_main,
				array(
					'title'  => esc_html__( 'Analytics', 'tinypress' ),
					'fields' => array(
						array(
							'id'    => '_notesadsd',
							'type'  => 'textarea',
							'title' => esc_html__( 'Notes', 'tinypress' ),
						),
					),
				)
			);
		}
	}
}

tinypress()->tinypress_metaboxes = new TINYPRESS_Meta_boxes();