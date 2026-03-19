<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Layout: Card (Carte bancaire)
 * This file ONLY renders the email BODY (header/footer already wrapped).
 *
 * Variables expected (from logestay_email_vars_from_booking):
 * - guest_name, total, currency
 * - support_email, pay_url, site_url
 * - payment_status, booking_status
 *
 * Scenario overrides (set by scenario files):
 * - email_title
 * - email_payment_text
 * - email_method_label
 * - email_badge_payment ['text','tone']
 * - email_badge_booking ['text','tone']
 * - email_cta_primary  ['label','url'] or false
 * - email_cta_secondary ['label','url'] or false
 * - email_action_html (string)  (optional)
 * - email_stay_html   (string)  (optional)
 * - email_note_html   (string)  (optional)
 */

if ( ! function_exists('logestay_email_badge_html') ) {
	function logestay_email_badge_html(array $badge): string {
		$text = (string) ($badge['text'] ?? '');
		$tone = (string) ($badge['tone'] ?? 'neutral'); // success|warning|danger|neutral

		$map = [
			'success' => ['bg'=>'#D1FAE5', 'fg'=>'#065F46'],
			'warning' => ['bg'=>'#FEF3C7', 'fg'=>'#92400E'],
			'danger'  => ['bg'=>'#FEE2E2', 'fg'=>'#991B1B'],
			'neutral' => ['bg'=>'#E5E7EB', 'fg'=>'#111827'],
		];
		$c = $map[$tone] ?? $map['neutral'];

		return '<span style="display:inline-block;padding:6px 12px;border-radius:999px;background:'.$c['bg'].';color:'.$c['fg'].';font-weight:800;font-size:12px;margin-right:8px;">'
			. esc_html($text)
			. '</span>';
	}
}

$guest_name    = (string) ($guest_name ?? '');
$total         = (float)  ($total ?? 0);
$currency      = (string) ($currency ?? 'EUR');
$support_email = (string) ($support_email ?? get_option('admin_email'));
$pay_url       = (string) ($pay_url ?? home_url('/'));
$site_url      = (string) ($site_url ?? home_url('/'));

$email_title        = (string) ($email_title ?? 'Paiement en attente');
$email_payment_text = (string) ($email_payment_text ?? "Votre paiement par carte bancaire est en cours de traitement.");
$email_method_label = (string) ($email_method_label ?? 'carte bancaire');

$email_badge_payment = is_array($email_badge_payment ?? null) ? $email_badge_payment : ['text'=>'En attente','tone'=>'warning'];
$email_badge_booking = is_array($email_badge_booking ?? null) ? $email_badge_booking : ['text'=>'En attente','tone'=>'warning'];

$email_cta_primary   = ($email_cta_primary === false) ? false : (is_array($email_cta_primary ?? null) ? $email_cta_primary : [
	'label' => 'Finaliser le paiement',
	'url'   => $pay_url,
]);

$email_cta_secondary = ($email_cta_secondary === false) ? false : (is_array($email_cta_secondary ?? null) ? $email_cta_secondary : [
	'label' => 'Contacter le support',
	'url'   => 'mailto:' . $support_email,
]);

$email_action_html = (string) ($email_action_html ?? '');
$email_stay_html   = (string) ($email_stay_html ?? '');
$email_note_html   = (string) ($email_note_html ?? '');

$amount_str = number_format_i18n((float)$total, 2);
$cur_sym    = (strtoupper($currency) === 'EUR') ? '€' : strtoupper($currency);
$host       = parse_url($site_url, PHP_URL_HOST) ?: 'www.logestay.com';
?>

<p style="margin:0 0 14px;color:#111827;font-size:16px;">
	Bonjour <?php echo $guest_name ? esc_html($guest_name) : ''; ?>,
</p>

<h2 style="margin:0 0 14px;font-size:30px;line-height:1.2;color:#111827;">
	<?php echo esc_html($email_title); ?>
</h2>

<div style="margin:12px 0 18px;">
	<?php
		echo logestay_email_badge_html($email_badge_payment);
		echo logestay_email_badge_html($email_badge_booking);
	?>
</div>

<!-- Payment details -->
<div style="background:#F8FAFC;border:1px solid #E5EEF9;border-radius:14px;padding:18px;">
	<div style="display:flex;gap:14px;align-items:flex-start;">
		<div style="width:46px;height:46px;border-radius:999px;background:#FFEDD5;display:inline;align-items:center;justify-content:center;font-weight:900;color:#9A3412;  line-height: 46px;
  text-align: center;">
			💳
		</div>

		<div style="flex:1;min-width:0;">
			<p style="margin:0 0 6px;font-weight:900;color:#111827;">Détails du paiement</p>
			<p style="margin:0 0 12px;color:#475569;font-size:14px;line-height:1.55;">
				<?php echo esc_html($email_payment_text); ?>
			</p>

			<div style="font-size:34px;font-weight:900;color:#0F172A;">
				<?php echo $amount_str; ?> <?php echo esc_html($cur_sym); ?>
				<span style="font-size:14px;font-weight:800;color:#64748B;">via <?php echo esc_html($email_method_label); ?></span>
			</div>
		</div>
	</div>
</div>

<?php if ( trim($email_stay_html) !== '' ) : ?>
	<div style="margin-top:16px;">
		<?php echo $email_stay_html; // trusted HTML from scenario file ?>
	</div>
<?php endif; ?>

<?php if ( trim($email_action_html) !== '' ) : ?>
	<div style="margin-top:14px;">
		<?php echo $email_action_html; // trusted HTML from scenario file ?>
	</div>
<?php endif; ?>

<?php if ( $email_cta_primary || $email_cta_secondary ) : ?>
	<div style="margin:18px 0 0;display:flex;gap:12px;flex-wrap:wrap;justify-content:center;">
		<?php if ( $email_cta_primary ) : ?>
			<a href="<?php echo esc_url($email_cta_primary['url'] ?? $pay_url); ?>"
			   style="display:inline-block;background:#F97316;color:#fff;text-decoration:none;font-weight:900;padding:14px 22px;border-radius:12px;">
				<?php echo esc_html($email_cta_primary['label'] ?? 'Finaliser le paiement'); ?>
			</a>
		<?php endif; ?>

		<?php if ( $email_cta_secondary ) : ?>
			<a href="<?php echo esc_url($email_cta_secondary['url'] ?? ('mailto:' . $support_email)); ?>"
			   style="display:inline-block;background:#F3F4F6;color:#111827;text-decoration:none;font-weight:900;padding:14px 22px;border-radius:12px;">
				<?php echo esc_html($email_cta_secondary['label'] ?? 'Contacter le support'); ?>
			</a>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php if ( trim($email_note_html) !== '' ) : ?>
	<div style="margin-top:16px;">
		<?php echo $email_note_html; ?>
	</div>
<?php endif; ?>
