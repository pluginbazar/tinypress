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

defined( 'ABSPATH' ) || exit;

defined( 'ABSPATH' ) || exit;
defined( 'TINYPRESS_PLUGIN_URL' ) || define( 'TINYPRESS_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
defined( 'TINYPRESS_PLUGIN_DIR' ) || define( 'TINYPRESS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
defined( 'TINYPRESS_PLUGIN_FILE' ) || define( 'TINYPRESS_PLUGIN_FILE', plugin_basename( __FILE__ ) );
defined( 'TINYPRESS_PLUGIN_VERSION' ) || define( 'TINYPRESS_PLUGIN_VERSION', '1.0.0' );

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

			register_activation_hook( __FILE__, [ $this, 'tinypress_data_table' ] );
			add_action( 'wp_head', array( $this, 'wpdocs_pingbackurl_example' ) );
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


		function tinypress_data_table() {

			global $wpdb;

			$table_name = $wpdb->prefix . "tinypress";

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id int(20) NOT NULL AUTO_INCREMENT,
      target_url varchar(200) NOT NULL,
      short_url varchar(200) NOT NULL,
      redirection varchar(200) NOT NULL,
      domain varchar(200) NOT NULL,
      notes varchar(200) NOT NULL,
      PRIMARY KEY id (id)
    ) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}


		/**
		 * Load Textdomain
		 */
		function load_textdomain() {
			load_plugin_textdomain( 'tinypress', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
		}


		function wpdocs_pingbackurl_example() {
			if ( is_singular() && pings_open() ) {
				echo '<link rel="pingback" href="' . esc_url( get_bloginfo( '_targetURL' ) ) . '">';
			}
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
//	$tinypress_sdk->notifications();

	do_action( 'pb_sdk_init_tinypress', $tinypress_sdk );
}

/**
 * @global \Pluginbazar\Client $tinypress_sdk
 */
global $tinypress_sdk;

pb_sdk_init_tinypress();

TINYPRESS_Main::instance();


add_action( 'wp_head', function () {

	global $wp, $wp_query, $wpdb;

	$url_string = $wp->request;
	$url_posts  = get_posts( array(
		'post_type'      => 'tinypress_url',
		'post_status'    => 'publish',
		'posts_per_page' => '1',
	) );



//	update_post_meta( 13, '_short_string', 'e7c0u' );
//	update_post_meta( 14, '_short_string', '56k08' );

	tinypress_create_url_slug();

	die();
}, 0 );