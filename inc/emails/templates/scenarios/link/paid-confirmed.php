<?php if ( ! defined('ABSPATH') ) exit;

$title_h1     = 'Paiement confirmé';
$details_text = "Votre paiement via le lien sécurisé a été validé.";
$payment_method_label = 'lien de paiement';

$badge_payment = ['text'=>'Payé', 'bg'=>'#D1FAE5', 'color'=>'#065F46', 'icon'=>'✅'];
$badge_booking = ['text'=>'Confirmée', 'bg'=>'#D1FAE5', 'color'=>'#065F46', 'icon'=>'✅'];

$show_cta = false;

// Optional success note like screenshot
$notice_box = [
  'bg' => '#ECFDF5',
  'border' => '#A7F3D0',
  'text' => "Bon séjour ! Merci pour votre confiance. Nous sommes ravis de vous accueillir avec LOGESTAY.",
];

include get_template_directory() . '/inc/emails/templates/layouts/layout-link.php';