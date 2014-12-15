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

		$book_reviews = Book_Reviews::get_instance();

		// get the options
		$options = $book_reviews->get_options();

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

		// check if ratings are enabled
		if ( $book_reviews->are_ratings_enabled() ) {
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
		if ( $book_reviews->is_author_image_enabled() ) {
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

		$meta_boxes['genre-select'] = array(
			'id'           => 'genre-select',
			'title'        => __( 'Genre', 'book-review-library' ),
			'show_names'   => false,
			'object_types' => array( 'book-review' ),
			'context'      => 'side',
			'priority'     => 'low',
			'fields'       => array(
				array(
					'id'               => 'genre-select',
					'taxonomy'         => 'genre',
					'type'             => 'taxonomy_radio'
				)
			)
		);

		// check if reading level is enabled
		if ( $book_reviews->is_reading_level_enabled() ) {
			$meta_boxes['reading-level'] = array(
				'id'           => 'reading-level',
				'title'        => __( 'Reading Level', 'book-review-library' ),
				'show_names'   => false,
				'object_types' => array( 'book-review' ),
				'context'      => 'normal',
				'priority'     => 'low',
				'fields'       => array(
					array(
						'id'               => 'reading-level',
						'taxonomy'         => 'reading-level',
						'type'             => 'taxonomy_radio'
					)
				)
			);
		}

		return $meta_boxes;
 	}

}

Book_Review_Library_CMB::get_instance();