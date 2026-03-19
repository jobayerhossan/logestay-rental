<?php
/**
 * Taxonomy: Amenity / Badge
 *
 * @package logestay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {

	$labels = [
		'name'          => __( 'Amenities', 'logestay' ),
		'singular_name' => __( 'Amenity', 'logestay' ),
		'menu_name'     => __( 'Amenities', 'logestay' ),
	];

	register_taxonomy( 'logestay_amenity', [ 'logestay_listing' ], [
		'labels'            => $labels,
		'public'            => true,
		'hierarchical'      => false,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => [ 'slug' => 'amenity' ],
	] );
} );

add_action( 'init', function () {

	$auth = function () { return current_user_can( 'manage_categories' ); };

	// Icon can be an attachment ID or a class name string, depending on your UI later.
	register_term_meta( 'logestay_amenity', 'logestay_amenity_icon', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	// Disable/hide amenity globally (without deleting term)
	register_term_meta( 'logestay_amenity', 'logestay_amenity_is_disabled', [
		'type'              => 'boolean',
		'single'            => true,
		'sanitize_callback' => function ( $v ) { return (bool) $v; },
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );
} );
