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

		// for posts
		add_filter( 'manage_post_posts_columns', array( $this, 'tinypress_copy_columns' ) );
		add_action( 'manage_post_posts_custom_column', array( $this, 'tinypress_copy_content' ), 10, 2 );
	}

	/**
	 * tinypress_copy_columns
	 *
	 * @param $columns
	 *
	 * @return array
	 */

	function tinypress_copy_columns( $columns ) {
		$new = array();

		$count = 0;
		foreach ( $columns as $col_id => $col_label ) {
			$count ++;
			if ( $count == 3 ) {
				$new['copy-link'] = esc_html__( 'Copy Link', 'tinypress' );
			}
			if ( 'copy-link' === $col_id ) {
				$new[ $col_id ] = esc_html__( 'Copy Link', 'tinypress' );
			} else {
				$new[ $col_id ] = $col_label;
			}
		}

		return $new;
	}

	/**
	 *tinypress_copy_content
	 *
	 * @param $column
	 * @param $post_id
	 *
	 * @return void
	 */

	function tinypress_copy_content( $column, $post_id ) {
		switch ( $column ) {
			case 'copy-link' :
				echo '<input type="hidden" id="hiddenInput" value="' . tinypress_get_tinyurl( $post_id ) . '">';
				echo '<div class="copy-link tiny-slug-copy">';
				echo '<span><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 115.77 122.88" style="enable-background:new 0 0 115.77 122.88" xml:space="preserve"><style type="text/css">.st0{fill-rule:evenodd;clip-rule:evenodd;}</style><g><path class="st0" d="M89.62,13.96v7.73h12.19h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02v0.02 v73.27v0.01h-0.02c-0.01,3.84-1.57,7.33-4.1,9.86c-2.51,2.5-5.98,4.06-9.82,4.07v0.02h-0.02h-61.7H40.1v-0.02 c-3.84-0.01-7.34-1.57-9.86-4.1c-2.5-2.51-4.06-5.98-4.07-9.82h-0.02v-0.02V92.51H13.96h-0.01v-0.02c-3.84-0.01-7.34-1.57-9.86-4.1 c-2.5-2.51-4.06-5.98-4.07-9.82H0v-0.02V13.96v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07V0h0.02h61.7 h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02V13.96L89.62,13.96z M79.04,21.69v-7.73v-0.02h0.02 c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01 c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v64.59v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h12.19V35.65 v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07v-0.02h0.02H79.04L79.04,21.69z M105.18,108.92V35.65v-0.02 h0.02c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01 c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v73.27v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h61.7h0.02 v0.02c0.91,0,1.75-0.39,2.37-1.01c0.61-0.61,1-1.46,1-2.37h-0.02V108.92L105.18,108.92z"/></g></svg></span>';
				echo '</div>';
				break;
		}
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