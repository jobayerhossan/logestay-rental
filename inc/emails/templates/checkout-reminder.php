<?php if ( ! defined('ABSPATH') ) exit; ?>
<p style="margin:0 0 16px;color:#111827;font-size:16px;line-height:1.6;">
	Bonjour <?php echo esc_html($guest_first_name ?: $guest_name); ?>,
</p>

<p style="margin:0 0 16px;color:#111827;font-size:16px;line-height:1.7;">
	Nous espérons que votre séjour se passe bien.
	Petit rappel : votre départ est prévu le <?php echo esc_html($check_out_with_time ?: ($check_out_formatted ?: $check_out)); ?>.
</p>

<p style="margin:0 0 12px;color:#111827;font-size:16px;line-height:1.7;">
	Avant de partir merci de :
</p>

<p style="margin:0 0 8px;color:#111827;font-size:16px;line-height:1.7;">
	• fermer les fenêtres
</p>

<p style="margin:0 0 8px;color:#111827;font-size:16px;line-height:1.7;">
	• éteindre les lumières
</p>

<p style="margin:0 0 16px;color:#111827;font-size:16px;line-height:1.7;">
	• laisser les clés dans le boîtier
</p>

<p style="margin:0;color:#111827;font-size:16px;line-height:1.7;">
	Merci beaucoup.
</p>
