<?php
/*
* @Author 		pluginbazar
* Copyright: 	2015 pluginbazar
*/

use Pluginbazar\Utils;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'sliderxwoo_get_template_part' ) ) {
	/**
	 * Get Template Part
	 *
	 * @param $slug
	 * @param string $name
	 * @param array $args
	 * @param bool $main_template | When you call a template from extensions you can use this param as true to check from main template only
	 */
	function sliderxwoo_get_template_part( $slug, $name = '', $args = array(), $main_template = false ) {

		$template   = '';
		$plugin_dir = SLIDERXWOO_PLUGIN_DIR;

		/**
		 * Locate template
		 */
		if ( $name ) {
			$template = locate_template( array(
				"{$slug}-{$name}.php",
				"sliderxwoo/{$slug}-{$name}.php"
			) );
		}

		/**
		 * Check directory for templates from Addons
		 */
		$backtrace      = debug_backtrace( 2, true );
		$backtrace      = empty( $backtrace ) ? array() : $backtrace;
		$backtrace      = reset( $backtrace );
		$backtrace_file = isset( $backtrace['file'] ) ? $backtrace['file'] : '';

		// Search in SliderXWoo Pro
		if ( strpos( $backtrace_file, 'slider-x-woo-pro' ) !== false && defined( 'SLIDERXWOO_PRO_PLUGIN_DIR' ) ) {
			$plugin_dir = $main_template ? SLIDERXWOO_PLUGIN_DIR : SLIDERXWOO_PRO_PLUGIN_DIR;
		}


		/**
		 * Search for Template in Plugin
		 *
		 * @in Plugin
		 */
		if ( ! $template && $name && file_exists( untrailingslashit( $plugin_dir ) . "/templates/{$slug}-{$name}.php" ) ) {
			$template = untrailingslashit( $plugin_dir ) . "/templates/{$slug}-{$name}.php";
		}


		/**
		 * Search for Template in Theme
		 *
		 * @in Theme
		 */
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", "sliderxwoo/{$slug}.php" ) );
		}


		/**
		 * Allow 3rd party plugins to filter template file from their plugin.
		 *
		 * @filter sliderxwoo_filters_get_template_part
		 */
		$template = apply_filters( 'sliderxwoo_filters_get_template_part', $template, $slug, $name );

		if ( $template ) {
			load_template( $template, false );
		}
	}
}


if ( ! function_exists( 'sliderxwoo_get_template' ) ) {
	/**
	 * Get Template
	 *
	 * @param $template_name
	 * @param array $args
	 * @param string $template_path
	 * @param string $default_path
	 * @param bool $main_template | When you call a template from extensions you can use this param as true to check from main template only
	 *
	 * @return WP_Error
	 */
	function sliderxwoo_get_template( $template_name, $args = array(), $template_path = '', $default_path = '', $main_template = false ) {

		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		/**
		 * Check directory for templates from Addons
		 */
		$backtrace      = debug_backtrace( 2, true );
		$backtrace      = empty( $backtrace ) ? array() : $backtrace;
		$backtrace      = reset( $backtrace );
		$backtrace_file = isset( $backtrace['file'] ) ? $backtrace['file'] : '';

		$located = sliderxwoo_locate_template( $template_name, $template_path, $default_path, $backtrace_file, $main_template );

		if ( ! file_exists( $located ) ) {
			return new WP_Error( 'invalid_data', __( '%s does not exist.', 'slider-x-woo' ), '<code>' . $located . '</code>' );
		}

		$located = apply_filters( 'sliderxwoo_filters_get_template', $located, $template_name, $args, $template_path, $default_path );

		do_action( 'sliderxwoo_before_template_part', $template_name, $template_path, $located, $args );

		include $located;

		do_action( 'sliderxwoo_after_template_part', $template_name, $template_path, $located, $args );
	}
}


if ( ! function_exists( 'sliderxwoo_locate_template' ) ) {
	/**
	 *  Locate template
	 *
	 * @param $template_name
	 * @param string $template_path
	 * @param string $default_path
	 * @param string $backtrace_file
	 * @param bool $main_template | When you call a template from extensions you can use this param as true to check from main template only
	 *
	 * @return mixed|void
	 */
	function sliderxwoo_locate_template( $template_name, $template_path = '', $default_path = '', $backtrace_file = '', $main_template = false ) {

		$plugin_dir = SLIDERXWOO_PLUGIN_DIR;

		/**
		 * Template path in Theme
		 */
		if ( ! $template_path ) {
			$template_path = 'sliderxwoo/';
		}

		// Check for SliderXWoo Pro
		if ( ! empty( $backtrace_file ) && strpos( $backtrace_file, 'slider-x-woo-pro' ) !== false && defined( 'SLIDERXWOO_PRO_PLUGIN_DIR' ) ) {
			$plugin_dir = $main_template ? SLIDERXWOO_PLUGIN_DIR : SLIDERXWOO_PRO_PLUGIN_DIR;
		}


		/**
		 * Template default path from Plugin
		 */
		if ( ! $default_path ) {
			$default_path = untrailingslashit( $plugin_dir ) . '/templates/';
		}

		/**
		 * Look within passed path within the theme - this is priority.
		 */
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		/**
		 * Get default template
		 */
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		/**
		 * Return what we found with allowing 3rd party to override
		 *
		 * @filter sliderxwoo_filters_locate_template
		 */
		return apply_filters( 'sliderxwoo_filters_locate_template', $template, $template_name, $template_path );
	}
}


if ( ! function_exists( 'sliderxwoo_schedule_classes' ) ) {
	/**
	 * Render schedule classes
	 *
	 * @param $day_id
	 * @param string $classes
	 */
	function sliderxwoo_schedule_classes( $day_id, $classes = '' ) {

		$classes = is_string( $classes ) ? explode( ' ', $classes ) : array();

		// Default class
		$classes[] = 'sliderxwoo-schedule';

		// Status class
		$classes[] = sliderxwoo()->get_current_day_id() == $day_id ? 'current opened' : '';

		// Open/Close class
		$classes[] = sliderxwoo_is_open() ? 'shop-open' : 'shop-close';

		printf( 'class="%s"', apply_filters( 'sliderxwoo_filters_schedule_classes', implode( ' ', $classes ) ) );
	}
}


if ( ! function_exists( 'sliderxwoo' ) ) {
	function sliderxwoo() {

		global $tinypressxwoo;

		if ( empty( $tinypressxwoo ) ) {
			$tinypressxwoo = new SLIDERXWOO_Functions();
		}

		return $tinypressxwoo;
	}
}


if ( ! function_exists( 'sliderxwoo_get_slider' ) ) {
	/**
	 * Return slider object
	 *
	 * @param int $tinypress_id
	 *
	 * @return SLIDERXWOO_Slider_base|null
	 */
	function sliderxwoo_get_slider( $tinypress_id = 0 ) {

		if ( $tinypress_id == 0 || empty( $tinypress_id ) ) {
			return null;
		}

		return new SLIDERXWOO_Slider_base( $tinypress_id );
	}
}


if ( ! function_exists( 'sliderxwoo_get_slider_title' ) ) {
	/**
	 * Get slider title HTML
	 *
	 * @param array $args
	 *
	 * @retun string
	 */
	function sliderxwoo_get_slider_title( $args = array() ) {

		global $tinypress;

		if ( ! $tinypress->get_meta( '_slider_title', true ) ) {
			return '';
		}

		$args = wp_parse_args( $args, array(
			'slider_id' => $tinypress->id,
			'class'     => 'slider-title',
			'before'    => '',
			'after'     => '',
			'echo'      => true,
			'wrapper'   => 'h2',
		) );

		ob_start();

		echo wp_kses_post( $args['before'] );

		echo esc_html( $tinypress->get_title() );

		echo wp_kses_post( $args['after'] );

		$tinypress_title = sprintf( '<%1$s class="%2$s">%3$s</%1$s>', $args['wrapper'], $args['class'], ob_get_clean() );
		$tinypress_title = apply_filters( 'SLIDERXWOO/Filters/slider_title', $tinypress_title, $args );

		if ( $args['echo'] ) {
			echo wp_kses_post( $tinypress_title );

			return '';
		}

		return $tinypress_title;
	}
}


if ( ! function_exists( 'sliderxwoo_get_slide_item_thumbnail' ) ) {
	/**
	 * Get slider item thumbnail HTML
	 *
	 * @param $product WC_Product
	 * @param array $args
	 *
	 * @retun string
	 */
	function sliderxwoo_get_slide_item_thumbnail( $product, $args = array() ) {

		global $tinypress;

		if ( ! $tinypress->get_meta( '_item_thumb', true ) ) {
			return '';
		}
		
		$args = wp_parse_args( $args, array(
			'slider_id' => $tinypress->id,
			'class'     => 'slider-img',
			'before'    => '',
			'after'     => '',
			'echo'      => true,
			'wrapper'   => 'div',
			'size'      => '',
			'href'      => true,
		) );

		ob_start();

		echo wp_kses_post( $args['before'] );

		printf( '<div class="thumbnail">%s%s%s</div>',
			( $args['href'] ? '<a href="' . $product->get_permalink() . '">' : '' ),
			$product->get_image( $args['size'] ),
			( $args['href'] ? '</a>' : '' )
		);

		echo wp_kses_post( $args['after'] );

		$slide_item_thumbnail = sprintf( '<%1$s class="%2$s">%3$s</%1$s>', $args['wrapper'], $args['class'], ob_get_clean() );
		$slide_item_thumbnail = apply_filters( 'SLIDERXWOO/Filters/slide_item_thumbnail', $slide_item_thumbnail, $product, $args );

		if ( $args['echo'] ) {
			echo wp_kses_post( $slide_item_thumbnail );

			return '';
		}

		return $slide_item_thumbnail;
	}
}


if ( ! function_exists( 'sliderxwoo_get_slide_item_title' ) ) {
	/**
	 * Get slider item title HTML
	 *
	 * @param $product WC_Product
	 * @param array $args
	 *
	 * @retun string
	 */
	function sliderxwoo_get_slide_item_title( $product, $args = array() ) {

		global $tinypress;

		if ( ! $tinypress->get_meta( '_item_title', false ) ) {
			return '';
		}

		$args       = wp_parse_args( $args, array(
			'slider_id' => $tinypress->id,
			'class'     => 'title',
			'before'    => '',
			'after'     => '',
			'echo'      => true,
			'wrapper'   => 'h4',
			'href'      => true,
		) );
		$item_title = $product->get_name();

		if ( $tinypress->get_meta( '_title_limit', false ) ) {
			$limit_length = $tinypress->get_meta( '_title_limit_length', 3 );
			$item_title   = wp_trim_words( $item_title, $limit_length, '' );
		}

		ob_start();

		echo wp_kses_post( $args['before'] );

		if ( $args['href'] ) {
			echo '<a href="' . esc_url( $product->get_permalink() ) . '">' . esc_html( $item_title ) . '</a>';
		} else {
			echo '<span>' . esc_html( $item_title ) . '</span>';
		}

		echo wp_kses_post( $args['after'] );

		$slide_item_title = sprintf( '<%1$s class="%2$s">%3$s</%1$s>', $args['wrapper'], $args['class'], ob_get_clean() );
		$slide_item_title = apply_filters( 'SLIDERXWOO/Filters/slide_item_title', $slide_item_title, $product, $args );

		if ( $args['echo'] ) {
			echo wp_kses_post( $slide_item_title );

			return '';
		}

		return $slide_item_title;
	}
}

if ( ! function_exists( 'sliderxwoo_get_slide_item_desc' ) ) {
	/**
	 * Get slider item description HTML
	 *
	 * @param $product WC_Product
	 * @param array $args
	 *
	 * @retun string
	 */
	function sliderxwoo_get_slide_item_desc( $product, $args = array() ) {

		global $tinypress;

		if ( ! $tinypress->get_meta( '_item_desc', true ) ) {
			return '';
		}

		$args      = wp_parse_args( $args, array(
			'slider_id'  => $tinypress->id,
			'class'      => 'desc',
			'before'     => '',
			'after'      => '',
			'echo'       => true,
			'wrapper'    => 'p',
			'word_count' => $tinypress->get_meta( '_desc_limit_length', 10 ),
		) );
		$item_desc = wp_trim_words( $product->get_description(), $args['word_count'], '' );

		ob_start();

		echo wp_kses_post( $args['before'] );

		echo esc_html( $item_desc );

		echo wp_kses_post( $args['after'] );

		$slide_item_desc = sprintf( '<%1$s class="%2$s">%3$s</%1$s>', $args['wrapper'], $args['class'], ob_get_clean() );
		$slide_item_desc = apply_filters( 'SLIDERXWOO/Filters/slide_item_desc', $slide_item_desc, $product, $args );

		if ( $args['echo'] ) {
			echo wp_kses_post( $slide_item_desc );

			return '';
		}

		return $slide_item_desc;
	}
}


if ( ! function_exists( 'sliderxwoo_get_slide_item_price' ) ) {
	/**
	 * Get slider item price HTML
	 *
	 * @param $product WC_Product
	 * @param array $args
	 *
	 * @retun string
	 */
	function sliderxwoo_get_slide_item_price( $product, $args = array() ) {

		global $tinypress;

		if ( ! $tinypress->get_meta( '_item_price', true ) ) {
			return '';
		}

		$args = wp_parse_args( $args, array(
			'slider_id' => $tinypress->id,
			'class'     => 'price',
			'before'    => '',
			'after'     => '',
			'echo'      => true,
			'wrapper'   => 'div',
		) );

		ob_start();

		echo wp_kses_post( $args['before'] );

		echo $product->get_price_html();

		echo wp_kses_post( $args['after'] );

		$slide_item_price = sprintf( '<%1$s class="%2$s">%3$s</%1$s>', $args['wrapper'], $args['class'], ob_get_clean() );
		$slide_item_price = apply_filters( 'SLIDERXWOO/Filters/slide_item_price', $slide_item_price, $product, $args );

		if ( $args['echo'] ) {
			echo wp_kses_post( $slide_item_price );

			return '';
		}

		return $slide_item_price;
	}
}


if ( ! function_exists( 'sliderxwoo_get_slide_item_rating' ) ) {
	/**
	 * Get slider item rating HTML
	 *
	 * @param $product WC_Product
	 * @param array $args
	 *
	 * @retun string
	 */
	function sliderxwoo_get_slide_item_rating( $product, $args = array() ) {

		global $tinypress;

		if ( ! $tinypress->get_meta( '_item_rating', true ) ) {
			return '';
		}

		$args = wp_parse_args( $args, array(
			'slider_id' => $tinypress->id,
			'class'     => 'rating',
			'before'    => '',
			'after'     => '',
			'echo'      => true,
			'wrapper'   => 'div',
		) );

		ob_start();

		echo wp_kses_post( $args['before'] );

		printf( '<span style="width:%s"></span>', ( ( ( $product->get_average_rating() / 5 ) * 100 ) . '%' ) );

		echo wp_kses_post( $args['after'] );

		$slide_item_rating = sprintf( '<%1$s class="%2$s">%3$s</%1$s>', $args['wrapper'], $args['class'], ob_get_clean() );
		$slide_item_rating = apply_filters( 'SLIDERXWOO/Filters/slide_item_rating', $slide_item_rating, $product, $args );

		if ( $args['echo'] ) {
			echo wp_kses_post( $slide_item_rating );

			return '';
		}

		return $slide_item_rating;
	}
}


if ( ! function_exists( 'sliderxwoo_get_slide_item_cart_btn' ) ) {
	/**
	 * Get slider item cart button HTML
	 *
	 * @param $product WC_Product
	 * @param array $args
	 *
	 * @retun string
	 */
	function sliderxwoo_get_slide_item_cart_btn( $product, $args = array() ) {

		global $tinypress;

		if ( ! $tinypress->get_meta( '_item_cart_btn', true ) ) {
			return '';
		}

		$args = wp_parse_args( $args, array(
			'slider_id' => $tinypress->id,
			'class'     => 'add-to-cart',
			'before'    => '',
			'after'     => '',
			'echo'      => true,
			'wrapper'   => 'div',
		) );

		ob_start();

		echo wp_kses_post( $args['before'] );

		woocommerce_template_loop_add_to_cart();

		echo wp_kses_post( $args['after'] );

		$slide_item_cart_btn = sprintf( '<%1$s class="%2$s">%3$s</%1$s>', $args['wrapper'], $args['class'], ob_get_clean() );
		$slide_item_cart_btn = apply_filters( 'SLIDERXWOO/Filters/slide_item_cart_btn', $slide_item_cart_btn, $product, $args );

		if ( $args['echo'] ) {
			echo wp_kses_post( $slide_item_cart_btn );

			return '';
		}

		return $slide_item_cart_btn;
	}
}


