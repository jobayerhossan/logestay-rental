<?php if ( ! defined('ABSPATH') ) exit; ?>

<p style="margin:0 0 14px;color:#111827;font-size:16px;">
  Bonjour <?php echo $guest_name ? esc_html($guest_name) : ' '; ?>,
</p>

<h2 style="margin:0 0 14px;font-size:28px;line-height:1.2;color:#111827;">
  Paiement en attente
</h2>

<div style="margin:14px 0 18px;">
  <span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#FEF3C7;color:#92400E;font-weight:700;font-size:12px;margin-right:8px;">
    En attente
  </span>
  <span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#D1FAE5;color:#065F46;font-weight:700;font-size:12px;">
    Confirmée
  </span>
</div>

<div style="background:#F8FAFC;border:1px solid #E5EEF9;border-radius:14px;padding:16px;">
  <div style="display:flex;gap:12px;align-items:flex-start;">
    <div style="width:44px;height:44px;border-radius:999px;background:#FFEDD5;display:flex;align-items:center;justify-content:center;font-weight:800;color:#9A3412;"></div>
    <div style="flex:1;">
      <p style="margin:0 0 4px;font-weight:800;color:#111827;">Détails du paiement</p>
      <p style="margin:0 0 10px;color:#475569;font-size:14px;line-height:1.5;">
        Un lien de paiement sécurisé vous a été envoyé.
      </p>

      <div style="font-size:30px;font-weight:900;color:#0F172A;">
        <?php echo number_format_i18n((float)$total, 2); ?> €
        <span style="font-size:14px;font-weight:700;color:#64748B;">via lien de paiement</span>
      </div>
    </div>
  </div>
</div>

<div style="margin:18px 0 0;display:flex;gap:12px;flex-wrap:wrap;">
  <a href="<?php echo esc_url($pay_url); ?>" style="display:inline-block;background:#F97316;color:#fff;text-decoration:none;font-weight:900;padding:14px 22px;border-radius:12px;">
    Finaliser le paiement
  </a>

  <a href="mailto:<?php echo esc_attr($support_email); ?>" style="display:inline-block;background:#F3F4F6;color:#111827;text-decoration:none;font-weight:900;padding:14px 22px;border-radius:12px;">
    Contacter le support
  </a>
</div>

<hr style="border:none;border-top:1px solid #E5E7EB;margin:22px 0;">

<p style="margin:0;color:#334155;font-size:14px;">
  Une question ? Notre équipe support est disponible 7j/7 pour vous accompagner.
</p>

<p style="margin:10px 0 0;color:#6B7280;font-size:12px;">
  <a href="<?php echo esc_url($site_url); ?>" style="color:#F97316;text-decoration:none;font-weight:800;">www.logestay.com</a>
  • Support : <?php echo esc_html($support_email); ?>
</p>
