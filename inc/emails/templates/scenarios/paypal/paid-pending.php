<?php if ( ! defined('ABSPATH') ) exit;

$email_title        = 'Paiement confirmé';
$email_payment_text = 'Votre paiement via PayPal a été confirmé.';
$email_method_label = 'PayPal';

$email_badge_payment = ['text'=>'Payé','tone'=>'success'];
$email_badge_booking = ['text'=>'En attente','tone'=>'warning'];

$email_cta_primary   = false;
$email_cta_secondary = false;

$email_body_html = '';

include get_template_directory() . '/inc/emails/templates/layouts/layout-paypal.php';