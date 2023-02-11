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


if ( ! function_exists( 'user_role_management' ) ) {
	/**
	 * Get user role management
	 *
	 * @return array
	 */

	function user_role_management() {
		$role  = array();
		$roles = wp_roles()->roles;

		foreach ( $roles as $key => $value ) {
			$role[ $key ] = $value['name'] ?? $key;
		}

		return $role;
	}
}


function tinypress_create_shorten_url( $args = array() ) {

	if ( empty( $target_url = Utils::get_args_option( 'target_url', $args ) ) ) {
		return new WP_Error( 404, esc_html__( 'Target url not found.', 'tinypress' ) );
	}

	if ( empty( $tiny_slug = Utils::get_args_option( 'tiny_slug', $args, tinypress_create_url_slug() ) ) ) {
		return new WP_Error( 404, esc_html__( 'Tiny slug could not created.', 'tinypress' ) );
	}

	$url_args = array(
		'post_title'  => wp_strip_all_tags( Utils::get_args_option( 'post_title', $args ) ),
		'post_type'   => 'tinypress_link',
		'post_status' => 'publish',
		'post_author' => get_current_user_id(),
		'meta_input'  => array(
			'target_url'  => $target_url,
			'tiny_slug'   => $tiny_slug,
			'redirection' => Utils::get_args_option( 'redirection', $args, 302 ),
			'notes'       => Utils::get_args_option( 'notes', $args ),
		),
	);

	$new_url_id = wp_insert_post( $url_args );

	if ( is_wp_error( $new_url_id ) ) {
		return $new_url_id;
	}

	return tinypress_get_tinyurl( $new_url_id );
}


if ( ! function_exists( 'tinypress_get_tinyurl' ) ) {
	/**
	 * Return tinyurl from slug or post ID
	 *
	 * @param $tiny_slug_or_post_id
	 *
	 * @return mixed|null
	 */
	function tinypress_get_tinyurl( $tiny_slug_or_post_id ) {
		$tinyurl = site_url();

		if ( is_string( $tiny_slug_or_post_id ) ) {
			$tinyurl = site_url( $tiny_slug_or_post_id );
		}

		if ( $tiny_slug_or_post_id > 0 ) {
			$tinyurl = site_url( Utils::get_meta( 'tiny_slug', $tiny_slug_or_post_id ) );
		}

		return apply_filters( 'TINYPRESS/Filters/get_tinyurl', $tinyurl );
	}
}
