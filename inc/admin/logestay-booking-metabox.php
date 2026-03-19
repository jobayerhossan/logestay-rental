<?php
/**
 * Booking Metabox UI (read-only overview + optional admin status update)
 *
 * @package logestay
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'logestay_booking_meta',
		__( 'Booking Details', 'logestay' ),
		'logestay_render_booking_meta_box',
		'logestay_booking',
		'normal',
		'high'
	);
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) return;

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'logestay_booking' !== $screen->post_type ) return;

	wp_add_inline_style( 'wp-admin', '
		.logestay-grid { display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; }
		@media (max-width: 1024px){ .logestay-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }
		@media (max-width: 782px){ .logestay-grid { grid-template-columns: 1fr; } }
		.logestay-field { background:#fff; border:1px solid #dcdcde; border-radius:10px; padding:12px; }
		.logestay-field label { display:block; font-weight:700; margin:0 0 6px; }
		.logestay-value { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
		.logestay-muted { color:#646970; font-size:12px; margin-top:4px; }
		.logestay-actions { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; }
		.logestay-actions .logestay-field { flex: 1; min-width: 220px; }
	' );
} );

/**
 * Helper: safe meta fetch
 */
function logestay_bm( int $post_id, string $key, $default = '' ) {
	$v = get_post_meta( $post_id, $key, true );
	if ( $v === '' || $v === null ) return $default;
	return $v;
}

/**
 * Helper: format value for display
 */
function logestay_format_meta_value( $v ): string {
	if ( is_array( $v ) || is_object( $v ) ) {
		return wp_json_encode( $v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	}
	if ( is_bool( $v ) ) return $v ? 'true' : 'false';
	return (string) $v;
}

function logestay_render_booking_meta_box( WP_Post $post ) {
	wp_nonce_field( 'logestay_booking_meta_save', 'logestay_booking_meta_nonce' );

	$booking_id = (int) $post->ID;

	// Core
	$listing_id = (int) logestay_bm( $booking_id, 'logestay_booking_listing_id', 0 );
	$check_in   = (string) logestay_bm( $booking_id, 'logestay_check_in', '' );
	$check_out  = (string) logestay_bm( $booking_id, 'logestay_check_out', '' );

	$adults   = (int) logestay_bm( $booking_id, 'logestay_adults', 1 );
	$children = (int) logestay_bm( $booking_id, 'logestay_children', 0 );
	$pets     = (int) logestay_bm( $booking_id, 'logestay_pets', 0 );

	$guest_name  = (string) logestay_bm( $booking_id, 'logestay_guest_name', '' );
	$guest_email = (string) logestay_bm( $booking_id, 'logestay_guest_email', '' );
	$guest_phone = (string) logestay_bm( $booking_id, 'logestay_guest_phone', '' );
	$logestay_cleaning_fee        =  logestay_bm( $booking_id, 'logestay_cleaning_fee', '' );
	$note        = (string) logestay_bm( $booking_id, 'logestay_special_requests', '' );

	$payment_method = (string) logestay_bm( $booking_id, 'logestay_payment_method', '' );
	$payment_type   = (string) logestay_bm( $booking_id, 'logestay_payment_type', '' );
	$payment_status = (string) logestay_bm( $booking_id, 'logestay_payment_status', '' );
	$txn_id         = (string) logestay_bm( $booking_id, 'logestay_payment_txn_id', '' );

	$total    = (float) logestay_bm( $booking_id, 'logestay_total_amount', 0 );
	$currency = (string) logestay_bm( $booking_id, 'logestay_currency', 'EUR' );

	$booking_status  = (string) logestay_bm( $booking_id, 'logestay_booking_status', 'pending' );
	$hold_expires_at = (string) logestay_bm( $booking_id, 'logestay_hold_expires_at', '' );
	$created_at      = (string) logestay_bm( $booking_id, 'logestay_created_at', '' );

	// Listing / city titles
	$listing_title = $listing_id ? get_the_title( $listing_id ) : '';
	$city_title    = '';
	if ( $listing_id ) {
		$city_id = (int) get_post_meta( $listing_id, 'logestay_city_id', true );
		$city_title = $city_id ? get_the_title( $city_id ) : '';
	}

	// Rough nights
	$nights = '';
	if ( $check_in && $check_out ) {
		$in  = strtotime( $check_in );
		$out = strtotime( $check_out );
		if ( $in && $out && $out > $in ) {
			$nights = (string) max( 1, round( ($out - $in) / DAY_IN_SECONDS ) );
		}
	}

	?>



	<h4 style="margin:0 0 10px;"><?php esc_html_e( 'Admin Actions (Optional)', 'logestay' ); ?></h4>
	<p class="logestay-muted" style="margin-top:0;">
		<?php esc_html_e( 'These fields help you test without Stripe webhooks. You can update booking/payment statuses manually.', 'logestay' ); ?>
	</p>

	<div class="logestay-actions">
		<div class="logestay-field">
			<label><?php esc_html_e( 'Update Booking Status', 'logestay' ); ?></label>
			<select name="logestay_admin_booking_status" class="widefat">
				<?php
				$opts = [ 'pending', 'confirmed', 'canceled', 'expired' ];
				foreach ( $opts as $o ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $o ),
						selected( $booking_status, $o, false ),
						esc_html( ucfirst( $o ) )
					);
				}
				?>
			</select>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Update Payment Status', 'logestay' ); ?></label>
			<select name="logestay_admin_payment_status" class="widefat">
				<?php
				$opts = [ 'pending', 'paid', 'failed', 'refunded' ];
				foreach ( $opts as $o ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $o ),
						selected( $payment_status, $o, false ),
						esc_html( ucfirst( $o ) )
					);
				}
				?>
			</select>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Clear Hold Expiry', 'logestay' ); ?></label>
			<label style="display:flex;gap:8px;align-items:center;font-weight:600;">
				<input type="checkbox" name="logestay_admin_clear_hold" value="1">
				<?php esc_html_e( 'Remove logestay_hold_expires_at', 'logestay' ); ?>
			</label>
		</div>
	</div>

	<hr style="margin:18px 0;">

	<div class="logestay-grid">

		<div class="logestay-field">
			<label><?php esc_html_e( 'Booking ID', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $booking_id ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Booking Status', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $booking_status ?: '—' ); ?></div>
			<div class="logestay-muted"><?php esc_html_e( 'pending / confirmed / canceled / expired', 'logestay' ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Hold Expires At', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $hold_expires_at ?: '—' ); ?></div>
		</div>

		<div class="logestay-field" style="grid-column: span 3;">
			<label><?php esc_html_e( 'Listing', 'logestay' ); ?></label>
			<div class="logestay-value">
				<?php
				if ( $listing_id ) {
					$edit = get_edit_post_link( $listing_id );
					echo '<a href="' . esc_url( $edit ) . '">' . esc_html( $listing_title ?: ('#' . $listing_id) ) . '</a>';
					echo ' <span style="color:#646970;">(#' . esc_html( $listing_id ) . ')</span>';
					if ( $city_title ) {
						echo '<div class="logestay-muted">' . esc_html__( 'City:', 'logestay' ) . ' ' . esc_html( $city_title ) . '</div>';
					}
				} else {
					echo '—';
				}
				?>
			</div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Check-in', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $check_in ?: '—' ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Check-out', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $check_out ?: '—' ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Nights', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $nights ?: '—' ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Adults', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( (string) $adults ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Children', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( (string) $children ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Pets', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( (string) $pets ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Guest Name', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $guest_name ?: '—' ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Guest Email', 'logestay' ); ?></label>
			<div class="logestay-value">
				<?php if ( $guest_email ) : ?>
					<a href="mailto:<?php echo esc_attr( $guest_email ); ?>"><?php echo esc_html( $guest_email ); ?></a>
				<?php else : ?>
					—
				<?php endif; ?>
			</div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Guest Phone', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $guest_phone ?: '—' ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Cleaning fee', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $logestay_cleaning_fee ?: '—' ); ?></div>
		</div>

		<div class="logestay-field" style="grid-column: span 3;">
			<label><?php esc_html_e( 'Special Requests', 'logestay' ); ?></label>
			<div><?php echo $note ? nl2br( esc_html( $note ) ) : '—'; ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Total Amount', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( number_format_i18n( $total, 2 ) . ' ' . strtoupper( $currency ) ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Payment Method', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $payment_method ?: '—' ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Payment Status', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $payment_status ?: '—' ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Payment Type', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $payment_type ?: '—' ); ?></div>
		</div>

		<div class="logestay-field" style="grid-column: span 2;">
			<label><?php esc_html_e( 'Transaction ID', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $txn_id ?: '—' ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Created At', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $created_at ?: '—' ); ?></div>
		</div>

	</div>

	

	<?php
}

add_action( 'save_post_logestay_booking', function ( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['logestay_booking_meta_nonce'] ) || ! wp_verify_nonce( $_POST['logestay_booking_meta_nonce'], 'logestay_booking_meta_save' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	// Optional admin actions
	$bs = sanitize_text_field( $_POST['logestay_admin_booking_status'] ?? '' );
	$ps = sanitize_text_field( $_POST['logestay_admin_payment_status'] ?? '' );

	$allowed_bs = [ 'pending', 'confirmed', 'canceled', 'expired' ];
	$allowed_ps = [ 'pending', 'paid', 'failed', 'refunded' ];

	if ( in_array( $bs, $allowed_bs, true ) ) {
		update_post_meta( $post_id, 'logestay_booking_status', $bs );
	}

	if ( in_array( $ps, $allowed_ps, true ) ) {
		update_post_meta( $post_id, 'logestay_payment_status', $ps );
	}

	if ( ! empty( $_POST['logestay_admin_clear_hold'] ) ) {
		delete_post_meta( $post_id, 'logestay_hold_expires_at' );
	}
}, 10, 1 );