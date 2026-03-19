<?php if ( ! defined('ABSPATH') ) exit;

$scenario = [
  'title' => 'Paiement en attente',
  'pay_badge'  => ['label'=>'En attente','bg'=>'#FEF3C7','color'=>'#92400E','icon'=>'⏳'],
  'book_badge' => ['label'=>'En attente','bg'=>'#FEF3C7','color'=>'#92400E','icon'=>'⏳'],
  'message' => "Votre réservation est en attente de réception du virement bancaire. Merci d'effectuer le virement pour confirmer votre réservation.",
  'method_label' => 'via paiement en liquide',
  'method_emoji' => '💳',
  'cta_primary_label' => 'Finaliser le paiement',
  'cta_primary_url' => $vars['pay_url'] ?? '',
  'show_bank_block' => true,
];

include get_template_directory() . '/inc/emails/templates/layouts/layout-cash.php';