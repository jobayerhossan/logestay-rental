<?php 
function logestay_normalize_date( $date ) {
  return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ? $date : '';
}

/**
 * Get hold duration in HOURS based on payment method.
 * Defaults to 24 hours when not set / invalid.
 */
function logestay_get_hold_hours_by_payment(string $payment): int {

	$settings = get_option('logestay_settings', []);
	$settings = is_array($settings) ? $settings : [];

	$default_hours = 24;

	$map = [
		'bank' => 'logestay_bank_hold_time',
		'cash' => 'logestay_cash_hold_time',
		'link' => 'logestay_link_hold_time', // IMPORTANT: your key is "link" not "payment_link"
	];

	$key = $map[$payment] ?? '';

	// Only bank/cash/link are "hold" gateways. Others can use default.
	if (!$key) {
		return $default_hours;
	}

	$hours = isset($settings[$key]) ? (int) $settings[$key] : 0;

	// fallback if empty/invalid
	if ($hours <= 0) {
		$hours = $default_hours;
	}

	return $hours;
}

function logestay_is_available( $listing_id, $check_in, $check_out ) {

	$listing_id = (int) $listing_id;

	// 0) Blocked by imported iCal dates (listing meta)
	$blocked = get_post_meta($listing_id, 'logestay_blocked_dates', true);
	$blocked = is_array($blocked) ? $blocked : [];

	if (!empty($blocked)) {
		$start = strtotime($check_in);
		$end   = strtotime($check_out);

		// iCal DTEND is exclusive, same as your booking logic: check each day in [start, end)
		for ($t = $start; $t < $end; $t += DAY_IN_SECONDS) {
			$d = date('Y-m-d', $t);
			if (in_array($d, $blocked, true)) {
				return false;
			}
		}
	}

	// 1) Existing booking overlap check (pending+confirmed, pending only if hold not expired)
	$args = [
		'post_type'      => 'logestay_booking',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_query'     => [
			'relation' => 'AND',

			// Same listing
			[
				'key'   => 'logestay_booking_listing_id',
				'value' => $listing_id,
			],

			// Status blocks availability
			[
				'key'     => 'logestay_booking_status',
				'value'   => [ 'pending', 'confirmed' ],
				'compare' => 'IN',
			],

			// Pending only blocks if hold not expired OR confirmed always blocks
			[
				'relation' => 'OR',
				[
					'key'     => 'logestay_hold_expires_at',
					'value'   => current_time( 'mysql' ),
					'compare' => '>',
					'type'    => 'DATETIME',
				],
				[
					'key'     => 'logestay_booking_status',
					'value'   => 'confirmed',
				],
			],

			// Overlapping dates
			[
				'key'     => 'logestay_check_in',
				'value'   => $check_out,
				'compare' => '<',
				'type'    => 'DATE',
			],
			[
				'key'     => 'logestay_check_out',
				'value'   => $check_in,
				'compare' => '>',
				'type'    => 'DATE',
			],
		],
	];

	$q = new WP_Query( $args );

	return ! $q->have_posts();
}

add_action( 'wp_ajax_logestay_check_availability', 'logestay_ajax_check_availability' );
add_action( 'wp_ajax_nopriv_logestay_check_availability', 'logestay_ajax_check_availability' );

function logestay_ajax_check_availability() {

	check_ajax_referer( 'logestay_nonce', 'nonce' );

	$listing_id = absint( $_POST['listing_id'] ?? 0 );
	$check_in   = logestay_normalize_date( $_POST['start_date'] ?? '' );
	$check_out  = logestay_normalize_date( $_POST['end_date'] ?? '' );
	$payment    = sanitize_text_field( $_POST['payment']);
	$guest      = $_POST['guest'] ?? [];
	$settings   = get_option('logestay_settings', []);
	$settings   = is_array($settings) ? $settings : [];
	$link_url   = trim((string) ($settings['logestay_payment_link_url'] ?? ''));

	if ( ! $listing_id || ! $check_in || ! $check_out ) {
		wp_send_json_error( [ 'message' => 'Invalid booking data.' ] );
	}

	if ( $payment === 'link' && $link_url === '' ) {
		wp_send_json_error( [ 'message' => __('Payment link is not configured yet. Please choose another payment method or contact support.', 'logestay') ] );
	}

	$adults   = absint($guest['adults'] ?? 1);
	$children = absint($guest['children'] ?? 0);
	$pets     = absint($guest['pets'] ?? 0);

	$max_adults   = absint(get_post_meta($listing_id, 'logestay_max_adults', true));
	$max_children = absint(get_post_meta($listing_id, 'logestay_max_children', true));
	$max_pets     = absint(get_post_meta($listing_id, 'logestay_max_pets', true));



	if ($max_adults < 1) $max_adults = 1;

	if ($adults < 1) $adults = 1;

	if ($adults > $max_adults) {
		wp_send_json_error(['message' => __('Too many adults for this listing.', 'logestay')]);
	}
	if ($max_children >= 0 && $children > $max_children) {
		wp_send_json_error(['message' => __('Too many children for this listing.', 'logestay')]);
	}
	if ($max_pets >= 0 && $pets > $max_pets) {
		wp_send_json_error(['message' => __('Too many pets for this listing.', 'logestay')]);
	}

	// 1️⃣ Availability check
	if ( ! logestay_is_available( $listing_id, $check_in, $check_out ) ) {
		wp_send_json_error( [ 'message' => __('Dates are no longer available.', 'logestay') ] );
	}

	// 2️⃣ Create HOLD booking based on payment method settings
	$hold_hours = logestay_get_hold_hours_by_payment($payment);
	$expires_at = date( 'Y-m-d H:i:s', strtotime( "+{$hold_hours} hours", current_time( 'timestamp' ) ) );

	$booking_id = wp_insert_post( [
		'post_type'   => 'logestay_booking',
		'post_status' => 'publish',
		'post_title'  => sprintf(
			'Booking – Listing #%d – %s → %s',
			$listing_id,
			$check_in,
			$check_out
		),
	] );

	if ( is_wp_error( $booking_id ) ) {
		wp_send_json_error( [ 'message' => __('Could not create booking.', 'logestay') ] );
	}

	// 3️⃣ Save meta
	update_post_meta( $booking_id, 'logestay_booking_listing_id', $listing_id );
	update_post_meta( $booking_id, 'logestay_check_in', $check_in );
	update_post_meta( $booking_id, 'logestay_check_out', $check_out );

	update_post_meta( $booking_id, 'logestay_booking_status', 'pending' );
	update_post_meta( $booking_id, 'logestay_payment_method', $payment );
	update_post_meta( $booking_id, 'logestay_payment_status', 'pending' );
	update_post_meta( $booking_id, 'logestay_hold_expires_at', $expires_at );
	update_post_meta( $booking_id, 'logestay_created_at', current_time( 'mysql' ) );
	if ( $payment === 'link' && $link_url !== '' ) {
		update_post_meta( $booking_id, 'logestay_payment_link_url', esc_url_raw( $link_url ) );
	}

	// Guest details
	update_post_meta( $booking_id, 'logestay_guest_name', sanitize_text_field( $guest['name'] ?? '' ) );
	update_post_meta( $booking_id, 'logestay_guest_email', sanitize_email( $guest['email'] ?? '' ) );
	update_post_meta( $booking_id, 'logestay_guest_phone', sanitize_text_field( $guest['phone'] ?? '' ) );
	update_post_meta( $booking_id, 'logestay_special_requests', sanitize_textarea_field( $guest['note'] ?? '' ) );

	update_post_meta($booking_id, 'logestay_adults', $adults);
	update_post_meta($booking_id, 'logestay_children', $children);
	update_post_meta($booking_id, 'logestay_pets', $pets);


	// 4️⃣ Pricing (save for Stripe + summary)
	$price_per_night = (float) get_post_meta( $listing_id, 'logestay_price_per_night', true );
	$cleaning_fee    = (float) get_post_meta($listing_id, 'logestay_cleaning_fee', true);

	if ($price_per_night < 0) $price_per_night = 0;
	if ($cleaning_fee < 0)    $cleaning_fee = 0;

	// nights = check_out - check_in (check_out is not charged as a night)
	$in_ts  = strtotime( $check_in );
	$out_ts = strtotime( $check_out );
	$nights = ( $out_ts - $in_ts ) / DAY_IN_SECONDS;
	$nights = (int) round( $nights );

	if ( $nights < 1 ) {
		wp_send_json_error( [ 'message' => __('Invalid stay length.', 'logestay') ] );
	}

	$subtotal = $nights * $price_per_night;
	$total    = $subtotal + $cleaning_fee;


	// Currency: use listing currency if you store it, otherwise default EUR
	$currency = 'EUR';

	// Save to booking (this is what Stripe endpoint reads later)
	update_post_meta( $booking_id, 'logestay_total_amount', (float) $total );
	update_post_meta( $booking_id, 'logestay_currency', strtoupper( $currency ) );

	update_post_meta( $booking_id, 'logestay_cleaning_fee', $cleaning_fee );

	// 4️⃣ Response
	wp_send_json_success( [
		'booking_id'  => $booking_id,
		'expires_at'  => $expires_at,
		'cleaning_fee'    => $cleaning_fee,
		'redirect_url'=> logestay_payment_redirect_url( $booking_id, $payment ),
	] );
}



function logestay_payment_redirect_url( $booking_id, $payment ) {

	switch ( $payment ) {
		case 'card':
			return site_url( '/checkout/stripe?booking=' . $booking_id );

		case 'paypal':
			return site_url( '/checkout/paypal?booking=' . $booking_id );

		default:
			return ''; // offline → frontend success screen
	}
}



add_action('init', function(){
	add_rewrite_rule('^ical/listing/([0-9]+)\.ics/?$', 'index.php?logestay_ical_listing=$matches[1]', 'top');
	add_rewrite_tag('%logestay_ical_listing%', '([0-9]+)');
});

add_action('template_redirect', function(){
	$listing_id = absint(get_query_var('logestay_ical_listing'));
	if(!$listing_id) return;

	header('Content-Type: text/calendar; charset=utf-8');
	header('Content-Disposition: inline; filename="listing-'.$listing_id.'.ics"');

	echo logestay_generate_listing_ics($listing_id);
	exit;
});

function logestay_generate_listing_ics($listing_id){
	$bookings = logestay_get_blocking_bookings($listing_id);

	$out  = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//LOGESTAY//EN\r\nCALSCALE:GREGORIAN\r\n";
	foreach($bookings as $b){
		$uid = 'logestay-booking-'.$b['id'].'@'.parse_url(home_url(), PHP_URL_HOST);
		$dtstart = str_replace('-','',$b['check_in'])."T000000Z";
		$dtend   = str_replace('-','',$b['check_out'])."T000000Z";

		$out .= "BEGIN:VEVENT\r\n";
		$out .= "UID:{$uid}\r\n";
		$out .= "DTSTART:{$dtstart}\r\n";
		$out .= "DTEND:{$dtend}\r\n";
		$out .= "SUMMARY:Booked\r\n";
		$out .= "END:VEVENT\r\n";
	}
	$out .= "END:VCALENDAR\r\n";
	return $out;
}



function logestay_get_blocking_bookings($listing_id){
	$now = current_time('mysql');

	$q = new WP_Query([
		'post_type'=>'logestay_booking',
		'posts_per_page'=>-1,
		'fields'=>'ids',
		'meta_query'=>[
			'relation'=>'AND',
			['key'=>'logestay_booking_listing_id','value'=>absint($listing_id)],
			['key'=>'logestay_booking_status','value'=>['pending','confirmed'],'compare'=>'IN'],
			[
				'relation'=>'OR',
				[
					'key'=>'logestay_hold_expires_at',
					'value'=>$now,
					'compare'=>'>',
					'type'=>'DATETIME',
				],
				['key'=>'logestay_booking_status','value'=>'confirmed'],
			]
		]
	]);

	$items=[];
	foreach($q->posts as $id){
		$items[]=[
			'id'=>$id,
			'check_in'=>get_post_meta($id,'logestay_check_in',true),
			'check_out'=>get_post_meta($id,'logestay_check_out',true),
		];
	}
	return $items;
}



add_filter('manage_logestay_booking_posts_columns', function($cols){
	$new = [];
	$new['cb'] = $cols['cb'];
	$new['title'] = __('Booking','logestay');
	$new['listing'] = __('Listing','logestay');
	$new['dates'] = __('Dates','logestay');
	$new['status'] = __('Status','logestay');
	$new['payment'] = __('Payment','logestay');
	$new['expires'] = __('Hold expires','logestay');
	return $new;
});

add_action('manage_logestay_booking_posts_custom_column', function($col, $post_id){
	if($col === 'listing'){
		$lid = (int)get_post_meta($post_id,'logestay_booking_listing_id',true);
		echo $lid ? esc_html(get_the_title($lid)) . ' (#' . $lid . ')' : '—';
	}
	if($col === 'dates'){
		$in = get_post_meta($post_id,'logestay_check_in',true);
		$out = get_post_meta($post_id,'logestay_check_out',true);
		echo esc_html($in . ' → ' . $out);
	}
	if($col === 'status'){
		echo esc_html(get_post_meta($post_id,'logestay_booking_status',true) ?: '—');
	}
	if($col === 'payment'){
		$method = get_post_meta($post_id,'logestay_payment_method',true);
		$pstat  = get_post_meta($post_id,'logestay_payment_status',true);
		echo esc_html($method ?: '—') . '<br><small>' . esc_html($pstat ?: '—') . '</small>';
	}
	if($col === 'expires'){
		echo esc_html(get_post_meta($post_id,'logestay_hold_expires_at',true) ?: '—');
	}
}, 10, 2);


function logestay_stripe_get_secret_key(): string {
	$mode = logestay_get_option('logestay_stripe_mode', 'test');
	$mode = ($mode === 'live') ? 'live' : 'test';

	$key = ($mode === 'live')
		? logestay_get_option('logestay_stripe_live_sk', '')
		: logestay_get_option('logestay_stripe_test_sk', '');

	return (string) $key;
}

function logestay_stripe_mode(): string {
	$mode = logestay_get_option('logestay_stripe_mode', 'test');
	return ($mode === 'live') ? 'live' : 'test';
}

function logestay_stripe_webhook_secret(): string {
	$mode = logestay_stripe_mode();
	return (string) (
		$mode === 'live'
			? logestay_get_option('logestay_stripe_live_whsec', '')
			: logestay_get_option('logestay_stripe_test_whsec', '')
	);
}

add_action('wp_ajax_logestay_create_checkout', 'logestay_create_checkout');
add_action('wp_ajax_nopriv_logestay_create_checkout', 'logestay_create_checkout');

function logestay_create_checkout() {
	check_ajax_referer('logestay_nonce', 'nonce');

	$booking_id = absint($_POST['booking_id'] ?? 0);
	if (!$booking_id) {
		wp_send_json_error(['message' => __('Invalid booking', 'logestay')]);
	}

	// Validate booking status
	$status = get_post_meta($booking_id, 'logestay_booking_status', true);
	if ($status !== 'pending') {
		wp_send_json_error(['message' => __('Booking not pending', 'logestay')]);
	}

	// Validate hold
	$expires = get_post_meta($booking_id, 'logestay_hold_expires_at', true);
	if ($expires && strtotime($expires) <= current_time('timestamp')) {
		update_post_meta($booking_id, 'logestay_booking_status', 'expired');
		wp_send_json_error(['message' => 'Hold expired']);
	}

	$total    = (float) get_post_meta($booking_id, 'logestay_total_amount', true);
	$currency = strtolower(get_post_meta($booking_id, 'logestay_currency', true) ?: 'eur');

	if ($total <= 0) {
		wp_send_json_error(['message' => __('Invalid amount', 'logestay')]);
	}

	$amount = (int) round($total * 100);
	if ($amount < 50) {
		wp_send_json_error(['message' => __('Amount too small', 'logestay')]);
	}

	// Get Stripe secret key from settings
	$secret = logestay_stripe_get_secret_key();
	if (!$secret) {
		wp_send_json_error(['message' => __('Stripe secret key missing in settings', 'logestay')]);
	}

	do_action('logestay_booking_payment_pending', $booking_id);

	// Optional: nicer product name
	$listing_id = absint(get_post_meta($booking_id, 'logestay_booking_listing_id', true));
	$product_name = $listing_id ? get_the_title($listing_id) : ('Booking #' . $booking_id);

	$success_token = wp_generate_password(20, false, false); // token
	update_post_meta($booking_id, 'logestay_stripe_success_token', $success_token);

	$cancel_token = wp_generate_password(20, false, false);
	update_post_meta($booking_id, 'logestay_stripe_cancel_token', $cancel_token);



	$success_url = add_query_arg([
		'ls_booking' => $booking_id,
		'ls_status'  => 'success',
		'ls_token'     => $success_token,
	], home_url('/'));

	$cancel_url = add_query_arg([
		'ls_booking' => $booking_id,
		'ls_status'  => 'cancel',
		'ls_token'     => $cancel_token,
	], home_url('/'));

	// Stripe API expects "application/x-www-form-urlencoded"
	$body = [
		'mode' => 'payment',

		// payment method
		'payment_method_types[0]' => 'card',

		// line item
		'line_items[0][quantity]' => 1,
		'line_items[0][price_data][currency]' => $currency,
		'line_items[0][price_data][unit_amount]' => $amount,
		'line_items[0][price_data][product_data][name]' => $product_name,

		// metadata
		'metadata[booking_id]' => (string) $booking_id,
		'metadata[listing_id]' => (string) $listing_id,

		// urls
		'success_url' => $success_url,
		'cancel_url'  => $cancel_url,
	];

	$response = wp_remote_post('https://api.stripe.com/v1/checkout/sessions', [
		'timeout' => 20,
		'headers' => [
			'Authorization' => 'Bearer ' . $secret,
			'Content-Type'  => 'application/x-www-form-urlencoded',
		],
		'body' => $body,
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Stripe request failed: ' . $response->get_error_message()]);
	}

	$code = wp_remote_retrieve_response_code($response);
	$raw  = wp_remote_retrieve_body($response);
	$data = json_decode($raw, true);

	if ($code < 200 || $code >= 300 || empty($data['id']) || empty($data['url'])) {
		$msg = $data['error']['message'] ?? __('Stripe error', 'logestay');
		wp_send_json_error([
			'message' => $msg,
			'stripe_code' => $code,
		]);
	}

	// Save payment meta
	update_post_meta($booking_id, 'logestay_payment_method', 'card');
	update_post_meta($booking_id, 'logestay_payment_type', 'instant');
	update_post_meta($booking_id, 'logestay_booking_status', 'confirmed');
	update_post_meta($booking_id, 'logestay_payment_status', 'pending');
	update_post_meta($booking_id, 'logestay_payment_txn_id', sanitize_text_field($data['id']));


	wp_send_json_success([
		'checkout_url' => esc_url_raw($data['url']),
	]);
}




add_action('wp_ajax_logestay_confirm_offline_booking', 'logestay_confirm_offline_booking');
add_action('wp_ajax_nopriv_logestay_confirm_offline_booking', 'logestay_confirm_offline_booking');

function logestay_confirm_offline_booking() {
  check_ajax_referer('logestay_nonce', 'nonce');

  $booking_id = absint($_POST['booking_id'] ?? 0);
  $method = sanitize_text_field($_POST['payment_method']);

  if(!$booking_id) wp_send_json_error(['message'=>'Invalid booking']);

  $hold_hours   = logestay_get_hold_hours_by_payment($method); // bank/cash/link uses settings, otherwise default 24
	$hold_minutes = $hold_hours * 60;

	$expires_at = date(
		'Y-m-d H:i:s',
		current_time('timestamp') + ($hold_minutes * MINUTE_IN_SECONDS)
	);


  update_post_meta($booking_id, 'logestay_payment_method', $method);
  update_post_meta($booking_id, 'logestay_payment_type', 'deferred');
  update_post_meta($booking_id, 'logestay_payment_status', 'pending');
  update_post_meta($booking_id, 'logestay_booking_status', 'confirmed');
  update_post_meta( $booking_id, 'logestay_hold_expires_at', $expires_at );

    // ✅ Trigger email right away for website bookings (AJAX meta updates do not fire save_post)
  logestay_trigger_booking_state_email($booking_id, 'offline');

  wp_send_json_success(['ok'=>true]);
}


add_action('wp_ajax_logestay_get_listing_popup_data', 'logestay_get_listing_popup_data');
add_action('wp_ajax_nopriv_logestay_get_listing_popup_data', 'logestay_get_listing_popup_data');

function logestay_get_listing_popup_data() {
	check_ajax_referer('logestay_nonce', 'nonce');

	$listing_id = absint($_POST['listing_id'] ?? 0);
	if (!$listing_id) wp_send_json_error(['message' => __('Invalid listing id', 'logestay')]);

	$title = get_the_title($listing_id);

	$city_id = absint(get_post_meta($listing_id, 'logestay_city_id', true));
	$city_title = $city_id ? get_the_title($city_id) : '';

	$price_per_night = (float) get_post_meta($listing_id, 'logestay_price_per_night', true);

	$max_adults   = absint(get_post_meta($listing_id, 'logestay_max_adults', true));
	$max_children = absint(get_post_meta($listing_id, 'logestay_max_children', true));
	$max_pets     = absint(get_post_meta($listing_id, 'logestay_max_pets', true));
	$cleaning_fee = (float) get_post_meta($listing_id, 'logestay_cleaning_fee', true);
	$logestay_airbnb_link = get_post_meta($listing_id, 'logestay_airbnb_link', true);
	$logestay_booking_link = get_post_meta($listing_id, 'logestay_booking_link', true);

	$checkin_time  = get_post_meta($listing_id, 'logestay_checkin_time', true);
	$checkout_time = get_post_meta($listing_id, 'logestay_checkout_time', true);

	// validate HH:MM, otherwise fallback
	$checkin_time  = (is_string($checkin_time)  && preg_match('/^\d{2}:\d{2}$/', $checkin_time))  ? $checkin_time  : '15:00';
	$checkout_time = (is_string($checkout_time) && preg_match('/^\d{2}:\d{2}$/', $checkout_time)) ? $checkout_time : '11:00';

	// reasonable defaults if empty
	if ($max_adults < 1) $max_adults = 1;

	wp_send_json_success([
		'listing_id' => $listing_id,
		'title'      => $title,
		'city_id'    => $city_id,
		'city_title' => $city_title,
		'price_per_night' => $price_per_night,
		'cleaning_fee' => $cleaning_fee,
		'airbnbUrl' => $logestay_airbnb_link,
		'bookingUrl' => $logestay_booking_link,
		'checkin_time'  => $checkin_time,
		'checkout_time' => $checkout_time,
		'max' => [
			'adults'   => $max_adults,
			'children' => $max_children,
			'pets'     => $max_pets,
		],
	]);
}



add_action('wp_ajax_logestay_get_disabled_dates', 'logestay_get_disabled_dates');
add_action('wp_ajax_nopriv_logestay_get_disabled_dates', 'logestay_get_disabled_dates');

function logestay_get_disabled_dates() {
	if (
		! isset($_POST['nonce']) ||
		! wp_verify_nonce($_POST['nonce'], 'logestay_nonce')
	) {
		wp_send_json_error(['message' => __('Invalid nonce', 'logestay')]);
	}

	$listing_id = absint($_POST['listing_id'] ?? 0);
	if (! $listing_id) {
		wp_send_json_error(['message' => __('Invalid listing', 'logestay')]);
	}

	// 1) Start with imported iCal blocked dates
	$disabled = [];
	$blocked = get_post_meta($listing_id, 'logestay_blocked_dates', true);
	if (is_array($blocked) && !empty($blocked)) {
		$disabled = $blocked;
	}

	// 2) Add own bookings (pending only if not expired, confirmed always)
	$q = new WP_Query([
		'post_type'      => 'logestay_booking',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => [
			'relation' => 'AND',
			[
				'key'   => 'logestay_booking_listing_id',
				'value' => $listing_id,
			],
			[
				'key'     => 'logestay_booking_status',
				'value'   => ['pending', 'confirmed'],
				'compare' => 'IN',
			],
			[
				'relation' => 'OR',
				[
					'key'     => 'logestay_hold_expires_at',
					'value'   => current_time('mysql'),
					'compare' => '>',
					'type'    => 'DATETIME',
				],
				[
					'key'   => 'logestay_booking_status',
					'value' => 'confirmed',
				],
			],
		],
	]);

	foreach ($q->posts as $booking_id) {
		$start = get_post_meta($booking_id, 'logestay_check_in', true);
		$end   = get_post_meta($booking_id, 'logestay_check_out', true);

		if (! $start || ! $end) continue;

		$cur = strtotime($start);
		$endTs = strtotime($end);

		while ($cur < $endTs) {
			$disabled[] = date('Y-m-d', $cur);
			$cur = strtotime('+1 day', $cur);
		}
	}

	$disabled = array_values(array_unique($disabled));
	sort($disabled);

	wp_send_json_success([
		'dates' => $disabled,
	]);
}


add_action('wp_ajax_logestay_mark_paid', 'logestay_mark_paid');
add_action('wp_ajax_nopriv_logestay_mark_paid', 'logestay_mark_paid');

function logestay_mark_paid() {
  check_ajax_referer('logestay_nonce', 'nonce');

  $booking_id = absint($_POST['booking_id'] ?? 0);
  $token      = sanitize_text_field($_POST['token'] ?? '');

  if (!$booking_id || !$token) {
    wp_send_json_error(['message' => __('Invalid request', 'logestay')]);
  }

  $saved = get_post_meta($booking_id, 'logestay_stripe_success_token', true);
  if (!$saved || !hash_equals($saved, $token)) {
    wp_send_json_error(['message' => __('Invalid token', 'logestay')]);
  }

  // HOLD check (optional but good)
  $expires = get_post_meta($booking_id, 'logestay_hold_expires_at', true);
  if ($expires && strtotime($expires) <= current_time('timestamp')) {
    update_post_meta($booking_id, 'logestay_booking_status', 'expired');
    wp_send_json_error(['message' => 'Hold expired']);
  }

  // ✅ Mark paid (TEMP until webhook)
  update_post_meta($booking_id, 'logestay_booking_status', 'confirmed');
  update_post_meta($booking_id, 'logestay_payment_status', 'paid');
  update_post_meta($booking_id, 'logestay_payment_method', 'card');
  update_post_meta($booking_id, 'logestay_payment_type', 'instant');
  update_post_meta($booking_id, 'logestay_hold_expires_at', '');

  // Important: prevent token reuse
  delete_post_meta($booking_id, 'logestay_stripe_success_token');

  logestay_trigger_booking_state_email($booking_id, 'card cancelled');


  wp_send_json_success(['message' => 'Booking marked as paid']);
}


add_action('wp_ajax_logestay_cancel_booking', 'logestay_cancel_booking');
add_action('wp_ajax_nopriv_logestay_cancel_booking', 'logestay_cancel_booking');

function logestay_cancel_booking() {
  check_ajax_referer('logestay_nonce', 'nonce');

  $booking_id = absint($_POST['booking_id'] ?? 0);
  $token      = sanitize_text_field($_POST['token'] ?? '');

  if (!$booking_id || !$token) {
    wp_send_json_error(['message' => __('Invalid request', 'logestay')]);
  }

  $saved = get_post_meta($booking_id, 'logestay_stripe_cancel_token', true);
  if (!$saved || !hash_equals($saved, $token)) {
    wp_send_json_error(['message' => 'Invalid token']);
  }

  // Release hold
  update_post_meta($booking_id, 'logestay_hold_expires_at', '');

  // Mark canceled (recommended)
  update_post_meta($booking_id, 'logestay_booking_status', 'canceled');

  // Mark payment status (choose one)
  update_post_meta($booking_id, 'logestay_payment_status', 'failed');

  // Optional: record reason
  update_post_meta($booking_id, 'logestay_payment_txn_id', 'cancelled_return');

  // Prevent reuse
  delete_post_meta($booking_id, 'logestay_stripe_cancel_token');

  logestay_trigger_booking_state_email($booking_id, 'card cancelled');


  wp_send_json_success(['message' => 'Booking cancelled and hold released']);
}


add_action('rest_api_init', function () {
	register_rest_route('logestay/v1', '/stripe-webhook', [
		'methods'             => 'POST',
		'callback'            => 'logestay_stripe_webhook_handler',
		'permission_callback' => '__return_true',
	]);
});



function logestay_stripe_parse_sig_header(string $header): array {
	$parts = [];
	foreach (explode(',', $header) as $item) {
		$item = trim($item);
		if (strpos($item, '=') === false) continue;
		[$k, $v] = array_map('trim', explode('=', $item, 2));
		$parts[$k][] = $v;
	}
	return $parts;
}

function logestay_stripe_verify_signature(string $payload, string $sig_header, string $secret, int $tolerance = 300): bool {
	if (!$secret || !$sig_header) return false;

	$parsed = logestay_stripe_parse_sig_header($sig_header);
	$t = isset($parsed['t'][0]) ? (int) $parsed['t'][0] : 0;
	$v1_list = $parsed['v1'] ?? [];

	if (!$t || empty($v1_list)) return false;

	// timestamp tolerance (5 mins default)
	if (abs(time() - $t) > $tolerance) return false;

	$signed_payload = $t . '.' . $payload;
	$expected = hash_hmac('sha256', $signed_payload, $secret);

	foreach ($v1_list as $sig) {
		if (hash_equals($expected, $sig)) {
			return true;
		}
	}

	return false;
}

add_action('wp_ajax_logestay_create_paypal_order', 'logestay_create_paypal_order');
add_action('wp_ajax_nopriv_logestay_create_paypal_order', 'logestay_create_paypal_order');
function logestay_create_paypal_order() {
  check_ajax_referer('logestay_nonce', 'nonce');

  $booking_id = absint($_POST['booking_id'] ?? 0);
  if (!$booking_id) wp_send_json_error(['message' => __('Invalid booking', 'logestay')]);

  // Validate booking status
  $status = get_post_meta($booking_id, 'logestay_booking_status', true);
  if ($status !== 'pending') wp_send_json_error(['message' => __('Booking not pending', 'logestay')]);

  // Validate hold
  $expires = get_post_meta($booking_id, 'logestay_hold_expires_at', true);
  if ($expires && strtotime($expires) <= current_time('timestamp')) {
    update_post_meta($booking_id, 'logestay_booking_status', 'expired');
    wp_send_json_error(['message' => 'Hold expired']);
  }

  $total    = (float) get_post_meta($booking_id, 'logestay_total_amount', true);
  $currency = strtoupper((string) (get_post_meta($booking_id, 'logestay_currency', true) ?: 'EUR'));

  if ($total <= 0) wp_send_json_error(['message' => __('Invalid amount', 'logestay')]);

  // ===== PayPal settings (add these to your settings page) =====
  $mode = function_exists('logestay_get_option')
    ? logestay_get_option('logestay_paypal_mode', 'test')
    : 'test';

  $client_id = function_exists('logestay_get_option')
    ? logestay_get_option($mode === 'live' ? 'logestay_paypal_live_client_id' : 'logestay_paypal_test_client_id', '')
    : '';

  $secret = function_exists('logestay_get_option')
    ? logestay_get_option($mode === 'live' ? 'logestay_paypal_live_secret' : 'logestay_paypal_test_secret', '')
    : '';

  if (!$client_id || !$secret) {
    wp_send_json_error(['message' => __('PayPal keys missing in settings', 'logestay')]);
  }

  $api_base = ($mode === 'live')
    ? 'https://api-m.paypal.com'
    : 'https://api-m.sandbox.paypal.com';

  // ===== 1) Get access token =====
  $token_res = wp_remote_post($api_base . '/v1/oauth2/token', [
    'timeout' => 30,
    'headers' => [
      'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $secret),
      'Content-Type'  => 'application/x-www-form-urlencoded',
    ],
    'body' => 'grant_type=client_credentials',
  ]);

  if (is_wp_error($token_res)) {
    wp_send_json_error(['message' => 'PayPal token error: ' . $token_res->get_error_message()]);
  }

  $token_body = json_decode(wp_remote_retrieve_body($token_res), true);
  $access_token = $token_body['access_token'] ?? '';
  if (!$access_token) {
    wp_send_json_error(['message' => __('PayPal token missing', 'logestay')]);
  }

  // ===== 2) Create order =====
  $success_url = add_query_arg([
    'ls_pay'   => 'paypal',
    'ls_book'  => $booking_id,
    'ls_stat'  => 'success',
    'k'        => wp_generate_password(16, false, false),
  ], home_url('/'));

  $cancel_url = add_query_arg([
    'ls_pay'   => 'paypal',
    'ls_book'  => $booking_id,
    'ls_stat'  => 'cancel',
    'k'        => wp_generate_password(16, false, false),
  ], home_url('/'));

  $order_res = wp_remote_post($api_base . '/v2/checkout/orders', [
    'timeout' => 30,
    'headers' => [
      'Authorization' => 'Bearer ' . $access_token,
      'Content-Type'  => 'application/json',
    ],
    'body' => wp_json_encode([
      'intent' => 'CAPTURE',
      'purchase_units' => [[
        'reference_id' => (string) $booking_id,
        'description'  => 'Booking #' . $booking_id,
        'amount' => [
          'currency_code' => $currency,
          'value'         => number_format($total, 2, '.', ''),
        ],
      ]],
      'application_context' => [
        'return_url' => $success_url,
        'cancel_url' => $cancel_url,
        'brand_name' => get_bloginfo('name'),
        'user_action' => 'PAY_NOW',
      ],
    ]),
  ]);

  if (is_wp_error($order_res)) {
    wp_send_json_error(['message' => 'PayPal order error: ' . $order_res->get_error_message()]);
  }

  $order_body = json_decode(wp_remote_retrieve_body($order_res), true);

  $paypal_order_id = $order_body['id'] ?? '';
  $links = $order_body['links'] ?? [];

  $approve_url = '';
  foreach ($links as $l) {
    if (($l['rel'] ?? '') === 'approve') {
      $approve_url = (string) ($l['href'] ?? '');
      break;
    }
  }

  if (!$paypal_order_id || !$approve_url) {
    wp_send_json_error(['message' => __('PayPal approval link missing', 'logestay')]);
  }

  // Save booking payment intent (temporary)
  update_post_meta($booking_id, 'logestay_payment_method', 'paypal');
  update_post_meta($booking_id, 'logestay_payment_type', 'instant');
  update_post_meta($booking_id, 'logestay_payment_status', 'pending');
  update_post_meta($booking_id, 'logestay_payment_txn_id', sanitize_text_field($paypal_order_id));

  logestay_trigger_booking_state_email($booking_id, 'paypal pending');

  wp_send_json_success([
    'approve_url' => esc_url_raw($approve_url),
  ]);
}


add_action('template_redirect', function () {
  if (empty($_GET['ls_pay']) || $_GET['ls_pay'] !== 'paypal') return;

  $booking_id = absint($_GET['ls_book'] ?? 0);
  $status     = sanitize_text_field($_GET['ls_stat'] ?? '');
  if (!$booking_id) return;

  // IMPORTANT: this is "temporary trust" until webhook
  // You can also validate booking exists + pending
  $book_status = get_post_meta($booking_id, 'logestay_booking_status', true);

  if ($status === 'success') {
    update_post_meta($booking_id, 'logestay_payment_method', 'paypal');
    update_post_meta($booking_id, 'logestay_payment_type', 'instant');
    update_post_meta($booking_id, 'logestay_payment_status', 'paid');
    update_post_meta($booking_id, 'logestay_booking_status', 'confirmed');

    // clear hold so it doesn't expire later
    delete_post_meta($booking_id, 'logestay_hold_expires_at');

    // optional: trigger email hooks you already made
    logestay_trigger_booking_state_email($booking_id, 'success');
  }

  if ($status === 'cancel') {
    update_post_meta($booking_id, 'logestay_payment_method', 'paypal');
    update_post_meta($booking_id, 'logestay_payment_status', 'failed'); // "not received"
    update_post_meta($booking_id, 'logestay_booking_status', 'canceled');
    delete_post_meta($booking_id, 'logestay_hold_expires_at');

    logestay_trigger_booking_state_email($booking_id, 'cancel');
  }

  // redirect to homepage with a short signal so JS can show modal
  $redir = add_query_arg([
    'ls_modal' => ($status === 'success') ? 'booking_success' : 'booking_cancel',
    'ls_book'  => $booking_id,
  ], home_url('/'));

  wp_safe_redirect($redir);
  exit;
});


// Add Listing ID column
add_filter('manage_logestay_listing_posts_columns', function ($columns) {

  $new = [];

  foreach ($columns as $key => $label) {
    $new[$key] = $label;

    // Insert after checkbox or title (your choice)
    if ($key === 'title') {
      $new['logestay_listing_id'] = __('Listing ID', 'logestay');
    }
  }

  return $new;
});

// Render Listing ID value
add_action('manage_logestay_listing_posts_custom_column', function ($column, $post_id) {

  if ($column === 'logestay_listing_id') {
    echo (int) $post_id;
  }

}, 10, 2);
