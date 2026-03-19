<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div>
	   <div class="min-h-screen bg-gray-50">
	      <div class="max-w-4xl mx-auto px-6 py-12">
	         <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-8 transition-colors">
	            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left w-4 h-4">
	               <path d="m12 19-7-7 7-7"></path>
	               <path d="M19 12H5"></path>
	            </svg>
	            <span class="text-sm"><?php _e('Back', 'logestay'); ?></span>
	         </a>
	         <?php while(have_posts()) : the_post(); ?>
		         <div class="bg-white rounded-lg shadow-sm p-8 md:p-12">
		            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-8">
		            	<?php the_title(); ?>
		            </h1>
		            <div class="prose prose-gray max-w-none page_content">
		               <?php the_content(); ?>
		            </div>
		         </div>
		     <?php endwhile; wp_reset_postdata(); ?>
	      </div>
	   </div>
   
<?php get_footer(); ?>