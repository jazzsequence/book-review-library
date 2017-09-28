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
 * Initialize the taxonomies.
 *
 * @since 2.0.0-alpha
 */
function bootstrap() {
	// Review Authors.
	if ( book_reviews_is_option_enabled( 'review-author' ) ) {
		add_action( 'init', array( $this, 'register_taxonomy_review_author' ) );
	}

	// Reading Level.
	if ( book_reviews_is_option_enabled( 'reading-level' ) ) {
		add_action( 'init', array( $this, 'register_taxonomy_reading_level' ) );
	}

	// Subject.
	if ( book_reviews_is_option_enabled( 'subject' ) ) {
		add_action( 'init', array( $this, 'register_taxonomy_subject' ) );
	}

	// Illustrator.
	if ( book_reviews_is_option_enabled( 'illustrator' ) ) {
		add_action( 'init', array( $this, 'register_taxonomy_illustrator' ) );
	}

	// Awards.
	if ( book_reviews_is_option_enabled( 'awards' ) ) {
		add_action( 'init', array( $this, 'register_taxonomy_awards' ) );
	}

	// Series.
	if ( book_reviews_is_option_enabled( 'series' ) ) {
		add_action( 'init', array( $this, 'register_taxonomy_series' ) );
	}

	// Star Ratings.
	if ( book_reviews_is_option_enabled( 'rating' ) ) {
		add_action( 'init', array( $this, 'register_taxonomy_rating' ) );
		add_action( 'init', array( $this, 'insert_star_ratings' ) );
		add_action( 'admin_init', array( $this, 'remove_rating_submenu' ) );
	}

	// Genres (on always).
	add_action( 'init', array( $this, 'register_taxonomy_genre' ) );

	// Book Authors (on always).
	add_action( 'init', array( $this, 'register_taxonomy_book_author' ) );

	// Languages.
	if ( book_reviews_is_option_enabled( 'languages' ) ) {
		add_action( 'init', array( $this, 'register_taxonomy_language' ) );
	}

	// Format.
	if ( book_reviews_is_option_enabled( 'format' ) ) {
		add_action( 'init', array( $this, 'register_taxonomy_format' ) );
		add_action( 'init', array( $this, 'insert_formats' ) );
	}

	// Publisher.
	if ( book_reviews_is_option_enabled( 'publisher' ) ) {
		add_action( 'init', array( $this, 'register_taxonomy_publisher' ) );
	}
}

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
