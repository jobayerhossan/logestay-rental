<?php if ( ! defined('ABSPATH') ) exit; ?>
<p style="margin:0 0 16px;color:#111827;font-size:16px;line-height:1.6;">
	Bonjour <?php echo esc_html($guest_first_name ?: $guest_name); ?>,
</p>

<p style="margin:0 0 16px;color:#111827;font-size:16px;line-height:1.7;">
	Nous avons bien reçu votre paiement.<br>
	Votre réservation est maintenant confirmée.
</p>

<div style="margin:20px 0;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:16px;padding:20px;">
	<div style="margin:0;font-size:18px;font-weight:800;color:#0F172A;">Logement</div>
	<div style="margin:0 0 14px;color:#0F172A;font-size:16px;font-weight:700;line-height:1.6;">
		<?php echo esc_html($listing_title); ?>
	</div>

	<div style="margin:0;font-size:18px;font-weight:800;color:#0F172A;">Arrivée</div>
	<div style="margin:0 0 14px;color:#334155;font-size:15px;line-height:1.7;">
		<?php echo esc_html($check_in_with_time ?: ($check_in_formatted ?: $check_in)); ?>
	</div>

	<div style="margin:0;font-size:18px;font-weight:800;color:#0F172A;">Départ</div>
	<div style="margin:0;color:#334155;font-size:15px;line-height:1.7;">
		<?php echo esc_html($check_out_with_time ?: ($check_out_formatted ?: $check_out)); ?>
	</div>
</div>

<p style="margin:20px 0 16px;color:#111827;font-size:16px;line-height:1.7;">
	Les instructions d’arrivée et le code d’accès vous seront envoyés 24 heures avant votre arrivée.
</p>

<p style="margin:0;color:#111827;font-size:16px;line-height:1.7;">
	Merci et à bientôt.
</p>
