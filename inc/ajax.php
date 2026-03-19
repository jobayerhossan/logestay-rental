<?php 
add_action('wp_ajax_logestay_get_listings_by_city', 'logestay_get_listings_by_city');
add_action('wp_ajax_nopriv_logestay_get_listings_by_city', 'logestay_get_listings_by_city');

function logestay_get_listings_by_city() {
  check_ajax_referer('logestay_city_filter', 'nonce');

  $city_id = isset($_POST['city_id']) ? absint($_POST['city_id']) : 0;
  if (!$city_id) {
    wp_send_json_error(['message' => __('Invalid city id', 'logestay')]);
  }

  $q = new WP_Query([
    'post_type'      => 'logestay_listing',
    'posts_per_page' => 3,
    'meta_query'     => [
      [
        'key'     => 'logestay_city_id',
        'value'   => $city_id,
        'compare' => '=',
        'type'    => 'NUMERIC',
      ]
    ]
  ]);

  ob_start();

  if ($q->have_posts()) : ?>
    <div class="relative">
      <div class="flex gap-8 overflow-x-auto scrollbar-hide snap-x snap-mandatory scroll-smooth pb-4" style="scrollbar-width: none;">
        <?php while($q->have_posts()) : $q->the_post();
          get_template_part('template-parts/listing', 'item');
        endwhile; ?>
      </div>
    </div>
  <?php else: ?>
    <div class="text-center py-20">
      <p class="text-2xl md:text-3xl font-semibold text-gray-700 mb-4">
        <?php _e('No accommodation available at the moment', 'logestay'); ?>
      </p>
      <p class="text-lg text-gray-500">
        <?php _e('Come back soon to discover our new offers', 'logestay'); ?>
      </p>
    </div>
  <?php endif;

  wp_reset_postdata();
  $html = ob_get_clean();

  wp_send_json_success([
    'city_title' => get_the_title($city_id),
    'html'       => $html,
    'count'      => (int) $q->found_posts,
  ]);
}



add_action( 'wp_ajax_logestay_submit_support_form', 'logestay_submit_support_form' );
add_action( 'wp_ajax_nopriv_logestay_submit_support_form', 'logestay_submit_support_form' );

function logestay_submit_support_form() {

  // Nonce check
  $nonce = $_POST['nonce'] ?? '';
  if ( ! wp_verify_nonce( $nonce, 'logestay_nonce' ) ) {
    wp_send_json_error( [ 'message' => __( 'Security check failed.', 'logestay' ) ], 403 );
  }

  $first_name  = sanitize_text_field( $_POST['firstName'] ?? '' );
  $email       = sanitize_email( $_POST['email'] ?? '' );
  $subject_key = sanitize_text_field( $_POST['subject'] ?? '' );
  $message     = sanitize_textarea_field( $_POST['message'] ?? '' );
  $gdpr        = absint( $_POST['gdprConsent'] ?? 0 );

  // Basic validation
  if ( $first_name === '' || $email === '' || ! is_email( $email ) || $subject_key === '' || $message === '' || ! $gdpr ) {
    wp_send_json_error( [ 'message' => __( 'Please fill all required fields correctly.', 'logestay' ) ], 422 );
  }

  // Subject labels (match your select values)
  $subject_map = [
    'pre-booking'   => __( 'Question before booking', 'logestay' ),
    'booking-issue' => __( 'Booking problem', 'logestay' ),
    'during-stay'   => __( 'During stay', 'logestay' ),
    'other'         => __( 'Other request', 'logestay' ),
  ];
  $subject_label = $subject_map[ $subject_key ] ?? $subject_key;

  $site_name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
  $site_url  = home_url();

  // Admin recipient (or your saved support email if you have it)
  $to_admin = get_option( 'admin_email' );

  // Optional: if you already store a support email in settings, prefer it:
  // $settings = get_option('logestay_settings', []);
  // if (!empty($settings['logestay_contact_email']) && is_email($settings['logestay_contact_email'])) {
  //   $to_admin = $settings['logestay_contact_email'];
  // }

  /* ----------------------------
   * 1) Email to Admin / Support
   * ---------------------------- */
  $admin_subject = sprintf( '[%s] Support: %s', $site_name, $subject_label );

  $admin_body  = '<p><strong>' . esc_html__( 'New support request', 'logestay' ) . '</strong></p>';
  $admin_body .= '<p>';
  $admin_body .= esc_html__( 'Name:', 'logestay' ) . ' ' . esc_html( $first_name ) . '<br>';
  $admin_body .= esc_html__( 'Email:', 'logestay' ) . ' ' . esc_html( $email ) . '<br>';
  $admin_body .= esc_html__( 'Subject:', 'logestay' ) . ' ' . esc_html( $subject_label ) . '<br>';
  $admin_body .= '</p>';
  $admin_body .= '<p><strong>' . esc_html__( 'Message:', 'logestay' ) . '</strong><br>' . nl2br( esc_html( $message ) ) . '</p>';

  $admin_headers = [
    'Content-Type: text/html; charset=UTF-8',
    'Reply-To: ' . $first_name . ' <' . $email . '>',
  ];

  $sent_admin = wp_mail( $to_admin, $admin_subject, $admin_body, $admin_headers );

  if ( ! $sent_admin ) {
    wp_send_json_error( [ 'message' => __( 'Email could not be sent. Please try again later.', 'logestay' ) ], 500 );
  }

  /* ----------------------------
   * 2) Auto-reply email to user
   * ---------------------------- */
  $user_subject = sprintf( '%s - %s', $site_name, __( 'We received your request', 'logestay' ) );

  $user_body  = '<p>' . sprintf( esc_html__( 'Bonjour %s,', 'logestay' ), esc_html( $first_name ) ) . '</p>';
  $user_body .= '<p>' . esc_html__( 'Merci de nous avoir contactés.', 'logestay' ) . '<br>';
  $user_body .= esc_html__( 'Votre demande a bien été reçue par notre équipe support.', 'logestay' ) . '</p>';

  $user_body .= '<p><strong>' . esc_html__( 'Récapitulatif de votre demande :', 'logestay' ) . '</strong></p>';
  $user_body .= '<ul>';
  $user_body .= '<li>' . esc_html__( 'Sujet :', 'logestay' ) . ' ' . esc_html( $subject_label ) . '</li>';
  $user_body .= '<li>' . esc_html__( 'Adresse e-mail :', 'logestay' ) . ' ' . esc_html( $email ) . '</li>';
  $user_body .= '</ul>';

  $user_body .= '<p>' . esc_html__( 'Notre équipe vous répondra dans un délai de 24 à 48 heures ouvrées.', 'logestay' ) . '</p>';

  $user_body .= '<p>' . esc_html__( 'En attendant, vous pouvez consulter notre foire aux questions, qui répond aux interrogations les plus courantes concernant les réservations, les logements et les séjours.', 'logestay' ) . '</p>';

  $user_body .= '<p>' . esc_html__( "Si votre demande est urgente ou concerne un séjour en cours, merci de le préciser clairement afin qu’elle soit traitée en priorité.", 'logestay' ) . '</p>';

  $user_body .= '<p>' . esc_html__( 'À très bientôt,', 'logestay' ) . '<br>';
  $user_body .= esc_html__( "L’équipe LOGESTAY", 'logestay' ) . '<br>';
  $user_body .= esc_html__( 'Service client', 'logestay' ) . '</p>';

  // From header (use your domain email)
  $from_email = 'noreply@logestay.com';
  $from_name  = $site_name;

  $user_headers = [
    'Content-Type: text/html; charset=UTF-8',
    'From: ' . $from_name . ' <' . $from_email . '>',
  ];

  // send auto reply (don’t block success if it fails)
  wp_mail( $email, $user_subject, $user_body, $user_headers );

  wp_send_json_success( [ 'message' => __( 'Message sent successfully!', 'logestay' ) ] );
}


add_action('wp_ajax_logestay_get_city_location', 'logestay_get_city_location');
add_action('wp_ajax_nopriv_logestay_get_city_location', 'logestay_get_city_location');

function logestay_get_city_location() {
  $nonce = $_POST['nonce'] ?? '';
  if ( ! wp_verify_nonce($nonce, 'logestay_nonce') ) {
    wp_send_json_error(['message' => 'Security check failed'], 403);
  }

  $city_id = absint($_POST['city_id'] ?? 0);
  if ( ! $city_id ) {
    wp_send_json_error(['message' => __('Invalid city', 'logestay')], 422);
  }

  $city = get_post($city_id);
  if ( ! $city || $city->post_type !== 'logestay_city' ) {
    wp_send_json_error(['message' => __('City not found', 'logestay')], 404);
  }

  $embed = get_post_meta($city_id, 'logestay_city_map_embed', true);
  $open  = get_post_meta($city_id, 'logestay_city_map_open', true);

  if(empty($embed)){
    $city_name = get_the_title($city_id);
    $query = rawurlencode($city_name);
    $embed = 'https://www.google.com/maps?q=' . $query . '&output=embed';
  }
  

  $embed = esc_url_raw($embed);
  $open  = esc_url_raw($open);

  $nearby = get_post_meta($city_id, 'logestay_city_nearby', true);
  $nearby = is_array($nearby) ? $nearby : [];

  $clean = [];
  foreach ($nearby as $n) {
    if ( ! is_array($n) ) continue;

    $title    = sanitize_text_field($n['title'] ?? '');
    $subtitle = sanitize_text_field($n['subtitle'] ?? '');
    $distance = sanitize_text_field($n['distance'] ?? '');
    $url      = esc_url_raw($n['url'] ?? '');
    $image_id = absint($n['image_id'] ?? 0);

    if ( $title === '' ) continue;

    $img = '';
    if ( $image_id ) {
      $src = wp_get_attachment_image_src($image_id, 'large');
      if ( $src && ! empty($src[0]) ) $img = esc_url($src[0]);
    }

    $clean[] = [
      'title'    => $title,
      'subtitle' => $subtitle,
      'distance' => $distance,
      'url'      => $url,
      'image'    => $img,
    ];
  }

  wp_send_json_success([
    'city_title' => get_the_title($city_id),
    'map_embed'  => $embed,
    'map_open'   => $open,
    'nearby'     => $clean,
  ]);
}

