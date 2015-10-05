<?php
/**
 * Book Review Library Roles.
 *
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPLv3
 * @link      http://chrisreynolds.io
 * @copyright 2015 Chris Reynolds
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Book_Review_Roles' ) ) {

	/**
	 * Book Review Library roles and capabilities.
	 */
	class Book_Review_Roles {

		/**
		 * Construct function to get things started.
		 */
		public function __construct() {
		}

		/**
		 * Run our hooks
		 */
		public function do_hooks() {
		}

		/**
		 * Add Book Review Library user roles.
		 * @since  1.6
		 * @return void
		 */
		public function add_roles() {
			// Add the Librarian role.
			$result = add_role( 'librarian', __( 'Librarian', 'book-review-library' ), array(
				// Core WordPress caps.
				'read' => true,
				'delete_posts' => true,
				'delete_published_posts' => true,
				'edit_posts' => true,
				'edit_published_posts' => true,
				'publish_posts' => true,
				'upload_files' => true,
				'unfiltered_html' => true,
				'unfiltered_upload' => true,
				'manage_options' => true, // Temporary fix for permissions to save book review options.
				'manage_book_review_options' => true,
				// Book Review-speicifc caps.
				'publish_book-reviews' => true,
				'edit_book-reviews' => true,
				'edit_published_book-reviews' => true,
				'delete_book-reviews' => true,
				'delete_published_book-reviews' => true,
				'read_book-reviews' => true,
				'edit_others_book-reviews' => true,
				'delete_others_book-reviews' => true,
			) );


			// Add the Book Reviewer role.
			$result = add_role( 'book-reviewer', __( 'Book Reviewer', 'book-review-library' ), array(
				// Core WordPress caps.
				'read' => true,
				'delete_posts' => true,
				'delete_published_posts' => true,
				'edit_posts' => true,
				'edit_published_posts' => true,
				'publish_posts' => true,
				'upload_files' => true,
				'unfiltered_html' => true,
				'unfiltered_upload' => true,
				// Book Review-specific caps.
				'publish_book-reviews' => true,
				'edit_book-reviews' => true,
				'edit_published_book-reviews' => true,
				'delete_book-reviews' => true,
				'delete_published_book-reviews' => true,
				'read_book-reviews' => true,
			) );
		}

		/**
		 * Add Book Review Library capabilities to existing user roles.
		 * @since  1.6
		 * @return void
		 */
		public function add_caps() {
			// Add book-reviews caps to authors.
			if ( get_role( 'author' ) ) {
				$role = get_role( 'author' );
				$role->add_cap( 'add_book-reviews' );
				$role->add_cap( 'publish_book-reviews' );
				$role->add_cap( 'edit_book-reviews' );
				$role->add_cap( 'read_book-reviews' );
				$role->add_cap( 'edit_published_book-reviews' );
				$role->add_cap( 'delete_published_book-reviews' );
				$role->add_cap( 'delete_book-reviews' );
			}

			// Add book-reviews caps to editors.
			if ( get_role( 'editor' ) ) {
				$role = get_role( 'editor' );
				$role->add_cap( 'add_book-reviews' );
				$role->add_cap( 'publish_book-reviews' );
				$role->add_cap( 'edit_book-reviews' );
				$role->add_cap( 'edit_others_book-reviews' );
				$role->add_cap( 'read_book-reviews' );
				$role->add_cap( 'edit_published_book-reviews' );
				$role->add_cap( 'delete_published_book-reviews' );
				$role->add_cap( 'delete_book-reviews' );
			}

			// Add book-reviews caps to admins.
			if ( get_role( 'administrator' ) ) {
				$role = get_role( 'administrator' );
				$role->add_cap( 'add_book-reviews' );
				$role->add_cap( 'publish_book-reviews' );
				$role->add_cap( 'edit_book-reviews' );
				$role->add_cap( 'edit_others_book-reviews' );
				$role->add_cap( 'read_book-reviews' );
				$role->add_cap( 'edit_published_book-reviews' );
				$role->add_cap( 'delete_published_book-reviews' );
				$role->add_cap( 'delete_book-reviews' );
				$role->add_cap( 'manage_book_review_options' );
			}
		}

		/**
		 * Remove Book Review Library user roles.
		 * @since  1.6
		 * @return void
		 */
		public function remove_roles() {
			// Remove Librarian role and all caps.
			if ( get_role( 'librarian' ) ) {
				$role = get_role( 'librarian' );
				$role->remove_cap( 'delete_published_book-reviews' );
				$role->remove_cap( 'edit_published_book-reviews' );
				$role->remove_cap( 'publish_book-reviews' );
				$role->remove_cap( 'edit_book-reviews' );
				$role->remove_cap( 'delete_book-reviews' );
				$role->remove_cap( 'read_book-reviews' );
				$role->remove_cap( 'edit_others_book-reviews' );
				$role->remove_cap( 'edit_minute' );
				$role->remove_cap( 'manage_book_review_options' );
				$role->remove_cap( 'manage_options' );
				remove_role( 'librarian' );
			}

			// Remove Book Reviewer role and all caps.
			if ( get_role( 'book-reviewer' ) ) {
				$role = get_role( 'book-reviewer' );
				$role->remove_cap( 'add_book-reviews' );
				$role->remove_cap( 'delete_published_book-reviews' );
				$role->remove_cap( 'edit_published_book-reviews' );
				$role->remove_cap( 'publish_book-reviews' );
				$role->remove_cap( 'edit_book-reviews' );
				$role->remove_cap( 'delete_book-reviews' );
				$role->remove_cap( 'read_book-reviews' );
				remove_role( 'book-reviewer' );
			}
		}

		/**
		 * Remove Book Review Library capabilities from existing user roles.
		 * @since  1.6
		 * @return void
		 */
		public function remove_caps() {

			// Remove Book Review Library capabilities from Authors.
			if ( get_role( 'author' ) ) {
				$role = get_role( 'author' );
				$role->remove_cap( 'add_agenda' );
				$role->remove_cap( 'publish_book-reviews' );
				$role->remove_cap( 'edit_book-reviews' );
				$role->remove_cap( 'read_book-reviews' );
				$role->remove_cap( 'edit_published_book-reviews' );
				$role->remove_cap( 'delete_published_book-reviews' );
				$role->remove_cap( 'delete_book-reviews' );
			}

			// Remove Book Review Library capabilities from Editors.
			if ( get_role( 'editor' ) ) {
				$role = get_role( 'editor' );
				$role->remove_cap( 'add_agenda' );
				$role->remove_cap( 'add_book-reviews' );
				$role->remove_cap( 'publish_book-reviews' );
				$role->remove_cap( 'edit_book-reviews' );
				$role->remove_cap( 'edit_others_book-reviews' );
				$role->remove_cap( 'read_book-reviews' );
				$role->remove_cap( 'edit_published_book-reviews' );
				$role->remove_cap( 'delete_published_book-reviews' );
				$role->remove_cap( 'delete_book-reviews' );
			}

			// Remove Book Review Library capabilities from Administrators.
			if ( get_role( 'administrator' ) ) {
				$role = get_role( 'administrator' );
				$role->remove_cap( 'add_agenda' );
				$role->remove_cap( 'add_book-reviews' );
				$role->remove_cap( 'publish_book-reviews' );
				$role->remove_cap( 'edit_book-reviews' );
				$role->remove_cap( 'edit_others_book-reviews' );
				$role->remove_cap( 'read_book-reviews' );
				$role->remove_cap( 'edit_published_book-reviews' );
				$role->remove_cap( 'delete_published_book-reviews' );
				$role->remove_cap( 'delete_book-reviews' );
				$role->remove_cap( 'manage_book_review_options' );
			}
		}

	}

	$_GLOBALS['Book_Review_Roles'] = new Book_Review_Roles;
	$_GLOBALS['Book_Review_Roles']->do_hooks();
}

/**
 * Optional wrapper function for calling this class
 */
function class_name() {
	return new Book_Review_Roles;
}