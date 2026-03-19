<?php 
	
	$id = get_the_ID(); 
	$logestay_city_id = get_post_meta($id, 'logestay_city_id', true);
	$city_name = get_the_title($logestay_city_id);
	$amenities = get_post_meta($id, 'logestay_listing_amenities', true);
	$featured_image_id = get_post_thumbnail_id( $id );
	$galleries         = get_post_meta( $id, 'logestay_listing_gallery', true );
	$galleries         = is_array( $galleries ) ? $galleries : [];

	$image_ids = [];

	if ( $featured_image_id ) {
	  $image_ids[] = (int) $featured_image_id;
	}

	foreach ( $galleries as $gid ) {
	  $gid = (int) $gid;
	  if ( ! $gid ) continue;
	  if ( $featured_image_id && $gid === (int) $featured_image_id ) continue;
	  $image_ids[] = $gid;
	}

?>
<div class="flex-shrink-0 w-full md:w-[calc(50%-1rem)] snap-center transition-all duration-500 ">
    <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 group h-full flex flex-col">
        <div class="cursor-pointer">
           <div class="relative group logestay-card-gallery">
              <div class="relative overflow-hidden rounded-xl aspect-video">
                  <?php if ( ! empty( $image_ids ) ) : ?>
			        <?php foreach ( $image_ids as $index => $att_id ) :

			          // Use large for card (better quality), fallback to full if needed
			          $src = wp_get_attachment_image_url( $att_id, 'large' );
			          if ( ! $src ) $src = wp_get_attachment_image_url( $att_id, 'full' );

			          $alt = get_post_meta( $att_id, '_wp_attachment_image_alt', true );
			          $alt = $alt ? $alt : get_the_title( $id ) . ' - ' . ( $index + 1 );

			          $opacity = ( $index === 0 ) ? 'opacity-100' : 'opacity-0';
			        ?>
			          <img
			            src="<?php echo esc_url( $src ); ?>"
			            alt="<?php echo esc_attr( $alt ); ?>"
			            class="absolute inset-0 w-full h-full object-cover transition-all duration-1000 <?php echo esc_attr( $opacity ); ?> group-hover:scale-105"
			            loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
			          >
			        <?php endforeach; ?>

			        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

			      <?php else : ?>
			        <div class="absolute inset-0 bg-gray-200 flex items-center justify-center text-gray-500">
			          No image
			        </div>
			      <?php endif; ?>
              </div>
              <?php if ( count( $image_ids ) > 1 ) : ?>
			      <button class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white hover:scale-110" aria-label="Image précédente">
			        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left w-5 h-5 text-gray-800">
			          <path d="m15 18-6-6 6-6"></path>
			        </svg>
			      </button>

			      <button class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white hover:scale-110" aria-label="Image suivante">
			        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-5 h-5 text-gray-800">
			          <path d="m9 18 6-6-6-6"></path>
			        </svg>
			      </button>

			      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
			        <?php foreach ( $image_ids as $i => $_ ) : ?>
			          <button
			            class="w-2 h-2 rounded-full transition-all duration-300 <?php echo $i === 0 ? 'bg-white w-6' : 'bg-white/50 hover:bg-white/75'; ?>"
			            aria-label="Aller à l'image <?php echo (int) ( $i + 1 ); ?>"
			          ></button>
			        <?php endforeach; ?>
			      </div>
			    <?php endif; ?>
           </div>
        </div>
        <div class="p-8 flex-1 flex flex-col">
           <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
           	<?php the_title(); ?>
           </h3>
           <div class="flex items-center gap-2 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-4 h-4 text-gray-500">
                 <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                 <circle cx="12" cy="10" r="3"></circle>
              </svg>
              <span class="text-sm text-gray-600 font-medium">
              	<?php echo $city_name; ?>
              </span>
           </div>
           <div class="text-gray-600 mb-6 leading-relaxed">
           	<?php the_excerpt(); ?>
           </div>
           <?php if ( ! empty( $amenities ) ) : ?>
			  <div class="flex flex-wrap gap-2 mb-8">
			    <?php foreach ( $amenities as $item ) : 
			      $label = esc_html( $item['label'] ?? '' );
			      $icon  = esc_attr( $item['icon'] ?? 'check' );
			      if ( ! $label ) continue;
			    ?>
			      <div class="flex items-center gap-2 bg-gray-50 hover:bg-gray-100 px-3 py-2 rounded-full border border-gray-200 transition-colors duration-200">
			        <i data-lucide="<?php echo $icon; ?>" class="w-4 h-4 text-gray-700"></i>
			        <span class="text-sm text-gray-700 font-medium">
			          <?php echo $label; ?>
			        </span>
			      </div>
			    <?php endforeach; ?>
			  </div>
			<?php endif; ?>
           <div class="mt-auto space-y-3">
              <button data-id="<?php echo $id; ?>" class="w-full bg-gray-900 text-white py-4 rounded-xl font-bold text-lg hover:bg-gray-800 transition-all duration-300 flex items-center justify-center gap-2 group-hover:scale-[1.02] shadow-lg hover:shadow-xl view_av secondary_btn">
                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-5 h-5">
                    <path d="M8 2v4"></path>
                    <path d="M16 2v4"></path>
                    <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                    <path d="M3 10h18"></path>
                 </svg>
                 <?php _e('View availability', 'logestay'); ?>
              </button>
              <p class="text-center text-sm text-gray-500"><?php _e('No commission – Direct booking', 'logestay'); ?></p>
           </div>
        </div>
     </div>
  </div>