<?php
/**
 * 1. REDIRECT ON LOGIN
 * Sends clients to /client-portal/ and therapists/admins to /wp-admin/ or /therapist-portal/
 */
function cree_custom_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
    if ( is_wp_error( $user ) || ! isset( $user->roles ) ) {
        return $redirect_to;
    }

    // Check if user is therapist/staff or admin
    if ( user_can( $user, 'edit_posts' ) || user_can( $user, 'administrator' ) ) {
        return admin_url(); // Or site_url( '/therapist-portal/' );
    } else {
        // Clients always go to client portal
        return site_url( '/client-portal/' );
    }
}
add_filter( 'login_redirect', 'cree_custom_login_redirect', 10, 3 );


/**
 * 2. HANDLE FAILED LOGIN ATTEMPTS
 * Prevents failed logins from dropping clients back onto the default /wp-login.php screen
 */
function cree_login_failed_redirect( $username ) {
    $referrer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';

    // If login attempt originated from our client login page
    if ( ! empty( $referrer ) && strpos( $referrer, 'client-login' ) !== false ) {
        // wp_redirect( home_url( '/client-login/?login=failed' ) );
        wp_redirect( site_url( '/client-login/?login=failed' ) );
        exit;
    }
}

add_action( 'wp_login_failed', 'cree_login_failed_redirect' );


/**
 * 3. BLOCK ACCESS TO /wp-admin/ FOR CLIENTS
 * Redirects non-staff users away from dashboard requests while keeping AJAX functional
 */
function cree_block_wp_admin_access() {
    // Allow AJAX requests to pass through
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    }

    // If user is logged in but does not have staff permissions
    if ( is_user_logged_in() && ! current_user_can( 'edit_posts' ) ) {
        wp_redirect( site_url( '/client-portal/' ) );
        exit;
    }
}
add_action( 'admin_init', 'cree_block_wp_admin_access' );


/**
 * 4. HIDE ADMIN BAR FOR CLIENTS
 * Prevents the top black WordPress toolbar from showing when clients browse the website
 */
function cree_disable_admin_bar_for_clients() {
    if ( is_user_logged_in() && ! current_user_can( 'edit_posts' ) ) {
        show_admin_bar( false );
    }
}
add_action( 'after_setup_theme', 'cree_disable_admin_bar_for_clients' );


/**
 * 5. REDIRECT ON LOGOUT
 * Redirects clients back to the custom client login page on logout
 */
function cree_custom_logout_redirect() {
    // wp_redirect( home_url( '/client-login/?loggedout=true' ) );
    wp_redirect( site_url( '/client-login/?loggedout=true' ) );
    exit;
}
add_action( 'wp_logout', 'cree_custom_logout_redirect' );