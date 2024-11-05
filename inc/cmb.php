<?php

class Book_Review_Library_CMB {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	private function __construct() {
		// initialize CMB2
		if ( file_exists( __DIR__ . '/cmb2/init.php' ) ) {
			require_once __DIR__ . '/cmb2/init.php';
		}

		// deal with meta boxes
		add_filter( 'cmb2_meta_boxes', [ $this, 'do_cmb_meta_boxes' ] );
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
	 * Deal with the metaboxes
	 *
	 * @since   1.5
	 */
	public function do_cmb_meta_boxes( array $meta_boxes ) {

		$meta_boxes['book-reviews-meta'] = [
			'id'           => 'book-reviews-meta',
			'title'        => __( 'Additional Information', 'book-review-library' ),
			'object_types' => [ 'book-review' ],
			'context'      => 'side',
			'priority'     => 'low',
			'show_names'   => true,
			'fields'       => [
				'isbn'          => [
					'name'       => __( 'ISBN:', 'book-review-library' ),
					'id'         => 'isbn',
					'type'       => 'text_medium',
				],
				'book_in_stock' => [
					'name'       => __( 'In Stock?', 'book-review-library' ),
					'id'         => 'book_in_stock',
					'type'       => 'select',
					'default'    => 1,
					'options'    => [
						0 => __( 'Book is out of stock', 'book-review-library' ),
						1 => __( 'Book is in stock', 'book-review-library' ),
					],
					'show_on_cb' => [ $this, 'is_stock_enabled' ],
				],
			],
		];

		$meta_boxes['book-information'] = [
			'id'           => 'book-information',
			'title'        => __( 'Book Details', 'book-review-library' ),
			'object_types' => [ 'book-review' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
			'fields'       => [
				'illustrator' => [
					'name'        => __( 'Illustrator', 'book-review-library' ),
					'id'          => 'illustrator',
					'taxonomy'    => 'illustrator',
					'type'        => 'taxonomy_multicheck',
					'show_on_cb'  => [ $this, 'is_illustrator_enabled' ],
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new illustrator', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=illustrator&post_type=book-review' ),
					'options'     => [
						'no_terms_text' => __( 'No illustrators have been added', 'book-review-library' ),
					],
				],
				'series' => [
					'name'        => __( 'Series', 'book-review-library' ),
					'id'          => 'series',
					'taxonomy'    => 'series',
					'type'        => 'taxonomy_radio',
					'show_on_cb'  => [ $this, 'is_series_enabled' ],
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new series', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=series&post_type=book-review' ),
					'options'     => [
						'no_terms_text' => __( 'No series have been added', 'book-review-library' ),
					],
				],
				'genre' => [
					'name'        => __( 'Genre', 'book-review-library' ),
					'id'          => 'genre',
					'taxonomy'    => 'genre',
					'type'        => 'taxonomy_multicheck',
					'show_on_cb'  => [ $this, 'is_genre_enabled' ],
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new genre', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=genre&post_type=book-review' ),
					'options'     => [
						'no_terms_text' => __( 'No genres have been added', 'book-review-library' ),
					],
				],
				'subjects' => [
					'name'        => __( 'Subjects', 'book-review-library' ),
					'id'          => 'subjects',
					'taxonomy'    => 'subject',
					'type'        => 'taxonomy_multicheck',
					'show_on_cb'  => [ $this, 'are_subjects_enabled' ],
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new subject', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=subject' ),
					'options'     => [
						'no_terms_text' => __( 'No subjects have been added', 'book-review-library' ),
					],
				],
				'reading-level' => [
					'name'        => __( 'Reading Level', 'book-review-library' ),
					'id'          => 'reading-level',
					'taxonomy'    => 'reading-level',
					'type'        => 'taxonomy_radio',
					'show_on_cb'  => [ $this, 'is_reading_level_enabled' ],
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new reading level', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=reading-level&post_type=book-review' ),
					'options'     => [
						'no_terms_text' => __( 'No reading levels have been added', 'book-review-library' ),
					],
				],
				'languages' => [
					'name'        => __( 'Language', 'book-review-library' ),
					'id'          => 'languages',
					'taxonomy'    => 'language',
					'type'        => 'taxonomy_radio',
					'show_on_cb'  => [ $this, 'are_languages_enabled' ],
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new language', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=language&post_type=book-review' ),
					'options'     => [
						'no_terms_text' => __( 'No languages have been added', 'book-review-library' ),
					],
				],
				'format' => [
					'name'        => __( 'Format', 'book-review-library' ),
					'id'          => 'format',
					'taxonomy'    => 'format',
					'type'        => 'taxonomy_multicheck',
					'show_on_cb'  => [ $this, 'is_format_enabled' ],
					'default'     => 'book',
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new format', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=format&post_type=book-review' ),
					'options'     => [
						'no_terms_text' => __( 'No formats have been added', 'book-review-library' ),
					],
				],
				'publisher' => [
					'name'        => __( 'Publisher', 'book-review-library' ),
					'id'          => 'publisher',
					'taxonomy'    => 'publisher',
					'type'        => 'taxonomy_multicheck',
					'show_on_cb'  => [ $this, 'is_publisher_enabled' ],
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new publisher', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=publisher&post_type=book-review' ),
					'options'     => [
						'no_terms_text' => __( 'No publishers have been added', 'book-review-library' ),
					],
				],
			],
		];

		// If none of the book information fields are active, display a message in the meta box that these fields can be activated from the Options page.
		if ( ! book_reviews_is_option_enabled( 'illustrator' ) &&
			! book_reviews_is_option_enabled( 'series' ) &&
			! book_reviews_is_option_enabled( 'genre' ) &&
			! book_reviews_is_option_enabled( 'subject' ) &&
			! book_reviews_is_option_enabled( 'reading-level' ) &&
			! book_reviews_is_option_enabled( 'languages' ) &&
			! book_reviews_is_option_enabled( 'format' ) &&
			! book_reviews_is_option_enabled( 'publisher' )
			) {
			$meta_boxes['book-information'] = [
				'id'           => 'book-information',
				'title'        => __( 'Book Details', 'book-review-library' ),
				'object_types' => [ 'book-review' ],
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
				'fields'       => [
					'no_fields' => [
						'name' => __( 'No Fields Enabled', 'book-review-library' ),
						'id'   => 'no_fields',
						'type' => 'title',
						'desc' => sprintf( 
							__( 'No book information fields are currently enabled. You can enable these fields from the <a href="%s">Options page</a>.', 'book-review-library' ), 
							admin_url( 'admin.php?page=book-review-library-options' ) 
						),
					],
				],
			];
		}

		$meta_boxes['book-author-information'] = [
			'id'           => 'author-information',
			'title'        => __( 'Author Details', 'book-review-library' ),
			'object_types' => [ 'book-review' ],
			'context'      => 'side',
			'priority'     => 'default',
			'show_names'   => true,
			'fields'       => [
				'author' => [
					'id'            => 'author',
					'taxonomy'      => 'book-author',
					'type'          => 'taxonomy_multicheck',
					'after_field'   => sprintf( '<a href="%s">' . __( 'Add a new author', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=book-author&post_type=book-review' ),
					'options'       => [
						'no_terms_text' => __( 'No authors have been added', 'book-review-library' ),
					],
				],
				'author-image' => [
					'name'       => __( 'Author Image', 'book-review-library' ),
					'id'         => 'author-image',
					'desc'       => __( 'Upload or select an image for this book\'s author or enter a URL to an image. No image will display if none is uploaded.', 'book-review-library' ),
					'type'       => 'file',
					'show_on_cb' => [ $this, 'is_author_image_enabled' ],
				],
			],
		];

		if ( book_reviews_is_option_enabled( 'awards' ) ) {
			$meta_boxes['awards'] = [
				'id'           => 'book-awards',
				'title'        => __( 'Awards', 'book-review-library' ),
				'show_names'   => true,
				'object_types' => [ 'book-review' ],
				'context'      => 'normal',
				'priority'     => 'low',
				'fields'       => [
					'awards' => [
						'id'          => 'awards',
						'taxonomy'    => 'awards',
						'type'        => 'taxonomy_multicheck',
						'after_field' => sprintf( '<a href="%s">' . __( 'Add an award', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=awards&post_type=book-review' ),
						'options'     => [
							'no_terms_text' => __( 'No awards have been added', 'book-review-library' ),
						],
					],
					'award-images' => [
						'id'          => 'award-images',
						'name'        => __( 'Award Images', 'book-review-library' ),
						'type'        => 'file_list',
						'desc'        => __( 'File name or image title <em>must</em> match the award name.', 'book-review-library' ),
					],
				],
			];
		}

		// check if ratings are enabled
		if ( book_reviews_is_option_enabled( 'rating' ) ) {
			$meta_boxes['star-rating'] = [
				'id'           => 'star-rating',
				'title'        => __( 'Star Rating', 'book-review-library' ),
				'show_names'   => false,
				'object_types' => [ 'book-review' ],
				'context'      => 'side',
				'priority'     => 'high',
				'fields'       => [
					[
						'id'               => 'star-rating',
						'taxonomy'         => 'rating',
						'type'             => 'taxonomy_radio',
						'show_option_none' => false,
						'default'          => 'zero-stars',
					],
				],
			];
		}

		return $meta_boxes;
	}

	/**
	 * Callback function for stock meta box
	 *
	 * @since  1.5.0
	 * @return bool     True if stock is enabled, false if it isn't
	 */
	public function is_stock_enabled() {
		return book_reviews_is_option_enabled( 'stock' );
	}

	/**
	 * Callback function for awards meta box
	 *
	 * @since  1.5.0
	 * @return bool     True if awards is enabled, false if it isn't
	 */
	public function are_awards_enabled() {
		return book_reviews_is_option_enabled( 'awards' );
	}

	/**
	 * Callback function for illustrator meta box
	 *
	 * @since 1.5.0
	 * @return bool     True if illustrator is enabled, false if it isn't
	 */
	public function is_illustrator_enabled() {
		return book_reviews_is_option_enabled( 'illustrator' );
	}

	/**
	 * Callback function for subjects meta box
	 *
	 * @since 1.5.0
	 * @return bool     True if subjects are enabled, false if it isn't
	 */
	public function are_subjects_enabled() {
		return book_reviews_is_option_enabled( 'subject' );
	}

	/**
	 * Callback function for series meta box
	 *
	 * @since 1.5.0
	 * @return bool     True if series is enabled, false if it isn't
	 */
	public function is_series_enabled() {
		return book_reviews_is_option_enabled( 'series' );
	}

	/**
	 * Callback function for languages meta box
	 *
	 * @since 1.5.0
	 * @return bool     True if languages are enabled, false if it isn't
	 */
	public function are_languages_enabled() {
		return book_reviews_is_option_enabled( 'languages' );
	}

	/**
	 * Callback function for format meta box
	 *
	 * @since 1.5.0
	 * @return bool     True if format is enabled, false if it isn't
	 */
	public function is_format_enabled() {
		return book_reviews_is_option_enabled( 'format' );
	}

	/**
	 * Callback function for publisher meta box
	 *
	 * @since 1.5.0
	 * @return bool     True if publisher is enabled, false if it isn't
	 */
	public function is_publisher_enabled() {
		return book_reviews_is_option_enabled( 'publisher' );
	}

	/**
	 * Callback function for genre meta box
	 *
	 * @since 1.5.0
	 * @return bool     True if genre is enabled, false if it isn't
	 */
	public function is_genre_enabled() {
		return book_reviews_is_option_enabled( 'genre' );
	}

	/**
	 * Callback function for reading-level meta box
	 *
	 * @since 1.5.0
	 * @return bool     True if reading-level is enabled, false if it isn't
	 */
	public function is_reading_level_enabled() {
		return book_reviews_is_option_enabled( 'reading-level' );
	}

	/**
	 * Callback function for author-image meta box
	 *
	 * @since 1.5.0
	 * @return bool     True if author-image is enabled, false if it isn't
	 */
	public function is_author_image_enabled() {
		return book_reviews_is_option_enabled( 'author-image' );
	}
}

Book_Review_Library_CMB::get_instance();
