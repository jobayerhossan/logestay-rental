<?php if ( ! defined('ABSPATH') ) exit;

$scenario = [
  'title' => 'Réservation annulée',
  'pay_badge'  => ['label'=>'Non reçu','bg'=>'#FEE2E2','color'=>'#991B1B'],
  'book_badge' => ['label'=>'Annulée','bg'=>'#FEE2E2','color'=>'#991B1B'],
  'message' => "Votre réservation a été annulée. Si vous avez effectué un paiement, il vous sera remboursé dans les 5 à 10 jours ouvrés.",
  'method_label' => 'via paiement en liquide',
  'cta_primary_label' => 'Contacter le support',
  'cta_primary_url' => 'mailto:' . ($vars['support_email'] ?? get_option('admin_email')),
  'show_bank_block' => true,
];

include get_template_directory() . '/inc/emails/templates/layouts/layout-cash.php';
