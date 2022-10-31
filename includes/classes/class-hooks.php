<?php
/**
 * Class Hooks
 *
 * @author Pluginbazar
 */

use Pluginbazar\Utils;

if ( ! class_exists( 'TINYPRESS_Hooks' ) ) {
	/**
	 * Class TINYPRESS_Hooks
	 */
	class TINYPRESS_Hooks {

		protected static $_instance = null;

		/**
		 * SLIDERXWOO_Hooks constructor.
		 */
		function __construct() {

			add_action( 'init', array( $this, 'register_everything' ) );
			add_action( 'init', array( $this, 'user_reports' ) );
			add_action( 'template_redirect', array( $this, 'redirect_url' ) );
		}

		function redirect_url() {
			$request_uri    = isset( $_SERVER ["REQUEST_URI"] ) ? $_SERVER ["REQUEST_URI"] : '';
			$key            = trim( $request_uri, '/' );
			$post_id        = tinypress()->key_to_post_id( $key );
			$url            = tinypress()->target_url( $post_id );
			$status         = (int) get_post_meta( $post_id, '_redirection', true );
			$get_ip_address = tinypress_get_ip_address();
			$curr_user_id   = get_current_user_id();


			if ( is_404() && ! is_page( $key ) ) {
				global $wpdb;
				$data   = array( 'user_id' => $curr_user_id, 'post_id' => $post_id, 'user_ip' => $get_ip_address );
				$format = array( '%d', '%d', '%s' );
				$wpdb->insert( TINNYPRESS_TABLE_REPORTS, $data, $format );

				if ( wp_safe_redirect( $url, $status ) ) {
					header( "Location: $url", true, 301 );
				}
				die();
			}

		}


		/**
		 * @return TINYPRESS_Hooks
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Register Post Types and Settings
		 */
		function register_everything() {

			global $tinypress_sdk;

			/**
			 * Register Post Types
			 */
			$tinypress_sdk->utils()->register_post_type( 'tinypress_url', array(
				'singular'            => esc_html__( 'Tiny URL', 'tinypress' ),
				'plural'              => esc_html__( 'All Tiny URLs', 'tinypress' ),
				'labels'              => array(
					'menu_name' => esc_html__( 'TinyPress', 'tinypress' ),
					'add_new'   => esc_html__( 'Short an URL', 'tinypress' ),
				),
				'menu_icon'           => 'dashicons-admin-links',
				'supports'            => array( 'title' ),
				'public'              => false,
				'exclude_from_search' => true,
			) );
		}


		/**
		 * Adds a submenu page under a custom post type parent.
		 */
		function user_reports() {
			add_menu_page( esc_html__( 'Reports', 'tinypress' ), esc_html__( 'User Reports', 'tinypress' ), 'manage_options', 'user-reports', array( $this, 'reports_data_table' ), 'dashicons-chart-bar', 36 );
		}

		/**
		 * Display callback for the submenu page.
		 */
		function reports_data_table() {

			require_once 'class-user-reports.php';

			$User_Reports_Table = new User_Reports_Table();
			$current_page       = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
			echo '<div class="wrap">';
			printf( '<h2 class="report-table">%s</h2>', esc_html__( 'All Reports', 'tinypress' ) );
			$User_Reports_Table->prepare_items();
			?>
            <form action="" method="get">
                <input type="hidden" name="page" value="<?php echo esc_attr( $current_page ); ?>"/>
				<?php $User_Reports_Table->search_box( esc_html__( 'Search', 'tinypress' ), 'search_id' ); ?>
            </form> <?php
			$User_Reports_Table->display();
			echo '</div>';

		}

	}
}

TINYPRESS_Hooks::instance();