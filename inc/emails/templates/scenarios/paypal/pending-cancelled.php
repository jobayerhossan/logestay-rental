<?php if ( ! defined('ABSPATH') ) exit;

$email_title        = 'Réservation annulée';
$email_payment_text = "Votre réservation a été annulée. Si vous avez effectué un paiement, il vous sera remboursé dans les 5 à 10 jours ouvrés.";
$email_method_label = 'PayPal';

$email_badge_payment = ['text'=>'En attente','tone'=>'warning'];
$email_badge_booking = ['text'=>'Annulée','tone'=>'danger'];

// No big CTA in your screenshot for cancelled
$email_cta_primary   = false;
$email_cta_secondary = false;

$email_body_html = '
<div style="margin-top:16px;background:#F8FAFC;border:1px solid #CBD5E1;border-radius:14px;padding:14px;display:flex;gap:10px;align-items:flex-start;">
  <div style="font-weight:900;color:#0F172A;">ⓘ</div>
  <div style="color:#334155;font-size:13px;line-height:1.5;">
    Votre réservation a été annulée. Aucune action n\'est requise.
  </div>
</div>';

include get_template_directory() . '/inc/emails/templates/layouts/layout-paypal.php';