<?php if ( ! defined('ABSPATH') ) exit;

$email_title        = 'Paiement non reçu';
$email_payment_text = "Nous n'avons pas reçu votre paiement par PayPal. Veuillez vérifier ou contacter notre support.";
$email_method_label = 'PayPal';

$email_badge_payment = ['text'=>'Non reçu','tone'=>'danger'];
$email_badge_booking = ['text'=>'En attente','tone'=>'warning'];

$email_cta_primary   = ['label'=>'Contacter le support','url'=>'mailto:' . $support_email];
$email_cta_secondary = false;

$email_body_html = '
<div style="margin-top:16px;background:#FEE2E2;border:1px solid #FCA5A5;border-radius:14px;padding:14px;color:#991B1B;font-weight:800;font-size:13px;line-height:1.5;">
  ⛔ Action requise. Veuillez régulariser votre paiement pour confirmer votre réservation.
</div>';

include get_template_directory() . '/inc/emails/templates/layouts/layout-paypal.php';