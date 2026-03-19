<?php 
/*
Template Name: Homepage
*/
get_header();

   $ids = get_post_meta(get_the_ID(), '_logestay_destination_select', true);
   $ids = is_array($ids) ? array_values(array_filter(array_map('absint', $ids))) : [];

   // If selected IDs exist -> query only those
   if (!empty($ids)) {

     $city_query = new WP_Query([
       'post_type'      => 'logestay_city',
       'post__in'       => $ids,
       'orderby'        => 'post__in', // keep the same order as selected
       'posts_per_page' => 3,
     ]);

   } else {

     // Default query
     $city_query = new WP_Query([
       'post_type'      => 'logestay_city',
       'posts_per_page' => 3,
     ]);

   }

   $first_city_id = 0;

   if ( ! empty($city_query->posts) ) {
     $first_city_id = (int) $city_query->posts[0]->ID;
   }

   $listing_query = new WP_Query(array(
     'post_type'      => 'logestay_listing',
     'posts_per_page' => 3,
     'meta_query'     => array(
       array(
         'key'     => 'logestay_city_id',
         'value'   => $first_city_id,
         'compare' => '=',
         'type'    => 'NUMERIC',
       )
     )
   ));

 ?>

   <section id="city-selector" class="py-20 md:py-28 bg-gradient-to-b from-gray-50 to-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
         <div class="text-center mb-12 md:mb-16">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-4 md:mb-6">
               <?php echo get_post_meta(get_the_ID(), '_logestay_destination_title', true); ?>
            </h2>
            <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto px-4">
               <?php echo get_post_meta(get_the_ID(), '_logestay_destination_subtitle', true); ?>
            </p>
         </div>
         <?php 
            if($city_query->have_posts()) : ?>
            <div class="md:hidden relative" >
               <div id="logestay-city-mobile-track" class="flex overflow-x-auto snap-x snap-mandatory scrollbar-hide gap-4 px-6" style="scroll-snap-type: x mandatory;">
                  <?php $num = 1; while($city_query->have_posts()) : $city_query->the_post();
                     get_template_part('template-parts/city', 'itemmd', array('num' => $num));
                     $num++;
                  endwhile; wp_reset_postdata(); ?>
               </div>
               <div id="logestay-city-mobile-dots" class="flex justify-center gap-2 mt-6">
                 <?php $num = 1; while($city_query->have_posts()) : $city_query->the_post(); ?>
                   <button
                     type="button"
                     class="logestay-city-dot h-2 rounded-full transition-all duration-300 <?php echo ($num == 1) ? 'w-8 bg-amber-500' : 'w-2 bg-gray-300 hover:bg-gray-400'; ?>"
                     data-index="<?php echo esc_attr($num - 1); ?>"
                     aria-label="<?php echo esc_attr( sprintf(__('View %s', 'logestay'), get_the_title()) ); ?>">
                   </button>
                 <?php $num++; endwhile; wp_reset_postdata(); ?>
               </div>
            </div>
         <?php endif; ?>

         <?php 
            if($city_query->have_posts()) : ?>
            <div  class="hidden md:grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
               <?php $num = 1; while($city_query->have_posts()) : $city_query->the_post();
                  get_template_part('template-parts/city', 'item', array('num' => $num));
                  $num++;
               endwhile; wp_reset_postdata(); ?>

            </div>
            <?php endif; 
         ?>
      </div>
   </section>

   <section id="apartments" class="py-20 md:py-32 bg-gray-50" data-selected-city="<?php echo (int) $first_city_id; ?>">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
         <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
               <?php echo get_post_meta(get_the_ID(), '_logestay_listing_title', true); ?>
               <span class="city_title"><?php echo get_the_title($first_city_id); ?></span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
              <?php echo get_post_meta(get_the_ID(), '_logestay_listing_subtitle', true); ?>
            </p>
         </div>

         <div id="logestay-listings-wrap">
            <?php if($listing_query->have_posts()) : ?>
               <div class="relative">
                  <div class="flex gap-8 overflow-x-auto scrollbar-hide snap-x snap-mandatory scroll-smooth pb-4" style="scrollbar-width: none;">

                     <?php 

                        while($listing_query->have_posts()) : $listing_query->the_post();
                           get_template_part('template-parts/listing', 'item');
                        endwhile; wp_reset_postdata();

                     ?>
                     
                  </div>
               </div>
            <?php else: ?>

               <div class="text-center py-20">
                  <p class="text-2xl md:text-3xl font-semibold text-gray-700 mb-4">
                     <?php echo get_post_meta(get_the_ID(), '_logestay_listing_notfound_title', true); ?>
                  </p>
                  <p class="text-lg text-gray-500">
                     <?php echo get_post_meta(get_the_ID(), '_logestay_listing_notfound_subtitle', true); ?>
                  </p>
               </div>

            <?php endif; ?>
         </div>
      </div>
   </section>

   <?php
// Reviews query (hide hidden ones)
$reviews_q = new WP_Query([
  'post_type'      => 'logestay_review',
  'posts_per_page' => 20,
  'post_status'    => 'publish'
]);

// Calculate average + count
$total = 0;
$count = 0;

if ( $reviews_q->have_posts() ) {
  foreach ( $reviews_q->posts as $p ) {
    $r = (float) get_post_meta( $p->ID, 'logestay_review_rating', true );
    if ( $r > 0 ) {
      $total += $r;
      $count++;
    }
  }
}
$avg = $count ? round( $total / $count, 1 ) : 0;
?>

<section class="py-20 md:py-32 bg-white" id="reviews">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">

    <div class="text-center mb-16">
      <div class="flex items-center justify-center gap-3 mb-4">
        <i data-lucide="star" class="w-8 h-8 fill-amber-400 text-amber-400"></i>
        <span class="text-5xl font-bold text-gray-900">
         <?php 
            if(get_post_meta(get_the_ID(), '_logestay_listing_rating', true)){
               echo get_post_meta(get_the_ID(), '_logestay_listing_rating', true);
            }else{
               echo esc_html( number_format_i18n( $avg, 1 ) );
            }
         ?>
         </span>
      </div>
      <p class="text-xl text-gray-600 mb-8">
         <?php 
            if(get_post_meta(get_the_ID(), '_logestay_listing_reviews', true)){
               echo get_post_meta(get_the_ID(), '_logestay_listing_reviews', true);
            }else{
               echo esc_html( $count );
               echo ' ';
               echo __('traveler reviews', 'logestay');

            }
         ?>
      </p>
      <h2 class="text-4xl md:text-5xl font-bold text-gray-900">
        <?php echo get_post_meta(get_the_ID(), '_logestay_review_title', true); ?>
      </h2>
    </div>

    <?php if ( $reviews_q->have_posts() ) : ?>
      <div class="relative">

        <!-- viewport -->
        <div class="overflow-hidden" id="logestay-reviews-viewport">
          <!-- track -->
          <div
            id="logestay-reviews-track"
            class="flex flex-nowrap transition-transform duration-500 ease-in-out"
          >
            <?php
            $i = 0;
            while ( $reviews_q->have_posts() ) : $reviews_q->the_post();

              $review_id  = get_the_ID();
              $rating     = (float) get_post_meta( $review_id, 'logestay_review_rating', true );
              $guest_name = get_post_meta( $review_id, 'logestay_review_guest_name', true );
              $date_raw   = get_post_meta( $review_id, 'logestay_review_date', true ); // YYYY-MM-DD
              $date_out   = $date_raw ? date_i18n( 'F Y', strtotime( $date_raw ) ) : '';

              // Group 2 cards per slide
              if ( $i % 2 === 0 ) {
               echo '<div class="logestay-review-slide min-w-full flex-shrink-0">';
               echo '<div class="grid md:grid-cols-2 gap-8">';
              }
              ?>

              <div class="bg-gray-50 rounded-2xl p-8 hover:bg-gray-100 transition-colors duration-300 relative">
                <i data-lucide="quote" class="w-10 h-10 text-amber-400 mb-4 opacity-50"></i>

                <div class="flex gap-1 mb-4">
                  <?php
                  $full = (int) floor( $rating );
                  if ( $full < 0 ) $full = 0;
                  if ( $full > 5 ) $full = 5;

                  for ( $s = 1; $s <= 5; $s++ ) :
                    $cls = $s <= $full ? 'text-amber-400 fill-amber-400 logestay-icon-fill' : 'text-gray-300';
                    ?>
                    <i data-lucide="star" class="w-4 h-4 <?php echo esc_attr( $cls ); ?>"></i>
                  <?php endfor; ?>
                </div>

                <p class="text-gray-700 text-lg mb-6 leading-relaxed">
                  <?php 
                  $review_text = apply_filters('the_content', get_the_content());
                  $review_text = wp_strip_all_tags($review_text); // keep it clean like your design
                  echo esc_html($review_text);
                   ?>
                </p>

                <div class="flex items-center justify-between">
                  <p class="font-semibold text-gray-900">
                    <?php echo esc_html( $guest_name ? $guest_name : get_the_title() ); ?>
                  </p>
                  <p class="text-sm text-gray-500">
                    <?php echo esc_html( $date_out ); ?>
                  </p>
                </div>
              </div>

              <?php
              $i++;

              // Close slide wrapper after 2 cards or at end
              if ( $i % 2 === 0 || $i === (int) $reviews_q->post_count ) {
                echo '</div></div>';
              }

            endwhile;
            wp_reset_postdata();
            ?>
          </div>
        </div>

        <!-- arrows -->
        <div class="flex justify-center gap-4 mt-12">
          <button
            type="button"
            id="logestay-reviews-prev"
            class="p-3 rounded-full bg-gray-100 hover:bg-gray-200 transition-all duration-300 hover:scale-110"
            aria-label="Avis précédents"
          >
            <i data-lucide="chevron-left" class="w-6 h-6 text-gray-700"></i>
          </button>

          <button
            type="button"
            id="logestay-reviews-next"
            class="p-3 rounded-full bg-gray-100 hover:bg-gray-200 transition-all duration-300 hover:scale-110"
            aria-label="Avis suivants"
          >
            <i data-lucide="chevron-right" class="w-6 h-6 text-gray-700"></i>
          </button>
        </div>

      </div>
    <?php else : ?>
      <div class="text-center py-16 text-gray-600">
        <?php echo __('No reviews yet.', 'logestay'); ?>
      </div>
    <?php endif; ?>

  </div>
</section>

<style>
/* Make lucide star appear filled when needed */
.logestay-icon-fill svg { fill: currentColor !important; }
</style>
   
   
   <section class="py-20 md:py-32 bg-gradient-to-b from-gray-50 to-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">

         <?php if(get_post_meta($first_city_id, 'logestay_city_map_embed', true)) : ?>
            <div class="text-center mb-12">
               <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                  <?php echo get_post_meta(get_the_ID(), '_logestay_listing_location_title', true); ?> 
                  <span class="js-city-location-title"><?php echo get_the_title($first_city_id); ?></span></h2>
               <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                  <?php echo get_post_meta(get_the_ID(), '_logestay_listing_location_subtitle', true); ?> 
               </p>
            </div>
            <div class="relative mb-16 group">
               <div class="rounded-3xl overflow-hidden shadow-2xl h-[400px] md:h-[500px] relative">
                  <iframe id="js-city-map-iframe" src="<?php echo get_post_meta($first_city_id, 'logestay_city_map_embed', true); ?>" width="100%" height="100%" style="border: 0px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Carte de localisation Toulouse" class="transition-all duration-300"></iframe>
                  <?php if(get_post_meta($first_city_id, 'logestay_city_map_open', true)) : ?>
                     <div class="absolute top-6 right-6 z-10">
                        <a href="<?php echo get_post_meta($first_city_id, 'logestay_city_map_open', true); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 bg-white/95 backdrop-blur-sm px-5 py-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 group/link">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-navigation w-5 h-5 text-emerald-600 group-hover/link:rotate-12 transition-transform duration-300">
                              <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                           </svg>
                           <span class="font-semibold text-gray-900 text-sm hidden sm:inline">
                              <?php _e('Open in Google Maps', 'logestay'); ?>
                           </span>
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-external-link w-4 h-4 text-gray-600">
                              <path d="M15 3h6v6"></path>
                              <path d="M10 14 21 3"></path>
                              <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                           </svg>
                        </a>
                     </div>
                  <?php endif; ?>
               </div>
            </div>
         <?php endif; ?>

            <?php  
               $nearby = get_post_meta($first_city_id, 'logestay_city_nearby', true);
               $nearby = is_array($nearby) ? $nearby : [];


               if($nearby) : 
            ?>

            <div class="nearby_section">
               <div class="flex items-center justify-between mb-8">
                  <h3 class="text-3xl font-bold text-gray-900"><?php _e('Nearby', 'logestay'); ?></h3>
                  <div class="md:flex gap-2">
                     <div class="md:flex gap-2">
                       <button class="js-nearby-prev p-3 rounded-full shadow-md transition-all duration-300 bg-white hover:bg-gray-50 hover:shadow-lg" aria-label="Previous" type="button">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left w-5 h-5 text-gray-900"><path d="m15 18-6-6 6-6"></path></svg>
                       </button>

                       <button class="js-nearby-next p-3 rounded-full shadow-md transition-all duration-300 bg-white hover:bg-gray-50 hover:shadow-lg" aria-label="Next" type="button">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-5 h-5 text-gray-400"><path d="m9 18 6-6-6-6"></path></svg>
                       </button>
                     </div>
                  </div>
               </div>
               <div class="relative -mx-4 sm:mx-0">


                  <div id="js-nearby-wrap" class="flex gap-4 md:gap-6 overflow-x-auto scrollbar-hide snap-x snap-mandatory scroll-smooth pb-4 px-4 sm:px-0" style="scrollbar-width: none;">

                     <?php foreach ($nearby as $n) : 
                        if ( ! is_array($n) ) continue;

                         $title    = sanitize_text_field($n['title'] ?? '');
                         $subtitle = sanitize_text_field($n['subtitle'] ?? '');
                         $distance = sanitize_text_field($n['distance'] ?? '');
                         $url      = esc_url_raw($n['url'] ?? '');
                         $image_id = absint($n['image_id'] ?? 0);

                         if ( $title === '' ) continue;

                         $img = '';
                         if ( $image_id ) {
                           $src = wp_get_attachment_image_src($image_id, 'large');
                           if ( $src && ! empty($src[0]) ) $img = esc_url($src[0]);
                         }
                      ?>
                     <div class="flex-shrink-0 w-[85%] sm:w-[70%] md:w-80 snap-center group/card">
                        <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 h-full">
                           <div class="relative h-48 overflow-hidden">
                              <?php echo wp_get_attachment_image($image_id, 'medium', false, array('class' => 'w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-700') ); ?>
                              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                           </div>
                           <div class="p-6">
                              <h4 class="text-xl font-bold text-gray-900 mb-2"><?php echo $title; ?></h4>
                              <p class="text-gray-600 mb-4"><?php echo $subtitle; ?></p>
                              <div class="flex items-center justify-between">
                                 <span class="text-sm text-gray-500"><?php echo $distance; ?></span>
                                 <a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-semibold text-sm transition-colors duration-300 group/link">
                                    <span><?php _e('Itinerary', 'logestay'); ?></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-navigation w-4 h-4 group-hover/link:translate-x-1 transition-transform duration-300">
                                       <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                                    </svg>
                                 </a>
                              </div>
                           </div>
                        </div>
                     </div>
                  <?php endforeach; ?>
                  </div>
               </div>
            </div>
         <?php endif; ?>
      </div>
   </section>
   
   <?php
   $faq_q = new WP_Query([
     'post_type'      => 'logestay_faq',
     'posts_per_page' => -1,
     'post_status'    => 'publish',
     'orderby'        => 'menu_order',
     'order'          => 'ASC',
   ]);

   $faq_count = (int) $faq_q->post_count;
   ?>

   <section class="py-20 md:py-32 bg-white" id="faq">
     <div class="max-w-4xl mx-auto px-4 sm:px-6">
       <div class="text-center mb-16">
         <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
            <?php echo get_post_meta(get_the_ID(), '_logestay_listing_faq_title', true); ?>
         </h2>
         <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
           <?php echo get_post_meta(get_the_ID(), '_logestay_listing_faq_subtitle', true); ?>
         </p>
       </div>

       <?php if ( $faq_q->have_posts() ) : ?>
         <div class="space-y-4 mb-12 logestay-faq" data-accordion="true">

           <?php while ( $faq_q->have_posts() ) : $faq_q->the_post(); ?>
             <?php
               $q = get_the_title();
               $a_html = apply_filters('the_content', get_the_content());
               $a_html = wp_kses_post($a_html);
             ?>

             <div class="bg-gray-50 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-md logestay-faq-item">
               <button type="button"
                 class="w-full flex items-center justify-between p-6 text-left transition-colors duration-200 logestay-faq-btn"
                 aria-expanded="false"
               >
                 <span class="text-lg font-semibold text-gray-900 pr-4"><?php echo esc_html($q); ?></span>

                 <span class="flex-shrink-0">
                   <i data-lucide="plus" class="w-6 h-6 text-gray-700 logestay-faq-icon"></i>
                 </span>
               </button>

               <div class="overflow-hidden transition-all duration-300 max-h-0 logestay-faq-panel" aria-hidden="true">
                 <div class="px-6 pb-6 pt-0">
                   <div class="text-gray-600 leading-relaxed">
                     <?php echo $a_html; ?>
                   </div>
                 </div>
               </div>
             </div>

           <?php endwhile; wp_reset_postdata(); ?>

         </div>
       <?php else: ?>
         <div class="text-center py-10 text-gray-500"><?php _e('No FAQs found.', 'logestay'); ?></div>
       <?php endif; ?>

       <div class="text-center bg-gray-50 rounded-2xl p-8 md:p-12">
         <i data-lucide="message-circle" class="w-12 h-12 text-gray-900 mx-auto mb-4"></i>
         <h3 class="text-2xl font-bold text-gray-900 mb-3">
            <?php echo get_post_meta(get_the_ID(), '_logestay_listing_support_title', true); ?>
         </h3>
         <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
            <?php echo get_post_meta(get_the_ID(), '_logestay_listing_support_subtitle', true); ?>
         </p>
         <button class="bg-gray-900 text-white px-8 py-4 rounded-xl font-semibold hover:bg-gray-800 transition-all duration-300 hover:scale-105 shadow-lg open_ct_form secondary_btn">
           <?php _e('Contact support', 'logestay'); ?>
         </button>
       </div>
     </div>
   </section>

   <script>
     // make sure lucide converts <i data-lucide="..."> into SVG
     if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
   </script>
   
   <section class="py-20 md:py-32 bg-gradient-to-b from-gray-900 to-gray-800 text-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
         <h2 class="text-4xl md:text-5xl font-bold mb-6">
            <?php echo get_post_meta(get_the_ID(), '_logestay_listing_cta_title', true); ?>
         </h2>
         <p class="text-xl text-gray-300 mb-12 max-w-3xl mx-auto leading-relaxed">
            <?php echo get_post_meta(get_the_ID(), '_logestay_listing_cta_subtitle', true); ?>
         </p>
         <div class="flex flex-col items-center gap-6 max-w-2xl mx-auto">
            <?php if(!get_post_meta(get_the_ID(), '_logestay_listing_cta_btn', true)) : ?>
               <a href="#apartments" class="w-full bg-white text-gray-900 px-8 py-6 rounded-2xl font-bold text-xl hover:bg-gray-100 transition-all duration-300 shadow-2xl hover:shadow-3xl hover:scale-105 flex items-center justify-center gap-3 group">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check w-6 h-6 group-hover:scale-110 transition-transform duration-300">
                     <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                     <path d="m9 12 2 2 4-4"></path>
                  </svg>
                  <span><?php _e('Book on the site', 'logestay'); ?></span>
               </a>
            <?php endif; ?>

            <?php 
               $airbnb  = get_post_meta( get_the_ID(), '_logestay_listing_cta_hide_airbnb', true );
               $booking = get_post_meta( get_the_ID(), '_logestay_listing_cta_hide_booking', true );
               if ( empty($airbnb) || empty($booking) ) : ?>
               <div class="w-full text-sm text-gray-400 my-2">
                  <?php _e('or through our partners', 'logestay'); ?>
               </div>
            <?php endif; ?>
            <div class="w-full grid sm:grid-cols-2 gap-4">
               <?php if(!$airbnb) : ?>
                  <a href="<?php echo get_post_meta(get_the_ID(), '_logestay_listing_cta_airbnb_url', true); ?>" target="_blank" rel="noopener noreferrer" class="bg-[#FF5A5F] text-white px-6 py-4 rounded-xl font-semibold text-base hover:bg-[#E0484D] transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 flex items-center justify-center gap-2 group">
                     <span><?php _e('Book on Airbnb', 'logestay'); ?></span>
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-external-link w-4 h-4 group-hover:translate-x-1 transition-transform duration-300">
                        <path d="M15 3h6v6"></path>
                        <path d="M10 14 21 3"></path>
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                     </svg>
                  </a>
               <?php endif; ?>

               <?php if(!$booking) : ?>
                  <a href="<?php echo get_post_meta(get_the_ID(), '_logestay_listing_cta_booking_url', true); ?>" target="_blank" rel="noopener noreferrer" class="bg-[#003580] text-white px-6 py-4 rounded-xl font-semibold text-base hover:bg-[#002A66] transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 flex items-center justify-center gap-2 group">
                     <span><?php _e('Book on Booking', 'logestay'); ?></span>
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-external-link w-4 h-4 group-hover:translate-x-1 transition-transform duration-300">
                        <path d="M15 3h6v6"></path>
                        <path d="M10 14 21 3"></path>
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                     </svg>
                  </a>
                <?php endif; ?>
            </div>
         </div>
      </div>
   </section>
   

<?php get_footer(); ?>