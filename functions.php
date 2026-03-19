<?php 
add_action('after_setup_theme', function() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['search-form','gallery','caption','style','script']);
  register_nav_menus([
    'footer_legal' => __('Footer Menu', 'logestay'),
  ]);
});

// Enqueue compiled CSS/JS (cache-busting via filemtime)
add_action('wp_enqueue_scripts', function () {

  	// Theme stylesheet (style.css)
  	$style_path = get_stylesheet_directory() . '/style.css';
	wp_enqueue_style(
	    'logestay-style',
	    get_stylesheet_uri(),
	    [],
	    file_exists($style_path) ? filemtime($style_path) : null
	);

	// Theme CSS file
	$css_rel  = 'assets/css/theme.css';
	$css_path = get_stylesheet_directory() . '/' . $css_rel;
	wp_enqueue_style(
	    'logestay-app',
	    get_stylesheet_directory_uri() . '/' . $css_rel,
	    [],
	    file_exists($css_path) ? filemtime($css_path) : null
	);

  	// jQuery (WP bundled)
  	wp_enqueue_script('jquery');

  // Lucide Icons
	wp_enqueue_script(
		'logestay-lucide',
		get_template_directory_uri() . '/assets/vendor/lucide.min.js',
		[],
		null,
		true
	);

	// Custom JS file
	$js_rel  = 'assets/js/custom.js';
	$js_path = get_stylesheet_directory() . '/' . $js_rel;
	wp_enqueue_script(
	    'custom',
	    get_stylesheet_directory_uri() . '/' . $js_rel,
	    ['jquery'],
	    file_exists($js_path) ? filemtime($js_path) : null,
	    true
	);

	wp_localize_script('custom', 'logestayc', [
	    'ajaxUrl' => admin_url('admin-ajax.php'),
	    'nonce'   => wp_create_nonce('logestay_nonce'),
	    'i18n' => array(
	    		'ct_title'                 => __('Contact support', 'logestay'),
					'ct_intro'                 => __('Explain your request to us, we will respond within 24 to 48 business hours.', 'logestay'),
					'ct_first_name_label'      => __('First Name *', 'logestay'),
					'ct_first_name_ph'         => __('Your first name', 'logestay'),
					'ct_email_label'           => __('Email *', 'logestay'),
					'ct_email_ph'              => __('your@email.com', 'logestay'),
					'ct_subject_label'         => __('Subject *', 'logestay'),
					'ct_subject_placeholder'   => __('Select a subject', 'logestay'),
					'ct_subject_prebooking'    => __('Question before booking', 'logestay'),
					'ct_subject_booking_issue' => __('Booking problem', 'logestay'),
					'ct_subject_during_stay'   => __('During stay', 'logestay'),
					'ct_subject_other'         => __('Other request', 'logestay'),
					'ct_message_label'         => __('Message *', 'logestay'),
					'ct_message_ph'            => __('Describe your question or problem in detail...', 'logestay'),
					'ct_gdpr_label'            => __('I agree that my information may be used to contact me. *', 'logestay'),
					'ct_response_time'         => __('Our team responds within 24 to 48 business hours.', 'logestay'),
					'cancel'                   => __('Cancel', 'logestay'),
					'send'                     => __('Send', 'logestay'),
					'close'                    => __('Close', 'logestay'),
					'message_sent_title' => __('Message sent successfully!', 'logestay'),
					'message_sent_desc'  => __('We have received your request and will respond as soon as possible.', 'logestay'),
					'generic_error' => __('Something went wrong. Please try again.', 'logestay'),
					'network_error' => __('Network error. Please try again.', 'logestay'),
	    )
	]);


	 wp_enqueue_script(
    'logestay-city-filter',
    get_template_directory_uri() . '/assets/js/city-filter.js',
    ['jquery'],
    '1.0.0',
    true
  );

  wp_localize_script('logestay-city-filter', 'logstaycity', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce'    => wp_create_nonce('logestay_city_filter'),
    'i18n' => array(
    		'itinerary' => __('Itinerary', 'logestay'),
    )
  ]);

  wp_enqueue_script(
	  'logestay-login',
	  get_template_directory_uri() . '/assets/js/logestay-login.js',
	  array('jquery'),
	  '1.0.0',
	  true
	);

	wp_localize_script('logestay-login', 'logestay_ajax', array(
	    'ajax_url' => admin_url('admin-ajax.php'),
	    'redirect' => admin_url(),
	));

  wp_enqueue_script(
	  'logestay-booking-popup',
	  get_template_directory_uri() . '/assets/js/logestay-booking-popup.js',
	  array('jquery'),
	  '1.0.0',
	  true
	);

	wp_localize_script('logestay-booking-popup', 'logestay', array(
	  'ajaxUrl'   => admin_url('admin-ajax.php'),
	  'nonce'     => wp_create_nonce('logestay_nonce'),
	  'settings'  => get_option('logestay_settings'),
	  'metas'     => get_post_meta(get_the_ID()),
	  'listing' => [
	    'checkin_time'  => get_post_meta(get_the_ID(), 'logestay_checkin_time', true) ?: '15:00',
	    'checkout_time' => get_post_meta(get_the_ID(), 'logestay_checkout_time', true) ?: '11:00',
	  ],
	  'i18n' => array(
	  	 'arrival'         => __( 'Arrival', 'logestay' ),
		  'departure'       => __( 'Departure', 'logestay' ),
		  'from'            => __( 'from', 'logestay' ),
		  'before'          => __( 'before', 'logestay' ),
		  'night_singular'  => __( 'night', 'logestay' ),
		  'night_plural'    => __( 'nights', 'logestay' ),
		  'stay_summary'    => __( 'Check-in from %1$s → Check-out before %2$s', 'logestay' ),
	    'locale'      => function_exists('determine_locale') ? determine_locale() : get_locale(),
	    'payments' => [
			  'card'        => __( 'Credit card', 'logestay' ),
			  'paypal'      => __( 'PayPal', 'logestay' ),
			  'bank'        => __( 'Bank transfer', 'logestay' ),
			  'cash'        => __( 'Cash payment', 'logestay' ),
			  'link'        => __( 'Payment link', 'logestay' ),

			  'badge_instant'  => __( 'Immediate', 'logestay' ),
			  'badge_deferred' => __( 'Delayed', 'logestay' ),
			  'card_sub'   => __( 'Secure card payment - Instant confirmation', 'logestay' ),
			  'paypal_sub' => __( 'PayPal payment - Instant confirmation', 'logestay' ),
			  'bank_sub'   => __( 'Transfer - Validation after receipt', 'logestay' ),
			  'cash_sub'   => __( 'Payment on site - Owner validation required', 'logestay' ),
			  'link_sub'   => __( 'Link sent by email - Validation after payment', 'logestay' ),
			],
	    'months' => [
		    __('January', 'logestay'),
		    __('February', 'logestay'),
		    __('March', 'logestay'),
		    __('April', 'logestay'),
		    __('May', 'logestay'),
		    __('June', 'logestay'),
		    __('July', 'logestay'),
		    __('August', 'logestay'),
		    __('September', 'logestay'),
		    __('October', 'logestay'),
		    __('November', 'logestay'),
		    __('December', 'logestay'),
		  ],
		  'days_short' => [
		    __('Sun', 'logestay'),
		    __('Mon', 'logestay'),
		    __('Tue', 'logestay'),
		    __('Wed', 'logestay'),
		    __('Thu', 'logestay'),
		    __('Fri', 'logestay'),
		    __('Sat', 'logestay'),
		  ],

	    // your existing keys (kept)
	    'processing'        => __('Processing…', 'logestay'),
	    'amount_to_pay'     => __('Amount to pay', 'logestay'),
	    'credit_card'       => __('Credit card payment', 'logestay'),
	    'credit_card_desc'  => __('Secure card payment. You will be redirected after validation to complete the payment.', 'logestay'),
	    'paypal'            => __('PayPal payment', 'logestay'),
	    'paypal_desc'       => __('You will be redirected to PayPal after validation.', 'logestay'),
	    'bank_details'      => __('Bank details', 'logestay'),
	    'bank_desc'         => __('Make the transfer to confirm your booking. Dates are temporarily blocked.', 'logestay'),
	    'beneficiary'       => __('Beneficiary', 'logestay'),
	    'iban'              => __('IBAN', 'logestay'),
	    'bic'               => __('BIC', 'logestay'),
	    'bank_instruction'  => __('Include the booking reference in the transfer description for quick processing.', 'logestay'),
	    'choose_date'       => __('Choose your dates', 'logestay'),
	    'date_ins'          => __('Click on a check-in date then a check-out date to select your stay period.', 'logestay'),
	    'select_stay_dates' => __('Select your stay dates', 'logestay'),
	    'number_of_guests'  => __('Number of guests', 'logestay'),
	    'payment_method'    => __('Payment method', 'logestay'),
	    'or_through_partners' => __('or through our partners', 'logestay'),
	    'airbnb'            => __('Book on Airbnb', 'logestay'),
	    'booking'           => __('Book on Booking', 'logestay'),
	    'badge_immediate'   => __('Immediate', 'logestay'),
	    'badge_delayed'     => __('Delayed', 'logestay'),
	    'pay_label_card'    => __('Credit card', 'logestay'),
	    'pay_label_paypal'  => __('PayPal', 'logestay'),
	    'pay_label_bank'    => __('Bank transfer', 'logestay'),
	    'pay_label_cash'    => __('Cash on-site', 'logestay'),
	    'pay_label_link'    => __('Payment link', 'logestay'),
	    'payment_item_desc_instant' => __('Secure payment - Instant confirmation', 'logestay'),
	    'guest_adults_label'   => __('Adults', 'logestay'),
	    'guest_children_label' => __('Children', 'logestay'),
	    'guest_pets_label'     => __('Pets', 'logestay'),
	    'guest_adults_sub'     => __('13 years and older', 'logestay'),
	    'guest_children_sub'   => __('2–12 years', 'logestay'),
	    'guest_pets_sub'       => __('', 'logestay'),
	    'cash_title'   => __('Cash payment', 'logestay'),
	    'cash_desc'    => __('Cash payment is made on site. Booking confirmed after payment.', 'logestay'),
	    'cash_prepare_exact'   => __('Prepare exact amount if possible', 'logestay'),
	    'cash_bring_id'         => __('Bring a valid ID. A receipt will be provided on site.', 'logestay'),
	    'link_title'   => __('Secure payment link', 'logestay'),
	    'link_desc'    => __('A secure payment link will be sent by email and SMS.', 'logestay'),
	    'err_complete_steps'   => __('Please complete all steps.', 'logestay'),
	    'err_payment'          => __('Payment error', 'logestay'),
	    'err_server'           => __('Server error. Try again.', 'logestay'),
	    'err_server_generic'   => __('Server error', 'logestay'),
	    'err_hold'             => __('Could not create booking hold', 'logestay'),
	    'guest_age_note' => __('13 years and older', 'logestay'),
	    'guest_age_note_adult' => __('13 years and older', 'logestay'),
	    'guest_age_note_child' => __('2 to 12 years', 'logestay'),
	    'guest_age_note_pet' => __('Dogs, cats, etc.', 'logestay'),
	    'payment_hint' => __('Secure payment - Instant confirmation', 'logestay'),
	    'stay_summary_title'      => __('Your stay summary', 'logestay'),
			'summary_property'        => __('Property', 'logestay'),
			'summary_stay_dates'      => __('Stay dates', 'logestay'),
			'summary_nights'          => __('nights', 'logestay'),
			'summary_pricing'         => __('Pricing', 'logestay'),
			'summary_stay_total'      => __('Stay total', 'logestay'),
			'summary_guests'          => __('Guests', 'logestay'),
			'summary_adults'          => __('Adult(s)', 'logestay'),
			'summary_children'        => __('Child', 'logestay'),
			'summary_pets'            => __('Pet', 'logestay'),
			'cleaning_fee'            => __('Cleaning fee', 'logestay'),
			'book_now'                => __('Book now', 'logestay'),
			'summary_secure_payment'  => __('Secure payment according to chosen method', 'logestay'),
			'summary_dates_blocked'   => __('Selected dates are temporarily blocked', 'logestay'),
			'summary_owner_confirms'  => __('The owner will confirm your booking', 'logestay'),
			'summary_no_charge_notice'=> __('No payment is charged without confirmation according to the chosen method. You will receive an email with access to your client area.', 'logestay'),
			'guest_details_title'            => __('Your details', 'logestay'),
			'full_name'                      => __('Full name', 'logestay'),
			'full_name_placeholder'          => __('John Doe', 'logestay'),
			'email'                          => __('Email', 'logestay'),
			'email_placeholder'              => __('john.doe@email.com', 'logestay'),
			'phone'                          => __('Phone', 'logestay'),
			'phone_placeholder'              => __('+1 555 123 4567', 'logestay'),
			'special_requests'               => __('Special requests', 'logestay'),
			'special_requests_placeholder'   => __('Late arrival, additional equipment...', 'logestay'),
			'back'                           => __('Back', 'logestay'),
			'confirm_booking'                => __('Confirm booking', 'logestay'),
			'error_invalid_dates'   => __('Please select valid dates.', 'logestay'),
			'error_select_payment'  => __('Please select a payment method.', 'logestay'),
			'error_guest_details'   => __('Please enter guest details.', 'logestay'),
			'error_invalid_email'   => __('Invalid email address.', 'logestay'),
			'booking_request_sent' => __('Booking request sent', 'logestay'),
			'booking_request_desc' => __('The owner will confirm your booking shortly. You will receive an email confirmation.', 'logestay'),
			'paypal_error' => __('PayPal error. Please try again.', 'logestay'),
			'server_error' => __('Server error. Please try again.', 'logestay'),
			'payment_error' => __('Payment error. Please try again.', 'logestay'),
			'booking_save_error' => __('Could not save booking.', 'logestay'),
			'booking_registered'       => __('Booking request registered', 'logestay'),
			'booking_registered_desc'  => __('Your booking request is registered. The owner will confirm your stay. You will receive an email with payment instructions.', 'logestay'),
			'close'                    => __('Close', 'logestay'),
			'booking_paid_title' => __('Booking confirmed and paid!', 'logestay'),
			'booking_paid_desc'  => __('Thank you for your booking. Payment has been received. You can access your client area by email.', 'logestay'),
			'payment_cancelled_title' => __('Payment cancelled', 'logestay'),
			'payment_cancelled_desc'  => __('Your payment was cancelled and no charge was made. You can restart your booking at any time.', 'logestay'),
			'or_partners'  => __('or through our partners', 'logestay'),
			'price_auto_update' => __( 'Price updates automatically based on your dates', 'logestay' ),
			'cleaning_fee_note' => __( 'The cleaning fee covers the cleaning of the accommodation after your stay.', 'logestay' ),
	  ),
	));


});


require_once get_template_directory() . '/inc/homepage-meta.php';
if ( file_exists( get_stylesheet_directory() . '/inc/logestay-loader.php' ) ) {
	require_once get_stylesheet_directory() . '/inc/logestay-loader.php';
}


require_once get_template_directory() . '/inc/emails/email-core.php';
require_once get_template_directory() . '/inc/emails/email-hooks.php';
require_once get_template_directory() . '/inc/emails/booking-email-triggers.php';
require_once get_template_directory() . '/inc/default-page.php';
require_once get_template_directory() . '/inc/colors.php';
require_once get_template_directory() . '/inc/ical.php';
require_once get_template_directory() . '/inc/auth/login.php';

add_action('init', function () {

  if ( ! function_exists('pll_register_string') ) {
    return;
  }

  $settings = get_option('logestay_settings', []);
  $settings = is_array($settings) ? $settings : [];

  $map = [
    'logestay_footer_title'    => 'Footer Title',
    'logestay_footer_subtitle' => 'Footer Subtitle',
    'logestay_copyright'       => 'Footer Copyright',
  ];

  foreach ( $map as $key => $label ) {
    $value = isset($settings[$key]) ? (string) $settings[$key] : '';
    if ( $value !== '' ) {
      pll_register_string(
        $key,                  // string name (unique key)
        $value,                // original value
        'LOGESTAY – Settings',  // group name in Polylang UI
        true                   // multiline safe
      );
    }
  }
});


add_action('template_redirect', function () {

  if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) return;
  if ( is_user_logged_in() ) return;

  $opts = get_option('logestay_settings', []);
  $enabled = ! empty($opts['logestay_maintenance_enabled']);

  if ( ! $enabled ) return;

  // allow WP login pages
  $uri = $_SERVER['REQUEST_URI'] ?? '';
  if ( strpos($uri, 'wp-login.php') !== false ) return;

  status_header(503);
  header('Retry-After: 3600');
  nocache_headers();

  $file = get_template_directory() . '/templates/logestay-maintenance.php';
  if ( file_exists($file) ) {
    include $file;
    exit;
  }
});



function logestay_admin_css() {
    wp_enqueue_style(
        'logestay-admin-style',
        get_template_directory_uri() . '/assets/css/admin.css',
        array(),
        '1.0'
    );
}
add_action('admin_enqueue_scripts', 'logestay_admin_css');


/**
 * Show admin bar ONLY for Super Admins
 */
add_filter('show_admin_bar', function($show) {
    
    // Allow only Super Admin
    if ( is_super_admin() ) {
        return true;
    }

    // Hide for everyone else (site admins, editors, etc.)
    return false;

});



/**
 * Remove WordPress Events & News dashboard widget
 */
add_action('wp_dashboard_setup', function () {
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
});


/**
 * Hide some admin menu pages for site administrators
 * but keep them available for Super Admin.
 */
add_action('admin_menu', function () {
    
    if ( is_super_admin() ) {
        return;
    }

    // Posts
    remove_menu_page('edit.php');

    // Comments
    remove_menu_page('edit-comments.php');

    // Media
    remove_menu_page('upload.php');

}, 999);