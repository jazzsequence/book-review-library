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

	// Register Widgets.
	add_action( 'widgets_init',                      __NAMESPACE__ . '\\register_widgets' );

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
