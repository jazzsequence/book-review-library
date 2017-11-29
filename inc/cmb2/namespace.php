<?php
/**
 * CMB2
 *
 * CMB2 helper functions.
 *
 * @since 2.0.0-alpha
 *
 * @package BookReview
 */

namespace BookReview\CMB2;
use BookReview\Options as Options;

/**
 * Helper for adding CMB2 boxes. Mostly used for taxonomy metaboxes, but can be used for other CMB2 metaboxes as well.
 *
 * @since 2.0.0-alpha
 * @param array $args Array of arguments. The 'metabox_id' parameter is required.
 */
function add_cmb2_box( $args = [] ) {
	// Bail if we weren't passed a taxonomy for a taxonomy meta box.
	if ( empty( $args['metabox_id'] ) ) {
		return;
	}

	$args['priority']     = empty( $args['priority'] ) ? 'default' : $args['priority'];
	$args['context']      = empty( $args['context'] ) ? 'side' : $args['context'];
	$args['object_types'] = empty( $args['object_types'] ) ? [ 'book-review' ] : $args['object_types'];
	$args['show_names']   = empty( $args['show_names'] ) ? true : $args['show_names'];
	$args['title']        = empty( $args['title'] ) ? $args['singular'] : $args['title'];

	$cmb = \new_cmb2_box( [
		'id'           => $args['metabox_id'],
		'title'        => $args['title'],
		'object_types' => $args['object_types'],
		'context'      => $args['context'],
		'priority'     => $args['priority'],
		'show_names'   => $args['show_names'],
	] );

	foreach ( $args['fields'] as $field => $args ) {
		$cmb->add_field( cmb2_field( $args ) );
	}
}

/**
 * Helper for adding CMB2 fields.
 *
 * @since  2.0.0-alpha
 * @param  array $args Array of all arguments.
 * @return array       Array of field arguments.
 */
function cmb2_field( $args = [] ) {
	$field_args['type']         = empty( $args['type'] ) ? 'taxonomy_multicheck' : $args['type'];
	$field_args['id']           = empty( $args['id'] ) ? '_br_' . $args['slug'] : $args['id'];

	$taxonomy = ( false !== strpos( $field_args['type'], 'taxonomy_' ) ) ? true : false;

	if ( $taxonomy ) {
		// Use the plural form of the taxonomy for the "no terms found" text, if a plural form exists. Otherwise improvise from the singular.
		if ( ! empty( $args['plural'] ) && empty( $args['no_terms'] ) ) {
			// Translators: %s is the plural form of the taxonomy.
			$args['no_terms'] = sprintf( esc_html__( 'No %s have been added', 'book-review-library' ), strtolower( $args['plural'] ) );
		} elseif ( empty( $args['plural'] ) && empty( $args['no_terms'] ) ) {
			// Translators: %s is the singlular form of the taxonomy, with an "s" added to make it plural.
			$args['no_terms'] = sprintf( esc_html__( 'No %ss have been added', 'book-review-library' ), strtolower( $args['singular'] ) );
		}

		$field_args['title']             = empty( $args['title'] ) ? $args['singular'] : $args['title'];
		$field_args['taxonomy']          = $args['slug'];
		$field_args['select_all_button'] = false;
		$field_args['show_on_cb']        = Options\is_option_enabled( $args['slug'] );
		$field_args['text']              = [
			'no_terms_text' => $args['no_terms'],
		];
		// Translators: 3: Singular taxonomy name.
		$field_args['after_field']       = sprintf( '<span class="add-new-%1$s"><a href="%2$s">' . __( 'Add a new %3$s', 'book-review-library' ) . '</a></span>', $args['slug'], sprintf( 'edit-tags.php?taxonomy=%s&post_type=book-review', $args['slug'] ), $args['singular'] );
	}

	return $field_args;
}
