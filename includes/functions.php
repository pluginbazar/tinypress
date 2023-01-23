<?php
/*
* @Author 		pluginbazar
* Copyright: 	2022 pluginbazar
*/

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;


if ( ! function_exists( 'tinypress' ) ) {
	/**
	 * @return TINYPRESS_Functions
	 */
	function tinypress() {
		global $tinypress;

		if ( empty( $tinypress ) ) {
			$tinypress = new TINYPRESS_Functions();
		}

		return $tinypress;
	}
}


if ( ! function_exists( 'tinypress_generate_random_string' ) ) {
	/**
	 * Generate random string
	 *
	 * @param int $length
	 *
	 * @return string
	 */
	function tinypress_generate_random_string( $length = 5 ) {
		$characters       = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen( $characters );
		$randomString     = '';

		for ( $i = 0; $i < $length; $i ++ ) {
			$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
		}

		return strtolower( $randomString );
	}
}


if ( ! function_exists( 'tinypress_create_url_slug' ) ) {
	/**Create url slug
	 *
	 * @param string $given_string
	 *
	 * @return mixed|string
	 */
	function tinypress_create_url_slug( $given_string = '' ) {
		global $wpdb;

		$given_string = empty( $given_string ) ? tinypress_generate_random_string() : $given_string;
		$post_id      = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value like %s", $given_string ) );

		if ( ! empty( $post_id ) ) {
			$given_string = tinypress_create_url_slug();
		}

		return $given_string;
	}
}


if ( ! function_exists( 'tinypress_get_ip_address' ) ) {
	/**get user ip
	 *
	 * @return mixed
	 */

	function tinypress_get_ip_address() {
		if ( ! empty( sanitize_text_field( $_SERVER['HTTP_CLIENT_IP'] ) ) ) {
			$ip = sanitize_text_field( $_SERVER['HTTP_CLIENT_IP'] );
		} elseif ( ! empty( sanitize_text_field( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) {
			$ip = sanitize_text_field( $_SERVER['HTTP_X_FORWARDED_FOR'] );
		} else {
			$ip = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
		}

		return $ip;
	}
}


if ( ! function_exists( 'tinypress_get_tiny_slug_copier' ) ) {
	/**
	 * TinyPress get tiny slug copier
	 *
	 * @param $post_id
	 * @param $display_input_field
	 * @param $args
	 *
	 * @return false|string
	 */
	function tinypress_get_tiny_slug_copier( $post_id, $display_input_field = false, $args = array() ) {

		global $post;

		$default_string   = Utils::get_args_option( 'default', $args );
		$wrapper_class    = Utils::get_args_option( 'wrapper_class', $args );
		$tiny_slug        = Utils::get_meta( 'tiny_slug', $post_id, $default_string );
		$link_prefix      = Utils::get_option( 'tinypress_link_prefix' );
		$link_prefix_slug = '';

		if ( '1' == $link_prefix ) {
			$link_prefix_slug = Utils::get_option( 'tinypress_link_prefix_slug', 'go' );
		}

		ob_start();

		echo '<div class="tiny-slug-wrap ' . esc_attr( $wrapper_class ) . '">';

		echo '<div class="tiny-slug-preview hint--top" aria-label="' . tinypress()::$text_hint . '" data-text-copied="' . tinypress()::$text_copied . '">';
		echo '<span class="prefix">' . esc_url( site_url( '/' . $link_prefix_slug . '/' ) ) . '</span>';
		echo '<span class="tiny-slug"> ' . esc_attr( $tiny_slug ) . ' </span>';
		echo '</div>';

		if ( $display_input_field ) {

			echo '<div class="tinypress-slug-field">';
			if ( 'tinypress_link' == $post->post_type ) {
				echo '<input type="text" class="tinypress-tiny-slug" name="tinypress_meta_main[tiny_slug]" value="' . esc_attr( $tiny_slug ) . '" placeholder="ad34o">';
			} else {
				echo '<input type="text" class="tinypress-tiny-slug" name="tinypress_meta_side_' . $post->post_type . '[tiny_slug]" value="' . esc_attr( $tiny_slug ) . '" placeholder="ad34o">';
			}
			echo '</div>';
		}

		echo '</div>';

		return ob_get_clean();
	}
}






