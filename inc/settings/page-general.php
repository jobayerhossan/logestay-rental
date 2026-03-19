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
    'logestay_contact_email',
    __('Contact Form Email', 'logestay'),
    'logestay_field_email',
    'logestay-settings-general',
    'logestay_section_general',
    [
      'key' => 'logestay_contact_email',
      'description' => __('Where contact form messages will be sent.', 'logestay'),
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
      do_settings_sections('logestay-settings-general');
      submit_button();
      ?>
    </form>
  </div>
  <?php
}