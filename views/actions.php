<?php
		include_once(BOOK_REVIEWS_TEMPLATE_TAGS);
		include_once( 'public.php' );

		$options = get_option( 'book_reviews_settings', book_reviews_option_defaults() );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		// set new thumbnail size
		add_action( 'after_setup_theme', array( $this, 'create_tiny_thumbs' ) );

		// Move metaboxes around
		add_action( 'add_meta_boxes', array( $this, 'move_meta_boxes' ) );

		// Register the options
		add_action( 'admin_init' , array( $this, 'settings_init' ) );

		// Rename "featured image"
		add_action('admin_head-post-new.php', array($this, 'change_thumbnail_html'));
		add_action('admin_head-post.php', array($this, 'change_thumbnail_html'));

		// Update the Book Review columns
		add_filter( 'manage_edit-book-review_columns', array($this, 'edit_book_review_columns') );
		add_action( 'manage_book-review_posts_custom_column', array($this, 'manage_book_review_columns'), 10, 2 );

		// save the meta data
		add_action('save_post', array( $this, 'save_book_review_postdata' ), 1, 2);

		// create a related items widget
		add_action( 'widgets_init', array( $this, 'register_book_review_widget' ) );

		// add a shortcode
		add_shortcode( 'book-reviews', array( $this, 'create_shortcode' ) );

		// add a custom where parameter to search by ISBN
		add_filter( 'posts_where', array( $this, 'search_by_isbn' ) );

		// do i18n stuff
		add_action( 'plugins_loaded', array( $this, 'setup_i18n' ) );

		// flush the rewrite rules
		add_action( 'activate-book-review-library.php', array( $this, 'flush_rewrites' ) );
		add_action( 'deactivate-book-review-library.php', array( $this, 'flush_rewrites' ) );

		// remove the settings menu for librarians
		add_action ( 'admin_menu', array( $this, 'remove_menu_for_librarians' ) );