<?php
/**
 * Book Review Post Type
 *
 * Registers and handles the Book Review post type.
 *
 * @since 2.0.0-alpha
 *
 * @package BookReview
 */

namespace BookReview\CPT;
use BookReview\Options as Options;

/**
 * Register the CPT
 *
 * @since 2.0.0-alpha
 */
function register_book_reviews() {
	$defaults = Options\defaults();
	$supports = [
		'title',
		'editor',
		'author',
		'thumbnail',
		'revisions',
		'comments',
	];

	// Remove comment support if we aren't enabling comments.
	if ( ! Options\is_option_enabled( 'comments' ) ) {
		unset( $supports['comments'] );
	}

	$capabilities = [
		'publish_posts'     => 'publish_book-reviews',
		'edit_posts'        => 'edit_book-reviews',
		'edit_others_posts' => 'edit_others_book-reviews',
		'delete_posts'      => 'delete_book-reviews',
		'edit_post'         => 'edit_book-review',
		'delete_post'       => 'delete_book-review',
		'read_post'         => 'read_book-review',
	];

	register_extended_post_type( 'book-review', [
		'show_in_feed'    => false, // This should be an option.
		'admin_cols'      => [],
		'admin_filters'   => [],
		'menu_position'   => 20,
		'capability_type' => 'book-review',
		'capabilities'    => $capabilities,
		'map_meta_cap'    => true,
		'menu_icon'       => 'dashicons-book-alt',
	] );
}

/**
 * Rename the featured image metabox
 *
 * @since 2.0.0-alpha
 */
function rename_featured_image() {
	remove_meta_box( 'postimagediv', 'book-review', 'side' );
	add_meta_box( 'postimagediv', esc_html__( 'Book Cover', 'book-review-library' ), 'post_thumbnail_meta_box', 'book-review', 'side', 'default' );
}
