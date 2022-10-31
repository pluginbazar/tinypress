<?php

use Pluginbazar\Utils;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Extending class
 */
class User_Reports_Table extends WP_List_Table {
	private $users_data;

	private function get_users_data( $search = "" ) {
		global $wpdb;
		if ( ! empty( $search ) ) {
			return $wpdb->get_results( "SELECT user_ip FROM " . TINNYPRESS_TABLE_REPORTS . " WHERE user_ip = '%{$search}%'", ARRAY_A );
		} else {
			return $wpdb->get_results( "SELECT post_id,user_ip,datetime, COUNT(*) as clicks_count FROM " . TINNYPRESS_TABLE_REPORTS . " GROUP BY post_id", ARRAY_A );
		}

	}

	/**
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'post_id'      => esc_html__( 'Post ID', 'tinypress' ),
			'title'        => esc_html__( 'Title', 'tinypress' ),
			'short_url'    => esc_html__( 'Short Link', 'tinypress' ),
			'clicks_count' => esc_html__( 'Clicks Count', 'tinypress' ),
			'user_ip'      => esc_html__( 'User IP', 'tinypress' ),
			'user_country' => esc_html__( 'Country', 'tinypress' ),
			'datetime'     => esc_html__( 'Date Time', 'tinypress' ),

		);

		return $columns;
	}

	/**
	 * @return void
	 */
	function prepare_items() {
		if ( isset( $_POST['page'] ) && isset( $_POST['s'] ) ) {
			$this->users_data = $this->get_users_data( $_POST['s'] );
		} else {
			$this->users_data = $this->get_users_data();
		}

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/* pagination */
		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$total_items  = count( $this->users_data );

		$this->users_data = array_slice( $this->users_data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		) );

		usort( $this->users_data, array( &$this, 'usort_reorder' ) );


		$this->items = $this->get_users_data();
		$this->items = $this->users_data;
	}

	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @return mixed|void
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'post_id':
			case 'title':
			case 'short_url':
			case 'clicks_count':
			case 'user_ip':
			case 'user_country':
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
		$post_id = Utils::get_args_option( 'post_id', $item );

		return sprintf( '<div class="post-id">%s</div>', $post_id );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	function column_title( $item ) {
		$post_id = Utils::get_args_option( 'post_id', $item );
		$title   = get_post( $post_id );
		$title   = $title->post_title;

		return sprintf(
			'<div class="post-title">%s</div>',
			ucwords( $title )
		);
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	function column_short_url( $item ) {

		$id         = Utils::get_args_option( 'post_id', $item );
		$short_link = get_post_meta( $id, '_short_string', true );
		$url        = get_site_url();

		return sprintf( '<div class="shortstring hint--top" aria-label="Click here to copy">%s/%s</div>', $url, $short_link );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	function column_clicks_count( $item ) {
		$count = Utils::get_args_option( 'clicks_count', $item );

		return sprintf( '<div class="clicks-count">%s</div>', $count );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	function column_user_ip( $item ) {

		$count = Utils::get_args_option( 'user_ip', $item );

		return sprintf( '<div class="user-ip">%s</div>', $count );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	function column_user_country( $item ) {
		$ip = Utils::get_args_option( 'user_ip', $item );

		$user_country = @json_decode( file_get_contents(
			"http://www.geoplugin.net/json.gp?ip=" . $ip ) );

		return sprintf( '<div class="user-country">%s</div>', $user_country->geoplugin_countryName );


	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	function column_datetime( $item ) {
		$post_id = Utils::get_args_option( 'datetime', $item );

		return sprintf( '<div class="date-time">%s</div>', $post_id );
	}

}