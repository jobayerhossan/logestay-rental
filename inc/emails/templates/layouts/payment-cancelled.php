<?php if ( ! defined('ABSPATH') ) exit; ?>

<p style="margin:0 0 14px;color:#111827;font-size:16px;">
  Bonjour <?php echo $guest_name ? esc_html($guest_name) : ' '; ?>,
</p>

<h2 style="margin:0 0 14px;font-size:28px;line-height:1.2;color:#111827;">
  Paiement annulé
</h2>

<div style="margin:14px 0 18px;">
  <span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#FEE2E2;color:#991B1B;font-weight:800;font-size:12px;margin-right:8px;">
    ✖ Non reçu
  </span>
  <span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#E5E7EB;color:#374151;font-weight:800;font-size:12px;">
    Annulée
  </span>
</div>

<div style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:14px;padding:16px;">
  <p style="margin:0 0 6px;font-weight:900;color:#9A3412;">Détails du paiement</p>
  <p style="margin:0;color:#7C2D12;font-size:14px;line-height:1.5;">
    Votre paiement a été annulé. Aucune somme n’a été débitée.
    Vous pouvez relancer le paiement depuis votre espace si vous le souhaitez.
  </p>

  <div style="margin-top:10px;font-size:28px;font-weight:900;color:#0F172A;">
    <?php echo number_format_i18n((float)$total, 2); ?> €
    <span style="font-size:14px;font-weight:700;color:#64748B;">(réservation #<?php echo (int)$booking_id; ?>)</span>
  </div>
</div>

<div style="margin:18px 0 0;display:flex;gap:12px;flex-wrap:wrap;">
  <a href="<?php echo esc_url($pay_url); ?>" style="display:inline-block;background:#F97316;color:#fff;text-decoration:none;font-weight:900;padding:14px 22px;border-radius:12px;">
    Relancer le paiement
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