<?php

/**
* Plugin Name: CREE Consejeria CFM
* Plugin URI: https://www.wordpress.org/cree-consejeria
* Description: My plugin's description
* Version: 1.0
* Requires at least: 5.6
* Requires PHP: 7.0
* Author: Rafael Ramírez Flores
* Author URI: https://www.codigowp.net
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: cree-consejeria-cfm
* Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if( !class_exists( 'CREE_Consejeria_CFM' )){

    class CREE_Consejeria_CFM{

        public function __construct(){

            $this->define_constants();

            require_once CREE_CONSEJERIA_CFM_PATH . 'post-types/class.cree-consejeria-cpt.php';
            $Cree_Consejeria_Post_Type = new CREE_Consejeria_Post_Type();

            add_action('admin_enqueue_scripts', [$this, 'register_admin_styles'], 999);

            require_once CREE_CONSEJERIA_CFM_PATH . 'shortcodes/class.cree-consejeria-shortcode.php';
            $Cree_Consejeria_Shortcode = new CREE_Consejeria_Shortcode();

            add_action('wp_enqueue_scripts', [$this, 'register_wp_styles'], 999);

            add_action('wp_ajax_cree_submit_intake_form', [$this, 'cree_ajax_intake_form_handler']);
            add_action('wp_ajax_nopriv_cree_submit_intake_form', [$this, 'cree_ajax_intake_form_handler']);

            add_action('wp_ajax_get_draft_form_data', [$this, 'get_draft_form_data']);
                        
        }

        public function define_constants(){
            define ( 'CREE_CONSEJERIA_CFM_PATH', plugin_dir_path( __FILE__ ) );
            define ( 'CREE_CONSEJERIA_CFM_URL', plugin_dir_url( __FILE__ ) );
            define ( 'CREE_CONSEJERIA_CFM_VERSION', '1.0.0' );
        }

        /**
         * Activate the plugin
         */
        public static function activate(){
            
            update_option('rewrite_rules', '' );

            global $wpdb;

            $table_name = $wpdb->prefix . 'cree_consejeria_client_forms_master';
            $charset_collate = $wpdb->get_charset_collate();

            $cree_consejeria_cfm_db_version = get_option( 'cree_consejeria_cfm_db_version' );
            $table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

            if ( $table_exists !== $table_name ) {

                $query = "CREATE TABLE $table_name (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    wp_id bigint(20) unsigned NOT NULL,
                    user_id bigint(20) unsigned DEFAULT 0,
                    form_type varchar(100) NOT NULL,
                    client_name varchar(255) DEFAULT NULL,
                    dob date DEFAULT NULL,
                    email varchar(100) DEFAULT NULL,
                    form_data_encrypted blob DEFAULT NULL,
                    signature varchar(255) DEFAULT NULL,
                    status varchar(20) DEFAULT 'complete',
                    signature_date date DEFAULT NULL,
                    user_ip varchar(45) DEFAULT NULL,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY  (id),
                    UNIQUE KEY wp_id (wp_id),
                    KEY user_id (user_id),
                    KEY form_type (form_type),
                    KEY client_name (client_name),
                    KEY dob (dob),
                    KEY status (status),
                    KEY user_ip (user_ip)
                    ) $charset_collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $query );
            }

            if ( empty( $cree_consejeria_cfm_db_version ) || version_compare( $cree_consejeria_cfm_db_version, '1.0.0', '<' ) ) {
                update_option( 'cree_consejeria_cfm_db_version', '1.0.0' );
            }

            $pages_to_create = array(
                array(
                    'slug'       => 'registration-short-form',
                    'title'      => __('Registration Short Form', 'cree-consejeria-cfm'),
                    'form_type'  => 'registration_short_form'
                ),
                array(
                    'slug'       => 'psychosocial-evaluation-form',
                    'title'      => __('Psychosocial Evaluation Form', 'cree-consejeria-cfm'),
                    'form_type'  => 'psychosocial_evaluation'
                ),
                array(
                    'slug'       => 'substance-abuse-form',
                    'title'      => __('Substance Abuse Form', 'cree-consejeria-cfm'),
                    'form_type'  => 'substance_abuse_intake'
                ),
                array(
                    'slug'       => 'aviso-de-practicas-de-privacidad',
                    'title'      => __('Aviso de Prácticas de Privacidad', 'cree-consejeria-cfm'),
                    'form_type'  => 'aviso_de_practicas_de_privacidad'
                ),
                array(
                    'slug'       => 'consentimiento-informado-para-psicoterapia',
                    'title'      => __('Consentimiento Informado para Psicoterapia', 'cree-consejeria-cfm'),
                    'form_type'  => 'consentimiento_informado_para_psicoterapia'
                ),
                array(
                    'slug'       => 'politicas-de-la-practica',
                    'title'      => __('Políticas de la Práctica', 'cree-consejeria-cfm'),
                    'form_type'  => 'politicas_de_la_practica'
                ),
                array(
                    'slug'       => 'consentimiento-informado-para-terapia-en-linea',
                    'title'      => __('Consentimiento Informado para Terapia en Línea', 'cree-consejeria-cfm'),
                    'form_type'  => 'consentimiento_informado_para_terapia_en_linea'
                ),
                array(
                    'slug'       => 'consentimiento-para-divulgacion-de-informacion',
                    'title'      => __('Consentimiento para la Divulgación de Información', 'cree-consejeria-cfm'),
                    'form_type'  => 'consentimiento_para_divulgacion_de_informacion'
                ),
            );

            // FIX: Force inclusion of pluggable user features to avoid Fatal Errors during activation context
            if ( ! function_exists( 'wp_get_current_user' ) ) {
                require_once ABSPATH . 'wp-includes/pluggable.php';
            }

            $current_user_id = wp_get_current_user()->ID;

            foreach ( $pages_to_create as $page_data ) {
                
                $page_exists = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND post_type = 'page'",
                        $page_data['slug']
                    )
                );

                if ( $page_exists === null ) {
                    $page = array(
                        'post_title'   => $page_data['title'],
                        'post_name'    => $page_data['slug'],
                        'post_status'  => 'publish',
                        'post_author'  => $current_user_id,
                        'post_type'    => 'page',
                        'post_content' => '[cree_consejeria_intake_form type="' . $page_data['form_type'] . '"]'
                    );
                    
                    wp_insert_post( $page );  
                }
            }
        }

        public function register_admin_styles($hook)
        {
            $cree_consejeria_admin_css_path = CREE_CONSEJERIA_CFM_PATH . 'assets/css/admin.css';
            $cree_consejeria_admin_css_url  = CREE_CONSEJERIA_CFM_URL . 'assets/css/admin.css';
            $cree_consejeria_admin_css_version = file_exists($cree_consejeria_admin_css_path) ? filemtime($cree_consejeria_admin_css_path) : false;

            wp_register_style(
                'cree-consejeria-admin-css',
                $cree_consejeria_admin_css_url,
                [],
                $cree_consejeria_admin_css_version
            );
        }

        public function register_wp_styles($hook)
        {
            $cree_consejeria_form_styles_css_path = CREE_CONSEJERIA_CFM_PATH . 'assets/css/form-styles.css';
            $cree_consejeria_form_styles_css_url  = CREE_CONSEJERIA_CFM_URL . 'assets/css/form-styles.css';
            $cree_consejeria_form_styles_css_version = file_exists($cree_consejeria_form_styles_css_path) ? filemtime($cree_consejeria_form_styles_css_path) : false;

            wp_register_style(
                'cree-consejeria-form-styles-css',
                $cree_consejeria_form_styles_css_url,
                array(),
                $cree_consejeria_form_styles_css_version
            );

            $cree_consejeria_form_handler_js_path = CREE_CONSEJERIA_CFM_PATH . 'js/form-handler.js';
            $cree_consejeria_form_handler_js_url  = CREE_CONSEJERIA_CFM_URL . 'js/form-handler.js';
            $cree_consejeria_form_handler_js_version = file_exists( $cree_consejeria_form_handler_js_path ) ? filemtime( $cree_consejeria_form_handler_js_path ) : false;

            wp_register_script(
                'cree-consejeria-form-handler-js', 
                $cree_consejeria_form_handler_js_url,
                array( 'jquery' ),
                $cree_consejeria_form_handler_js_version,
                true
            );

            wp_localize_script( 
                'cree-consejeria-form-handler-js',
                'CREE_INTAKE_FORM',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce'    => wp_create_nonce( 'cree_secure_form_submission' )
                )
            );
        }

        public function cree_ajax_intake_form_handler() {
    
            check_ajax_referer( 'cree_secure_form_submission', 'nonce' );

            if ( empty( $_POST['form_data'] ) || ! is_array( $_POST['form_data'] ) ) {
                wp_send_json_error( array( 'message' => __( 'Invalid data array payload received.', 'cree-consejeria' ) ) );
            }

            if ( defined( 'CREE_CONSEJERIA_CFM_PATH' ) && file_exists( CREE_CONSEJERIA_CFM_PATH . 'functions/cree_consejeria_functions.php' ) ) {
                require_once CREE_CONSEJERIA_CFM_PATH . 'functions/cree_consejeria_functions.php';
            }

            $raw_data = map_deep( $_POST['form_data'], 'stripslashes' );
            $payload  = array_map( 'sanitize_text_field', $raw_data );

            $payload['user_ip'] = ! empty( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '';

            // Retrieve the action from the POST data
            $form_action  = isset($_POST['form_action']) ? $_POST['form_action'] : 'final_submission';

            // Process based on the action
            if ($form_action === 'partial_submission') {
                $payload['form_status'] = 'draft';
                // Handle saving as draft/partial
                // Example: wp_insert_post() with post_status => 'draft'
                // wp_send_json_success(['message' => 'Draft saved successfully.']);
            } else {

                $payload['form_status'] = 'complete';
                // Handle final submission
                // Example: Validate fields and save to database
                // wp_send_json_success(['message' => 'Form submitted successfully.']);
            }

            $result = cree_consejeria_handle_form_submission( $payload );

            if ( is_wp_error( $result ) ) {
                wp_send_json_error( array( 'message' => $result->get_error_message() ) );
            } else {

                // 1. Obtener el form_type de los datos sanitizados
                $form_type = ! empty( $payload['form_type'] ) ? $payload['form_type'] : '';

                // 2. Mapear el slug técnico del formulario a un nombre legible para el usuario
                $form_names = array(
                    'substance_abuse_intake' => __( 'Substance Abuse Intake', 'cree-consejeria' ),
                    'aviso_de_practicas_de_privacidad' => __( 'Aviso de Prácticas de Privacidad', 'cree-consejeria' ),
                    'consentimiento_informado_para_psicoterapia' => __( 'Consentimiento Informado para Psicoterapia', 'cree-consejeria' ),
                    'politicas_de_la_practica' => __( 'Políticas de la Práctica', 'cree-consejeria' ),
                    'consentimiento_informado_para_terapia_en_linea' => __( 'Consentimiento Informado para Terapia en Línea', 'cree-consejeria' ),
                    'consentimiento_para_divulgacion_de_informacion' => __( 'Consentimiento para Divulgación de Información', 'cree-consejeria' ),
                    'registration_short_form' => __( 'Registration Short Form', 'cree-consejeria' ),
                    'psychosocial_evaluation' => __( 'Psychosocial Evaluation', 'cree-consejeria' )
                );

                // 3. Obtener el nombre legible, o usar un fallback genérico si no coincide ninguno
                $friendly_name = isset( $form_names[ $form_type ] ) ? $form_names[ $form_type ] : __( 'intake', 'cree-consejeria' );

                $success_message = sprintf(
                    /* translators: %s: El nombre legible del formulario (ej. Substance Abuse Intake) */
                    __( 'Your %s form has been securely submitted and encrypted successfully.', 'cree-consejeria' ),
                    $friendly_name
                );

                wp_send_json_success( array( 'message' => $success_message, 'wp_id' => $result ) );
            }

            wp_die();
        }


        function get_draft_form_data() {
            global $wpdb;
            
            // Catch errors and send to JS instead of crashing
            try {
                $user_id = get_current_user_id();
                $wp_id = isset($_GET['wp_id']) ? intval($_GET['wp_id']) : 0;

                if (!$user_id) throw new Exception("Not logged in");
                if (!$wp_id) throw new Exception("No valid WP_ID provided");

                $table_name = $wpdb->prefix . 'cree_consejeria_client_forms_master';
                $key = SECURE_AUTH_KEY;

                $sql = $wpdb->prepare(
                    "SELECT *, AES_DECRYPT(form_data_encrypted, %s) as decrypted_data 
                    FROM $table_name WHERE wp_id = %d", 
                    $key, 
                    $wp_id
                );

                $row = $wpdb->get_row($sql, ARRAY_A);

                if (!$row) {
                    throw new Exception("No record found in database for ID: " . $wp_id);
                }

                // Merge column data (client_name, email, dob, etc.) 
                // with the decrypted JSON data
                $decrypted_json = json_decode($row['decrypted_data'], true);
                
                // Remove the raw blob and decrypted string to keep the response clean
                unset($row['form_data_encrypted']);
                unset($row['decrypted_data']);
                
                // Combine everything into one clean data object
                $response_data = array_merge($row, $decrypted_json);

                wp_send_json_success(['form_data' => $response_data]);

            } catch (Exception $e) {
                // Send the error message to the JS console
                wp_send_json_error(['message' => $e->getMessage()]);
            }
        }

        /**
         * Deactivate the plugin
         */
        public static function deactivate(){
            flush_rewrite_rules();
        }        

        /**
         * Uninstall the plugin
         * FIX: Switched to public static function to be valid within register_uninstall_hook
         */
        public static function uninstall(){
            delete_option('cree_consejeria_cfm_db_version');
            
            global $wpdb;
            $wpdb->query(
                "DELETE FROM $wpdb->posts WHERE post_name IN ('registration-short-form', 'substance-abuse-form', 'psychosocial-evaluation-form', 
                'aviso-de-practicas-de-privacidad', 'consentimiento-informado-para-psicoterapia', 
                'politicas-de-la-practica', 'consentimiento-informado-para-terapia-en-linea', 'consentimiento-para-divulgacion-de-informacion') AND post_type = 'page'"
            );
            $wpdb->query(
                "DROP TABLE IF EXISTS {$wpdb->prefix}cree_consejeria_client_forms_master"
            );
        }       
    }
}

// Plugin Instantiation
if ( class_exists( 'CREE_Consejeria_CFM' ) ){
    register_activation_hook( __FILE__, array( 'CREE_Consejeria_CFM', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'CREE_Consejeria_CFM', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'CREE_Consejeria_CFM', 'uninstall' ) );

    $cree_consejeria_cfm = new CREE_Consejeria_CFM(); 
}