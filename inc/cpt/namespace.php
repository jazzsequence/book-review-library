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

/**
 * Initialize the post type.
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\register_book_reviews' );
}

function register_book_reviews() {
	$defaults = \book_reviews_option_defaults();
	$supports = [
		'title',
		'editor',
		'author',
		'thumbnail',
		'revisions',
		'comments',
	];

	// Remove comment support if we aren't enabling comments.
	if ( ! book_reviews_is_option_enabled( 'comments' ) ) {
		unset( $supports['commetns'] );
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
	] );
}