<?php

class Book_Review_Library_Taxonomies {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	private function __construct() {

		$book_reviews = Book_Reviews::get_instance();

		// Review Authors
		if ( $book_reviews->are_review_authors_enabled() )
			add_action( 'init', array( $this, 'register_taxonomy_review_author' ) );

		// Reading Level
		if ( $book_reviews->is_reading_level_enabled() )
			add_action( 'init', array( $this, 'register_taxonomy_reading_level' ) );

		// Subject
		if ( $book_reviews->is_subject_enabled() )
			add_action( 'init', array( $this, 'register_taxonomy_subject' ) );

		// Illustrator
		if ( $book_reviews->is_illustrator_enabled() )
			add_action( 'init', array( $this, 'register_taxonomy_illustrator' ) );

		// Awards
		if ( $book_reviews->are_awards_enabled() )
			add_action( 'init', array( $this, 'register_taxonomy_awards' ) );


		if ( $book_reviews->are_series_enabled() )
			add_action( 'init', array( $this, 'register_taxonomy_series' ) );

		// Star Ratings
		if ( $book_reviews->are_ratings_enabled() ) {
			add_action( 'init', array( $this, 'register_taxonomy_rating' ) );
			add_action( 'init', array( $this, 'insert_star_ratings' ) );
			add_action( 'admin_init', array( $this, 'remove_rating_submenu' ) );
		}

		// Genres (on always)
		add_action( 'init', array( $this, 'register_taxonomy_genre' ) );

		// Book Authors (on always)
		add_action( 'init', array( $this, 'register_taxonomy_book_author' ) );

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
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Helper function to register the all the taxonomies
	 *
	 * @since 1.5.0
	 */
	public function register_the_taxonomy( $args = array() ) {
		if ( empty( $args ) )
			return;

		$singular          = $args['singular'];
		$plural            = $args['plural'];
		$slug              = $args['slug'];
		$show_ui           = $args['show_ui'];
		$show_in_nav_menus = $args['show_in_nav_menus'];
		$tagcloud          = $args['show_tagcloud'];
		$hierarchical      = $args['hierarchical'];

		$labels = array(
			'name' => $plural,
			'singular_name' => $singular,
			'search_items' =>  sprintf( __( 'Search %s', 'book-review-library' ), $plural ),
			'popular_items' => sprintf( __( 'Popular %s', 'book-review-library' ), $plural ),
			'all_items' => sprintf( __( 'All %s', 'book-review-library' ), $plural ),
			'parent_item' => sprintf( __( 'Parent %s', 'book-review-library' ), $singular ),
			'parent_item_colon' => sprintf( __( 'Parent %s:', 'book-review-library' ), $singular ),
			'edit_item' => sprintf( __( 'Edit %s', 'book-review-library' ), $singular ),
			'update_item' => sprintf( __( 'Update %s', 'book-review-library' ), $singular ),
			'add_new_item' => sprintf( __( 'Add New %s', 'book-review-library' ), $singular ),
			'new_item_name' => sprintf( __( 'New %s Name', 'book-review-library' ), $singular ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'book-review-library' ), $plural ),
			'add_or_remove_items' => sprintf( __( 'Add or remove %s', 'book-review-library' ), $plural ),
			'choose_from_most_used' => sprintf( __( 'Choose from the most used %s', 'book-review-library' ), $plural ),
			'menu_name' => $plural,
		);

		$taxonomy = array(
			'label' => $plural,
			'labels' => $labels,
			'public' => true,
			'show_in_nav_menus' => $show_in_nav_menus,
			'show_ui' => $show_ui,
			'show_tagcloud' => $tagcloud,
			'hierarchical' => $hierarchical,
			'query_var' =>  $slug,
			'rewrite' => array(
				'slug' => $slug,
				'with_front' => true,
				'hierarchical' => false,
			),
			'capabilities' => array(
				'manage_terms' => 'edit_book-reviews',
				'edit_terms' => 'edit_book-reviews',
				'delete_terms' => 'edit_others_book-reviews',
				'manage_categories' => 'edit_book-reviews',
				'assign_terms' => 'edit_book-reviews'
			),
		);

		register_taxonomy( $slug, array( 'book-review' ), $taxonomy );
	}

	/**
	 * Register the genre taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_genre() {
		register_taxonomy('genre', array('book-review'), array(
			'label' => __('Genres', 'book-review-library'),
			'labels' => array(
				'name' => __( 'Genres', 'book-review-library' ),
				'singular_name' => __( 'Genre', 'book-review-library' ),
				'search_items' =>  __( 'Search Genres', 'book-review-library' ),
				'popular_items' => __( 'Popular Genres', 'book-review-library' ),
				'all_items' => __( 'All Genres', 'book-review-library' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( 'Edit Genre', 'book-review-library' ),
				'update_item' => __( 'Update Genre', 'book-review-library' ),
				'add_new_item' => __( 'Add New Genre', 'book-review-library' ),
				'new_item_name' => __( 'New Genre Name', 'book-review-library' ),
				'separate_items_with_commas' => __( 'Separate genres with commas', 'book-review-library' ),
				'add_or_remove_items' => __( 'Add or remove genres', 'book-review-library' ),
				'choose_from_most_used' => __( 'Choose from the most used genres', 'book-review-library' ),
				'not_found' => __( 'No genres found', 'book-review-library' ),
				'menu_name' => __( 'Genres', 'book-review-library' ),
			),
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => false,
			'update_count_callback' => '',
			'query_var' => 'genre',
			'rewrite' => array(
				'slug' => 'genre',
				'with_front' => true,
				'hierarchical' => false,
			),
			'capabilities' => array(
				'manage_terms' => 'edit_book-reviews',
				'edit_terms' => 'edit_book-reviews',
				'delete_terms' => 'edit_others_book-reviews',
				'manage_categories' => 'edit_book-reviews',
				'assign_terms' => 'edit_book-reviews'
			),
		));
	}


	/**
	 * Register the review author taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_review_author() {
		register_taxonomy('review-author', array('book-review'), array(
			'label' => __('Review Author', 'book-review-library'),
			'labels' => array(
				'name' => __( 'Review Author', 'book-review-library' ),
				'singular_name' => __( 'Review Author', 'book-review-library' ),
				'search_items' =>  __( 'Search Review Authors', 'book-review-library' ),
				'popular_items' => __( 'Popular Review Authors', 'book-review-library' ),
				'all_items' => __( 'All Review Authors', 'book-review-library' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( 'Edit Review Author', 'book-review-library' ),
				'update_item' => __( 'Update Review Author', 'book-review-library' ),
				'add_new_item' => __( 'Add New Review Author', 'book-review-library' ),
				'new_item_name' => __( 'New Review Author Name', 'book-review-library' ),
				'separate_items_with_commas' => __( 'Separate Review Authors with commas', 'book-review-library' ),
				'add_or_remove_items' => __( 'Add or remove Review Authors', 'book-review-library' ),
				'choose_from_most_used' => __( 'Choose from the most used Review Authors', 'book-review-library' ),
				'menu_name' => __( 'Review Authors', 'book-review-library' ),
			),
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => false,
			'hierarchical' => true,
			'update_count_callback' => '',
			'query_var' => 'review-author',
			'rewrite' => array(
				'slug' => 'review-author',
				'with_front' => true,
				'hierarchical' => false,
			),
			'capabilities' => array(
				'manage_terms' => 'edit_book-reviews',
				'edit_terms' => 'edit_book-reviews',
				'delete_terms' => 'edit_others_book-reviews',
				'manage_categories' => 'edit_book-reviews',
				'assign_terms' => 'edit_book-reviews'
			),
		));
	}


	/**
	 * Register the book author taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_book_author() {
		register_taxonomy('book-author', array('book-review'), array(
			'label' => __('Book Authors', 'book-review-library'),
			'labels' => array(
				'name' => __( 'Book Authors', 'book-review-library' ),
				'singular_name' => __( 'Author', 'book-review-library' ),
				'search_items' =>  __( 'Search Book Authors', 'book-review-library' ),
				'popular_items' => __( 'Popular Book Authors', 'book-review-library' ),
				'all_items' => __( 'All Book Authors', 'book-review-library' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( 'Edit Author', 'book-review-library' ),
				'update_item' => __( 'Update Author', 'book-review-library' ),
				'add_new_item' => __( 'Add New Author', 'book-review-library' ),
				'new_item_name' => __( 'New Author Name', 'book-review-library' ),
				'separate_items_with_commas' => __( 'Separate Book Authors with commas', 'book-review-library' ),
				'add_or_remove_items' => __( 'Add or remove Book Authors', 'book-review-library' ),
				'choose_from_most_used' => __( 'Choose from the most used Book Authors', 'book-review-library' ),
				'menu_name' => __( 'Book Authors', 'book-review-library' ),
			),
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => true,
			'update_count_callback' => '',
			'query_var' => 'book-author',
			'rewrite' => array(
				'slug' => 'book-author',
				'with_front' => true,
				'hierarchical' => false,
			),
			'capabilities' => array(
				'manage_terms' => 'edit_book-reviews',
				'edit_terms' => 'edit_book-reviews',
				'delete_terms' => 'edit_others_book-reviews',
				'manage_categories' => 'edit_book-reviews',
				'assign_terms' => 'edit_book-reviews'
			),
		));
	}

	/**
	 * Register the reading level taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_reading_level() {
		register_taxonomy('reading-level', array('book-review'), array(
			'label' => __('Reading Level', 'book-review-library'),
			'labels' => array(
				'name' => __( 'Reading Level', 'book-review-library' ),
				'singular_name' => __( 'Reading Level', 'book-review-library' ),
				'search_items' =>  __( 'Search Reading Levels', 'book-review-library' ),
				'popular_items' => __( 'Popular Reading Levels', 'book-review-library' ),
				'all_items' => __( 'All Reading Levels', 'book-review-library' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( 'Edit Reading Level', 'book-review-library' ),
				'update_item' => __( 'Update Reading Level', 'book-review-library' ),
				'add_new_item' => __( 'Add New Reading Level', 'book-review-library' ),
				'new_item_name' => __( 'New Reading Level Name', 'book-review-library' ),
				'separate_items_with_commas' => __( 'Separate Reading Levels with commas', 'book-review-library' ),
				'add_or_remove_items' => __( 'Add or remove Reading Levels', 'book-review-library' ),
				'choose_from_most_used' => __( 'Choose from the most used Reading Levels', 'book-review-library' ),
				'menu_name' => __( 'Reading Levels', 'book-review-library' ),
			),
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => false,
			'update_count_callback' => '',
			'query_var' => 'reading-level',
			'rewrite' => array(
				'slug' => 'reading-level',
				'with_front' => true,
				'hierarchical' => false,
			),
			'capabilities' => array(
				'manage_terms' => 'edit_book-reviews',
				'edit_terms' => 'edit_book-reviews',
				'delete_terms' => 'edit_others_book-reviews',
				'manage_categories' => 'edit_book-reviews',
				'assign_terms' => 'edit_book-reviews'
			),
		));
	}

	/**
	 * Register the subject taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_subject() {
		$args = array(
			'singular'          => __( 'Subject', 'book-review-library' ),
			'plural'            => __( 'Subjects', 'book-review-library' ),
			'slug'              => 'subject',
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'hierarchical'      => false
		);
		$this->register_the_taxonomy( $args );
	}

	/**
	 * Register the illustrator taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_illustrator() {
		$args = array(
			'singular'          => __( 'Illustrator', 'book-review-library' ),
			'plural'            => __( 'Illustrators', 'book-review-library' ),
			'slug'              => 'illustrator',
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'hierarchical'      => true
		);
		$this->register_the_taxonomy( $args );
	}

	/**
	 * Register the awards taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_awards() {
		$args = array(
			'singular'          => __( 'Award', 'book-review-library' ),
			'plural'            => __( 'Awards', 'book-review-library' ),
			'slug'              => 'awards',
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'hierarchical'      => false
		);
		$this->register_the_taxonomy( $args );
	}

	/**
	 * Register the series taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_series() {
		$args = array(
			'singular'          => __( 'Series', 'book-review-library' ),
			'plural'            => __( 'Series', 'book-review-library' ),
			'slug'              => 'series',
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'hierarchical'      => true
		);
		$this->register_the_taxonomy( $args );
	}

	/**
	 * Register the rating taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_rating() {
		$args = array(
			'singular'          => __( 'Star Rating', 'book-review-library' ),
			'plural'            => __( 'Star Ratings', 'book-review-library' ),
			'slug'              => 'rating',
			'show_ui'           => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'hierarchical'      => false
		);
		$this->register_the_taxonomy( $args );
	}

	/**
	 * Inserts the rating levels
	 *
	 * @since 	1.0.0
	 */
	public function insert_star_ratings() {
		wp_insert_term( '0', 'rating', array(
			'description' => __( 'Zero stars', 'book-review-library' ),
			'slug' => 'zero-stars'
		) );
		wp_insert_term( '1', 'rating', array(
			'description' => __( 'One star', 'book-review-library' ),
			'slug' => 'one-star'
		) );
		wp_insert_term( '2', 'rating', array(
			'description' => __( 'Two stars', 'book-review-library' ),
			'slug' => 'two-stars'
		) );
		wp_insert_term( '3', 'rating', array(
			'description' => __( 'Three stars', 'book-review-library' ),
			'slug' => 'three-stars'
		) );
		wp_insert_term( '4', 'rating', array(
			'description' => __( 'Four stars', 'book-review-library' ),
			'slug' => 'four-stars'
		) );
		wp_insert_term( '5', 'rating', array(
			'description' => __( 'Five stars', 'book-review-library' ),
			'slug' => 'five-stars'
		) );
	}

	/**
	 * Removes rating submenu so rating levels cannot be (easily) changed from the default
	 *
	 * @since 	1.0.0
	 */
	public function remove_rating_submenu() {
		remove_submenu_page('edit.php?post_type=book-review','edit-tags.php?taxonomy=rating&amp;post_type=book-review');
	}

}

Book_Review_Library_Taxonomies::get_instance();