<?php

if ( ! class_exists( 'CREE_Consejeria_Shortcode' ) ) {
    
    class CREE_Consejeria_Shortcode 
    {
        public function __construct() 
        {
            // Register Shortcode
            add_shortcode( 'cree_consejeria_universal_intake_form', array( $this, 'render_form_shortcode' ) );
        }

        public function render_form_shortcode( $atts ) 
        {
            $attributes = shortcode_atts( array(
                'type' => 'registration_short_form',
            ), $atts );

            // Dynamic Cache Busting for Stylesheet
            $cree_consejeria_form_styles_css_path = CREE_CONSEJERIA_PATH . 'assets/css/form-styles.css';
            $cree_consejeria_form_styles_css_url  = CREE_CONSEJERIA_URL . 'assets/css/form-styles.css';
            $cree_consejeria_form_styles_css_version = file_exists( $cree_consejeria_form_styles_css_path ) ? filemtime( $cree_consejeria_form_styles_css_path ) : null;

            wp_enqueue_style( 'cree-intake-styles-css', $cree_consejeria_form_styles_css_url, array(), $cree_consejeria_form_styles_css_version );

            // Dynamic Cache Busting for JS Script Handler
            // 1. Point the path and URL to the correct filename: client-registration-form.js
            $cree_consejeria_form_handler_js_path = CREE_CONSEJERIA_PATH . 'js/client-registration-form.js';
            $cree_consejeria_form_handler_js_url  = CREE_CONSEJERIA_URL . 'js/client-registration-form.js';
            $cree_consejeria_form_handler_js_version = file_exists( $cree_consejeria_form_handler_js_path ) ? filemtime( $cree_consejeria_form_handler_js_path ) : null;

            // 2. Enqueue using your plugin's established frontend handle
            wp_enqueue_script( 'cree-consejeria-frontend-js', $cree_consejeria_form_handler_js_url, array( 'jquery' ), $cree_consejeria_form_handler_js_version, true );

            // 3. Localize data directly to that exact handle so the script can see it
            wp_localize_script( 'cree-consejeria-frontend-js', 'cree_intake_app', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'cree_secure_intake_submission' )
            ));

            ob_start();
            if ( file_exists( CREE_CONSEJERIA_PATH . 'views/cree-consejeria_shortcode.php' ) ) {
                require( CREE_CONSEJERIA_PATH . 'views/cree-consejeria_shortcode.php' );
            }
            return ob_get_clean();
        }

        /**
         * AJAX Backend Processing Handler Method
         */
        public function cree_ajax_intake_form_handler() 
        {
            // 1. Security Nonce Check
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'cree_secure_intake_submission' ) ) {
                wp_send_json_error( array( 'message' => __( 'Security verification expired. Please reload the page.', 'cree-consejeria' ) ) );
            }

            // 2. Structural Data Array Verification
            if ( empty( $_POST['form_data'] ) || ! is_array( $_POST['form_data'] ) ) {
                wp_send_json_error( array( 'message' => __( 'Invalid data array payload received.', 'cree-consejeria' ) ) );
            }

            // Force inclusion of submission engine
            if ( defined( 'CREE_CONSEJERIA_PATH' ) && file_exists( CREE_CONSEJERIA_PATH . 'functions/cree_consejeria_functions.php' ) ) {
                require_once CREE_CONSEJERIA_PATH . 'functions/cree_consejeria_functions.php';
            }

            // 3. Clear quote escaping artifacts added automatically by WordPress core
            $raw_data = map_deep( $_POST['form_data'], 'stripslashes' );
            
            // 4. Sanitize strings cleanly before running database queries
            $payload = array_map( 'sanitize_text_field', $raw_data );

            // 5. Fire global database table insertion and CPT post creator engine
            $result = cree_consejeria_handle_form_submission( $payload );

            if ( is_wp_error( $result ) ) {
                wp_send_json_error( array( 'message' => $result->get_error_message() ) );
            } else {
                wp_send_json_success( array( 'message' => __( 'Your intake form has been securely submitted and encrypted successfully.', 'cree-consejeria' ) ) );
            }
        }
    }
}

// --- CRITICAL FIX: REGISTER THE AJAX HOOKS GLOBALLY OUTSIDE THE LIFECYCLE WINDOW ---
// This ensures admin-ajax.php can explicitly see and call the method directly.
add_action( 'wp_ajax_cree_submit_intake_form', function() {
    $handler = new CREE_Consejeria_Shortcode();
    $handler->cree_ajax_intake_form_handler();
});
add_action( 'wp_ajax_nopriv_cree_submit_intake_form', function() {
    $handler = new CREE_Consejeria_Shortcode();
    $handler->cree_ajax_intake_form_handler();
});