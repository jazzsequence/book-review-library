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
		if ( file_exists(  __DIR__ . '/cmb/init.php' ) ) {
			require_once  __DIR__ . '/cmb/init.php';
		}

		// deal with meta boxes
		add_filter( 'cmb2_meta_boxes', array( $this, 'do_cmb_meta_boxes') );
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
	 * Deal with the metaboxes
	 *
	 * @since 	1.5
	 */
	public function  do_cmb_meta_boxes( array $meta_boxes ) {

		$meta_boxes['book-reviews-meta'] = array(
			'id'           => 'book-reviews-meta',
			'title'        => __( 'Additional Information', 'book-review-library' ),
			'object_types' => array( 'book-review' ),
			'context'      => 'normal',
			'priority'     => 'default',
			'show_names'   => true,
			'fields'       => array(
				'isbn'          => array(
					'name'       => __( 'ISBN:', 'book-review-library' ),
					'id'         => 'isbn',
					'type'       => 'text_medium'
				),
				'book_in_stock' => array(
					'name'       => __( 'In Stock?', 'book-review-library' ),
					'id'         => 'book_in_stock',
					'type'       => 'select',
					'default'    => 1,
					'options'    => array(
						0 => __( 'Book is out of stock', 'book-review-library' ),
						1 => __( 'Book is in stock', 'book-review-library' )
					),
					'show_on_cb' => array( $this, 'is_stock_enabled' )
				),
				'award_image' => array(
					'name'       => __( 'Upload Award Image', 'book-review-library' ),
					'desc'       => __( 'Upload an image or enter a URL', 'book-review-library' ),
					'type'       => 'file',
					'id'         => 'award_image',
					'show_on_cb' => array( $this, 'are_awards_enabled' )
				)

			)
		);

		$meta_boxes['book-information'] = array(
			'id'           => 'book-information',
			'title'        => __( 'Book Details', 'book-review-library' ),
			'object_types' => array( 'book-review' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
			'fields'       => array(
/*				'author' => array(
					'name'          => __( 'Author', 'book-review-library' ),
					'id'            => 'author',
					'taxonomy'      => 'book-author',
					'type'          => 'taxonomy_multicheck',
					'after_field'   => sprintf( '<a href="%s">' . __( 'Add a new author', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=book-author&post_type=book-review' ),
					'options'       => array(
						'no_terms_text' => __( 'No authors have been added', 'book-review-library' )
					)
				), */
				'illustrator' => array(
					'name'        => __( 'Illustrator', 'book-review-library' ),
					'id'          => 'illustrator',
					'taxonomy'    => 'illustrator',
					'type'        => 'taxonomy_multicheck',
					'show_on_cb'  => 'is_illustrator_enabled',
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new illustrator', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=illustrator&post_type=book-review' ),
					'options'     => array(
						'no_terms_text' => __( 'No illustrators have been added', 'book-review-library' )
					)
				),
				'series' => array(
					'name'        => __( 'Series', 'book-review-library' ),
					'id'          => 'series',
					'taxonomy'    => 'series',
					'type'        => 'taxonomy_radio',
					'show_on_cb'  => 'is_series_enabled',
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new series', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=series&post_type=book-review' ),
					'options'     => array(
						'no_terms_text' => __( 'No series have been added', 'book-review-library' )
					)
				),
				'genre' => array(
					'name'        => __( 'Genre', 'book-review-library' ),
					'id'          => 'genre',
					'taxonomy'    => 'genre',
					'type'        => 'taxonomy_multicheck',
					'show_on_cb'  => 'is_genre_enabled',
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new genre', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=genre&post_type=book-review' ),
					'options'     => array(
						'no_terms_text' => __( 'No genres have been added', 'book-review-library' )
					)
				),
				'subjects' => array(
					'name'        => __( 'Subjects', 'book-review-library' ),
					'id'          => 'subjects',
					'taxonomy'    => 'subject',
					'type'        => 'taxonomy_multicheck',
					'show_on_cb'  => 'are_subjects_enabled',
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new subject', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=subject' ),
					'options'     => array(
						'no_terms_text' => __( 'No subjects have been added', 'book-review-library' )
					)
				),
				'reading-level' => array(
					'name'        => __( 'Reading Level', 'book-review-library' ),
					'id'          => 'reading-level',
					'taxonomy'    => 'reading-level',
					'type'        => 'taxonomy_radio',
					'show_on_cb'  => 'is_reading_level_enabled',
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new reading level', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=reading-level&post_type=book-review' ),
					'options'     => array(
						'no_terms_text' => __( 'No reading levels have been added', 'book-review-library' )
					)
				),
				'languages' => array(
					'name'        => __( 'Language', 'book-review-library' ),
					'id'          => 'languages',
					'taxonomy'    => 'language',
					'type'        => 'taxonomy_radio',
					'show_on_cb'  => 'are_languages_enabled',
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new language', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=language&post_type=book-review' ),
					'options'     => array(
						'no_terms_text' => __( 'No languages have been added', 'book-review-library' )
					)
				),
				'format' => array(
					'name'        => __( 'Format', 'book-review-library' ),
					'id'          => 'format',
					'taxonomy'    => 'format',
					'type'        => 'taxonomy_multicheck',
					'show_on_cb'  => 'is_format_enabled',
					'default'     => 'book',
					'after_field' => sprintf( '<a href="%s">' . __( 'Add a new format', 'book-review-library' ) . '</a>', 'edit-tags.php?taxonomy=format&post_type=book-review' ),
					'options'     => array(
						'no_terms_text' => __( 'No formats have been added', 'book-review-library' )
					)
				),
			)
		);

		// check if ratings are enabled
		if ( book_reviews_is_option_enabled( 'ratings' ) ) {
			$meta_boxes['star-rating'] = array(
				'id'           => 'star-rating',
				'title'        => __( 'Star Rating', 'book-review-library' ),
				'show_names'   => false,
				'object_types' => array( 'book-review' ),
				'context'      => 'side',
				'priority'     => 'high',
				'fields'       => array(
					array(
						'id'               => 'star-rating',
						'taxonomy'         => 'rating',
						'type'             => 'taxonomy_radio',
						'show_option_none' => false,
						'default'          => 'zero-stars'
					)
				)
			);
		}

		// check if author image is enabled
		if ( book_reviews_is_option_enabled( 'author-image' ) ) {
			$meta_boxes['author-image'] = array(
				'id'           => 'author-image',
				'title'        => __( 'Author Image', 'book-review-library' ),
				'show_names'   => false,
				'object_types' => array( 'book-review' ),
				'context'      => 'side',
				'priority'     => 'low',
				'fields'       => array(
					array(
						'id'   => 'author-image',
						'desc' => __( 'Upload or select an image for this book\'s author or enter a URL to an image. No image will display if none is uploaded.', 'book-review-library' ),
						'type' => 'file'
					)
				)
			);
		}

		return $meta_boxes;
 	}

 	/**
 	 * Callback function for stock meta box
 	 *
 	 * @since  1.5.0
 	 * @return bool 	True if stock is enabled, false if it isn't
 	 */
 	public function is_stock_enabled() {
 		return book_reviews_is_option_enabled( 'stock' );
 	}

 	/**
 	 * Callback function for awards meta box
 	 *
 	 * @since  1.5.0
 	 * @return bool 	True if awards is enabled, false if it isn't
 	 */
 	public function are_awards_enabled() {
 		return book_reviews_is_option_enabled( 'awards' );
 	}

 	/**
 	 * Callback function for illustrator meta box
 	 *
 	 * @since 1.5.0
 	 * @return bool 	True if illustrator is enabled, false if it isn't
 	 */
 	public function is_illustrator_enabled() {
 		return book_reviews_is_option_enabled( 'illustrator' );
 	}

 	/**
 	 * Callback function for subjects meta box
 	 *
 	 * @since 1.5.0
 	 * @return bool 	True if subjects are enabled, false if it isn't
 	 */
 	public function are_subjects_enabled() {
 		return book_reviews_is_option_enabled( 'subject' );
 	}

 	/**
 	 * Callback function for series meta box
 	 *
 	 * @since 1.5.0
 	 * @return bool 	True if series is enabled, false if it isn't
 	 */
 	public function is_series_enabled() {
 		return book_reviews_is_option_enabled( 'series' );
 	}

 	/**
 	 * Callback function for languages meta box
 	 *
 	 * @since 1.5.0
 	 * @return bool 	True if languages are enabled, false if it isn't
 	 */
 	public function are_languages_enabled() {
 		return book_reviews_is_option_enabled( 'languages' );
 	}

 	/**
 	 * Callback function for format meta box
 	 *
 	 * @since 1.5.0
 	 * @return bool 	True if format is enabled, false if it isn't
 	 */
 	public function is_format_enabled() {
 		return book_reviews_is_option_enabled( 'format' );
 	}

 	/**
 	 * Callback function for publisher meta box
 	 *
 	 * @since 1.5.0
 	 * @return bool 	True if publisher is enabled, false if it isn't
 	 */
 	public function is_publisher_enabled() {
 		return book_reviews_is_option_enabled( 'publisher' );
 	}

 	/**
 	 * Callback function for genre meta box
 	 *
 	 * @since 1.5.0
 	 * @return bool 	True if genre is enabled, false if it isn't
 	 */
 	public function is_genre_enabled() {
 		return book_reviews_is_option_enabled( 'genre' );
 	}

 	/**
 	 * Callback function for reading-level meta box
 	 *
 	 * @since 1.5.0
 	 * @return bool 	True if reading-level is enabled, false if it isn't
 	 */
 	public function is_reading_level_enabled() {
 		return book_reviews_is_option_enabled( 'reading-level' );
 	}

}

Book_Review_Library_CMB::get_instance();