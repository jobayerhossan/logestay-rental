<?php
/**
 * CPT: Booking
 *
 * @package logestay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {

	$labels = [
		'name'          => __( 'Bookings', 'logestay' ),
		'singular_name' => __( 'Booking', 'logestay' ),
		'menu_name'     => __( 'Bookings', 'logestay' ),
	];

	register_post_type( 'logestay_booking', [
		'labels'             => $labels,
		'public'             => false,
		'show_ui'            => true,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-calendar-alt',
		'supports'           => [ 'title', 'revisions' ],
		'capability_type'    => 'post',
		'map_meta_cap'       => true,
	] );
} );

add_action( 'init', function () {

	$auth = function () { return current_user_can( 'edit_posts' ); };

	// Core
	register_post_meta( 'logestay_booking', 'logestay_booking_listing_id', [
		'type'              => 'integer',
		'single'            => true,
		'sanitize_callback' => 'absint',
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	foreach ( [ 'logestay_check_in', 'logestay_check_out' ] as $key ) {
		register_post_meta( 'logestay_booking', $key, [
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => function ( $v ) {
				$v = sanitize_text_field( $v );
				// allow YYYY-mm-dd only
				return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $v ) ? $v : '';
			},
			'show_in_rest'      => true,
			'auth_callback'     => $auth,
		] );
	}

	// Guests
	foreach ( [ 'logestay_adults', 'logestay_children', 'logestay_pets' ] as $key ) {
		register_post_meta( 'logestay_booking', $key, [
			'type'              => 'integer',
			'single'            => true,
			'sanitize_callback' => 'absint',
			'show_in_rest'      => true,
			'auth_callback'     => $auth,
		] );
	}

	// Guest details
	register_post_meta( 'logestay_booking', 'logestay_guest_name', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	register_post_meta( 'logestay_booking', 'logestay_guest_email', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_email',
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	register_post_meta( 'logestay_booking', 'logestay_guest_phone', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	register_post_meta( 'logestay_booking', 'logestay_special_requests', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_textarea_field',
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	// Payment
	register_post_meta( 'logestay_booking', 'logestay_payment_method', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => function ( $v ) {
			$allowed = [ 'card', 'paypal', 'bank', 'cash', 'link' ];
			$v = sanitize_text_field( $v );
			return in_array( $v, $allowed, true ) ? $v : '';
		},
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	register_post_meta( 'logestay_booking', 'logestay_payment_type', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => function ( $v ) {
			$allowed = [ 'instant', 'deferred' ];
			$v = sanitize_text_field( $v );
			return in_array( $v, $allowed, true ) ? $v : 'deferred';
		},
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	register_post_meta( 'logestay_booking', 'logestay_payment_status', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => function ( $v ) {
			$allowed = [ 'pending', 'paid', 'failed', 'refunded' ];
			$v = sanitize_text_field( $v );
			return in_array( $v, $allowed, true ) ? $v : 'pending';
		},
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	register_post_meta( 'logestay_booking', 'logestay_payment_txn_id', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	register_post_meta( 'logestay_booking', 'logestay_total_amount', [
		'type'              => 'number',
		'single'            => true,
		'sanitize_callback' => function ( $v ) { return is_numeric( $v ) ? (float) $v : 0; },
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	register_post_meta( 'logestay_booking', 'logestay_currency', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	// Status + hold expiry
	register_post_meta( 'logestay_booking', 'logestay_booking_status', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => function ( $v ) {
			$allowed = [ 'pending', 'confirmed', 'canceled', 'expired' ];
			$v = sanitize_text_field( $v );
			return in_array( $v, $allowed, true ) ? $v : 'pending';
		},
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	register_post_meta( 'logestay_booking', 'logestay_hold_expires_at', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field', // ISO datetime
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );

	register_post_meta( 'logestay_booking', 'logestay_created_at', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest'      => true,
		'auth_callback'     => $auth,
	] );
} );
