<?php
/**
 * Some helper functions used by the plugin
 *
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPL-3.0
 * @link      http://chrisreynolds.io
 * @copyright 2015 Chris Reynolds
 */

namespace BookReview;

/**
 * Get Review Author
 * returns a formatted list of review authors (comma-separated by default)
 *
 * @since 	1.0.0
 *
 * @param 	$before 	string 		string to display before the author name
 * @param 	$after 		string		string to display after the author name (comma by default)
 * @param 	$forced 	boolean 	by default, if the item is the last in the list, the $after variable doesn't render. If $forced is set to TRUE it 									 will bypass this and render it anyway (e.g. if passing $before = '<li>' / $after = '</li>')
 * @return 	$review_author_list		sanitized string of the results
 */
function get_review_author($before = null, $after = ', ', $forced = false) {
	global $post;
	$review_authors = get_the_terms( $post->ID, 'review-author' );
	$review_author_list = null;
	if ( $review_authors && !is_wp_error( $review_authors ) ) {
		$review_author_out = array();
		foreach ( $review_authors as $review_author ) {
			$review_author_out[] = $review_author->name;
		}
		$count = 0;
		foreach ( $review_author_out as $out ) {
			$review_author_list .= $before . $out;
			$count++;
			if ( ( count($review_author_out) > 1 ) && ( $after == ', ' ) && ( count($review_author_out) != $count ) || $forced ) {
				$review_author_list .= $after;
			}
		}
	}
	if ( $review_author_list )
		return wp_kses_post($review_author_list);
}

/**
 * Get genres
 * returns a formatted list of genres (comma-separated by default) with links to each
 *
 * @since 	1.0.0
 *
 * @param 	$before 	string 		string to display before the genre
 * @param 	$after 		string		string to display after the genre (comma by default)
 * @param 	$forced 	boolean 	by default, if the item is the last in the list, the $after variable doesn't render. If $forced is set to TRUE it 									 will bypass this and render it anyway (e.g. if passing $before = '<li>' / $after = '</li>')
 * @return 	$genre_list				sanitized string of the results
 */
function get_genres($before = null, $after = ', ', $forced = false) {
	global $post;
	$genres = get_the_terms( $post->ID, 'genre' );
	$genre_list = null;
	if ( $genres && !is_wp_error( $genres ) ) {
		$genre_out = array();
		foreach ( $genres as $genre ) {
			$genre_out[] = sprintf( '<a href="%s">%s</a>',
				home_url() . '/?genre=' . $genre->slug,
				$genre->name);
		}
		$count = 0;
		foreach ( $genre_out as $out ) {
			$genre_list .= $before . $out;
			$count++;
			if ( ( count($genre_out) > 1 ) && ( $after == ', ' ) && ( count($genre_out) != $count ) || $forced ) {
				$genre_list .= $after;
			}
		}
	}
	if ( $genre_list )
		return wp_kses_post($genre_list);
}

/**
 * Get Book Author
 * returns a formatted list of book authors (comma-separated by default) with links to each
 *
 * @since 	1.0.0
 *
 * @param 	$before 	string 		string to display before the author name
 * @param 	$after 		string		string to display after the author name (comma by default)
 * @param 	$forced 	boolean 	by default, if the item is the last in the list, the $after variable doesn't render. If $forced is set to TRUE it 									 will bypass this and render it anyway (e.g. if passing $before = '<li>' / $after = '</li>')
 * @param 	$post_obj   object      An optional post object to pass instead of the current post
 * @return 	$book_author_list		sanitized string of the results
 */
function get_book_author($before = null, $after = ', ', $forced = false, $post_obj = null) {
	global $post;
	$post = ( $post_obj ) ? $post_obj : $post;
	$book_authors = get_the_terms( $post->ID, 'book-author' );
	$book_author_list = null;
	if ( $book_authors && !is_wp_error( $book_authors ) ) {
		$book_author_out = array();
		foreach ( $book_authors as $book_author ) {
			$book_author_out[] = sprintf( '<a href="%s">%s</a>',
				home_url() . '/?book-author=' . $book_author->slug,
				$book_author->name);
		}
		$count = 0;
		foreach ( $book_author_out as $out ) {
			$book_author_list .= $before . $out;
			$count++;
			if ( ( count($book_author_out) > 1 ) && ( $after == ', ' ) && ( count($book_author_out) != $count ) || $forced ) {
				$book_author_list .= $after;
			}
		}
		if ( $book_author_list )
			return wp_kses_post($book_author_list);
	}
}

/**
 * Get reading level
 * returns a formatted list of reading levels (comma-separated by default) with links to each
 *
 * @since 	1.0.0
 *
 * @param 	$before 	string 		string to display before the reading level
 * @param 	$after 		string		string to display after the reading level (comma by default)
 * @param 	$forced 	boolean 	by default, if the item is the last in the list, the $after variable doesn't render. If $forced is set to TRUE it 									 will bypass this and render it anyway (e.g. if passing $before = '<li>' / $after = '</li>')
 * @return 	$reading_level_list		sanitized string of the results
 */
function get_reading_level($before = null, $after = ', ', $forced = false) {
	global $post;
	$reading_levels = get_the_terms( $post->ID, 'reading-level' );
	$reading_level_list = null;
	if ( $reading_levels && !is_wp_error( $reading_levels ) ) {
		$reading_level_out = array();
		foreach ( $reading_levels as $reading_level ) {
			$reading_level_out[] = sprintf( '<a href="%s">%s</a>',
				home_url() . '/?reading-level=' . $reading_level->slug,
				$reading_level->name);
		}
		$count = 0;
		foreach ( $reading_level_out as $out ) {
			$reading_level_list .= $before . $out;
			$count++;
			if ( ( count($reading_level_out) > 1 ) && ( $after == ', ' ) && ( count($reading_level_out) != $count ) || $forced ) {
				$reading_level_list .= $after;
			}
		}
	}
	if ( $reading_level_list )
		return wp_kses_post($reading_level_list);
}

/**
 * Get subjects
 * returns a formatted list of subjects (comma-separated by default) with links to each
 *
 * @since 	1.0.0
 *
 * @param 	$before 	string 		string to display before the subject
 * @param 	$after 		string		string to display after the subject (comma by default)
 * @param 	$forced 	boolean 	by default, if the item is the last in the list, the $after variable doesn't render. If $forced is set to TRUE it 									 will bypass this and render it anyway (e.g. if passing $before = '<li>' / $after = '</li>')
 * @return 	$subject_list			sanitized string of the results
 */
function get_subjects($before = null, $after = ', ', $forced = false) {
	global $post;
	$subjects = get_the_terms( $post->ID, 'subject' );
	$subject_list = null;
	if ( $subjects && !is_wp_error( $subjects ) ) {
		$subject_out = array();
		foreach ( $subjects as $subject ) {
			$subject_out[] = sprintf( '<a href="%s">%s</a>',
				home_url() . '/?subject=' . $subject->slug,
				$subject->name);
		}
		$count = 0;
		foreach ( $subject_out as $out ) {
			$subject_list .= $before . $out;
			$count++;
			if ( ( count($subject_out) > 1 ) && ( $after == ', ' ) && ( count($subject_out) != $count ) || $forced ) {
				$subject_list .= $after;
			}
		}
	}
	if ( $subject_list )
		return wp_kses_post($subject_list);
}

/**
 * Get rating stars
 * returns a genericon star icon for each star value in the rating taxonomy
 *
 * @since 	1.0.0
 *
 * @return 	$rating_out				sanitized string of the results
 */
function get_rating_stars() {
	global $post;
	$ratings = get_the_terms( $post->ID, 'rating' );
	if ( $ratings && !is_wp_error( $ratings ) ) {
		$rating_out = null;
		foreach ( $ratings as $rating ) {
			if ( $rating->name == '5' ) {
				$stars = $rating->name;
				$rating_out = '<div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div>';
			}
			elseif ( $rating->name == '4' ) {
				$stars = $rating->name;
				$rating_out = '<div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div>';
			}
			elseif ( $rating->name == '3' ) {
				$stars = $rating->name;
				$rating_out = '<div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div>';
			}
			elseif ( $rating->name == '2' ) {
				$stars = $rating->name;
				$rating_out = '<div class="genericon genericon-star"></div><div class="genericon genericon-star"></div>';
			}
			elseif ( $rating->name == '1' ) {
				$stars = $rating->name;
				$rating_out = '<div class="genericon genericon-star"></div>';
			}
			elseif ( $rating->name == '0' ) {
				$stars = $rating->name;
				$rating_out = __( 'Zero stars', 'book-review-library' );
			}
		}

	}
	if ( $rating_out )
		return wp_kses_post($rating_out);
}

/**
 * Get rating
 * returns a comma-separated list of ratings (if more than one rating is applied to a review, for some reason)
 *
 * @since 	1.0.0
 *
 * @return 	$stars				sanitized string of the results
 */
function get_rating($before = null, $after = ', ', $forced = false) {
	global $post;
	$ratings = get_the_terms( $post->ID, 'rating' );
	if ( $ratings && !is_wp_error( $ratings ) ) {
		$stars_out = array();
		foreach ( $ratings as $rating ) {
			if ( $rating->name == '0' ) {
				// if rating is 0, get_rating() will return false
				// returning "zero" means we can do a check on the result, and deal with the result on the display side
				$stars_out[] = "zero";
			} else {
				$stars_out[] = $rating->name;
			}
		}
		$stars = join( ', ', $stars_out); // join the stars list if for some reason someone has used more than one
	}
	if ( $stars )
		return esc_attr($stars);
}

/**
 * Get illustrator
 * returns a formatted list of illustrators (comma-separated by default) with links to each
 *
 * @since 	1.0.0
 *
 * @param 	$before 	string 		string to display before the illustrator name
 * @param 	$after 		string		string to display after the illustrator name (comma by default)
 * @param 	$forced 	boolean 	by default, if the item is the last in the list, the $after variable doesn't render. If $forced is set to TRUE it 									 will bypass this and render it anyway (e.g. if passing $before = '<li>' / $after = '</li>')
 * @return 	$illustrator_list		sanitized string of the results
 */
function get_illustrator($before = null, $after = ', ', $forced = false) {
	global $post;

	$illustrators = get_the_terms( $post->ID, 'illustrator' );
	$illustrator_list = null;
	if ( $illustrators && !is_wp_error( $illustrators ) ) {
		$illustrator_out = array();
		foreach ( $illustrators as $illustrator ) {
			$illustrator_out[] = sprintf( '<a href="%s">%s</a>',
				home_url() . '/?illustrator=' . $illustrator->slug,
				$illustrator->name);
		}
		$count = 0;
		foreach ( $illustrator_out as $out ) {
			$illustrator_list .= $before . $out;
			$count++;
			if ( ( count($illustrator_out) > 1 ) && ( $after == ', ' ) && ( count($illustrator_out) != $count ) || $forced ) {
				$illustrator_list .= $after;
			}
		}
	}
	if ( $illustrator_list )
		return wp_kses_post($illustrator_list);
}

/**
 * Get awards
 * returns a formatted list of awards (comma-separated by default) with links to each
 *
 * @since 	1.0.0
 *
 * @param 	$before 	string 		string to display before the award name
 * @param 	$after 		string		string to display after the award name (comma by default)
 * @param 	$forced 	boolean 	by default, if the item is the last in the list, the $after variable doesn't render. If $forced is set to TRUE it 									 will bypass this and render it anyway (e.g. if passing $before = '<li>' / $after = '</li>')
 * @return 	$award_list				sanitized string of the results
 */
function get_awards($before = null, $after = ', ', $forced = false) {
	global $post;
	$awards = get_the_terms( $post->ID, 'awards' );
	$award_list = null;
	if ( $awards && !is_wp_error( $awards ) ) {
		$award_out = array();
		foreach ( $awards as $award ) {
			$award_out[] = sprintf( '<a href="%s">%s</a>',
				home_url() . '/?awards=' . $award->slug,
				$award->name);
		}
		$count = 0;
		foreach ( $award_out as $out ) {
			$award_list .= $before . $out;
			$count++;
			if ( ( count($award_out) > 1 ) && ( $after == ', ' ) && ( count($award_out) != $count ) || $forced ) {
				$award_list .= $after;
			}
		}
	}
	if ( $award_list )
		return wp_kses_post($award_list);
}

/**
 * Get series
 * returns a formatted list of series (comma-separated by default) with links to each
 *
 * @since 	1.0.0
 *
 * @param 	$before 	string 		string to display before the series name
 * @param 	$after 		string		string to display after the series name (comma by default)
 * @param 	$forced 	boolean 	by default, if the item is the last in the list, the $after variable doesn't render. If $forced is set to TRUE it 									 will bypass this and render it anyway (e.g. if passing $before = '<li>' / $after = '</li>')
 * @return 	$series_list				sanitized string of the results
 */
function get_book_series($before = null, $after = ', ', $forced = false) {
	global $post;
	$seriess = get_the_terms( $post->ID, 'series' );
	$series_list = null;
	if ( $seriess && !is_wp_error( $seriess ) ) {
		$illustrator_out = array();
		foreach ( $seriess as $series ) {
			$series_out[] = sprintf( '<a href="%s">%s</a>',
				home_url() . '/?series=' . $series->slug,
				$series->name);
		}
		$count = 0;
		foreach ( $series_out as $out ) {
			$series_list .= $before . $out;
			$count++;
			if ( ( count($series_out) > 1 ) && ( $after == ', ' ) && ( count($series_out) != $count ) || $forced ) {
				$series_list .= $after;
			}
		}
	}
	if ( $series_list )
		return wp_kses_post($series_list);
}

/**
 * Enabled/Disabled toggle
 * This function is deprecated in 2.0.
 *
 * @since 	1.0.0
 */
function book_reviews_true_false() {
	return _deprecated_function( 'book_reviews_true_false', esc_html__( 'Book Review Library 2.0+', 'book-review-library' ), 'Options\\true_false' );
}

/**
 * Book Review Title Filter
 * This function is deprecated in 2.0.
 *
 * @since 1.4.7
 */
function book_reviews_title_filter() {
	return _deprecated_function( 'book_reviews_title_filter', esc_html__( 'Book Review Library 2.0+', 'book-review-library' ), 'Options\\book_covers' );
}

/**
 * Book Review Thumbnail Option
 * This function is deprecated in 2.0.
 *
 * @since 1.4.11
 */
function book_reviews_image_size() {
	return _deprecated_function( 'book_reviews_image_size', esc_html__( 'Book Review Library 2.0+', 'book-review-library' ), 'Options\\book_covers' );
}

/**
 * Check if a particular option is enabled, wrapper for Book_Reviews::is_option_enabled()
 *
 * @since 1.5.0
 * @param 	string 	The option name to check
 * @return 	bool 	True of the setting is enabled, false if it isn't or no option was
 * 					passed
 */
function book_reviews_is_option_enabled( $option_name = '' ) {
	$book_reviews = Book_Reviews::get_instance();

	return $book_reviews->is_option_enabled( $option_name );
}

/**
 * Wrapper function for cmb2_get_option to get specified Book Review
 * setting
 *
 * @since  1.5.0
 * @link   https://github.com/WebDevStudios/CMB2/wiki/Using-CMB-to-create-an-Admin-Theme-Options-Page
 * @param  string $key 	Options array key
 * @return mixed 		Option value
 */
function book_review_get_option( $key = '' ) {
	$Book_Reviews_Options = new Book_Reviews_Options;
	return cmb2_get_option( $Book_Reviews_Options->key, $key );
}

/**
 * Default option settings
 *
 * @since 	1.0.0
 *
 * @return 	$defaults 	all the default settings (everything disabled)
 */
function book_reviews_option_defaults() {
	$Book_Reviews_Options = new Book_Reviews_Options;
	return $Book_Reviews_Options->defaults();
}