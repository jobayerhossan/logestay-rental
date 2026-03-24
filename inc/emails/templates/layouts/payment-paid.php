<?php if ( ! defined('ABSPATH') ) exit; ?>

<p style="margin:0 0 14px;color:#111827;font-size:16px;">
  Bonjour <?php echo $guest_name ? esc_html($guest_name) : ' '; ?>,
</p>

<h2 style="margin:0 0 14px;font-size:28px;line-height:1.2;color:#111827;">
  Paiement confirmé
</h2>

<div style="margin:14px 0 18px;">
  <span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#D1FAE5;color:#065F46;font-weight:800;font-size:12px;margin-right:8px;">
    Payé
  </span>
  <span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#D1FAE5;color:#065F46;font-weight:800;font-size:12px;">
    Confirmée
  </span>
</div>

<!-- Payment details -->
<div style="background:#F8FAFC;border:1px solid #E5EEF9;border-radius:14px;padding:16px;">
  <div style="display:flex;gap:12px;align-items:flex-start;">
    <div style="width:44px;height:44px;border-radius:999px;background:#FFEDD5;display:flex;align-items:center;justify-content:center;font-weight:800;color:#9A3412;"></div>
    <div style="flex:1;">
      <p style="margin:0 0 4px;font-weight:900;color:#111827;">Détails du paiement</p>
      <p style="margin:0 0 10px;color:#475569;font-size:14px;line-height:1.5;">
        Votre paiement par carte bancaire a été validé avec succès.
      </p>

      <div style="font-size:30px;font-weight:900;color:#0F172A;">
        <?php echo number_format_i18n((float)$total, 2); ?> €
        <span style="font-size:14px;font-weight:700;color:#64748B;">via carte bancaire</span>
      </div>
    </div>
  </div>
</div>

<!-- Stay card -->
<div style="margin-top:16px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:14px;padding:18px;">
  <div style="display:flex;gap:12px;align-items:flex-start;">
    <div style="width:44px;height:44px;border-radius:999px;background:#DBEAFE;display:flex;align-items:center;justify-content:center;font-weight:800;color:#1D4ED8;"></div>
    <div style="flex:1;">
      <p style="margin:0;font-weight:900;font-size:18px;color:#0F172A;">Votre séjour</p>
      <p style="margin:10px 0 0;font-weight:900;color:#0F172A;"><?php echo esc_html($listing_title); ?></p>
      <?php if (!empty($city_name)) : ?>
        <p style="margin:6px 0 0;color:#475569;"><?php echo esc_html($city_name); ?></p>
      <?php endif; ?>
    </div>
  </div>

  <hr style="border:none;border-top:1px solid #BFDBFE;margin:14px 0;">

  <div style="display:flex;gap:20px;flex-wrap:wrap;">
    <div style="min-width:220px;">
      <p style="margin:0;color:#64748B;font-size:11px;letter-spacing:.06em;">DATES</p>
      <p style="margin:6px 0 0;font-weight:900;color:#0F172A;">
        <?php echo esc_html($check_in_with_time ?: $check_in); ?> → <?php echo esc_html($check_out_with_time ?: $check_out); ?>
      </p>
    </div>

    <div style="min-width:200px;">
      <p style="margin:0;color:#64748B;font-size:11px;letter-spacing:.06em;">VOYAGEURS</p>
      <?php
        $adults = (int) get_post_meta($booking_id, 'logestay_adults', true);
        $children = (int) get_post_meta($booking_id, 'logestay_children', true);
        $pets = (int) get_post_meta($booking_id, 'logestay_pets', true);
        $people = max(0, $adults + $children);
      ?>
      <p style="margin:6px 0 0;font-weight:900;color:#0F172A;">
        <?php echo (int)$people; ?> personne<?php echo $people > 1 ? 's' : ''; ?>
      </p>
    </div>
  </div>
</div>

<div style="margin:16px 0 0;text-align:center;">
  <a href="<?php echo esc_url($account_url); ?>" style="display:inline-block;background:#F97316;color:#fff;text-decoration:none;font-weight:900;padding:14px 26px;border-radius:12px;">
    Voir ma localisation
  </a>
</div>

<div style="margin-top:16px;background:#DCFCE7;border:1px solid #86EFAC;border-radius:14px;padding:14px;color:#065F46;font-weight:800;">
  Bon séjour ! Merci pour votre confiance. Nous sommes ravis de vous accueillir avec LOGESTAY.
</div>

<hr style="border:none;border-top:1px solid #E5E7EB;margin:22px 0;">

<p style="margin:0;color:#334155;font-size:14px;">
  Une question ? Notre équipe support est disponible 7j/7 pour vous accompagner.
</p>

<p style="margin:10px 0 0;color:#6B7280;font-size:12px;">
  <a href="<?php echo esc_url($site_url); ?>" style="color:#F97316;text-decoration:none;font-weight:800;">www.logestay.com</a>
  • Support : <?php echo esc_html($support_email); ?>
</p>
