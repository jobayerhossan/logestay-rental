<?php if ( ! defined('ABSPATH') ) exit;

$title_h1     = 'Réservation annulée';
$details_text = "Votre réservation a été annulée. Si vous avez effectué un paiement, il vous sera remboursé dans les 5 à 10 jours ouvrés.";
$payment_method_label = 'lien de paiement';

$badge_payment = ['text'=>'Payé', 'bg'=>'#D1FAE5', 'color'=>'#065F46', 'icon'=>'✅'];
$badge_booking = ['text'=>'Annulée', 'bg'=>'#FEE2E2', 'color'=>'#991B1B', 'icon'=>'⛔'];

$show_cta = false;

$notice_box = [
  'bg' => '#F8FAFC',
  'border' => '#CBD5E1',
  'text' => "Votre réservation a été annulée. Aucune action n'est requise.",
];

include get_template_directory() . '/inc/emails/templates/layouts/layout-link.php';