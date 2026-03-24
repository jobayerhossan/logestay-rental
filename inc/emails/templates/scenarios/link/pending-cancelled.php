<?php if ( ! defined('ABSPATH') ) exit;

$title_h1             = 'Réservation annulée';
$status_subtitle      = 'Paiement non effectué';
$details_text         = "Le paiement n’a pas été effectué. La réservation a été annulée automatiquement.";
$payment_method_label = 'Lien de paiement (paiement non effectué)';
$amount_suffix        = 'Lien de paiement (paiement non effectué)';

$badge_payment = false;
$badge_booking = ['text'=>'Annulée', 'bg'=>'#FEE2E2', 'color'=>'#991B1B'];

$show_cta   = false;
$notice_box = false;

include get_template_directory() . '/inc/emails/templates/layouts/layout-link.php';
