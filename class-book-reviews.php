<?php
/**
 * Plugin Name.
 *
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPLv3
 * @link      http://chrisreynolds.io
 * @copyright 2014 Chris Reynolds
 */

/**
 * Plugin class.
 *
 * @package Book_Reviews
 * @author  Chris Reynolds <hello@chrisreynolds.io>
 */
class Book_Reviews {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = '1.4.10';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'book-review-library';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		include_once( 'views/actions.php' );

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
	 * Do i18n stuff
	 *
	 * @since 1.4
	 * @link http://ottopress.com/2013/language-packs-101-prepwork/
	 */
	public function setup_i18n() {
		load_plugin_textdomain( 'book-review-library', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		$result = add_role( 'librarian', __( 'Librarian', 'book-review-library' ), array(
			// core WordPress caps
			'read' => true,
			'delete_posts' => true,
			'delete_published_posts' => true,
			'edit_posts' => true,
			'edit_published_posts' => true,
			'publish_posts' => true,
			'upload_files' => true,
			'unfiltered_html' => true,
			'unfiltered_upload' => true,
			'manage_options' => true, // temporary fix for permissions to save book review options
			'manage_book_review_options' => true,
			// Book Review-speicifc caps
			'publish_book-reviews' => true,
			'edit_book-reviews' => true,
			'edit_published_book-reviews' => true,
			'delete_book-reviews' => true,
			'delete_published_book-reviews' => true,
			'read_book-reviews' => true,
			'edit_others_book-reviews' => true,
			'delete_others_book-reviews' => true,
		) );

		$result = add_role( 'book-reviewer', __( 'Book Reviewer', 'book-review-library' ), array(
			// core WordPress caps
			'read' => true,
			'delete_posts' => true,
			'delete_published_posts' => true,
			'edit_posts' => true,
			'edit_published_posts' => true,
			'publish_posts' => true,
			'upload_files' => true,
			'unfiltered_html' => true,
			'unfiltered_upload' => true,
			// Book Review-specific caps
			'publish_book-reviews' => true,
			'edit_book-reviews' => true,
			'edit_published_book-reviews' => true,
			'delete_book-reviews' => true,
			'delete_published_book-reviews' => true,
			'read_book-reviews' => true,
		) );

		// add book-reviews caps to authors
		if ( get_role( 'author' ) ) {
			$role = get_role( 'author' );
			$role->add_cap( 'add_book-reviews' );
			$role->add_cap( 'publish_book-reviews' );
			$role->add_cap( 'edit_book-reviews' );
			$role->add_cap( 'read_book-reviews' );
			$role->add_cap( 'edit_published_book-reviews' );
			$role->add_cap( 'delete_published_book-reviews' );
			$role->add_cap( 'delete_book-reviews' );
		}

		// add book-reviews caps to editors
		if ( get_role( 'editor' ) ) {
			$role = get_role( 'editor' );
			$role->add_cap( 'add_book-reviews' );
			$role->add_cap( 'publish_book-reviews' );
			$role->add_cap( 'edit_book-reviews' );
			$role->add_cap( 'edit_others_book-reviews' );
			$role->add_cap( 'read_book-reviews' );
			$role->add_cap( 'edit_published_book-reviews' );
			$role->add_cap( 'delete_published_book-reviews' );
			$role->add_cap( 'delete_book-reviews' );
		}

		// add book-reviews caps to admins
		if ( get_role( 'administrator' ) ) {
			$role = get_role( 'administrator' );
			$role->add_cap( 'add_book-reviews' );
			$role->add_cap( 'publish_book-reviews' );
			$role->add_cap( 'edit_book-reviews' );
			$role->add_cap( 'edit_others_book-reviews' );
			$role->add_cap( 'read_book-reviews' );
			$role->add_cap( 'edit_published_book-reviews' );
			$role->add_cap( 'delete_published_book-reviews' );
			$role->add_cap( 'delete_book-reviews' );
			$role->add_cap( 'manage_book_review_options' );
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		if ( get_role( 'librarian' ) ) {
			$role = get_role( 'librarian' );
			$role->remove_cap( 'delete_published_book-reviews' );
			$role->remove_cap( 'edit_published_book-reviews' );
			$role->remove_cap( 'publish_book-reviews' );
			$role->remove_cap( 'edit_book-reviews' );
			$role->remove_cap( 'delete_book-reviews' );
			$role->remove_cap( 'read_book-reviews' );
			$role->remove_cap( 'edit_others_book-reviews' );
			$role->remove_cap( 'edit_minute' );
			$role->remove_cap( 'manage_book_review_options' );
			$role->remove_cap( 'manage_options' );
			remove_role( 'librarian' );
		}

		if ( get_role( 'book-reviewer' ) ) {
			$role = get_role( 'book-reviewer' );
			$role->remove_cap( 'add_book-reviews' );
			$role->remove_cap( 'delete_published_book-reviews' );
			$role->remove_cap( 'edit_published_book-reviews' );
			$role->remove_cap( 'publish_book-reviews' );
			$role->remove_cap( 'edit_book-reviews' );
			$role->remove_cap( 'delete_book-reviews' );
			$role->remove_cap( 'read_book-reviews' );
			remove_role( 'book-reviewer' );
		}

		if ( get_role( 'author' ) ) {
			$role = get_role( 'author' );
			$role->remove_cap( 'add_agenda' );
			$role->remove_cap( 'publish_book-reviews' );
			$role->remove_cap( 'edit_book-reviews' );
			$role->remove_cap( 'read_book-reviews' );
			$role->remove_cap( 'edit_published_book-reviews' );
			$role->remove_cap( 'delete_published_book-reviews' );
			$role->remove_cap( 'delete_book-reviews' );
		}

		if ( get_role( 'editor' ) ) {
			$role = get_role( 'editor' );
			$role->remove_cap( 'add_agenda' );
			$role->remove_cap( 'add_book-reviews' );
			$role->remove_cap( 'publish_book-reviews' );
			$role->remove_cap( 'edit_book-reviews' );
			$role->remove_cap( 'edit_others_book-reviews' );
			$role->remove_cap( 'read_book-reviews' );
			$role->remove_cap( 'edit_published_book-reviews' );
			$role->remove_cap( 'delete_published_book-reviews' );
			$role->remove_cap( 'delete_book-reviews' );
		}

		if ( get_role( 'administrator' ) ) {
			$role = get_role( 'administrator' );
			$role->remove_cap( 'add_agenda' );
			$role->remove_cap( 'add_book-reviews' );
			$role->remove_cap( 'publish_book-reviews' );
			$role->remove_cap( 'edit_book-reviews' );
			$role->remove_cap( 'edit_others_book-reviews' );
			$role->remove_cap( 'read_book-reviews' );
			$role->remove_cap( 'edit_published_book-reviews' );
			$role->remove_cap( 'delete_published_book-reviews' );
			$role->remove_cap( 'delete_book-reviews' );
			$role->remove_cap( 'manage_book_review_options' );
		}

		wp_delete_term( '0', 'rating' );
		wp_delete_term( '1', 'rating' );
		wp_delete_term( '2', 'rating' );
		wp_delete_term( '3', 'rating' );
		wp_delete_term( '4', 'rating' );
		wp_delete_term( '5', 'rating' );
	}

	/**
	 * Remove the settings menu for librarians
	 *
	 * @since 1.4.6
	 *
	 * @todo Remove this when manage_book_review_options cap is working
	 */
	public function remove_menu_for_librarians() {
		$user = wp_get_current_user();

		if ( 'librarian' == $user->roles[0] ) { // if the current user is a librarian
			remove_menu_page( 'options-general.php' );
		}
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		wp_enqueue_style( 'genericons', plugins_url( 'genericons/genericons.css', __FILE__ ), array(), $this->version );
		wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( is_admin() && current_user_can( 'publish_book-reviews' ) ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
			wp_enqueue_script( 'media-upload' );
		}

	}

	/**
	 * Register and enqueue genericons style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $post;
		if ( ! is_admin() && ( 'book-review' == get_post_type() || ( is_page() && has_shortcode( $post->post_content, 'book-reviews' ) ) ) ) {
			wp_enqueue_style( $this->plugin_slug . '-genericons', plugins_url( 'genericons/genericons.css', __FILE__ ), array(), $this->version );
			wp_enqueue_style( $this->plugin_slug . '-public', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since   0.1
	 */
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_submenu_page(
			'edit.php?post_type=book-review',
			__( 'Book Reviews Options', 'book-review-library' ),
			__( 'Options', 'book-review-library' ),
			'manage_book_review_options',
			$this->plugin_slug . '-options',
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since   1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Flush rewrite rules
	 *
	 * @since 1.4.3
	 */
	public function flush_rewrites() {
		flush_rewrite_rules();
	}

	/**
	 * Register the book review post type
	 *
	 * @since 	1.0.0
	 */
	public function register_post_type_book_review() {
		include_once( BOOK_REVIEWS_FUNC );
		$defaults = book_reviews_option_defaults();
		$options = get_option( 'book_reviews_settings', $defaults );
		if ( isset( $options['comments'] ) && $options['comments'] ) {
			$supports = array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'comments' );
		} else {
			$supports = array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions' );
		}

		$capabilities = array(
			'publish_posts' => 'publish_book-reviews',
			'edit_posts' => 'edit_book-reviews',
			'edit_others_posts' => 'edit_others_book-reviews',
			'delete_posts' => 'delete_book-reviews',
			'edit_post' => 'edit_book-review',
			'delete_post' => 'delete_book-review',
			'read_post' => 'read_book-review',
		);
		$labels = array(
				'name' => __( 'Book Reviews', 'book-review-library' ),
				'singular_name' => __( 'Book Review', 'book-review-library' ),
				'add_new' => __( 'Add New', 'book-review-library' ),
				'add_new_item' => __( 'Add New Book Review', 'book-review-library' ),
				'edit_item' => __( 'Edit Review', 'book-review-library' ),
				'new_item' => __( 'New Book Review', 'book-review-library' ),
				'view_item' => __( 'View Book Review', 'book-review-library' ),
				'search_items' => __( 'Search Book Reviews', 'book-review-library' ),
				'not_found' => __( 'No book reviews found', 'book-review-library' ),
				'not_found_in_trash' => __( 'No book reviews found in Trash', 'book-review-library' ),
				'menu_name' => __( 'Book Reviews', 'book-review-library' ),
		);
		$args = array(
		'labels' => $labels,
			'hierarchical' => false,
			'description' => 'Book Review',
			'supports' => $supports,
			'taxonomies' => array( 'genre', 'review-author' ),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 20,
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'book-review',
			'capabilities' => $capabilities,
			'map_meta_cap' => true,
		);
		register_post_type( 'book-review', $args );

	}

	/**
	 * Register the genre taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_genre() {
		register_taxonomy('genre', array( 'book-review' ), array(
			'label' => __( 'Genres', 'book-review-library' ),
			'labels' => array(
				'name' => __( 'Genres', 'book-review-library' ),
				'singular_name' => __( 'Genre', 'book-review-library' ),
				'search_items' => __( 'Search Genres', 'book-review-library' ),
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
				'assign_terms' => 'edit_book-reviews',
			),
		));
	}


	/**
	 * Register the review author taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_review_author() {
		register_taxonomy('review-author', array( 'book-review' ), array(
			'label' => __( 'Review Author', 'book-review-library' ),
			'labels' => array(
				'name' => __( 'Review Author', 'book-review-library' ),
				'singular_name' => __( 'Review Author', 'book-review-library' ),
				'search_items' => __( 'Search Review Authors', 'book-review-library' ),
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
				'assign_terms' => 'edit_book-reviews',
			),
		));
	}


	/**
	 * Register the book author taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_book_author() {
		register_taxonomy('book-author', array( 'book-review' ), array(
			'label' => __( 'Book Authors', 'book-review-library' ),
			'labels' => array(
				'name' => __( 'Book Authors', 'book-review-library' ),
				'singular_name' => __( 'Author', 'book-review-library' ),
				'search_items' => __( 'Search Book Authors', 'book-review-library' ),
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
				'assign_terms' => 'edit_book-reviews',
			),
		));
	}

	/**
	 * Register the reading level taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_reading_level() {
		register_taxonomy('reading-level', array( 'book-review' ), array(
			'label' => __( 'Reading Level', 'book-review-library' ),
			'labels' => array(
				'name' => __( 'Reading Level', 'book-review-library' ),
				'singular_name' => __( 'Reading Level', 'book-review-library' ),
				'search_items' => __( 'Search Reading Levels', 'book-review-library' ),
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
				'assign_terms' => 'edit_book-reviews',
			),
		));
	}

	/**
	 * Register the subject taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_subject() {
		register_taxonomy('subject', array( 'book-review' ), array(
			'label' => __( 'Subjects', 'book-review-library' ),
			'labels' => array(
				'name' => __( 'Subjects', 'book-review-library' ),
				'singular_name' => __( 'Subject', 'book-review-library' ),
				'search_items' => __( 'Search Subjects', 'book-review-library' ),
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
				'assign_terms' => 'edit_book-reviews',
			),
		));
	}

	/**
	 * Register the illustrator taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_illustrator() {
		register_taxonomy('illustrator', array( 'book-review' ), array(
			'label' => __( 'Illustrators', 'book-review-library' ),
			'labels' => array(
				'name' => __( 'Illustrators', 'book-review-library' ),
				'singular_name' => __( 'Illustrator', 'book-review-library' ),
				'search_items' => __( 'Search Illustrators', 'book-review-library' ),
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
				'assign_terms' => 'edit_book-reviews',
			),
		));
	}

	/**
	 * Register the awards taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_awards() {
		register_taxonomy('awards', array( 'book-review' ), array(
			'label' => __( 'Awards', 'book-review-library' ),
			'labels' => array(
				'name' => __( 'Awards', 'book-review-library' ),
				'singular_name' => __( 'Award', 'book-review-library' ),
				'search_items' => __( 'Search Awards', 'book-review-library' ),
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
				'assign_terms' => 'edit_book-reviews',
			),
		));
	}

	/**
	 * Register the series taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_series() {
		register_taxonomy('series', array( 'book-review' ), array(
			'label' => __( 'Series', 'book-review-library' ),
			'labels' => array(
				'name' => __( 'Series', 'book-review-library' ),
				'singular_name' => __( 'Series', 'book-review-library' ),
				'search_items' => __( 'Search Series', 'book-review-library' ),
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
				'assign_terms' => 'edit_book-reviews',
			),
		));
	}

	/**
	 * Register the rating taxonomy
	 *
	 * @since 	1.0.0
	 */
	public function register_taxonomy_rating() {
		register_taxonomy('rating', array( 'book-review' ), array(
			'label' => __( 'Star Ratings', 'book-review-library' ),
			'labels' => array(
				'name' => __( 'Star Ratings', 'book-review-library' ),
				'singular_name' => __( 'Star', 'book-review-library' ),
				'search_items' => __( 'Search Ratings', 'book-review-library' ),
				'popular_items' => __( 'Popular Ratings', 'book-review-library' ),
				'all_items' => __( 'Stars', 'book-review-library' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( 'Edit Rating', 'book-review-library' ),
				'update_item' => __( 'Update Rating', 'book-review-library' ),
				'add_new_item' => __( 'Add New Rating', 'book-review-library' ),
				'new_item_name' => __( 'New Rating Name', 'book-review-library' ),
				'separate_items_with_commas' => __( 'Separate Star Ratings with commas', 'book-review-library' ),
				'add_or_remove_items' => __( 'Add or remove Star Ratings', 'book-review-library' ),
				'choose_from_most_used' => __( 'Choose from the most used Star Ratings', 'book-review-library' ),
				'menu_name' => __( 'Star Ratings', 'book-review-library' ),
			),
			'public' => true,
			'show_in_nav_menus' => false,
			'show_ui' => true,
			'show_tagcloud' => false,
			'hierarchical' => true,
			'update_count_callback' => '',
			'query_var' => 'rating',
			'rewrite' => array(
				'slug' => 'rating',
				'with_front' => true,
				'hierarchical' => false,
			),
			'capabilities' => array(
				'manage_terms' => 'edit_book-reviews',
				'edit_terms' => 'edit_book-reviews',
				'delete_terms' => 'edit_others_book-reviews',
				'manage_categories' => 'edit_book-reviews',
				'assign_terms' => 'edit_book-reviews',
			),
		));
	}

	/**
	 * Inserts the rating levels
	 *
	 * @since 	1.0.0
	 */
	public function insert_star_ratings() {
		wp_insert_term( '0', 'rating', array(
			'description' => __( 'Zero stars', 'book-review-library' ),
			'slug' => 'zero-stars',
		) );
		wp_insert_term( '1', 'rating', array(
			'description' => __( 'One star', 'book-review-library' ),
			'slug' => 'one-star',
		) );
		wp_insert_term( '2', 'rating', array(
			'description' => __( 'Two stars', 'book-review-library' ),
			'slug' => 'two-stars',
		) );
		wp_insert_term( '3', 'rating', array(
			'description' => __( 'Three stars', 'book-review-library' ),
			'slug' => 'three-stars',
		) );
		wp_insert_term( '4', 'rating', array(
			'description' => __( 'Four stars', 'book-review-library' ),
			'slug' => 'four-stars',
		) );
		wp_insert_term( '5', 'rating', array(
			'description' => __( 'Five stars', 'book-review-library' ),
			'slug' => 'five-stars',
		) );
	}

	/**
	 * Removes rating submenu so rating levels cannot be (easily) changed from the default
	 *
	 * @since 	1.0.0
	 */
	public function remove_rating_submenu() {
		remove_submenu_page( 'edit.php?post_type=book-review','edit-tags.php?taxonomy=rating&amp;post_type=book-review' );
	}

	/**
	 * Moves the taxonomy meta boxes around and modifies the featured image text
	 *
	 * @since 	1.0.0
	 */
	public function move_meta_boxes() {
		global $wp_meta_boxes;

		$screen = get_current_screen();
		if ( 'book-review' != $screen->post_type ) {
			return;
		} else {

			include_once( BOOK_REVIEWS_FUNC );

			$options = get_option( 'book_reviews_settings', book_reviews_option_defaults() );

			unset( $wp_meta_boxes['book-review']['normal']['core']['authordiv'] );

			unset( $wp_meta_boxes['book-review']['side']['core']['book-authordiv'] );
			add_meta_box( 'book-authordiv', __( 'Book Author', 'book-review-library' ), 'post_categories_meta_box', 'book-review', 'normal', 'core', array( 'taxonomy' => 'book-author' ) );

			if ( isset( $options['illustrator'] ) && ($options['illustrator'] == true) ) {
				unset( $wp_meta_boxes['book-review']['side']['core']['illustratordiv'] );
				add_meta_box( 'illustratordiv', __( 'Illustrator', 'book-review-library' ), 'post_categories_meta_box', 'book-review', 'normal', 'core', array( 'taxonomy' => 'illustrator' ) );
			}

			if ( isset( $options['series'] ) && ($options['series'] == true) ) {
				unset( $wp_meta_boxes['book-review']['side']['core']['seriesdiv'] );
				add_meta_box( 'seriesdiv', __( 'Series', 'book-review-library' ), 'post_categories_meta_box', 'book-review', 'normal', 'core', array( 'taxonomy' => 'series' ) );
			}

			if ( isset( $options['reading-level'] ) && ($options['reading-level'] == true) ) {
				unset( $wp_meta_boxes['book-review']['side']['core']['tagsdiv-reading-level'] );
				add_meta_box( 'tagsdiv-reading-level', __( 'Reading Level', 'book-review-library' ), 'post_tags_meta_box', 'book-review', 'normal', 'core', array( 'taxonomy' => 'reading-level' ) );
			}

			if ( isset( $options['rating'] ) && ($options['rating'] == true) ) {
				unset( $wp_meta_boxes['book-review']['side']['core']['ratingdiv'] );
				add_meta_box( 'ratingdiv', __( 'Star Rating', 'book-review-library' ), 'post_categories_meta_box', 'book-review', 'normal', 'core', array( 'taxonomy' => 'rating' ) );
			}

			remove_meta_box( 'postimagediv', 'book-review', 'side' );
	    	add_meta_box( 'postimagediv', __( 'Book Cover', 'book-review-library' ), 'post_thumbnail_meta_box', 'book-review', 'side', 'default' );
	    }
	}

	/**
	 * Adds the Additional Information meta box for book review posts
	 *
	 * @since 	1.0.0
	 */
	public function book_reviews_meta_box() {
		add_meta_box( 'book-reviews-meta', __( 'Additional Information', 'book-review-library' ), array( $this, 'book_reviews_box' ), 'book-review', 'normal', 'default' );
	}

	/**
	 * Renters the actual content of the Additional Information meta box
	 *
	 * @since 	1.0.0
	 */
	public function book_reviews_box() {
		global $post;

		include_once( BOOK_REVIEWS_FUNC );

		$options = get_option( 'book_reviews_settings', book_reviews_option_defaults() );

		echo '<input type="hidden" name="noncename" id="noncename" value="' .
		wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';

		echo '<div class="isbn-meta">';
		echo '<label for="isbn"><strong>' . __( 'ISBN:', 'book-review-library' ) . '</strong></label><br />';
		echo '<input class="widefat" id="isbn" name="isbn" value="' . wp_strip_all_tags( get_post_meta( $post->ID, 'isbn', true ), true ) . '" type="text" />';
		echo '</div>';

		if ( isset( $options['stock'] ) && ($options['stock'] == true) ) {
			echo '<div class="in-stock-box">';
			echo '<label for="in-stock"><strong>' . __( 'In stock?', 'book-review-library' ) . '</strong></label><br />';
			echo '<select name="book_in_stock">';
			$selected = get_post_meta( $post->ID, 'book_in_stock', true );
			echo '<option value="1" ' . selected( $selected, 1 ) . '>' . __( 'Book is in stock', 'book-review-library' ) . '</option>';
			echo '<option value="0" ' . selected( $selected, 0 ) . '>' . __( 'Book out of stock', 'book-review-library' ) . '</option>';
			echo '</select>';
			echo '</div>';
		}

		if ( isset( $options['awards'] ) && ($options['awards'] == true) ) {
			echo '<div class="award-image-upload">';
			echo '<label for-"award-image-upload"><strong>' . __( 'Upload Award Image', 'book-review-library' ) . '</strong></label><br />';
			echo '<input style="width: 55%;" id="award_image" class="award_image" name="award_image" value="' . get_post_meta( $post->ID, 'award_image', true ) . '" type="text" /> <input id="upload_file_image_button" type="button" class="upload_button button button-primary" value="Upload Image" />';
			echo '</div>';
		}
	}

		/**
		 * Registers the options
		 *
		 * @since 	1.0.0
		 */
	public function settings_init() {
		register_setting( 'book_reviews_settings', 'book_reviews_settings' );
	}

	/**
	 * Filter for the featured image post box
	 *
	 * @since 	1.0.0
	 */
	public function change_thumbnail_html( $content ) {
	    if ( 'book-review' == $GLOBALS['post_type'] ) {
			add_filter( 'admin_post_thumbnail_html', array( $this, 'do_thumb' ) ); }
	}

	/**
	 * Replaces "Set featured image" with "Select Book Cover"
	 *
	 * @since 	1.0.0
	 *
	 * @return 	string 	returns the modified text
	 */
	public function do_thumb( $content ) {
		 return str_replace( __( 'Set featured image' ), __( 'Select Book Cover', 'book-review-library' ),$content );
	}

	/**
	 * Creates new columns for the Book Reviews dashboard page
	 *
	 * @since 	1.0.0
	 *
	 * @return 	$columns
	 */
	public function edit_book_review_columns( $columns ) {
		$default_columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title', 'book-review-library' ),
			'book_author' => __( 'Author', 'book-review-library' ),
			'genre' => __( 'Genre', 'book-review-library' ),
		);
		$series_column = array();
		if ( taxonomy_exists( 'series' ) ) {
			$series_column = array( 'series' => __( 'Series', 'book-review-library' ) );
		}
		$illustrator_column = array();
		if ( taxonomy_exists( 'illustrator' ) ) {
			$illustrator_column = array( 'illustrator' => __( 'Illustrator', 'book-review-library' ) );
		}
		$subject_column = array();
		if ( taxonomy_exists( 'subject' ) ) {
			$subject_column = array( 'subjects' => __( 'Subjects', 'book-review-library' ) );
		}
		$reading_level_column = array();
		if ( taxonomy_exists( 'reading-level' ) ) {
			$reading_level_column = array( 'reading_level' => __( 'Reading Level', 'book-review-library' ) );
		}
		$awards_column = array();
		if ( taxonomy_exists( 'awards' ) ) {
			$awards_column = array( 'awards' => __( 'Awards', 'book-review-library' ) );
		}
		$rating_column = array();
		if ( taxonomy_exists( 'rating' ) ) {
			$rating_column = array( 'rating' => __( 'Rating', 'book-review-library' ) );
		}
		$columns = array_merge( $default_columns, $series_column, $illustrator_column, $subject_column, $reading_level_column, $awards_column, $rating_column );

		return $columns;
	}

	/**
	 * Renders new data for the new columns
	 *
	 * @since 	1.0.0
	 */
	public function manage_book_review_columns( $column, $post_id ) {
		global $post;

		switch ( $column ) {

			// if displaying the book author column
			case 'book_author' :
				// get the author(s) for the book
				$terms = get_the_terms( $post_id, 'book-author' );

				// if terms were found
				if ( ! empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'book-author' => $term->slug ), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'book-author', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				} // if no terms are found, say something
				else {
					_e( 'No authors found', 'book-review-library' );
				}
				break;

			// if displaying the genre column
			case 'genre' :
				// get the genre(s) for the book
				$terms = get_the_terms( $post_id, 'genre' );

				// if terms were found
				if ( ! empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'genre' => $term->slug ), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'genre', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				} // if no terms are found, say something
				else {
					_e( 'No genres found', 'book-review-library' );
				}
				break;

			// if displaying the series column
			case 'series' :
				// get the series(s) for the book
				$terms = get_the_terms( $post_id, 'series' );

				// if terms were found
				if ( ! empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'series' => $term->slug ), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'series', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				} // if no terms are found, say something
				else {
					_e( 'No series found', 'book-review-library' );
				}
				break;

			// if displaying the illustrator column
			case 'illustrator' :
				// get the illustrator(s) for the book
				$terms = get_the_terms( $post_id, 'illustrator' );

				// if terms were found
				if ( ! empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'illustrator' => $term->slug ), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'illustrator', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				} // if no terms are found, say something
				else {
					_e( 'No illustrators found', 'book-review-library' );
				}
				break;

			// if displaying the subjects column
			case 'subjects' :
				// get the subjects for the book
				$terms = get_the_terms( $post_id, 'subject' );

				// if terms were found
				if ( ! empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'subject' => $term->slug ), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'subject', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				} // if no terms are found, say something
				else {
					_e( 'No subjects found', 'book-review-library' );
				}
				break;

			// if displaying the reading level column
			case 'reading_level' :
				// get the reading level for the book
				$terms = get_the_terms( $post_id, 'reading-level' );

				// if terms were found
				if ( ! empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'reading-level' => $term->slug ), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'reading-level', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				} // if no terms are found, say something
				else {
					_e( 'No reading level found', 'book-review-library' );
				}
				break;

			// if displaying the awards column
			case 'awards' :
				// get the awards for the book
				$terms = get_the_terms( $post_id, 'awards' );

				// if terms were found
				if ( ! empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'awards' => $term->slug ), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'awards', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				} // if no terms are found, say something
				else {
					_e( 'No awards found', 'book-review-library' );
				}
				break;

			// if displaying the rating column
			case 'rating' :
				// get the book review rating
				$terms = get_the_terms( $post_id, 'rating' );

				// if terms were found
				if ( ! empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach ( $terms as $term ) {
						if ( $term->name == '5' ) {
							$rating = '<div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div>';
						} elseif ( $term->name == '4' ) {
							$rating = '<div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div>';
						} elseif ( $term->name == '3' ) {
							$rating = '<div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div>';
						} elseif ( $term->name == '2' ) {
							$rating = '<div class="genericon genericon-star"></div><div class="genericon genericon-star"></div>';
						} elseif ( $term->name == '1' ) {
							$rating = '<div class="genericon genericon-star"></div>';
						} else {
							$rating = _e( 'No rating found', 'book-review-library' );
						}
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'rating' => $term->slug ), 'edit.php' ) ),
							$rating
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				} // if no terms are found, say something
				else {
					_e( 'No rating found', 'book-review-library' );
				}
				break;

			// just break out of the switch statement for everything else
			default :
				break;
		}
	}

	/**
	 * Adds a filter on the search to allow searching by ISBN
	 *
	 * @since 1.4
	 * @link http://wordpress.org/support/topic/include-custom-field-values-in-search?replies=16#post-1932930
	 * @link http://www.devblog.fr/en/2013/09/05/modifying-wordpress-search-query-to-include-taxonomy-and-meta/
	 */
	public function search_by_isbn( $where ) {
		// load the meta keys into an array
		$keys = array( 'isbn' ); // currently we're just using one, but we can expand this later
		if ( is_search() && ! is_admin() ) {
			global $wpdb;
			$query = get_search_query();
			$query = like_escape( $query );

			// include postmeta in search
			foreach ( $keys as $key ) {
			 	$where .= " OR {$wpdb->posts}.ID IN (SELECT {$wpdb->postmeta}.post_id FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->postmeta}.meta_key = '$key' AND {$wpdb->postmeta}.meta_value LIKE '%$query%' AND {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)";
			}
			 // include taxonomy in search
			$where .= " OR {$wpdb->posts}.ID IN (SELECT {$wpdb->posts}.ID FROM {$wpdb->posts},{$wpdb->term_relationships},{$wpdb->terms} WHERE {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id AND {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->terms}.term_id AND {$wpdb->terms}.name LIKE '%$query%')";

		}
		return $where;
	}

	/**
	 * Saves the book-review post meta data
	 *
	 * @since 	1.0.0
	 */
	public function save_book_review_postdata( $post_id, $post ) {
		$nonce = isset( $_POST['noncename'] ) ? $_POST['noncename'] : 'all the hosts, dream and blue';
		if ( ! wp_verify_nonce( $nonce, plugin_basename( __FILE__ ) ) ) {
			return $post->ID;
		}
		/* confirm user is allowed to save page/post */
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post->ID ) ) {
				return $post->ID; }
		} else {
			if ( ! current_user_can( 'edit_post', $post->ID ) ) {
				return $post->ID; }
		}

		/* ready our data for storage */
		$meta_keys = array( 'award_image' => 'text', 'book_in_stock' => 'text', 'isbn' => 'text' );

		/* Add values of $mydata as custom fields */
		foreach ( $meta_keys as $meta_key => $type ) {
			if ( $post->post_type == 'revision' ) {
				return; }
			if ( isset( $_POST[ $meta_key ] ) ) {
				if ( $type == 'text' ) {
					$value = wp_kses_post( $_POST[ $meta_key ] );
				}

				update_post_meta( $post->ID, $meta_key, $value );
			} else {
				delete_post_meta( $post->ID, $meta_key );
			}
		}
	}

	/**
	 * Registers the widget
	 *
	 * @since 	1.0.0
	 */
	public function register_book_review_widget() {
		include_once( BOOK_REVIEWS_WIDGETS );
		register_widget( 'Book_Review_Widget' );
		register_widget( 'Book_Review_Recent_Widget' );
	}

	/**
	 * Adds a new image size (for the widget)
	 *
	 * @since 	1.0.0
	 */
	public function create_tiny_thumbs() {
		if ( function_exists( 'add_image_size' ) ) {
			add_image_size( 'tiny', 36, 36, true );
			add_image_size( 'book-cover', 133, 200, false );
		}
	}

	/**
	 * Creates the shortcode
	 *
	 * @since 	1.0.0
	 */
	public function create_shortcode( $atts ) {
		global $is_book_review_shortcode;

		$is_book_review_shortcode = true;

		include_once( BOOK_REVIEWS_FUNC );
		$defaults = book_reviews_option_defaults();
		$options = get_option( 'book_reviews_settings', $defaults );

		extract(shortcode_atts( array(
			'count' => '',
			'covers' => true,
			'order_by' => 'date_added', // author, title, date added (default)
			'format' => 'none', // 0 = none, 1 = excerpt, 2 = full
			'author' => '', // any author
			'genre' => '',// any genre
		), $atts ));

		$covers = null;
		$orderby_author = null;
		$author = null;
		$genre = null;

		if ( isset( $atts['count'] ) ) {
			$count = $atts['count'];
		} else {
			$count = -1;
		}
		if ( isset( $atts['covers'] ) && 'true' == $atts['covers'] ) {
			$covers = true;
		} else {
			$covers = false;
		}
		if ( isset( $atts['order_by'] ) ) {
			$order_by = $atts['order_by'];
			switch ( $order_by ) {
				case 'date_added' :
					$orderby = 'date';
					$order = 'DESC';
					break;
				case 'author' :
					$terms = get_terms( 'book-author' );
					$orderby_author = true;
					$orderby = 'title';
					$order = 'ASC';
					break;
				case 'title' :
					$orderby = 'title';
					$order = 'ASC';
					break;
				default :
					$orderby = 'date';
					$order = 'DESC';
					break;
			}
		} else {
			$orderby = 'date';
			$order = 'DESC';
		}
		if ( isset( $atts['format'] ) ) {
			$format = 0;
			if ( 'excerpt' == $atts['format'] ) {
				$format = 1;
			}
			if ( 'full' == $atts['format'] ) {
				$format = 2;
			}
		}
		if ( isset( $atts['author'] ) ) {
			$author = sanitize_title( $atts['author'] ); // sanitize the title in case someone didn't remember to do that
		}

		if ( isset( $atts['genre'] ) ) {
			$genre = sanitize_title( $atts['genre'] ); // sanitize the genre in case someone didn't remember to do that
		}

		if ( ! $orderby_author ) { // if we're not ordering by author, do things normally
			if ( ! $author && ! $genre ) { // we are not listing books of a specific author or a specific genre
				$args = array(
					'post_type' => 'book-review',
					'posts_per_page' => $count,
					'orderby' => $orderby,
					'order' => $order,
				);
			} elseif ( $author && ! $genre ) { // we're listing all the books by a specific author, but no specific genre
				$args = array(
					'post_type' => 'book-review',
					'posts_per_page' => $count,
					'orderby' => 'title',
					'order' => 'ASC',
					'book-author' => $author,
				);
			} elseif ( $genre && ! $author ) { // we're listing all the books of a specific genre, but not a specific author
				$args = array(
					'post_type' => 'book-review',
					'posts_per_page' => $count,
					'orderby' => $orderby,
					'order' => $order,
					'genre' => $genre,
				);
			} elseif ( $genre && $author ) { // we're listing all the books by a particular author in a specific genre
				$args = array(
					'post_type' => 'book-review',
					'posts_per_page' => $count,
					'orderby' => $orderby,
					'order' => $order,
					'genre' => $genre,
					'book-author' => $author,
				);
			}

			$query = new WP_Query( $args );
			ob_start();
			if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
			<div class="book-review-wrapper orderedby-<?php echo esc_attr( $orderby ); ?>" id="book-review-<?php echo get_the_ID(); ?>">
				<?php if ( has_term( '','book-author' ) && ( isset( $options['title-filter'] ) && $options['title-filter'] ) ) {
					/* translators: 1: title, 2: author */
					echo sprintf( __( '%1$s' ), '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>' );
} else {
	echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
} ?>

				<div <?php post_class( 'book-review-sc' ); ?>>
					<?php if ( ($covers == true) && has_post_thumbnail() ) { ?>
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="alignleft pull-left thumbnail">
							<?php if ( isset( $options['thumbnail'] ) && 'book-cover' == $options['thumbnail'] ) {
								the_post_thumbnail( 'book-cover' );
} else {
	the_post_thumbnail( 'thumbnail' );
} ?>
						</a>
					<?php } ?>

					<?php if ( $format ) {
						if ( $format == 1 ) { ?>
							<?php the_excerpt(); ?>
						<?php } elseif ( $format == 2 ) { ?>
							<?php the_content(); ?>
						<?php }
} ?>
				</div>

				<div class="post-meta">
					<?php if ( $options['rating'] && has_term( '','rating' ) ) {
						$rating = get_rating();
						if ( $rating == 'zero' ) {
							$rating = '0'; }
						$rating_arr = get_term_by( 'name', $rating, 'rating' );
						$star_slug = $rating_arr->slug;
						$rating_string = '<a href="' . home_url() . '/?rating=' . $star_slug . '/">' . get_rating_stars() . '</a>';
						echo '<span class="rating">';
						echo $rating_string;
						echo '</span><br />';
}
if ( $options['review-author'] && has_term( '','review-author' ) && is_singular( 'book-review' ) ) {
	$rev_auth = get_term_by( 'name', get_review_author(), 'review-author' );
	$rev_auth_slug = $rev_auth->slug;
	$author_string = '<a href="' . home_url() . '/?review-author=' . $rev_auth_slug . '/">' . get_review_author() . '</a>';
	echo '<span class="author">';
	echo sprintf( __( 'Review by %s', 'book-review-library' ), $author_string );
	echo '</span><br />';
}
if ( $options['reading-level'] && has_term( '', 'reading-level' ) ) {
	echo '<span class="reading-level">';
	echo sprintf( __( 'Reading Level: %s', 'book-review-library' ), get_reading_level() );
	echo '<span><br />';
}
if ( isset( $options['stock'] ) && $options['stock'] ) {
	if ( get_post_meta( get_the_ID(), 'book_in_stock', true ) ) {
		echo '<span class="in-stock">';
		_e( 'This book is <strong>in stock</strong>', 'book-review-library' );
		echo '</span>';
	} else {
		echo '<span class="out-of-stock">';
		_e( 'This book is <strong>currently checked out</strong>', 'book-review-library' );
		echo '</span>';
	}
} ?>
				</div>
				<div class="post-data">
					<?php if ( isset( $options['title-filter'] ) && ! $options['title-filter']  && has_term( '', 'book-author' ) ) { ?>
						<span class="book-author"><?php echo sprintf( __( '<strong>Author:</strong> %s', 'book-review-library' ), get_book_author() ); ?></span><br />
					<?php } ?>
					<?php if ( has_term( '','genre' ) ) { ?>
						<span class="genre"><?php echo sprintf( __( '<strong>Genre:</strong> %s', 'book-review-library' ), get_genres() ); ?></span><br />
					<?php } ?>
					<?php if ( $options['series'] && has_term( '','series' ) ) { ?>
						<span class="series"><?php echo sprintf( __( '<strong>Series:</strong> %s | ', 'book-review-library' ), get_book_series() ); ?></span>
					<?php } ?>
					<?php if ( $options['subject'] && has_term( '','subject' ) ) { ?>
						<span class="subjects"><?php echo sprintf( __( '<strong>Subjects:</strong> %s', 'book-review-library' ), get_subjects() ); ?></span><br />
					<?php } ?>
					<?php if ( $options['illustrator'] && has_term( '','illustrator' ) ) { ?>
						<span class="illustrator"><?php echo sprintf( __( '<strong>Illustrated by</strong> %s', 'book-review-library' ), get_illustrator() ); ?></span>
					<?php } ?>
				</div>
			</div>
			<?php
			endwhile;
endif;
			wp_reset_query();
			return ob_get_clean();
		} else { // we're doing this by book author, time to loop through again....
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$args = array(
						'post_type' => 'book-review',
						'posts_per_page' => $count,
						'tax_query' => array(
							array(
								'taxonomy' => 'book-author',
								'field' => 'slug',
								'terms' => $term->name,
							),
						),
						'orderby' => 'title',
						'order' => 'ASC',
					);
					$query = new WP_Query( $args );
					ob_start();
					if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
					<div class="book-review-wrapper orderedby-<?php echo esc_attr( $orderby ); ?>" id="book-review-<?php echo get_the_ID(); ?>">
						<?php if ( has_term( '','book-author' ) && ( isset( $options['title-filter'] ) && $options['title-filter'] ) ) {
							echo sprintf( __( '%1$s' ), '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>' );
} else {
	echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
} ?>

						<div <?php post_class( 'book-review-sc' ); ?>>
							<?php if ( ($covers == true) && has_post_thumbnail() ) { ?>
								<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="alignleft pull-left thumbnail">
									<?php if ( isset( $options['thumbnail'] ) && 'book-cover' == $options['thumbnail'] ) {
										the_post_thumbnail( 'book-cover' );
} else {
	the_post_thumbnail( 'thumbnail' );
} ?>
								</a>
							<?php } ?>

							<?php if ( $format ) {
								if ( $format == 1 ) { ?>
									<?php the_excerpt(); ?>
								<?php } elseif ( $format == 2 ) { ?>
									<?php the_content(); ?>
								<?php }
} ?>
						</div>

						<div class="post-meta">
							<?php if ( has_term( '','rating' ) ) {
								$rating = get_rating();
								if ( $rating == 'zero' ) {
									$rating = '0'; }
								$rating_arr = get_term_by( 'name', $rating, 'rating' );
								$star_slug = $rating_arr->slug;
								$rating_string = '<a href="' . home_url() . '/?rating=' . $star_slug . '/">' . get_rating_stars() . '</a>';
								echo '<span class="rating">';
								echo $rating_string;
								echo '</span><br />';
}
if ( $options['review-author'] && has_term( '','review-author' ) && is_singular( 'book-review' ) ) {
	$rev_auth = get_term_by( 'name', get_review_author(), 'review-author' );
	$rev_auth_slug = $rev_auth->slug;
	$author_string = '<a href="' . home_url() . '/?review-author=' . $rev_auth_slug . '/">' . get_review_author() . '</a>';
	echo '<span class="author">';
	echo sprintf( __( 'Review by %s', 'book-review-library' ), $author_string );
	echo '</span><br />';
}
if ( $options['reading-level'] && has_term( '', 'reading-level' ) ) {
	echo '<span class="reading-level">';
	echo sprintf( __( 'Reading Level: %s', 'book-review-library' ), get_reading_level() );
	echo '<span><br />';
}
if ( isset( $options['stock'] ) && $options['stock'] ) {
	if ( get_post_meta( get_the_ID(), 'book_in_stock', true ) ) {
		echo '<span class="in-stock">';
		_e( 'This book is <strong>in stock</strong>', 'book-review-library' );
		echo '</span>';
	} else {
		echo '<span class="out-of-stock">';
		_e( 'This book is <strong>currently checked out</strong>', 'book-review-library' );
		echo '</span>';
	}
} ?>
						</div>
						<div class="post-data">
							<?php if ( isset( $options['title-filter'] ) && ! $options['title-filter']  && has_term( '', 'book-author' ) ) { ?>
								<span class="book-author"><?php echo sprintf( __( '<strong>Author:</strong> %s', 'book-review-library' ), get_book_author() ); ?></span><br />
							<?php } ?>
							<?php if ( has_term( '','genre' ) ) { ?>
								<span class="genre"><?php echo sprintf( __( '<strong>Genre:</strong> %s', 'book-review-library' ), get_genres() ); ?></span><br />
							<?php } ?>
							<?php if ( $options['series'] && has_term( '','series' ) ) { ?>
								<span class="series"><?php echo sprintf( __( '<strong>Series:</strong> %s | ', 'book-review-library' ), get_book_series() ); ?></span>
							<?php } ?>
							<?php if ( $options['subject'] && has_term( '','subject' ) ) { ?>
								<span class="subjects"><?php echo sprintf( __( '<strong>Subjects:</strong> %s', 'book-review-library' ), get_subjects() ); ?></span><br />
							<?php } ?>
							<?php if ( $options['illustrator'] && has_term( '','illustrator' ) ) { ?>
								<span class="illustrator"><?php echo sprintf( __( '<strong>Illustrated by</strong> %s', 'book-review-library' ), get_illustrator() ); ?></span>
							<?php } ?>
						</div>
					</div>
					<?php
					endwhile;
endif;
					wp_reset_query();
					return ob_get_clean();
				}
			}
		}
	}
}
