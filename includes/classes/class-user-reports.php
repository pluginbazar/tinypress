<?php
defined( 'ABSPATH' ) || exit;


//Loading table class

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


//Extending class

class User_Reports_Table extends WP_List_Table {
	private function get_users_data() {
		global $wpdb;

		return $wpdb->get_results( "SELECT id,post_id,user_ip,datetime, COUNT(*) as hits_count FROM " . TINNYPRESS_TABLE_REPORTS . " GROUP BY post_id", ARRAY_A );
	}

	/**
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'id'         => esc_html__( 'ID', 'tinypress' ),
			'post_id'    => esc_html__( 'Post ID', 'tinypress' ),
			'hits_count' => esc_html__( 'Hits Count', 'tinypress' ),
			'user_ip'    => esc_html__( 'User IP', 'tinypress' ),
			'datetime'   => esc_html__( 'Date Time', 'tinypress' ),

		);

		return $columns;
	}

	/**
	 * @return void
	 */
	function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $this->get_users_data();
	}

	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @return mixed|void
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
			case 'post_id':
			case 'hits_count':
			case 'user_ip':
			case 'datetime':
			default:
				return $item[ $column_name ];
		}
	}

	/**
	 * @param $item
	 *
	 * @return string|void
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="emp[]" value="%s" />',
			$item['id']
		);
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	function column_post_id( $item ) {
		$post_id = isset( $item['post_id'] ) ? $item['post_id'] : '';

		return sprintf( '<div class="post-id">%s</div>', $post_id );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	function column_datetime( $item ) {
		$post_id = isset( $item['datetime'] ) ? $item['datetime'] : '';

		return sprintf( '<div class="date-time">%s</div>', $post_id );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	function column_hits_count( $item ) {
		$count = isset( $item['hits_count'] ) ? $item['hits_count'] : '';

		return sprintf( '<div class="date-time">%s</div>', $count );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	function column_user_ip( $item ) {

		$count = isset( $item['user_ip'] ) ? $item['user_ip'] : '';

		return sprintf( '<div class="user-ip">%s</div>', $count );
	}

}