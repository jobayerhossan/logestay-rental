<?php if ( ! defined('ABSPATH') ) exit;

$email_title        = 'Paiement en attente';
$email_payment_text = 'Votre paiement par PayPal est en cours de traitement.';
$email_method_label = 'PayPal';

$email_badge_payment = ['text'=>'En attente','tone'=>'warning'];
$email_badge_booking = ['text'=>'Confirmée','tone'=>'success'];

$email_cta_primary   = ['label'=>'Finaliser le paiement','url'=>$pay_url];
$email_cta_secondary = ['label'=>'Contacter le support','url'=>'mailto:' . $support_email];

$email_body_html = '';

include get_template_directory() . '/inc/emails/templates/layouts/layout-paypal.php';