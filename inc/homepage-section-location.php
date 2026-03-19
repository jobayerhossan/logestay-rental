<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Logestay_Homepage_Section_Location {

	public static function render( int $post_id ) {
		$title        = (string) get_post_meta( $post_id, '_logestay_listing_location_title', true );
		$subtitle     = (string) get_post_meta( $post_id, '_logestay_listing_location_subtitle', true );
		?>

		<div class="logestay-field">
			<label for="logestay_listing_location_title"><?php echo esc_html__( 'Title', 'logestay' ); ?></label>
			<input type="text" id="logestay_listing_location_title" name="logestay_listing_location_title" value="<?php echo esc_attr( $title ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_listing_location_subtitle"><?php echo esc_html__( 'Subtitle', 'logestay' ); ?></label>
			<textarea id="logestay_listing_location_subtitle" name="logestay_listing_location_subtitle" rows="3"><?php echo esc_textarea( $subtitle ); ?></textarea>
		</div>


		<?php
	}

	public static function save( int $post_id, array $data ) {
		$title        = isset( $data['logestay_listing_location_title'] ) ? sanitize_text_field( wp_unslash( $data['logestay_listing_location_title'] ) ) : '';
		$subtitle     = isset( $data['logestay_listing_location_subtitle'] ) ? sanitize_textarea_field( wp_unslash( $data['logestay_listing_location_subtitle'] ) ) : '';

		update_post_meta( $post_id, '_logestay_listing_location_title', $title );
		update_post_meta( $post_id, '_logestay_listing_location_subtitle', $subtitle );
	}
}
