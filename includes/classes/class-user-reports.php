<?php

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Extending class
 */
class User_Reports_Table extends WP_List_Table {
	private $users_data;

	private function get_users_data() {
		global $wpdb;

		return $wpdb->get_results( "SELECT post_id,user_id,user_ip,user_location, datetime, COUNT(*) as clicks_count FROM " . TINYLINKS_TABLE_REPORTS . " GROUP BY post_id", ARRAY_A );
	}

	/**
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'title'         => esc_html__( 'Title', 'tinylinks' ),
			'short_url'     => esc_html__( 'Short Link', 'tinylinks' ),
			'clicks_count'  => esc_html__( 'Clicks Count', 'tinylinks' ),
			'user_location' => esc_html__( 'User Location', 'tinylinks' ),
			'datetime'      => esc_html__( 'Date Time', 'tinylinks' ),

		);

		return $columns;
	}

	/**
	 * @return void
	 */
	function prepare_items() {
		$this->users_data = $this->get_users_data();

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/* pagination */
		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$total_items  = count( $this->users_data );

		$this->users_data = array_slice( $this->users_data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
		) );

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
			case 'title':
			case 'short_url':
			case 'clicks_count':
			case 'user_location':
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
		$id = Utils::get_args_option( 'id', $item );

		return sprintf(
			'<input type="checkbox" name="emp[]" value="%s" />',
			$id,
		);
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
			'<div class="post-title"><a href="post.php?post=%s&action=edit">%s</a></div>',
			$post_id,
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
	function column_user_location( $item ) {
		$city          = '';
		$user_id       = Utils::get_args_option( 'user_id', $item );
		$user_location = Utils::get_args_option( 'user_location', $item );
		$user_location = json_decode( $user_location, true );
		$user_id       = get_user_by( 'id', $user_id );
		$country       = isset( $user_location['geoplugin_countryName'] ) ? Utils::get_args_option( 'geoplugin_countryName', $user_location ) : esc_html__( 'earth', 'tinylinks' );

		if ( ! empty( $user_location['geoplugin_city'] ) ) {
			$city = Utils::get_args_option( 'geoplugin_regionName', $user_location ) . ',';
		} elseif ( ! empty( $user_location['geoplugin_regionName'] ) ) {
			$city = Utils::get_args_option( 'geoplugin_regionName', $user_location );
		}

		if ( $user_id->ID == 0 ) {
			$user = esc_html__( 'Someone', 'tinylinks' );
		} else {
			$user = ucfirst( $user_id->display_name );
		}
		$from_text = esc_html__( 'from', 'tinylinks' );

		return sprintf( '<div class="user-location">%s %s %s %s</div>', $user, $from_text, $city, $country );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	function column_datetime( $item ) {
		$date_time = Utils::get_args_option( 'datetime', $item );
		$date_time = strtotime( $date_time );
		$time      = date( 'jS M, y - h:i a', $date_time );

		return sprintf( '<div class="date-time">%s</div>', $time );
	}

}