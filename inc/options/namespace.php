<?php
/**
 * Sets up the options for admin.php
 *
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPL-3.0
 * @link      http://chrisreynolds.io
 * @copyright 2015 Chris Reynolds
 */

namespace BookReview\Options;

/**
 * Default option settings.
 *
 * @since 	1.0.0
 * @param   string $option A specific option to get the default for.
 * @return 	array          All the default settings (everything disabled) or the specific option default requested.
 */
function defaults( $option = '' ) {
	$options = [
		'review-author' => 0,
		'reading-level' => 0,
		'subject'       => 0,
		'illustrator'   => 0,
		'awards'        => 0,
		'series'        => 0,
		'rating'        => 0,
		'stock'         => 0,
		'roles'         => 0,
		'title-filter'  => 'title',
		'comments'      => 0,
		'author-image'  => 0,
		'languages'     => 0,
		'format'        => 0,
		'publisher'     => 0,
		'thumbnail'     => 'book-cover',
	];

	// If no specific option was passed, return all the default options.
	if ( '' === $option ) {
		return $options;
	}

	// If that option doesn't exist, return false.
	if ( ! isset( $options[ $option ] ) ) {
		return false;
	}

	return $options[ $option ];
}

/**
 * Set up the CMB2 fields.
 *
 * @since  1.5.0
 * @return array An array of CMB2 option fields.
 */
function cmb2_fields() {
	return [
		'review_authors' => [
			'name'    => esc_html__( 'Review Authors', 'book-review-library' ),
			'desc'    => esc_html__( 'Enable this if the person adding the book review is not the original author of the review.', 'book-review-library' ),
			'id'      => 'review-author',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'review-author' ),
		],
		'reading_level' => [
			'name'    => esc_html__( 'Reading Level', 'book-review-library' ),
			'desc'    => esc_html__( 'Enable this to display the reading level for the book.', 'book-review-library' ),
			'id'      => 'reading-level',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'reading-level' ),
		],
		'subject' => [
			'name'    => esc_html__( 'Subject', 'book-review-library' ),
			'desc'    => esc_html__( 'Enable this to tag the book with different subjects (unique from genres).', 'book-review-library' ),
			'id'      => 'subject',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'subject' ),
		],
		'illustrator' => [
			'name'    => esc_html__( 'Illustrator', 'book-review-library' ),
			'desc'    => esc_html__( 'Enable this to add illustrators to book reviews.', 'book-review-library' ),
			'id'      => 'illustrator',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'illustrator' ),
		],
		'awards' => [
			'name'    => esc_html__( 'Awards', 'book-review-library' ),
			'desc'    => esc_html__( 'Enable this to add awards the book has received.', 'book-review-library' ),
			'id'      => 'awards',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'awards' ),
		],
		'series' => [
			'name'    => esc_html__( 'Series', 'book-review-library' ),
			'desc'    => esc_html__( 'Enable this to group books by series.', 'book-review-library' ),
			'id'      => 'series',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'series' ),
		],
		'ratings' => [
			'name'    => esc_html__( 'Ratings', 'book-review-library' ),
			'desc'    => esc_html__( 'Enable this for star ratings.', 'book-review-library' ),
			'id'      => 'rating',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'rating' ),
		],
		'language' => [
			'name'    => esc_html__( 'Languages', 'book-review-library' ),
			'desc'    => esc_html__( 'When enabled, allow books to be grouped by language.', 'book-review-library' ),
			'id'      => 'language',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'languages' ),
		],
		'format' => [
			'name'    => esc_html__( 'Format', 'book-review-library' ),
			'desc'    => esc_html__( 'Group books by formats (eBook, audiobook, etc).', 'book-review-library' ),
			'id'      => 'format',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'format' ),
		],
		'publisher' => [
			'name'    => esc_html__( 'Publisher', 'book-review-library' ),
			'desc'    => esc_html__( 'Group books by their publisher.', 'book-review-library' ),
			'id'      => 'publisher',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'publisher' ),
		],
		'book_cover' => [
			'name'    => esc_html__( 'Book cover size', 'book-review-library' ),
			'desc'    => esc_html__( 'If covers are displayed, this controls how they are sized. Either uses the theme setting for thumbnails (which may be controlled by the theme or the Thumbnail setting on the Media Settings page) or a Book Review Library standard book cover size.', 'book-review-library' ),
			'id'      => 'thumbnail',
			'type'    => 'select',
			'options' => book_covers(),
			'default' => defaults( 'thumbnail' ),
		],
		'stock' => [
			'name'    => esc_html__( 'Stock', 'book-review-library' ),
			'desc'    => esc_html__( 'Enable this to display "In Stock"/"Out of Stock" information with the book review.', 'book-review-library' ),
			'id'      => 'stock',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'stock' ),
		],
		'author_image' => [
			'name'    => esc_html__( 'Author Image', 'book-review-library' ),
			'desc'    => esc_html__( 'Enable to allow uploads for an author image to display with the book review.', 'book-review-library' ),
			'id'      => 'author-image',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'author-image' ),
		],
		'author_title' => [
			'name'    => esc_html__( 'Display author with title', 'book-review-library' ),
			// Translators: %1$s is an opening <strong> tag. %2$s closes it.
			'desc'    => sprintf( esc_html__( '%1$sWith the title%2$s displays the author on the same line as the book title.', 'book-review-library' ) . '<br />' . esc_html__( '%1$sWith the title but not hyperlinked%2$s displays the author on the same line as the book title but does not link the author name.', 'book-review-library' ) . '<br />' . esc_html__( '%1$sOn a new line%2$s adds a line break before displaying the author.', 'book-review-library' ) . '<br />' . esc_html__( '%1$sDisabled%2$s removes the author from the title entirely.', 'book-review-library' ), '<strong>', '</strong>' ),
			'id'      => 'title-filter',
			'type'    => 'select',
			'options' => author_title(),
			'default' => defaults( 'title-filter' ),
		],
		'comments' => [
			'name'    => esc_html__( 'Comments on book reviews', 'book-review-library' ),
			'desc'    => esc_html__( 'If enabled, allows visitors to comment on book reviews.', 'book-review-library' ),
			'id'      => 'comments',
			'type'    => 'select',
			'options' => true_false(),
			'default' => defaults( 'comments' ),
		],
	];
}

/**
 * Register the setting to WP
 *
 * @since 1.5.0
 * @link  https://github.com/WebDevStudios/CMB2/wiki/Using-CMB-to-create-an-Admin-Theme-Options-Page
 */
function init() {
	register_setting( option_key(), option_key() );
}

/**
 * Register the administration menu for this plugin into the WordPress
 * Dashboard menu. (migrated from Book_Reviews)
 *
 * @since 0.1
 */
function add_plugin_admin_menu() {
	add_submenu_page(
		'edit.php?post_type=book-review',                                   // Parent menu.
		esc_html__( 'Book Review Library Options', 'book-review-library' ), // Page title.
		esc_html__( 'Options', 'book-review-library' ),                     // Menu title.
		'manage_book_review_options',                                       // Capability.
		'book-review-library-options',                                      // Page slug.
		__NAMESPACE__ . '\\admin_page_display'                              // Options page callback.
	);
}

/**
 * Returns the options key.
 *
 * @since  2.0.0-alpha
 * @return string      The option key for Book Review Library options.
 */
function option_key() {
	return 'book_reviews_settings';
}

/**
 * Defines the option metabox and field configuration
 *
 * @since  1.5.0
 * @link   https://github.com/WebDevStudios/CMB2/wiki/Using-CMB-to-create-an-Admin-Theme-Options-Page
 * @return array
 */
function option_metabox() {
	return [
		'id'         => 'option_metabox',
		'show_on'    => [
			'key'   => 'options-page',
			'value' => option_key(),
		],
		'show_names' => true,
		'fields'     => cmb2_fields(),
	];
}

/**
 * Admin page markup. Mostly handled by CMB2
 *
 * @since 1.5.0
 * @link  https://github.com/WebDevStudios/CMB2/wiki/Using-CMB-to-create-an-Admin-Theme-Options-Page
 */
function admin_page_display() {
	?>
	<div class="wrap cmb2_options_page <?php echo esc_html( option_key() ); ?>">
		<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
		<?php cmb2_metabox_form( option_metabox(), option_key() ); ?>
	</div>
	<?php
}

/**
 * Handles true/false settings
 *
 * @since  1.5.0
 * @return array
 */
function true_false() {
	return [
		true  => esc_html__( 'Enabled', 'book-review-library' ),
		false => esc_html__( 'Disabled', 'book-review-library' ),
	];
}

/**
 * Handles book cover size settings
 *
 * @since  1.5.0
 * @return array
 */
function book_covers() {
	return [
		'thumbnail'  => esc_html__( 'Use the post thumbnail size', 'book-review-library' ),
		'book-cover' => esc_html__( 'Use 6:9 book cover size (133px x 200px)', 'book-review-library' ),
	];
}

/**
 * Handles author title settings
 *
 * @since  1.5.0
 * @return array
 */
function author_title() {
	return [
		'title'        => esc_html__( 'With the title', 'book-review-library' ),
		'title-nolink' => esc_html__( 'With the title but not hyperlinked', 'book-review-library' ),
		'newline'      => esc_html__( 'On a new line', 'book-review-library' ),
		'disabled'     => esc_html__( 'Disabled', 'book-review-library' ),
	];
}

/**
 * Get Book Review Library options helper function
 *
 * @since 	1.5.0
 * @return  array 	The options array for Book Review Library
 */
function get_options() {
	// Get the options.
	return get_option( option_key(), defaults() );
}

/**
 * Check if a given option is enabled
 *
 * @since 	1.5.0
 * @param 	string $option_name The option name to check.
 * @return 	bool 	            True of the setting is enabled, false if it isn't or no option was passed
 */
function is_option_enabled( $option_name = '' ) {
	// Return false if nothing was passed.
	if ( '' == $option_name ) {
		return false;
	}

	// Get the options.
	$options = get_options();

	// If the options array isn't an array.
	if ( empty( $options ) ) {
		return false;
	}

	// If the option isn't set.
	if ( ! isset( $options[ $option_name ] ) ) {
		return false;
	}

	// If the option is true.
	if ( true == $options[ $option_name ] ) {
		return true;
	}

	// For anything else.
	return false;
}
