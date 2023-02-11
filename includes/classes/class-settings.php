<?php
/**
 * Settings class
 *
 * @author Pluginbazar
 */

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TINYPRESS_Settings' ) ) {
	class TINYPRESS_Settings {

		protected static $_instance = null;

		/**
		 * TINYPRESS_Settings constructor.
		 */
		public function __construct() {
			global $tinypress_wpdk;

			// Generate settings page
			$settings_args = array(
				'framework_title' => esc_html__( 'TinyPress Settings', 'tinypress' ),
				'menu_title'      => esc_html__( 'Settings', 'tinypress' ),
				'menu_slug'       => 'settings',
				'menu_type'       => 'submenu',
				'menu_parent'     => 'edit.php?post_type=tinypress_link',
				'database'        => 'option',
				'theme'           => 'light',
				'show_search'     => false,
			);

			WPDK_Settings::createSettingsPage( $tinypress_wpdk->plugin_unique_id, $settings_args, $this->get_settings_pages() );
		}

		/**
		 * Return settings pages
		 *
		 * @return mixed|void
		 */
		function get_settings_pages() {
			$field_sections['settings'] = array(
				'title'    => esc_html__( 'General', 'tinypress' ),
				'sections' => array(
					array(
						'title'  => esc_html__( 'Options', 'tinypress' ),
						'fields' => array(
							array(
								'id'       => 'tinypress_link_prefix',
								'type'     => 'switcher',
								'title'    => esc_html__( 'Link Prefix', 'tinypress' ),
								'subtitle' => esc_html__( 'Add custom prefix.', 'tinypress' ),
								'label'    => esc_html__( 'Customize your tiny url in a better way.', 'tinypress' ),
								'default'  => true,
							),
							array(
								'id'          => 'tinypress_link_prefix_slug',
								'type'        => 'text',
								'title'       => esc_html__( 'Prefix Slug', 'tinypress' ),
								'subtitle'    => esc_html__( 'Custom prefix slug.', 'tinypress' ),
								'desc'        => sprintf( esc_html__( 'This prefix slug will be added this way - %s', 'tinypress' ), esc_url( site_url( 'go/my-tiny-slug' ) ) ),
								'placeholder' => esc_html__( 'go', 'tinypress' ),
								'default'     => esc_html__( 'go', 'tinypress' ),
								'dependency'  => array( 'tinypress_link_prefix', '==', '1' ),
							),
						),
					),
					array(
						'title'  => esc_html__( 'Role Management', 'tinypress' ),
						'fields' => array(
							array(
								'id'      => 'tinypress_role_view',
								'type'    => 'checkbox',
								'title'   => esc_html__( 'Who Can View Links?', 'tinypress' ),
								'inline'  => true,
								'options' => user_role_management(),
							),
							array(
								'id'      => 'tinypress_role_create',
								'type'    => 'checkbox',
								'title'   => esc_html__( 'Who Can Create/Edit Links', 'tinypress' ),
								'inline'  => true,
								'options' => user_role_management(),
							),
							array(
								'id'      => 'tinypress_role_analytics',
								'type'    => 'checkbox',
								'title'   => esc_html__( 'Who Can Check Analytics', 'tinypress' ),
								'inline'  => true,
								'options' => user_role_management(),
							),
							array(
								'id'      => 'tinypress_role_edit',
								'type'    => 'checkbox',
								'title'   => esc_html__( 'Who Can Edit Settings', 'tinypress' ),
								'inline'  => true,
								'options' => user_role_management(),
							),
						),
					),
				),

			);

			$field_sections['security'] = array(
				'title'    => esc_html__( 'Browser Extensions', 'tinypress' ),
				'sections' => array(
					array(
						'title'  => esc_html__( 'Google Chrome', 'tinypress' ),
						'fields' => array(
							array(
								'id'      => 'tinypress_auth_key_chrome',
								'type'    => 'text',
								'title'   => esc_html__( 'Authentication Key', 'tinypress' ),
								'default' => md5( site_url() ),
							),
						),
					),
				),
			);

			return apply_filters( 'TINYPRESS/Filters/settings_pages', $field_sections );
		}


		/**
		 * @return TINYPRESS_Settings
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}
TINYPRESS_Settings::instance();