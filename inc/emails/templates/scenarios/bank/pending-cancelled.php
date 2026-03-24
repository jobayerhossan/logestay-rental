<?php if ( ! defined('ABSPATH') ) exit;

$scenario = [
  'title' => 'Réservation annulée',
  'status_subtitle' => 'Paiement non effectué',
  'pay_badge'  => false,
  'book_badge' => ['label'=>'Annulée','bg'=>'#FEE2E2','color'=>'#991B1B'],
  'message' => "Le paiement n’a pas été reçu. La réservation a été annulée automatiquement.",
  'method_label' => 'Virement bancaire (paiement non reçu)',
  'show_bank_block' => false,
  'show_cta' => false,
];

include get_template_directory() . '/inc/emails/templates/layouts/layout-bank.php';
