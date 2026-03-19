<?php
if ( ! defined('ABSPATH') ) exit;

function logestay_register_settings_colors() {

  add_settings_section(
    'logestay_section_colors',
    '',
    '__return_false',
    'logestay-settings-colors'
  );

  add_settings_field(
    'logestay_color_primary',
    __('Primary Color', 'logestay'),
    'logestay_field_color',
    'logestay-settings-colors',
    'logestay_section_colors',
    ['key' => 'logestay_color_primary']
  );

  add_settings_field(
    'logestay_color_secondary',
    __('Secondary Color', 'logestay'),
    'logestay_field_color',
    'logestay-settings-colors',
    'logestay_section_colors',
    ['key' => 'logestay_color_secondary']
  );
}

function logestay_settings_page_colors() {
  if ( ! current_user_can('manage_options') ) return;
  ?>
  <div class="wrap logestay-settings-wrap">
    <h1><?php esc_html_e('LOGE STAY – Colors', 'logestay'); ?></h1>

    <form method="post" action="options.php">
      <?php
      settings_fields('logestay_settings_group');
      do_settings_sections('logestay-settings-colors');
      submit_button();
      ?>
    </form>
  </div>
  <?php
}