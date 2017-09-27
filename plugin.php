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

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
