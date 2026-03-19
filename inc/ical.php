<?php 
function logestay_fetch_ical_raw($url) {
  $url = trim((string) $url);
  if (!$url) return '';

  $url = esc_url_raw($url);
  if (!$url) return '';

  $res = wp_remote_get($url, [
    'timeout'     => 20,
    'redirection' => 5,
    'sslverify'   => false, // helpful on localhost/self-signed
    'headers'     => [
      'Accept'     => 'text/calendar, text/plain;q=0.9, */*;q=0.8',
      'User-Agent' => 'Logestay iCal Sync (+WordPress)',
    ],
  ]);

  if (is_wp_error($res)) {
    return '';
  }

  $code = (int) wp_remote_retrieve_response_code($res);
  $body = (string) wp_remote_retrieve_body($res);

  // If not 2xx, fail
  if ($code < 200 || $code >= 300) {
    return '';
  }

  // If HTML came back, it's not an ICS
  if (stripos($body, 'BEGIN:VCALENDAR') === false) {
    return '';
  }

  return $body;
}

/**
 * Very lightweight parser: reads DTSTART/DTEND inside VEVENT blocks.
 * Returns array of ['start'=>'Y-m-d','end'=>'Y-m-d'] ranges.
 */
function logestay_parse_ical_ranges($ics) {
  if (!$ics) return [];

  // Handle folded lines (RFC 5545): lines starting with space/tab are continuations
  $ics = preg_replace("/\r\n[ \t]/", "", $ics);
  $ics = str_replace("\r\n", "\n", $ics); // normalize

  preg_match_all('/BEGIN:VEVENT(.*?)END:VEVENT/s', $ics, $matches);
  if (empty($matches[1])) return [];

  $ranges = [];

  foreach ($matches[1] as $block) {

    // Grab DTSTART / DTEND value after ":" (can be 20260110 or 20260110T120000Z)
    preg_match('/DTSTART[^:]*:([0-9]{8}(?:T[0-9]{6}Z?)?)/i', $block, $m1);
    preg_match('/DTEND[^:]*:([0-9]{8}(?:T[0-9]{6}Z?)?)/i', $block, $m2);

    if (empty($m1[1]) || empty($m2[1])) continue;

    $startYmd = substr($m1[1], 0, 8);
    $endYmd   = substr($m2[1], 0, 8);

    $start = DateTime::createFromFormat('Ymd', $startYmd);
    $end   = DateTime::createFromFormat('Ymd', $endYmd);

    if (!$start || !$end) continue;

    $ranges[] = [
      'start' => $start->format('Y-m-d'),
      'end'   => $end->format('Y-m-d'), // DTEND is exclusive for stays
    ];
  }

  return $ranges;
}

/**
 * Expand ranges into day list (Y-m-d). DTEND is exclusive.
 */
function logestay_ranges_to_days(array $ranges) {
  $days = [];

  foreach ($ranges as $r) {
    $start = strtotime($r['start']);
    $end   = strtotime($r['end']);
    if (!$start || !$end) continue;

    for ($t = $start; $t < $end; $t += DAY_IN_SECONDS) {
      $days[] = date('Y-m-d', $t);
    }
  }

  $days = array_values(array_unique($days));
  sort($days);
  return $days;
}


function logestay_sync_listing_icals($listing_id) {
  $listing_id = absint($listing_id);
  if (!$listing_id) return false;

  $airbnb = get_post_meta($listing_id, 'logestay_ical_airbnb_url', true);
  $booking= get_post_meta($listing_id, 'logestay_ical_booking_url', true);

  $all_days = [];

  foreach ([$airbnb, $booking] as $url) {
    $raw = logestay_fetch_ical_raw($url);
    if (!$raw) continue;

    $ranges = logestay_parse_ical_ranges($raw);
    $days   = logestay_ranges_to_days($ranges);

    $all_days = array_merge($all_days, $days);
  }

  $all_days = array_values(array_unique($all_days));
  sort($all_days);

  update_post_meta($listing_id, 'logestay_blocked_dates', $all_days);
  update_post_meta($listing_id, 'logestay_icals_last_sync', current_time('mysql'));

  return true;
}


add_action('init', function () {
  if (!wp_next_scheduled('logestay_ical_sync_cron')) {
    wp_schedule_event(time() + 60, 'hourly', 'logestay_ical_sync_cron');
  }
});

add_action('logestay_ical_sync_cron', function () {
  $q = new WP_Query([
    'post_type' => 'logestay_listing',
    'posts_per_page' => 50,
    'fields' => 'ids',
  ]);

  foreach ($q->posts as $listing_id) {
    logestay_sync_listing_icals($listing_id);
  }
});


add_action('init', function () {
  add_rewrite_rule('^ical/listing/([0-9]+)\.ics/?$', 'index.php?logestay_ical_listing=$matches[1]', 'top');
});

add_filter('query_vars', function ($vars) {
  $vars[] = 'logestay_ical_listing';
  return $vars;
});

add_action('template_redirect', function () {
  $listing_id = absint(get_query_var('logestay_ical_listing'));
  if (!$listing_id) return;

  header('Content-Type: text/calendar; charset=utf-8');
  header('Cache-Control: no-cache, no-store, must-revalidate');

  echo "BEGIN:VCALENDAR\r\n";
  echo "VERSION:2.0\r\n";
  echo "PRODID:-//LOGESTAY//Booking Calendar//EN\r\n";

  // output confirmed bookings for this listing
  $q = new WP_Query([
    'post_type' => 'logestay_booking',
    'posts_per_page' => -1,
    'meta_query' => [
      ['key'=>'logestay_booking_listing_id','value'=>$listing_id,'compare'=>'='],
      ['key'=>'logestay_booking_status','value'=>'confirmed','compare'=>'='],
    ],
  ]);

  foreach ($q->posts as $b) {
    $in  = get_post_meta($b->ID, 'logestay_check_in', true);
    $out = get_post_meta($b->ID, 'logestay_check_out', true);

    if (!$in || !$out) continue;

    $dtstart = date('Ymd', strtotime($in));
    $dtend   = date('Ymd', strtotime($out));

    echo "BEGIN:VEVENT\r\n";
    echo "UID:logestay-booking-{$b->ID}@logestay\r\n";
    echo "DTSTART;VALUE=DATE:{$dtstart}\r\n";
    echo "DTEND;VALUE=DATE:{$dtend}\r\n";
    echo "SUMMARY:Booked\r\n";
    echo "END:VEVENT\r\n";
  }

  echo "END:VCALENDAR\r\n";
  exit;
});


add_action('after_switch_theme', function () {
  flush_rewrite_rules();
});

add_action('switch_theme', function () {
  wp_clear_scheduled_hook('logestay_ical_sync_cron');
});


add_action('init', function () {
  if (isset($_GET['run_ical_sync']) && current_user_can('manage_options')) {
    do_action('logestay_ical_sync_cron');
    wp_die('iCal sync executed');
  }
});