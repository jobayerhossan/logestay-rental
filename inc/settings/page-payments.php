<?php
/**
 * LOGESTAY – Payments Settings Page (Tabs)
 *
 * Drop-in file: inc/settings/page-payments.php
 *
 * Requirements:
 * - logestay_get_option($key, $default) must exist (your helper)
 * - Field renderers should exist:
 *   - logestay_field_checkbox()
 *   - logestay_field_text()
 *   - logestay_field_password()
 *   - logestay_field_select()
 *
 * This file:
 * - Adds submenu under "LOGE STAY"
 * - Registers sections/fields for the Payments page
 * - Renders tabs (Stripe/PayPal/Bank/Cash/Payment Link)
 */

if ( ! defined('ABSPATH') ) exit;

/**
 * Register submenu page under LOGE STAY menu.
 */
add_action('admin_menu', function () {

	// Parent slug must match your top-level menu slug.
	// You already use: add_menu_page(..., 'logestay-settings', ...)
	$parent_slug = 'logestay-settings';

	add_submenu_page(
		$parent_slug,
		__('Payments', 'logestay'),
		__('Payments', 'logestay'),
		'manage_options',
		'logestay-settings-payments',
		'logestay_settings_page_payments'
	);
}, 20);

/**
 * Register settings + fields.
 */
add_action('admin_init', function () {

	// Register the option if not already registered elsewhere
	// (Safe even if registered in another file; WP ignores duplicates)
	/*register_setting(
		'logestay_settings_group',
		'logestay_settings',
		[
			'type'              => 'array',
			'sanitize_callback' => 'logestay_settings_sanitize_payments_bridge',
			'default'           => [],
		]
	);*/

	logestay_register_settings_payments();

}, 20);

/**
 * Sanitizer bridge:
 * - If you already have logestay_sanitize_settings(), reuse it
 * - Otherwise sanitize only fields used here
 */
function logestay_settings_sanitize_payments_bridge($input) {
	$input = is_array($input) ? $input : [];

	// If your main sanitizer exists, use it first (keeps existing behavior)
	if ( function_exists('logestay_sanitize_settings') ) {
		$out = (array) logestay_sanitize_settings($input);
	} else {
		$out = [];
	}

	/* --------------------------
	 * PAYMENTS (toggles)
	 * -------------------------- */
	$payment_bool_keys = [
	  'logestay_payments_enabled_card',
	  'logestay_payments_enabled_paypal',
	  'logestay_payments_enabled_bank',
	  'logestay_payments_enabled_cash',
	  'logestay_payments_enabled_link',
	];

	if ($page === 'payments') {
	  // On the payments page: missing checkbox = unchecked = 0
	  foreach ($payment_bool_keys as $k) {
	    $out[$k] = ! empty($input[$k]) ? 1 : 0;
	  }
	} else {
	  // On other pages: only update if present (don’t reset)
	  foreach ($payment_bool_keys as $k) {
	    if (array_key_exists($k, $input)) {
	      $out[$k] = ! empty($input[$k]) ? 1 : 0;
	    }
	  }
	}

	// Stripe
	$mode = sanitize_text_field($input['logestay_stripe_mode'] ?? 'test');
	$out['logestay_stripe_mode'] = in_array($mode, ['test','live'], true) ? $mode : 'test';

	foreach ([
		'logestay_stripe_test_pk',
		'logestay_stripe_test_sk',
		'logestay_stripe_live_pk',
		'logestay_stripe_live_sk',
		'logestay_stripe_test_whsec',
		'logestay_stripe_live_whsec',
	] as $k) {
		$out[$k] = sanitize_text_field($input[$k] ?? '');
	}

	// PayPal
	// PayPal
	$pp_mode = sanitize_text_field($input['logestay_paypal_mode'] ?? 'test');
	$out['logestay_paypal_mode'] = in_array($pp_mode, ['test','live'], true) ? $pp_mode : 'test';

	$out['logestay_paypal_test_client_id'] = sanitize_text_field($input['logestay_paypal_test_client_id'] ?? '');
	$out['logestay_paypal_test_secret']    = sanitize_text_field($input['logestay_paypal_test_secret'] ?? '');
	$out['logestay_paypal_live_client_id'] = sanitize_text_field($input['logestay_paypal_live_client_id'] ?? '');
	$out['logestay_paypal_live_secret']    = sanitize_text_field($input['logestay_paypal_live_secret'] ?? '');

	// Bank
	$out['logestay_bank_beneficiary'] = sanitize_text_field($input['logestay_bank_beneficiary'] ?? '');
	$out['logestay_bank_iban']        = sanitize_text_field($input['logestay_bank_iban'] ?? '');
	$out['logestay_bank_bic']         = sanitize_text_field($input['logestay_bank_bic'] ?? '');
	$out['logestay_bank_note']        = sanitize_textarea_field($input['logestay_bank_note'] ?? '');

	// Cash
	$out['logestay_cash_office_name']    = sanitize_text_field($input['logestay_cash_office_name'] ?? '');
	$out['logestay_cash_office_address'] = sanitize_text_field($input['logestay_cash_office_address'] ?? '');
	$out['logestay_cash_hours']          = sanitize_text_field($input['logestay_cash_hours'] ?? '');

	// Payment Link
	$out['logestay_payment_link_url'] = esc_url_raw($input['logestay_payment_link_url'] ?? '');

	return $out;
}

/**
 * Register settings sections/fields for Payments page.
 */
function logestay_register_settings_payments() {

	$page = 'logestay-settings-payments';

	/* ---------------------------------------------------------
	 * STRIPE TAB
	 * --------------------------------------------------------- */
	add_settings_section(
		'logestay_section_stripe',
		__('Credit Card', 'logestay'),
		'__return_false',
		$page
	);

	add_settings_field(
		'logestay_payments_enabled_card',
		__('Enable Credit Card', 'logestay'),
		'logestay_field_checkbox',
		$page,
		'logestay_section_stripe',
		['key' => 'logestay_payments_enabled_card']
	);

	add_settings_field(
		'logestay_stripe_mode',
		__('Stripe Mode', 'logestay'),
		'logestay_field_select',
		$page,
		'logestay_section_stripe',
		[
			'key'       => 'logestay_stripe_mode',
			'default'   => 'test',
			'options'   => [
				'test' => __('Test (Sandbox)', 'logestay'),
				'live' => __('Live (Production)', 'logestay'),
			],
			'description' => __('Use Test during development. Switch to Live only when ready.', 'logestay'),
		]
	);

	add_settings_field(
		'logestay_stripe_test_pk',
		__('Test Publishable Key', 'logestay'),
		'logestay_field_text',
		$page,
		'logestay_section_stripe',
		['key' => 'logestay_stripe_test_pk', 'placeholder' => 'pk_test_...']
	);

	add_settings_field(
		'logestay_stripe_test_sk',
		__('Test Secret Key', 'logestay'),
		'logestay_field_password',
		$page,
		'logestay_section_stripe',
		['key' => 'logestay_stripe_test_sk', 'placeholder' => 'sk_test_...']
	);

	add_settings_field(
		'logestay_stripe_live_pk',
		__('Live Publishable Key', 'logestay'),
		'logestay_field_text',
		$page,
		'logestay_section_stripe',
		['key' => 'logestay_stripe_live_pk', 'placeholder' => 'pk_live_...']
	);

	add_settings_field(
		'logestay_stripe_live_sk',
		__('Live Secret Key', 'logestay'),
		'logestay_field_password',
		$page,
		'logestay_section_stripe',
		['key' => 'logestay_stripe_live_sk', 'placeholder' => 'sk_live_...']
	);

	add_settings_field(
		'logestay_stripe_test_whsec',
		__('Test Webhook Secret', 'logestay'),
		'logestay_field_password',
		$page,
		'logestay_section_stripe',
		['key' => 'logestay_stripe_test_whsec', 'placeholder' => 'whsec_...']
	);

	add_settings_field(
		'logestay_stripe_live_whsec',
		__('Live Webhook Secret', 'logestay'),
		'logestay_field_password',
		$page,
		'logestay_section_stripe',
		['key' => 'logestay_stripe_live_whsec', 'placeholder' => 'whsec_...']
	);

	/* ---------------------------------------------------------
	 * PAYPAL TAB
	 * --------------------------------------------------------- */
	add_settings_section(
		'logestay_section_paypal',
		__('PayPal', 'logestay'),
		'__return_false',
		$page
	);

	add_settings_field(
		'logestay_payments_enabled_paypal',
		__('Enable PayPal', 'logestay'),
		'logestay_field_checkbox',
		$page,
		'logestay_section_paypal',
		['key' => 'logestay_payments_enabled_paypal']
	);

	add_settings_field(
	  'logestay_paypal_mode',
	  __('PayPal Mode', 'logestay'),
	  'logestay_field_select',
	  $page,
	  'logestay_section_paypal',
	  [
	    'key' => 'logestay_paypal_mode',
	    'default' => 'test',
	    'options' => [
	      'test' => __('Test (Sandbox)', 'logestay'),
	      'live' => __('Live (Production)', 'logestay'),
	    ],
	    'description' => __('Use Test during development. Switch to Live only when ready.', 'logestay'),
	  ]
	);

	add_settings_field(
	  'logestay_paypal_test_client_id',
	  __('Test Client ID', 'logestay'),
	  'logestay_field_text',
	  $page,
	  'logestay_section_paypal',
	  ['key' => 'logestay_paypal_test_client_id', 'placeholder' => 'AaBbCc...']
	);

	add_settings_field(
	  'logestay_paypal_test_secret',
	  __('Test Secret', 'logestay'),
	  'logestay_field_password',
	  $page,
	  'logestay_section_paypal',
	  ['key' => 'logestay_paypal_test_secret', 'placeholder' => 'EJkLm...']
	);

	add_settings_field(
	  'logestay_paypal_live_client_id',
	  __('Live Client ID', 'logestay'),
	  'logestay_field_text',
	  $page,
	  'logestay_section_paypal',
	  ['key' => 'logestay_paypal_live_client_id', 'placeholder' => 'AaBbCc...']
	);

	add_settings_field(
	  'logestay_paypal_live_secret',
	  __('Live Secret', 'logestay'),
	  'logestay_field_password',
	  $page,
	  'logestay_section_paypal',
	  ['key' => 'logestay_paypal_live_secret', 'placeholder' => 'EJkLm...']
	);

	

	/* ---------------------------------------------------------
	 * BANK TAB
	 * --------------------------------------------------------- */
	add_settings_section(
		'logestay_section_bank',
		__('Bank Transfer', 'logestay'),
		'__return_false',
		$page
	);

	add_settings_field(
		'logestay_payments_enabled_bank',
		__('Enable Bank Transfer', 'logestay'),
		'logestay_field_checkbox',
		$page,
		'logestay_section_bank',
		['key' => 'logestay_payments_enabled_bank']
	);

	add_settings_field(
		'logestay_bank_beneficiary',
		__('Beneficiary', 'logestay'),
		'logestay_field_text',
		$page,
		'logestay_section_bank',
		['key' => 'logestay_bank_beneficiary', 'placeholder' => 'LOGESTAY SAS']
	);

	add_settings_field(
		'logestay_bank_iban',
		__('IBAN', 'logestay'),
		'logestay_field_text',
		$page,
		'logestay_section_bank',
		['key' => 'logestay_bank_iban', 'placeholder' => 'FR76 ...']
	);

	add_settings_field(
		'logestay_bank_bic',
		__('BIC', 'logestay'),
		'logestay_field_text',
		$page,
		'logestay_section_bank',
		['key' => 'logestay_bank_bic', 'placeholder' => 'LOGEFRPP']
	);

	add_settings_field(
		'logestay_bank_hold_time',
		__('Hold Time(Hours)', 'logestay'),
		'logestay_field_text',
		$page,
		'logestay_section_bank',
		['key' => 'logestay_bank_hold_time', 'placeholder' => '24']
	);



	/* ---------------------------------------------------------
	 * CASH TAB
	 * --------------------------------------------------------- */
	add_settings_section(
		'logestay_section_cash',
		__('Cash Payment', 'logestay'),
		'__return_false',
		$page
	);

	add_settings_field(
		'logestay_payments_enabled_cash',
		__('Enable Cash Payment', 'logestay'),
		'logestay_field_checkbox',
		$page,
		'logestay_section_cash',
		['key' => 'logestay_payments_enabled_cash']
	);

	add_settings_field(
		'logestay_cash_office_name',
		__('Office Name', 'logestay'),
		'logestay_field_text',
		$page,
		'logestay_section_cash',
		['key' => 'logestay_cash_office_name', 'placeholder' => 'LOGESTAY Office']
	);

	add_settings_field(
		'logestay_cash_office_address',
		__('Office Address', 'logestay'),
		'logestay_field_text',
		$page,
		'logestay_section_cash',
		['key' => 'logestay_cash_office_address', 'placeholder' => '12 Rue de la République, 31000 Toulouse']
	);

	add_settings_field(
		'logestay_cash_hours',
		__('Horaires', 'logestay'),
		'logestay_field_text',
		$page,
		'logestay_section_cash',
		['key' => 'logestay_cash_hours', 'placeholder' => 'Du lundi au vendredi : 09:00 – 18:00 | Samedi : 10:00 – 16:00']
	);

	add_settings_field(
		'logestay_cash_hold_time',
		__('Hold Time(Hours)', 'logestay'),
		'logestay_field_text',
		$page,
		'logestay_section_cash',
		['key' => 'logestay_cash_hold_time', 'placeholder' => '24']
	);

	/* ---------------------------------------------------------
	 * PAYMENT LINK TAB
	 * --------------------------------------------------------- */
	add_settings_section(
		'logestay_section_payment_link',
		__('Payment Link', 'logestay'),
		'__return_false',
		$page
	);

	add_settings_field(
		'logestay_payments_enabled_link',
		__('Enable Payment Link', 'logestay'),
		'logestay_field_checkbox',
		$page,
		'logestay_section_payment_link',
		['key' => 'logestay_payments_enabled_link']
	);

	add_settings_field(
		'logestay_link_hold_time',
		__('Hold Time(Hours)', 'logestay'),
		'logestay_field_text',
		$page,
		'logestay_section_payment_link',
		['key' => 'logestay_link_hold_time', 'placeholder' => '24']
	);

	add_settings_field(
		'logestay_payment_link_url',
		__('Lien de paiement / Payment URL', 'logestay'),
		'logestay_field_url',
		$page,
		'logestay_section_payment_link',
		[
			'key' => 'logestay_payment_link_url',
			'placeholder' => 'https://pay.example.com/checkout/booking-123',
			'description' => __('This exact URL will be sent to guests when Payment Link is selected.', 'logestay'),
		]
	);

}

/**
 * Enqueue tab UI only for this settings page.
 */
add_action('admin_enqueue_scripts', function ($hook) {
	// This hook will look like: loge-stay_page_logestay-settings-payments
	if ( strpos($hook, 'logestay-settings-payments') === false ) return;

	wp_add_inline_style('wp-admin', '
		.logestay-settings-wrap .form-table th { width: 260px; }
		.ls-tab-panel { margin-top: 14px; }
	');

	wp_add_inline_script('jquery', "
		jQuery(function($){
			var \$tabs = $('.ls-tabs a.nav-tab');
			var \$panels = $('.ls-tab-panel');

			function openTab(id){
				\$tabs.removeClass('nav-tab-active');
				\$tabs.filter('[href=\"'+id+'\"]').addClass('nav-tab-active');

				\$panels.hide();
				$(id).show();
				if(window.history && window.history.replaceState){
					window.history.replaceState(null, document.title, id);
				}
			}

			\$tabs.on('click', function(e){
				e.preventDefault();
				openTab($(this).attr('href'));
			});

			var hash = window.location.hash || '#ls-tab-stripe';
			if(!$(hash).length){ hash = '#ls-tab-stripe'; }
			openTab(hash);
		});
	");
});

/**
 * Render page
 */
function logestay_settings_page_payments() {
	if ( ! current_user_can('manage_options') ) return;

	$page = 'logestay-settings-payments';
	$link_enabled = (bool) logestay_get_option('logestay_payments_enabled_link', 0);
	$link_url = trim((string) logestay_get_option('logestay_payment_link_url', ''));
	?>
	<div class="wrap logestay-settings-wrap">
		<h1><?php esc_html_e('LOGE STAY – Payments', 'logestay'); ?></h1>

		<?php if ( $link_enabled && $link_url === '' ) : ?>
			<div class="notice notice-warning inline">
				<p>
					<?php esc_html_e('Payment Link is enabled but no payment URL has been added yet. Guests cannot use this method until a valid URL is saved.', 'logestay'); ?>
				</p>
			</div>
		<?php endif; ?>

		<h2 class="nav-tab-wrapper ls-tabs">
			<a href="#ls-tab-stripe" class="nav-tab"><?php esc_html_e('Credit Card', 'logestay'); ?></a>
			<a href="#ls-tab-paypal" class="nav-tab"><?php esc_html_e('PayPal', 'logestay'); ?></a>
			<a href="#ls-tab-bank" class="nav-tab"><?php esc_html_e('Bank Transfer', 'logestay'); ?></a>
			<a href="#ls-tab-cash" class="nav-tab"><?php esc_html_e('Cash Payment', 'logestay'); ?></a>
			<a href="#ls-tab-link" class="nav-tab"><?php esc_html_e('Payment Link', 'logestay'); ?></a>
		</h2>

		<form method="post" action="options.php">
			<?php settings_fields('logestay_settings_group'); ?>
			  <input type="hidden" name="logestay_settings[_ls_page]" value="payments">

			<div id="ls-tab-stripe" class="ls-tab-panel" style="display:none;">
				<table class="form-table" role="presentation">
					<?php do_settings_fields($page, 'logestay_section_stripe'); ?>
				</table>
			</div>

			<div id="ls-tab-paypal" class="ls-tab-panel" style="display:none;">
				<table class="form-table" role="presentation">
					<?php do_settings_fields($page, 'logestay_section_paypal'); ?>
				</table>
			</div>

			<div id="ls-tab-bank" class="ls-tab-panel" style="display:none;">
				<table class="form-table" role="presentation">
					<?php do_settings_fields($page, 'logestay_section_bank'); ?>
				</table>
			</div>

			<div id="ls-tab-cash" class="ls-tab-panel" style="display:none;">
				<table class="form-table" role="presentation">
					<?php do_settings_fields($page, 'logestay_section_cash'); ?>
				</table>
			</div>

			<div id="ls-tab-link" class="ls-tab-panel" style="display:none;">
				<table class="form-table" role="presentation">
					<?php do_settings_fields($page, 'logestay_section_payment_link'); ?>
				</table>
			</div>

			<?php submit_button(__('Save Changes', 'logestay')); ?>
		</form>
	</div>
	<?php
}
