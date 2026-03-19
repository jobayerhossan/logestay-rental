<?php if ( ! defined('ABSPATH') ) exit; ?>

<?php
// Helpers (safe defaults)
$guest_name     = $guest_name ?? '';
$total          = (float)($total ?? 0);
$currency       = strtoupper((string)($currency ?? 'EUR'));
$support_email  = $support_email ?? get_option('admin_email');
$site_url       = $site_url ?? home_url('/');
$pay_url        = $pay_url ?? '';
$payment_method_label = $payment_method_label ?? 'lien de paiement';

// Scenario-driven strings
$title_h1       = $title_h1 ?? 'Paiement en attente';
$details_text   = $details_text ?? 'Un lien de paiement sécurisé vous a été envoyé.';
$amount_suffix  = $amount_suffix ?? ('via ' . $payment_method_label);

// Badges
$badge_payment  = $badge_payment ?? ['text'=>'En attente', 'bg'=>'#FEF3C7', 'color'=>'#92400E', 'icon'=>'⏳'];
$badge_booking  = $badge_booking ?? ['text'=>'En attente', 'bg'=>'#FEF3C7', 'color'=>'#92400E', 'icon'=>'⏳'];

// Optional CTA + notice
$show_cta       = isset($show_cta) ? (bool)$show_cta : true;
$cta_label      = $cta_label ?? 'Finaliser le paiement';
$show_support   = isset($show_support) ? (bool)$show_support : true;

$notice_box     = $notice_box ?? null; // ['bg'=>..., 'border'=>..., 'text'=>...]
?>

<p style="margin:0 0 14px;color:#111827;font-size:16px;">
  Bonjour <?php echo $guest_name ? esc_html($guest_name) : ''; ?>,
</p>

<h2 style="margin:0 0 14px;font-size:28px;line-height:1.2;color:#111827;">
  <?php echo esc_html($title_h1); ?>
</h2>

<div style="margin:14px 0 18px;">
  <span style="display:inline-block;padding:6px 10px;border-radius:999px;background:<?php echo esc_attr($badge_payment['bg']); ?>;color:<?php echo esc_attr($badge_payment['color']); ?>;font-weight:700;font-size:12px;margin-right:8px;">
    <?php echo esc_html($badge_payment['icon'] . ' ' . $badge_payment['text']); ?>
  </span>
  <span style="display:inline-block;padding:6px 10px;border-radius:999px;background:<?php echo esc_attr($badge_booking['bg']); ?>;color:<?php echo esc_attr($badge_booking['color']); ?>;font-weight:700;font-size:12px;">
    <?php echo esc_html($badge_booking['icon'] . ' ' . $badge_booking['text']); ?>
  </span>
</div>

<!-- Payment details -->
<div style="background:#F8FAFC;border:1px solid #E5EEF9;border-radius:14px;padding:16px;">
  <div style="display:flex;gap:12px;align-items:flex-start;">
    <div style="width:44px;height:44px;border-radius:999px;background:#FFEDD5;display:inline;align-items:center;justify-content:center;font-weight:800;color:#9A3412; line-height:44px; text-align:center;">
      💳
    </div>
    <div style="flex:1;">
      <p style="margin:0 0 4px;font-weight:800;color:#111827;">Détails du paiement</p>
      <p style="margin:0 0 10px;color:#475569;font-size:14px;line-height:1.5;">
        <?php echo esc_html($details_text); ?>
      </p>

      <div style="font-size:30px;font-weight:900;color:#0F172A;">
        <?php echo number_format_i18n($total, 2); ?> €
        <span style="font-size:14px;font-weight:700;color:#64748B;"><?php echo esc_html($amount_suffix); ?></span>
      </div>
    </div>
  </div>
</div>

<?php if ( is_array($notice_box) && ! empty($notice_box['text']) ) : ?>
  <div style="margin-top:14px;background:<?php echo esc_attr($notice_box['bg']); ?>;border:1px solid <?php echo esc_attr($notice_box['border']); ?>;border-radius:12px;padding:12px 14px;color:#0F172A;font-size:13px;line-height:1.5;">
    <?php echo esc_html($notice_box['text']); ?>
  </div>
<?php endif; ?>

<?php if ( $show_cta ) : ?>
  <div style="margin:18px 0 0;display:flex;gap:12px;flex-wrap:wrap;">
    <?php if ( $pay_url ) : ?>
      <a href="<?php echo esc_url($pay_url); ?>" style="display:inline-block;background:#F97316;color:#fff;text-decoration:none;font-weight:900;padding:14px 22px;border-radius:12px;">
        <?php echo esc_html($cta_label); ?>
      </a>
    <?php endif; ?>

    <?php if ( $show_support ) : ?>
      <a href="mailto:<?php echo esc_attr($support_email); ?>" style="display:inline-block;background:#F3F4F6;color:#111827;text-decoration:none;font-weight:900;padding:14px 22px;border-radius:12px;">
        Contacter le support
      </a>
    <?php endif; ?>
  </div>
<?php endif; ?>

