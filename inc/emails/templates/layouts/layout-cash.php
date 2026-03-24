<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Layout: Cash (paiement en liquide) — BODY ONLY
 *
 * NOTE:
 * Header/footer wrapper is already rendered by your global email header/footer.
 * This file outputs ONLY the content block (greeting + title + badges + payment card + CTA + footer text).
 */

// Vars + defaults
$guest_name     = (string) ($guest_name ?? '');
$total          = (float)  ($total ?? 0);
$currency       = (string) ($currency ?? 'EUR');

$support_email  = (string) ($support_email ?? get_option('admin_email'));
$pay_url        = (string) ($pay_url ?? home_url('/'));
$site_url       = (string) ($site_url ?? home_url('/'));

// statuses for default badges
$payment_status = (string) ($payment_status ?? 'pending'); // pending|paid|failed|refunded
$booking_status = (string) ($booking_status ?? 'pending'); // pending|confirmed|canceled|expired

// Scenario-driven overrides
$email_title        = (string) ($email_title ?? 'Paiement en attente');
$email_status_subtitle = (string) ($email_status_subtitle ?? '');
$email_payment_text = (string) ($email_payment_text ?? 'Votre paiement en liquide est attendu.');
$email_method_label = (string) ($email_method_label ?? 'paiement en liquide');
$email_amount_suffix = (string) ($email_amount_suffix ?? ('via ' . $email_method_label));

// Optional scenario HTML blocks
$email_body_html = (string) ($email_body_html ?? '');

// If you already have these helper functions globally, REMOVE these function blocks.
// (keep only one copy in your project)
if ( ! function_exists('logestay_email_default_badges') ) {
  function logestay_email_default_badges(string $payment_status, string $booking_status): array {
    $pay = ['text' => 'En attente', 'tone' => 'warning'];
    if ($payment_status === 'paid')     $pay = ['text' => 'Payé', 'tone' => 'success'];
    if ($payment_status === 'failed')   $pay = ['text' => 'Non reçu', 'tone' => 'danger'];
    if ($payment_status === 'refunded') $pay = ['text' => 'Remboursé', 'tone' => 'neutral'];

    $book = ['text' => 'En attente', 'tone' => 'warning'];
    if ($booking_status === 'confirmed') $book = ['text' => 'Confirmée', 'tone' => 'success'];
    if ($booking_status === 'canceled')  $book = ['text' => 'Annulée', 'tone' => 'danger'];
    if ($booking_status === 'expired')   $book = ['text' => 'Expirée', 'tone' => 'neutral'];

    return [$pay, $book];
  }
}

if ( ! function_exists('logestay_email_badge_html') ) {
  function logestay_email_badge_html(array $badge): string {
    $text = (string) ($badge['text'] ?? '');
    $tone = (string) ($badge['tone'] ?? 'neutral');

    $map = [
      'success' => ['bg'=>'#D1FAE5', 'fg'=>'#065F46'],
      'warning' => ['bg'=>'#FEF3C7', 'fg'=>'#92400E'],
      'danger'  => ['bg'=>'#FEE2E2', 'fg'=>'#991B1B'],
      'neutral' => ['bg'=>'#E5E7EB', 'fg'=>'#111827'],
    ];
    $c = $map[$tone] ?? $map['neutral'];

    return '<span style="display:inline-block;padding:6px 10px;border-radius:999px;background:'.$c['bg'].';color:'.$c['fg'].';font-weight:700;font-size:12px;margin-right:8px;">'
      . esc_html($text)
      . '</span>';
  }
}

// Default badges (unless scenario overrides)
[$def_pay_badge, $def_book_badge] = logestay_email_default_badges($payment_status, $booking_status);

$email_badge_payment = ($email_badge_payment === false) ? false : (is_array($email_badge_payment ?? null) ? $email_badge_payment : $def_pay_badge);
$email_badge_booking = ($email_badge_booking === false) ? false : (is_array($email_badge_booking ?? null) ? $email_badge_booking : $def_book_badge);

// CTAs
$show_cta = isset($show_cta) ? (bool)$show_cta : true;

$cta_primary = is_array($email_cta_primary ?? null) ? $email_cta_primary : [
  'label' => 'Finaliser le paiement',
  'url'   => $pay_url,
];

$cta_secondary = is_array($email_cta_secondary ?? null) ? $email_cta_secondary : [
  'label' => 'Contacter le support',
  'url'   => 'mailto:' . $support_email,
];

// Currency symbol
$currency_symbol = ($currency === 'EUR' || strtoupper($currency) === 'EUR') ? '€' : strtoupper($currency);
?>

<p style="margin:0 0 14px;color:#111827;font-size:16px;">
  Bonjour <?php echo $guest_name ? esc_html($guest_name) : ''; ?>,
</p>

<h2 style="margin:0 0 14px;font-size:28px;line-height:1.2;color:#111827;">
  <?php echo esc_html($email_title); ?>
</h2>

<?php if ( $email_status_subtitle !== '' ) : ?>
  <p style="margin:0 0 14px;color:#64748B;font-size:13px;font-weight:700;line-height:1.5;">
    <?php echo esc_html($email_status_subtitle); ?>
  </p>
<?php endif; ?>

<div style="margin:14px 0 18px;">
  <?php
    if ( is_array($email_badge_payment) ) {
      echo logestay_email_badge_html($email_badge_payment);
    }
    if ( is_array($email_badge_booking) ) {
      echo logestay_email_badge_html($email_badge_booking);
    }
  ?>
</div>

<!-- Payment details (Cash) -->
<div style="background:#F8FAFC;border:1px solid #E5EEF9;border-radius:14px;padding:16px;">
  <div style="display:flex;gap:12px;align-items:flex-start;">
    <div style="width:44px;height:44px;border-radius:999px;background:#FFEDD5;display:inline-block;border:1px solid #FDBA74;">
    </div>

    <div style="flex:1;">
      <p style="margin:0 0 4px;font-weight:800;color:#111827;">Détails du paiement</p>
      <p style="margin:0 0 10px;color:#475569;font-size:14px;line-height:1.5;">
        <?php echo esc_html($email_payment_text); ?>
      </p>

      <div style="font-size:30px;font-weight:900;color:#0F172A;">
        <?php echo number_format_i18n($total, 2); ?> <?php echo esc_html($currency_symbol); ?>
        <span style="font-size:14px;font-weight:700;color:#64748B;"><?php echo esc_html($email_amount_suffix); ?></span>
      </div>
    </div>
  </div>
</div>

<?php if ( trim($email_body_html) !== '' ) : ?>
  <div style="margin-top:16px;">
    <?php echo $email_body_html; // trusted HTML from scenario file ?>
  </div>
<?php endif; ?>

<?php if ( $show_cta ) : ?>
  <div style="margin:18px 0 0;display:flex;gap:12px;flex-wrap:wrap;">
    <?php if ( ! empty($cta_primary['url']) ) : ?>
      <a href="<?php echo esc_url($cta_primary['url']); ?>"
         style="display:inline-block;background:#F97316;color:#fff;text-decoration:none;font-weight:900;padding:14px 22px;border-radius:12px;">
        <?php echo esc_html($cta_primary['label'] ?? 'Finaliser le paiement'); ?>
      </a>
    <?php endif; ?>

    <?php if ( ! empty($cta_secondary['url']) ) : ?>
      <a href="<?php echo esc_url($cta_secondary['url']); ?>"
         style="display:inline-block;background:#F3F4F6;color:#111827;text-decoration:none;font-weight:900;padding:14px 22px;border-radius:12px;">
        <?php echo esc_html($cta_secondary['label'] ?? 'Contacter le support'); ?>
      </a>
    <?php endif; ?>
  </div>
<?php endif; ?>
