<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Helper: check if Default Site Page is enabled
 */
function logestay_default_page_enabled(): bool {
  $opts = get_option('logestay_settings', []);
  return ! empty($opts['logestay_default_page_enabled']);
}

/**
 * Frontend redirect to Default Site Page template
 */
add_action('template_redirect', function () {

  // Only frontend
  if ( is_admin() ) return;

  // Only if enabled
  if ( ! logestay_default_page_enabled() ) return;

  // Logged-in users can browse normally
  if ( is_user_logged_in() ) return;

  // Allow admin-ajax
  if ( wp_doing_ajax() ) return;

  // Allow REST if needed
  if ( defined('REST_REQUEST') && REST_REQUEST ) return;

  $req = $_SERVER['REQUEST_URI'] ?? '';
  $path = wp_parse_url( home_url( $req ), PHP_URL_PATH );
  $path = trim( (string) $path, '/' );

  // Allow wp-login and wp-admin
  if ( strpos( $req, 'wp-login.php' ) !== false ) return;
  if ( strpos( $req, 'wp-admin' ) !== false ) return;

  // Allow custom login route with or without language prefix
  $allowed_paths = array(
    'login',
    'fr/login',
    'en/login',
    'es/login',
    'pt/login',
  );

  if ( in_array( $path, $allowed_paths, true ) ) {
    return;
  }

  // Optional: allow password reset routes if needed later
  // Example:
  // if ( in_array( $path, array( 'reset-password', 'fr/reset-password', 'en/reset-password', 'es/reset-password', 'pt/reset-password' ), true ) ) {
  //     return;
  // }

  $file = get_template_directory() . '/templates/logestay-default-page.php';
  if ( file_exists( $file ) ) {
    status_header(200);
    nocache_headers();
    include $file;
    exit;
  }

}, 1);

add_action('admin_notices', function () {

  if ( ! current_user_can('manage_options') ) return;
  if ( ! logestay_default_page_enabled() ) return;

  $settings_url = admin_url('admin.php?page=logestay-settings');

  echo '<div class="notice notice-warning" style="border-left-color:#f59e0b;">';
  echo '<p style="margin:0; padding:6px 0;">';
  echo '<strong>' . esc_html__('Default Site Page is currently enabled.', 'logestay') . '</strong> ';
  echo esc_html__('Visitors will only see the default page until you disable it.', 'logestay') . ' ';
  echo '<a href="' . esc_url($settings_url) . '" class="button button-primary" style="margin-left:10px;">'
    . esc_html__('Go live now', 'logestay') . '</a>';
  echo '</p>';
  echo '</div>';

});