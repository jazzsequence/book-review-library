<?php
/**
 * Deals with public-facing stuff.
 *
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPL-3.0
 * @link      http://chrisreynolds.io
 * @copyright 2014 Chris Reynolds
 */

add_action( 'init', 'book_review_archive_check' );
add_action( 'init', 'book_review_single_check' );
add_action( 'init', 'book_review_taxonomy_check' );

/**
 * Post Type Archive check
 * if there's an archive template for the book-review post type, don't do this
 *
 * @since 	1.0.0
 */
function book_review_archive_check() {
	;

	$archive = locate_template( 'archive-book-review.php' );
	if ( empty( $archive ) ) {
		include_once( BOOK_REVIEWS_FUNC );
		$defaults = book_reviews_option_defaults();
		$options = get_option( 'book_reviews_settings', $defaults );
		// archive template for book reviews not found, so do this...
		if ( isset( $options['title-filter'] ) ) {
			switch ( $options['title-filter'] ) {
				case 'title' :
					add_filter( 'the_title', 'filter_book_review_title', 20 );
					break;

				case 'newline' :
					add_filter( 'the_title', 'filter_book_review_title_newline', 20 );
					break;

				case 'disabled' :
					break;
			}
		}

			add_filter( 'the_content', 'filter_book_review_single', 20 );
		add_filter( 'get_the_excerpt', 'filter_book_review_excerpt', 20 );
	}
}
/**
 * Post Type Single check
 * if there's an single.php template for the book-review post type, don't do this
 *
 * @since 	1.0.0
 */
function book_review_single_check() {
	;

	$single = locate_template( 'single-book-review.php' );
	if ( empty( $single ) ) {
		include_once( BOOK_REVIEWS_FUNC );
		$defaults = book_reviews_option_defaults();
		$options = get_option( 'book_reviews_settings', $defaults );
		// single template for book reviews not found, so do this...
		if ( isset( $options['title-filter'] ) ) {
			switch ( $options['title-filter'] ) {
				case 'title' :
					add_filter( 'the_title', 'filter_book_review_title', 20 );
					break;

				case 'newline' :
					add_filter( 'the_title', 'filter_book_review_title_newline', 20 );
					break;

				case 'disabled' :
					break;
			}
		}

			add_filter( 'the_content', 'filter_book_review_single', 20 );
	}
}

/**
 * Post Type Taxonomy check
 * if there's a taxonomy template at all, don't do this
 *
 * @since 	1.0.0
 */
function book_review_taxonomy_check() {
	;

	$taxonomy = locate_template( 'taxonomy.php' );
	if ( empty( $taxonomy ) ) {
		include_once( BOOK_REVIEWS_FUNC );
		$defaults = book_reviews_option_defaults();
		$options = get_option( 'book_reviews_settings', $defaults );
		// this actually makes it work better for tax archives if a taxonomy.php *doesn't* exist than if it does...maybe...
		if ( isset( $options['title-filter'] ) ) {
			switch ( $options['title-filter'] ) {
				case 'title' :
					add_filter( 'the_title', 'filter_book_review_title', 20 );
					break;

				case 'newline' :
					add_filter( 'the_title', 'filter_book_review_title_newline', 20 );
					break;

				case 'disabled' :
					break;
			}
		}

			add_filter( 'the_content', 'filter_book_review_single', 20 );
		add_filter( 'get_the_excerpt', 'filter_book_review_excerpt', 20 );
	}
}

/**
 * Single book review filter
 * filters the_content and adds the book review meta data and taxonomies
 *
 * @since 	1.0.0
 */
function filter_book_review_single( $content ) {
	global $post, $is_book_review_shortcode;

	$awards = null;
	$meta = null;
	$postmeta = null;
	include_once( BOOK_REVIEWS_FUNC );
	$options = get_option( 'book_reviews_settings', book_reviews_option_defaults() );

	// check for awards
	if ( $options['awards'] && has_term( '','awards' ) ) {
		$awards = '<div class="awards post-data alignleft">';
		if ( get_post_meta( $post->ID, 'award_image', true ) ) {
			$awards .= '<img src="' . get_post_meta( $post->ID, 'award_image', true ) . '" alt="' . wp_strip_all_tags( get_the_title() ) . '" class="aligncenter" /><br />';
		}
		$awards .= '<ul>';
		$awards .= get_awards( '<li>','</li>' );
		$awards .= '</ul>';
		$awards .= '</div>';
	}

	$meta = '<div class="post-meta">';
	if ( $options['rating'] && has_term( '','rating' ) ) {
		$rating = get_rating();
		if ( $rating == 'zero' ) {
			$rating = '0'; }
		$rating_arr = get_term_by( 'name', $rating, 'rating' );
		$star_slug = $rating_arr->slug;
		$rating_string = '<a href="' . home_url() . '/?rating=' . $star_slug . '/">' . get_rating_stars() . '</a>';
		$meta .= '<span class="rating">';
		$meta .= $rating_string;
		$meta .= '</span><br />';
	}
	if ( $options['review-author'] && has_term( '','review-author' ) && is_singular( 'book-review' ) ) {
		$rev_auth = get_term_by( 'name', get_review_author(), 'review-author' );
		$rev_auth_slug = $rev_auth->slug;
		$author_string = '<a href="' . home_url() . '/?review-author=' . $rev_auth_slug . '/">' . get_review_author() . '</a>';
		$meta .= '<span class="author">';
		$meta .= sprintf( __( 'Review by %s', 'book-review-library' ), $author_string );
		$meta .= '</span><br />';
	}
	if ( $options['reading-level'] && has_term( '', 'reading-level' ) ) {
		$meta .= '<span class="reading-level">';
		$meta .= sprintf( __( 'Reading Level: %s', 'book-review-library' ), get_reading_level() );
		$meta .= '<span><br />';
	}
	if ( ! empty( $options['stock'] ) ) {
		if ( get_post_meta( $post->ID, 'book_in_stock', true ) ) {
			$meta .= '<span class="in-stock">';
			$meta .= __( 'This book is <strong>in stock</strong>', 'book-review-library' );
			$meta .= '</span>';
		} else {
			$meta .= '<span class="out-of-stock">';
			$meta .= __( 'This book is <strong>currently checked out</strong>', 'book-review-library' );
			$meta .= '</span>';
		}
	}
	$meta .= '</div>';

	$postmeta = '<hr />';
	$postmeta .= '<div class="post-data">';
	if ( isset( $options['title-filter'] ) && ! $options['title-filter'] && has_term( '', 'book-author' ) ) {
		$postmeta .= '<span class="book-author">';
		$postmeta .= '<strong>' . __( 'Author:', 'book-review-library' ) . '</strong>&nbsp;';
		$postmeta .= get_book_author();
		$postmeta .= '</span><br />';
	}
	if ( has_term( '','genre' ) ) {
		$postmeta .= '<span class="genre">' . sprintf( __( '<strong>Genre:</strong> %s', 'book-review-library' ), get_genres() ) . '</span><br />';
	}
	if ( $options['series'] && has_term( '','series' ) ) {
		$postmeta .= '<span class="series">' . sprintf( __( '<strong>Series:</strong> %s | ', 'book-review-library' ), get_book_series() ) . '</span>';
	}
	if ( $options['subject'] && has_term( '','subject' ) ) {
		$postmeta .= '<span class="subjects">' . sprintf( __( '<strong>Subjects:</strong> %s', 'book-review-library' ), get_subjects() ) . '</span><br />';
	}
	if ( $options['illustrator'] && has_term( '','illustrator' ) ) {
		$postmeta .= '<span class="illustrator">' . sprintf( __( '<strong>Illustrated by</strong> %s', 'book-review-library' ), get_illustrator() ) . '</span>';
	}
	$postmeta .= '</div>';

	if ( ( 'book-review' == get_post_type() ) && in_the_loop() && ! $is_book_review_shortcode && ! is_search() ) : // only do this if we're in the loop
		return $awards . $content . $meta . $postmeta;
	else : // otherwise, don't do anything
		return $content;
	endif;
}

/**
 * Excerpt book review filter
 * filters the_excerpt and adds the book review meta data and taxonomies
 *
 * @since 	1.0.0
 */
function filter_book_review_excerpt( $content ) {
	global $post, $is_book_review_shortcode;

	$meta = null;
	$postmeta = null;
	include_once( BOOK_REVIEWS_FUNC );
	$options = get_option( 'book_reviews_settings', book_reviews_option_defaults() );

	$meta = '<div class="post-meta">';
	if ( $options['rating'] && has_term( '','rating' ) ) {
		$rating = get_rating();
		if ( $rating == 'zero' ) {
			$rating = '0'; }
		$rating_arr = get_term_by( 'name', $rating, 'rating' );
		$star_slug = $rating_arr->slug;
		$rating_string = '<a href="' . home_url() . '/?rating=' . $star_slug . '/">' . get_rating_stars() . '</a>';
		$meta .= '<span class="rating">';
		$meta .= $rating_string;
		$meta .= '</span><br />';
	}
	if ( $options['review-author'] && has_term( '','review-author' ) && is_singular( 'book-review' ) ) {
		$rev_auth = get_term_by( 'name', get_review_author(), 'review-author' );
		$rev_auth_slug = $rev_auth->slug;
		$author_string = '<a href="' . home_url() . '/?review-author=' . $rev_auth_slug . '/">' . get_review_author() . '</a>';
		$meta .= '<span class="author">';
		$meta .= sprintf( __( 'Review by %s', 'book-review-library' ), $author_string );
		$meta .= '</span><br />';
	}
	if ( $options['reading-level'] && has_term( '', 'reading-level' ) ) {
		$meta .= '<span class="reading-level">';
		$meta .= sprintf( __( 'Reading Level: %s', 'book-review-library' ), get_reading_level() );
		$meta .= '<span><br />';
	}
	if ( ! empty( $options['stock'] ) ) {
		if ( get_post_meta( $post->ID, 'book_in_stock', true ) ) {
			$meta .= '<span class="in-stock">';
			$meta .= __( 'This book is <strong>in stock</strong>', 'book-review-library' );
			$meta .= '</span>';
		} else {
			$meta .= '<span class="out-of-stock">';
			$meta .= __( 'This book is <strong>currently checked out</strong>', 'book-review-library' );
			$meta .= '</span>';
		}
	}
	$meta .= '</div>';

	$postmeta = '<hr />';
	$postmeta .= '<div class="post-data">';
	if ( isset( $options['title-filter'] ) && ! $options['title-filter'] && has_term( '', 'book-author' ) ) {
		$postmeta .= '<span class="book-author">';
		$postmeta .= '<strong>' . __( 'Author:', 'book-review-library' ) . '</strong>&nbsp;';
		$postmeta .= get_book_author();
		$postmeta .= '</span><br />';
	}
	if ( has_term( '','genre' ) ) {
		$postmeta .= '<span class="genre">' . sprintf( __( '<strong>Genre:</strong> %s', 'book-review-library' ), get_genres() ) . '</span><br />';
	}
	if ( $options['series'] && has_term( '','series' ) ) {
		$postmeta .= '<span class="series">' . sprintf( __( '<strong>Series:</strong> %s | ', 'book-review-library' ), get_book_series() ) . '</span>';
	}
	if ( $options['subject'] && has_term( '','subject' ) ) {
		$postmeta .= '<span class="subjects">' . sprintf( __( '<strong>Subjects:</strong> %s', 'book-review-library' ), get_subjects() ) . '</span><br />';
	}
	if ( $options['illustrator'] && has_term( '','illustrator' ) ) {
		$postmeta .= '<span class="illustrator">' . sprintf( __( '<strong>Illustrated by</strong> %s', 'book-review-library' ), get_illustrator() ) . '</span>';
	}
	$postmeta .= '</div>';

	if ( ( 'book-review' == get_post_type() ) && in_the_loop() && ! $is_book_review_shortcode && ! is_search() ) : // only do this if we're in the loop
		return $content . $meta . $postmeta;
	else : // otherwise, don't do anything
		return $content;
	endif;
}

/**
 * Book review title filter
 * filters the_title and adds the book author
 *
 * @since 	1.0.0
 */
function filter_book_review_title( $title ) {
	global $post;

	if ( has_term( '','book-author' ) && 'book-review' == get_post_type() && in_the_loop() ) {
		$new_title = sprintf( __( '%1$s by %2$s', 'book-review-library' ), $title . '</a>', get_book_author() );
		return $new_title;
	} else {
		return $title;
	}
}

/**
 * Book review newline title filter
 * filters the_title and adds the book author on a new line
 *
 * @since 	1.4.7
 */
function filter_book_review_title_newline( $title ) {
	global $post;

	if ( has_term( '','book-author' ) && 'book-review' == get_post_type() && in_the_loop() ) {
		$new_title = sprintf( __( '%1$s by %2$s', 'book-review-library' ), $title . '</a><br /><div class="book-author">', get_book_author() . '</div>' );
		return $new_title;
	} else {
		return $title;
	}
}

/**
 * Alter the previous_post output so the correct book author can be displayed.
 * @param  string $return The original output.
 * @since  1.4.14
 * @return string         The new output.
 */
function filter_book_review_title_previous_post( $return ) {
	$previous_post = get_previous_post();
	$options       = get_option( 'book_reviews_settings', book_reviews_option_defaults() );
	$author        = get_book_author( null, ', ', false, $previous_post );

	// Check the post type. Only change the output if we're looking at a book review.
	if ( $previous_post && 'book-review' == $previous_post->post_type ) {
		if ( $return && $previous_post->ID !== get_queried_object_id() ) {

			$output = '<a href="' . get_the_permalink( $previous_post->ID ) . '" rel="previous"><span class="meta-nav">' . __( 'Previous Review', 'book-review-library' ) . '</span>' . esc_attr( $previous_post->post_title ) . '</a>';

			if ( isset( $options['title-filter'] ) && 'disabled' !== $options['title-filter'] ) {
				$output = sprintf( __( '%1$s by %2$s', 'book-review-library' ), $output, $author );
			}

			return $output;

		}
	}

	// Only return an output if the previous post isn't the current post.
	return ( $return && $previous_post->ID !== get_queried_object_id() ) ? $return : false;
}
add_filter( 'previous_post_link', 'filter_book_review_title_previous_post', 1 );

/**
 * Alter the previous_post output so the correct book author can be displayed.
 * @param  string $return The original output.
 * @since  1.4.14
 * @return string         The new output.
 */
function filter_book_review_title_next_post( $return ) {
	$next_post = get_next_post();
	$options   = get_option( 'book_reviews_settings', book_reviews_option_defaults() );
	$author    = get_book_author( null, ', ', false, $next_post );

	// Check the post type. Only change the output if we're looking at a book review.
	if ( $next_post && 'book-review' == $next_post->post_type ) {
		if ( $return && $next_post->ID !== get_queried_object_id() ) {

			$output = '<a href="' . get_the_permalink( $next_post->ID ) . '" rel="next"><span class="meta-nav">' . __( 'Next Review', 'book-review-library' ) . '</span>' . esc_attr( $next_post->post_title ) . '</a>';

			if ( isset( $options['title-filter'] ) && 'disabled' !== $options['title-filter'] ) {
				$output = sprintf( __( '%1$s by %2$s', 'book-review-library' ), $output, $author );
			}

			return $output;

		}
	}

	// Only return an output if the next post isn't the current post.
	return ( $return && $next_post->ID !== get_queried_object_id() ) ? $return : false;
}
add_filter( 'next_post_link', 'filter_book_review_title_next_post', 1 );
