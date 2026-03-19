<?php
/**
 * LogeStay custom login route
 */

add_action('init', 'logestay_register_login_route');
function logestay_register_login_route() {
    add_rewrite_rule(
        '^(fr|en|es|pt)?/?login/?$',
        'index.php?logestay_login=1',
        'top'
    );
}

add_filter('query_vars', 'logestay_login_query_vars');
function logestay_login_query_vars($vars) {
    $vars[] = 'logestay_login';
    return $vars;
}

add_filter('template_include', 'logestay_login_template_include');
function logestay_login_template_include($template) {
    if (get_query_var('logestay_login')) {
        $custom_template = get_template_directory() . '/templates/login-template.php';

        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }

    return $template;
}



add_action('login_init', 'logestay_redirect_default_login');
function logestay_redirect_default_login() {

    // 🚨 IMPORTANT: skip if user is already logged in
    if ( is_user_logged_in() ) {
        return;
    }

    $action = isset($_REQUEST['action']) ? sanitize_key($_REQUEST['action']) : 'login';

    $allowed_actions = array(
        'logout',
        'lostpassword',
        'rp',
        'resetpass',
        'register',
        'postpass',
        'confirmaction',
    );

    if ($action === 'login') {
        wp_safe_redirect(home_url('/login/'));
        exit;
    }

    if (!in_array($action, $allowed_actions, true)) {
        wp_safe_redirect(home_url('/login/'));
        exit;
    }
}



add_action('admin_init', 'logestay_redirect_admin_guests_to_login');
function logestay_redirect_admin_guests_to_login() {

    // allow ajax
    if ( wp_doing_ajax() ) {
        return;
    }

    // allow logged in users
    if ( is_user_logged_in() ) {
        return;
    }

    wp_safe_redirect(home_url('/login/'));
    exit;
}


//add_action('wp_login_failed', 'logestay_login_failed_redirect');
function logestay_login_failed_redirect() {
  wp_safe_redirect(home_url('/login/?login=failed'));
  exit;
}




add_action('wp_ajax_nopriv_logestay_ajax_login', 'logestay_ajax_login');
function logestay_ajax_login() {

    // nonce check
    if ( ! isset($_POST['security']) || ! wp_verify_nonce($_POST['security'], 'logestay_login_action') ) {
        wp_send_json_error( __('Security check failed', 'logestay') );
    }

    $creds = array(
        'user_login'    => sanitize_text_field($_POST['log']),
        'user_password' => $_POST['pwd'],
        'remember'      => ! empty($_POST['remember']),
    );

    $user = wp_signon($creds, false);

    if ( is_wp_error($user) ) {
        wp_send_json_error( __('Invalid username or password', 'logestay') );
    }

    // set current user
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);

    wp_send_json_success();
}



add_action('wp_ajax_nopriv_logestay_ajax_forgot_password', 'logestay_ajax_forgot_password');

function logestay_ajax_forgot_password() {

    // nonce check
    if ( ! isset($_POST['security']) || ! wp_verify_nonce($_POST['security'], 'logestay_forgot_action') ) {
        wp_send_json_error( __('Security check failed', 'logestay') );
    }

    $user_login = sanitize_text_field($_POST['user_login']);

    if ( empty($user_login) ) {
        wp_send_json_error( __('Please enter an email address', 'logestay') );
    }

    // check if user exists
    if ( strpos($user_login, '@') ) {
        $user = get_user_by('email', $user_login);
    } else {
        $user = get_user_by('login', $user_login);
    }

    if ( ! $user ) {
        // 🔒 security: don't reveal user existence
        wp_send_json_success();
    }

    // generate reset key
    $reset_key = get_password_reset_key($user);

    if ( is_wp_error($reset_key) ) {
        wp_send_json_error( __('Could not generate reset link', 'logestay') );
    }

    // build reset URL
    $reset_url = home_url(
      '/login/?reset_key=' . $reset_key . '&login=' . rawurlencode($user->user_login)
    );
    // send email (use WP core)
    $site_name = get_bloginfo('name');

    $subject = sprintf(
        __('Reset your %s password', 'logestay'),
        $site_name
    );

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $site_name . ' <no-reply@' . $_SERVER['SERVER_NAME'] . '>',
    );

    $message = '
    <div style="font-family: Arial, sans-serif; line-height:1.6; color:#333;">
        <h2 style="color:#ff7a00; margin-bottom:10px;">'.$site_name.'</h2>

        <p>'.__('Hello,', 'logestay').'</p>

        <p>'.__('We received a request to reset your password.', 'logestay').'</p>

        <p>
            <a href="'.$reset_url.'" 
               style="display:inline-block; padding:12px 20px; background:#ff7a00; color:#fff; text-decoration:none; border-radius:6px;">
               '.__('Reset Password', 'logestay').'
            </a>
        </p>

        <p>'.__('If the button does not work, copy and paste this link into your browser:', 'logestay').'</p>

        <p style="word-break:break-all;">'.$reset_url.'</p>

        <p>'.__('If you did not request this, you can safely ignore this email.', 'logestay').'</p>

        <hr style="margin:20px 0; border:none; border-top:1px solid #eee;">

        <p style="font-size:12px; color:#999;">
            '.$site_name.'
        </p>
    </div>
    ';

    wp_mail($user->user_email, $subject, $message, $headers);

    wp_send_json_success();
}



add_action('wp_ajax_nopriv_logestay_ajax_reset_password', 'logestay_ajax_reset_password');

function logestay_ajax_reset_password() {

    if ( ! wp_verify_nonce($_POST['security'], 'logestay_reset_action') ) {
        wp_send_json_error(__('Security check failed', 'logestay'));
    }

    $key   = sanitize_text_field($_POST['reset_key']);
    $login = sanitize_text_field($_POST['login']);
    $pass  = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($pass) || empty($confirm)) {
        wp_send_json_error(__('Please fill all fields', 'logestay'));
    }

    if ($pass !== $confirm) {
        wp_send_json_error(__('Passwords do not match', 'logestay'));
    }

    $user = check_password_reset_key($key, $login);

    if (is_wp_error($user)) {
        wp_send_json_error(__('Invalid or expired link', 'logestay'));
    }

    reset_password($user, $pass);

    wp_send_json_success();
}