<?php if ( ! defined('ABSPATH') ) exit; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo esc_html( get_bloginfo('name') ); ?></title>
</head>
<body style="margin:0;padding:0;background:#0B1B3A;">
  <?php if ( ! empty($preheader) ) : ?>
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
      <?php echo esc_html($preheader); ?>
    </div>
  <?php endif; ?>

  <div style="padding:40px 12px;">
    <div style="max-width:680px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 12px 40px rgba(0,0,0,.2);">

      <div style="padding:24px 28px;border-bottom:1px solid #edf2f7;">
        <?php if ( ! empty($logo_url) ) : ?>
          <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_name); ?>" style="height:38px;width:auto;display:block;">
        <?php else : ?>
          <div style="font-weight:800;font-size:18px;color:#111;"><?php echo esc_html($site_name); ?></div>
        <?php endif; ?>
      </div>

      <div style="padding:28px;">