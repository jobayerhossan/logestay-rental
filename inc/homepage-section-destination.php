<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Logestay_Homepage_Section_Destination {

	public static function render( int $post_id ) {
		$title        = (string) get_post_meta( $post_id, '_logestay_destination_title', true );
		$subtitle     = (string) get_post_meta( $post_id, '_logestay_destination_subtitle', true );
		$destinations = get_post_meta( $post_id, '_logestay_destination_select', true );
		$destinations = is_array($destinations) ? array_map('absint', $destinations) : [];
		?>

		<div class="logestay-field">
			<label for="logestay_destination_title"><?php echo esc_html__( 'Title', 'logestay' ); ?></label>
			<input type="text" id="logestay_destination_title" name="logestay_destination_title" value="<?php echo esc_attr( $title ); ?>">
		</div>

		<div class="logestay-field">
			<label for="logestay_destination_subtitle"><?php echo esc_html__( 'Subtitle', 'logestay' ); ?></label>
			<textarea id="logestay_destination_subtitle" name="logestay_destination_subtitle" rows="3"><?php echo esc_textarea( $subtitle ); ?></textarea>
		</div>

		<div class="logestay-field">
		  <label for="logestay_destination_select"><?php echo esc_html__( 'Select Destinations', 'logestay' ); ?></label>

		  <?php
		  $cities = get_posts([
		    'post_type'      => 'logestay_city',
		    'posts_per_page' => -1,
		    'post_status'    => ['publish','draft','pending','private'],
		    'orderby'        => 'title',
		    'order'          => 'ASC',
		    'no_found_rows'  => true,
		  ]);
		  ?>

		  <select
		    id="logestay_destination_select"
		    name="logestay_destination_select[]"
		    multiple
		    style="width:100%; min-height:160px;"
		  >
		    <?php foreach ( $cities as $city ) : ?>
		      <option value="<?php echo (int) $city->ID; ?>" <?php selected( in_array((int)$city->ID, $destinations, true), true ); ?>>
		        <?php echo esc_html( $city->post_title ); ?>
		      </option>
		    <?php endforeach; ?>
		  </select>

		  <p style="margin:6px 0 0;color:#646970;">
		    <?php echo esc_html__( 'Hold Cmd/Ctrl to select multiple destinations.', 'logestay' ); ?>
		  </p>
		</div>

		<?php
	}

	public static function save( int $post_id, array $data ) {
		$title        = isset( $data['logestay_destination_title'] ) ? sanitize_text_field( wp_unslash( $data['logestay_destination_title'] ) ) : '';
		$subtitle     = isset( $data['logestay_destination_subtitle'] ) ? sanitize_textarea_field( wp_unslash( $data['logestay_destination_subtitle'] ) ) : '';
		
		$destinations = [];

		if ( isset($data['logestay_destination_select']) ) {
		  $raw = $data['logestay_destination_select'];
		  $raw = is_array($raw) ? $raw : [$raw];

		  $destinations = array_values(array_filter(array_map('absint', $raw)));
		}

		update_post_meta( $post_id, '_logestay_destination_title', $title );
		update_post_meta( $post_id, '_logestay_destination_subtitle', $subtitle );
		update_post_meta( $post_id, '_logestay_destination_select', $destinations );
	}
}
