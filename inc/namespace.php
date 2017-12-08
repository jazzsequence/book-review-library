<?php
/**
 * Book Review Library main plugin namespace.
 *
 * @since   2.0.0-alpha
 *
 * @package BookReview
 */

namespace BookReview;
use BookReview\Roles as Roles;
use BookReview\Taxonomies as Taxonomies;

/**
 * Bootstrap the plugin.
 *
 * Registers actions and filter required to run the plugin.
 */
function bootstrap() {
	spl_autoload_register( __NAMESPACE__ . '\\autoload' );

	// Do i18n.
	add_action( 'plugins_loaded',                    __NAMESPACE__ . '\\setup_i18n' );

	// Register Widgets.
	add_action( 'widgets_init',                      __NAMESPACE__ . '\\register_widgets' );

	// Remove the settings menu for librarians.
	add_action( 'admin_menu',                        __NAMESPACE__ . '\\remove_menu_for_librarians' );

	// Activation hooks.
	add_action( 'book_review_action_add_roles',      __NAMESPACE__ . '\\Roles\\add_roles' );
	add_action( 'book_review_action_add_caps',       __NAMESPACE__ . '\\Roles\\add_caps' );

	// Deactivation hooks.
	add_action( 'book_review_action_remove_roles',   __NAMESPACE__ . '\\Roles\\remove_roles' );
	add_action( 'book_review_action_remove_caps',    __NAMESPACE__ . '\\Roles\\remove_caps' );
	add_action( 'book_review_action_delete_ratings', __NAMESPACE__ . '\\Taxonomies\\delete_ratings' );

	// Initialize the taxonomies.
	Taxonomies\bootstrap();

	// Initialize the post type.
	add_action( 'init',                              __NAMESPACE__ . '\\CPT\\register_book_reviews' );
	add_action( 'do_meta_boxes',                     __NAMESPACE__ . '\\CPT\\rename_featured_image' );
	add_action( 'cmb2_init',                         __NAMESPACE__ . '\\CPT\\add_book_review_meta' );
	add_action( 'cmb2_init',                         __NAMESPACE__ . '\\CPT\\add_author_info' );

	// Initialize the options.
	add_action( 'admin_init',                        __NAMESPACE__ . '\\Options\\init' );
	add_action( 'admin_menu',                        __NAMESPACE__ . '\\Options\\add_plugin_admin_menu' );
}

/**
 * Autoload classes for this namespace.
 *
 * @param string $class Class name.
 */
function autoload( $class ) {
	if ( strpos( $class, __NAMESPACE__ . '\\' ) !== 0 ) {
		return;
	}

	$relative = strtolower( substr( $class, strlen( __NAMESPACE__ . '\\' ) ) );
	$parts = explode( '\\', $relative );
	$final = str_replace( '_', '-', array_pop( $parts ) );
	array_push( $parts, 'class-' . $final . '.php' );
	$path = __DIR__ . '/' . implode( '/', $parts );

	require $path;
}

/**
 * Registers the widgets
 *
 * @since 	1.0.0
 */
function register_widgets() {
	register_widget( __NAMESPACE__ . '\\Widgets\\Book_Review_Widget' );
	register_widget( __NAMESPACE__ . '\\Widgets\\Book_Review_Recent_Widget' );
}

/**
 * Fired when the plugin is activated.
 *
 * @since    1.0.0
 *
 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
 */
function activate( $network_wide ) {
	Roles\add_roles();
	Roles\add_caps();
}

/**
 * Fired when the plugin is deactivated.
 *
 * @since    1.0.0
 *
 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
 */
function deactivate( $network_wide ) {
	Roles\remove_caps();
	Roles\remove_roles();
	Taxonomies\delete_ratings();
}

/**
 * Do i18n stuff
 *
 * @since 1.4
 * @link http://ottopress.com/2013/language-packs-101-prepwork/
 */
function setup_i18n() {
	load_plugin_textdomain( 'book-review-library', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}

/**
 * Remove the settings menu for librarians
 *
 * @since 1.4.6
 */
function remove_menu_for_librarians() {
	if ( ! current_user_can( 'manage_book_review_options' ) ) {
		remove_menu_page( 'options-general.php' );
	}
}