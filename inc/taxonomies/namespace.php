<?php
/**
 * Taxonomies
 *
 * All the Book Review Library taxonomy stuff.
 *
 * @since 2.0.0-alpha
 *
 * @package BookReview
 */

namespace BookReview\Taxonomies;
use BookReview\Options as Options;
use BookReview\CMB2 as CMB2;

/**
 * Initialize the taxonomies.
 *
 * @since 2.0.0-alpha
 */
function bootstrap() {
	// Loop through and register all the taxonomies.
	foreach ( taxonomies() as $taxonomy => $args ) {
		$taxonomy = str_replace( '_', '-', $taxonomy );

		// Always-on taxonomies don't come up when checking the options.
		if ( ! Options\is_option_enabled( $args['slug'] ) && in_array( $taxonomy, [ 'genre', 'book-author' ] ) ) {
			$tax = str_replace( '-', '_', $taxonomy );
			add_action( 'init',      __NAMESPACE__ . '\\register_taxonomy_' . $tax );
			add_action( 'cmb2_init', __NAMESPACE__ . '\\add_cmb2_box_' . $tax );
		} elseif ( Options\is_option_enabled( $args['slug'] ) ) {
			$tax = str_replace( '-', '_', $taxonomy );
			add_action( 'init',      __NAMESPACE__ . '\\register_taxonomy_' . $tax );
			add_action( 'cmb2_init', __NAMESPACE__ . '\\add_cmb2_box_' . $tax );
		}
	}

	// Insert rating values and prevent ratings from being edited.
	if ( Options\is_option_enabled( 'rating' ) ) {
		add_action( 'init',       __NAMESPACE__ . '\\insert_star_ratings' );
		add_action( 'admin_init', __NAMESPACE__ . '\\remove_rating_submenu' );
	}

	// Insert formats.
	if ( Options\is_option_enabled( 'format' ) ) {
		add_action( 'init',       __NAMESPACE__ . '\\insert_formats' );
	}
}

/**
 * Helper function to register the all the taxonomies
 *
 * @param array $args Any arguments that can be passed into register_taxonomy or register_extended_taxonomy. $args['singular'] is required.
 *
 * @since 1.5.0
 */
function register_the_taxonomy( $args = [] ) {
	$singular                  = $args['singular']; // Required.
	$plural                    = isset( $args['plural'] ) ? $args['plural'] : $singular . 's';
	$slug                      = isset( $args['slug'] ) ? $args['slug'] : sanitize_title( $args['singular'] );
	$name                      = isset( $args['slug'] ) ? $args['slug'] : $slug;
	$args['show_ui']           = isset( $args['show_ui'] ) ? $args['show_ui'] : true;
	$args['show_in_nav_menus'] = isset( $args['show_in_nav_menus'] ) ? $args['show_in_nav_menus'] : true;
	$args['tagcloud']          = isset( $args['show_tagcloud'] ) ? $args['show_tagcloud'] : true;
	$args['hierarchical']      = isset( $args['hierarchical'] ) ? $args['hierarchical'] : true;
	$args['dashboard_glance']  = isset( $args['dashboard_glance'] ) ? $args['dashboard_glance'] : true;
	$args['admin_cols']        = isset( $args['admin_cols'] ) ? $args['admin_cols'] : [];
	$args['meta_box']          = false; // Leaving here in case extended cpts adds support for customizing positions.

	$args['capabilities']      = [
		'manage_terms'      => 'edit_book-reviews',
		'edit_terms'        => 'edit_book-reviews',
		'delete_terms'      => 'edit_others_book-reviews',
		'manage_categories' => 'edit_book-reviews',
		'assign_terms'      => 'edit_book-reviews',
	];

	register_extended_taxonomy( $name, 'book-review', $args, [
		'singular' => $singular,
		'plural'   => $plural,
		'slug'     => $slug,
	] );
}

/**
 * Return an array of taxonomies that we want to register.
 *
 * @since  2.0.0-alpha
 * @return array Array of taxonomy names.
 */
function taxonomies( $tax = false ) {
	$taxonomies = [
		'genre' => [
			'singular'     => esc_html__( 'Genre', 'book-review-library' ),
			'slug'         => 'genre',
			'hierarchical' => false,
		],
		'book-author' => [
			'singular' => esc_html__( 'Author', 'book-review-library' ),
			'plural'   => esc_html__( 'Book Authors', 'book-review-library' ),
			'slug'     => 'book-author',
			'context'  => 'advanced',
		],
		'review-author' => [
			'singular'      => esc_html__( 'Review Author', 'book-review-library' ),
			'slug'          => 'review-author',
			'show_tagcloud' => false,
			'priority'      => 'low',
		],
		'reading-level' => [
			'singular'     => esc_html__( 'Reading Level', 'book-review-library' ),
			'slug'         => 'reading-level',
			'hierarchical' => false,
			'type'         => 'taxonomy_radio',
			'priority'     => 'low',
		],
		'subject' => [
			'singular'     => esc_html__( 'Subject', 'book-review-library' ),
			'name'         => esc_html__( 'Subjects', 'book-review-library' ),
			'slug'         => 'subject',
			'hierarchical' => false,
		],
		'illustrator' => [
			'singular' => esc_html__( 'Illustrator', 'book-review-library' ),
			'slug'     => 'illustrator',
			'context'  => 'advanced',
		],
		'awards' => [
			'singular'     => esc_html__( 'Award', 'book-review-library' ),
			'name'         => esc_html__( 'Awards', 'book-review-library' ),
			'slug'         => 'awards',
			'hierarchical' => false,
			'context'      => 'advanced',
			'priority'     => 'low',
		],
		'series' => [
			'singular' => esc_html__( 'Series', 'book-review-library' ),
			'plural'   => esc_html__( 'Series', 'book-review-library' ),
			'slug'     => 'series',
			'context'  => 'advanced',
		],
		'rating' => [
			'singular'          => esc_html__( 'Star Rating', 'book-review-library' ),
			'name'              => esc_html__( 'Rating', 'book-review-library' ),
			'slug'              => 'rating',
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'hierarchical'      => false,
			'type'              => 'taxonomy_select',
		],
		'language' => [
			'singular'     => esc_html__( 'Language', 'book-review-library' ),
			'slug'         => 'language',
			'hierarchical' => false,
			'type'         => 'taxonomy_select',
			'context'      => 'advanced',
		],
		'format' => [
			'singular'     => esc_html__( 'Format', 'book-review-library' ),
			'slug'         => 'format',
			'hierarchical' => false,
			'type'         => 'taxonomy_radio',
		],
		'publisher' => [
			'singular' => esc_html__( 'Publisher', 'book-review-library' ),
			'slug'     => 'publisher',
			'type'     => 'taxonomy_select',
			'context'  => 'advanced',
		],
	];

	if ( $tax && ! empty( $taxonomies[ $tax ] ) ) {
		return $taxonomies[ $tax ];
	}

	return $taxonomies;
}

/**
 * Register the genre taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_genre() {
	$genre = taxonomies( 'genre' );
	register_the_taxonomy( $genre );
}

/**
 * Add the Genre metabox.
 *
 * @since 2.0.0-alpha
 */
function add_cmb2_box_genre() {
	$genre = taxonomies( 'genre' );
	CMB2\add_cmb2_box( $genre );
}

/**
 * Register the review author taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_review_author() {
	$review_author = taxonomies( 'review-author' );
	register_the_taxonomy( $review_author );
}

function add_cmb2_box_review_author() {
	$review_author = taxonomies( 'review-author' );
	CMB2\add_cmb2_box( $review_author );
}

/**
 * Register the book author taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_book_author() {
	$book_author = taxonomies( 'book-author' );
	register_the_taxonomy( $book_author );
}

function add_cmb2_box_book_author() {
	CMB2\add_cmb2_box( taxonomies( 'book-author' ) );
}

/**
 * Register the reading level taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_reading_level() {
	$reading_level = taxonomies( 'reading-level' );
	register_the_taxonomy( $reading_level );
}

function add_cmb2_box_reading_level() {
	CMB2\add_cmb2_box( taxonomies( 'reading-level' ) );
}

/**
 * Register the subject taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_subject() {
	$subject = taxonomies( 'subject' );
	register_the_taxonomy( $subject );
}

function add_cmb2_box_subject() {
	CMB2\add_cmb2_box( taxonomies( 'subject' ) );
}

/**
 * Register the illustrator taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_illustrator() {
	$illustrator = taxonomies( 'illustrator' );
	register_the_taxonomy( $illustrator );
}

function add_cmb2_box_illustrator() {
	CMB2\add_cmb2_box( taxonomies( 'illustrator' ) );
}

/**
 * Register the awards taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_awards() {
	$awards = taxonomies( 'awards' );
	register_the_taxonomy( $awards );
}

function add_cmb2_box_awards() {
	CMB2\add_cmb2_box( taxonomies( 'awards' ) );
}

/**
 * Register the series taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_series() {
	$series = taxonomies( 'series' );
	register_the_taxonomy( $series );
}

function add_cmb2_box_series() {
	CMB2\add_cmb2_box( taxonomies( 'series' ) );
}

/**
 * Register the rating taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_rating() {
	$rating = taxonomies( 'rating' );
	register_the_taxonomy( $rating );
}

function add_cmb2_box_rating() {
	CMB2\add_cmb2_box( taxonomies( 'rating' ) );
}

/**
 * Register the language taxonomy
 *
 * @since 1.5.0
 * @todo Add action, add option
 */
function register_taxonomy_language() {
	$language = taxonomies( 'language' );
	register_the_taxonomy( $language );
}

function add_cmb2_box_language() {
	CMB2\add_cmb2_box( taxonomies( 'language' ) );
}

/**
 * Register the format taxonomy
 *
 * @since 1.5.0
 * @todo add action, option, cmb, default format terms
 */
function register_taxonomy_format() {
	$format = taxonomies( 'format' );
	register_the_taxonomy( $format );
}

function add_cmb2_box_format() {
	CMB2\add_cmb2_box( taxonomies( 'format' ) );
}

/**
 * Register the publisher taxonomy
 *
 * @since 1.5.0
 * @todo add action, option, cmb
 */
function register_taxonomy_publisher() {
	$publisher = taxonomies( 'publisher' );
	register_the_taxonomy( $publisher );
}

function add_cmb2_box_publisher() {
	CMB2\add_cmb2_box( taxonomies( 'publisher' ) );
}

/**
 * Inserts the rating levels
 *
 * @since 	1.0.0
 */
function insert_star_ratings() {
	wp_insert_term( '☆☆☆☆☆', 'rating', [
		'description' => esc_html__( 'Zero stars', 'book-review-library' ),
		'slug' => 'zero-stars',
	] );
	wp_insert_term( '★☆☆☆☆', 'rating', [
		'description' => esc_html__( 'One star', 'book-review-library' ),
		'slug' => 'one-star',
	] );
	wp_insert_term( '★★☆☆☆', 'rating', [
		'description' => esc_html__( 'Two stars', 'book-review-library' ),
		'slug' => 'two-stars',
	] );
	wp_insert_term( '★★★☆☆', 'rating', [
		'description' => esc_html__( 'Three stars', 'book-review-library' ),
		'slug' => 'three-stars',
	] );
	wp_insert_term( '★★★★☆', 'rating', [
		'description' => esc_html__( 'Four stars', 'book-review-library' ),
		'slug' => 'four-stars',
	] );
	wp_insert_term( '★★★★★', 'rating', [
		'description' => esc_html__( 'Five stars', 'book-review-library' ),
		'slug' => 'five-stars',
	] );

}

/**
 * Delete ratings
 *
 * @since 2.0.0-alpha
 */
function delete_ratings() {
	do_action( 'book_review_action_delete_ratings' );
	wp_delete_term( '☆☆☆☆☆', 'rating' );
	wp_delete_term( '★☆☆☆☆', 'rating' );
	wp_delete_term( '★★☆☆☆', 'rating' );
	wp_delete_term( '★★★☆☆', 'rating' );
	wp_delete_term( '★★★★☆', 'rating' );
	wp_delete_term( '★★★★★', 'rating' );
}

/**
 * Insert book formats
 *
 * @since 1.5.0
 */
function insert_formats() {
	wp_insert_term( 'Audiobook', 'format', [
		'description' => esc_html__( 'Books on tape, CD, Audible and the like', 'book-review-library' ),
		'slug'        => 'audiobook',
	] );
	wp_insert_term( 'Book', 'format', [
		'description' => esc_html__( 'The default format for book reviews', 'book-review-library' ),
		'slug'        => 'book',
	] );
	wp_insert_term( 'Graphic Novel', 'format', [
		'description' => esc_html__( 'Long form comic, manga, or other illustrated story', 'book-review-library' ),
		'slug'        => 'graphic-novel',
	] );
	wp_insert_term( 'eBook', 'format', [
		'description' => esc_html__( 'Any book in digital format', 'book-review-library' ),
		'slug'        => 'ebook',
	] );
	wp_insert_term( 'Periodical', 'format', [
		'description' => esc_html__( 'Magazine or newspaper published at regular intervals', 'book-review-library' ),
		'slug'        => 'periodical',
	] );
	wp_insert_term( 'Reference' , 'format', [
		'description' => esc_html__( 'Encyclopedia, dictionary or other nonfiction reference material', 'book-review-library' ),
		'slug'        => 'reference',
	] );
	wp_insert_term( 'Picture Book', 'format', [
		'description' => esc_html__( 'Any book -- generally a children\'s book -- in which pictures make up a large portion -- if not most -- of the book\'s content', 'book-review-library' ),
		'slug'        => 'picture-book',
	] );
}

/**
 * Removes rating submenu so rating levels cannot be (easily) changed from the default
 *
 * @since 	1.0.0
 */
function remove_rating_submenu() {
	remove_submenu_page( 'edit.php?post_type=book-review','edit-tags.php?taxonomy=rating&amp;post_type=book-review' );
}
