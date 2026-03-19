<?php 
$select_class = 'opacity-0';
$cityitem_class = 'relative w-full h-[240px] md:h-[280px] rounded-2xl overflow-hidden shadow-xl transition-all duration-300 md:hover:scale-105 md:hover:shadow-2xl';
if($args['num'] == 1){
	$select_class = 'opacity-100';
	$cityitem_class = 'relative w-full h-[240px] md:h-[280px] rounded-2xl overflow-hidden shadow-xl transition-all duration-300 md:hover:scale-105 md:hover:shadow-2xl ring-4 ring-amber-500 shadow-2xl';
}

$listing_count = new WP_Query([
    'post_type'      => 'logestay_listing',
    'post_status'    => 'publish',
    'posts_per_page' => 1,
    'fields'         => 'ids',
    'meta_query'     => [
        [
            'key'   => 'logestay_city_id',
            'value' => get_the_ID()
        ],
    ],
]);

$count = $listing_count->found_posts;
?>
<div class="min-w-[85%] snap-center pt-1" style="scroll-snap-align: center;">
  <button class="<?php echo $cityitem_class ; ?> city_item" data-id="<?php echo get_the_ID(); ?>">
     <?php the_post_thumbnail('large', array('class' => "w-full h-full object-cover")); ?>
     <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
     <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8 text-white">
        <h3 class="text-3xl md:text-4xl font-bold mb-3"><?php the_title(); ?></h3>
        <div class="inline-flex items-center gap-2 bg-white/25 backdrop-blur-md px-4 py-2 rounded-full"><span class="text-sm md:text-base font-semibold"><?php echo $count; ?> <?php _e('available properties', 'logestay'); ?></span></div>
     </div>
     <div class="absolute top-4 right-4 bg-amber-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg <?php echo $select_class; ?>"><?php _e('Selected', 'logestay'); ?></div>
  </button>
</div>