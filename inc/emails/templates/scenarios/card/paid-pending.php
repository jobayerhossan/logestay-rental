<?php if ( ! defined('ABSPATH') ) exit;

$email_title        = 'Paiement confirmé';
$email_badge_payment= ['text'=>'Payé','tone'=>'success'];
$email_badge_booking= ['text'=>'En attente','tone'=>'warning'];
$email_payment_text = "Votre paiement par carte bancaire a été validé avec succès.";
$email_method_label = 'carte bancaire';

$email_cta_primary = false;
$email_cta_secondary = false;

include get_template_directory() . '/inc/emails/templates/layouts/layout-card.php';