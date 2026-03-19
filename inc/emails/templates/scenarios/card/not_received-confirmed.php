<?php if ( ! defined('ABSPATH') ) exit;

$email_title        = 'Paiement non reçu';
$email_badge_payment= ['text'=>'Non reçu','tone'=>'danger'];
$email_badge_booking= ['text'=>'Confirmée','tone'=>'success'];
$email_payment_text = "Nous n'avons pas reçu votre paiement par carte bancaire. Veuillez vérifier ou contacter notre support.";
$email_method_label = 'carte bancaire';

$email_cta_primary = false;
$email_cta_secondary = [
	'label' => 'Contacter le support',
	'url'   => 'mailto:' . ($support_email ?? get_option('admin_email')),
];

$email_action_html = '
<div style="margin-top:12px;background:#FEE2E2;border:1px solid #FCA5A5;border-radius:12px;padding:12px;color:#991B1B;font-weight:800;font-size:13px;">
  ⛔ Action requise. Veuillez régulariser votre paiement pour confirmer votre réservation.
</div>';

include get_template_directory() . '/inc/emails/templates/layouts/layout-card.php';