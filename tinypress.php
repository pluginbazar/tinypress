<?php
/*
	Plugin Name: Tinypress
	Plugin URI: https://pluginbazar.com/plugin/slider-x-woo
	Description: Best tinypress plugin
	Version: 1.0.0
	Text Domain: tinypress
	Author: Pluginbazar
	Author URI: https://pluginbazar.com/
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
global $wpdb;
defined( 'ABSPATH' ) || exit;

defined( 'ABSPATH' ) || exit;
defined( 'TINYPRESS_PLUGIN_URL' ) || define( 'TINYPRESS_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
defined( 'TINYPRESS_PLUGIN_DIR' ) || define( 'TINYPRESS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
defined( 'TINYPRESS_PLUGIN_FILE' ) || define( 'TINYPRESS_PLUGIN_FILE', plugin_basename( __FILE__ ) );
defined( 'TINYPRESS_PLUGIN_VERSION' ) || define( 'TINYPRESS_PLUGIN_VERSION', '1.0.0' );
defined( 'TINNYPRESS_TABLE_REPORTS' ) || define( 'TINNYPRESS_TABLE_REPORTS', sprintf( '%stinypress_reports', $wpdb->prefix ) );

if ( ! class_exists( 'TINYPRESS_Main' ) ) {
	/**
	 * Class TINYPRESS_Main
	 */
	class TINYPRESS_Main {

		protected static $_instance = null;

		protected static $_script_version = null;

		/**
		 * TINYPRESS_Main constructor.
		 */
		function __construct() {

			self::$_script_version = defined( 'WP_DEBUG' ) && WP_DEBUG ? current_time( 'U' ) : TINYPRESS_PLUGIN_VERSION;

			$this->define_scripts();
			$this->define_classes_functions();

			add_action( 'init', array( $this, 'create_data_table' ) );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		}

		/**
		 * @return TINYPRESS_Main
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * create_data_table
		 *
		 * @return void
		 */

		function create_data_table() {

			if ( ! function_exists( 'maybe_create_table' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			}

			$sql_create_table = "CREATE TABLE " . TINNYPRESS_TABLE_REPORTS . " (
                            id int(50) NOT NULL AUTO_INCREMENT,
                            post_id varchar(50) NOT NULL,
						    user_ip varchar(255) NOT NULL,
                            datetime  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (id)
                            );";

			maybe_create_table( TINNYPRESS_TABLE_REPORTS, $sql_create_table );
		}


		/**
		 * Load Textdomain
		 */
		function load_textdomain() {
			load_plugin_textdomain( 'tinypress', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Include Classes and Functions
		 */
		function define_classes_functions() {

			require_once TINYPRESS_PLUGIN_DIR . 'includes/classes/class-hooks.php';
			require_once TINYPRESS_PLUGIN_DIR . 'includes/classes/class-functions.php';
			require_once TINYPRESS_PLUGIN_DIR . 'includes/functions.php';

			require_once TINYPRESS_PLUGIN_DIR . 'includes/classes/class-meta-boxes.php';
		}

		/**
		 * Localize Scripts
		 *
		 * @return mixed|void
		 */
		function localize_scripts() {
			return apply_filters( 'tinypress/filters/localize_scripts', array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'copyText'   => esc_html__( 'Copied !', 'tinypress' ),
				'removeConf' => esc_html__( 'Are you really want to remove this schedule?', 'tinypress' ),
			) );
		}


		/**
		 * Load Admin Scripts
		 */
		function admin_scripts() {

			wp_enqueue_script( 'tinypress', plugins_url( '/assets/admin/js/scripts.js', __FILE__ ), array( 'jquery' ), self::$_script_version );
			wp_localize_script( 'tinypress', 'tinypress', $this->localize_scripts() );

			wp_enqueue_style( 'tinypress', TINYPRESS_PLUGIN_URL . 'assets/admin/css/style.css', self::$_script_version );
		}

		/**
		 * Load Scripts
		 */
		function define_scripts() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}
	}
}


function pb_sdk_init_tinypress() {

	if ( ! function_exists( 'get_plugins' ) ) {
		include_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	if ( ! class_exists( 'Pluginbazar\Client' ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/sdk/classes/class-client.php' );
	}

	global $tinypress_sdk;

	$tinypress_sdk = new Pluginbazar\Client( esc_html( 'TinyPress - Best URL Shortener Plugin' ), 'tinypress', 36, __FILE__ );

	do_action( 'pb_sdk_init_tinypress', $tinypress_sdk );
}

/**
 * @global \Pluginbazar\Client $tinypress_sdk
 */
global $tinypress_sdk;

pb_sdk_init_tinypress();

TINYPRESS_Main::instance();
