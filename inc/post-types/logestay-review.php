<?php
/**
 * CPT: Review
 *
 * @package logestay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {

	$labels = [
		'name'          => __( 'Reviews', 'logestay' ),
		'singular_name' => __( 'Review', 'logestay' ),
		'menu_name'     => __( 'Reviews', 'logestay' ),
	];

	register_post_type( 'logestay_review', [
		'labels'       => $labels,
		'public'       => true,
		'show_ui'      => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-star-filled',
		'supports'     => [ 'title', 'editor', 'thumbnail', 'revisions' ],
	] );
} );

add_action( 'init', function () {

	$auth = function () { return current_user_can( 'edit_posts' ); };

	register_post_meta( 'logestay_review', 'logestay_review_rating', [
		'type'              => 'number',
		'single'            => true,
		'sanitize_callback' => function ( $v ) {
			$v = is_numeric( $v ) ? (float) $v : 0;
			if ( $v < 0 ) { $v = 0; }
			if ( $v > 5 ) { $v = 5; }
			return $v;
		},
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	foreach ( [ 'logestay_review_guest_name', 'logestay_review_guest_country' ] as $key ) {
		register_post_meta( 'logestay_review', $key, [
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'auth_callback'     => $auth,
		] );
	}

	register_post_meta( 'logestay_review', 'logestay_review_date', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	
} );
