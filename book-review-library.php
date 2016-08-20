<?php
/**
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPLv3
 * @link      http://chrisreynolds.io
 * @copyright 2014 Chris Reynolds
 *
 * @wordpress-plugin
 * Plugin Name: Book Review Library
 * Plugin URI:  http://museumthemes.com/book-review-library/
 * Description: A book cataloguing and review system designed with bookophiles and librarians in mind.
 * Version:     1.4.19
 * Author:      Chris Reynolds
 * Author URI:  http://chrisreynolds.io
 * License:     GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: book-review-library
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'BOOK_REVIEWS_FUNC', plugin_dir_path( __FILE__ ) . 'inc/func.php' );
define( 'BOOK_REVIEWS_WIDGETS', plugin_dir_path( __FILE__ ) . 'inc/widgets.php' );

$is_book_review_shortcode = false;

require_once( plugin_dir_path( __FILE__ ) . 'class-book-reviews.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'Book_Reviews', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Book_Reviews', 'deactivate' ) );

Book_Reviews::get_instance();
