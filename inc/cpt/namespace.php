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
			'review-author' => Taxonomies\taxonomies( 'review-author' ),
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

/**
 * Add Awards metabox
 *
 * @since 2.0.0-20171207
 */
function add_awards() {
	// Bail if awards are disabled.
	if ( ! Options\is_option_enabled( 'awards' ) ) {
		return;
	}

	CMB2\add_cmb2_box([
		'metabox_id' => 'book-awards',
		'title'      => esc_html__( 'Awards', 'book-review-library' ),
		'context'    => 'normal',
		'priority'   => 'low',
		'fields'     => [
			'awards'       => Taxonomies\taxonomies( 'awards' ),
			'award-images' => [
				'id'          => 'award-images',
				'name'        => esc_html__( 'Award Images', 'book-review-library' ),
				'type'        => 'file_list',
				'desc'        => wp_kses_post( __( 'File name or image title must match the award name.', 'book-review-library' ) ),
			],
		],
	]);
}

/**
 * Filter for the featured image post box
 *
 * @since 	1.0.0
 */
function change_thumbnail_html() {
	if ( 'book-review' === \get_post_type() ) {
		add_filter( 'admin_post_thumbnail_html', __NAMESPACE__ . '\\rename_post_thumbnail' );
	}
}

/**
 * Replaces "Set featured image" with "Select Book Cover"
 *
 * @since 	1.0.0
 * @param   string $content The html content of the featured image metabox.
 * @return 	string 	        The modified text.
 */
function rename_post_thumbnail( $content ) {
	return str_replace( __( 'Set featured image' ), esc_html__( 'Select Book Cover', 'book-review-library' ), $content );
}

/**
 * Adds a filter on the search to allow searching by ISBN
 *
 * @since 1.4
 * @param string $where The where MySQL query for the search.
 * @link http://wordpress.org/support/topic/include-custom-field-values-in-search?replies=16#post-1932930
 * @link http://www.devblog.fr/en/2013/09/05/modifying-wordpress-search-query-to-include-taxonomy-and-meta/
 */
function search_by_isbn( $where ) {
	// Load the meta keys into an array.
	$keys = [ 'isbn' ]; // Currently we're just using one, but we can expand this later.
	if ( is_search() && ! is_admin() ) {
		global $wpdb;
		$query = get_search_query();
		$query = esc_like( $query );

		// Include postmeta in search.
		foreach ( $keys as $key ) {
			$where .= " OR {$wpdb->posts}.ID IN (SELECT {$wpdb->postmeta}.post_id FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->postmeta}.meta_key = '$key' AND {$wpdb->postmeta}.meta_value LIKE '%$query%' AND {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)";
		}
		// Include taxonomy in search.
		$where .= " OR {$wpdb->posts}.ID IN (SELECT {$wpdb->posts}.ID FROM {$wpdb->posts},{$wpdb->term_relationships},{$wpdb->terms} WHERE {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id AND {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->terms}.term_id AND {$wpdb->terms}.name LIKE '%$query%')";

	}
	return $where;
}
