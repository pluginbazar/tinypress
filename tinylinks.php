<?php
/*
	Plugin Name: TinyLinks
	Plugin URI: https://pluginbazar.com/plugin/tinylinks
	Description: Best URL Shortener Plugin
	Version: 1.0.0
	Text Domain: tinylinks
	Author: Pluginbazar
	Author URI: https://pluginbazar.com/
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

global $wpdb;
defined( 'ABSPATH' ) || exit;

defined( 'TINYLINKS_PLUGIN_URL' ) || define( 'TINYLINKS_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
defined( 'TINYLINKS_PLUGIN_DIR' ) || define( 'TINYLINKS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
defined( 'TINYLINKS_PLUGIN_FILE' ) || define( 'TINYLINKS_PLUGIN_FILE', plugin_basename( __FILE__ ) );
defined( 'TINYLINKS_PLUGIN_VERSION' ) || define( 'TINYLINKS_PLUGIN_VERSION', '1.0.0' );
defined( 'TINYLINKS_TABLE_REPORTS' ) || define( 'TINYLINKS_TABLE_REPORTS', sprintf( '%stinylinks_reports', $wpdb->prefix ) );

if ( ! class_exists( 'TINYLINKS_Main' ) ) {
	/**
	 * Class TINYLINKS_Main
	 */
	class TINYLINKS_Main {

		protected static $_instance = null;

		protected static $_script_version = null;

		/**
		 * TINYLINKS_Main constructor.
		 */
		function __construct() {
			self::$_script_version = defined( 'WP_DEBUG' ) && WP_DEBUG ? current_time( 'U' ) : TINYLINKS_PLUGIN_VERSION;

			$this->define_scripts();
			$this->define_classes_functions();

			add_action( 'init', array( $this, 'create_data_table' ) );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		}

		/**
		 * @return TINYLINKS_Main
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Create data table
		 *
		 * @return void
		 */

		function create_data_table() {
			if ( ! function_exists( 'maybe_create_table' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			}

			$sql_create_table = "CREATE TABLE " . TINYLINKS_TABLE_REPORTS . " (
                            id int(50) NOT NULL AUTO_INCREMENT,
                            user_id varchar(50) NOT NULL,
                            post_id varchar(50) NOT NULL,
						    user_ip varchar(255) NOT NULL,
						    user_location varchar(1024) NOT NULL,
                            datetime  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (id)
                            );";

			maybe_create_table( TINYLINKS_TABLE_REPORTS, $sql_create_table );
		}


		/**
		 * Load Textdomain
		 */
		function load_textdomain() {
			load_plugin_textdomain( 'tinylinks', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Include Classes and Functions
		 */
		function define_classes_functions() {
			require_once TINYLINKS_PLUGIN_DIR . 'includes/classes/class-hooks.php';
			require_once TINYLINKS_PLUGIN_DIR . 'includes/classes/class-functions.php';
			require_once TINYLINKS_PLUGIN_DIR . 'includes/functions.php';
			require_once TINYLINKS_PLUGIN_DIR . 'includes/classes/class-meta-boxes.php';
		}

		/**
		 * Localize Scripts
		 *
		 * @return mixed|void
		 */
		function localize_scripts() {
			return apply_filters( 'tinylinks/filters/localize_scripts', array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'copyText'   => esc_html__( 'Copied !', 'tinylinks' ),
				'removeConf' => esc_html__( 'Are you really want to remove this schedule?', 'tinylinks' ),
			) );
		}


		/**
		 * Load Admin Scripts
		 */
		function admin_scripts() {
			wp_enqueue_script( 'tinylinks', plugins_url( '/assets/admin/js/scripts.js', __FILE__ ), array( 'jquery' ), self::$_script_version );
			wp_localize_script( 'tinylinks', 'tinylinks', $this->localize_scripts() );

			wp_enqueue_style( 'tinylinks', TINYLINKS_PLUGIN_URL . 'assets/admin/css/style.css', self::$_script_version );
			wp_enqueue_style( 'tinylinks-tool-tip', TINYLINKS_PLUGIN_URL . 'assets/hint.min.css' );
		}

		/**
		 * Load Scripts
		 */
		function define_scripts() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}
	}
}


function pb_sdk_init_tinylinks() {
	if ( ! function_exists( 'get_plugins' ) ) {
		include_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	if ( ! class_exists( 'Pluginbazar\Client' ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wpdk/classes/class-client.php' );
	}

	global $tinylinks_sdk;

	$tinylinks_sdk = new WPDK\Client( esc_html( 'TinyLinks - Best URL Shortener Plugin' ), 'tinylinks', 36, __FILE__ );

	do_action( 'pb_sdk_init_tinylinks', $tinylinks_sdk );
}

/**
 * @global \WPDK\Client $tinylinks_sdk
 */
global $tinylinks_sdk;

pb_sdk_init_tinylinks();

TINYLINKS_Main::instance();
