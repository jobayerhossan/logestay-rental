<?php if ( ! defined('ABSPATH') ) exit;

$scenario = [
  'title' => 'Paiement non reçu',
  'pay_badge'  => ['label'=>'Non reçu','bg'=>'#FEE2E2','color'=>'#991B1B'],
  'book_badge' => ['label'=>'Confirmée','bg'=>'#D1FAE5','color'=>'#065F46'],
  'message' => "Nous n'avons pas reçu votre paiement par virement bancaire. Veuillez vérifier ou contacter notre support.",
  'method_label' => 'via virement bancaire',
  'cta_primary_label' => 'Contacter le support',
  'cta_primary_url' => 'mailto:' . ($vars['support_email'] ?? get_option('admin_email')),
  'show_bank_block' => true,
  'note_box' => [
    'text' => "Action requise : veuillez régulariser votre paiement pour confirmer votre réservation.",
    'bg' => '#FEE2E2',
    'border' => '#FCA5A5',
    'color' => '#991B1B',
  ],
];

include get_template_directory() . '/inc/emails/templates/layouts/layout-bank.php';
