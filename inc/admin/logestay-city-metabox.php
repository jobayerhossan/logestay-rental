<?php
/**
 * City Metabox UI (Map + Nearby)
 *
 * @package logestay
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ---------------------------------------------------------
 * Register Meta Box
 * --------------------------------------------------------- */
add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'logestay_city_meta',
		__( 'City Location & Nearby', 'logestay' ),
		'logestay_render_city_meta_box',
		'logestay_city',
		'normal',
		'high'
	);
} );

/* ---------------------------------------------------------
 * Render Meta Box
 * --------------------------------------------------------- */
function logestay_render_city_meta_box( WP_Post $post ) {
	wp_nonce_field( 'logestay_city_meta_save', 'logestay_city_meta_nonce' );

	$map_embed = get_post_meta( $post->ID, 'logestay_city_map_embed', true );
	$map_open  = get_post_meta( $post->ID, 'logestay_city_map_open', true );

	$nearby = get_post_meta( $post->ID, 'logestay_city_nearby', true );
	$nearby = is_array( $nearby ) ? $nearby : [];

	wp_enqueue_media();
	?>
	<style>
		.logestay-field label { font-weight:600; display:block; margin-bottom:6px; }
		.logestay-nearby-item {
			border:1px solid #ddd;
			padding:12px;
			border-radius:6px;
			margin-bottom:10px;
			background:#fafafa;
		}
		.logestay-nearby-grid {
			display:grid;
			grid-template-columns: 120px 1fr;
			gap:12px;
		}
		.logestay-thumb {
			width:100%;
			height:80px;
			background:#eee;
			display:flex;
			align-items:center;
			justify-content:center;
			cursor:pointer;
			border-radius:4px;
			overflow:hidden;
		}
		.logestay-thumb img {
			width:100%;
			height:100%;
			object-fit:cover;
		}
		.logestay-remove {
			color:#b32d2e;
			cursor:pointer;
			font-weight:600;
			margin-top:6px;
			display:inline-block;
		}
	</style>

	<!-- MAP SETTINGS -->
	<div class="logestay-field">
		<label><?php esc_html_e( 'Google Map Embed URL (iframe src)', 'logestay' ); ?></label>
		<input type="text" class="widefat"
			name="logestay_city_map_embed"
			placeholder="https://www.google.com/maps/embed?pb=..."
			value="<?php echo esc_attr( $map_embed ); ?>">
	</div>

	<div class="logestay-field" style="margin-top:12px;">
		<label><?php esc_html_e( 'Google Map Open Link', 'logestay' ); ?></label>
		<input type="text" class="widefat"
			name="logestay_city_map_open"
			placeholder="https://www.google.com/maps/place/..."
			value="<?php echo esc_attr( $map_open ); ?>">
	</div>

	<hr>

	<!-- NEARBY REPEATER -->
	<h3><?php esc_html_e( 'Nearby Places', 'logestay' ); ?></h3>

	<div id="logestay-nearby-wrap">
		<?php foreach ( $nearby as $i => $item ) :
			$image_id = absint( $item['image_id'] ?? 0 );
			$title    = esc_attr( $item['title'] ?? '' );
			$distance = esc_attr( $item['distance'] ?? '' );
			$url      = esc_url( $item['url'] ?? '' );
			$img      = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
		?>
		<div class="logestay-nearby-item">
			<div class="logestay-nearby-grid">
				<div>
					<div class="logestay-thumb js-image-pick">
						<?php if ( $img ) : ?>
							<img src="<?php echo esc_url( $img ); ?>">
						<?php else : ?>
							<span><?php esc_html_e( 'Select Image', 'logestay' ); ?></span>
						<?php endif; ?>
					</div>
					<input type="hidden" name="logestay_city_nearby[<?php echo $i; ?>][image_id]" value="<?php echo $image_id; ?>">
				</div>

				<div>
					<input class="widefat" placeholder="Title"
						name="logestay_city_nearby[<?php echo $i; ?>][title]"
						value="<?php echo $title; ?>">

					<input class="widefat" style="margin-top:6px;" placeholder="Distance (e.g. 1.2 km)"
						name="logestay_city_nearby[<?php echo $i; ?>][distance]"
						value="<?php echo $distance; ?>">

					<input class="widefat" style="margin-top:6px;" placeholder="Google Maps URL"
						name="logestay_city_nearby[<?php echo $i; ?>][url]"
						value="<?php echo $url; ?>">

					<span class="logestay-remove js-remove"><?php esc_html_e( 'Remove', 'logestay' ); ?></span>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>

	<button type="button" class="button button-secondary" id="logestay-add-nearby">
		<?php esc_html_e( 'Add Nearby Place', 'logestay' ); ?>
	</button>

	<script>
	jQuery(function($){
		let i = <?php echo count( $nearby ); ?>;

		$('#logestay-add-nearby').on('click', function(){
			const tpl = `
			<div class="logestay-nearby-item">
				<div class="logestay-nearby-grid">
					<div>
						<div class="logestay-thumb js-image-pick">
							<span>Select Image</span>
						</div>
						<input type="hidden" name="logestay_city_nearby[${i}][image_id]">
					</div>
					<div>
						<input class="widefat" placeholder="Title"
							name="logestay_city_nearby[${i}][title]">

						<input class="widefat" style="margin-top:6px;" placeholder="Distance"
							name="logestay_city_nearby[${i}][distance]">

						<input class="widefat" style="margin-top:6px;" placeholder="Google Maps URL"
							name="logestay_city_nearby[${i}][url]">

						<span class="logestay-remove js-remove">Remove</span>
					</div>
				</div>
			</div>`;
			$('#logestay-nearby-wrap').append(tpl);
			i++;
		});

		$(document).on('click', '.js-remove', function(){
			$(this).closest('.logestay-nearby-item').remove();
		});

		$(document).on('click', '.js-image-pick', function(){
			const wrap = $(this);
			const input = wrap.next('input');

			const frame = wp.media({
				title: 'Select Image',
				button: { text: 'Use image' },
				multiple: false
			});

			frame.on('select', function(){
				const attachment = frame.state().get('selection').first().toJSON();
				input.val(attachment.id);
				wrap.html('<img src="'+attachment.sizes.thumbnail.url+'">');
			});

			frame.open();
		});
	});
	</script>
	<?php
}

/* ---------------------------------------------------------
 * Save Meta
 * --------------------------------------------------------- */
add_action( 'save_post_logestay_city', function ( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['logestay_city_meta_nonce'] ) ) return;
	if ( ! wp_verify_nonce( $_POST['logestay_city_meta_nonce'], 'logestay_city_meta_save' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	update_post_meta(
		$post_id,
		'logestay_city_map_embed',
		esc_url_raw( $_POST['logestay_city_map_embed'] ?? '' )
	);

	update_post_meta(
		$post_id,
		'logestay_city_map_open',
		esc_url_raw( $_POST['logestay_city_map_open'] ?? '' )
	);

	$nearby = $_POST['logestay_city_nearby'] ?? [];
	$clean  = [];

	if ( is_array( $nearby ) ) {
		foreach ( $nearby as $n ) {
			if ( empty( $n['title'] ) ) continue;

			$clean[] = [
				'title'    => sanitize_text_field( $n['title'] ),
				'distance' => sanitize_text_field( $n['distance'] ?? '' ),
				'url'      => esc_url_raw( $n['url'] ?? '' ),
				'image_id' => absint( $n['image_id'] ?? 0 ),
			];
		}
	}

	update_post_meta( $post_id, 'logestay_city_nearby', $clean );
} );