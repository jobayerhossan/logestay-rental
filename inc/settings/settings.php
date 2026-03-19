<?php
/**
 * LOGESTAY Settings (submenu pages)
 *
 * @package logestay
 */
if ( ! defined('ABSPATH') ) exit;

/**
 * Option helper
 */
function logestay_get_option( string $key, $default = '' ) {
  $options = get_option('logestay_settings', []);
  return isset($options[$key]) ? $options[$key] : $default;
}

/**
 * Register menu + submenus
 */
add_action('admin_menu', function () {

  // Parent menu
  add_menu_page(
    __('LOGE STAY', 'logestay'),
    __('LOGE STAY', 'logestay'),
    'manage_options',
    'logestay-settings',
    'logestay_settings_page_general', // default page
    'dashicons-admin-generic',
    61
  );

  // Subpages
  add_submenu_page(
    'logestay-settings',
    __('General', 'logestay'),
    __('General', 'logestay'),
    'manage_options',
    'logestay-settings',
    'logestay_settings_page_general'
  );

  add_submenu_page(
    'logestay-settings',
    __('Colors', 'logestay'),
    __('Colors', 'logestay'),
    'manage_options',
    'logestay-settings-colors',
    'logestay_settings_page_colors'
  );

  add_submenu_page(
    'logestay-settings',
    __('Payments', 'logestay'),
    __('Payments', 'logestay'),
    'manage_options',
    'logestay-settings-payments',
    'logestay_settings_page_payments'
  );
  add_submenu_page(
    'logestay-settings',
    __('Maintenance', 'logestay'),
    __('Maintenance', 'logestay'),
    'manage_options',
    'logestay-settings-maintenance',
    'logestay_settings_page_maintenance'
  );
});

/**
 * Register settings + sections/fields for EACH page
 */
add_action('admin_init', function () {

  register_setting(
    'logestay_settings_group',
    'logestay_settings',
    [
      'type'              => 'array',
      'sanitize_callback' => 'logestay_sanitize_settings',
      'default'           => [],
    ]
  );

  /**
   * Load per-page field registration
   */
  $base = get_template_directory() . '/inc/settings/';
  require_once $base . 'page-general.php';
  require_once $base . 'page-colors.php';
  require_once $base . 'page-payments.php';
  require_once $base . 'page-maintenance.php';

  // register fields (functions are in those files)
  if ( function_exists('logestay_register_settings_general') )  logestay_register_settings_general();
  if ( function_exists('logestay_register_settings_colors') )   logestay_register_settings_colors();
  if ( function_exists('logestay_register_settings_payments') ) logestay_register_settings_payments();
  if ( function_exists('logestay_register_settings_maintenance') ) logestay_register_settings_maintenance();
});

/**
 * Enqueue: color picker only where needed
 */
add_action('admin_enqueue_scripts', function ($hook) {

  // our pages:
  $is_general  = ($hook === 'toplevel_page_logestay-settings');
  $is_colors   = ($hook === 'logestay-settings_page_logestay-settings-colors');
  $is_payments = ($hook === 'logestay-settings_page_logestay-settings-payments');

  //if ( ! $is_general && ! $is_colors && ! $is_payments ) return;

  // Color picker only on Colors page
  
  wp_enqueue_style('wp-color-picker');
  wp_enqueue_script('wp-color-picker');

  wp_add_inline_script('wp-color-picker', "jQuery(function($){ $('.logestay-color-field').wpColorPicker(); });");


  // Small admin style (optional)
  wp_add_inline_style('wp-admin', '
    .logestay-settings-wrap .form-table th { width: 260px; }
    .logestay-settings-wrap .postbox { max-width: 920px; }
  ');
});

add_action('admin_enqueue_scripts', function ($hook) {

  wp_enqueue_media();

  wp_add_inline_script('jquery', '
    jQuery(function($){
      var frame;

      $(document).on("click", ".logestay-media-upload", function(e){
        e.preventDefault();

        var $wrap = $(this).closest(".logestay-media-field");
        var $idField = $wrap.find(".logestay-media-id");
        var $preview = $wrap.find(".logestay-media-preview");
        var $remove  = $wrap.find(".logestay-media-remove");

        if(frame){
          frame.off("select");
        }

        frame = wp.media({
          title: "Select Logo",
          button: { text: "Use this logo" },
          multiple: false
        });

        frame.on("select", function(){
          var att = frame.state().get("selection").first().toJSON();
          if(!att || !att.id) return;

          $idField.val(att.id);

          var url = (att.sizes && att.sizes.medium) ? att.sizes.medium.url : att.url;
          $preview.html(
            "<img src=\'" + url + "\' style=\'max-width:180px;height:auto;display:block;border:1px solid #e5e7eb;border-radius:10px;padding:8px;background:#fff;\'>"
          );

          $remove.show();
        });

        frame.open();
      });

      $(document).on("click", ".logestay-media-remove", function(e){
        e.preventDefault();

        var $wrap = $(this).closest(".logestay-media-field");

        $wrap.find(".logestay-media-id").val("");
        $wrap.find(".logestay-media-preview").html(
          "<div style=\'width:200px;height:90px;border:1px dashed #cbd5e1;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#64748b;background:#f8fafc;\'>No logo selected</div>"
        );

        $(this).hide();
      });
    });
    ');
  
});

/* ---------------------------------------------------------
 * Field renderers (reused by all pages)
 * --------------------------------------------------------- */
function logestay_field_media(array $args) {
  $key   = $args['key'];
  $id    = (int) logestay_get_option($key, 0);

  $button = $args['button'] ?? __('Choose', 'logestay');
  $remove = $args['remove'] ?? __('Remove', 'logestay');

  $img = '';
  $url = '';
  if ($id) {
    $url = wp_get_attachment_image_url($id, 'medium');
    if ($url) {
      $img = '<img src="' . esc_url($url) . '" style="max-width:180px;height:auto;display:block;border:1px solid #e5e7eb;border-radius:10px;padding:8px;background:#fff;">';
    }
  }

  echo '<div class="logestay-media-field" style="display:flex;gap:14px;align-items:flex-start;flex-wrap:wrap;">';

  echo '<div class="logestay-media-preview" style="min-width:200px;">';
  echo $img ? $img : '<div style="width:200px;height:90px;border:1px dashed #cbd5e1;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#64748b;background:#f8fafc;">No logo selected</div>';
  echo '</div>';

  echo '<div>';
  echo '<input type="hidden" class="logestay-media-id" name="logestay_settings[' . esc_attr($key) . ']" value="' . esc_attr($id) . '">';
  echo '<button type="button" class="button button-primary logestay-media-upload">' . esc_html($button) . '</button> ';
  echo '<button type="button" class="button logestay-media-remove" ' . ($id ? '' : 'style="display:none;"') . '>' . esc_html($remove) . '</button>';

  if (!empty($args['description'])) {
    echo '<p class="description" style="margin-top:8px;">' . esc_html($args['description']) . '</p>';
  }

  echo '</div>';
  echo '</div>';
}
function logestay_field_checkbox( array $args ) {
  $key   = $args['key'];
  $value = (bool) logestay_get_option($key, false);

  echo '<label><input type="checkbox" name="logestay_settings[' . esc_attr($key) . ']" value="1" ' . checked($value, true, false) . '> ';
  echo esc_html__('Enabled', 'logestay') . '</label>';
}

function logestay_field_email( array $args ) {
  $key   = $args['key'];
  $value = esc_attr( logestay_get_option($key, '') );

  echo '<input type="email" class="regular-text" name="logestay_settings[' . esc_attr($key) . ']" value="' . $value . '" placeholder="support@example.com">';

  if ( ! empty($args['description']) ) {
    echo '<p class="description">' . esc_html($args['description']) . '</p>';
  }
}

function logestay_field_url( array $args ) {
  $key   = $args['key'];
  $value = esc_attr( logestay_get_option($key, '') );
  $ph    = ! empty($args['placeholder']) ? esc_attr($args['placeholder']) : '';

  echo '<input type="url" class="regular-text" name="logestay_settings[' . esc_attr($key) . ']" value="' . $value . '" placeholder="' . $ph . '">';
}

function logestay_field_color( array $args ) {
  $key   = $args['key'];
  $value = esc_attr( logestay_get_option($key, '') );

  echo '<input type="text" class="regular-text logestay-color-field" name="logestay_settings[' . esc_attr($key) . ']" value="' . $value . '" data-default-color="#000000">';
}

function logestay_field_text( array $args ) {
  $key   = $args['key'];
  $value = esc_attr( logestay_get_option($key, '') );
  $ph    = ! empty($args['placeholder']) ? esc_attr($args['placeholder']) : '';

  echo '<input type="text" class="regular-text" name="logestay_settings[' . esc_attr($key) . ']" value="' . $value . '" placeholder="' . $ph . '">';
}

function logestay_field_password( array $args ) {
  $key   = $args['key'];
  $value = esc_attr( logestay_get_option($key, '') );
  $ph    = ! empty($args['placeholder']) ? esc_attr($args['placeholder']) : '';

  echo '<input type="password" class="regular-text" name="logestay_settings[' . esc_attr($key) . ']" value="' . $value . '" placeholder="' . $ph . '" autocomplete="new-password">';
}

function logestay_field_select( array $args ) {
  $key     = $args['key'];
  $value   = esc_attr( logestay_get_option($key, $args['default'] ?? 'test') );
  $options = $args['options'] ?? [];

  echo '<select name="logestay_settings[' . esc_attr($key) . ']">';
  foreach ($options as $k => $label) {
    echo '<option value="' . esc_attr($k) . '" ' . selected($value, $k, false) . '>' . esc_html($label) . '</option>';
  }
  echo '</select>';

  if ( ! empty($args['description']) ) {
    echo '<p class="description">' . esc_html($args['description']) . '</p>';
  }
}

/* ---------------------------------------------------------
 * Sanitize (single place)
 * --------------------------------------------------------- */
function logestay_sanitize_settings( $input ) {
  $input = is_array($input) ? $input : [];

  // ✅ IMPORTANT: start from existing saved options
  $out = get_option('logestay_settings', []);
  $out = is_array($out) ? $out : [];
  $page = isset($input['_ls_page']) ? sanitize_text_field($input['_ls_page']) : '';

  // Helper: checkbox (keeps old value if field not present in POST)
  $set_bool = function($key) use (&$out, $input) {
    if ( array_key_exists($key, $input) ) {
      $out[$key] = ! empty($input[$key]) ? 1 : 0;
    }
  };

  // Helper: text-like (keeps old value if field not present in POST)
  $set_text = function($key, $sanitize = 'sanitize_text_field') use (&$out, $input) {
    if ( array_key_exists($key, $input) ) {
      $out[$key] = call_user_func($sanitize, $input[$key]);
    }
  };

  // Helper: email
  $set_email = function($key) use (&$out, $input) {
    if ( array_key_exists($key, $input) ) {
      $out[$key] = sanitize_email($input[$key]);
    }
  };

  // Helper: url
  $set_url = function($key) use (&$out, $input) {
    if ( array_key_exists($key, $input) ) {
      $out[$key] = esc_url_raw($input[$key]);
    }
  };

  // Helper: absint
  $set_int = function($key) use (&$out, $input) {
    if ( array_key_exists($key, $input) ) {
      $out[$key] = absint($input[$key]);
    }
  };

  /* --------------------------
   * GENERAL
   * -------------------------- */
  $set_email('logestay_contact_email');
  $set_text('logestay_footer_title');
  $set_text('logestay_footer_subtitle');
  $set_text('logestay_copyright');
  $set_int('logestay_logo_id');
  $set_int('logestay_email_logo_id');

  if ( isset($input['logestay_default_page_enabled']) ) {
    $out['logestay_default_page_enabled'] = wp_kses_post(
      $input['logestay_default_page_enabled']
    );
  }
  $out['logestay_default_page_enabled'] = ! empty($input['logestay_default_page_enabled']) ? 1 : 0;

  // If you have these on general page too:
  $set_bool('logestay_airbnb_enabled');
  $set_url('logestay_airbnb_link');
  $set_bool('logestay_booking_enabled');
  $set_url('logestay_booking_link');

  /* --------------------------
   * COLORS
   * -------------------------- */
  foreach (['logestay_color_primary', 'logestay_color_secondary'] as $k) {
    if ( array_key_exists($k, $input) ) {
      $v = trim((string)$input[$k]);
      $out[$k] = ($v === '') ? '' : sanitize_text_field($v);
    }
  }


  $set_text('logestay_maint_title');
  $set_text('logestay_maintenance_subtitle');
  $set_text('logestay_maintenance_message');
  $set_text('logestay_maint_contact_title');
  $set_text('logestay_maint_footer_note');
  $set_text('logestay_credit');

  $set_text('logestay_contact_whatsapp');
  $set_url('logestay_contact_phone');
  $set_text('logestay_contact_email');

  if ( isset($input['logestay_maintenance_note']) ) {
    $out['logestay_maintenance_note'] = wp_kses_post(
      $input['logestay_maintenance_note']
    );
  }

  $out['logestay_maintenance_enabled'] = ! empty($input['logestay_maintenance_enabled']) ? 1 : 0;

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

  if ( $page === 'payments' ) {
    // On payments page: missing checkbox = unchecked = 0
    foreach ( $payment_bool_keys as $k ) {
      $out[$k] = ! empty($input[$k]) ? 1 : 0;
    }
  } else {
    // Other pages: only update if present (don’t reset)
    foreach ( $payment_bool_keys as $k ) {
      if ( array_key_exists($k, $input) ) {
        $out[$k] = ! empty($input[$k]) ? 1 : 0;
      }
    }
  }

  /* --------------------------
   * STRIPE
   * -------------------------- */
  if ( array_key_exists('logestay_stripe_mode', $input) ) {
    $mode = sanitize_text_field($input['logestay_stripe_mode']);
    $out['logestay_stripe_mode'] = in_array($mode, ['test','live'], true) ? $mode : 'test';
  }

  $set_text('logestay_stripe_test_pk');
  $set_text('logestay_stripe_test_sk');
  $set_text('logestay_stripe_live_pk');
  $set_text('logestay_stripe_live_sk');
  $set_text('logestay_stripe_test_whsec');
  $set_text('logestay_stripe_live_whsec');
  

  /* --------------------------
   * PAYPAL EXTRA
   * -------------------------- */
  if ( array_key_exists('logestay_paypal_mode', $input) ) {
    $mode = sanitize_text_field($input['logestay_paypal_mode']);
    $out['logestay_paypal_mode'] = in_array($mode, ['test','live'], true) ? $mode : 'test';
  }
  $set_text('logestay_paypal_test_client_id');
  $set_text('logestay_paypal_test_secret');
  $set_text('logestay_paypal_live_client_id');
  $set_text('logestay_paypal_live_secret');

  /* --------------------------
   * BANK EXTRA
   * -------------------------- */
  $set_text('logestay_bank_beneficiary');
  $set_text('logestay_bank_iban');
  $set_text('logestay_bank_bic');
  $set_text('logestay_bank_reference_prefix');
  $set_text('logestay_bank_hold_time');

  /* --------------------------
   * CASH / PAYMENT LINK EXTRA
   * -------------------------- */
  $set_text('logestay_cash_note');
  $set_text('logestay_cash_office_name');
  $set_text('logestay_cash_office_address');
  $set_text('logestay_cash_hours');
  $set_text('logestay_cash_hold_time');
  $set_text('logestay_link_hold_time');

  return $out;
}