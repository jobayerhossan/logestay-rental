<?php
/**
 * CPT: FAQ
 *
 * @package logestay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {

	$labels = [
		'name'          => __( 'FAQs', 'logestay' ),
		'singular_name' => __( 'FAQ', 'logestay' ),
		'menu_name'     => __( 'FAQ', 'logestay' ),
	];

	register_post_type( 'logestay_faq', [
		'labels'       => $labels,
		'public'       => true,
		'show_ui'      => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-editor-help',
		'supports'     => [ 'title', 'editor', 'page-attributes', 'revisions' ], // page-attributes => menu_order for sorting
	] );
} );

add_action( 'init', function () {

	register_post_meta( 'logestay_faq', 'logestay_faq_is_hidden', [
		'type'              => 'boolean',
		'single'            => true,
		'sanitize_callback' => function ( $v ) { return (bool) $v; },
		'show_in_rest'      => true,
		'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
	] );
} );
