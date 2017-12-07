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
use BookReview\CMB2 as CMB2;
use BookReview\Taxonomies as Taxonomies;

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

/**
 * Add the Additional Information metabox.
 *
 * @since 2.0.0-20171207
 */
function add_book_review_meta() {
	CMB2\add_cmb2_box([
		'metabox_id' => 'book-reviews-meta',
		'title'      => esc_html__( 'Additional Information', 'book-review-library' ),
		'priority'   => 'low',
		'fields'     => [
			'isbn' => [
				'name' => esc_html__( 'ISBN:', 'book-review-library' ),
				'id'   => 'isbn',
				'type' => 'text_medium',
			],
			'book_in_stock' => [
				'name'       => esc_html__( 'In Stock?', 'book-review-library' ),
				'id'         => 'book_in_stock',
				'type'       => 'select',
				'default'    => 1,
				'options'    => [
					0 => esc_html__( 'Book is out of stock', 'book-review-library' ),
					1 => esc_html__( 'Book is in stock', 'book-review-library' ),
				],
				'show_on_cb' => Options\is_option_enabled( 'stock' ),
			],
		],
	]);
}

/**
 * Add the Author Details metabox
 *
 * @since 2.0.0-20171207
 */
function add_author_info() {
	CMB2\add_cmb2_box([
		'metabox_id' => 'author-information',
		'title'      => esc_html__( 'Author Details', 'book-review-library' ),
		'fields'     => [
			'author' => Taxonomies\taxonomies( 'book-author' ),
			'author-image' => [
				'name'       => esc_html__( 'Author Image', 'book-review-library' ),
				'id'         => 'author-image',
				'desc'       => esc_html__( 'Upload or select an image for this book\'s author or enter a URL to an image. No image will display if none is uploaded.', 'book-review-library' ),
				'type'       => 'file',
				'show_on_cb' => Options\is_option_enabled( 'author-image' ),
			],
		],
	]);
}
