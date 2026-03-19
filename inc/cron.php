<?php
if ( ! defined('ABSPATH') ) exit;

add_filter('cron_schedules', function($schedules){
	$schedules['logestay_5min'] = [
		'interval' => 5 * 60,
		'display'  => 'Every 5 minutes (LOGESTAY)',
	];
	return $schedules;
});

add_action('init', function(){
	if ( ! wp_next_scheduled('logestay_cron_expire_holds') ) {
		wp_schedule_event(time() + 60, 'logestay_5min', 'logestay_cron_expire_holds');
	}
});

add_action('logestay_cron_expire_holds', function(){
	logestay_expire_holds();
});


function logestay_expire_holds() {
	$now = current_time('mysql');

	$q = new WP_Query([
		'post_type'      => 'logestay_booking',
		'post_status'    => 'any',
		'posts_per_page' => 50,
		'fields'         => 'ids',
		'meta_query'     => [
			'relation' => 'AND',
			[
				'key'   => 'logestay_booking_status',
				'value' => 'pending',
			],
			[
				'key'     => 'logestay_hold_expires_at',
				'value'   => $now,
				'compare' => '<=',
				'type'    => 'DATETIME',
			],
		],
	]);

	foreach ( $q->posts as $booking_id ) {
		update_post_meta($booking_id, 'logestay_booking_status', 'expired');
		// optional: mark payment failed for instant methods
		// update_post_meta($booking_id, 'logestay_payment_status', 'failed');
	}
}