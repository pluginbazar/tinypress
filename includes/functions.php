<?php
/*
* @Author 		pluginbazar
* Copyright: 	2022 pluginbazar
*/

defined( 'ABSPATH' ) || exit;


if ( ! function_exists( 'tinylinks' ) ) {
	/**
	 * @return TINYLINKS_Functions
	 */
	function tinylinks() {
		global $tinylinks;

		if ( empty( $tinylinks ) ) {
			$tinylinks = new TINYLINKS_Functions();
		}

		return $tinylinks;
	}
}


if ( ! function_exists( 'tinylinks_generate_random_string' ) ) {
	/**
	 * Generate random string
	 *
	 * @param int $length
	 *
	 * @return string
	 */
	function tinylinks_generate_random_string( $length = 5 ) {
		$characters       = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen( $characters );
		$randomString     = '';

		for ( $i = 0; $i < $length; $i ++ ) {
			$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
		}

		return strtolower( $randomString );
	}
}


if ( ! function_exists( 'tinylinks_create_url_slug' ) ) {
	/**Create url slug
	 *
	 * @param string $given_string
	 *
	 * @return mixed|string
	 */
	function tinylinks_create_url_slug( $given_string = '' ) {
		global $wpdb;

		$given_string = empty( $given_string ) ? tinylinks_generate_random_string() : $given_string;
		$post_id      = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value like %s", $given_string ) );

		if ( ! empty( $post_id ) ) {
			$given_string = tinylinks_create_url_slug();
		}

		return $given_string;
	}
}


if ( ! function_exists( 'tinylinks_get_ip_address' ) ) {
	/**get user ip
	 *
	 * @return mixed
	 */

	function tinylinks_get_ip_address() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}
}

