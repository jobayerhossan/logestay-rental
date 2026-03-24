<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Map WP booking payment_status into your email status buckets:
 * pending / paid / not_received
 *
 * NOTE: "failed" = "not_received" (as you requested)
 */
function logestay_email_map_payment_status(string $payment_status): string {
    $payment_status = strtolower(trim($payment_status));

    if ($payment_status === 'paid') {
        return 'paid';
    }

    if ($payment_status === 'failed') {
        return 'not_received';
    }

    // optional: refunded -> not_received or paid (choose what you want)
    if ($payment_status === 'refunded') {
        return 'not_received';
    }

    // default
    return 'pending';
}

function logestay_email_map_booking_status(string $booking_status): string {
    $booking_status = strtolower(trim($booking_status));

    // your 3 states: pending / confirmed / cancelled
    if ($booking_status === 'confirmed') return 'confirmed';
    if ($booking_status === 'canceled' || $booking_status === 'cancelled') return 'cancelled';
    if ($booking_status === 'expired') return 'cancelled'; // treat expired as cancelled for emails (optional)

    return 'pending';
}

/**
 * Build the template key:
 * {gateway}_{paymentState}_{bookingState}
 * Example: bank_pending_confirmed
 */
function logestay_email_template_key_from_booking(int $booking_id): string {
    $method = (string) get_post_meta($booking_id, 'logestay_payment_method', true);
    $method = $method ? strtolower($method) : 'bank';

    $payment_status = (string) get_post_meta($booking_id, 'logestay_payment_status', true);
    $booking_status = (string) get_post_meta($booking_id, 'logestay_booking_status', true);

    $p = logestay_email_map_payment_status($payment_status);
    $b = logestay_email_map_booking_status($booking_status);

    return "{$method}_{$p}_{$b}";
}

/**
 * Detect changes on save and fire a single hook.
 * This works when you update meta via admin metabox.
 */
add_action('save_post_logestay_booking', function($post_id, $post, $update){
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision($post_id) ) return;
    if ( ! current_user_can('edit_post', $post_id) ) return;

    // We only want to run after the metabox has saved values.
    // So do it late:
}, 99, 3);

/**
 * Hook that runs AFTER meta changes were saved.
 * We compare previous stored snapshot vs new snapshot.
 */
add_action('save_post_logestay_booking', function($post_id){
    static $ran = [];
    if ( isset($ran[$post_id]) ) return; // avoid double run
    $ran[$post_id] = true;



    // Read "current"
    $new_method = (string) get_post_meta($post_id, 'logestay_payment_method', true);
    $new_pay    = (string) get_post_meta($post_id, 'logestay_payment_status', true);
    $new_book   = (string) get_post_meta($post_id, 'logestay_booking_status', true);

    // Read "previous snapshot" (we store it ourselves)
    $prev = get_post_meta($post_id, '_logestay_email_snapshot', true);
    $prev = is_array($prev) ? $prev : [];

    $old_method = (string)($prev['payment_method'] ?? '');
    $old_pay    = (string)($prev['payment_status'] ?? '');
    $old_book   = (string)($prev['booking_status'] ?? '');

    $changed = (
        $new_method !== $old_method ||
        $new_pay    !== $old_pay ||
        $new_book   !== $old_book
    );



    // Update snapshot every time
    update_post_meta($post_id, '_logestay_email_snapshot', [
        'payment_method' => $new_method,
        'payment_status' => $new_pay,
        'booking_status' => $new_book,
        'updated_at'     => current_time('mysql'),
    ]);

    if ( ! $changed ) return;

    // Fire one hook with the computed template key
    $key = logestay_email_template_key_from_booking($post_id);


    /**
     * Example key: bank_pending_confirmed
     * You’ll map this to a template file and subject.
     */
    do_action('logestay_booking_state_email', $post_id, $key, [
        'old' => ['payment_method'=>$old_method,'payment_status'=>$old_pay,'booking_status'=>$old_book],
        'new' => ['payment_method'=>$new_method,'payment_status'=>$new_pay,'booking_status'=>$new_book],
    ]);


}, 100);

add_action('save_post_logestay_booking', function($post_id){
	$payment_status = (string) get_post_meta($post_id, 'logestay_payment_status', true);
	$booking_status = (string) get_post_meta($post_id, 'logestay_booking_status', true);

	if ($payment_status !== 'paid' || $booking_status !== 'confirmed') {
		return;
	}

	if (function_exists('logestay_get_or_create_keybox_code')) {
		logestay_get_or_create_keybox_code((int) $post_id);
	}
}, 110);
