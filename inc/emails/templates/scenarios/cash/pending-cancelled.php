<?php if ( ! defined('ABSPATH') ) exit;

$scenario = [
  'title' => 'Réservation annulée',
  'pay_badge'  => ['label'=>'En attente','bg'=>'#FEF3C7','color'=>'#92400E','icon'=>'⏳'],
  'book_badge' => ['label'=>'Annulée','bg'=>'#FEE2E2','color'=>'#991B1B','icon'=>'⛔'],
  'message' => "Votre réservation a été annulée. Aucune action n'est requise.",
  'method_label' => 'via paiement en liquide',
  'method_emoji' => '💳',
  'cta_primary_label' => 'Finaliser le paiement',
  'cta_primary_url' => '', // cancelled -> no payment CTA
  'show_bank_block' => true,
];

include get_template_directory() . '/inc/emails/templates/layouts/layout-cash.php';