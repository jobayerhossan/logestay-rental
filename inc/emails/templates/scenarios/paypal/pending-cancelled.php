<?php if ( ! defined('ABSPATH') ) exit;

$email_title           = 'Réservation annulée';
$email_status_subtitle = 'Paiement non effectué';
$email_payment_text    = "Le paiement n’a pas été effectué. La réservation a été annulée automatiquement.";
$email_method_label    = 'PayPal (paiement non effectué)';
$email_amount_suffix   = 'PayPal (paiement non effectué)';

$email_badge_payment = false;
$email_badge_booking = ['text'=>'Annulée','tone'=>'danger'];

$email_cta_primary   = false;
$email_cta_secondary = false;
$email_body_html     = '';

include get_template_directory() . '/inc/emails/templates/layouts/layout-paypal.php';
