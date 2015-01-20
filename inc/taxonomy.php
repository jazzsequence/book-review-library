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

		// Review Authors
		if ( book_reviews_is_option_enabled( 'review-author' ) )
			add_action( 'init', array( $this, 'register_taxonomy_review_author' ) );

		// Reading Level
		if ( book_reviews_is_option_enabled( 'reading-level' ) )
			add_action( 'init', array( $this, 'register_taxonomy_reading_level' ) );

		// Subject
		if ( book_reviews_is_option_enabled( 'subject' ) )
			add_action( 'init', array( $this, 'register_taxonomy_subject' ) );

		// Illustrator
		if ( book_reviews_is_option_enabled( 'illustrator' ) )
			add_action( 'init', array( $this, 'register_taxonomy_illustrator' ) );

		// Awards
		if ( book_reviews_is_option_enabled( 'awards' ) )
			add_action( 'init', array( $this, 'register_taxonomy_awards' ) );

		// Series
		if ( book_reviews_is_option_enabled( 'series' ) )
			add_action( 'init', array( $this, 'register_taxonomy_series' ) );

		// Star Ratings
		if ( book_reviews_is_option_enabled( 'rating' ) ) {
			add_action( 'init', array( $this, 'register_taxonomy_rating' ) );
			add_action( 'init', array( $this, 'insert_star_ratings' ) );
			add_action( 'admin_init', array( $this, 'remove_rating_submenu' ) );
		}

		// Genres (on always)
		add_action( 'init', array( $this, 'register_taxonomy_genre' ) );

		// Book Authors (on always)
		add_action( 'init', array( $this, 'register_taxonomy_book_author' ) );

		// Languages
		if ( book_reviews_is_option_enabled( 'languages' ) )
			add_action( 'init', array( $this, 'register_taxonomy_language' ) );

		// Format
		if ( book_reviews_is_option_enabled( 'format' ) ) {
			add_action( 'init', array( $this, 'register_taxonomy_format' ) );
			add_action( 'init', array( $this, 'insert_formats' ) );
		}

		// Publisher
		if ( book_reviews_is_option_enabled( 'publisher' ) )
			add_action( 'init', array( $this, 'register_taxonomy_publisher' ) );

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
	 * @param array $args 	singular (required), plural (required), slug (required), show_ui, show_in_nav_menus, tagcloud,
	 * 						hierarchical, use_singular_labels
	 *
	 * @since 1.5.0
	 */
	public function register_the_taxonomy( $args = array() ) {
		if ( empty( $args ) )
			return;

		$singular          = $args['singular']; // required
		$plural            = $args['plural'];   // required
		$slug              = $args['slug'];     // required
		$show_ui           = ( isset( $args['show_ui'] ) ) ? $args['show_ui'] : true;
		$show_in_nav_menus = ( isset( $args['show_in_nav_menus'] ) ) ? $args['show_in_nav_menus'] : true;
		$tagcloud          = ( isset( $args['show_tagcloud'] ) ) ? $args['show_tagcloud'] : true;
		$hierarchical      = ( isset ( $args['hierarchical'] ) ) ? $args['hierarchical'] : true;
		$name              = ( isset( $args['use_singular_labels'] ) && $args['use_singular_labels'] ) ? $singular : $plural;

		$labels = array(
			'name' => $name,
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
			'label' => $name,
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
		$args = array(
			'singular'            => __( 'Genre', 'book-review-library' ),
			'plural'              => __( 'Genres', 'book-review-library' ),
			'slug'                => 'genre',
			'hierarchical'        => false,
			'use_singular_labels' => true
		);
		$this->register_the_taxonomy( $args );
	}


	/**
	 * Register the review author taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_review_author() {
		$args = array(
			'singular'            => __( 'Review Author', 'book-review-library' ),
			'plural'              => __( 'Review Authors', 'book-review-library' ),
			'slug'                => 'review-author',
			'show_tagcloud'       => false,
			'use_singular_labels' => true
		);
		$this->register_the_taxonomy( $args );
	}


	/**
	 * Register the book author taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_book_author() {
		$args = array(
			'singular'          => __( 'Author', 'book-review-library' ),
			'plural'            => __( 'Book Authors', 'book-review-library' ),
			'slug'              => 'book-author',
		);
		$this->register_the_taxonomy( $args );
	}

	/**
	 * Register the reading level taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_reading_level() {
		$args = array(
			'singular'            => __('Reading Level', 'book-review-library'),
			'plural'              => __('Reading Levels', 'book-review-library'),
			'slug'                => 'reading-level',
			'hierarchical'        => false,
			'use_singular_labels' => true
		);
		$this->register_the_taxonomy( $args );
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
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'hierarchical'      => false
		);
		$this->register_the_taxonomy( $args );
	}

	/**
	 * Register the language taxonomy
	 *
	 * @since 1.5.0
	 * @todo Add action, add option
	 */
	public function register_taxonomy_language() {
		$args = array(
			'singular'            => __( 'Language', 'book-review-library' ),
			'plural'              => __( 'Languages', 'book-review-library' ),
			'slug'                => 'language',
			'hierarchical'        => false,
		);
		$this->register_the_taxonomy( $args );
	}

	/**
	 * Register the format taxonomy
	 *
	 * @since 1.5.0
	 * @todo add action, option, cmb, default format terms
	 */
	public function register_taxonomy_format() {
		$args = array(
			'singular'            => __( 'Format', 'book-review-library' ),
			'plural'              => __( 'Formats', 'book-review-library' ),
			'slug'                => 'format',
			'hierarchical'        => false,
			'use_singular_labels' => true
		);
		$this->register_the_taxonomy( $args );
	}

	/**
	 * Register the publisher taxonomy
	 *
	 * @since 1.5.0
	 * @todo add action, option, cmb
	 */
	public function register_taxonomy_publisher() {
		$args = array(
			'singular'            => __( 'Publisher', 'book-review-library' ),
			'plural'              => __( 'Publishers', 'book-review-library' ),
			'slug'                => 'publisher',
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
	 * Insert book formats
	 *
	 * @since 1.5.0
	 */
	public function insert_formats() {
		wp_insert_term( 'Audiobook', 'format', array(
			'description' => __( 'Books on tape, CD, Audible and the like', 'book-review-library' ),
			'slug' => 'audiobook'
		) );
		wp_insert_term( 'Book', 'format', array(
			'description' => __( 'The default format for book reviews', 'book-review-library' ),
			'slug' => 'book'
		) );
		wp_insert_term( 'Graphic Novel', 'format', array(
			'description' => __( 'Long form comic, manga, or other illustrated story', 'book-review-library' ),
			'slug' => 'graphic-novel'
		) );
		wp_insert_term( 'eBook', 'format', array(
			'description' => __( 'Any book in digital format', 'book-review-library' ),
			'slug' => 'ebook'
		) );
		wp_insert_term( 'Periodical', 'format', array(
			'description' => __( 'Magazine or newspaper published at regular intervals', 'book-review-library' ),
			'slug' => 'periodical'
		) );
		wp_insert_term( 'Reference' , 'format', array(
			'description' => __( 'Encyclopedia, dictionary or other nonfiction reference material', 'book-review-library' ),
			'slug' => 'reference'
		) );
		wp_insert_term( 'Picture Book', 'format', array(
			'description' => __( 'Any book -- generally a children\'s book -- in which pictures make up a large portion -- if not most -- of the book\'s content', 'book-review-library' ),
			'slug' => 'picture-book'
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