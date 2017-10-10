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

/**
 * Initialize the taxonomies.
 *
 * @since 2.0.0-alpha
 */
function bootstrap() {
	// Loop through and register all the taxonomies.
	foreach ( taxonnomies() as $taxonomy ) {
		$taxonomy = str_replace( '_', '-', $taxonomy );
		if ( ! book_reviews_is_option_enabled( $taxonomy ) ) {
			// Always-on taxonomies.
			if ( ! in_array( $taxonomy, [ 'genre', 'book-author' ] ) ) {
				return;
			}
			add_action( 'init', __NAMESPACE__ . '\\register_taxonnomy_' . str_replace( '-', '_', $taxonomy ) );
		}
	}

	// Insert rating values and prevent ratings from being edited.
	if ( book_reviews_is_option_enabled( 'rating' ) ) {
		add_action( 'init',       __NAMESPACE__ . '\\insert_star_ratings' );
		add_action( 'admin_init', __NAMESPACE__ . '\\remove_rating_submenu' );
	}

	// Insert formats.
	if ( book_reviews_is_option_enabled( 'format' ) ) {
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
	$name                      = isset( $args['name'] ) ? $args['name'] : $slug;
	$args['show_ui']           = isset( $args['show_ui'] ) ? $args['show_ui'] : true;
	$args['show_in_nav_menus'] = isset( $args['show_in_nav_menus'] ) ? $args['show_in_nav_menus'] : true;
	$args['tagcloud']          = isset( $args['show_tagcloud'] ) ? $args['show_tagcloud'] : true;
	$args['hierarchical']      = isset( $args['hierarchical'] ) ? $args['hierarchical'] : true;
	$args['dashboard_glance']  = isset( $args['dashboard_glance'] ) ? $args['dashboard_glance'] : true;
	$args['meta_box']          = isset( $args['meta_box'] ) ? $args['meta_box'] : false;
	$args['admin_cols']        = isset( $args['admin_cols'] ) ? $args['admin_cols'] : [];

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
function taxonomies() {
	return [
		'genre',
		'book-author',
		'review-author',
		'reading-level',
		'subject',
		'illustrator',
		'awards',
		'series',
		'rating',
		'language',
		'format',
		'publisher',
	];
}

/**
 * Register the genre taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_genre() {
	$args = [
		'singular'     => esc_html__( 'Genre', 'book-review-library' ),
		'plural'       => esc_html__( 'Genres', 'book-review-library' ),
		'slug'         => 'genre',
		'hierarchical' => false,
	];
	register_the_taxonomy( $args );
}


/**
 * Register the review author taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_review_author() {
	$args = [
		'singular'      => esc_html__( 'Review Author', 'book-review-library' ),
		'plural'        => esc_html__( 'Review Authors', 'book-review-library' ),
		'slug'          => 'review-author',
		'show_tagcloud' => false,
	];
	register_the_taxonomy( $args );
}


/**
 * Register the book author taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_book_author() {
	$args = [
		'singular' => esc_html__( 'Author', 'book-review-library' ),
		'plural'   => esc_html__( 'Book Authors', 'book-review-library' ),
		'slug'     => 'book-author',
	];
	register_the_taxonomy( $args );
}

/**
 * Register the reading level taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_reading_level() {
	$args = [
		'singular'     => esc_html__( 'Reading Level', 'book-review-library' ),
		'plural'       => esc_html__( 'Reading Levels', 'book-review-library' ),
		'slug'         => 'reading-level',
		'hierarchical' => false,
	];
	register_the_taxonomy( $args );
}

/**
 * Register the subject taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_subject() {
	$args = [
		'singular'     => esc_html__( 'Subject', 'book-review-library' ),
		'plural'       => esc_html__( 'Subjects', 'book-review-library' ),
		'slug'         => 'subject',
		'hierarchical' => false,
	];
	register_the_taxonomy( $args );
}

/**
 * Register the illustrator taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_illustrator() {
	$args = [
		'singular' => esc_html__( 'Illustrator', 'book-review-library' ),
		'plural'   => esc_html__( 'Illustrators', 'book-review-library' ),
		'slug'     => 'illustrator',
	];
	register_the_taxonomy( $args );
}

/**
 * Register the awards taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_awards() {
	$args = [
		'singular'     => esc_html__( 'Award', 'book-review-library' ),
		'plural'       => esc_html__( 'Awards', 'book-review-library' ),
		'slug'         => 'awards',
		'hierarchical' => false,
	];
	register_the_taxonomy( $args );
}

/**
 * Register the series taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_series() {
	$args = [
		'singular' => esc_html__( 'Series', 'book-review-library' ),
		'plural'   => esc_html__( 'Series', 'book-review-library' ),
		'slug'     => 'series',
	];
	register_the_taxonomy( $args );
}

/**
 * Register the rating taxonomy
 *
 * @since 	1.0.0
 */
function register_taxonomy_rating() {
	$args = [
		'singular'          => esc_html__( 'Star Rating', 'book-review-library' ),
		'plural'            => esc_html__( 'Star Ratings', 'book-review-library' ),
		'slug'              => 'rating',
		'show_in_nav_menus' => false,
		'show_tagcloud'     => false,
		'hierarchical'      => false,
	];
	register_the_taxonomy( $args );
}

/**
 * Register the language taxonomy
 *
 * @since 1.5.0
 * @todo Add action, add option
 */
function register_taxonomy_language() {
	$args = [
		'singular'     => esc_html__( 'Language', 'book-review-library' ),
		'plural'       => esc_html__( 'Languages', 'book-review-library' ),
		'slug'         => 'language',
		'hierarchical' => false,
	];
	register_the_taxonomy( $args );
}

/**
 * Register the format taxonomy
 *
 * @since 1.5.0
 * @todo add action, option, cmb, default format terms
 */
function register_taxonomy_format() {
	$args = [
		'singular'     => esc_html__( 'Format', 'book-review-library' ),
		'plural'       => esc_html__( 'Formats', 'book-review-library' ),
		'slug'         => 'format',
		'hierarchical' => false,
	];
	register_the_taxonomy( $args );
}

/**
 * Register the publisher taxonomy
 *
 * @since 1.5.0
 * @todo add action, option, cmb
 */
function register_taxonomy_publisher() {
	$args = [
		'singular' => esc_html__( 'Publisher', 'book-review-library' ),
		'plural'   => esc_html__( 'Publishers', 'book-review-library' ),
		'slug'     => 'publisher',
	];
	register_the_taxonomy( $args );
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
