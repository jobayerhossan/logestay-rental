<?php
/**
 * Review Metabox UI
 *
 * @package logestay
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register metabox
 */
add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'logestay_review_meta',
		__( 'Review Details', 'logestay' ),
		'logestay_render_review_meta_box',
		'logestay_review',
		'normal',
		'high'
	);
} );

/**
 * Render metabox
 */
function logestay_render_review_meta_box( WP_Post $post ) {
	wp_nonce_field( 'logestay_review_meta_save', 'logestay_review_meta_nonce' );

	$rating     = get_post_meta( $post->ID, 'logestay_review_rating', true );
	$date       = get_post_meta( $post->ID, 'logestay_review_date', true );

	
	?>

	<style>
		.logestay-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
		.logestay-field label{font-weight:600;display:block;margin-bottom:6px}
		.logestay-help{font-size:12px;color:#646970;margin-top:4px}
	</style>

	<div class="logestay-grid">
		<div class="logestay-field">
			<label><?php esc_html_e( 'Rating (0–5)', 'logestay' ); ?></label>
			<input type="number" step="0.1" min="0" max="5" class="widefat"
				name="logestay_review_rating"
				value="<?php echo esc_attr( $rating ); ?>">
		</div>
		<div class="logestay-field">
			<label><?php esc_html_e( 'Review Date', 'logestay' ); ?></label>
			<input type="text" class="widefat"
				name="logestay_review_date"
				value="<?php echo esc_attr( $date ); ?>">
		</div>


	</div>


	<?php
}

/**
 * Save handler
 */
add_action( 'save_post_logestay_review', function ( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['logestay_review_meta_nonce'] ) ||
	     ! wp_verify_nonce( $_POST['logestay_review_meta_nonce'], 'logestay_review_meta_save' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	update_post_meta( $post_id, 'logestay_review_rating',
		min( 5, max( 0, (float) ( $_POST['logestay_review_rating'] ?? 0 ) ) )
	);



	update_post_meta( $post_id, 'logestay_review_date',
		sanitize_text_field( $_POST['logestay_review_date'] ?? '' )
	);

	


} );