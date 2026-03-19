<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Logestay_Homepage_Section_Cta {

	public static function render( int $post_id ) {
		$title        = (string) get_post_meta( $post_id, '_logestay_listing_cta_title', true );
		$subtitle     = (string) get_post_meta( $post_id, '_logestay_listing_cta_subtitle', true );
		$btn     = (string) get_post_meta( $post_id, '_logestay_listing_cta_btn', true );
		$airbnb     = (string) get_post_meta( $post_id, '_logestay_listing_cta_hide_airbnb', true );
		$booking     = (string) get_post_meta( $post_id, '_logestay_listing_cta_hide_booking', true );

		$airbnb_url     = (string) get_post_meta( $post_id, '_logestay_listing_cta_airbnb_url', true );
		$booking_url     = (string) get_post_meta( $post_id, '_logestay_listing_cta_booking_url', true );
		?>

		<div class="logestay-field">
			<label for="logestay_listing_cta_title"><?php echo esc_html__( 'Title', 'logestay' ); ?></label>
			<input type="text" id="logestay_listing_cta_title" name="logestay_listing_cta_title" value="<?php echo esc_attr( $title ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_listing_cta_subtitle"><?php echo esc_html__( 'Subtitle', 'logestay' ); ?></label>
			<textarea id="logestay_listing_cta_subtitle" name="logestay_listing_cta_subtitle" rows="3"><?php echo esc_textarea( $subtitle ); ?></textarea>
		</div>

		<div class="logestay-field logestay-field-checkbox">
			<input type="checkbox" id="logestay_listing_cta_btn" name="logestay_listing_cta_btn" <?php if($btn){echo 'checked';} ?> >
			<label for="logestay_listing_cta_btn"><?php echo esc_html__( 'Hide Book Button', 'logestay' ); ?></label>
		</div>

		<div class="logestay-field logestay-field-checkbox">
			<input type="checkbox" id="logestay_listing_cta_hide_airbnb" name="logestay_listing_cta_hide_airbnb" <?php if($airbnb){echo 'checked';} ?> >
			<label for="logestay_listing_cta_hide_airbnb"><?php echo esc_html__( 'Hide Airbnb Button', 'logestay' ); ?></label>
		</div>

		<div class="logestay-field logestay-field-checkbox">
			<input type="checkbox" id="logestay_listing_cta_hide_booking" name="logestay_listing_cta_hide_booking" <?php if($booking){echo 'checked';} ?> >
			<label for="logestay_listing_cta_hide_booking"><?php echo esc_html__( 'Hide Booking.com Button', 'logestay' ); ?></label>
		</div>

		<div class="logestay-field">
			<label for="logestay_listing_cta_airbnb_url"><?php echo esc_html__( 'Airbnb URL', 'logestay' ); ?></label>
			<input type="text" id="logestay_listing_cta_airbnb_url" name="logestay_listing_cta_airbnb_url" value="<?php echo esc_attr( $airbnb_url ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_listing_cta_booking_url"><?php echo esc_html__( 'Booking.com URL', 'logestay' ); ?></label>
			<input type="text" id="logestay_listing_cta_booking_url" name="logestay_listing_cta_booking_url" value="<?php echo esc_attr( $booking_url ); ?>">
		</div>


		<?php
	}

	public static function save( int $post_id, array $data ) {
		$title        = isset( $data['logestay_listing_cta_title'] ) ? sanitize_text_field( wp_unslash( $data['logestay_listing_cta_title'] ) ) : '';
		$subtitle     = isset( $data['logestay_listing_cta_subtitle'] ) ? sanitize_textarea_field( wp_unslash( $data['logestay_listing_cta_subtitle'] ) ) : '';
		$btn     = isset( $data['logestay_listing_cta_btn'] ) ? sanitize_textarea_field( wp_unslash( $data['logestay_listing_cta_btn'] ) ) : '';
		$airbnb     = isset( $data['logestay_listing_cta_hide_airbnb'] ) ? sanitize_textarea_field( wp_unslash( $data['logestay_listing_cta_hide_airbnb'] ) ) : '';
		$booking     = isset( $data['logestay_listing_cta_hide_booking'] ) ? sanitize_textarea_field( wp_unslash( $data['logestay_listing_cta_hide_booking'] ) ) : '';
		$airbnb_url        = isset( $data['logestay_listing_cta_airbnb_url'] ) ? sanitize_text_field( wp_unslash( $data['logestay_listing_cta_airbnb_url'] ) ) : '';
		$booking_url        = isset( $data['logestay_listing_cta_booking_url'] ) ? sanitize_text_field( wp_unslash( $data['logestay_listing_cta_booking_url'] ) ) : '';

		update_post_meta( $post_id, '_logestay_listing_cta_title', $title );
		update_post_meta( $post_id, '_logestay_listing_cta_subtitle', $subtitle );
		update_post_meta( $post_id, '_logestay_listing_cta_btn', $btn );
		update_post_meta( $post_id, '_logestay_listing_cta_hide_airbnb', $airbnb );
		update_post_meta( $post_id, '_logestay_listing_cta_hide_booking', $booking );
		update_post_meta( $post_id, '_logestay_listing_cta_airbnb_url', $airbnb_url );
		update_post_meta( $post_id, '_logestay_listing_cta_booking_url', $booking_url );
	}
}
