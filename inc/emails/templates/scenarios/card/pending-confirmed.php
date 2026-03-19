<?php if ( ! defined('ABSPATH') ) exit;

$email_title        = 'Paiement en attente';
$email_badge_payment= ['text'=>'En attente','tone'=>'warning'];
$email_badge_booking= ['text'=>'Confirmée','tone'=>'success'];
$email_payment_text = "Votre paiement par carte bancaire est en cours de traitement.";
$email_method_label = 'carte bancaire';

$email_cta_primary = false;
$email_cta_secondary = false;

include get_template_directory() . '/inc/emails/templates/layouts/layout-card.php';