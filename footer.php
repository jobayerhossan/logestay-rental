<?php 
   $settings = get_option('logestay_settings');

   $title    = isset($settings['logestay_footer_title']) ? $settings['logestay_footer_title'] : '';
   $subtitle = isset($settings['logestay_footer_subtitle']) ? $settings['logestay_footer_subtitle'] : '';
   $copy     = isset($settings['logestay_copyright']) ? $settings['logestay_copyright'] : '';
?>

   <footer class="relative w-full bg-gradient-to-b from-gray-900 to-black text-gray-300">
      <div class="max-w-7xl mx-auto px-6 py-12 text-center">
         <div class="space-y-4">
            <p class="text-sm font-medium text-gray-400"><?php echo function_exists('pll__') ? pll__($title) : esc_html($title); ?></p>
            <p class="text-xs text-gray-500"><?php echo function_exists('pll__') ? pll__($subtitle) : esc_html($subtitle); ?></p>
            <div class="pt-6 mt-6 border-t border-gray-800">
               <?php
                  $locations = get_nav_menu_locations();
                  $menu_id   = isset($locations['footer_legal']) ? (int) $locations['footer_legal'] : 0;

                  if ( $menu_id ) {
                     $items = wp_get_nav_menu_items($menu_id);

                     // Keep only top-level items (optional)
                     $items = array_values(array_filter((array) $items, function($it){
                        return (int) $it->menu_item_parent === 0;
                     }));

                     if ( ! empty($items) ) : ?>
                        <div class="flex flex-wrap justify-center gap-4 mb-4">
                           <?php foreach ( $items as $i => $item ) : ?>
                              <?php if ( $i > 0 ) : ?>
                                 <span class="text-xs text-gray-700">•</span>
                              <?php endif; ?>

                              <a
                                 href="<?php echo esc_url($item->url); ?>"
                                 class="text-xs text-gray-500 hover:text-gray-300 transition-colors"
                                 <?php echo ! empty($item->target) ? 'target="'.esc_attr($item->target).'"' : ''; ?>
                                 <?php echo ! empty($item->xfn) ? 'rel="'.esc_attr($item->xfn).'"' : ''; ?>
                              >
                                 <?php echo esc_html($item->title); ?>
                              </a>
                           <?php endforeach; ?>
                        </div>
                     <?php endif;
                  }
                  ?>
               <p class="text-xs text-gray-500">
                  <?php echo function_exists('pll__') ? pll__($copy) : esc_html($copy); ?>
               </p>
            </div>
         </div>
      </div>
   </footer>
</div>

<?php wp_footer(); ?>
</body>
</html>