<?php
/**
 * Sets up the options for admin.php
 *
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPL-3.0
 * @link      http://chrisreynolds.io
 * @copyright 2013 Chris Reynolds
 */

/**
 * Book Reviews Options class
 * Handles the options and options page markup
 * @since 1.5.0
 */
class Book_Reviews_Options {

	/**
	 * Option key
	 * @var string
	 */
	private $key = 'book_reviews_settings';

	/**
	 * Array of metaboxes/fields
	 * @var string
	 */
	protected $option_metabox = array();

	/**
	 * Options Page Title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	public function __construct() {

		// set up our title
		$this->title = __( 'Book Review Library Options', 'book-review-library' );
		$this->defaults = $this->defaults();

		// set up the CMB2 fields
		$this->fields = array(
			'review_authors' => array(
				'name'    => __( 'Review Authors', 'book-review-library' ),
				'desc'    => __( 'Enable this if the person adding the book review is not the original author of the review.', 'book-review-library' ),
				'id'      => 'review-author',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['review-author']
			),
			'reading_level' => array(
				'name'    => __( 'Reading Level', 'book-review-library' ),
				'desc'    => __( 'Enable this to display the reading level for the book.', 'book-review-library' ),
				'id'      => 'reading-level',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['reading-level']
			),
			'subject' => array(
				'name'    => __( 'Subject', 'book-review-library' ),
				'desc'    => __( 'Enable this to tag the book with different subjects (unique from genres).', 'book-review-library' ),
				'id'      => 'subject',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['subject']
			),
			'illustrator' => array(
				'name'    => __( 'Illustrator', 'book-review-library' ),
				'desc'    => __( 'Enable this to add illustrators to book reviews.', 'book-review-library' ),
				'id'      => 'illustrator',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['illustrator']
			),
			'awards' => array(
				'name'    => __( 'Awards', 'book-review-library' ),
				'desc'    => __( 'Enable this to add awards the book has received.', 'book-review-library' ),
				'id'      => 'awards',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['awards']
			),
			'series' => array(
				'name'    => __( 'Series', 'book-review-library' ),
				'desc'    => __( 'Enable this to group books by series.', 'book-review-library' ),
				'id'      => 'series',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['series']
			),
			'ratings' => array(
				'name'    => __( 'Ratings', 'book-review-library' ),
				'desc'    => __( 'Enable this for star ratings.', 'book-review-library' ),
				'id'      => 'rating',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['rating']
			),
			'languages' => array(
				'name'    => __( 'Languages', 'book-review-library' ),
				'desc'    => __( 'When enabled, allow books to be grouped by language.', 'book-review-library' ),
				'id'      => 'languages',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['languages']
			),
			'format' => array(
				'name'    => __( 'Format', 'book-review-library' ),
				'desc'    => __( 'Group books by formats (eBook, audiobook, etc).', 'book-review-library' ),
				'id'      => 'format',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['format']
			),
			'publisher' => array(
				'name'    => __( 'Publisher', 'book-review-library' ),
				'desc'    => __( 'Group books by their publisher.', 'book-review-library' ),
				'id'      => 'publisher',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['publisher']
			),
			'book_cover' => array(
				'name'    => __( 'Book cover size', 'book-review-library' ),
				'desc'    => __( 'If covers are displayed, this controls how they are sized. Either uses the theme setting for thumbnails (which may be controlled by the theme or the Thumbnail setting on the Media Settings page) or a Book Review Library standard book cover size.', 'book-review-library' ),
				'id'      => 'thumbnail',
				'type'    => 'select',
				'options' => $this->book_covers(),
				'default' => $this->defaults['thumbnail']
			),
			'stock' => array(
				'name'    => __( 'Stock', 'book-review-library' ),
				'desc'    => __( 'Enable this to display "In Stock"/"Out of Stock" information with the book review.', 'book-review-library' ),
				'id'      => 'stock',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['stock']
			),
			'author_image' => array(
				'name'    => __( 'Author Image', 'book-review-library' ),
				'desc'    => __( 'Enable to allow uploads for an author image to display with the book review.', 'book-review-library' ),
				'id'      => 'author-image',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['author-image']
			),
			'author_title' => array(
				'name'    => __( 'Display author with title', 'book-review-library' ),
				'desc'    => sprintf( __( '%1$sWith the title%2$s displays the author on the same line as the book title.', 'book-review-library' ) . '<br />' . __( '%1$sWith the title but not hyperlinked%2$s displays the author on the same line as the book title but does not link the author name.', 'book-review-library' ) . '<br />' . __( '%1$sOn a new line%2$s adds a line break before displaying the author.', 'book-review-library' ) . '<br />' . __( '%1$sDisabled%2$s removes the author from the title entirely.', 'book-review-library' ), '<strong>', '</strong>' ),
				'id'      => 'title-filter',
				'type'    => 'select',
				'options' => $this->author_title(),
				'default' => $this->defaults['title-filter']
			),
			'comments' => array(
				'name'    => __( 'Comments on book reviews', 'book-review-library' ),
				'desc'    => __( 'If enabled, allows visitors to comment on book reviews.', 'book-review-library' ),
				'id'      => 'comments',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['comments']
			)
		);

	}

	/**
	 * Initiate our hooks
	 *
	 * @since 1.5.0
	 * @link  https://github.com/WebDevStudios/CMB2/wiki/Using-CMB-to-create-an-Admin-Theme-Options-Page
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
	}

	/**
	 * Register the setting to WP
	 *
	 * @since 1.5.0
	 * @link  https://github.com/WebDevStudios/CMB2/wiki/Using-CMB-to-create-an-Admin-Theme-Options-Page
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress
	 * Dashboard menu. (migrated from Book_Reviews)
	 *
	 * @since 0.1
	 */
	public function add_plugin_admin_menu() {
		$this->options_page = add_submenu_page(
			'edit.php?post_type=book-review',       // parent menu
			$this->title,                           // page title
			__( 'Options', 'book-review-library' ), // menu title
			'manage_book_review_options',           // capability
			'book-review-library-options',          // page slug
			array( $this, 'admin_page_display' )    // options page callback
		);
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 *
	 * @since 1.5.0
	 * @link  https://github.com/WebDevStudios/CMB2/wiki/Using-CMB-to-create-an-Admin-Theme-Options-Page
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2_options_page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->option_metabox(), $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Defines the option metabox and field configuration
	 *
	 * @since  1.5.0
	 * @link   https://github.com/WebDevStudios/CMB2/wiki/Using-CMB-to-create-an-Admin-Theme-Options-Page
	 * @return array
	 */
	public function option_metabox() {
		return array(
			'id'         => 'option_metabox',
			'show_on'    => array( 'key' => 'options-page', 'value' => $this->key ),
			'show_names' => true,
			'fields'     => $this->fields
		);
	}

	/**
	 * Default option settings (moved from book_review_option_defaults)
	 *
	 * @since 	1.0.0
	 * @return 	$defaults 	all the default settings (everything disabled)
	 */
	public function defaults() {
		$defaults = array(
			'review-author' => false,
			'reading-level' => false,
			'subject'       => false,
			'illustrator'   => false,
			'awards'        => false,
			'series'        => false,
			'rating'        => false,
			'stock'         => false,
			'roles'         => false,
			'title-filter'  => 'title',
			'comments'      => false,
			'author-image'  => false,
			'languages'     => false,
			'format'        => false,
			'publisher'     => false,
			'thumbnail'     => 'book-cover'
		);
		return $defaults;
	}

	/**
	 * Handles true/false settings
	 *
	 * @since  1.5.0
	 * @return array
	 */
	public function true_false() {
		return array(
			true  => __( 'Enabled', 'book-review-library' ),
			false => __( 'Disabled', 'book-review-library' )
		);
	}

	/**
	 * Handles book cover size settings
	 *
	 * @since  1.5.0
	 * @return array
	 */
	public function book_covers() {
		return array(
			'thumbnail'  => __( 'Use the post thumbnail size', 'book-review-library' ),
			'book-cover'  => __( 'Use 6:9 book cover size (133px x 200px)', 'book-review-library' )
		);
	}

	/**
	 * Handles author title settings
	 *
	 * @since  1.5.0
	 * @return array
	 */
	public function author_title() {
		return array(
			'title'        => __( 'With the title', 'book-review-library' ),
			'title-nolink' => __( 'With the title but not hyperlinked', 'book-review-library' ),
			'newline'      => __( 'On a new line', 'book-review-library' ),
			'disabled'     => __( 'Disabled', 'book-review-library' )
		);
	}

}
$Book_Reviews_Options = new Book_Reviews_Options();
$Book_Reviews_Options->hooks();

/**
 * Wrapper function for cmb2_get_option to get specified Book Review
 * setting
 *
 * @since  1.5.0
 * @link   https://github.com/WebDevStudios/CMB2/wiki/Using-CMB-to-create-an-Admin-Theme-Options-Page
 * @param  string $key 	Options array key
 * @return mixed 		Option value
 */
function book_review_get_option( $key = '' ) {
	global $Book_Reviews_Options;
	return cmb2_get_option( $Book_Reviews_Options->key, $key );
}

/**
 * Default option settings
 *
 * @since 	1.0.0
 *
 * @return 	$defaults 	all the default settings (everything disabled)
 */
function book_reviews_option_defaults() {
	global $Book_Reviews_Options;
	return $Book_Reviews_Options->defaults();
}