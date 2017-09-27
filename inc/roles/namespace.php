<?php
/**
 * User Roles.
 *
 * All the Book Review Library Roles and Capabilities.
 *
 * @since 2.0.0-alpha
 *
 * @package BookReview
 */

namespace BookReview\Roles;

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
