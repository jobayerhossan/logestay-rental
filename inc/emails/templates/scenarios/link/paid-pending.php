<?php if ( ! defined('ABSPATH') ) exit;

$title_h1     = 'Paiement confirmé';
$details_text = "Votre paiement via le lien sécurisé a été validé.";
$payment_method_label = 'lien de paiement';

$badge_payment = ['text'=>'Payé', 'bg'=>'#D1FAE5', 'color'=>'#065F46', 'icon'=>'✅'];
$badge_booking = ['text'=>'En attente', 'bg'=>'#FEF3C7', 'color'=>'#92400E', 'icon'=>'⏳'];

$show_cta = false;

include get_template_directory() . '/inc/emails/templates/layouts/layout-link.php';