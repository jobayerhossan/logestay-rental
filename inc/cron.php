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
	logestay_send_due_arrival_instructions();
	logestay_send_due_checkout_reminders();
	logestay_send_due_post_stay_emails();
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

function logestay_send_due_arrival_instructions() {
	$now = current_time('timestamp');

	$q = new WP_Query([
		'post_type'      => 'logestay_booking',
		'post_status'    => 'any',
		'posts_per_page' => 100,
		'fields'         => 'ids',
		'meta_query'     => [
			'relation' => 'AND',
			[
				'key'   => 'logestay_booking_status',
				'value' => 'confirmed',
			],
			[
				'key'   => 'logestay_payment_status',
				'value' => 'paid',
			],
			[
				'key'     => 'logestay_arrival_instructions_sent_at',
				'compare' => 'NOT EXISTS',
			],
		],
	]);

	foreach ( $q->posts as $booking_id ) {
		$check_in = (string) get_post_meta($booking_id, 'logestay_check_in', true);
		if ($check_in === '') {
			continue;
		}

		$listing_id = (int) get_post_meta($booking_id, 'logestay_booking_listing_id', true);
		$checkin_time = $listing_id ? (string) get_post_meta($listing_id, 'logestay_checkin_time', true) : '';
		if ($checkin_time === '') {
			$checkin_time = '15:00';
		}

		$checkin_ts = strtotime($check_in . ' ' . $checkin_time);
		if (!$checkin_ts) {
			continue;
		}

		$send_at = $checkin_ts - DAY_IN_SECONDS;
		if ($now >= $send_at && $now < $checkin_ts) {
			logestay_send_arrival_instructions_email((int) $booking_id);
		}
	}
}

function logestay_send_due_checkout_reminders() {
	$now = current_time('timestamp');

	$q = new WP_Query([
		'post_type'      => 'logestay_booking',
		'post_status'    => 'any',
		'posts_per_page' => 100,
		'fields'         => 'ids',
		'meta_query'     => [
			'relation' => 'AND',
			[
				'key'   => 'logestay_booking_status',
				'value' => 'confirmed',
			],
			[
				'key'     => 'logestay_checkout_reminder_sent_at',
				'compare' => 'NOT EXISTS',
			],
		],
	]);

	foreach ( $q->posts as $booking_id ) {
		$check_out = (string) get_post_meta($booking_id, 'logestay_check_out', true);
		if ($check_out === '') {
			continue;
		}

		$listing_id = (int) get_post_meta($booking_id, 'logestay_booking_listing_id', true);
		$checkout_time = $listing_id ? (string) get_post_meta($listing_id, 'logestay_checkout_time', true) : '';
		if ($checkout_time === '') {
			$checkout_time = '11:00';
		}

		$checkout_ts = strtotime($check_out . ' ' . $checkout_time);
		if (!$checkout_ts) {
			continue;
		}

		$send_at = $checkout_ts - DAY_IN_SECONDS;
		if ($now >= $send_at && $now < $checkout_ts) {
			logestay_send_checkout_reminder_email((int) $booking_id);
		}
	}
}

function logestay_send_due_post_stay_emails() {
	$now = current_time('timestamp');

	$q = new WP_Query([
		'post_type'      => 'logestay_booking',
		'post_status'    => 'any',
		'posts_per_page' => 100,
		'fields'         => 'ids',
		'meta_query'     => [
			'relation' => 'AND',
			[
				'key'   => 'logestay_booking_status',
				'value' => 'confirmed',
			],
			[
				'key'     => 'logestay_post_stay_email_sent_at',
				'compare' => 'NOT EXISTS',
			],
		],
	]);

	foreach ( $q->posts as $booking_id ) {
		$check_out = (string) get_post_meta($booking_id, 'logestay_check_out', true);
		if ($check_out === '') {
			continue;
		}

		$listing_id = (int) get_post_meta($booking_id, 'logestay_booking_listing_id', true);
		$checkout_time = $listing_id ? (string) get_post_meta($listing_id, 'logestay_checkout_time', true) : '';
		if ($checkout_time === '') {
			$checkout_time = '11:00';
		}

		$checkout_ts = strtotime($check_out . ' ' . $checkout_time);
		if (!$checkout_ts) {
			continue;
		}

		$send_at = $checkout_ts + (2 * HOUR_IN_SECONDS);
		if ($now >= $send_at) {
			logestay_send_post_stay_email((int) $booking_id);
		}
	}
}
