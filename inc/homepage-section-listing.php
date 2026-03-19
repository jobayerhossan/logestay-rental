<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Logestay_Homepage_Section_Listing {

	public static function render( int $post_id ) {
		$title        = (string) get_post_meta( $post_id, '_logestay_listing_title', true );
		$subtitle     = (string) get_post_meta( $post_id, '_logestay_listing_subtitle', true );
		$notfound_title     = (string) get_post_meta( $post_id, '_logestay_listing_notfound_title', true );
		$notfound_subtitle     = (string) get_post_meta( $post_id, '_logestay_listing_notfound_subtitle', true );
		?>

		<div class="logestay-field">
			<label for="logestay_listing_title"><?php echo esc_html__( 'Title', 'logestay' ); ?></label>
			<input type="text" id="logestay_listing_title" name="logestay_listing_title" value="<?php echo esc_attr( $title ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_listing_subtitle"><?php echo esc_html__( 'Subtitle', 'logestay' ); ?></label>
			<textarea id="logestay_listing_subtitle" name="logestay_listing_subtitle" rows="3"><?php echo esc_textarea( $subtitle ); ?></textarea>
		</div>

		<div class="logestay-field">
			<label for="logestay_listing_notfound_title"><?php echo esc_html__( 'Not Found Title', 'logestay' ); ?></label>
			<input type="text" id="logestay_listing_notfound_title" name="logestay_listing_notfound_title" value="<?php echo esc_attr( $notfound_title ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_listing_notfound_subtitle"><?php echo esc_html__( 'Not Found SubTitle', 'logestay' ); ?></label>
			<input type="text" id="logestay_listing_notfound_subtitle" name="logestay_listing_notfound_subtitle" value="<?php echo esc_attr( $notfound_subtitle ); ?>">
		</div>


		<?php
	}

	public static function save( int $post_id, array $data ) {
		$title        = isset( $data['logestay_listing_title'] ) ? sanitize_text_field( wp_unslash( $data['logestay_listing_title'] ) ) : '';
		$subtitle     = isset( $data['logestay_listing_subtitle'] ) ? sanitize_textarea_field( wp_unslash( $data['logestay_listing_subtitle'] ) ) : '';
		$subtitle     = isset( $data['logestay_listing_subtitle'] ) ? sanitize_textarea_field( wp_unslash( $data['logestay_listing_subtitle'] ) ) : '';
		$nottitle     = isset( $data['logestay_listing_notfound_title'] ) ? sanitize_textarea_field( wp_unslash( $data['logestay_listing_notfound_title'] ) ) : '';
		$notsubtitle     = isset( $data['logestay_listing_notfound_subtitle'] ) ? sanitize_textarea_field( wp_unslash( $data['logestay_listing_notfound_subtitle'] ) ) : '';

		update_post_meta( $post_id, '_logestay_listing_title', $title );
		update_post_meta( $post_id, '_logestay_listing_subtitle', $subtitle );
		update_post_meta( $post_id, '_logestay_listing_notfound_title', $nottitle );
		update_post_meta( $post_id, '_logestay_listing_notfound_subtitle', $notsubtitle );
	}
}
