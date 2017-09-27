<?php
/**
 * Plugin Name: Book Review Library
 * Plugin URI: https://wordpress.org/plugins/book-review-libarary/
 * Description: A book cataloguing and review system designed with book collectors, bookophiles and librarians in mind.
 * Author: Chris Reynolds
 * Author URI: https://chrisreynolds.io
 * License: GPLv2
 */

namespace BookReview;

require_once __DIR__ . '/inc/namespace.php';

// TODO: Find a better way to solve this problem.
$is_book_review_shortcode = false;

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__,   __NAMESPACE__ . '\\activate' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\deactivate' );
