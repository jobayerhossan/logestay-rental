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

	add_meta_box(
		'logestay_booking_email_history',
		__( 'Email History', 'logestay' ),
		'logestay_render_booking_email_history_meta_box',
		'logestay_booking',
		'side',
		'default'
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
		.logestay-email-history { margin:0; padding:0; list-style:none; }
		.logestay-email-history li { margin:0 0 12px; padding:0 0 12px; border-bottom:1px solid #dcdcde; }
		.logestay-email-history li:last-child { margin-bottom:0; padding-bottom:0; border-bottom:0; }
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

function logestay_set_booking_admin_notice( string $code ): void {
	$GLOBALS['logestay_booking_admin_notice'] = $code;
}

add_filter( 'redirect_post_location', function ( $location ) {
	$notice = $GLOBALS['logestay_booking_admin_notice'] ?? '';
	if ( $notice === '' ) {
		return $location;
	}

	return add_query_arg( 'logestay_booking_notice', rawurlencode( $notice ), $location );
} );

add_action( 'admin_notices', function () {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'logestay_booking' !== $screen->post_type ) return;

	$notice = sanitize_text_field( $_GET['logestay_booking_notice'] ?? '' );
	if ( $notice === '' ) return;

	$map = [
		'booking_confirmed_paid'   => [ 'type' => 'success', 'text' => __( 'Booking marked as confirmed and payment marked as paid.', 'logestay' ) ],
		'arrival_sent_manual'      => [ 'type' => 'success', 'text' => __( 'Arrival instructions sent manually to the guest.', 'logestay' ) ],
		'arrival_sent_manual_again'=> [ 'type' => 'warning', 'text' => __( 'Arrival instructions were already sent before and have now been sent again manually.', 'logestay' ) ],
		'arrival_send_failed'      => [ 'type' => 'error', 'text' => __( 'Arrival instructions could not be sent. Please check guest email, booking status, and payment status.', 'logestay' ) ],
	];

	if ( empty( $map[ $notice ] ) ) return;

	$data = $map[ $notice ];
	printf(
		'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
		esc_attr( $data['type'] ),
		esc_html( $data['text'] )
	);
} );

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
	$keybox_code     = (string) logestay_bm( $booking_id, 'logestay_keybox_code', '' );
	$arrival_sent_at = (string) logestay_bm( $booking_id, 'logestay_arrival_instructions_sent_at', '' );
	$arrival_source  = (string) logestay_bm( $booking_id, 'logestay_arrival_instructions_sent_source', '' );

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

	<div style="margin-top:12px;display:flex;gap:12px;flex-wrap:wrap;">
		<?php submit_button( __( 'Payment Received / Confirm Booking', 'logestay' ), 'secondary', 'logestay_confirm_paid_and_booking', false ); ?>
		<?php submit_button( $arrival_sent_at ? __( 'Resend Arrival Instructions', 'logestay' ) : __( 'Send Arrival Instructions', 'logestay' ), 'primary', 'logestay_send_arrival_instructions_now', false ); ?>
	</div>
	<p class="logestay-muted" style="margin-top:8px;">
		<?php esc_html_e( 'Use the manual arrival email button for urgent bookings or manually validated payments.', 'logestay' ); ?>
	</p>

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

		<div class="logestay-field">
			<label><?php esc_html_e( 'Keybox Code', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $keybox_code ?: '—' ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Arrival Instructions Sent At', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $arrival_sent_at ?: '—' ); ?></div>
		</div>

		<div class="logestay-field">
			<label><?php esc_html_e( 'Arrival Instructions Source', 'logestay' ); ?></label>
			<div class="logestay-value"><?php echo esc_html( $arrival_source ?: '—' ); ?></div>
		</div>

	</div>

	

	<?php
}

function logestay_render_booking_email_history_meta_box( WP_Post $post ) {
	$booking_id = (int) $post->ID;
	$log = function_exists( 'logestay_get_booking_email_log' )
		? logestay_get_booking_email_log( $booking_id )
		: [];

	if ( empty( $log ) ) {
		echo '<p>' . esc_html__( 'No booking emails have been logged yet.', 'logestay' ) . '</p>';
		return;
	}

	$log = array_reverse( $log );

	echo '<ul class="logestay-email-history">';
	foreach ( $log as $entry ) {
		$subject  = (string) ( $entry['subject'] ?? '' );
		$type     = (string) ( $entry['type'] ?? '' );
		$source   = (string) ( $entry['source'] ?? '' );
		$template = (string) ( $entry['template'] ?? '' );
		$sent_at  = (string) ( $entry['sent_at'] ?? '' );

		echo '<li>';
		echo '<strong style="display:block;margin-bottom:4px;">' . esc_html( $subject ?: __( 'Email sent', 'logestay' ) ) . '</strong>';
		if ( $sent_at ) {
			echo '<div class="logestay-muted" style="margin-top:0;">' . esc_html( $sent_at ) . '</div>';
		}
		if ( $type ) {
			echo '<div class="logestay-muted">' . esc_html__( 'Type:', 'logestay' ) . ' ' . esc_html( $type ) . '</div>';
		}
		if ( $source ) {
			echo '<div class="logestay-muted">' . esc_html__( 'Source:', 'logestay' ) . ' ' . esc_html( $source ) . '</div>';
		}
		if ( $template ) {
			echo '<div class="logestay-muted">' . esc_html__( 'Template:', 'logestay' ) . ' ' . esc_html( $template ) . '</div>';
		}
		echo '</li>';
	}
	echo '</ul>';
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

	if ( ! empty( $_POST['logestay_confirm_paid_and_booking'] ) ) {
		update_post_meta( $post_id, 'logestay_booking_status', 'confirmed' );
		update_post_meta( $post_id, 'logestay_payment_status', 'paid' );
		if ( function_exists( 'logestay_get_or_create_keybox_code' ) ) {
			logestay_get_or_create_keybox_code( (int) $post_id );
		}
		logestay_set_booking_admin_notice( 'booking_confirmed_paid' );
	}

	if ( ! empty( $_POST['logestay_send_arrival_instructions_now'] ) ) {
		$already_sent = (string) get_post_meta( $post_id, 'logestay_arrival_instructions_sent_at', true );
		$sent = function_exists( 'logestay_send_arrival_instructions_email' )
			? logestay_send_arrival_instructions_email( (int) $post_id, 'manual' )
			: false;

		if ( $sent ) {
			logestay_set_booking_admin_notice( $already_sent ? 'arrival_sent_manual_again' : 'arrival_sent_manual' );
		} else {
			logestay_set_booking_admin_notice( 'arrival_send_failed' );
		}
	}
}, 10, 1 );
