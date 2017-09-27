<?php
/**
 * Book Review Library main plugin namespace.
 *
 * @since   2.0.0-alpha
 *
 * @package BookReview
 */

namespace BookReview;

/**
 * Bootstrap the plugin.
 *
 * Registers actions and filter required to run the plugin.
 */
function bootstrap() {
	spl_autoload_register( __NAMESPACE__ . '\\autoload' );

	// Register Widgets.
	add_action( 'widgets_init', __NAMESPACE__ . '\\register_widgets' );
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
	$final = array_pop( $parts );
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
