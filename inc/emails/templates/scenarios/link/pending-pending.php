<?php if ( ! defined('ABSPATH') ) exit;

$title_h1     = 'Paiement en attente';
$details_text = "Un lien de paiement sécurisé vous a été envoyé.";
$payment_method_label = 'lien de paiement';

$badge_payment = ['text'=>'En attente', 'bg'=>'#FEF3C7', 'color'=>'#92400E', 'icon'=>'⏳'];
$badge_booking = ['text'=>'En attente', 'bg'=>'#FEF3C7', 'color'=>'#92400E', 'icon'=>'⏳'];

$show_cta = true;
$cta_label = 'Finaliser le paiement';

include get_template_directory() . '/inc/emails/templates/layouts/layout-link.php';