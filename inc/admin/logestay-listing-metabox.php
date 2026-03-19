<?php
/**
 * Listing Metabox UI (City, capacity, pricing, iCal, external links, gallery)
 *
 * @package logestay
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'logestay_listing_meta',
		__( 'Listing Settings', 'logestay' ),
		'logestay_render_listing_meta_box',
		'logestay_listing',
		'normal',
		'high'
	);
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	// Only on listing edit screens
	if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'logestay_listing' !== $screen->post_type ) {
		return;
	}

	// Media uploader + sortable
	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );

	// Lucide Icons
	wp_enqueue_script(
		'logestay-lucide',
		get_template_directory_uri() . '/assets/vendor/lucide.min.js',
		[],
		null,
		true
	);

	// Amenities JS
	wp_enqueue_script(
		'logestay-listing-amenities',
		get_template_directory_uri() . '/assets/admin/listing-amenities.js',
		[ 'jquery', 'jquery-ui-sortable', 'logestay-lucide' ],
		'1.0',
		true
	);

	// Amenities CSS
	wp_enqueue_style(
		'logestay-listing-amenities',
		get_template_directory_uri() . '/assets/admin/listing-amenities.css',
		[],
		'1.0'
	);

	wp_add_inline_style( 'wp-admin', '
		
	' );

	// Small inline JS + CSS for gallery UI
	wp_add_inline_style( 'wp-admin', '
		
	' );


} );

function logestay_render_listing_meta_box( WP_Post $post ) {
	wp_nonce_field( 'logestay_listing_meta_save', 'logestay_listing_meta_nonce' );

	$amenity_presets = [
		[ 'key' => 'area',        'label' => '70 m²',             'icon' => 'square' ],
		[ 'key' => 'apartment',   'label' => 'Appartement',       'icon' => 'building-2' ],
		[ 'key' => 'floor',       'label' => '1er étage',         'icon' => 'arrow-up' ],
		[ 'key' => 'elevator',    'label' => 'Ascenseur',         'icon' => 'move' ],
		[ 'key' => 'bedrooms',    'label' => '2 chambres',        'icon' => 'bed' ],
		[ 'key' => 'living',      'label' => 'Salon',             'icon' => 'sofa' ],
		[ 'key' => 'dining',      'label' => 'Salle à manger',    'icon' => 'utensils-crossed' ],
		[ 'key' => 'office',      'label' => 'Bureau',            'icon' => 'briefcase' ],
		[ 'key' => 'kitchen',     'label' => 'Cuisine équipée',   'icon' => 'chef-hat' ],
		[ 'key' => 'wifi',        'label' => 'Wi-Fi',             'icon' => 'wifi' ],
		[ 'key' => 'tv',          'label' => 'TV',                'icon' => 'tv' ],
		[ 'key' => 'ac',          'label' => 'Climatisation',     'icon' => 'wind' ],
		[ 'key' => 'heat',        'label' => 'Chauffage',         'icon' => 'flame' ],
		[ 'key' => 'washer',      'label' => 'Lave-linge',        'icon' => 'washing-machine' ],
		[ 'key' => 'dishwasher',  'label' => 'Lave-vaisselle',    'icon' => 'utensils' ],
		[ 'key' => 'microwave',   'label' => 'Micro-ondes',       'icon' => 'microwave' ],
		[ 'key' => 'bath',        'label' => 'Salle de bain',     'icon' => 'bath' ],
		[ 'key' => 'shower',      'label' => 'Douche',            'icon' => 'shower-head' ],
		[ 'key' => 'parking',     'label' => 'Parking',           'icon' => 'car' ],
		[ 'key' => 'secure',      'label' => 'Accès sécurisé',    'icon' => 'shield-check' ],
		[ 'key' => 'intercom',    'label' => 'Interphone',        'icon' => 'phone' ],
		[ 'key' => 'nosmoke',     'label' => 'Non-fumeur',        'icon' => 'ban' ],
		[ 'key' => 'pets',        'label' => 'Animaux acceptés',  'icon' => 'paw-print' ],
		[ 'key' => 'pmr',         'label' => 'Accès PMR',         'icon' => 'accessibility' ],
	];

	$lucide_icons = [
		'square','building-2','arrow-up','move','bed','sofa','chef-hat','wifi','tv','wind','flame',
		'washing-machine','utensils','utensils-crossed','microwave','bath','shower-head','car','shield-check','phone',
		'ban','paw-print','accessibility','briefcase','check'
	];

	$amenities = get_post_meta( $post->ID, 'logestay_listing_amenities', true );
	$amenities = is_array( $amenities ) ? $amenities : [];

	$city_id = (int) get_post_meta( $post->ID, 'logestay_city_id', true );

	$gallery = get_post_meta( $post->ID, 'logestay_listing_gallery', true );
	$gallery = is_array( $gallery ) ? $gallery : [];

	$max_adults   = get_post_meta( $post->ID, 'logestay_max_adults', true );
	$max_children = get_post_meta( $post->ID, 'logestay_max_children', true );
	$max_pets     = get_post_meta( $post->ID, 'logestay_max_pets', true );
	$min_nights   = get_post_meta( $post->ID, 'logestay_min_nights', true );
	$max_nights   = get_post_meta( $post->ID, 'logestay_max_nights', true );

	$price    = get_post_meta( $post->ID, 'logestay_price_per_night', true );
	$cleaning = get_post_meta( $post->ID, 'logestay_cleaning_fee', true );
	$deposit  = get_post_meta( $post->ID, 'logestay_security_deposit', true );
	$currency = get_post_meta( $post->ID, 'logestay_currency', true );

	$ical_airbnb  = get_post_meta( $post->ID, 'logestay_ical_airbnb_url', true );
	$ical_booking = get_post_meta( $post->ID, 'logestay_ical_booking_url', true );

	$airbnb_link  = get_post_meta( $post->ID, 'logestay_airbnb_link', true );
	$booking_link = get_post_meta( $post->ID, 'logestay_booking_link', true );

	$cities = get_posts([
		'post_type'      => 'logestay_city',
		'posts_per_page' => -1,
		'post_status'    => [ 'publish', 'draft', 'pending', 'private' ],
		'orderby'        => 'title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	]);

	$checkin_time  = get_post_meta( $post->ID, 'logestay_checkin_time', true );
	$checkout_time = get_post_meta( $post->ID, 'logestay_checkout_time', true );

	$gallery_csv = implode( ',', array_map( 'absint', $gallery ) );

	// Placeholder thumbnail for initial render (if attachment fetch fails)
	$placeholder = includes_url( 'images/media/default.png' );
	?>
	<div class="logestay-field">
		<label><?php esc_html_e( 'City', 'logestay' ); ?></label>
		<select name="logestay_city_id" class="widefat">
			<option value="0"><?php esc_html_e( '— Select City —', 'logestay' ); ?></option>
			<?php foreach ( $cities as $city ) : ?>
				<option value="<?php echo (int) $city->ID; ?>" <?php selected( $city_id, (int) $city->ID ); ?>>
					<?php echo esc_html( $city->post_title ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<p class="logestay-help"><?php esc_html_e( 'Assign this listing to a destination city.', 'logestay' ); ?></p>
	</div>

	<hr>

	<h4 style="margin:0 0 10px;"><?php esc_html_e( 'Capacity & Limits', 'logestay' ); ?></h4>
	<div class="logestay-grid-3">
		<div class="logestay-field">
			<label><?php esc_html_e( 'Max Adults', 'logestay' ); ?></label>
			<input type="number" min="0" class="widefat" name="logestay_max_adults" value="<?php echo esc_attr( $max_adults ); ?>">
		</div>
		<div class="logestay-field">
			<label><?php esc_html_e( 'Max Children', 'logestay' ); ?></label>
			<input type="number" min="0" class="widefat" name="logestay_max_children" value="<?php echo esc_attr( $max_children ); ?>">
		</div>
		<div class="logestay-field">
			<label><?php esc_html_e( 'Max Pets', 'logestay' ); ?></label>
			<input type="number" min="0" class="widefat" name="logestay_max_pets" value="<?php echo esc_attr( $max_pets ); ?>">
		</div>
	</div>

	<div class="logestay-grid-3" style="margin-top:12px;">
		<div class="logestay-field">
			<label><?php esc_html_e( 'Min Nights', 'logestay' ); ?></label>
			<input type="number" min="0" class="widefat" name="logestay_min_nights" value="<?php echo esc_attr( $min_nights ); ?>">
		</div>
		<div class="logestay-field">
			<label><?php esc_html_e( 'Max Nights', 'logestay' ); ?></label>
			<input type="number" min="0" class="widefat" name="logestay_max_nights" value="<?php echo esc_attr( $max_nights ); ?>">
		</div>
		<div></div>
	</div>

	<hr>

	<h4 style="margin:0 0 10px;"><?php esc_html_e( 'Check-in & Check-out', 'logestay' ); ?></h4>

	<div class="logestay-grid-3">
		<div class="logestay-field">
			<label><?php esc_html_e( 'Check-in time', 'logestay' ); ?></label>
			<input type="time" class="widefat" name="logestay_checkin_time" value="<?php echo esc_attr( $checkin_time ); ?>" placeholder="15:00">
			<p class="logestay-help"><?php esc_html_e( 'If empty, default will be 15:00.', 'logestay' ); ?></p>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Check-out time', 'logestay' ); ?></label>
			<input type="time" class="widefat" name="logestay_checkout_time" value="<?php echo esc_attr( $checkout_time ); ?>" placeholder="11:00">
			<p class="logestay-help"><?php esc_html_e( 'If empty, default will be 11:00.', 'logestay' ); ?></p>
		</div>

		<div></div>
	</div>

	<hr>

	<h4 style="margin:0 0 10px;"><?php esc_html_e( 'Pricing', 'logestay' ); ?></h4>
	<div class="logestay-grid">
		<div class="logestay-field">
			<label><?php esc_html_e( 'Price / Night', 'logestay' ); ?></label>
			<input type="number" step="0.01" min="0" class="widefat" name="logestay_price_per_night" value="<?php echo esc_attr( $price ); ?>">
		</div>
		<div class="logestay-field">
			<label><?php esc_html_e( 'Cleaning Fee', 'logestay' ); ?></label>
			<input type="number" step="0.01" min="0" class="widefat" name="logestay_cleaning_fee" value="<?php echo esc_attr( $cleaning ); ?>">
		</div>
		<!--
		<div class="logestay-field">
			<label><?php esc_html_e( 'Currency', 'logestay' ); ?></label>
			<input type="text" class="widefat" name="logestay_currency" value="<?php echo esc_attr( $currency ); ?>" placeholder="EUR">
		</div> -->
	</div>

	<hr>

	<h4 style="margin:0 0 10px;"><?php esc_html_e( 'Sync (iCal)', 'logestay' ); ?></h4>
	<div class="logestay-grid-3">
		<div class="logestay-field" style="grid-column: span 2;">
			<label><?php esc_html_e( 'Airbnb iCal URL', 'logestay' ); ?></label>
			<input type="url" class="widefat" name="logestay_ical_airbnb_url" value="<?php echo esc_attr( $ical_airbnb ); ?>">
		</div>
		<div class="logestay-field">
			<label><?php esc_html_e( 'Booking iCal URL', 'logestay' ); ?></label>
			<input type="url" class="widefat" name="logestay_ical_booking_url" value="<?php echo esc_attr( $ical_booking ); ?>">
		</div>
	</div>

	<hr>

	<h4 style="margin:0 0 10px;"><?php esc_html_e( 'External Links', 'logestay' ); ?></h4>
	<div class="logestay-grid-3">
		<div class="logestay-field">
			<label><?php esc_html_e( 'Airbnb Listing Link', 'logestay' ); ?></label>
			<input type="url" class="widefat" name="logestay_airbnb_link" value="<?php echo esc_attr( $airbnb_link ); ?>">
		</div>
		<div class="logestay-field" style="grid-column: span 2;">
			<label><?php esc_html_e( 'Booking Listing Link', 'logestay' ); ?></label>
			<input type="url" class="widefat" name="logestay_booking_link" value="<?php echo esc_attr( $booking_link ); ?>">
		</div>
	</div>

	<hr>

	<h4 style="margin:0 0 10px;"><?php esc_html_e( 'Gallery', 'logestay' ); ?></h4>

	<input type="hidden" id="logestay_listing_gallery" name="logestay_listing_gallery" value="<?php echo esc_attr( $gallery_csv ); ?>">

	<div class="logestay-gallery-actions">
		<button type="button" class="button button-primary" id="logestay-gallery-add">
			<?php esc_html_e( 'Add / Manage Images', 'logestay' ); ?>
		</button>
		<button type="button" class="button" id="logestay-gallery-clear">
			<?php esc_html_e( 'Clear', 'logestay' ); ?>
		</button>
		<span class="logestay-help"><?php esc_html_e( 'Drag to reorder. Click × to remove.', 'logestay' ); ?></span>
	</div>

	<div id="logestay-gallery-wrap" class="logestay-gallery" data-placeholder="<?php echo esc_url( $placeholder ); ?>">
		<!-- JS renders thumbnails -->
	</div>


	<hr>

	<h4 style="margin:0 0 10px;"><?php esc_html_e( 'Amenities', 'logestay' ); ?></h4>

	<input type="hidden" id="logestay_listing_amenities" name="logestay_listing_amenities"
		value="<?php echo esc_attr( wp_json_encode( array_values( $amenities ) ) ); ?>">

	<script>
	window.LOGESTAY_AMENITY_PRESETS = <?php echo wp_json_encode( $amenity_presets ); ?>;
	//window.LOGESTAY_LUCIDE_ICONS = <?php echo wp_json_encode( $lucide_icons ); ?>;
	</script>

	<div class="logestay-amenity-actions">
		<div class="loge_flex">
			<select id="logestay-amenity-preset" class="regular-text">
				<option value=""><?php esc_html_e( '— Select from presets —', 'logestay' ); ?></option>
				<?php foreach ( $amenity_presets as $p ) : ?>
					<option value="<?php echo esc_attr( $p['key'] ); ?>">
						<?php echo esc_html( $p['label'] . ' (' . $p['icon'] . ')' ); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<button type="button" class="button" id="logestay-amenity-add-preset">
				<?php esc_html_e( 'Add preset', 'logestay' ); ?>
			</button>

		</div>

		<div class="loge_flex">
			<input type="text" id="logestay-amenity-custom-label" class="regular-text" placeholder="<?php echo esc_attr__( 'Custom label (e.g. WC séparés)', 'logestay' ); ?>">

			<input type="hidden" id="logestay-amenity-custom-icon" value="check">
				<button type="button" class="logestay-icon-btn logestay-open-icon-picker" data-target="#logestay-amenity-custom-icon">
			  	<span class="icon" data-icon="check"></span>
			  	<span class="name">check</span>
			</button>

			<button type="button" class="button button-primary" id="logestay-amenity-add-custom">
				<?php esc_html_e( 'Add custom', 'logestay' ); ?>
			</button>

			
		</div>
	</div>
	<span class="logestay-help"><?php esc_html_e( 'Drag to reorder. You can mix presets and custom items.', 'logestay' ); ?></span>

	<div id="logestay-amenities-wrap" class="logestay-amenities">
		<!-- JS renders items -->
	</div>

	<?php
}

add_action( 'save_post_logestay_listing', function ( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['logestay_listing_meta_nonce'] ) || ! wp_verify_nonce( $_POST['logestay_listing_meta_nonce'], 'logestay_listing_meta_save' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	update_post_meta( $post_id, 'logestay_city_id', absint( $_POST['logestay_city_id'] ?? 0 ) );

	update_post_meta( $post_id, 'logestay_max_adults', absint( $_POST['logestay_max_adults'] ?? 0 ) );
	update_post_meta( $post_id, 'logestay_max_children', absint( $_POST['logestay_max_children'] ?? 0 ) );
	update_post_meta( $post_id, 'logestay_max_pets', absint( $_POST['logestay_max_pets'] ?? 0 ) );
	update_post_meta( $post_id, 'logestay_min_nights', absint( $_POST['logestay_min_nights'] ?? 0 ) );
	update_post_meta( $post_id, 'logestay_max_nights', absint( $_POST['logestay_max_nights'] ?? 0 ) );

	$price = $_POST['logestay_price_per_night'] ?? '';
	update_post_meta( $post_id, 'logestay_price_per_night', is_numeric( $price ) ? (float) $price : 0 );

	foreach ( [ 'logestay_cleaning_fee', 'logestay_security_deposit' ] as $k ) {
		$v = $_POST[ $k ] ?? '';
		update_post_meta( $post_id, $k, is_numeric( $v ) ? (float) $v : 0 );
	}

	update_post_meta( $post_id, 'logestay_currency', sanitize_text_field( $_POST['logestay_currency'] ?? '' ) );

	update_post_meta( $post_id, 'logestay_ical_airbnb_url', esc_url_raw( $_POST['logestay_ical_airbnb_url'] ?? '' ) );
	update_post_meta( $post_id, 'logestay_ical_booking_url', esc_url_raw( $_POST['logestay_ical_booking_url'] ?? '' ) );

	update_post_meta( $post_id, 'logestay_airbnb_link', esc_url_raw( $_POST['logestay_airbnb_link'] ?? '' ) );
	update_post_meta( $post_id, 'logestay_booking_link', esc_url_raw( $_POST['logestay_booking_link'] ?? '' ) );

	// Gallery CSV -> array of ints
	$csv = sanitize_text_field( $_POST['logestay_listing_gallery'] ?? '' );
	$ids = array_filter( array_map( 'absint', array_map( 'trim', explode( ',', $csv ) ) ) );
	update_post_meta( $post_id, 'logestay_listing_gallery', array_values( $ids ) );


	// Amenities JSON -> array
	$raw = wp_unslash( $_POST['logestay_listing_amenities'] ?? '' );
	$amenities = json_decode( $raw, true );

	$clean = [];

	if ( is_array( $amenities ) ) {
		foreach ( $amenities as $a ) {
			if ( ! is_array( $a ) ) continue;

			$label = sanitize_text_field( $a['label'] ?? '' );
			$icon  = sanitize_text_field( $a['icon'] ?? '' );

			if ( $label === '' ) continue;

			$clean[] = [
				'label' => $label,
				'icon'  => $icon ?: 'check',
			];
		}
	}

	update_post_meta( $post_id, 'logestay_listing_amenities', $clean );

	$checkin_time  = sanitize_text_field( $_POST['logestay_checkin_time'] ?? '' );
	$checkout_time = sanitize_text_field( $_POST['logestay_checkout_time'] ?? '' );

	// very light validation: keep only HH:MM
	$checkin_time  = preg_match( '/^\d{2}:\d{2}$/', $checkin_time ) ? $checkin_time : '';
	$checkout_time = preg_match( '/^\d{2}:\d{2}$/', $checkout_time ) ? $checkout_time : '';

	update_post_meta( $post_id, 'logestay_checkin_time', $checkin_time );
	update_post_meta( $post_id, 'logestay_checkout_time', $checkout_time );

} );
