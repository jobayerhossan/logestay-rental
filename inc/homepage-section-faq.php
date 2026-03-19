<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Logestay_Homepage_Section_Faq {

	public static function render( int $post_id ) {
		$title        = (string) get_post_meta( $post_id, '_logestay_listing_faq_title', true );
		$subtitle     = (string) get_post_meta( $post_id, '_logestay_listing_faq_subtitle', true );
		?>

		<div class="logestay-field">
			<label for="logestay_listing_faq_title"><?php echo esc_html__( 'Title', 'logestay' ); ?></label>
			<input type="text" id="logestay_listing_faq_title" name="logestay_listing_faq_title" value="<?php echo esc_attr( $title ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_listing_faq_subtitle"><?php echo esc_html__( 'Subtitle', 'logestay' ); ?></label>
			<textarea id="logestay_listing_faq_subtitle" name="logestay_listing_faq_subtitle" rows="3"><?php echo esc_textarea( $subtitle ); ?></textarea>
		</div>


		<?php
	}

	public static function save( int $post_id, array $data ) {
		$title        = isset( $data['logestay_listing_faq_title'] ) ? sanitize_text_field( wp_unslash( $data['logestay_listing_faq_title'] ) ) : '';
		$subtitle     = isset( $data['logestay_listing_faq_subtitle'] ) ? sanitize_textarea_field( wp_unslash( $data['logestay_listing_faq_subtitle'] ) ) : '';

		update_post_meta( $post_id, '_logestay_listing_faq_title', $title );
		update_post_meta( $post_id, '_logestay_listing_faq_subtitle', $subtitle );
	}
}
