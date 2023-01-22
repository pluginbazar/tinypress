<?php

use WPDK\Utils;

/**
 * Class Link Columns
 */
class TINYPRESS_Column_link {

	protected static $_instance = null;

	/**
	 * TINYPRESS_Column_link Constructor.
	 */
	function __construct() {

		add_filter( 'manage_tinypress_link_posts_columns', array( $this, 'add_columns' ), 16, 1 );
		add_action( 'manage_tinypress_link_posts_custom_column', array( $this, 'columns_content' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'remove_row_actions' ), 10, 2 );
	}


	/**
	 * Remove row actions for Schedules post type
	 *
	 * @param $actions
	 *
	 * @return mixed
	 */
	function remove_row_actions( $actions ) {

		global $post;

		if ( $post->post_type === 'tinypress_link' ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['view'] );
			unset( $actions['edit'] );
			unset( $actions['trash'] );
		}

		return $actions;
	}


	/**
	 * Add columns content
	 *
	 * @param $column_id
	 * @param $post_id
	 */
	function columns_content( $column_id, $post_id ) {

		switch ( $column_id ) {

			case 'link-title':
				echo '<strong><a class="row-title" href="' . esc_url( get_edit_post_link( $post_id ) ) . '">' . get_the_title( $post_id ) . '</a></strong>';
				break;

			case 'short-link':
				echo tinypress_get_tiny_slug_copier( $post_id, false, array( 'wrapper_class' => 'mini' ) );
				break;

			case 'click-count':

				global $wpdb;

				$click_count = $wpdb->get_var( "SELECT COUNT(*) FROM " . TINYPRESS_TABLE_REPORTS . " WHERE post_id = $post_id" );

				echo '<div class="click-count">' . sprintf( esc_html__( 'Clicked %s times', 'tinypress' ), $click_count ) . '</div>';
				break;

			case 'link-actions':

				echo '<div class="link-actions">';

				echo '<a href="' . esc_url( get_edit_post_link( $post_id ) ) . '" class="action action-edit">' . esc_html__( 'Edit', 'tinypress' ) . '</a>';
				echo '<a href="' . esc_url( get_delete_post_link( $post_id ) ) . '" class="action action-delete">' . esc_html__( 'Delete', 'tinypress' ) . '</a>';

				echo '</div>';

				break;

			default:
				break;
		}
	}


	/**
	 * Add columns on Schedules listing
	 *
	 * @return string[]
	 */
	function add_columns( $columns ) {

		$new_columns = array(
			'cb'           => Utils::get_args_option( 'cb', $columns ),
			'link-title'   => esc_html__( 'Link Title', 'tinypress' ),
			'short-link'   => esc_html__( 'Shorten Link', 'tinypress' ),
			'click-count'  => esc_html__( 'Stats', 'tinypress' ),
			'link-actions' => esc_html__( 'Actions', 'tinypress' ),
		);

		return apply_filters( 'TINYPRESS/Filters/link_columns', $new_columns, $columns );
	}


	/**
	 * @return TINYPRESS_Column_link
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}

TINYPRESS_Column_link::instance();