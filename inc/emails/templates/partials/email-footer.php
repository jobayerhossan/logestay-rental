<?php if ( ! defined('ABSPATH') ) exit; ?>
      </div>

      <div style="padding:18px 28px;border-top:1px solid #edf2f7;color:#6b7280;font-size:12px;line-height:1.6;">
        <p style="margin:0;color:#334155;font-size:14px;">
          Une question ? Notre équipe support est disponible 7j/7 pour vous accompagner.
        </p>
        <div style="margin-bottom:6px;">
          <?php echo esc_html($site_name); ?> • Support: <?php echo esc_html($support_email ?? get_option('admin_email')); ?>
        </div>
        <div>
          <?php echo esc_html__('You received this email because you made a booking on LOGESTAY.', 'logestay'); ?>
        </div>
      </div>

    </div>
  </div>
</body>
</html>