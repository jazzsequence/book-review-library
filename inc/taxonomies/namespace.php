<?php
/**
 * Taxonomies
 *
 * All the Book Review Library taxonomy stuff.
 *
 * @since 2.0.0-alpha
 *
 * @package BookReview
 */

namespace BookReview\Taxonomies;

/**
 * Delete ratings
 *
 * @since 2.0.0-alpha
 */
function delete_ratings() {
	wp_delete_term( '0', 'rating' );
	wp_delete_term( '1', 'rating' );
	wp_delete_term( '2', 'rating' );
	wp_delete_term( '3', 'rating' );
	wp_delete_term( '4', 'rating' );
	wp_delete_term( '5', 'rating' );
}
