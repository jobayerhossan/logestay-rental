<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Logestay_Homepage_Section_Reviews {

	public static function render( int $post_id ) {
		$title        = (string) get_post_meta( $post_id, '_logestay_review_title', true );
		$rating     = (string) get_post_meta( $post_id, '_logestay_listing_rating', true );
		$reviews     = (string) get_post_meta( $post_id, '_logestay_listing_reviews', true );
		?>

		<div class="logestay-field">
			<label for="logestay_review_title"><?php echo esc_html__( 'Title', 'logestay' ); ?></label>
			<input type="text" id="logestay_review_title" name="logestay_review_title" value="<?php echo esc_attr( $title ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_listing_rating"><?php echo esc_html__( 'Rating', 'logestay' ); ?></label>
			<input type="text" id="logestay_listing_rating" name="logestay_listing_rating" value="<?php echo esc_attr( $rating ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_listing_reviews"><?php echo esc_html__( 'Reviews', 'logestay' ); ?></label>
			<input type="text" id="logestay_listing_reviews" name="logestay_listing_reviws" value="<?php echo esc_attr( $reviews ); ?>">
		</div>



		<?php
	}

	public static function save( int $post_id, array $data ) {
		$title        = isset( $data['logestay_review_title'] ) ? sanitize_text_field( wp_unslash( $data['logestay_review_title'] ) ) : '';
		$rating        = isset( $data['logestay_listing_rating'] ) ? sanitize_text_field( wp_unslash( $data['logestay_listing_rating'] ) ) : '';
		$reviews        = isset( $data['logestay_listing_reviws'] ) ? sanitize_text_field( wp_unslash( $data['logestay_listing_reviws'] ) ) : '';

		update_post_meta( $post_id, '_logestay_review_title', $title );
		update_post_meta( $post_id, '_logestay_listing_rating', $rating );
		update_post_meta( $post_id, '_logestay_listing_reviews', $reviews );
	}
}
