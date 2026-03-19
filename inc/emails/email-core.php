<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Get custom logo URL (uses WP Customizer "Site Identity")
 */
function logestay_get_email_logo_url(): string {
	$settings = get_option('logestay_settings');
	$custom_logo_id = $settings['logestay_email_logo_id'];
	if ( $custom_logo_id ) {
		$url = wp_get_attachment_image_url($custom_logo_id, 'full');
		if ($url) return $url;
	}
	return ''; 
}

/**
 * Load template file with variables, return HTML
 */
function logestay_email_render_template(string $template, array $vars = []): string {
  $base = get_template_directory() . '/inc/emails/templates/';
  $file = $base . ltrim($template, '/');

  if ( ! file_exists($file) ) {
    return '';
  }

 
  ob_start();
  extract($vars, EXTR_SKIP);

  // Capture return value too
  $returned = include $file;

  $buffered = (string) ob_get_clean();

  // If template used echo -> buffered has content
  if ( trim($buffered) !== '' ) {
    return $buffered;
  }

  // If template returned a string -> use it
  if ( is_string($returned) && trim($returned) !== '' ) {
    return $returned;
  }

  return '';
}

/**
 * Get header/footer partials
 */
function logestay_email_get_header(array $vars = []): string {
	return logestay_email_render_template('partials/email-header.php', $vars);
}
function logestay_email_get_footer(array $vars = []): string {
	return logestay_email_render_template('partials/email-footer.php', $vars);
}

/**
 * Wrap content with header/footer and global wrapper.
 */
function logestay_email_wrap(string $content, array $args = []): string {
	$args = wp_parse_args($args, [
		'preheader' => '',
		'logo_url'  => logestay_get_email_logo_url(),
		'site_name' => get_bloginfo('name'),
	]);

	$header = logestay_email_get_header($args);
	$footer = logestay_email_get_footer($args);

	// Hooks (WooCommerce-style)
	$header = apply_filters('logestay_email_header', $header, $args);
	$footer = apply_filters('logestay_email_footer', $footer, $args);
	$content = apply_filters('logestay_email_content', $content, $args);

	return $header . $content . $footer;
}

/**
 * Send HTML email
 */
function logestay_mail(string $to, string $subject, string $html, array $headers = [], array $attachments = []): bool {
	$headers[] = 'Content-Type: text/html; charset=UTF-8';

	$from_name  = apply_filters('logestay_email_from_name', get_bloginfo('name'));
	$from_email = apply_filters('logestay_email_from_email', get_option('admin_email'));

	$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';

	return wp_mail($to, $subject, $html, $headers, $attachments);
}