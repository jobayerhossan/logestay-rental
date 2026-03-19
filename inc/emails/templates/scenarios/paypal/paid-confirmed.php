<?php if ( ! defined('ABSPATH') ) exit;

$email_title        = 'Paiement confirmé';
$email_payment_text = 'Votre paiement via PayPal a été confirmé.';
$email_method_label = 'PayPal';

$email_badge_payment = ['text'=>'Payé','tone'=>'success'];
$email_badge_booking = ['text'=>'Confirmée','tone'=>'success'];

// CTA in screenshot: "Voir ma localisation"
$email_cta_primary   = ['label'=>'Voir ma localisation','url'=>$account_url ?: $site_url];
$email_cta_secondary = false;

// Build a small “stay card” (uses vars you already have)
$check_in  = isset($check_in) ? (string)$check_in : '';
$check_out = isset($check_out) ? (string)$check_out : '';
$listing_title = isset($listing_title) ? (string)$listing_title : '';
$city_name = isset($city_name) ? (string)$city_name : '';

$adults   = (int) ($adults ?? (int)get_post_meta($booking_id, 'logestay_adults', true));
$children = (int) ($children ?? (int)get_post_meta($booking_id, 'logestay_children', true));
$pets     = (int) ($pets ?? (int)get_post_meta($booking_id, 'logestay_pets', true));
$travellers = max(0, $adults + $children);

$email_body_html = '
<div style="margin-top:18px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:16px;padding:18px;">
  <div style="font-weight:900;color:#0F172A;font-size:16px;margin-bottom:10px;">📍 Votre séjour</div>
  <div style="color:#0F172A;font-weight:900;margin-bottom:4px;">'.esc_html($listing_title).'</div>
  <div style="color:#334155;font-size:13px;margin-bottom:12px;">'.esc_html($city_name).'</div>

  <div style="border-top:1px solid #BFDBFE;padding-top:12px;display:flex;gap:16px;flex-wrap:wrap;">
    <div style="min-width:220px;">
      <div style="font-size:11px;color:#64748B;font-weight:800;letter-spacing:.06em;margin-bottom:2px;">DATES</div>
      <div style="font-size:13px;color:#0F172A;font-weight:900;">'.esc_html($check_in).' → '.esc_html($check_out).'</div>
    </div>

    <div style="min-width:160px;">
      <div style="font-size:11px;color:#64748B;font-weight:800;letter-spacing:.06em;margin-bottom:2px;">VOYAGEURS</div>
      <div style="font-size:13px;color:#0F172A;font-weight:900;">'.esc_html((string)$travellers).' personnes</div>
    </div>
  </div>
</div>

<div style="margin-top:16px;background:#DCFCE7;border:1px solid #BBF7D0;border-radius:14px;padding:14px;color:#065F46;font-weight:800;font-size:13px;line-height:1.5;">
  ✅ Bon séjour ! Merci pour votre confiance. Nous sommes ravis de vous accueillir avec LOGESTAY.
</div>';

include get_template_directory() . '/inc/emails/templates/layouts/layout-paypal.php';