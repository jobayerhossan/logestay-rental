<?php if ( ! defined('ABSPATH') ) exit;

$email_title           = 'Réservation annulée';
$email_status_subtitle = 'Paiement non effectué';
$email_payment_text    = "La réservation n’a pas été confirmée. Elle a été annulée automatiquement.";
$email_method_label    = 'Paiement sur place (non confirmé)';
$email_amount_suffix   = 'Paiement sur place (non confirmé)';

$email_badge_payment = false;
$email_badge_booking = ['text'=>'Annulée','tone'=>'danger'];

$show_cta        = false;
$email_body_html = '';

include get_template_directory() . '/inc/emails/templates/layouts/layout-cash.php';
