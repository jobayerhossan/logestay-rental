<?php
/**
 * CPT: City (Destination)
 *
 * @package logestay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {

	$labels = [
		'name'               => __( 'Cities', 'logestay' ),
		'singular_name'      => __( 'City', 'logestay' ),
		'add_new'            => __( 'Add New', 'logestay' ),
		'add_new_item'       => __( 'Add New City', 'logestay' ),
		'edit_item'          => __( 'Edit City', 'logestay' ),
		'new_item'           => __( 'New City', 'logestay' ),
		'view_item'          => __( 'View City', 'logestay' ),
		'search_items'       => __( 'Search Cities', 'logestay' ),
		'not_found'          => __( 'No cities found.', 'logestay' ),
		'not_found_in_trash' => __( 'No cities found in Trash.', 'logestay' ),
		'menu_name'          => __( 'Cities', 'logestay' ),
	];

	register_post_type( 'logestay_city', [
		'labels'             => $labels,
		'public'             => true,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-location-alt',
		'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields' ],
		'has_archive'        => true,
		'rewrite'            => [ 'slug' => 'cities' ],
		'show_in_menu'       => true,
	] );
} );

add_action( 'init', function () {

	// City meta
	register_post_meta( 'logestay_city', 'logestay_city_subtitle', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest'      => true,
		'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
	] );

	register_post_meta( 'logestay_city', 'logestay_city_lat', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field', // (store as string; easier for admin input)
		'show_in_rest'      => true,
		'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
	] );

	register_post_meta( 'logestay_city', 'logestay_city_lng', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest'      => true,
		'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
	] );

	register_post_meta( 'logestay_city', 'logestay_city_map_zoom', [
		'type'              => 'integer',
		'single'            => true,
		'sanitize_callback' => function ( $v ) { return absint( $v ); },
		'show_in_rest'      => true,
		'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
	] );

	/**
	 * Nearby Places Repeater (stored as an array)
	 * Each item:
	 * - name, distance, maps_url, type, image_id
	 */
	register_post_meta( 'logestay_city', 'logestay_city_nearby_places', [
		'type'              => 'array',
		'single'            => true,
		'sanitize_callback' => function ( $value ) {
			if ( ! is_array( $value ) ) {
				return [];
			}

			$out = [];
			foreach ( $value as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$out[] = [
					'name'     => isset( $row['name'] ) ? sanitize_text_field( $row['name'] ) : '',
					'distance' => isset( $row['distance'] ) ? sanitize_text_field( $row['distance'] ) : '',
					'maps_url' => isset( $row['maps_url'] ) ? esc_url_raw( $row['maps_url'] ) : '',
					'type'     => isset( $row['type'] ) ? sanitize_text_field( $row['type'] ) : '',
					'image_id' => isset( $row['image_id'] ) ? absint( $row['image_id'] ) : 0,
				];
			}
			return $out;
		},
		'show_in_rest'      => [
			'schema' => [
				'type'  => 'array',
				'items' => [
					'type'       => 'object',
					'properties' => [
						'name'     => [ 'type' => 'string' ],
						'distance' => [ 'type' => 'string' ],
						'maps_url' => [ 'type' => 'string' ],
						'type'     => [ 'type' => 'string' ],
						'image_id' => [ 'type' => 'integer' ],
					],
				],
			],
		],
		'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
	] );
} );
