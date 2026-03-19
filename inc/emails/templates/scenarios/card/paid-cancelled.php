<?php if ( ! defined('ABSPATH') ) exit;

$email_title        = 'Réservation annulée';
$email_badge_payment= ['text'=>'Payé','tone'=>'success'];
$email_badge_booking= ['text'=>'Annulée','tone'=>'danger'];
$email_payment_text = "Votre réservation a été annulée. Si vous avez effectué un paiement, il vous sera remboursé dans les 5 à 10 jours ouvrés.";
$email_method_label = 'carte bancaire';

$email_cta_primary = false;
$email_cta_secondary = false;

$email_note_html = '
<div style="margin-top:14px;background:#F8FAFC;border:1px solid #CBD5E1;border-radius:12px;padding:14px;display:flex;gap:10px;align-items:flex-start;">
  <div style="font-weight:900;color:#334155;">ⓘ</div>
  <div style="color:#334155;font-size:13px;line-height:1.5;">
    Votre réservation a été annulée. Aucune action n\'est requise.
  </div>
</div>';

include get_template_directory() . '/inc/emails/templates/layouts/layout-card.php';