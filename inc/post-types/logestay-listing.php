<?php
/**
 * CPT: Listing (Property)
 *
 * @package logestay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {

	$labels = [
		'name'               => __( 'Listings', 'logestay' ),
		'singular_name'      => __( 'Listing', 'logestay' ),
		'add_new_item'       => __( 'Add New Listing', 'logestay' ),
		'edit_item'          => __( 'Edit Listing', 'logestay' ),
		'view_item'          => __( 'View Listing', 'logestay' ),
		'search_items'       => __( 'Search Listings', 'logestay' ),
		'menu_name'          => __( 'Listings', 'logestay' ),
	];

	register_post_type( 'logestay_listing', [
		'labels'       => $labels,
		'public'       => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-admin-multisite',
		'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions',],
		'has_archive'  => true,
		'rewrite'      => [ 'slug' => 'listings' ],
	] );
} );

add_action( 'init', function () {

	$auth = function () { return current_user_can( 'edit_posts' ); };

	// Relationship to City CPT (post_id)
	register_post_meta( 'logestay_listing', 'logestay_city_id', [
		'type'              => 'integer',
		'single'            => true,
		'sanitize_callback' => function ( $v ) { return absint( $v ); },
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	// Gallery (array of attachment IDs)
	register_post_meta( 'logestay_listing', 'logestay_listing_gallery', [
		'type'              => 'array',
		'single'            => true,
		'sanitize_callback' => function ( $value ) {
			if ( ! is_array( $value ) ) { return []; }
			return array_values( array_filter( array_map( 'absint', $value ) ) );
		},
		'show_in_rest'      => [
			'schema' => [
				'type'  => 'array',
				'items' => [ 'type' => 'integer' ],
			],
		],
		'auth_callback'     => $auth,
	] );

	// Capacity
	foreach ( [
		'logestay_max_adults'   => 'integer',
		'logestay_max_children' => 'integer',
		'logestay_max_pets'     => 'integer',
		'logestay_min_nights'   => 'integer',
		'logestay_max_nights'   => 'integer',
	] as $key => $type ) {
		register_post_meta( 'logestay_listing', $key, [
			'type'              => $type,
			'single'            => true,
			'sanitize_callback' => function ( $v ) { return absint( $v ); },
			'show_in_rest'      => true,
			'auth_callback'     => $auth,
		] );
	}

	// Pricing
	register_post_meta( 'logestay_listing', 'logestay_price_per_night', [
		'type'              => 'number',
		'single'            => true,
		'sanitize_callback' => function ( $v ) { return is_numeric( $v ) ? (float) $v : 0; },
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	foreach ( [ 'logestay_cleaning_fee' ] as $key ) {
		register_post_meta( 'logestay_listing', $key, [
			'type'              => 'number',
			'single'            => true,
			'sanitize_callback' => function ( $v ) { return is_numeric( $v ) ? (float) $v : 0; },
			'show_in_rest'      => true,
			'auth_callback'     => $auth,
		] );
	}

	register_post_meta( 'logestay_listing', 'logestay_currency', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	// iCal sync URLs
	foreach ( [ 'logestay_ical_airbnb_url', 'logestay_ical_booking_url' ] as $key ) {
		register_post_meta( 'logestay_listing', $key, [
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'esc_url_raw',
			'show_in_rest'      => true,
			'auth_callback'     => $auth,
		] );
	}

	register_post_meta( 'logestay_listing', 'logestay_ical_last_sync', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field', // store ISO datetime string
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	// External booking links per listing
	foreach ( [ 'logestay_airbnb_link', 'logestay_booking_link' ] as $key ) {
		register_post_meta( 'logestay_listing', $key, [
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'esc_url_raw',
			'show_in_rest'      => true,
			'auth_callback'     => $auth,
		] );
	}
} );
