<?php if ( ! defined('ABSPATH') ) exit;

$title_h1     = 'Paiement non reçu';
$details_text = "Nous n'avons pas reçu votre paiement par lien de paiement. Veuillez vérifier ou contacter notre support.";
$payment_method_label = 'lien de paiement';

$badge_payment = ['text'=>'Non reçu', 'bg'=>'#FEE2E2', 'color'=>'#991B1B'];
$badge_booking = ['text'=>'En attente', 'bg'=>'#FEF3C7', 'color'=>'#92400E'];

$show_cta = true;
$cta_label = 'Contacter le support';

// “Action required” box
$notice_box = [
  'bg' => '#FEF2F2',
  'border' => '#FCA5A5',
  'text' => "Action requise. Veuillez régulariser votre paiement pour confirmer votre réservation.",
];

include get_template_directory() . '/inc/emails/templates/layouts/layout-link.php';
