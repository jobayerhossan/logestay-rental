<?php if ( ! defined('ABSPATH') ) exit; ?>

<p style="margin:0 0 14px;color:#111827;font-size:16px;">
  <?php echo esc_html__('Hello', 'logestay'); ?>
  <?php echo $guest_name ? esc_html($guest_name) : esc_html__('there', 'logestay'); ?>,
</p>

<h2 style="margin:0 0 14px;font-size:28px;line-height:1.2;color:#111827;">
  <?php esc_html_e('Payment pending', 'logestay'); ?>
</h2>

<div style="margin:14px 0 18px;">
  <span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#FEF3C7;color:#92400E;font-weight:700;font-size:12px;margin-right:8px;">
    <?php esc_html_e('Pending', 'logestay'); ?>
  </span>
  <span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#D1FAE5;color:#065F46;font-weight:700;font-size:12px;">
    <?php echo esc_html( ucfirst($booking_status ?: 'pending') ); ?>
  </span>
</div>

<div style="background:#F9FAFB;border:1px solid #EEF2F7;border-radius:14px;padding:16px;">
  <p style="margin:0 0 6px;color:#374151;font-weight:700;">
    <?php esc_html_e('Payment details', 'logestay'); ?>
  </p>

  <p style="margin:0 0 10px;color:#6B7280;font-size:14px;">
    <?php esc_html_e('Your payment is being processed.', 'logestay'); ?>
  </p>

  <div style="font-size:28px;font-weight:800;color:#111827;">
    <?php echo number_format_i18n((float)$total, 2); ?> <?php echo esc_html($currency); ?>
    <span style="font-size:12px;font-weight:700;color:#6B7280;">
      <?php echo esc_html__('via', 'logestay'); ?> <?php echo esc_html($payment_method ?: 'card'); ?>
    </span>
  </div>
</div>

<div style="margin:18px 0 0;display:flex;gap:12px;flex-wrap:wrap;">
  <a href="<?php echo esc_url($account_url); ?>" style="display:inline-block;background:#F97316;color:#fff;text-decoration:none;font-weight:800;padding:12px 18px;border-radius:12px;">
    <?php esc_html_e('Complete payment', 'logestay'); ?>
  </a>

  <a href="mailto:<?php echo esc_attr($support_email); ?>" style="display:inline-block;background:#F3F4F6;color:#111827;text-decoration:none;font-weight:800;padding:12px 18px;border-radius:12px;">
    <?php esc_html_e('Contact support', 'logestay'); ?>
  </a>
</div>