<?php

class Book_Review_Library_CPT {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	private function __construct() {
		// Register post type
		add_action( 'init', [ $this, 'register_post_type_book_review' ] );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register the book review post type
	 *
	 * @since   1.0.0
	 */
	public function register_post_type_book_review() {
		include_once BOOK_REVIEWS_TEMPLATE_TAGS;
		$defaults = book_reviews_option_defaults();
		$options = get_option( 'book_reviews_settings', $defaults );
		if ( book_reviews_is_option_enabled( 'comments' ) ) {
			$supports = [ 'title', 'editor', 'author', 'thumbnail', 'revisions', 'comments' ];
		} else {
			$supports = [ 'title', 'editor', 'author', 'thumbnail', 'revisions' ];
		}

		$capabilities = [
			'publish_posts' => 'publish_book-reviews',
			'edit_posts' => 'edit_book-reviews',
			'edit_others_posts' => 'edit_others_book-reviews',
			'delete_posts' => 'delete_book-reviews',
			'edit_post' => 'edit_book-review',
			'delete_post' => 'delete_book-review',
			'read_post' => 'read_book-review',
		];
		$labels = [
			'name' => __( 'Book Reviews', 'book-review-library' ),
			'singular_name' => __( 'Book Review', 'book-review-library' ),
			'add_new' => __( 'Add New', 'book-review-library' ),
			'add_new_item' => __( 'Add New Book Review', 'book-review-library' ),
			'edit_item' => __( 'Edit Review', 'book-review-library' ),
			'new_item' => __( 'New Book Review', 'book-review-library' ),
			'view_item' => __( 'View Book Review', 'book-review-library' ),
			'search_items' => __( 'Search Book Reviews', 'book-review-library' ),
			'not_found' => __( 'No book reviews found', 'book-review-library' ),
			'not_found_in_trash' => __( 'No book reviews found in Trash', 'book-review-library' ),
			'menu_name' => __( 'Book Reviews', 'book-review-library' ),
		];
		$args = [
			'labels' => $labels,
			'hierarchical' => false,
			'description' => 'Book Review',
			'supports' => $supports,
			'show_in_rest' => true,
			'taxonomies' => [ 'genre', 'review-author' ],
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 20,
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'book-review',
			'capabilities' => $capabilities,
			'map_meta_cap' => true,
		];
		register_post_type( 'book-review', $args );
	}
}

Book_Review_Library_CPT::get_instance();
