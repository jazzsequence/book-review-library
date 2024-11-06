<?php
/**
 * Sets up the options for admin.php
 *
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPL-3.0
 * @link      http://chrisreynolds.io
 * @copyright 2024 Chris Reynolds
 */

/**
 * Book Reviews Options class
 * Handles the options and options page markup
 *
 * @since 1.5.0
 */
class Book_Reviews_Options {

	/**
	 * Option key
	 *
	 * @var string
	 */
	private $key = 'book_reviews_settings';

	/**
	 * Array of metaboxes/fields
	 *
	 * @var string
	 */
	protected $option_metabox = [];

	/**
	 * Options Page Title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Default options
	 * 
	 * @var array
	 */
	protected $defaults = [];

	/**
	 * CMB2 fields
	 * 
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Screen ID
	 * 
	 * @var string
	 */
	protected $screen_id = 'book-review_page_book-review-library-options';

	/**
	 * Options Page hook
	 *
	 * @var string
	 */
	protected $options_page = '';

	public function __construct() {
		// set up our title
		$this->title = __( 'Book Review Library Options', 'book-review-library' );
		$this->defaults = $this->defaults();

		// set up the CMB2 fields
		$this->fields = [
			'review_authors' => [
				'name'    => __( 'Review Authors', 'book-review-library' ),
				'desc'    => __( 'Enable this if the person adding the book review is not the original author of the review.', 'book-review-library' ),
				'id'      => 'review-author',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['review-author'],
			],
			'reading_level' => [
				'name'    => __( 'Reading Level', 'book-review-library' ),
				'desc'    => __( 'Enable this to display the reading level for the book.', 'book-review-library' ),
				'id'      => 'reading-level',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['reading-level'],
			],
			'subject' => [
				'name'    => __( 'Subject', 'book-review-library' ),
				'desc'    => __( 'Enable this to tag the book with different subjects (unique from genres).', 'book-review-library' ),
				'id'      => 'subject',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['subject'],
			],
			'illustrator' => [
				'name'    => __( 'Illustrator', 'book-review-library' ),
				'desc'    => __( 'Enable this to add illustrators to book reviews.', 'book-review-library' ),
				'id'      => 'illustrator',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['illustrator'],
			],
			'awards' => [
				'name'    => __( 'Awards', 'book-review-library' ),
				'desc'    => __( 'Enable this to add awards the book has received.', 'book-review-library' ),
				'id'      => 'awards',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['awards'],
			],
			'series' => [
				'name'    => __( 'Series', 'book-review-library' ),
				'desc'    => __( 'Enable this to group books by series.', 'book-review-library' ),
				'id'      => 'series',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['series'],
			],
			'ratings' => [
				'name'    => __( 'Ratings', 'book-review-library' ),
				'desc'    => __( 'Enable this for star ratings.', 'book-review-library' ),
				'id'      => 'rating',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['rating'],
			],
			'languages' => [
				'name'    => __( 'Languages', 'book-review-library' ),
				'desc'    => __( 'When enabled, allow books to be grouped by language.', 'book-review-library' ),
				'id'      => 'languages',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['languages'],
			],
			'format' => [
				'name'    => __( 'Format', 'book-review-library' ),
				'desc'    => __( 'Group books by formats (eBook, audiobook, etc).', 'book-review-library' ),
				'id'      => 'format',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['format'],
			],
			'publisher' => [
				'name'    => __( 'Publisher', 'book-review-library' ),
				'desc'    => __( 'Group books by their publisher.', 'book-review-library' ),
				'id'      => 'publisher',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['publisher'],
			],
			'book_cover' => [
				'name'    => __( 'Book cover size', 'book-review-library' ),
				'desc'    => __( 'If covers are displayed, this controls how they are sized. Either uses the theme setting for thumbnails (which may be controlled by the theme or the Thumbnail setting on the Media Settings page) or a Book Review Library standard book cover size.', 'book-review-library' ),
				'id'      => 'thumbnail',
				'type'    => 'select',
				'options' => $this->book_covers(),
				'default' => $this->defaults['thumbnail'],
			],
			'stock' => [
				'name'    => __( 'Stock', 'book-review-library' ),
				'desc'    => __( 'Enable this to display "In Stock"/"Out of Stock" information with the book review.', 'book-review-library' ),
				'id'      => 'stock',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['stock'],
			],
			'author_image' => [
				'name'    => __( 'Author Image', 'book-review-library' ),
				'desc'    => __( 'Enable to allow uploads for an author image to display with the book review.', 'book-review-library' ),
				'id'      => 'author-image',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['author-image'],
			],
			'author_title' => [
				'name'    => __( 'Display author with title', 'book-review-library' ),
				'desc'    => sprintf( __( '%1$sWith the title%2$s displays the author on the same line as the book title.', 'book-review-library' ) . '<br />' . __( '%1$sWith the title but not hyperlinked%2$s displays the author on the same line as the book title but does not link the author name.', 'book-review-library' ) . '<br />' . __( '%1$sOn a new line%2$s adds a line break before displaying the author.', 'book-review-library' ) . '<br />' . __( '%1$sDisabled%2$s removes the author from the title entirely.', 'book-review-library' ), '<strong>', '</strong>' ),
				'id'      => 'title-filter',
				'type'    => 'select',
				'options' => $this->author_title(),
				'default' => $this->defaults['title-filter'],
			],
			'comments' => [
				'name'    => __( 'Comments on book reviews', 'book-review-library' ),
				'desc'    => __( 'If enabled, allows visitors to comment on book reviews.', 'book-review-library' ),
				'id'      => 'comments',
				'type'    => 'select',
				'options' => $this->true_false(),
				'default' => $this->defaults['comments'],
			],
		];
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 1.5.0
	 * @link  https://github.com/WebDevStudios/CMB2/wiki/Using-CMB-to-create-an-Admin-Theme-Options-Page
	 */
	public function hooks() {
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'admin_menu', [ $this, 'add_plugin_admin_menu' ] );
		add_action( 'cmb2_admin_init', [ $this, 'register_options' ] );	
		add_action( 'current_screen', [ $this, 'maybe_save' ] );
		add_filter( 'admin_footer', [ $this, 'maybe_hookup_fields' ], 2 );
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
			[ $this, 'admin_page_display' ]    // options page callback
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
		<div id="cmb2-options-page-<?php echo $this->key; ?>" class="wrap cmb2-options-page <?php echo $this->key; ?>" style="display:none">
			<?php cmb2_get_metabox( $this->metabox_id, $this->key, 'options-page' )->show_form(); ?>
		</div>
		<script type="text/javascript">
			var cmb2 = document.getElementById( 'cmb2-options-page-<?php echo $this->key; ?>' );
			var submit = document.getElementById( 'submit' ).parentNode;
			submit.parentNode.insertBefore( cmb2, submit );
			cmb2.style.display = '';
		</script>
		<?php
	}

	/**
	 * Defines the option metabox and field configuration
	 *
	 * @since  1.5.0
	 * @link   https://github.com/WebDevStudios/CMB2/wiki/Using-CMB-to-create-an-Admin-Theme-Options-Page
	 * @return array
	 */
	public function register_options() {
		$cmb = new_cmb2_box( [
			'id' => 'option_metabox',
			'hookup' => false,
			'object_types' => [ 'options-page' ],
		] );

		foreach ( $this->fields as $field ) {
			$cmb->add_field( $field );
		}
	}

	public function maybe_save() {
		if ( empty( $_POST ) ) {
			return;
		}

		$url = wp_get_referer();
		if ( false === strpos( $url, $this->key ) ) {
			wp_die( 'you came from the wrong place', 'wrong place', [ 'url' => $url ] );
		}

		add_filter( 'whitelist_options', [ $this, 'save_our_options' ] );
	}

	public function save_our_options( $whitelist_options ) {
		$cmb = cmb2_get_metabox( 'option_metabox', $this->key );
		if ( $cmb )  {
			$hookup = new CMB2_hookup( $cmb );;

			if ( $hookup->can_save( 'options-page' ) ) {
				$cmb->save_fields( $this->key, 'options-page', $_POST );
			}
		}

		remove_filter( 'whitelist_options', [ $this, 'save_our_options' ] );
		return $whitelist_options;
	}

	public function maybe_hookup_fields() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : (object) array( 'id' => null );
		// var_dump($screen->id);
		// Only show on our screen.
		if ( $this->screen_id !== $screen->id ) {
			return;
		}

		CMB2_hookup::enqueue_cmb_css();
		$this->admin_page_display();
	}

	/**
	 * Default option settings (moved from book_review_option_defaults)
	 *
	 * @since   1.0.0
	 * @return  $defaults   all the default settings (everything disabled)
	 */
	public function defaults() {
		$defaults = [
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
			'thumbnail'     => 'book-cover',
		];
		return $defaults;
	}

	/**
	 * Handles true/false settings
	 *
	 * @since  1.5.0
	 * @return array
	 */
	public function true_false() {
		return [
			true  => __( 'Enabled', 'book-review-library' ),
			false => __( 'Disabled', 'book-review-library' ),
		];
	}

	/**
	 * Handles book cover size settings
	 *
	 * @since  1.5.0
	 * @return array
	 */
	public function book_covers() {
		return [
			'thumbnail'  => __( 'Use the post thumbnail size', 'book-review-library' ),
			'book-cover'  => __( 'Use 6:9 book cover size (133px x 200px)', 'book-review-library' ),
		];
	}

	/**
	 * Handles author title settings
	 *
	 * @since  1.5.0
	 * @return array
	 */
	public function author_title() {
		return [
			'title'        => __( 'With the title', 'book-review-library' ),
			'title-nolink' => __( 'With the title but not hyperlinked', 'book-review-library' ),
			'newline'      => __( 'On a new line', 'book-review-library' ),
			'disabled'     => __( 'Disabled', 'book-review-library' ),
		];
	}
}
$Book_Reviews_Options = new Book_Reviews_Options();
$Book_Reviews_Options->hooks();
