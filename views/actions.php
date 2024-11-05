<?php

/**
 * Sets up the actions
 * 
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPLv3
 * @link      http://chrisreynolds.io
 * @copyright 2024 Chris Reynolds
 */

require_once BOOK_REVIEWS_TEMPLATE_TAGS;
require_once plugin_dir_path( __FILE__ ) . 'public.php';

// Load admin style sheet and JavaScript.
add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );
add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );

// Load public-facing style sheet and JavaScript.
add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );

// set new thumbnail size.
add_action( 'after_setup_theme', [ $this, 'create_tiny_thumbs' ] );

// Move metaboxes around.
add_action( 'add_meta_boxes', [ $this, 'move_meta_boxes' ] );

// Register the options.
add_action( 'admin_init', [ $this, 'settings_init' ] );

// Rename "featured image".
add_action( 'admin_head-post-new.php', [ $this, 'change_thumbnail_html' ] );
add_action( 'admin_head-post.php', [ $this, 'change_thumbnail_html' ] );

// Update the Book Review columns.
add_filter( 'manage_edit-book-review_columns', [ $this, 'edit_book_review_columns' ] );
add_action( 'manage_book-review_posts_custom_column', [ $this, 'manage_book_review_columns' ], 10, 2 );

// save the meta data.
add_action( 'save_post', [ $this, 'save_book_review_postdata' ], 1, 2 );

// create a related items widget.
add_action( 'widgets_init', [ $this, 'register_book_review_widget' ] );

// add a shortcode.
add_shortcode( 'book-reviews', [ $this, 'create_shortcode' ] );

// add a custom where parameter to search by ISBN.
add_filter( 'posts_where', [ $this, 'search_by_isbn' ] );

// do i18n stuff.
add_action( 'plugins_loaded', [ $this, 'setup_i18n' ] );

// flush the rewrite rules.
add_action( 'activate-book-review-library.php', [ $this, 'flush_rewrites' ] );
add_action( 'deactivate-book-review-library.php', [ $this, 'flush_rewrites' ] );

// remove the settings menu for librarians.
add_action( 'admin_menu', [ $this, 'remove_menu_for_librarians' ] );
