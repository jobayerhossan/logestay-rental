<?php if ( ! defined('ABSPATH') ) exit; ?>
<p style="margin:0 0 16px;color:#111827;font-size:16px;line-height:1.6;">
	Bonjour <?php echo esc_html($guest_first_name ?: $guest_name); ?>,
</p>

<p style="margin:0 0 16px;color:#111827;font-size:16px;line-height:1.7;">
	Votre séjour commence bientôt.
</p>

<div style="margin:20px 0;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:16px;padding:20px;">
	<div style="margin:0;font-size:18px;font-weight:800;color:#0F172A;">Adresse du logement</div>
	<div style="margin:0 0 14px;color:#334155;font-size:15px;line-height:1.7;">
		<?php echo esc_html($property_address ?: $city_name); ?>
	</div>

	<div style="margin:0;font-size:18px;font-weight:800;color:#0F172A;">Code du boîtier à clés</div>
	<div style="margin:0 0 14px;color:#0F172A;font-size:16px;font-weight:700;line-height:1.7;letter-spacing:0.08em;">
		<?php echo esc_html($keybox_code); ?>
	</div>

	<div style="margin:0;font-size:18px;font-weight:800;color:#0F172A;">Arrivée</div>
	<div style="margin:0;color:#334155;font-size:15px;line-height:1.7;">
		<?php echo esc_html($check_in_with_time ?: ($check_in_formatted ?: $check_in)); ?>
	</div>
</div>

<?php if ( ! empty($host_phone) ) : ?>
	<p style="margin:20px 0 16px;color:#111827;font-size:16px;line-height:1.7;">
		Si vous avez besoin d’aide :<br>
			<?php echo esc_html($host_phone); ?>
		</p>
	<?php endif; ?>

<p style="margin:0;color:#111827;font-size:16px;line-height:1.7;">
	Nous vous souhaitons un excellent séjour.
</p>
