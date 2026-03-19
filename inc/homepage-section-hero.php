<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Logestay_Homepage_Section_Hero {

	public static function render( int $post_id ) {
		// Text fields
		$title    = (string) get_post_meta( $post_id, '_logestay_hero_title', true );
		$subtitle = (string) get_post_meta( $post_id, '_logestay_hero_subtitle', true );
		$rating   = (string) get_post_meta( $post_id, '_logestay_hero_rating', true );
		$review   = (string) get_post_meta( $post_id, '_logestay_hero_review', true );
		$btn_text = (string) get_post_meta( $post_id, '_logestay_hero_btn_text', true );
		$btn_link = (string) get_post_meta( $post_id, '_logestay_hero_btn_link', true );

		// Slides (ordered CSV IDs)
		$gallery_ids_csv = (string) get_post_meta( $post_id, '_logestay_hero_gallery_ids', true );
		$ids = array_filter( array_map( 'absint', explode( ',', $gallery_ids_csv ) ) );
		?>

		<h3 style="margin-top:0;"><?php echo esc_html__( 'Hero Content', 'logestay' ); ?></h3>

		<div class="logestay-field">
			<label for="logestay_hero_title"><?php echo esc_html__( 'Hero Title', 'logestay' ); ?></label>
			<input type="text" id="logestay_hero_title" name="logestay_hero_title" value="<?php echo esc_attr( $title ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_hero_subtitle"><?php echo esc_html__( 'Hero Subtitle', 'logestay' ); ?></label>
			<textarea id="logestay_hero_subtitle" name="logestay_hero_subtitle" rows="3"><?php echo esc_textarea( $subtitle ); ?></textarea>
		</div>

		<div class="logestay-field">
			<label for="logestay_hero_rating"><?php echo esc_html__( 'Rating (example: 4.9)', 'logestay' ); ?></label>
			<input type="text" id="logestay_hero_rating" name="logestay_hero_rating" value="<?php echo esc_attr( $rating ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_hero_review"><?php echo esc_html__( 'Review Text / Count', 'logestay' ); ?></label>
			<input type="text" id="logestay_hero_review" name="logestay_hero_review" value="<?php echo esc_attr( $review ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_hero_btn_text"><?php echo esc_html__( 'Button Text', 'logestay' ); ?></label>
			<input type="text" id="logestay_hero_btn_text" name="logestay_hero_btn_text" value="<?php echo esc_attr( $btn_text ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_hero_btn_link"><?php echo esc_html__( 'Button Link', 'logestay' ); ?></label>
			<input type="text" id="logestay_hero_btn_link" name="logestay_hero_btn_link" value="<?php echo esc_attr( $btn_link ); ?>" placeholder="https://example.com">
		</div>

		<hr style="margin:18px 0;">

		<h3 style="margin:0 0 10px;"><?php echo esc_html__( 'Hero Slides', 'logestay' ); ?></h3>
		<p style="margin-top:0;"><?php echo esc_html__( 'Add unlimited images. Drag & drop to reorder.', 'logestay' ); ?></p>

		<div class="logestay-gallery">
			<input class="logestay-gallery__ids" type="hidden" name="logestay_hero_gallery_ids" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">

			<button type="button" class="button button-primary logestay-gallery__add">
				<?php echo esc_html__( 'Add / Select Images', 'logestay' ); ?>
			</button>

			<ul class="logestay-gallery__list">
				<?php foreach ( $ids as $id ) : ?>
					<?php
					$thumb = wp_get_attachment_image_url( $id, 'thumbnail' );
					if ( ! $thumb ) continue;
					?>
					<li class="logestay-gallery__item" data-id="<?php echo esc_attr( $id ); ?>">
						<div class="logestay-gallery__thumb">
							<img src="<?php echo esc_url( $thumb ); ?>" alt="">
						</div>
						<div class="logestay-gallery__actions">
							<button type="button" class="button link-button logestay-gallery__remove">
								<?php echo esc_html__( 'Remove', 'logestay' ); ?>
							</button>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<?php
	}

	public static function save( int $post_id, array $data ) {
		// Slides
		$ids_csv = isset( $data['logestay_hero_gallery_ids'] ) ? (string) wp_unslash( $data['logestay_hero_gallery_ids'] ) : '';
		$ids     = array_filter( array_map( 'absint', explode( ',', $ids_csv ) ) );
		update_post_meta( $post_id, '_logestay_hero_gallery_ids', implode( ',', $ids ) );

		// Text fields
		$title    = isset( $data['logestay_hero_title'] ) ? sanitize_text_field( wp_unslash( $data['logestay_hero_title'] ) ) : '';
		$subtitle = isset( $data['logestay_hero_subtitle'] ) ? sanitize_textarea_field( wp_unslash( $data['logestay_hero_subtitle'] ) ) : '';
		$rating   = isset( $data['logestay_hero_rating'] ) ? sanitize_text_field( wp_unslash( $data['logestay_hero_rating'] ) ) : '';
		$review   = isset( $data['logestay_hero_review'] ) ? sanitize_text_field( wp_unslash( $data['logestay_hero_review'] ) ) : '';
		$btn_text = isset( $data['logestay_hero_btn_text'] ) ? sanitize_text_field( wp_unslash( $data['logestay_hero_btn_text'] ) ) : '';
		$btn_link = isset( $data['logestay_hero_btn_link'] ) ? esc_url_raw( wp_unslash( $data['logestay_hero_btn_link'] ) ) : '';

		update_post_meta( $post_id, '_logestay_hero_title', $title );
		update_post_meta( $post_id, '_logestay_hero_subtitle', $subtitle );
		update_post_meta( $post_id, '_logestay_hero_rating', $rating );
		update_post_meta( $post_id, '_logestay_hero_review', $review );
		update_post_meta( $post_id, '_logestay_hero_btn_text', $btn_text );
		update_post_meta( $post_id, '_logestay_hero_btn_link', $btn_link );
	}
}
