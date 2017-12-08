<?php
/**
 * Plugin Name: Book Review Library
 * Plugin URI: https://wordpress.org/plugins/book-review-libarary/
 * Description: A book cataloguing and review system designed with book collectors, bookophiles and librarians in mind.
 * Version: 2.0.0-20171208
 * Author: Chris Reynolds
 * Author URI: https://chrisreynolds.io
 * License: GPLv2
 *
 * @package BookReview
 */

namespace BookReview;

// Load vendor libraries.
require_once __DIR__ . '/vendor/cmb2/init.php';
require_once __DIR__ . '/vendor/extended-cpts/extended-cpts.php';

// Load our internal libraries.
require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/cpt/namespace.php';
require_once __DIR__ . '/inc/cmb2/namespace.php';
require_once __DIR__ . '/inc/roles/namespace.php';
require_once __DIR__ . '/inc/taxonomies/namespace.php';
require_once __DIR__ . '/inc/options/namespace.php';
require_once __DIR__ . '/inc/shortcode/namespace.php';
require_once __DIR__ . '/inc/template-tags.php';

// TODO: Find a better way to solve this problem.
$is_book_review_shortcode = false;

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__,   __NAMESPACE__ . '\\activate' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\deactivate' );
