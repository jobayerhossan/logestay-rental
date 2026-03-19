<?php if ( ! defined('ABSPATH') ) exit;

$title_h1     = 'Paiement non reçu';
$details_text = "Nous n'avons pas reçu votre paiement par lien de paiement. Veuillez vérifier ou contacter notre support.";
$payment_method_label = 'lien de paiement';

$badge_payment = ['text'=>'Non reçu', 'bg'=>'#FEE2E2', 'color'=>'#991B1B', 'icon'=>'⛔'];
$badge_booking = ['text'=>'Confirmée', 'bg'=>'#D1FAE5', 'color'=>'#065F46', 'icon'=>'✅'];

$show_cta = true;
$cta_label = 'Contacter le support';

$notice_box = [
  'bg' => '#FEF2F2',
  'border' => '#FCA5A5',
  'text' => "Action requise. Veuillez régulariser votre paiement pour confirmer votre réservation.",
];

include get_template_directory() . '/inc/emails/templates/layouts/layout-link.php';