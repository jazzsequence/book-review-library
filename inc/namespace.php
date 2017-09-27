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
	add_action( 'widgets_init',                      __NAMESPACE__ . '\\register_widgets' );

	// Activation hooks.
	add_action( 'book_review_action_add_roles',      __NAMESPACE__ . '\\add_roles' );
	add_action( 'book_review_action_add_caps',       __NAMESPACE__ . '\\add_caps' );

	// Deactivation hooks.
	add_action( 'book_review_action_remove_roles',   __NAMESPACE__ . '\\remove_roles' );
	add_action( 'book_review_action_remove_caps',    __NAMESPACE__ . '\\remove_caps' );
	add_action( 'book_review_action_delete_ratings', __NAMESPACE__ . '\\delete_ratings' );
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

/**
 * Fired when the plugin is activated.
 *
 * @since    1.0.0
 *
 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
 */
function activate( $network_wide ) {
	do_action( 'book_review_action_add_roles' );
	do_action( 'book_review_action_add_caps' );
}

/**
 * Fired when the plugin is deactivated.
 *
 * @since    1.0.0
 *
 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
 */
function deactivate( $network_wide ) {
	do_action( 'book_review_action_remove_caps' );
	do_action( 'book_review_action_remove_roles' );
	do_action( 'book_review_action_delete_ratings' );
}

/**
 * Add a new user role.
 *
 * @since 2.0.0-alpha
 * @param string $role The user role name.
 * @param string $name The localized user role name.
 */
function add_new_role( $role, $name ) {
	add_role( $role, $name, [] );
	add_new_caps( $role );
}

/**
 * Add new capabilities for existing user roles.
 *
 * @since 2.0.0-alpha
 * @param string $role The user role to update.
 */
function add_new_caps( $role ) {
	$caps = get_caps_for( $role );

	$user_role = get_role( $role );
	if ( $user_role ) {
		foreach ( $caps as $capability ) {
			$user_role->add_cap( $capability );
		}
	}
}

/**
 * Get caps for a role
 *
 * @since  2.0.0-alpha
 * @param  string $role The role needed.
 * @return array        An array of caps for that role.
 */
function get_caps_for( $role ) {
	// Base WP caps for new roles.
	$base_wp = [
		'read'                          => true,
		'delete_posts'                  => true,
		'delete_published_posts'        => true,
		'edit_posts'                    => true,
		'edit_published_posts'          => true,
		'publish_posts'                 => true,
		'upload_files'                  => true,
		'unfiltered_html'               => true,
		'unfiltered_upload'             => true,
	];

	$librarian_base = array_merge( $base_wp, [ 'manage_options' ] ); // TODO: Grant access to save book review options without granting admin manage_options caps.

	// Base caps for existing roles.
	$base_caps = [
		'add_book-reviews',
		'publish_book-reviews',
		'edit_book-reviews',
		'read_book-reviews',
		'edit_published_book-reviews',
		'delete_published_book-reviews',
		'delete_book-reviews',
	];

	// Update caps for other existing roles.
	$editor_caps = array_merge( $base_caps, [ 'edit_others_book-reviews' ] );
	$admin_caps = array_merge( $editor_caps, [ 'manage_book_review_options' ] );

	// Mash all the caps together.
	$caps = [
		'author'        => $base_caps,
		'editor'        => $editor_caps,
		'administrator' => $admin_caps,
		'librarian'     => array_merge( $librarian_base, $admin_caps ),
		'book-reviewer' => array_merge( $base_wp, $base_caps ),
	];

	return $caps[ $role ];
}

/**
 * Add new roles.
 *
 * @since 2.0.0-alpha
 */
function add_roles() {
	$roles = [
		'librarian'     => esc_html__( 'Librarian', 'book-review-library' ),
		'book-reviewer' => esc_html__( 'Book Reviewer', 'book-review-library' ),
	];

	foreach ( $roles as $role => $name ) {
		add_new_role( $role, $name );
	}
}

/**
 * 	Add new caps to existing roles.
 *
 * @since 2.0.0-alpha
 */
function add_caps() {
	foreach ( [ 'author', 'editor', 'administrator' ] as $role ) {
		add_new_caps( $role );
	}
}

/**
 * Remove Book Review Library capabilities for a specific role.
 *
 * @since 2.0.0-alpha
 * @param string $role The role to remove caps from.
 */
function remove_new_caps( $role ) {
	$caps = get_caps_for( $role );

	$user_role = get_role( $role );
	if ( $user_role ) {
		$user_role->remove_cap( $caps );
	}
}

/**
 * Remove Book Review Library capabilities from all user roles.
 *
 * @since 2.0.0-alpha
 */
function remove_caps() {
	foreach ( [ 'librarian', 'book-reviewer', 'author', 'editor', 'administrator' ] as $role ) {
		remove_new_caps( $role );
	}
}

/**
 * Remove Book Review Library new roles.
 *
 * @since 2.0.0-alpha
 */
function remove_roles() {
	foreach ( [ 'librarian', 'book-reviewer' ] as $role ) {
		remove_role( $role );
	}
}

/**
 * Delete ratings
 *
 * @since 2.0.0-alpha
 */
function delete_ratings() {
	wp_delete_term( '0', 'rating' );
	wp_delete_term( '1', 'rating' );
	wp_delete_term( '2', 'rating' );
	wp_delete_term( '3', 'rating' );
	wp_delete_term( '4', 'rating' );
	wp_delete_term( '5', 'rating' );
}
