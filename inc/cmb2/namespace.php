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

/**
 * Helper for adding CMB2 boxes. Mostly used for taxonomy metaboxes, but can be used for other CMB2 metaboxes as well.
 *
 * @since 2.0.0-alpha
 * @param array   $args     Array of arguments. The 'slug' parameter is required.
 * @param boolean $taxonomy Whether this box is a taxonomy box. If it is, additional settings are passed to the CMB2 box.
 */
function add_cmb2_box( $args = [], $taxonomy = true ) {
	// Bail if we weren't passed a taxonomy for a taxonomy meta box.
	if ( empty( $args['slug'] ) ) {
		return;
	}

	$args['priority'] = empty( $args['priority'] ) ? 'default' : $args['priority'];
	$args['context']  = empty( $args['context'] ) ? 'side' : $args['context'];
	$args['type']     = empty( $args['type'] ) ? 'taxonomy_multicheck' : $args['type'];
	$args['name']     = empty( $args['name'] ) ? $args['singular'] : $args['name'];

	// Use the plural form of the taxonomy for the "no terms found" text, if a plural form exists. Otherwise improvise from the singular.
	if ( ! empty( $args['plural'] ) && empty( $args['no_terms'] ) ) {
		// Translators: %s is the plural form of the taxonomy.
		$args['no_terms'] = sprintf( esc_html__( 'No %s found', 'book-review-library' ), strtolower( $args['plural'] ) );
	} elseif ( empty( $args['plural'] ) && empty( $args['no_terms'] ) ) {
		// Translators: %s is the singlular form of the taxonomy, with an "s" added to make it plural.
		$args['no_terms'] = sprintf( esc_html__( 'No %ss found', 'book-review-library' ), strtolower( $args['singular'] ) );
	}

	$prefix = '_br_' . $args['slug'];

	$cmb = \new_cmb2_box( [
		'id'           => $prefix . '_metabox',
		'title'        => $args['name'],
		'object_types' => [ 'book-review' ],
		'context'      => $args['context'],
		'priority'     => $args['priority'],
	] );

	$field_args = [
		'id'   => $prefix . $args['slug'],
		'type' => $args['type'],
	];

	if ( $taxonomy ) {
		$field_args['taxonomy']          = $args['slug'];
		$field_args['select_all_button'] = false;
		$field_args['text']              = [
			'no_terms_text' => $args['no_terms'],
		];
	}

	$cmb->add_field( $field_args );
}
