<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); 


	$slide_ids = get_post_meta(get_the_ID(), '_logestay_hero_gallery_ids', true);
	$slide_array = explode(',', $slide_ids);
	$settings = get_option('logestay_settings');


	 ?>

	<div class="min-h-screen">
	   <section class="relative w-full h-screen flex items-center justify-center overflow-hidden">
	      <div class="absolute inset-0 z-0">
	         <div class="relative group h-full w-full logestay-hero-slider" data-autoplay="1" data-interval="6000">
	            <div class="relative overflow-hidden  h-full w-full">
	            	<?php 
		            	if($slide_array) { foreach ($slide_array as $key => $image_id){
		            		$classes = 'logestay-hero-slide absolute inset-0 w-full h-full object-cover transition-all duration-1000 opacity-0 group-hover:scale-105';
		            		if($key == 0){
		            			$classes = 'logestay-hero-slide absolute inset-0 w-full h-full object-cover transition-all duration-1000 opacity-100 group-hover:scale-105';
		            		}
		            		echo wp_get_attachment_image($image_id, 'full', false, array('class' => $classes));
		            	}}
	            	?>
	               <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
	            </div>
	            <button class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white hover:scale-110 logestay-hero-prev" aria-label="Image précédente">
	               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left w-5 h-5 text-gray-800">
	                  <path d="m15 18-6-6 6-6"></path>
	               </svg>
	            </button>
	            <button class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white hover:scale-110 logestay-hero-next" aria-label="Image suivante">
	               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-5 h-5 text-gray-800">
	                  <path d="m9 18 6-6-6-6"></path>
	               </svg>
	            </button>
	            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 logestay-hero-dots"><button class="w-2 h-2 rounded-full transition-all duration-300 bg-white/50 hover:bg-white/75" aria-label="Aller à l'image 1"></button><button class="w-2 h-2 rounded-full transition-all duration-300 bg-white/50 hover:bg-white/75" aria-label="Aller à l'image 2"></button><button class="w-2 h-2 rounded-full transition-all duration-300 bg-white/50 hover:bg-white/75" aria-label="Aller à l'image 3"></button><button class="w-2 h-2 rounded-full transition-all duration-300 bg-white w-6" aria-label="Aller à l'image 4"></button></div>
	         </div>
	         <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/40 to-black/70"></div>
	      </div>
	      <div class="absolute top-4 left-4 sm:top-6 sm:left-6 z-20">
	      	<a href="<?php the_permalink(); ?>">
	      		<?php 
		      		$custom_logo_id = $settings['logestay_logo_id'];
		      		echo wp_get_attachment_image($custom_logo_id, 'full', false, array('class' => 'h-10 sm:h-14 md:h-16 lg:h-20 w-auto drop-shadow-2xl'));
		      	?>
	      	</a>
	      </div>
	      <div class="absolute top-4 right-4 sm:top-6 sm:right-6 z-20">
	        	<?php
				/**
				 * LOGESTAY Polylang language switcher (custom markup)
				 * - Uses pll_the_languages(['raw'=>1]) so we can build exact HTML
				 * - Uses emoji flags (map by slug) with fallback
				 */
				if ( function_exists('pll_the_languages') ) :

				  $langs = pll_the_languages([
				    'raw'               => 1,
				    'hide_if_empty'     => 0,
				    'hide_if_no_translation' => 0,
				  ]);

				  if ( is_array($langs) && ! empty($langs) ) :

				    // Map Polylang language slug => emoji flag
				    $flag_map = [
				      'fr' => '🇫🇷',
				      'en' => '🇬🇧',
				      'pt' => '🇵🇹',
				      'es' => '🇪🇸',
				      // add more if you need
				      // 'de' => '🇩🇪',
				      // 'it' => '🇮🇹',
				    ];

				    $current = null;
				    foreach ( $langs as $l ) {
				      if ( ! empty($l['current_lang']) ) {
				        $current = $l;
				        break;
				      }
				    }
				    if ( ! $current ) $current = reset($langs);

				    $current_slug = (string)($current['slug'] ?? '');
				    $current_name = (string)($current['name'] ?? '');
				    $current_flag = $flag_map[$current_slug] ?? '🌐';

				    $label_select = function_exists('pll__') ? pll__('Select language') : 'Select language';
				    ?>
				    <div class="relative z-50 logestay-lang">
				      <button
				        type="button"
				        class="flex items-center gap-2 px-3 py-2 sm:px-4 bg-white/10 backdrop-blur-md rounded-full text-white hover:bg-white/20 transition-all duration-300 border border-white/20 shadow-lg logestay-lang-btn"
				        aria-label="<?php echo esc_attr($label_select); ?>"
				        aria-expanded="false"
				      >
				        <span class="text-lg sm:text-xl"><?php echo esc_html($current_flag); ?></span>
				        <span class="hidden sm:inline text-sm font-medium"><?php echo esc_html($current_name); ?></span>
				        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300 logestay-lang-chevron"></i>
				      </button>

				      <div class="absolute top-full mt-2 right-0 bg-white rounded-xl shadow-xl overflow-hidden min-w-[160px] animate-fade-in hidden logestay-lang-menu">
				        <?php foreach ( $langs as $l ) :
				          $slug = (string)($l['slug'] ?? '');
				          $name = (string)($l['name'] ?? '');
				          $url  = (string)($l['url'] ?? '#');
				          $is_current = ! empty($l['current_lang']);
				          $flag = $flag_map[$slug] ?? '🌐';

				          // Same classes as your markup
				          $item_class = $is_current
				            ? 'w-full flex items-center gap-3 px-4 py-3 text-left transition-colors duration-200 bg-amber-50 text-amber-900 font-semibold'
				            : 'w-full flex items-center gap-3 px-4 py-3 text-left transition-colors duration-200 text-gray-700 hover:bg-gray-50';
				          ?>
				          <a
				            href="<?php echo esc_url($url); ?>"
				            class="<?php echo esc_attr($item_class); ?>"
				            data-lang="<?php echo esc_attr($slug); ?>"
				          >
				            <span class="text-xl"><?php echo esc_html($flag); ?></span>
				            <span class="text-sm"><?php echo esc_html($name); ?></span>
				          </a>
				        <?php endforeach; ?>
				      </div>
				    </div>
				  <?php endif; ?>
				<?php endif; ?>
	      </div>
	      <div class="relative z-10 text-center text-white px-6 max-w-5xl mx-auto flex flex-col justify-center h-full">
	         <div class="flex-1 flex flex-col items-center justify-center">
	            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold mb-4 md:mb-6 tracking-tight animate-fade-in drop-shadow-2xl leading-tight whitespace-nowrap"><?php echo get_post_meta(get_the_ID(), '_logestay_hero_title', true); ?></h1>
	            <div class="flex items-center justify-center gap-2 md:gap-3 mb-6 md:mb-8 animate-fade-in-delay-2">
	               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-5 h-5 md:w-6 md:h-6 fill-amber-400 text-amber-400 drop-shadow-lg">
	                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
	               </svg>
	               <span class="text-lg md:text-xl font-bold"><?php echo get_post_meta(get_the_ID(), '_logestay_hero_rating', true); ?></span><span class="text-gray-300">•</span><span class="text-lg md:text-xl"><?php echo get_post_meta(get_the_ID(), '_logestay_hero_review', true); ?></span>
	            </div>
	            <a href="<?php echo get_post_meta(get_the_ID(), '_logestay_hero_btn_link', true); ?>" class="bg-white text-gray-900 px-8 py-4 md:px-10 md:py-5 rounded-full text-lg md:text-xl font-bold hover:bg-amber-50 transition-all duration-300 shadow-2xl hover:shadow-amber-500/20 hover:scale-105 animate-fade-in-delay-3">
	            	<?php echo get_post_meta(get_the_ID(), '_logestay_hero_btn_text', true); ?>
	            </a>
	         </div>
	         <p class="text-lg sm:text-xl md:text-2xl font-light mb-20 sm:mb-24 animate-fade-in-delay tracking-wide">
	         	<?php echo get_post_meta(get_the_ID(), '_logestay_hero_subtitle', true); ?>
	         </p>
	      </div>
	      <a href="#city-selector" class="absolute bottom-6 sm:bottom-10 left-1/2 -translate-x-1/2 z-10 text-white animate-bounce hover:scale-110 transition-transform duration-300" aria-label="Défiler vers le bas">
	         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-8 h-8 sm:w-10 sm:h-10 drop-shadow-lg">
	            <path d="m6 9 6 6 6-6"></path>
	         </svg>
	      </a>
	   </section>