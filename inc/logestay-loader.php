<?php
/**
 * LOGE STAY Loader
 *
 * @package logestay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$logestay_inc_files = [
	'/inc/post-types/logestay-city.php',
	'/inc/post-types/logestay-listing.php',
	'/inc/post-types/logestay-booking.php',
	'/inc/post-types/logestay-review.php',
	'/inc/post-types/logestay-faq.php',
	'/inc/taxonomies/logestay-amenity.php',
	'/inc/settings/settings.php',
	'/inc/admin/logestay-city-metabox.php',
	'/inc/admin/logestay-listing-metabox.php',
	'/inc/admin/logestay-booking-metabox.php',
	'/inc/admin/review-metabox.php',
	'/inc/ajax.php',
	'/inc/booking.php',
	'/inc/cron.php',
];

foreach ( $logestay_inc_files as $file ) {
	$path = get_stylesheet_directory() . $file;
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}
