<?php
if ( ! defined('ABSPATH') ) exit;

function logestay_register_settings_general() {

  add_settings_section(
    'logestay_section_general',
    '',
    '__return_false',
    'logestay-settings-general'
  );

  add_settings_field(
    'logestay_simple_mode_enabled',
    __('Enable Simple Mode (Showcase Mode)', 'logestay'),
    'logestay_field_checkbox',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_simple_mode_enabled',
      'description' => __('When enabled, the booking popup switches to a simplified showcase mode with partner and direct-contact buttons instead of the booking flow.', 'logestay'),
    ]
  );

  add_settings_field(
    'logestay_airbnb_enabled',
    __('Enable Airbnb Button', 'logestay'),
    'logestay_field_checkbox',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_airbnb_enabled',
      'description' => __('Uses the Airbnb link saved on each listing. If no listing link is filled, the button stays hidden automatically.', 'logestay'),
    ]
  );

  add_settings_field(
    'logestay_booking_enabled',
    __('Enable Booking.com Button', 'logestay'),
    'logestay_field_checkbox',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_booking_enabled',
      'description' => __('Uses the Booking.com link saved on each listing. If no listing link is filled, the button stays hidden automatically.', 'logestay'),
    ]
  );

  add_settings_field(
    'logestay_contact_whatsapp_enabled',
    __('Enable WhatsApp Button', 'logestay'),
    'logestay_field_checkbox',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_contact_whatsapp_enabled',
    ]
  );

  add_settings_field(
    'logestay_contact_whatsapp',
    __('WhatsApp Number / Link', 'logestay'),
    'logestay_field_text',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_contact_whatsapp',
      'placeholder' => '+33612345678 or https://wa.me/33612345678',
      'description' => __('If you enter only a number, the frontend will automatically build the WhatsApp link.', 'logestay'),
    ]
  );

  add_settings_field(
    'logestay_contact_phone_enabled',
    __('Enable Phone Button', 'logestay'),
    'logestay_field_checkbox',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_contact_phone_enabled',
    ]
  );

  add_settings_field(
    'logestay_contact_phone',
    __('Phone Number', 'logestay'),
    'logestay_field_text',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_contact_phone',
      'placeholder' => '+33 1 42 86 83 26',
    ]
  );

  add_settings_field(
    'logestay_contact_email_enabled',
    __('Enable Email Button', 'logestay'),
    'logestay_field_checkbox',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_contact_email_enabled',
    ]
  );

  add_settings_field(
    'logestay_contact_email',
    __('Email', 'logestay'),
    'logestay_field_email',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_contact_email',
      'description' => __('Used for the contact form and for the showcase-mode email button.', 'logestay'),
    ]
  );

  add_settings_field(
    'logestay_default_page_enabled',
    __('Enable Default Site Page (public)', 'logestay'),
    'logestay_field_checkbox',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_default_page_enabled',
    ]
  );

  add_settings_field(
    'logestay_logo_id',
    __('Logo', 'logestay'),
    'logestay_field_media',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_logo_id',
      'button' => __('Choose Logo', 'logestay'),
      'remove' => __('Remove', 'logestay'),
      'description' => __('Upload/select a logo. (Stores attachment ID)', 'logestay'),
    ]
  );

  add_settings_field(
    'logestay_email_logo_id',
    __('Logo for Email Header', 'logestay'),
    'logestay_field_media',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_email_logo_id',
      'button' => __('Choose Logo', 'logestay'),
      'remove' => __('Remove', 'logestay'),
      'description' => __('Upload/select a logo for email header', 'logestay'),
    ]
  );

  add_settings_field(
    'logestay_footer_title',
    __('Footer Title', 'logestay'),
    'logestay_field_text',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_footer_title',
      'description' => __('The text will be shown on the Footer', 'logestay'),
    ]
  );

  add_settings_field(
    'logestay_footer_subtitle',
    __('Footer Subtitle', 'logestay'),
    'logestay_field_text',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_footer_subtitle',
      'description' => __('The text will be shown on the Footer', 'logestay'),
    ]
  );

  add_settings_field(
    'logestay_copyright',
    __('Footer Copyright', 'logestay'),
    'logestay_field_text',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_copyright',
      'description' => __('The text will be shown on the Footer', 'logestay'),
    ]
  );

  

}

function logestay_settings_page_general() {
  if ( ! current_user_can('manage_options') ) return;
  ?>
  <div class="wrap logestay-settings-wrap">
    <h1><?php esc_html_e('LOGE STAY – General', 'logestay'); ?></h1>

    <form method="post" action="options.php">
      <?php
      settings_fields('logestay_settings_group');
      ?>
      <input type="hidden" name="logestay_settings[_ls_page]" value="general">
      <?php
      do_settings_sections('logestay-settings-general');
      submit_button();
      ?>
    </form>
  </div>
  <?php
}
