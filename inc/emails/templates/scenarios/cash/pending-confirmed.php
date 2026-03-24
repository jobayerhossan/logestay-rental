<?php if ( ! defined('ABSPATH') ) exit; ?>
<p style="margin:0 0 16px;color:#111827;font-size:16px;line-height:1.6;">
	Bonjour <?php echo esc_html($guest_first_name ?: $guest_name); ?>,
</p>

<p style="margin:0 0 16px;color:#111827;font-size:16px;line-height:1.7;">
	Votre réservation est enregistrée et le logement est bloqué pour vous pendant 24 heures.
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
	<div style="margin:0 0 14px;color:#334155;font-size:15px;line-height:1.7;">
		<?php echo esc_html($check_out_with_time ?: ($check_out_formatted ?: $check_out)); ?>
	</div>

	<div style="margin:0;font-size:18px;font-weight:800;color:#0F172A;">Voyageurs</div>
	<div style="margin:0 0 14px;color:#334155;font-size:15px;line-height:1.7;">
		<?php echo esc_html((string) $guest_count); ?>
	</div>

	<div style="margin:0;font-size:18px;font-weight:800;color:#0F172A;">Montant à payer</div>
	<div style="margin:0;color:#334155;font-size:15px;font-weight:700;line-height:1.7;">
		<?php echo esc_html($reservation_price); ?>
	</div>
</div>

<div style="margin:20px 0;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:16px;padding:20px;">
	<div style="margin:0 0 12px;font-size:18px;font-weight:800;color:#0F172A;">Paiement sur place</div>

	<p style="margin:0 0 12px;color:#334155;font-size:15px;line-height:1.7;">
		Merci de vous présenter à l'adresse suivante :
	</p>

	<div style="margin:0;font-size:18px;font-weight:800;color:#0F172A;">Adresse</div>
	<div style="margin:0 0 14px;color:#334155;font-size:15px;line-height:1.7;">
		<?php echo esc_html($cash_office_address); ?>
	</div>

	<div style="margin:0;font-size:18px;font-weight:800;color:#0F172A;">Horaires</div>
	<div style="margin:0;color:#334155;font-size:15px;line-height:1.7;">
		<?php echo nl2br(esc_html($cash_hours)); ?>
	</div>
</div>

<div style="margin:20px 0 0;background:#FFF7ED;border:1px solid #FED7AA;border-radius:16px;padding:20px;">
	<div style="margin:0 0 12px;font-size:18px;font-weight:800;color:#9A3412;">Important</div>
	<p style="margin:0 0 12px;color:#9A3412;font-size:15px;line-height:1.7;">
		Votre réservation est bloquée pendant 24 heures.
	</p>
	<p style="margin:0;color:#9A3412;font-size:15px;line-height:1.7;">
		Sans paiement dans ce délai la réservation sera automatiquement annulée.
	</p>
</div>
