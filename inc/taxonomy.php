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
		register_taxonomy('subject', array('book-review'), array(
			'label' => __('Subjects', 'book-review-library'),
			'labels' => array(
				'name' => __( 'Subjects', 'book-review-library' ),
				'singular_name' => __( 'Subject', 'book-review-library' ),
				'search_items' =>  __( 'Search Subjects', 'book-review-library' ),
				'popular_items' => __( 'Popular Subjects', 'book-review-library' ),
				'all_items' => __( 'All Subjects', 'book-review-library' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( 'Edit Subject', 'book-review-library' ),
				'update_item' => __( 'Update Subject', 'book-review-library' ),
				'add_new_item' => __( 'Add New Subject', 'book-review-library' ),
				'new_item_name' => __( 'New Subject Name', 'book-review-library' ),
				'separate_items_with_commas' => __( 'Separate Subjects with commas', 'book-review-library' ),
				'add_or_remove_items' => __( 'Add or remove Subjects', 'book-review-library' ),
				'choose_from_most_used' => __( 'Choose from the most used Subjects', 'book-review-library' ),
				'menu_name' => __( 'Subjects', 'book-review-library' ),
			),
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => false,
			'update_count_callback' => '',
			'query_var' => 'subject',
			'rewrite' => array(
				'slug' => 'subject',
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
	 * Register the illustrator taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_illustrator() {
		register_taxonomy('illustrator', array('book-review'), array(
			'label' => __('Illustrators', 'book-review-library'),
			'labels' => array(
				'name' => __( 'Illustrators', 'book-review-library' ),
				'singular_name' => __( 'Illustrator', 'book-review-library' ),
				'search_items' =>  __( 'Search Illustrators', 'book-review-library' ),
				'popular_items' => __( 'Popular Illustrators', 'book-review-library' ),
				'all_items' => __( 'All Illustrators', 'book-review-library' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( 'Edit Illustrator', 'book-review-library' ),
				'update_item' => __( 'Update Illustrator', 'book-review-library' ),
				'add_new_item' => __( 'Add New Illustrator', 'book-review-library' ),
				'new_item_name' => __( 'New Illustrator Name', 'book-review-library' ),
				'separate_items_with_commas' => __( 'Separate Illustrators with commas', 'book-review-library' ),
				'add_or_remove_items' => __( 'Add or remove Illustrators', 'book-review-library' ),
				'choose_from_most_used' => __( 'Choose from the most used Illustrators', 'book-review-library' ),
				'menu_name' => __( 'Illustrators', 'book-review-library' ),
			),
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => true,
			'update_count_callback' => '',
			'query_var' => 'illustrator',
			'rewrite' => array(
				'slug' => 'illustrator',
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
	 * Register the awards taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_awards() {
		register_taxonomy('awards', array('book-review'), array(
			'label' => __('Awards', 'book-review-library'),
			'labels' => array(
				'name' => __( 'Awards', 'book-review-library' ),
				'singular_name' => __( 'Award', 'book-review-library' ),
				'search_items' =>  __( 'Search Awards', 'book-review-library' ),
				'popular_items' => __( 'Popular Awards', 'book-review-library' ),
				'all_items' => __( 'All Awards', 'book-review-library' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( 'Edit Award', 'book-review-library' ),
				'update_item' => __( 'Update Award', 'book-review-library' ),
				'add_new_item' => __( 'Add New Award', 'book-review-library' ),
				'new_item_name' => __( 'New Award Name', 'book-review-library' ),
				'separate_items_with_commas' => __( 'Separate Awards with commas', 'book-review-library' ),
				'add_or_remove_items' => __( 'Add or remove Awards', 'book-review-library' ),
				'choose_from_most_used' => __( 'Choose from the most used Awards', 'book-review-library' ),
				'menu_name' => __( 'Awards', 'book-review-library' ),
			),
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => false,
			'update_count_callback' => '',
			'query_var' => 'awards',
			'rewrite' => array(
				'slug' => 'awards',
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
	 * Register the series taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_series() {
		register_taxonomy('series', array('book-review'), array(
			'label' => __('Series', 'book-review-library'),
			'labels' => array(
				'name' => __( 'Series', 'book-review-library' ),
				'singular_name' => __( 'Series', 'book-review-library' ),
				'search_items' =>  __( 'Search Series', 'book-review-library' ),
				'popular_items' => __( 'Popular Series', 'book-review-library' ),
				'all_items' => __( 'All Series', 'book-review-library' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( 'Edit Series', 'book-review-library' ),
				'update_item' => __( 'Update Series', 'book-review-library' ),
				'add_new_item' => __( 'Add New Series', 'book-review-library' ),
				'new_item_name' => __( 'New Series Name', 'book-review-library' ),
				'separate_items_with_commas' => __( 'Separate Series with commas', 'book-review-library' ),
				'add_or_remove_items' => __( 'Add or remove Series', 'book-review-library' ),
				'choose_from_most_used' => __( 'Choose from the most used Series', 'book-review-library' ),
				'menu_name' => __( 'Series', 'book-review-library' ),
			),
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => true,
			'update_count_callback' => '',
			'query_var' => 'series',
			'rewrite' => array(
				'slug' => 'series',
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
	 * Register the rating taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_rating() {
		// $singular     = $args['singular'];
		// $plural       = $args['plural'];
		// $slug         = $args['slug'];
		// $show_ui      = $args['show_ui'];
		// $tagcloud     = $args['show_tagcloud'];
		// $hierarchical = $args['hierarchical'];
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

		// register_taxonomy('rating', array('book-review'), array(
		// 	'label' => __('Star Ratings', 'book-review-library'),
		// 	'labels' => array(
		// 		'name' => __( 'Star Ratings', 'book-review-library' ),
		// 		'singular_name' => __( 'Star', 'book-review-library' ),
		// 		'search_items' =>  __( 'Search Ratings', 'book-review-library' ),
		// 		'popular_items' => __( 'Popular Ratings', 'book-review-library' ),
		// 		'all_items' => __( 'Stars', 'book-review-library' ),
		// 		'parent_item' => null,
		// 		'parent_item_colon' => null,
		// 		'edit_item' => __( 'Edit Rating', 'book-review-library' ),
		// 		'update_item' => __( 'Update Rating', 'book-review-library' ),
		// 		'add_new_item' => __( 'Add New Rating', 'book-review-library' ),
		// 		'new_item_name' => __( 'New Rating Name', 'book-review-library' ),
		// 		'separate_items_with_commas' => __( 'Separate Star Ratings with commas', 'book-review-library' ),
		// 		'add_or_remove_items' => __( 'Add or remove Star Ratings', 'book-review-library' ),
		// 		'choose_from_most_used' => __( 'Choose from the most used Star Ratings', 'book-review-library' ),
		// 		'menu_name' => __( 'Star Ratings', 'book-review-library' ),
		// 	),
		// 	'public' => true,
		// 	'show_in_nav_menus' => false,
		// 	'show_ui' => true,
		// 	'show_tagcloud' => false,
		// 	'hierarchical' => true,
		// 	'update_count_callback' => '',
		// 	'query_var' => 'rating',
		// 	'rewrite' => array(
		// 		'slug' => 'rating',
		// 		'with_front' => true,
		// 		'hierarchical' => false,
		// 	),
		// 	'capabilities' => array(
		// 		'manage_terms' => 'edit_book-reviews',
		// 		'edit_terms' => 'edit_book-reviews',
		// 		'delete_terms' => 'edit_others_book-reviews',
		// 		'manage_categories' => 'edit_book-reviews',
		// 		'assign_terms' => 'edit_book-reviews'
		// 	),
		// ));
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

		$taxonomy = array(
			'label' => $plural,
			'labels' => array(
				'name' => $plural,
				'singular_name' => $singular,
				'search_items' =>  sprintf( __( 'Search %s', 'book-review-library' ), $plural ),
				'popular_items' => sprintf( __( 'Popular %s', 'book-review-library' ), $plural ),
				'all_items' => sprintf( __( 'All %s', 'book-review-library' ), $plural ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => sprintf( __( 'Edit %s' ), $singular ),
				'update_item' => sprintf( __( 'Update %s' ), $singular ),
				'add_new_item' => sprintf( __( 'Add New %s' ), $singular ),
				'new_item_name' => sprintf( __( 'New %s Name' ), $singular ),
				'separate_items_with_commas' => sprintf( __( 'Separate %s with commas' ), $plural ),
				'add_or_remove_items' => sprintf( __( 'Add or remove %s' ), $plural ),
				'choose_from_most_used' => sprintf( __( 'Choose from the most used %s' ), $plural ),
				'menu_name' => $plural,
			),
			'public' => true,
			'show_in_nav_menus' => $show_in_nav_menus,
			'show_ui' => $show_ui,
			'show_tagcloud' => $tagcloud,
			'hierarchical' => $hierarchical,
			'update_count_callback' => '',
			'query_var' =>  $slug,
			'rewrite' => array(
				'slug' =>  $slug,
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

		register_taxonomy( $slug, array( 'book-review' ), array( $taxonomy ) );
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