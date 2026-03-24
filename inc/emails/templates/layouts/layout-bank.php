<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Layout: Bank emails
 *
 * Expected:
 * - $vars (array) : booking variables (guest_name, total, etc.)
 * - $scenario (array) : scenario config for this email
 *
 * $scenario keys:
 *  - title (string)
 *  - pay_badge (array) ['label','bg','color']
 *  - book_badge (array) ['label','bg','color']
 *  - message (string)
 *  - method_label (string) e.g. "via virement bancaire"
 *  - cta_primary_label (string)
 *  - cta_primary_url (string)
 *  - show_bank_block (bool)
 *  - note_box (array|null) ['text','bg','border','color']  (optional)
 */

$guest_name      = $vars['guest_name']      ?? '';
$total           = (float)($vars['total']   ?? 0);
$pay_url         = $scenario['cta_primary_url'] ?? ($vars['pay_url'] ?? '');
$support_email   = $vars['support_email']   ?? get_option('admin_email');
$site_url        = $vars['site_url']        ?? home_url('/');

$bank_beneficiary = $vars['bank_beneficiary'] ?? '';
$bank_iban        = $vars['bank_iban']        ?? '';
$bank_bic         = $vars['bank_bic']         ?? '';
$bank_reference   = $vars['bank_reference']   ?? '';

$title        = $scenario['title'] ?? 'Paiement en attente';
$status_subtitle = $scenario['status_subtitle'] ?? '';
$message      = $scenario['message'] ?? '';
$method_label = $scenario['method_label'] ?? 'via virement bancaire';
$cta_primary_label = $scenario['cta_primary_label'] ?? 'Finaliser le paiement';
$show_bank_block   = array_key_exists('show_bank_block', $scenario) ? (bool)$scenario['show_bank_block'] : true;
 $show_cta         = array_key_exists('show_cta', $scenario) ? (bool)$scenario['show_cta'] : true;

$pay_badge  = array_key_exists('pay_badge', $scenario) && $scenario['pay_badge'] === false ? false : ($scenario['pay_badge']  ?? ['label'=>'En attente','bg'=>'#FEF3C7','color'=>'#92400E']);
$book_badge = array_key_exists('book_badge', $scenario) && $scenario['book_badge'] === false ? false : ($scenario['book_badge'] ?? ['label'=>'Confirmée','bg'=>'#D1FAE5','color'=>'#065F46']);

$note_box = $scenario['note_box'] ?? null;
?>

<p style="margin:0 0 14px;color:#111827;font-size:16px;">
  Bonjour <?php echo $guest_name ? esc_html($guest_name) : ' '; ?>,
</p>

<h2 style="margin:0 0 14px;font-size:28px;line-height:1.2;color:#111827;">
  <?php echo esc_html($title); ?>
</h2>

<?php if ( $status_subtitle !== '' ) : ?>
  <p style="margin:0 0 14px;color:#64748B;font-size:13px;font-weight:700;line-height:1.5;">
    <?php echo esc_html($status_subtitle); ?>
  </p>
<?php endif; ?>

<div style="margin:14px 0 18px;">
  <?php if ( is_array($pay_badge) ) : ?>
    <span style="display:inline-block;padding:6px 10px;border-radius:999px;background:<?php echo esc_attr($pay_badge['bg']); ?>;color:<?php echo esc_attr($pay_badge['color']); ?>;font-weight:700;font-size:12px;margin-right:8px;">
      <?php echo esc_html($pay_badge['label']); ?>
    </span>
  <?php endif; ?>

  <?php if ( is_array($book_badge) ) : ?>
    <span style="display:inline-block;padding:6px 10px;border-radius:999px;background:<?php echo esc_attr($book_badge['bg']); ?>;color:<?php echo esc_attr($book_badge['color']); ?>;font-weight:700;font-size:12px;">
      <?php echo esc_html($book_badge['label']); ?>
    </span>
  <?php endif; ?>
</div>

<!-- Payment details -->
<div style="background:#F8FAFC;border:1px solid #E5EEF9;border-radius:14px;padding:16px;">
  <div style="display:flex;gap:12px;align-items:flex-start;">
    <div style="width:44px;height:44px;border-radius:999px;background:#FFEDD5;display:inline-block;border:1px solid #FDBA74;">
    </div>
    <div style="flex:1;">
      <p style="margin:0 0 4px;font-weight:800;color:#111827;">Détails du paiement</p>

      <?php if ($message) : ?>
        <p style="margin:0 0 10px;color:#475569;font-size:14px;line-height:1.5;">
          <?php echo esc_html($message); ?>
        </p>
      <?php endif; ?>

      <div style="font-size:30px;font-weight:900;color:#0F172A;">
        <?php echo number_format_i18n((float)$total, 2); ?> €
        <span style="font-size:14px;font-weight:700;color:#64748B;"><?php echo esc_html($method_label); ?></span>
      </div>
    </div>
  </div>
</div>

<?php if ($show_bank_block) : ?>
<!-- Bank coordinates -->
<div style="margin-top:16px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:14px;padding:16px;">
  <h3 style="margin:0 0 12px;font-size:18px;color:#0F172A;">Coordonnées bancaires</h3>

  <div style="display:flex;gap:18px;flex-wrap:wrap;margin-bottom:12px;">
    <div style="flex:1;min-width:220px;">
      <p style="margin:0;color:#64748B;font-size:11px;letter-spacing:.06em;">BÉNÉFICIAIRE</p>
      <p style="margin:3px 0 0;font-weight:900;color:#0F172A;"><?php echo esc_html($bank_beneficiary); ?></p>
    </div>

    <div style="min-width:160px;">
      <p style="margin:0;color:#64748B;font-size:11px;letter-spacing:.06em;">MONTANT</p>
      <p style="margin:3px 0 0;font-weight:900;color:#0F172A;"><?php echo number_format_i18n((float)$total, 2); ?> €</p>
    </div>
  </div>

  <div style="margin-bottom:10px;">
    <p style="margin:0 0 6px;color:#64748B;font-size:11px;letter-spacing:.06em;">IBAN</p>
    <div style="background:#fff;border:1px solid #D1E3FF;border-radius:10px;padding:12px 12px;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;font-weight:800;color:#0F172A;">
      <?php echo esc_html($bank_iban); ?>
    </div>
  </div>

  <div style="margin-bottom:10px;">
    <p style="margin:0 0 6px;color:#64748B;font-size:11px;letter-spacing:.06em;">BIC</p>
    <div style="background:#fff;border:1px solid #D1E3FF;border-radius:10px;padding:12px 12px;font-weight:800;color:#0F172A;">
      <?php echo esc_html($bank_bic); ?>
    </div>
  </div>

  <div style="margin-bottom:10px;">
    <p style="margin:0 0 6px;color:#64748B;font-size:11px;letter-spacing:.06em;">RÉFÉRENCE DE PAIEMENT</p>
    <div style="background:#fff;border:1px solid #FDBA74;border-radius:10px;padding:12px 12px;font-weight:900;color:#C2410C;">
      <?php echo esc_html($bank_reference); ?>
    </div>
  </div>

  <p style="margin:10px 0 0;color:#334155;font-style:italic;font-size:13px;">
    Merci d'indiquer la référence lors de votre virement pour faciliter le traitement de votre réservation.
  </p>
</div>
<?php endif; ?>

<?php if ($note_box && !empty($note_box['text'])) : ?>
  <div style="margin-top:14px;padding:12px 14px;border-radius:12px;background:<?php echo esc_attr($note_box['bg'] ?? '#FFF7ED'); ?>;border:1px solid <?php echo esc_attr($note_box['border'] ?? '#FED7AA'); ?>;color:<?php echo esc_attr($note_box['color'] ?? '#9A3412'); ?>;font-size:13px;">
    <?php echo esc_html($note_box['text']); ?>
  </div>
<?php endif; ?>

<?php if ( $show_cta ) : ?>
  <!-- CTA -->
  <div style="margin:18px 0 0;display:flex;gap:12px;flex-wrap:wrap;">
    <?php if (!empty($pay_url)) : ?>
      <a href="<?php echo esc_url($pay_url); ?>" style="display:inline-block;background:#F97316;color:#fff;text-decoration:none;font-weight:900;padding:14px 22px;border-radius:12px;">
        <?php echo esc_html($cta_primary_label); ?>
      </a>
    <?php endif; ?>

    <a href="mailto:<?php echo esc_attr($support_email); ?>" style="display:inline-block;background:#F3F4F6;color:#111827;text-decoration:none;font-weight:900;padding:14px 22px;border-radius:12px;">
      Contacter le support
    </a>
  </div>
<?php endif; ?>
