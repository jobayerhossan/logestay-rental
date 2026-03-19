<?php
if ( ! defined('ABSPATH') ) exit;

function logestay_register_settings_maintenance() {

  add_settings_section(
    'logestay_section_maintenance',
    __('Maintenance Mode', 'logestay'),
    '__return_false',
    'logestay-settings-maintenance'
  );

  add_settings_field(
    'logestay_maintenance_enabled',
    __('Enable Maintenance Mode', 'logestay'),
    'logestay_field_checkbox',
    'logestay-settings-maintenance',
    'logestay_section_maintenance',
    ['key' => 'logestay_maintenance_enabled']
  );

  // Texts
  $maint_fields = [
    'logestay_maint_title'           => __('Title', 'logestay'),
    'logestay_maint_subtitle'        => __('Subtitle', 'logestay'),
    'logestay_maintenance_message'   => __('Message', 'logestay'),
    'logestay_maintenance_note'       => __('Note', 'logestay'),
    'logestay_maint_contact_title'   => __('Contact title', 'logestay'),
    'logestay_maint_footer_note'     => __('Footer note', 'logestay'),
    'logestay_contact_whatsapp'  => __('WhatsApp Link', 'logestay'),
    'logestay_contact_phone'  => __('Phone', 'logestay'),
    'logestay_contact_email'  => __('Email', 'logestay'),
    'logestay_credit'  => __('Credit', 'logestay'),
  ];

  foreach ($maint_fields as $key => $label) {
    $cb = (strpos($key, '_url') !== false) ? 'logestay_field_url' : 'logestay_field_text';

    add_settings_field(
      $key,
      $label,
      $cb,
      'logestay-settings-maintenance',
      'logestay_section_maintenance',
      ['key' => $key]
    );
  }
}

function logestay_settings_page_maintenance() {
  if ( ! current_user_can('manage_options') ) return;
  ?>
  <div class="wrap logestay-settings-wrap">
    <h1><?php esc_html_e('Maintenance', 'logestay'); ?></h1>

    <form method="post" action="options.php">
      <?php
      settings_fields('logestay_settings_group');
      do_settings_sections('logestay-settings-maintenance');
      submit_button();
      ?>
    </form>
  </div>
  <?php
}