<?php if ( ! defined('ABSPATH') ) exit;

$email_title        = 'Paiement confirmé';
$email_badge_payment= ['text'=>'Payé','tone'=>'success'];
$email_badge_booking= ['text'=>'Confirmée','tone'=>'success'];
$email_payment_text = "Votre paiement par carte bancaire a été validé avec succès.";
$email_method_label = 'carte bancaire';

$stay_title   = esc_html($listing_title ?? '');
$stay_city    = esc_html($city_name ?? '');
$stay_dates   = esc_html(($check_in ?? '') . ' → ' . ($check_out ?? ''));
$stay_guests  = (int) get_post_meta($booking_id, 'logestay_adults', true) + (int) get_post_meta($booking_id, 'logestay_children', true);

$email_stay_html = '
<div style="margin-top:16px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:14px;padding:16px;">
  <div style="display:flex;gap:10px;align-items:center;margin-bottom:10px;">
    <div style="width:34px;height:34px;border-radius:999px;background:#DBEAFE;display:inline;align-items:center;justify-content:center;font-weight:900;color:#1D4ED8;  line-height: 34px;
  text-align: center;">📍</div>
    <div style="font-weight:900;color:#0F172A;">Votre séjour</div>
  </div>

  <div style="color:#0F172A;font-weight:900;margin-bottom:6px;">'.$stay_title.'</div>
  <div style="color:#334155;font-size:13px;line-height:1.45;">'.$stay_city.'</div>

  <hr style="border:none;border-top:1px solid #BFDBFE;margin:12px 0;">

  <div style="display:flex;gap:18px;flex-wrap:wrap;">
    <div>
      <div style="font-size:11px;color:#64748B;font-weight:800;letter-spacing:.06em;margin-bottom:2px;">DATES</div>
      <div style="font-weight:900;color:#0F172A;">'.$stay_dates.'</div>
    </div>
    <div>
      <div style="font-size:11px;color:#64748B;font-weight:800;letter-spacing:.06em;margin-bottom:2px;">VOYAGEURS</div>
      <div style="font-weight:900;color:#0F172A;">'.$stay_guests.' personne(s)</div>
    </div>
  </div>

  <div style="margin-top:14px;text-align:center;">
    <a href="'.esc_url($site_url ?? home_url('/')).'"
       style="display:inline-block;background:#F97316;color:#fff;text-decoration:none;font-weight:900;padding:12px 20px;border-radius:12px;">
      📍 Voir ma localisation
    </a>
  </div>

  <div style="margin-top:14px;background:#DCFCE7;border:1px solid #86EFAC;border-radius:12px;padding:12px;color:#065F46;font-weight:800;font-size:13px;">
    ✅ Bon séjour ! Merci pour votre confiance. Nous sommes ravis de vous accueillir avec LOGESTAY.
  </div>
</div>';

$email_cta_primary = false;
$email_cta_secondary = false;

include get_template_directory() . '/inc/emails/templates/layouts/layout-card.php';