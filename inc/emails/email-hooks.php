<?php
if ( ! defined('ABSPATH') ) exit;

function logestay_email_guest_first_name(string $full_name): string {
	$full_name = trim(wp_strip_all_tags($full_name));
	if ($full_name === '') {
		return '';
	}

	$parts = preg_split('/\s+/', $full_name);
	return (string) ($parts[0] ?? $full_name);
}

function logestay_email_format_date(string $date): string {
	$date = trim($date);
	if ($date === '') {
		return '';
	}

	$timestamp = strtotime($date);
	if (!$timestamp) {
		return $date;
	}

	return wp_date('d/m/Y', $timestamp, wp_timezone());
}

function logestay_email_payment_method_label(string $method): string {
	$map = [
		'card'   => 'Carte bancaire',
		'paypal' => 'PayPal',
		'bank'   => 'Virement bancaire',
		'cash'   => 'Paiement sur place',
		'link'   => 'Lien de paiement',
	];

	$method = strtolower(trim($method));
	return $map[$method] ?? ucfirst($method);
}

function logestay_email_property_address(int $listing_id, int $city_id): string {
	$candidates = [
		'logestay_property_address',
		'logestay_listing_address',
		'logestay_address',
		'_logestay_listing_location_title',
		'_logestay_listing_location_subtitle',
	];

	foreach ($candidates as $meta_key) {
		$value = trim((string) get_post_meta($listing_id, $meta_key, true));
		if ($value !== '') {
			return $value;
		}
	}

	if ($city_id) {
		return (string) get_the_title($city_id);
	}

	return '';
}

function logestay_email_subject_from_vars(array $vars, int $booking_id = 0): string {
	$method = logestay_email_norm_method($vars['payment_method'] ?? '');
	$pay    = logestay_email_norm_payment_status($vars['payment_status'] ?? '');
	$book   = logestay_email_norm_booking_status($vars['booking_status'] ?? '');

	// Client requirement: special subject for confirmed paid card/PayPal bookings.
	if (in_array($method, ['card', 'paypal'], true) && $pay === 'paid' && $book === 'confirmed') {
		$property_name  = trim((string) ($vars['listing_title'] ?? ''));
		$check_in_label = trim((string) ($vars['check_in_formatted'] ?? ($vars['check_in'] ?? '')));
		$check_out_label = trim((string) ($vars['check_out_formatted'] ?? ($vars['check_out'] ?? '')));

		return sprintf(
			'Réservation confirmée – %s du %s au %s',
			$property_name !== '' ? $property_name : sprintf('Booking #%d', $booking_id),
			$check_in_label,
			$check_out_label
		);
	}

	$subject_map = [
		'pending'      => __('Payment pending', 'logestay'),
		'paid'         => __('Payment confirmed', 'logestay'),
		'not_received' => __('Payment not received', 'logestay'),
	];

	$book_map = [
		'pending'   => __('Booking pending', 'logestay'),
		'confirmed' => __('Booking confirmed', 'logestay'),
		'cancelled' => __('Booking cancelled', 'logestay'),
	];

	return sprintf(
		'%s — %s%s',
		$subject_map[$pay] ?? __('Booking update', 'logestay'),
		$book_map[$book] ?? __('Booking update', 'logestay'),
		$booking_id ? sprintf(' (Booking #%d)', $booking_id) : ''
	);
}


/**
 * Normalize meta values to our 3x3 matrix.
 */
function logestay_email_norm_payment_status(string $status): string {
	$status = strtolower(trim($status));

	// your meta allows: pending, paid, failed, refunded
	if ($status === 'paid') return 'paid';
	if ($status === 'pending') return 'pending';

	// treat failed/refunded as not_received for now (matches screenshot intent)
	return 'not_received';
}

function logestay_email_norm_booking_status(string $status): string {
	$status = strtolower(trim($status));

	// your meta allows: pending, confirmed, canceled, expired
	if ($status === 'confirmed') return 'confirmed';
	if ($status === 'pending') return 'pending';

	// canceled/expired -> cancelled (UK spelling, matches templates)
	return 'cancelled';
}

function logestay_email_norm_method(string $method): string {
	$method = strtolower(trim($method));

	// ensure it matches folder names
	$allowed = ['bank','cash','link','card','paypal'];
	return in_array($method, $allowed, true) ? $method : '';
}

/**
 * Build scenario template path like:
 * scenarios/bank/pending-confirmed.php
 */
function logestay_email_scenario_template(array $vars): string {
	$method = logestay_email_norm_method($vars['payment_method'] ?? '');
	$pay    = logestay_email_norm_payment_status($vars['payment_status'] ?? '');
	$book   = logestay_email_norm_booking_status($vars['booking_status'] ?? '');

	return "scenarios/{$method}/{$pay}-{$book}.php";
}

/**
 * Helper: build booking email vars
 */
function logestay_email_vars_from_booking(int $booking_id): array {
	$listing_id = (int) get_post_meta($booking_id, 'logestay_booking_listing_id', true);
	$city_id    = $listing_id ? (int) get_post_meta($listing_id, 'logestay_city_id', true) : 0;

	$check_in  = (string) get_post_meta($booking_id, 'logestay_check_in', true);
	$check_out = (string) get_post_meta($booking_id, 'logestay_check_out', true);

	$total    = (float) get_post_meta($booking_id, 'logestay_total_amount', true);
	$currency = (string) (get_post_meta($booking_id, 'logestay_currency', true) ?: 'EUR');
	$currency = strtoupper(trim($currency));
	if ($currency === '') $currency = 'EUR';

	// Guests
	$adults   = (int) get_post_meta($booking_id, 'logestay_adults', true);
	$children = (int) get_post_meta($booking_id, 'logestay_children', true);
	$pets     = (int) get_post_meta($booking_id, 'logestay_pets', true);

	// Guest details
	$guest_name  = (string) get_post_meta($booking_id, 'logestay_guest_name', true);
	$guest_email = (string) get_post_meta($booking_id, 'logestay_guest_email', true);
	$guest_phone = (string) get_post_meta($booking_id, 'logestay_guest_phone', true);
	$guest_note  = (string) get_post_meta($booking_id, 'logestay_special_requests', true);

	// Status/meta
	$payment_method = (string) get_post_meta($booking_id, 'logestay_payment_method', true);
	$payment_status = (string) get_post_meta($booking_id, 'logestay_payment_status', true);
	$booking_status = (string) get_post_meta($booking_id, 'logestay_booking_status', true);
	$checkin_time   = $listing_id ? (string) get_post_meta($listing_id, 'logestay_checkin_time', true) : '';
	$checkout_time  = $listing_id ? (string) get_post_meta($listing_id, 'logestay_checkout_time', true) : '';

	// URLs / support
	$settings = get_option('logestay_settings', []);
	$settings = is_array($settings) ? $settings : [];
	$support_email = (string) (
		function_exists('logestay_get_option')
			? logestay_get_option('logestay_contact_email', get_option('admin_email'))
			: get_option('admin_email')
	);

	$site_url    = home_url('/');
	$account_url = home_url('/'); // later: client area URL
	$support_url = home_url('/'); // later: support page
	$pay_url     = home_url('/'); // later: resume payment / client area

	// Nice-to-have: logo (works in emails header)
	$custom_logo_id = (int) get_theme_mod('custom_logo');
	$logo_url = $custom_logo_id ? wp_get_attachment_image_url($custom_logo_id, 'full') : '';

	// Nights (optional helper)
	$nights = 0;
	if ($check_in && $check_out) {
		$ts_in  = strtotime($check_in);
		$ts_out = strtotime($check_out);
		if ($ts_in && $ts_out && $ts_out > $ts_in) {
			$nights = (int) round(($ts_out - $ts_in) / DAY_IN_SECONDS);
		}
	}

	// Payment reference (prefer stored meta if you create one later)
	$bank_reference = (string) get_post_meta($booking_id, 'logestay_payment_reference', true);
	if ($bank_reference === '') {
		$bank_reference = 'REF-' . $booking_id;
	}

	$guest_count = max(0, $adults) + max(0, $children);
	$property_address = logestay_email_property_address($listing_id, $city_id);
	$host_phone = '+33 1 42 86 83 26';

	$vars = [
		'booking_id' => $booking_id,
		'listing_id' => $listing_id,
		'city_id'    => $city_id,

		'listing_title' => $listing_id ? get_the_title($listing_id) : '',
		'city_name'     => $city_id ? get_the_title($city_id) : '',

		'check_in'  => $check_in,
		'check_out' => $check_out,
		'nights'    => $nights,

		'total'    => $total,
		'currency' => $currency,

		// Helpful formatted (optional)
		'total_formatted' => number_format_i18n($total, 2) . ' ' . $currency,

		// Guest info
		'guest_name'  => $guest_name,
		'guest_first_name' => logestay_email_guest_first_name($guest_name),
		'guest_email' => $guest_email,
		'guest_phone' => $guest_phone,
		'guest_note'  => $guest_note,

		'adults'   => max(0, $adults),
		'children' => max(0, $children),
		'pets'     => max(0, $pets),
		'guest_count' => $guest_count,

		// Status
		'payment_method' => $payment_method,
		'payment_method_label' => logestay_email_payment_method_label($payment_method),
		'payment_status' => $payment_status,
		'booking_status' => $booking_status,
		'checkin_time' => $checkin_time ?: '15:00',
		'checkout_time' => $checkout_time ?: '11:00',
		'check_in_formatted' => logestay_email_format_date($check_in),
		'check_out_formatted' => logestay_email_format_date($check_out),
		'property_address' => $property_address,
		'reservation_price' => number_format_i18n($total, 2) . ' ' . $currency,
		'host_phone' => $host_phone,

		// Links / support
		'account_url'   => $account_url,
		'support_email' => $support_email,
		'support_url'   => $support_url,
		'pay_url'       => $pay_url,
		'site_url'      => $site_url,

		// Brand
		'logo_url' => $logo_url,
	];

	/**
	 * Payment links / office / bank details
	 * (still hardcoded, later move to settings)
	 */
	$vars['bank_beneficiary'] = 'LOGESTAY SAS';
	$vars['bank_iban']        = 'FR76 1234 5678 9012 3456 7890 123';
	$vars['bank_bic']         = 'LOGEFRPP';
	$vars['bank_reference']   = $bank_reference;

	$vars['cash_office_name']    = 'LOGESTAY Office';
	$vars['cash_office_address'] = '12 Rue de la République, 31000 Toulouse';
	$vars['cash_hours']          = 'Mon-Fri: 9am-6pm | Sat: 10am-4pm';

	$vars['payment_link_note']   = "Un lien de paiement sécurisé vous a été envoyé.";

	return apply_filters('logestay_email_booking_vars', $vars, $booking_id);
}


function logestay_send_booking_status_email(int $booking_id): bool {
	$vars = logestay_email_vars_from_booking($booking_id);
	if (empty($vars['guest_email'])) return false;

	$template = logestay_email_scenario_template($vars);

	// fallback if missing
	$content = logestay_email_render_template($template, $vars);
	if ($content === '') {
		// fallback to a generic template if you want (optional)
		return false;
	}

	$subject = logestay_email_subject_from_vars($vars, $booking_id);

	$html = logestay_email_wrap($content, [
		'preheader' => $subject,
	]);

	return logestay_mail($vars['guest_email'], $subject, $html);
}

/**
 * Main function to send a booking email by template key
 */
function logestay_send_booking_email(int $booking_id, string $template_key): bool {
	$vars = logestay_email_vars_from_booking($booking_id);

	if ( empty($vars['guest_email']) ) return false;

	// Map template keys to template files + subject
	$map = [
		'payment_pending' => [
			'file' => 'payment-pending.php',
			'subject' => sprintf(__('Payment pending – Booking #%d', 'logestay'), $booking_id),
		],
		'payment_paid' => [
			'file' => 'payment-paid.php',
			'subject' => sprintf(__('Payment received – Booking #%d', 'logestay'), $booking_id),
		],
		'payment_cancelled' => [
			'file' => 'payment-cancelled.php',
			'subject' => sprintf(__('Payment cancelled – Booking #%d', 'logestay'), $booking_id),
		],
		'offline_bank' => [
			'file' => 'offline-bank.php',
			'subject' => sprintf(__('Bank transfer instructions – Booking #%d', 'logestay'), $booking_id),
		],
		'offline_cash' => [
			'file' => 'offline-cash.php',
			'subject' => sprintf(__('Cash payment instructions – Booking #%d', 'logestay'), $booking_id),
		],
		'offline_payment_link' => [
			'file' => 'offline-payment-link.php',
			'subject' => sprintf(__('Payment link – Booking #%d', 'logestay'), $booking_id),
		],
	];

	$config = $map[$template_key] ?? null;
	if (!$config) return false;

	$subject = apply_filters('logestay_email_subject', $config['subject'], $template_key, $booking_id, $vars);

	$content = logestay_email_render_template($config['file'], $vars);
	$html    = logestay_email_wrap($content, [
		'preheader' => $subject,
	]);

	$html = apply_filters('logestay_email_html', $html, $template_key, $booking_id, $vars);

	return logestay_mail($vars['guest_email'], $subject, $html);
}

/**
 * WooCommerce-like hook points (call these when statuses change)
 *
 * You will call these in your booking logic:
 * do_action('logestay_booking_payment_pending', $booking_id);
 * do_action('logestay_booking_payment_paid', $booking_id);
 * etc.
 */
add_action('logestay_booking_payment_pending', function($booking_id){
	logestay_send_booking_status_email((int)$booking_id);
});

add_action('logestay_booking_payment_paid', function($booking_id){
	logestay_send_booking_status_email((int)$booking_id);
});

add_action('logestay_booking_payment_cancelled', function($booking_id){
	logestay_send_booking_status_email((int)$booking_id);
});

add_action('logestay_booking_offline_bank', function($booking_id){
	logestay_send_booking_status_email((int)$booking_id);
});

add_action('logestay_booking_offline_cash', function($booking_id){
	logestay_send_booking_status_email((int)$booking_id);
});

add_action('logestay_booking_offline_payment_link', function($booking_id){
	logestay_send_booking_status_email((int)$booking_id);
});


function logestay_email_template_config(string $key, int $booking_id = 0): array {
	// key format: {gateway}_{paymentState}_{bookingState}
	// Example: bank_pending_confirmed
	$parts  = explode('_', $key, 3);
	$method = logestay_email_norm_method($parts[0] ?? '');
	$pay    = logestay_email_norm_payment_status($parts[1] ?? '');
	$book   = logestay_email_norm_booking_status($parts[2] ?? '');

	// ✅ IMPORTANT: your templates are in scenarios/{method}/{pay}-{book}.php
	$file = "scenarios/{$method}/{$pay}-{$book}.php";

	$subject = logestay_email_subject_from_vars(logestay_email_vars_from_booking($booking_id), $booking_id);

	return [
		'template' => $file,
		'subject'  => $subject,
	];
}


add_action('logestay_booking_state_email', function(int $booking_id, string $key, array $ctx){

	$vars = function_exists('logestay_email_vars_from_booking')
		? logestay_email_vars_from_booking($booking_id)
		: [];

	$to = (string)($vars['guest_email'] ?? '');
	if ( ! $to ) return;

	$cfg = logestay_email_template_config($key, $booking_id);

	// Render scenario template (scenario includes the correct layout file internally)
	$body = logestay_email_render_template($cfg['template'], [
		'vars' => $vars, // ✅ your scenario files expect $vars (array)
	] + $vars);

	if ( ! $body ) return;

	// ✅ Wrap with header/footer like your other sender does
	$html = logestay_email_wrap($body, [
		'preheader' => $cfg['subject'],
	]);

	logestay_mail($to, $cfg['subject'], $html);

}, 10, 3);


function logestay_trigger_booking_state_email(int $booking_id, string $source = ''): void {

  if ( ! function_exists('logestay_email_template_key_from_booking') ) return;

  $key = logestay_email_template_key_from_booking($booking_id);

  do_action('logestay_booking_state_email', $booking_id, $key, [
    'source' => $source,
    'new' => [
      'payment_method' => (string) get_post_meta($booking_id, 'logestay_payment_method', true),
      'payment_status' => (string) get_post_meta($booking_id, 'logestay_payment_status', true),
      'booking_status' => (string) get_post_meta($booking_id, 'logestay_booking_status', true),
    ],
    'old' => [],
  ]);

  // prevent save_post from immediately re-sending
  update_post_meta($booking_id, '_logestay_email_snapshot', [
    'payment_method' => (string) get_post_meta($booking_id, 'logestay_payment_method', true),
    'payment_status' => (string) get_post_meta($booking_id, 'logestay_payment_status', true),
    'booking_status' => (string) get_post_meta($booking_id, 'logestay_booking_status', true),
    'updated_at'     => current_time('mysql'),
  ]);
}
