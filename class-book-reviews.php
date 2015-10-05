<?php
/**
 * Plugin Name.
 *
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPLv3
 * @link      http://chrisreynolds.io
 * @copyright 2015 Chris Reynolds
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
	protected $version = '1.5.0.rc1';

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

		require_once( plugin_dir_path( __FILE__ ) . '/inc/roles.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/views/options.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/views/actions.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/inc/cmb.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/inc/cpt.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/inc/taxonomy.php' );

		$this->roles = new Book_Review_Roles();

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
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		// Add new roles on activation.
		$this->roles->add_roles();

		// Add new capabilities on activation.
		$this->roles->add_caps();

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		// Remove Book Review Library capabilities from existing user roles.
		$this->roles->remove_caps();

		// Remove Book Review Library user roles and all caps.
		$this->roles->remove_roles();

		// Remove Book Review Library auto-inserted taxonomy terms.
		Book_Review_Library_Taxonomies::remove_terms();

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
		}

	}

	/**
	 * Register and enqueue genericons style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $post;
		if ( !is_admin() && ( 'book-review' == get_post_type() || ( is_page() && has_shortcode( $post->post_content, 'book-reviews' ) ) ) ) {
			wp_enqueue_style( $this->plugin_slug . '-genericons', plugins_url( 'genericons/genericons.css', __FILE__ ), array(), $this->version );
			wp_enqueue_style( $this->plugin_slug . '-public', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
		}
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

			include_once(BOOK_REVIEWS_TEMPLATE_TAGS);

			$options = get_option( 'book_reviews_settings', book_reviews_option_defaults() );

			unset( $wp_meta_boxes['book-review']['normal']['core']['authordiv'] );

			remove_meta_box( 'publisherdiv', 'book-review', 'side' );
			remove_meta_box( 'tagsdiv-format', 'book-review', 'side' );
			remove_meta_box( 'tagsdiv-language', 'book-review', 'side' );
			remove_meta_box( 'tagsdiv-subject', 'book-review', 'side' );
			remove_meta_box( 'seriesdiv', 'book-review', 'side' );
			remove_meta_box( 'book-authordiv', 'book-review', 'side' );
			remove_meta_box( 'illustratordiv', 'book-review', 'side' );
			remove_meta_box( 'tagsdiv-reading-level', 'book-review', 'side' );
			remove_meta_box( 'tagsdiv-genre', 'book-review', 'side' );
			remove_meta_box( 'tagsdiv-rating', 'book-review', 'side' );
			remove_meta_box( 'postimagediv', 'book-review', 'side' );
	    	add_meta_box('postimagediv', __('Book Cover', 'book-review-library'), 'post_thumbnail_meta_box', 'book-review', 'side', 'default');
	    }
	}


 	/**
 	 * Get Book Review Library options helper function
 	 *
 	 * @since 	1.5.0
 	 * @return  array 	The options array for Book Review Library
 	 */
 	public function get_options() {
		// include helper functions
		include_once(BOOK_REVIEWS_TEMPLATE_TAGS);

		// get the options
		return get_option( 'book_reviews_settings', book_reviews_option_defaults() );
 	}

 	/**
 	 * Check if a given option is enabled
 	 *
 	 * @since 	1.5.0
 	 * @param 	string 	The option name to check
 	 * @return 	bool 	True of the setting is enabled, false if it isn't or no option was
 	 * 					passed
 	 */
 	public function is_option_enabled( $option_name = '' ) {

 		// return false if nothing was passed
 		if ( '' == $option_name )
 			return false;

		// get the options
		$options = $this->get_options();

 		// if the options array isn't an array
 		if ( empty( $options ) )
 			return false;

 		// if the option isn't set
 		if ( !isset( $options[$option_name] ) )
 			return false;

 		// if the option is true
 		if ( true == $options[$option_name] )
 			return true;

 		// for anything else
 		return false;

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
	    if ('book-review' == $GLOBALS['post_type'])
	      add_filter('admin_post_thumbnail_html', array($this,'do_thumb'));
	}

	/**
	 * Replaces "Set featured image" with "Select Book Cover"
	 *
	 * @since 	1.0.0
	 *
	 * @return 	string 	returns the modified text
	 */
	public function do_thumb($content){
		 return str_replace(__('Set featured image'), __('Select Book Cover', 'book-review-library'),$content);
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
		$columns = array_merge($default_columns, $series_column, $illustrator_column, $subject_column, $reading_level_column, $awards_column, $rating_column);

		return $columns;
	}

	/**
	 * Renders new data for the new columns
	 *
	 * @since 	1.0.0
	 */
	public function manage_book_review_columns( $column, $post_id ){
		global $post;

		switch( $column ) {

			// if displaying the book author column
			case 'book_author' :
				// get the author(s) for the book
				$terms = get_the_terms( $post_id, 'book-author' );

				// if terms were found
				if ( !empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'book-author' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'book-author', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				}
				// if no terms are found, say something
				else {
					_e( 'No authors found', 'book-review-library' );
				}
				break;

			// if displaying the genre column
			case 'genre' :
				// get the genre(s) for the book
				$terms = get_the_terms( $post_id, 'genre' );

				// if terms were found
				if ( !empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'genre' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'genre', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				}
				// if no terms are found, say something
				else {
					_e( 'No genres found', 'book-review-library' );
				}
				break;

			// if displaying the series column
			case 'series' :
				// get the series(s) for the book
				$terms = get_the_terms( $post_id, 'series' );

				// if terms were found
				if ( !empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'series' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'series', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				}
				// if no terms are found, say something
				else {
					_e( 'No series found', 'book-review-library' );
				}
				break;

			// if displaying the illustrator column
			case 'illustrator' :
				// get the illustrator(s) for the book
				$terms = get_the_terms( $post_id, 'illustrator' );

				// if terms were found
				if ( !empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'illustrator' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'illustrator', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				}
				// if no terms are found, say something
				else {
					_e( 'No illustrators found', 'book-review-library' );
				}
				break;

			// if displaying the subjects column
			case 'subjects' :
				// get the subjects for the book
				$terms = get_the_terms( $post_id, 'subject' );

				// if terms were found
				if ( !empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'subject' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'subject', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				}
				// if no terms are found, say something
				else {
					_e( 'No subjects found', 'book-review-library' );
				}
				break;

			// if displaying the reading level column
			case 'reading_level' :
				// get the reading level for the book
				$terms = get_the_terms( $post_id, 'reading-level' );

				// if terms were found
				if ( !empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'reading-level' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'reading-level', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				}
				// if no terms are found, say something
				else {
					_e( 'No reading level found', 'book-review-library' );
				}
				break;

			// if displaying the awards column
			case 'awards' :
				// get the awards for the book
				$terms = get_the_terms( $post_id, 'awards' );

				// if terms were found
				if ( !empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'awards' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'awards', 'display' ) )
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				}
				// if no terms are found, say something
				else {
					_e( 'No awards found', 'book-review-library' );
				}
				break;

			// if displaying the rating column
			case 'rating' :
				// get the book review rating
				$terms = get_the_terms( $post_id, 'rating' );

				// if terms were found
				if ( !empty( $terms ) ) {

					$out = array();

					// loop through each term, linking to the 'edit posts' page for the specific term
					foreach( $terms as $term ) {
						if ( $term->name == '5' ) {
							$rating = '<div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div>';
						}
						elseif ( $term->name == '4' ) {
							$rating = '<div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div>';
						}
						elseif ( $term->name == '3' ) {
							$rating = '<div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div>';
						}
						elseif ( $term->name == '2' ) {
							$rating = '<div class="genericon genericon-star"></div><div class="genericon genericon-star"></div>';
						}
						elseif ( $term->name == '1' ) {
							$rating = '<div class="genericon genericon-star"></div>';
						}
						else {
							$rating = _e( 'No rating found', 'book-review-library' );
						}
						$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'rating' => $term->slug ), 'edit.php' ) ),
						$rating
						);
					}

					// join the terms, separating them with a comma
					echo join( ', ', $out );
				}
				// if no terms are found, say something
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
        if( is_search() && !is_admin()) {
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
	public function save_book_review_postdata($post_id, $post) {
		$nonce = isset( $_POST['noncename'] ) ? $_POST['noncename'] : 'all the hosts, dream and blue';
		if ( !wp_verify_nonce( $nonce, plugin_basename(__FILE__) )) {
		return $post->ID;
		}
		/* confirm user is allowed to save page/post */
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post->ID ))
			return $post->ID;
		} else {
			if ( !current_user_can( 'edit_post', $post->ID ))
			return $post->ID;
		}

		/* ready our data for storage */
		$meta_keys = array('award_image' => 'text', 'book_in_stock' => 'text', 'isbn' => 'text');

		/* Add values of $mydata as custom fields */
		foreach ($meta_keys as $meta_key => $type) {
			if( $post->post_type == 'revision' )
				return;
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
		include_once(BOOK_REVIEWS_WIDGETS);
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
	 * Returns a WP_Query arguments array based on a string passed.
	 * @since  1.5.0
	 * @param  string $param What type of WP_Query arguments to use.
	 * @param  array  $args  Some values to pass directly to the WP_Query args.
	 * @return array         A WP_Query arguments array.
	 */
	public function books_by( $param = '', $args = array() ) {
		if ( ! $param ) {
			return false;
		}

		$values = wp_parse_args( $args, array(
			'title'    => '',
			'post_id'  => 0,
			'count'    => -1,
			'order_by' => 'date_added', // Author, title, date added (default).
			'order'    => 'DESC',
			'format'   => 'none',       // 0 = none, 1 = excerpt, 2 = full
			'author'   => '',           // any author
			'genre'    => '',           // any genre
			'terms'    => array(),
		) );

		$args = array();

		switch ( $param ) {
			case 'title' :
				$args = array(
					'post_type'      => 'book-review',
					'name'           => $values['title'],
					'posts_per_page' => 1,
				);
				break;

			case 'post_id' :
				$args = array(
					'post_type'      => 'book-review',
					'p'              => $values['post_id'],
					'posts_per_page' => 1,
				);
				break;

			case 'default' :
				$args = array(
					'post_type'      => 'book-review',
					'posts_per_page' => $values['count'],
					'orderby'        => $values['orderby'],
					'order'          => $values['order'],
				);
				break;

			case 'author' :
				$args = array(
					'post_type'      => 'book-review',
					'posts_per_page' => $values['count'],
					'orderby'        => 'title',
					'order'          => 'ASC',
					'book-author'    => $values['author'],
				);
				break;

			case 'genre' :
				$args = array(
					'post_type'      => 'book-review',
					'posts_per_page' => $values['count'],
					'orderby'        => $values['orderby'],
					'order'          => $values['order'],
					'genre'          => $values['genre'],
				);
				break;

			case 'genre_and_author' :
				$args = array(
					'post_type'      => 'book-review',
					'posts_per_page' => $values['count'],
					'orderby'        => $values['orderby'],
					'order'          => $values['order'],
					'genre'          => $values['genre'],
					'book-author'    => $values['author'],
				);
				break;

			case 'group_by_author' :
				$args = array(
					'post_type' => 'book-review',
					'posts_per_page' => $values['count'],
					'tax_query' => array(
						array(
							'taxonomy' => 'book-author',
							'field' => 'slug',
							'terms' => $values['terms'],
						),
					),
					'orderby' => 'title',
					'order' => 'ASC',
				);
				break;

			default:
				$args = array(
					'post_type'      => 'book-review',
					'posts_per_page' => $values['count'],
					'orderby'        => $values['orderby'],
					'order'          => $values['order'],
				);
				break;
		}

		return $args;
	}

	/**
	 * Echoes the rating markup.
	 * @since  1.5.0
	 * @return void
	 */
	public function do_rating() {
		$rating = get_rating();
		if ( 'zero' == $rating ) {
			$rating = '0';
		}
		$rating_arr = get_term_by( 'name', $rating, 'rating' );
		$star_slug = $rating_arr->slug;
		$rating_string = '<a href="' . home_url() . '/?rating=' . $star_slug . '/">' . get_rating_stars() . '</a>';
		$output = '<span class="rating">';
		$output .= $rating_string;
		$output .= '</span><br />';
		echo esc_attr( $output );
	}

	/**
	 * Echoes the review author markup.
	 * @since  1.5.0
	 * @return void
	 */
	public function do_review_author() {
		$review_author      = get_term_by( 'name', get_review_author(), 'review-author' );
		$review_author_slug = $review_author->slug;
		$author_string      = '<a href="' . home_url() . '/?review-author=' . $review_author_slug . '/">' . get_review_author() . '</a>';
		$output = '<span class="author">';
		$output .= sprintf( esc_attr__( 'Review by %s', 'book-review-library' ), $author_string );
		$output .= '</span><br />';
		echo esc_attr( $output );
	}


	/**
	 * Echoes the reading level markup.
	 * @since  1.5.0
	 * @return void
	 */
	public function do_reading_level() {
		$output = '<span class="reading-level">';
		$output .= sprintf( esc_html__( 'Reading Level: %s', 'book-review-library' ), get_reading_level() );
		$output .= '<span><br />';
		echo esc_attr( $output );
	}

	/**
	 * Echoes the stock markup.
	 * @since  1.5.0
	 * @return void
	 */
	public function do_stock() {
		if ( get_post_meta( get_the_ID(), 'book_in_stock', true ) ) {
			$output = '<span class="in-stock">';
			$output .= esc_attr__( 'This book is <strong>in stock</strong>', 'book-review-library' );
			$output .= '</span>';
		} else {
			$output = '<span class="out-of-stock">';
			$output .= esc_attr__( 'This book is <strong>currently checked out</strong>', 'book-review-library' );
			$output .= '</span>';
		}

		echo esc_attr( $output );
	}

	/**
	 * Figure out the orderby parameters
	 * @since  1.5.0
	 * @param  string $order_by The sort order passed to the shortcode.
	 * @return array            Orderby parameters and helper variables for determining output.
	 */
	public function get_order_by( $order_by = '' ) {

		$orderby = array(
			'orderby'        => 'date',
			'order'          => 'DESC',
			'terms'          => array(),
			'orderby_author' => false,
			'title'          => '',
		);

		if ( ! $order_by ) {
			return $orderby;
		}

		switch ( $order_by ) {
			case 'date_added' :
				return $orderby;
				break;
			case 'author' :
				$orderby['terms']          = get_terms( 'book-author' );
				$orderby['orderby_author'] = true;
				$orderby['orderby']        = 'title';
				$orderby['order']          = 'ASC';
				break;
			case 'title' :
				$orderby['title'] = 'title';
				$orderby['order'] = 'ASC';
				break;
			default :
				break;
		}

		return $orderby;

	}

	/**
	 * Return output if there are no books found.
	 * @param  boolean $echo Whether to echo or return the output.
	 * @since  1.5.0
	 * @return string        The "no books found" markup.
	 */
	public function no_books_found( $echo = false ) {
		$output = '<div class="book-review-library no-posts">';
		$output .= '<p>' . __( 'No books found.', 'book-review-library' ) . '</p>';
		$output .= '</div>';
		if ( ! $echo ) {
			return $output;
		}

		echo $output; // WPCS: XSS ok.
		return;
	}

	/**
	 * Method for Book Review Library loops
	 * @param  boolean $query The WP_Query to run.
	 * @param  array   $args  Optional. Arguments to determine how the loop outputs.
	 * @return void
	 */
	public function the_loop( $query = false, $args = array() ) {

		if ( ! $query ) {
			$this->no_books_found( true );
			return;
		}

		include_once( BOOK_REVIEWS_TEMPLATE_TAGS );
		$options = get_option( 'book_reviews_settings', book_reviews_option_defaults() );
		$args    = wp_parse_args( $args, array(
			'orderby'   => '',
			'covers'    => '',
			'thumbnail' => '',
			'format'    => '',
		) );
		extract( $args );

		if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
			<div class="book-review-wrapper orderedby-<?php esc_attr_e( $orderby ); ?>" id="book-review-<?php echo absint( get_the_ID() ); ?>">
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<div <?php post_class( 'book-review-sc' ); ?>>
					<?php if ( ( $covers ) && has_post_thumbnail() ) { ?>
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="alignleft pull-left thumbnail"><?php the_post_thumbnail( $thumbnail ); ?></a>
					<?php } // End covers. ?>

					<?php if ( $format ) { ?>
						<?php the_excerpt(); ?>
					<?php } else { ?>
						<?php the_content(); ?>
					<?php } // End Format. ?>
				</div>

				<div class="post-meta">
					<?php
					if ( has_term( '', 'rating' ) ) {
						$this->do_rating();
					} // End Ratings.
					if ( has_term( '', 'review-author' ) && is_singular( 'book-review' ) ) {
						$this->do_review_author();
					} // End Review Author.
					if ( has_term( '', 'reading-level' ) ) {
						$this->do_reading_level();
					} // End Reading Levels.
					if ( isset( $options['stock'] ) && $options['stock'] ) {
						$this->do_stock();
					} // End Stock.
					?>
				</div>
				<div class="post-data">
					<?php if ( isset( $options['title-filter'] ) && ! $options['title-filter']  && has_term( '', 'book-author' ) ) { ?>
						<span class="book-author"><?php echo sprintf( __( '<strong>Author:</strong> %s', 'book-review-library' ), get_book_author() ); // WPCS: XSS ok. ?></span><br />
					<?php } // End Authors. ?>
					<?php if ( has_term( '', 'genre' ) ) { ?>
						<span class="genre"><?php echo sprintf( __( '<strong>Genre:</strong> %s', 'book-review-library' ), get_genres() );  // WPCS: XSS ok. ?></span><br />
					<?php } // End Genres. ?>
					<?php if ( has_term( '', 'series' ) ) { ?>
						<span class="series"><?php echo sprintf( __( '<strong>Series:</strong> %s | ', 'book-review-library' ), get_book_series() );  // WPCS: XSS ok. ?></span>
					<?php } // End Series. ?>
					<?php if ( has_term( '', 'subject' ) ) { ?>
						<span class="subjects"><?php echo sprintf( __( '<strong>Subjects:</strong> %s', 'book-review-library' ), get_subjects() );  // WPCS: XSS ok. ?></span><br />
					<?php } // End Subjects. ?>
					<?php if ( has_term( '', 'illustrator' ) ) { ?>
						<span class="illustrator"><?php echo sprintf( __( '<strong>Illustrated by</strong> %s', 'book-review-library' ), get_illustrator() );  // WPCS: XSS ok. ?></span>
					<?php } // End Illustrators. ?>
				</div>
			</div>
			<?php
		endwhile;
		else :
			$this->no_books_found( true );
		endif;
		wp_reset_query();

	}

	/**
	 * Creates the shortcode
	 * @param  array $atts Passed shortcode attributes.
	 * @since  1.0.0
	 * @return string      The output of the shortcode.
	 */
	public function create_shortcode( $atts ) {
		global $is_book_review_shortcode;
		$is_book_review_shortcode = true;

		extract(shortcode_atts( array(
			'count'    => -1,
			'covers'   => false,
			'order_by' => 'date_added', // Author, title, date added (default).
			'format'   => 0,            // 0 = none, 1 = excerpt, 2 = full
			'author'   => false,        // any author
			'genre'    => false,        // any genre
			'title'    => false,        // any title
			'id'       => 0,            // A specific post ID.
		), $atts ));

		extract( $this->get_order_by( $order_by ) );

		$thumbnail = ( isset( $options['thumbnail'] ) && 'book-cover' == $options['thumbnail'] ) ? 'book-cover' : 'thumbnail';

		// We passed a book title.
		if ( $title ) {
			$args = $this->books_by( 'title' );
		} // We passed a post ID.
		elseif ( $post_id ) {
			$args = $this->books_by( 'post_id' );
		} // We are not listing books of a specific author or a specific genre.
		elseif ( ! $author && ! $genre ) {
			$args = $this->books_by( 'default', array( 'count' => $count, 'orderby' => $orderby, 'order' => $order ) );
		} // We're listing all the books by a specific author, but no specific genre.
		elseif ( $author && ! $genre ) {
			$args = $this->books_by( 'author', array( 'count' => $count, 'author' => $author ) );
		} // We're listing all the books of a specific genre, but not a specific author.
		elseif ( $genre && ! $author ) {
			$args = $this->books_by( 'genre', array( 'count' => $count, 'orderby' => $orderby, 'order' => $order, 'genre' => $genre ) );
		} // We're listing all the books by a particular author in a specific genre.
		elseif ( $genre && $author ) {
			$args = $this->books_by( 'genre_and_author', array( 'count' => $count, 'orderby' => $orderby, 'order' => $order, 'genre' => $genre, 'author' => $author ) );
		}

		// Check the order_by author value to determine if we need a separate loop for each author.
		if ( ! $orderby_author ) {

			$query = new WP_Query( $args );
			ob_start();

			$this->the_loop( $query, array(
				'orderby'   => $orderby,
				'covers'    => $covers,
				'thumbnail' => $thumbnail,
				'format'    => $format,
			) );

			return ob_get_clean();

		} else {

			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {

					$query = new WP_Query( $args );
					ob_start();

					$this->the_loop( $query, array(
						'orderby'   => $orderby,
						'covers'    => $covers,
						'thumbnail' => $thumbnail,
						'format'    => $format,
					) );

					return ob_get_clean();

				} // End foreach loop.
			} // End empty terms check.

		} // End orderby author check.

	} // End function.

} // End class.
