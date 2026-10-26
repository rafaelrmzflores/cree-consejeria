<?php

/**
* Plugin Name: CREE Consejeria CFM
* Plugin URI: https://www.wordpress.org/cree-consejeria
* Description: Sistema de Consejería y Formulario de Intake Encryptado
* Version: 3.0.2
* Requires at least: 5.6
* Requires PHP: 7.0
* Author: Rafael Ramírez Flores
* Author URI: https://www.codigowp.net
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: cree-consejeria
* Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'CREE_Consejeria_CFM' ) ) {

    class CREE_Consejeria_CFM {

        public function __construct() {

            $this->define_constants();

            require_once CREE_CONSEJERIA_CFM_PATH . 'post-types/class.cree-consejeria-cpt.php';
            $Cree_Consejeria_Post_Type = new CREE_Consejeria_Post_Type();

            add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_styles' ], 999 );

            require_once CREE_CONSEJERIA_CFM_PATH . 'shortcodes/class.cree-consejeria-form-shortcode.php';
            $CREE_Consejeria_Form_Shortcode = new CREE_Consejeria_Form_Shortcode();

            require_once CREE_CONSEJERIA_CFM_PATH . 'shortcodes/class.cree-consejeria-note-shortcode.php';
            $Cree_Consejeria_Note_Shortcode = new CREE_Consejeria_Note_Shortcode();

            require_once CREE_CONSEJERIA_CFM_PATH . 'shortcodes/class.cree-consejeria-client-portal-shortcode.php';
            $Cree_Consejeria_Client_Portal = new CREE_Consejeria_Client_Portal();

            require_once CREE_CONSEJERIA_CFM_PATH . 'shortcodes/class.cree-client-login-shortcode.php';
            $Cree_Consejeria_Client_Login = new CREE_Client_Login_Shortcode();

            require_once CREE_CONSEJERIA_CFM_PATH . 'includes/class-cree-security-redirects.php';

            add_action( 'init', array( $this, 'load_textdomain' ) );

            add_action( 'wp_enqueue_scripts', [ $this, 'register_wp_styles' ], 999 );

            // AJAX Handlers
            add_action( 'wp_ajax_cree_submit_intake_form', [ $this, 'cree_ajax_intake_form_handler' ] );
            add_action( 'wp_ajax_nopriv_cree_submit_intake_form', [ $this, 'cree_ajax_intake_form_handler' ] );
            
            add_action( 'wp_ajax_cree_add_new_client', [ $this, 'cree_add_new_client' ] );
            add_action( 'wp_ajax_cree_load_client_notes', [ $this, 'ajax_load_client_notes' ] );
            add_action( 'wp_ajax_cree_save_session_note', [ $this, 'ajax_save_session_note' ] );
            add_action( 'wp_ajax_cree_get_client_form_content', [ $this, 'cree_ajax_get_client_form_content' ] );

            add_action( 'wp_ajax_cree_book_appointment', array( $this, 'ajax_book_appointment' ) );
            add_action( 'wp_ajax_nopriv_cree_book_appointment', array( $this, 'ajax_book_appointment' ) );  
            
            add_action( 'wp_ajax_cree_get_user_appointments', array( $this, 'ajax_get_user_appointments' ) );
            add_action( 'wp_ajax_nopriv_cree_get_user_appointments', array( $this, 'ajax_get_user_appointments' ) );
            
            add_action( 'wp_ajax_cree_update_appointment_status', array( $this, 'ajax_update_appointment_status' ) );

            add_action( 'wp_ajax_cree_save_treatment_goal', array( $this, 'ajax_save_treatment_goal' ) );
            
            add_action( 'wp_ajax_cree_get_client_treatment_goals', array( $this, 'ajax_get_client_treatment_goals' ) );
            add_action( 'wp_ajax_nopriv_cree_get_client_treatment_goals', array( $this, 'ajax_get_client_treatment_goals' ) );
            
            add_action( 'wp_ajax_cree_export_intake_pdf_html', array( $this, 'ajax_export_intake_pdf_html' ) );
            add_action( 'wp_ajax_nopriv_cree_export_intake_pdf_html', array( $this, 'ajax_export_intake_pdf_html' ) );

            add_action( 'wp_ajax_cree_get_single_form_details', array( $this, 'ajax_get_single_form_details' ) );
            add_action( 'wp_ajax_nopriv_cree_get_single_form_details', array( $this, 'ajax_get_single_form_details' ) );

            add_action( 'wp_ajax_cree_assign_form_to_client', array( $this, 'ajax_assign_form_to_client' ) );
            add_action( 'wp_ajax_cree_get_client_profile_data', array( $this, 'ajax_get_client_profile_data' ) );

            add_action( 'wp_ajax_cree_unassign_form', array( $this, 'ajax_unassign_form' ) );

            add_action( 'wp_ajax_cree_get_client_form_template', array( $this, 'ajax_get_client_form_template' ) );

            // Register AJAX hooks for Online Therapy Consent form submission
            add_action( 'wp_ajax_cree_submit_consentimiento_terapia_en_linea', array( $this, 'ajax_submit_consentimiento_terapia_en_linea' ) );
            add_action( 'wp_ajax_nopriv_cree_submit_consentimiento_terapia_en_linea', array( $this, 'ajax_submit_consentimiento_terapia_en_linea' ) );

            add_action( 'wp_ajax_cree_update_session_note', array( $this, 'ajax_update_session_note' ) );

            add_action( 'plugins_loaded', array( $this, 'check_plugin_version' ) );
        }

        public function define_constants() {
            define( 'CREE_CONSEJERIA_CFM_PATH', plugin_dir_path( __FILE__ ) );
            define( 'CREE_CONSEJERIA_CFM_URL', plugin_dir_url( __FILE__ ) );
            define( 'CREE_CONSEJERIA_CFM_VERSION', '3.0.2' );
        }

        /**
         * Activate the plugin
         */
        public static function activate() {
            // Buffer output to prevent "unexpected output during activation" warnings
            ob_start();

            update_option( 'rewrite_rules', '' );

            global $wpdb;

            $forms_table_name = $wpdb->prefix . 'cree_consejeria_client_forms_master';
            $notes_table_name = $wpdb->prefix . 'cree_consejeria_session_notes';
            $appointments_table = $wpdb->prefix . 'cree_consejeria_appointments';
            $goals_table        = $wpdb->prefix . 'cree_consejeria_treatment_goals';
            $charset_collate  = $wpdb->get_charset_collate();

            $cree_consejeria_cfm_db_version = get_option( 'cree_consejeria_cfm_db_version' );
            $forms_table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $forms_table_name ) );

            if ( $forms_table_exists !== $forms_table_name ) {
                $query = "CREATE TABLE $forms_table_name (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    wp_id bigint(20) unsigned NOT NULL,
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
                    KEY wp_id (wp_id),
                    KEY form_type (form_type),
                    KEY client_name (client_name),
                    KEY dob (dob),
                    KEY status (status),
                    KEY user_ip (user_ip)
                    ) $charset_collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $query );
            }

            $notes_table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $notes_table_name ) );
            if ( $notes_table_exists !== $notes_table_name ) {
                $query = "CREATE TABLE $notes_table_name (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    client_id bigint(20) unsigned NOT NULL,
                    therapist_id bigint(20) unsigned NOT NULL,
                    session_date date NOT NULL,
                    note_encrypted blob NOT NULL,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY  (id),
                    KEY client_id (client_id),
                    KEY therapist_id (therapist_id),
                    KEY session_date (session_date)
                ) $charset_collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $query );
            }

            // 1. Appointments & Calendar Management Table
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $appointments_table ) ) !== $appointments_table ) {
                $sql_appointments = "CREATE TABLE $appointments_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    client_id bigint(20) unsigned NOT NULL,
                    therapist_id bigint(20) unsigned NOT NULL,
                    appointment_date datetime NOT NULL,
                    duration_minutes int(11) DEFAULT 50,
                    status varchar(30) DEFAULT 'scheduled', /* scheduled, completed, cancelled, no_show */
                    notes_encrypted blob DEFAULT NULL,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY client_id (client_id),
                    KEY therapist_id (therapist_id),
                    KEY appointment_date (appointment_date),
                    KEY status (status)
                ) $charset_collate;";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql_appointments );
            }

            // 2. Treatment Goals & Clinical Plans Table
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $goals_table ) ) !== $goals_table ) {
                $sql_goals = "CREATE TABLE $goals_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    client_id bigint(20) unsigned NOT NULL,
                    therapist_id bigint(20) unsigned NOT NULL,
                    goal_title varchar(255) NOT NULL,
                    goal_description_encrypted blob DEFAULT NULL,
                    target_date date DEFAULT NULL,
                    status varchar(30) DEFAULT 'in_progress', /* in_progress, achieved, revised, discontinued */
                    progress_percentage int(3) DEFAULT 0,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY client_id (client_id),
                    KEY therapist_id (therapist_id),
                    KEY status (status)
                ) $charset_collate;";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql_goals );
            }

            if ( empty( $cree_consejeria_cfm_db_version ) || version_compare( $cree_consejeria_cfm_db_version, '1.1.0', '<' ) ) {
                update_option( 'cree_consejeria_cfm_db_version', '1.1.0' );
            }

            $pages_to_create = array(
                //Intake Forms
                array( 'slug' => 'registration-short-form', 'title' => __('Formulario de Registro Breve', 'cree-consejeria'), 'form_type' => 'registration_short_form' ),
                array( 'slug' => 'psychosocial-evaluation-form', 'title' => __('Formulario de Evaluación Psicosocial', 'cree-consejeria'), 'form_type' => 'psychosocial_evaluation' ),
                array( 'slug' => 'substance-abuse-form', 'title' => __('Formulario de Abuso de Sustancias', 'cree-consejeria'), 'form_type' => 'substance_abuse_intake' ),
               // Legal & Privacy Forms
                array( 'slug' => 'aviso-de-practicas-de-privacidad', 'title' => __('Aviso de Prácticas de Privacidad', 'cree-consejeria'), 'form_type' => 'aviso_de_practicas_de_privacidad' ), 
                array( 'slug' => 'politicas-de-la-practica', 'title' => __('Aviso de Políticas de la Práctica', 'cree-consejeria'), 'form_type' => 'politicas_de_la_practica' ),
                // Consentimientos Informados
                array( 'slug' => 'consentimiento-informado-para-psicoterapia', 'title' => __('Consentimiento Informado para Psicoterapia', 'cree-consejeria'), 'form_type' => 'consentimiento_informado_para_psicoterapia' ),
                array( 'slug' => 'consentimiento-informado-para-terapia-en-linea', 'title' => __('Consentimiento Informado para Terapia en Línea', 'cree-consejeria'), 'form_type' => 'consentimiento_informado_para_terapia_en_linea' ),
                array( 'slug' => 'consentimiento-para-divulgacion-de-informacion', 'title' => __('Consentimiento para la Divulgación de Información', 'cree-consejeria'), 'form_type' => 'consentimiento_para_divulgacion_de_informacion' ),
                // Client Portal and Login Pages
                array( 'slug' => 'client-portal', 'title' => __('Portal del Cliente', 'cree-consejeria'), 'form_type' => '' ),
                array( 'slug' => 'client-login', 'title' => __('Ingreso del Cliente', 'cree-consejeria'), 'form_type' => '' )
            );

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
                    $content = '';
                    if ( $page_data['slug'] === 'client-portal' ) {
                        $content = '[cree_client_portal]';
                    } elseif ( $page_data['slug'] === 'client-login' ) {
                        $content = '[cree_client_login]';
                    } elseif ( ! empty( $page_data['form_type'] ) ) {
                        $content = '[cree_consejeria_intake_form type="' . $page_data['form_type'] . '"]';
                    }

                    $page = array(
                        'post_title'   => $page_data['title'],
                        'post_name'    => $page_data['slug'],
                        'post_status'  => 'publish',
                        'post_author'  => $current_user_id,
                        'post_type'    => 'page',
                        'post_content' => $content
                    );
                    wp_insert_post( $page );  
                }
            }

            $therapist_portal_page_exists = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND post_type = 'page'",
                    'cree-therapist-portal'
                )
            );

            if ( $therapist_portal_page_exists === null ) {
                $therapist_portal_page = array(
                    'post_title'   => __('CREE Portal del Terapeuta', 'cree-consejeria'),
                    'post_name'    => 'cree-therapist-portal',
                    'post_status'  => 'publish',
                    'post_author'  => $current_user_id,
                    'post_type'    => 'page',
                    'post_content' => '[cree_therapist_portal]'
                );
                wp_insert_post( $therapist_portal_page );  
            }

            // Clear buffer so no accidental output breaks activation
            ob_end_clean();
        }

        public function load_textdomain() {
            load_plugin_textdomain( 'cree-consejeria', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }

        public function register_admin_styles( $hook ) {
            $css_path = CREE_CONSEJERIA_CFM_PATH . 'assets/css/admin.css';
            $css_url  = CREE_CONSEJERIA_CFM_URL . 'assets/css/admin.css';
            $version  = file_exists( $css_path ) ? filemtime( $css_path ) : false;

            wp_register_style( 'cree-consejeria-admin-css', $css_url, [], $version );
        }

        public function register_wp_styles( $hook ) {
            $css_path = CREE_CONSEJERIA_CFM_PATH . 'assets/css/form-styles.css';
            $css_url  = CREE_CONSEJERIA_CFM_URL . 'assets/css/form-styles.css';
            $css_ver  = file_exists( $css_path ) ? filemtime( $css_path ) : false;

            wp_register_style( 'cree-consejeria-form-styles-css', $css_url, array(), $css_ver );

            $form_js_path = CREE_CONSEJERIA_CFM_PATH . 'js/form-handler.js';
            $form_js_url  = CREE_CONSEJERIA_CFM_URL . 'js/form-handler.js';
            $form_js_ver  = file_exists( $form_js_path ) ? filemtime( $form_js_path ) : false;

            wp_register_script( 'cree-consejeria-form-handler-js', $form_js_url, array( 'jquery' ), $form_js_ver, true );
            wp_localize_script( 'cree-consejeria-form-handler-js', 'CREE_INTAKE_FORM', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'cree_secure_form_submission' )
            ) );

            $note_js_path = CREE_CONSEJERIA_CFM_PATH . 'js/note-handler.js';
            $note_js_url  = CREE_CONSEJERIA_CFM_URL . 'js/note-handler.js';
            $note_js_ver  = file_exists( $note_js_path ) ? filemtime( $note_js_path ) : false;

            wp_register_script( 'cree-consejeria-note-handler-js', $note_js_url, array( 'jquery' ), $note_js_ver, true );
            wp_localize_script( 'cree-consejeria-note-handler-js', 'CREE_NOTE_FORM', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'cree_secure_note_nonce' )
            ) );
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

            $result = cree_consejeria_handle_form_submission( $payload );

            if ( is_wp_error( $result ) ) {
                wp_send_json_error( array( 'message' => $result->get_error_message() ) );
            } else {
                $form_type = ! empty( $payload['form_type'] ) ? $payload['form_type'] : '';

                $form_names = array(
                    'substance_abuse_intake'                     => __( 'Substance Abuse Intake', 'cree-consejeria' ),
                    'aviso_de_practicas_de_privacidad'           => __( 'Aviso de Prácticas de Privacidad', 'cree-consejeria' ),
                    'consentimiento_informado_para_psicoterapia' => __( 'Consentimiento Informado para Psicoterapia', 'cree-consejeria' ),
                    'politicas_de_la_practica'                   => __( 'Políticas de la Práctica', 'cree-consejeria' ),
                    'consentimiento_informado_para_terapia_en_linea' => __( 'Consentimiento Informado para Terapia en Línea', 'cree-consejeria' ),
                    'consentimiento_para_divulgacion_de_informacion' => __( 'Consentimiento para Divulgación de Información', 'cree-consejeria' ),
                    'registration_short_form'                   => __( 'Registration Short Form', 'cree-consejeria' ),
                    'psychosocial_evaluation'                   => __( 'Psychosocial Evaluation', 'cree-consejeria' )
                );

                $friendly_name = isset( $form_names[ $form_type ] ) ? $form_names[ $form_type ] : __( 'intake', 'cree-consejeria' );

                // Send email notification to therapist
                $this->send_intake_notification_email( $friendly_name, $payload );

                $success_message = sprintf(
                    __( 'Your %s form has been securely submitted and encrypted successfully.', 'cree-consejeria' ),
                    $friendly_name
                );

                wp_send_json_success( array( 'message' => $success_message ) );
            }

            wp_die();
        }

        private function send_intake_notification_email_deprecated( $form_title, $payload ) {
            $to          = get_option( 'admin_email' );
            $client_name = ! empty( $payload['client_name'] ) ? sanitize_text_field( $payload['client_name'] ) : __( 'Cliente no especificado', 'cree-consejeria' );
            $client_email= ! empty( $payload['email'] ) ? sanitize_email( $payload['email'] ) : 'N/A';

            $subject = sprintf( '[CREE Consejería] Nuevo Formulario Recibido: %s', $form_title );

            $message  = "Hola,\n\n";
            $message .= "Se ha completado y cifrado un nuevo formulario de intake en el portal.\n\n";
            $message .= "--------------------------------------------------\n";
            $message .= "Formulario: " . $form_title . "\n";
            $message .= "Cliente: " . $client_name . "\n";
            $message .= "Correo: " . $client_email . "\n";
            $message .= "Fecha: " . wp_date( 'F j, Y, g:i a' ) . "\n";
            $message .= "--------------------------------------------------\n\n";
            $message .= "Consulte los detalles cifrados en el Portal del Terapeuta: " . home_url( '/cree-therapist-portal/' ) . "\n\n";
            $message .= "Atentamente,\nSistema CREE Consejería";

            $headers = array( 'Content-Type: text/plain; charset=UTF-8' );
            wp_mail( $to, $subject, $message, $headers );
        }

        private function send_intake_notification_email( $form_title, $payload ) {
            $to          = get_option( 'admin_email' );
            $client_name = ! empty( $payload['client_name'] ) ? sanitize_text_field( $payload['client_name'] ) : __( 'Cliente no especificado', 'cree-consejeria' );
            $client_email= ! empty( $payload['email'] ) ? sanitize_email( $payload['email'] ) : 'N/A';

            $subject = sprintf( '[CREE Consejería] Nuevo Formulario Recibido: %s', $form_title );

            $message  = "Hola,\n\n";
            $message .= "Se ha completado y cifrado un nuevo formulario de intake en el portal.\n\n";
            $message .= "--------------------------------------------------\n";
            $message .= "Formulario: " . $form_title . "\n";
            $message .= "Cliente: " . $client_name . "\n";
            $message .= "Correo: " . $client_email . "\n";
            $message .= "Fecha: " . wp_date( 'F j, Y, g:i a' ) . "\n";
            $message .= "--------------------------------------------------\n\n";
            $message .= "Consulte los detalles cifrados en el Portal del Terapeuta: " . home_url( '/cree-therapist-portal/' ) . "\n\n";
            $message .= "Atentamente,\nSistema CREE Consejería";

            // Get site domain for a proper sender address
            $site_name = wp_parse_url( home_url(), PHP_URL_HOST );
            $from_email = 'no-reply@' . $site_name;

            $headers = array(
                'Content-Type: text/plain; charset=UTF-8',
                'From: CREE Consejería <' . $from_email . '>'
            );

            wp_mail( $to, $subject, $message, $headers );
        }

        public function ajax_save_session_note() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
            }

            global $wpdb;
            $notes_table_name = $wpdb->prefix . 'cree_consejeria_session_notes';
            $encryption_key = $this->get_encryption_key();

            $client_id    = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
            $therapist_id = get_current_user_id();
            $session_date = isset( $_POST['session_date'] ) ? sanitize_text_field( $_POST['session_date'] ) : '';
            $note_text    = isset( $_POST['note_text'] ) ? sanitize_textarea_field( $_POST['note_text'] ) : '';

            if ( ! $client_id || empty( $session_date ) || empty( $note_text ) ) {
                wp_send_json_error( array( 'message' => 'Please fill out all required note fields.' ) );
            }

            $inserted = $wpdb->query( $wpdb->prepare(
                "INSERT INTO $notes_table_name (client_id, therapist_id, session_date, note_encrypted) 
                VALUES (%d, %d, %s, AES_ENCRYPT(%s, %s))",
                $client_id,
                $therapist_id,
                $session_date,
                $note_text,
                $encryption_key
            ) );

            

            if ( $inserted ) {
                // Fetch the new database ID so we can assign it to the edit button
                $note_id = $wpdb->insert_id;
                
                // Format the newly created note card HTML for instant DOM injection
                $formatted_date = wp_date( 'F j, Y', strtotime( $session_date ) );
                ob_start();
                ?>
                <div class="cree-note-card" data-note-id="<?php echo intval( $note_id ); ?>" style="background:#fff; border:1px solid #e5e7eb; border-left:4px solid #10b981; padding:14px; margin-bottom:12px; border-radius:6px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div class="cree-note-date-text" style="font-size:12px; font-weight:bold; color:#374151;">📅 Session Date: <?php echo esc_html( $formatted_date ); ?></div>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" onclick="creePrintNote(this)" style="background:#6b7280; color:#fff; border:none; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:bold; cursor:pointer;">🖨️ Print</button>
                            <button type="button" class="cree-edit-note-btn" style="background:#3b82f6; color:#fff; border:none; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:bold; cursor:pointer;">✏️ Edit</button>
                        </div>
                    </div>
                    
                    <!-- Static View Mode -->
                    <div class="cree-note-text-view" style="font-size:13.5px; color:#1f2937; white-space:pre-wrap;"><?php echo esc_html( $note_text ); ?></div>

                    <!-- Edit Mode (Hidden by default) -->
                    <div class="cree-note-edit-form" style="display:none; margin-top:10px;">
                        <textarea class="cree-note-edit-textarea" rows="4" style="width:100%; padding:8px; border-radius:4px; border:1px solid #d1d5db; font-size:13.5px;"><?php echo esc_textarea( $note_text ); ?></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px;">
                            <button type="button" class="cree-save-note-btn" style="background:#10b981; color:#fff; border:none; padding:6px 14px; border-radius:4px; font-weight:bold; cursor:pointer; font-size:12px;">💾 Save</button>
                            <button type="button" class="cree-cancel-note-btn" style="background:#e5e7eb; color:#374151; border:none; padding:6px 12px; border-radius:4px; font-weight:bold; cursor:pointer; font-size:12px;">Cancel</button>
                        </div>
                    </div>
                </div>
                <?php
                $note_html = ob_get_clean();
                wp_send_json_success( array(
                    'message'   => '✔️ Session note saved successfully!',
                    'note_html' => $note_html
                ) );
            } else {
                wp_send_json_error( array( 'message' => 'Database error: ' . $wpdb->last_error ) );
            }
        }
        
        public function ajax_load_client_notes() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( 'Unauthorized' );
            }

            global $wpdb;
            $notes_table_name = $wpdb->prefix . 'cree_consejeria_session_notes';
            $encryption_key = $this->get_encryption_key();
            $client_id        = intval( $_POST['client_id'] );

            $query = $wpdb->prepare(
                "SELECT session_date, CAST(AES_DECRYPT(note_encrypted, %s) AS CHAR) AS decrypted_note 
                FROM $notes_table_name 
                WHERE client_id = %d 
                ORDER BY session_date DESC",
                $encryption_key,
                $client_id
            );

            $notes = $wpdb->get_results( $query );

            if ( empty( $notes ) ) {
                wp_send_json_success( array( 'html' => '<p><em>No previous notes found for this client yet.</em></p>' ) );
            }

            $html = '';
            foreach ( $notes as $note ) {
                $date = wp_date( 'F j, Y', strtotime( $note->session_date ) );
                $html .= '<div style="background:#fff; padding:10px; border-left:4px solid #0073aa; margin-bottom:10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">';
                $html .= '<strong>' . esc_html( $date ) . '</strong>';
                $html .= '<p style="margin:5px 0 0 0; font-size:14px;">' . nl2br( esc_html( $note->decrypted_note ) ) . '</p>';
                $html .= '</div>';
            }

            wp_send_json_success( array( 'html' => $html ) );
        }

        /**
         * AJAX Handler: Create a New Client User + Send Welcome Email
         */
        public function cree_add_new_client() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );

            if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( 'Unauthorized permission.' );
            }

            $first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
            $last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
            $email      = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

            if ( empty( $first_name ) || empty( $last_name ) || empty( $email ) ) {
                wp_send_json_error( 'Please fill out all client fields.' );
            }

            if ( ! is_email( $email ) ) {
                wp_send_json_error( 'Invalid email address provided.' );
            }

            if ( email_exists( $email ) ) {
                wp_send_json_error( 'A client account with this email address already exists.' );
            }

            $username        = $email;
            $random_password = wp_generate_password( 12, false );

            $user_id = wp_create_user( $username, $random_password, $email );

            if ( is_wp_error( $user_id ) ) {
                wp_send_json_error( $user_id->get_error_message() );
            }

            $display_name = trim( $first_name . ' ' . $last_name );

            wp_update_user( array(
                'ID'           => $user_id,
                'first_name'   => $first_name,
                'last_name'    => $last_name,
                'display_name' => $display_name,
                'role'         => 'subscriber'
            ) );

            // Send custom welcome email with password reset link
            $this->send_client_welcome_email( $user_id, $display_name, $email );

            wp_send_json_success( array(
                'user_id'      => $user_id,
                'display_name' => $display_name,
                'email'        => $email,
                'message'      => 'Client created successfully and welcome email sent!'
            ) );
        }

        private function send_client_welcome_email( $user_id, $display_name, $email ) {
            $user = get_userdata( $user_id );
            $key  = get_password_reset_key( $user );

            if ( is_wp_error( $key ) ) {
                return;
            }

            $reset_url = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' );
            $login_url = home_url( '/client-login/' );

            $subject = 'Bienvenido(a) a CREE Consejería - Configura tu Cuenta';

            $message  = "Hola " . $display_name . ",\n\n";
            $message .= "Se ha creado tu cuenta en el portal de CREE Consejería.\n\n";
            $message .= "Para definir tu contraseña e ingresar por primera vez, haz clic en el siguiente enlace:\n";
            $message .= $reset_url . "\n\n";
            $message .= "Acceso al portal: " . $login_url . "\n\n";
            $message .= "Usuario: " . $email . "\n\n";
            $message .= "Atentamente,\nEquipo de CREE Consejería";

            $headers = array( 'Content-Type: text/plain; charset=UTF-8' );
            wp_mail( $email, $subject, $message, $headers );
        }

        /**
         * AJAX Handler: Decrypt form content for the logged-in client owner
         */
        public function cree_ajax_get_client_form_content() {
            check_ajax_referer( 'cree_secure_form_submission', 'security' );

            if ( ! is_user_logged_in() ) {
                wp_send_json_error( 'You must be logged in to view form details.' );
            }

            $current_user_id = get_current_user_id();
            $form_id         = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;

            global $wpdb;
            $forms_table    = $wpdb->prefix . 'cree_consejeria_client_forms_master';
            $encryption_key = $this->get_encryption_key();

            // FIX: Changed user_id to wp_id to match database table schema
            $query = $wpdb->prepare(
                "SELECT CAST(AES_DECRYPT(form_data_encrypted, %s) AS CHAR) AS decrypted_data, created_at 
                FROM {$forms_table} 
                WHERE id = %d AND wp_id = %d",
                $encryption_key,
                $form_id,
                $current_user_id
            );

            $record = $wpdb->get_row( $query );

            if ( ! $record || empty( $record->decrypted_data ) ) {
                wp_send_json_error( 'Form record not found or access denied.' );
            }

            // $form_fields = json_decode( $record->decrypted_data, true );

            // if ( ! is_array( $form_fields ) ) {
            //     $form_fields = maybe_unserialize( $record->decrypted_data );
            // }

            $form_fields = json_decode( $record->decrypted_data, true );
            if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $form_fields ) ) {
                $form_fields = array(); // Failsafe for corrupted data
            }

            $html = '<div class="cree-form-summary-view">';
            if ( is_array( $form_fields ) ) {
                $html .= '<table style="width:100%; border-collapse:collapse;">';
                foreach ( $form_fields as $field_key => $field_val ) {
                    if ( is_array( $field_val ) ) $field_val = implode( ', ', $field_val );
                    
                    $clean_label = ucwords( str_replace( array( '_', '-' ), ' ', $field_key ) );
                    $html .= '<tr style="border-bottom:1px solid #eee;">';
                    $html .= '<td style="padding:6px; font-weight:bold; width:40%;">' . esc_html( $clean_label ) . ':</td>';
                    $html .= '<td style="padding:6px;">' . esc_html( $field_val ) . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
            } else {
                $html .= '<p>' . esc_html( $record->decrypted_data ) . '</p>';
            }
            $html .= '</div>';

            wp_send_json_success( $html );
        }

        /**
         * Book an Appointment (Client or Therapist)
         */
        public function ajax_book_appointment_deprecated_1() {
            // 1. Flexible Nonce Verification
            if ( isset( $_POST['security'] ) ) {
                check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            } elseif ( isset( $_POST['nonce'] ) ) {
                check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            } else {
                wp_send_json_error( array( 'message' => __( 'Security check failed.', 'cree-consejeria' ) ) );
            }

            if ( ! is_user_logged_in() ) {
                wp_send_json_error( array( 'message' => __( 'Authentication required.', 'cree-consejeria' ) ) );
            }

            $client_id        = current_user_can( 'edit_posts' ) && isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : get_current_user_id();
            $therapist_id     = get_current_user_id();
            $appointment_date = isset( $_POST['appointment_date'] ) ? sanitize_text_field( $_POST['appointment_date'] ) : '';
            $notes            = isset( $_POST['notes'] ) ? sanitize_textarea_field( $_POST['notes'] ) : '';

            if ( empty( $appointment_date ) ) {
                wp_send_json_error( array( 'message' => __( 'Please select a valid date and time.', 'cree-consejeria' ) ) );
            }

            global $wpdb;
            $table_name     = $wpdb->prefix . 'cree_consejeria_appointments';
            $encryption_key = $this->get_encryption_key();

            $inserted = $wpdb->query( $wpdb->prepare(
                "INSERT INTO {$table_name} (client_id, therapist_id, appointment_date, status, notes_encrypted) 
                VALUES (%d, %d, %s, 'scheduled', AES_ENCRYPT(%s, %s))",
                $client_id,
                $therapist_id,
                $appointment_date,
                $notes,
                $encryption_key
            ) );

            if ( $inserted ) {
                wp_send_json_success( array( 'message' => __( 'Appointment successfully scheduled.', 'cree-consejeria' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to save appointment in database.', 'cree-consejeria' ) ) );
            }
        }

        /**
         * Book an Appointment (Client or Therapist)
         */
        public function ajax_book_appointment_deprecated_2() {
            // 1. Flexible Nonce Verification
            if ( isset( $_POST['security'] ) ) {
                check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            } elseif ( isset( $_POST['nonce'] ) ) {
                check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            } else {
                wp_send_json_error( array( 'message' => __( 'Security check failed.', 'cree-consejeria' ) ) );
            }

            if ( ! is_user_logged_in() ) {
                wp_send_json_error( array( 'message' => __( 'Authentication required.', 'cree-consejeria' ) ) );
            }

            $client_id        = current_user_can( 'edit_posts' ) && isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : get_current_user_id();
            $therapist_id     = get_current_user_id();
            $appointment_date = isset( $_POST['appointment_date'] ) ? sanitize_text_field( $_POST['appointment_date'] ) : '';
            $notes            = isset( $_POST['notes'] ) ? sanitize_textarea_field( $_POST['notes'] ) : '';

            if ( empty( $appointment_date ) ) {
                wp_send_json_error( array( 'message' => __( 'Please select a valid date and time.', 'cree-consejeria' ) ) );
            }

            // FIX: Convert HTML5 'datetime-local' format (YYYY-MM-DDThh:mm) to MySQL DATETIME format (YYYY-MM-DD hh:mm:ss)
            $appointment_date = str_replace('T', ' ', $appointment_date);
            if ( strlen($appointment_date) == 16 ) {
                $appointment_date .= ':00';
            }

            global $wpdb;
            $table_name     = $wpdb->prefix . 'cree_consejeria_appointments';
            $encryption_key = $this->get_encryption_key();

            $inserted = $wpdb->query( $wpdb->prepare(
                "INSERT INTO {$table_name} (client_id, therapist_id, appointment_date, status, notes_encrypted)
                 VALUES (%d, %d, %s, 'scheduled', AES_ENCRYPT(%s, %s))",
                $client_id,
                $therapist_id,
                $appointment_date,
                $notes,
                $encryption_key
            ) );

            if ( $inserted ) {
                wp_send_json_success( array( 'message' => __( 'Appointment successfully scheduled.', 'cree-consejeria' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to save appointment in database.', 'cree-consejeria' ) ) );
            }
        }

        /**
         * Book an Appointment (Intelligent: Conflict Check & Email)
         */
        public function ajax_book_appointment_deprecated_3() {
            if ( isset( $_POST['security'] ) ) { check_ajax_referer( 'cree_secure_note_nonce', 'security' ); } 
            elseif ( isset( $_POST['nonce'] ) ) { check_ajax_referer( 'cree_secure_form_submission', 'nonce' ); } 
            else { wp_send_json_error( array( 'message' => __( 'Security check failed.', 'cree-consejeria' ) ) ); }

            if ( ! is_user_logged_in() ) wp_send_json_error( array( 'message' => __( 'Authentication required.', 'cree-consejeria' ) ) );

            $client_id        = current_user_can( 'edit_posts' ) && isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : get_current_user_id();
            $therapist_id     = get_current_user_id();
            $appointment_date = isset( $_POST['appointment_date'] ) ? sanitize_text_field( $_POST['appointment_date'] ) : '';
            $duration_minutes = isset( $_POST['duration_minutes'] ) ? intval( $_POST['duration_minutes'] ) : 50;
            $notes            = isset( $_POST['notes'] ) ? sanitize_textarea_field( $_POST['notes'] ) : '';

            if ( empty( $appointment_date ) ) wp_send_json_error( array( 'message' => __( 'Please select a valid date and time.', 'cree-consejeria' ) ) );

            // Convert HTML5 datetime to MySQL format
            $appointment_date = str_replace('T', ' ', $appointment_date);
            if ( strlen($appointment_date) == 16 ) $appointment_date .= ':00';

            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_appointments';
            $encryption_key = $this->get_encryption_key();

            // CONFLICT CHECK: Does this time block overlap with an existing scheduled appointment for this therapist?
            $overlap = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM {$table_name} 
                 WHERE therapist_id = %d AND status = 'scheduled' 
                 AND appointment_date < DATE_ADD(%s, INTERVAL %d MINUTE) 
                 AND DATE_ADD(appointment_date, INTERVAL duration_minutes MINUTE) > %s",
                $therapist_id, $appointment_date, $duration_minutes, $appointment_date
            ) );

            if ( $overlap ) {
                wp_send_json_error( array( 'message' => __( 'Schedule Conflict: You already have an active appointment booked during this time block.', 'cree-consejeria' ) ) );
            }

            $inserted = $wpdb->query( $wpdb->prepare(
                "INSERT INTO {$table_name} (client_id, therapist_id, appointment_date, duration_minutes, status, notes_encrypted)
                 VALUES (%d, %d, %s, %d, 'scheduled', AES_ENCRYPT(%s, %s))",
                $client_id, $therapist_id, $appointment_date, $duration_minutes, $notes, $encryption_key
            ) );

            if ( $inserted ) {
                // AUTO-EMAIL NOTIFICATION
                $client_info = get_userdata( $client_id );
                if ( $client_info ) {
                    $formatted_time = wp_date( 'l, F j, Y \a\t g:i A', strtotime( $appointment_date ) );
                    $subject = '[CREE Consejería] Nueva Cita Programada';
                    $message = "Hola {$client_info->first_name},\n\nSu terapeuta ha programado una nueva sesión para usted.\n\nFecha y Hora: {$formatted_time}\nDuración: {$duration_minutes} minutos\nNotas / Enlace: {$notes}\n\nSi necesita reprogramar, por favor hágalo con al menos 24 horas de antelación accediendo al Portal del Cliente.\n\nAtentamente,\nEquipo de CREE Consejería";
                    
                    wp_mail( $client_info->user_email, $subject, $message, array('Content-Type: text/plain; charset=UTF-8') );
                }
                wp_send_json_success( array( 'message' => __( 'Appointment successfully scheduled and client notified.', 'cree-consejeria' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to save appointment in database.', 'cree-consejeria' ) ) );
            }
        }

        /**
         * Book or Edit an Appointment (Intelligent: Conflict Check & Email)
         */
        public function ajax_book_appointment() {
            if ( isset( $_POST['security'] ) ) { check_ajax_referer( 'cree_secure_note_nonce', 'security' ); } 
            elseif ( isset( $_POST['nonce'] ) ) { check_ajax_referer( 'cree_secure_form_submission', 'nonce' ); } 
            else { wp_send_json_error( array( 'message' => __( 'Security check failed.', 'cree-consejeria' ) ) ); }

            if ( ! is_user_logged_in() ) wp_send_json_error( array( 'message' => __( 'Authentication required.', 'cree-consejeria' ) ) );

            $client_id        = current_user_can( 'edit_posts' ) && isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : get_current_user_id();
            $therapist_id     = get_current_user_id();
            $appointment_id   = isset( $_POST['appointment_id'] ) ? intval( $_POST['appointment_id'] ) : 0;
            $appointment_date = isset( $_POST['appointment_date'] ) ? sanitize_text_field( $_POST['appointment_date'] ) : '';
            $duration_minutes = isset( $_POST['duration_minutes'] ) ? intval( $_POST['duration_minutes'] ) : 50;
            $notes            = isset( $_POST['notes'] ) ? sanitize_textarea_field( $_POST['notes'] ) : '';

            if ( empty( $appointment_date ) ) wp_send_json_error( array( 'message' => __( 'Please select a valid date and time.', 'cree-consejeria' ) ) );

            $appointment_date = str_replace('T', ' ', $appointment_date);
            if ( strlen($appointment_date) == 16 ) $appointment_date .= ':00';

            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_appointments';
            $encryption_key = $this->get_encryption_key();

            // CONFLICT CHECK: Ignore the current appointment if we are editing it
            $overlap_query = $wpdb->prepare(
                "SELECT id FROM {$table_name} 
                 WHERE therapist_id = %d AND status = 'scheduled' AND id != %d 
                 AND appointment_date < DATE_ADD(%s, INTERVAL %d MINUTE) 
                 AND DATE_ADD(appointment_date, INTERVAL duration_minutes MINUTE) > %s",
                $therapist_id, $appointment_id, $appointment_date, $duration_minutes, $appointment_date
            );
            
            if ( $wpdb->get_var( $overlap_query ) ) {
                wp_send_json_error( array( 'message' => __( 'Schedule Conflict: You already have an active appointment booked during this time block.', 'cree-consejeria' ) ) );
            }

            if ( $appointment_id > 0 ) {
                // UPDATE EXISTING APPOINTMENT
                $result = $wpdb->query( $wpdb->prepare(
                    "UPDATE {$table_name} SET appointment_date = %s, duration_minutes = %d, notes_encrypted = AES_ENCRYPT(%s, %s) WHERE id = %d",
                    $appointment_date, $duration_minutes, $notes, $encryption_key, $appointment_id
                ) );
                $success_msg = 'Appointment successfully updated.';
            } else {
                // INSERT NEW APPOINTMENT
                $result = $wpdb->query( $wpdb->prepare(
                    "INSERT INTO {$table_name} (client_id, therapist_id, appointment_date, duration_minutes, status, notes_encrypted)
                     VALUES (%d, %d, %s, %d, 'scheduled', AES_ENCRYPT(%s, %s))",
                    $client_id, $therapist_id, $appointment_date, $duration_minutes, $notes, $encryption_key
                ) );
                $success_msg = 'Appointment successfully scheduled and client notified.';
            }

            if ( $result !== false ) {
                // AUTO-EMAIL NOTIFICATION
                $client_info = get_userdata( $client_id );
                if ( $client_info ) {
                    $formatted_time = wp_date( 'l, F j, Y \a\t g:i A', strtotime( $appointment_date ) );
                    $subject = $appointment_id > 0 ? '[CREE Consejería] Actualización de Cita' : '[CREE Consejería] Nueva Cita Programada';
                    $message = "Hola {$client_info->first_name},\n\nSu sesión ha sido confirmada/actualizada.\n\nFecha y Hora: {$formatted_time}\nDuración: {$duration_minutes} minutos\nNotas / Enlace: {$notes}\n\nSi necesita reprogramar, por favor hágalo con al menos 24 horas de antelación accediendo al Portal del Cliente.\n\nAtentamente,\nEquipo de CREE Consejería";
                    wp_mail( $client_info->user_email, $subject, $message, array('Content-Type: text/plain; charset=UTF-8') );
                }
                wp_send_json_success( array( 'message' => __( $success_msg, 'cree-consejeria' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to save appointment in database.', 'cree-consejeria' ) ) );
            }
        }

        /**
         * Fetch Appointments List with Quick Actions
         */
        public function ajax_get_user_appointments() {
            if ( isset( $_POST['security'] ) ) {
                check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            } elseif ( isset( $_POST['nonce'] ) ) {
                check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            } else {
                wp_send_json_error( array( 'message' => __( 'Security verification failed.', 'cree-consejeria' ) ) );
            }

            if ( ! is_user_logged_in() ) {
                wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'cree-consejeria' ) ) );
            }

            global $wpdb;
            $table_name     = $wpdb->prefix . 'cree_consejeria_appointments';
            $encryption_key = $this->get_encryption_key();
            $current_user   = get_current_user_id();
            $is_therapist   = current_user_can( 'edit_posts' );

            if ( $is_therapist ) {
                $client_filter = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
                $where_clause  = $client_filter > 0 ? $wpdb->prepare( "WHERE a.client_id = %d", $client_filter ) : "";
                $query         = "SELECT a.*, CAST(AES_DECRYPT(a.notes_encrypted, '$encryption_key') AS CHAR) AS decrypted_notes, u.display_name AS client_name 
                                FROM {$table_name} a 
                                LEFT JOIN {$wpdb->users} u ON a.client_id = u.ID 
                                {$where_clause} 
                                ORDER BY a.appointment_date DESC";
            } else {
                $query = $wpdb->prepare(
                    "SELECT a.*, CAST(AES_DECRYPT(a.notes_encrypted, %s) AS CHAR) AS decrypted_notes 
                    FROM {$table_name} a 
                    WHERE a.client_id = %d 
                    ORDER BY a.appointment_date DESC",
                    $encryption_key,
                    $current_user
                );
            }

            $results = $wpdb->get_results( $query );

            ob_start();
            if ( empty( $results ) ) {
                echo '<p><em>' . esc_html__( 'No appointments scheduled yet.', 'cree-consejeria' ) . '</em></p>';
            } else {
                echo '<table class="widefat striped" style="width:100%; border-collapse:collapse; margin-top:10px;">';
                echo '<thead><tr><th style="padding:10px;">' . esc_html__( 'Date & Time', 'cree-consejeria' ) . '</th>';
                if ( $is_therapist ) {
                    echo '<th style="padding:10px;">' . esc_html__( 'Client', 'cree-consejeria' ) . '</th>';
                }
                echo '<th style="padding:10px;">' . esc_html__( 'Status', 'cree-consejeria' ) . '</th>';
                echo '<th style="padding:10px;">' . esc_html__( 'Notes', 'cree-consejeria' ) . '</th>';
                if ( $is_therapist ) {
                    echo '<th style="padding:10px;">' . esc_html__( 'Actions', 'cree-consejeria' ) . '</th>';
                }
                echo '</tr></thead><tbody>';

                foreach ( $results as $row ) {
                    $formatted_date = wp_date( 'F j, Y — g:i a', strtotime( $row->appointment_date ) );
                    $status_badge   = '<span style="padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; text-transform: uppercase; ' .
                                    ( $row->status === 'completed' ? 'background: #d1fae5; color: #065f46;' : ( $row->status === 'cancelled' ? 'background: #fee2e2; color: #991b1b;' : 'background: #e0f2fe; color: #075985;' ) ) .
                                    '">' . esc_html( ucfirst( $row->status ) ) . '</span>';

                    echo '<tr>';
                    echo '<td style="padding:10px;"><strong>' . esc_html( $formatted_date ) . '</strong></td>';
                    if ( $is_therapist ) {
                        echo '<td style="padding:10px;">' . esc_html( $row->client_name ? $row->client_name : 'Client #' . $row->client_id ) . '</td>';
                    }
                    echo '<td style="padding:10px;">' . $status_badge . '</td>';
                    echo '<td style="padding:10px;">' . esc_html( $row->decrypted_notes ? $row->decrypted_notes : '-' ) . '</td>';
                    
                    // THIS OUTPUTS THE ACTION BUTTONS THAT CALL THE JAVASCRIPT FUNCTION
                    if ( $is_therapist ) {
                        echo '<td style="padding:10px;">';
                        if ( $row->status !== 'completed' ) {
                            echo '<button type="button" onclick="creeUpdateAppointmentStatus(' . intval( $row->id ) . ', \'completed\')" style="background: #10b981; color: #fff; border: none; padding: 4px 8px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 12px; margin-right: 5px;">✓ Complete</button>';
                        }
                        if ( $row->status !== 'cancelled' ) {
                            echo '<button type="button" onclick="creeUpdateAppointmentStatus(' . intval( $row->id ) . ', \'cancelled\')" style="background: #ef4444; color: #fff; border: none; padding: 4px 8px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 12px;">✕ Cancel</button>';
                        }
                        echo '</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }

            wp_send_json_success( array( 'html' => ob_get_clean() ) );
        }

        public function ajax_update_appointment_status_deprecated_1() {
            // Nonce verification
            if ( isset( $_POST['security'] ) ) {
                check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            } elseif ( isset( $_POST['nonce'] ) ) {
                check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            } else {
                wp_send_json_error( array( 'message' => __( 'Security check failed.', 'cree-consejeria' ) ) );
            }

            if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'cree-consejeria' ) ) );
            }

            $appointment_id = isset( $_POST['appointment_id'] ) ? intval( $_POST['appointment_id'] ) : 0;
            $status         = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';

            if ( ! $appointment_id || ! in_array( $status, array( 'scheduled', 'completed', 'cancelled' ), true ) ) {
                wp_send_json_error( array( 'message' => __( 'Invalid parameters provided.', 'cree-consejeria' ) ) );
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_appointments';

            $updated = $wpdb->update(
                $table_name,
                array( 'status' => $status ),
                array( 'id' => $appointment_id ),
                array( '%s' ),
                array( '%d' )
            );

            if ( false !== $updated ) {
                wp_send_json_success( array( 'message' => __( 'Appointment updated successfully.', 'cree-consejeria' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Database update failed.', 'cree-consejeria' ) ) );
            }
        }

        public function ajax_update_appointment_status() {
            if ( isset( $_POST['security'] ) ) { check_ajax_referer( 'cree_secure_note_nonce', 'security' ); } 
            elseif ( isset( $_POST['nonce'] ) ) { check_ajax_referer( 'cree_secure_form_submission', 'nonce' ); } 
            else { wp_send_json_error( array( 'message' => __( 'Security check failed.', 'cree-consejeria' ) ) ); }

            if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'cree-consejeria' ) ) );

            $appointment_id = isset( $_POST['appointment_id'] ) ? intval( $_POST['appointment_id'] ) : 0;
            $status         = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';

            if ( ! $appointment_id || ! in_array( $status, array( 'scheduled', 'completed', 'cancelled' ), true ) ) {
                wp_send_json_error( array( 'message' => __( 'Invalid parameters provided.', 'cree-consejeria' ) ) );
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_appointments';
            
            // Fetch appointment info before updating to get the date and client ID for the email
            $appt = $wpdb->get_row( $wpdb->prepare( "SELECT client_id, appointment_date FROM {$table_name} WHERE id = %d", $appointment_id ) );

            $updated = $wpdb->update(
                $table_name,
                array( 'status' => $status ),
                array( 'id' => $appointment_id ),
                array( '%s' ),
                array( '%d' )
            );

            if ( false !== $updated ) {
                // If it was cancelled, fire an automated email to the client
                if ( $status === 'cancelled' && $appt ) {
                    $client_info = get_userdata( $appt->client_id );
                    if ( $client_info ) {
                        $formatted_time = wp_date( 'l, F j, Y \a\t g:i A', strtotime( $appt->appointment_date ) );
                        $subject = '[CREE Consejería] Aviso de Cancelación de Cita';
                        $message = "Hola {$client_info->first_name},\n\nLe informamos que su sesión programada para el {$formatted_time} ha sido cancelada por su terapeuta.\n\nPor favor, acceda a su Portal del Cliente o contáctenos para programar una nueva fecha.\n\nAtentamente,\nEquipo de CREE Consejería";
                        wp_mail( $client_info->user_email, $subject, $message, array('Content-Type: text/plain; charset=UTF-8') );
                    }
                }
                wp_send_json_success( array( 'message' => __( 'Appointment updated successfully.', 'cree-consejeria' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Database update failed.', 'cree-consejeria' ) ) );
            }
        }

        /**
         * Add / Update Treatment Goal (Therapist Only)
         */
        public function ajax_save_treatment_goal() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );

            if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'cree-consejeria' ) ) );
            }

            $client_id           = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
            $goal_title          = isset( $_POST['goal_title'] ) ? sanitize_text_field( $_POST['goal_title'] ) : '';
            $goal_description    = isset( $_POST['goal_description'] ) ? sanitize_textarea_field( $_POST['goal_description'] ) : '';
            $target_date         = isset( $_POST['target_date'] ) ? sanitize_text_field( $_POST['target_date'] ) : '';
            $progress_percentage = isset( $_POST['progress_percentage'] ) ? intval( $_POST['progress_percentage'] ) : 0;
            $status              = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'in_progress';
            $therapist_id        = get_current_user_id();

            if ( ! $client_id || empty( $goal_title ) ) {
                wp_send_json_error( array( 'message' => __( 'Client ID and Goal Title are required.', 'cree-consejeria' ) ) );
            }

            global $wpdb;
            $table_name     = $wpdb->prefix . 'cree_consejeria_treatment_goals';
            $encryption_key = $this->get_encryption_key();

            $inserted = $wpdb->query( $wpdb->prepare(
                "INSERT INTO {$table_name} (client_id, therapist_id, goal_title, goal_description_encrypted, target_date, progress_percentage, status) 
                VALUES (%d, %d, %s, AES_ENCRYPT(%s, %s), %s, %d, %s)",
                $client_id,
                $therapist_id,
                $goal_title,
                $goal_description,
                $encryption_key,
                $target_date,
                $progress_percentage,
                $status
            ) );

            if ( $inserted ) {
                wp_send_json_success( array( 'message' => __( 'Treatment goal added successfully.', 'cree-consejeria' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Database insertion failed.', 'cree-consejeria' ) ) );
            }
        }

        /**
         * Get Client Treatment Goals List
         */
       public function ajax_get_client_treatment_goals() {
            // 1. Flexible Security Verification (Catches either nonce)
            if ( isset( $_POST['security'] ) ) {
                check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            } elseif ( isset( $_POST['nonce'] ) ) {
                check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            } else {
                wp_send_json_error( array( 'message' => __( 'Security nonce missing.', 'cree-consejeria' ) ) );
            }

            if ( ! is_user_logged_in() ) {
                wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'cree-consejeria' ) ) );
            }

            // 2. Capture Client ID directly from POST request
            $client_id = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;

            if ( ! $client_id ) {
                wp_send_json_error( array( 'message' => __( 'No client selected.', 'cree-consejeria' ) ) );
            }

            global $wpdb;
            $table_name     = $wpdb->prefix . 'cree_consejeria_treatment_goals';
            $encryption_key = $this->get_encryption_key();

            // 3. Query DB and decrypt description blob
            $query = $wpdb->prepare(
                "SELECT id, goal_title, CAST(AES_DECRYPT(goal_description_encrypted, %s) AS CHAR) AS description, target_date, progress_percentage, status 
                FROM {$table_name} 
                WHERE client_id = %d 
                ORDER BY created_at DESC",
                $encryption_key,
                $client_id
            );

            $results = $wpdb->get_results( $query );

            ob_start();
            if ( empty( $results ) ) {
                echo '<p><em>' . esc_html__( 'No treatment goals recorded for this client yet.', 'cree-consejeria' ) . '</em></p>';
            } else {
                foreach ( $results as $goal ) {
                    ?>
                    <div class="cree-goal-card" style="background: #ffffff; border: 1px solid #e5e7eb; border-left: 4px solid #2563eb; padding: 16px; margin-bottom: 12px; border-radius: 6px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <strong style="font-size: 15px; color: #111827;"><?php echo esc_html( $goal->goal_title ); ?></strong>
                            <span style="font-size: 12px; font-weight: bold; padding: 2px 8px; border-radius: 4px; background: #eff6ff; color: #1e40af;">
                                <?php echo esc_html( ucfirst( str_replace( '_', ' ', $goal->status ) ) ); ?>
                            </span>
                        </div>
                        <p style="margin: 0 0 10px 0; font-size: 13.5px; color: #4b5563;"><?php echo esc_html( $goal->description ? $goal->description : '(No details provided)' ); ?></p>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="flex-grow: 1; background: #e5e7eb; border-radius: 4px; height: 8px; overflow: hidden;">
                                <div style="width: <?php echo intval( $goal->progress_percentage ); ?>%; background: #2563eb; height: 100%;"></div>
                            </div>
                            <span style="font-size: 12px; font-weight: bold; color: #374151;"><?php echo intval( $goal->progress_percentage ); ?>%</span>
                        </div>
                        <?php if ( ! empty( $goal->target_date ) ) : ?>
                            <div style="font-size: 11px; color: #6b7280; margin-top: 6px;">🎯 Target Date: <?php echo esc_html( $goal->target_date ); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php
                }
            }

            wp_send_json_success( array( 'html' => ob_get_clean() ) );
        }

        /**
         * Export Intake Form HTML Payload for PDF Printing
         */
        public function ajax_export_intake_pdf_html() {
            // 1. Flexible Nonce Verification
            if ( isset( $_POST['security'] ) ) {
                check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            } elseif ( isset( $_POST['nonce'] ) ) {
                check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            } else {
                wp_send_json_error( 'Security check failed.' );
            }

            if ( ! is_user_logged_in() ) {
                wp_send_json_error( 'Unauthorized access.' );
            }

            $form_id      = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
            $current_user = get_current_user_id();

            if ( ! $form_id ) {
                wp_send_json_error( 'Invalid Form ID.' );
            }

            global $wpdb;
            $forms_table    = $wpdb->prefix . 'cree_consejeria_client_forms_master';
            $encryption_key = $this->get_encryption_key();

            // $where_clause = current_user_can( 'edit_posts' ) 
            //     ? $wpdb->prepare( "WHERE id = %d", $form_id ) 
            //     : $wpdb->prepare( "WHERE id = %d AND wp_id = %d", $form_id, $current_user );

            // $record = $wpdb->get_row( "SELECT *, CAST(AES_DECRYPT(form_data_encrypted, '$encryption_key') AS CHAR) AS decrypted_data FROM {$forms_table} {$where_clause}" );

            // In ajax_get_single_form_details and ajax_export_intake_pdf_html
            $query = current_user_can( 'edit_posts' )
                ? $wpdb->prepare( "SELECT *, CAST(AES_DECRYPT(form_data_encrypted, %s) AS CHAR) AS decrypted_data FROM {$forms_table} WHERE id = %d", $encryption_key, $form_id )
                : $wpdb->prepare( "SELECT *, CAST(AES_DECRYPT(form_data_encrypted, %s) AS CHAR) AS decrypted_data FROM {$forms_table} WHERE id = %d AND wp_id = %d", $encryption_key, $form_id, $current_user );

            $record = $wpdb->get_row( $query );

            if ( ! $record || empty( $record->decrypted_data ) ) {
                wp_send_json_error( 'Record not found or access denied.' );
            }

            // $form_fields = json_decode( $record->decrypted_data, true );
            // if ( ! is_array( $form_fields ) ) {
            //     $form_fields = maybe_unserialize( $record->decrypted_data );
            // }

            $form_fields = json_decode( $record->decrypted_data, true );
            if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $form_fields ) ) {
                $form_fields = array(); // Failsafe for corrupted data
            }

            ob_start();
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>CREE Consejería — Form Evaluation Record #<?php echo intval( $record->id ); ?></title>
                <style>
                    body { font-family: Helvetica, Arial, sans-serif; margin: 40px; color: #111; line-height: 1.5; }
                    .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 30px; }
                    .header h1 { margin: 0; color: #1e3a8a; font-size: 22px; }
                    .header p { margin: 4px 0 0 0; color: #4b5563; font-size: 13px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                    th, td { border: 1px solid #d1d5db; padding: 10px; font-size: 13px; text-align: left; }
                    th { background-color: #f3f4f6; color: #111827; }
                    .footer { margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 15px; font-size: 11px; color: #6b7280; text-align: center; }
                    @media print {
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="no-print" style="margin-bottom: 20px; text-align: right;">
                    <button onclick="window.print()" style="background: #2563eb; color: #fff; border: none; padding: 10px 18px; border-radius: 4px; font-weight: bold; cursor: pointer;">🖨️ Print / Save as PDF</button>
                </div>

                <div class="header">
                    <h1>CREE CONSEJERÍA — Evaluation Record</h1>
                    <p>Confidential Clinical Document | Form Type: <?php echo esc_html( ucwords( str_replace( '_', ' ', $record->form_type ) ) ); ?></p>
                </div>

                <table>
                    <tr><th>Client Name:</th><td><?php echo esc_html( $record->client_name ); ?></td></tr>
                    <tr><th>Date of Birth:</th><td><?php echo esc_html( $record->dob ); ?></td></tr>
                    <tr><th>Email Address:</th><td><?php echo esc_html( $record->email ); ?></td></tr>
                    <tr><th>Submission Date:</th><td><?php echo esc_html( $record->created_at ); ?></td></tr>
                    <tr><th>Digital Signature:</th><td><?php echo esc_html( $record->signature ); ?></td></tr>
                </table>

                <h3 style="margin-top: 30px; color: #1f2937;">Form Responses</h3>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40%;">Field / Question</th>
                            <th style="width: 60%;">Client Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( is_array( $form_fields ) ) : ?>
                            <?php foreach ( $form_fields as $key => $val ) : ?>
                                <?php if ( is_array( $val ) ) $val = implode( ', ', $val ); ?>
                                <tr>
                                    <td><strong><?php echo esc_html( ucwords( str_replace( array( '_', '-' ), ' ', $key ) ) ); ?></strong></td>
                                    <td><?php echo esc_html( $val ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="footer">
                    Confidential Medical Record — Encrypted Storage Layer | CREE Consejería CFM
                </div>
            </body>
            </html>
            <?php
            wp_send_json_success( array( 'html' => ob_get_clean() ) );
        }



        /**
         * Checks for missing plugin database tables and creates them on admin page refresh.
         */
        public function maybe_create_tables() {
            if ( ! current_user_can( 'activate_plugins' ) ) {
                return;
            }

            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            // 1. Appointments Table
            $appointments_table = $wpdb->prefix . 'cree_consejeria_appointments';
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $appointments_table ) ) !== $appointments_table ) {
                $sql_appointments = "CREATE TABLE $appointments_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    client_id bigint(20) unsigned NOT NULL,
                    therapist_id bigint(20) unsigned NOT NULL,
                    appointment_date datetime NOT NULL,
                    duration_minutes int(11) DEFAULT 50,
                    status varchar(30) DEFAULT 'scheduled',
                    notes_encrypted blob DEFAULT NULL,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY client_id (client_id),
                    KEY therapist_id (therapist_id),
                    KEY appointment_date (appointment_date),
                    KEY status (status)
                ) $charset_collate;";
                dbDelta( $sql_appointments );
            }

            // 2. Treatment Goals Table
            $goals_table = $wpdb->prefix . 'cree_consejeria_treatment_goals';
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $goals_table ) ) !== $goals_table ) {
                $sql_goals = "CREATE TABLE $goals_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    client_id bigint(20) unsigned NOT NULL,
                    therapist_id bigint(20) unsigned NOT NULL,
                    goal_title varchar(255) NOT NULL,
                    goal_description_encrypted blob DEFAULT NULL,
                    target_date date DEFAULT NULL,
                    status varchar(30) DEFAULT 'in_progress',
                    progress_percentage int(3) DEFAULT 0,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY client_id (client_id),
                    KEY therapist_id (therapist_id),
                    KEY status (status)
                ) $charset_collate;";
                dbDelta( $sql_goals );
            }

            // 3. Client Forms Master Table
            $forms_table = $wpdb->prefix . 'cree_consejeria_client_forms_master';
            
            // FIX: Forcefully drop the strict unique index on wp_id to allow multiple form submissions per client.
            // Suppress errors briefly so the script doesn't crash if the index was already dropped.
            $wpdb->suppress_errors(true);
            $wpdb->query("ALTER TABLE {$forms_table} DROP INDEX wp_id");
            $wpdb->suppress_errors(false);

            $sql_forms = "CREATE TABLE $forms_table (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                wp_id bigint(20) unsigned NOT NULL,
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
                PRIMARY KEY (id),
                KEY wp_id (wp_id),
                KEY form_type (form_type)
            ) $charset_collate;";
            dbDelta( $sql_forms );

            // 4. Session Notes Table
            $notes_table = $wpdb->prefix . 'cree_consejeria_session_notes';
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $notes_table ) ) !== $notes_table ) {
                $sql_notes = "CREATE TABLE $notes_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    client_id bigint(20) unsigned NOT NULL,
                    therapist_id bigint(20) unsigned NOT NULL,
                    session_date date NOT NULL,
                    note_encrypted blob NOT NULL,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY client_id (client_id),
                    KEY therapist_id (therapist_id)
                ) $charset_collate;";
                dbDelta( $sql_notes );
            }
        }

        public function check_plugin_version() {
            if ( get_option( 'cree_consejeria_cfm_version' ) !== CREE_CONSEJERIA_CFM_VERSION ) {
                $this->maybe_create_tables();
                update_option( 'cree_consejeria_cfm_version', CREE_CONSEJERIA_CFM_VERSION );
            }
        }

        /**
         * AJAX Handler: Fetch Decrypted Single Form Details for Client
         */
        public function ajax_get_single_form_details() {
            // Nonce Check
            if ( isset( $_POST['nonce'] ) ) {
                check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            } elseif ( isset( $_POST['security'] ) ) {
                check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            } else {
                wp_send_json_error( array( 'message' => __( 'Security check failed.', 'cree-consejeria' ) ) );
            }

            if ( ! is_user_logged_in() ) {
                wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'cree-consejeria' ) ) );
            }

            $form_id      = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
            $current_user = get_current_user_id();

            if ( ! $form_id ) {
                wp_send_json_error( array( 'message' => __( 'Invalid Form ID.', 'cree-consejeria' ) ) );
            }

            global $wpdb;
            $forms_table    = $wpdb->prefix . 'cree_consejeria_client_forms_master';
            $encryption_key = $this->get_encryption_key();

            // Ensure clients can only view THEIR OWN forms (security rule)
            // $where_clause = current_user_can( 'edit_posts' ) 
            //     ? $wpdb->prepare( "WHERE id = %d", $form_id ) 
            //     : $wpdb->prepare( "WHERE id = %d AND wp_id = %d", $form_id, $current_user );

            // $record = $wpdb->get_row( "SELECT *, CAST(AES_DECRYPT(form_data_encrypted, '$encryption_key') AS CHAR) AS decrypted_data FROM {$forms_table} {$where_clause}" );

            // In ajax_get_single_form_details and ajax_export_intake_pdf_html
            $query = current_user_can( 'edit_posts' )
                ? $wpdb->prepare( "SELECT *, CAST(AES_DECRYPT(form_data_encrypted, %s) AS CHAR) AS decrypted_data FROM {$forms_table} WHERE id = %d", $encryption_key, $form_id )
                : $wpdb->prepare( "SELECT *, CAST(AES_DECRYPT(form_data_encrypted, %s) AS CHAR) AS decrypted_data FROM {$forms_table} WHERE id = %d AND wp_id = %d", $encryption_key, $form_id, $current_user );

            $record = $wpdb->get_row( $query );

            if ( ! $record || empty( $record->decrypted_data ) ) {
                wp_send_json_error( array( 'message' => __( 'Form record not found or access restricted.', 'cree-consejeria' ) ) );
            }

            // $form_fields = json_decode( $record->decrypted_data, true );
            // if ( ! is_array( $form_fields ) ) {
            //     $form_fields = maybe_unserialize( $record->decrypted_data );
            // }

            $form_fields = json_decode( $record->decrypted_data, true );
            if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $form_fields ) ) {
                $form_fields = array(); // Failsafe for corrupted data
            }

            ob_start();
            ?>
            <table class="widefat striped" style="width:100%; border-collapse:collapse; margin-top:10px;">
                <tbody>
                    <tr>
                        <td style="width:35%;"><strong>Form Type:</strong></td>
                        <td><?php echo esc_html( ucwords( str_replace( '_', ' ', $record->form_type ) ) ); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Submitted Date:</strong></td>
                        <td><?php echo esc_html( $record->created_at ); ?></td>
                    </tr>
                    <?php if ( is_array( $form_fields ) ) : ?>
                        <?php foreach ( $form_fields as $key => $val ) : ?>
                            <?php if ( is_array( $val ) ) $val = implode( ', ', $val ); ?>
                            <tr>
                                <td><strong><?php echo esc_html( ucwords( str_replace( array( '_', '-' ), ' ', $key ) ) ); ?>:</strong></td>
                                <td><?php echo esc_html( $val ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php

            wp_send_json_success( array( 'html' => ob_get_clean() ) );
        }

        /**
         * AJAX Handler: Assign a Form to a Client
         */
        public function ajax_assign_form_to_client() {
            if ( isset( $_POST['security'] ) ) {
                check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            } elseif ( isset( $_POST['nonce'] ) ) {
                check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            } else {
                wp_send_json_error( array( 'message' => __( 'Security check failed.', 'cree-consejeria' ) ) );
            }

            if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'cree-consejeria' ) ) );
            }

            $client_id = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
            $form_type = isset( $_POST['form_type'] ) ? sanitize_text_field( $_POST['form_type'] ) : '';

            if ( ! $client_id || empty( $form_type ) ) {
                wp_send_json_error( array( 'message' => __( 'Please select a valid client and form.', 'cree-consejeria' ) ) );
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_assigned_forms';

            // Auto-create assigned forms tracking table if missing
            $wpdb->query( "CREATE TABLE IF NOT EXISTS {$table_name} (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                client_id BIGINT(20) NOT NULL,
                therapist_id BIGINT(20) NOT NULL,
                form_type VARCHAR(100) NOT NULL,
                status VARCHAR(50) DEFAULT 'assigned',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;" );

            $inserted = $wpdb->insert(
                $table_name,
                array(
                    'client_id'    => $client_id,
                    'therapist_id' => get_current_user_id(),
                    'form_type'    => $form_type,
                    'status'       => 'assigned',
                    'created_at'   => current_time( 'mysql' ),
                ),
                array( '%d', '%d', '%s', '%s', '%s' )
            );

            if ( $inserted ) {
                wp_send_json_success( array( 'message' => __( 'Form assigned to client successfully.', 'cree-consejeria' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to assign form to database.', 'cree-consejeria' ) ) );
            }
        }

        /**
         * AJAX Handler: Fetch Client Profile Tabs HTML
         */
        public function ajax_get_client_profile_data() {
            // 1. Flexible Nonce Check
            if ( isset( $_POST['security'] ) ) {
                check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            } elseif ( isset( $_POST['nonce'] ) ) {
                check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            } else {
                wp_send_json_error( array( 'message' => __( 'Security check failed.', 'cree-consejeria' ) ) );
            }

            if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'cree-consejeria' ) ) );
            }

            $client_id = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
            if ( ! $client_id ) {
                wp_send_json_error( array( 'message' => __( 'Client ID not provided.', 'cree-consejeria' ) ) );
            }

            $user_info = get_userdata( $client_id );
            if ( ! $user_info ) {
                wp_send_json_error( array( 'message' => __( 'Client user profile not found.', 'cree-consejeria' ) ) );
            }

            global $wpdb;
            $encryption_key = $this->get_encryption_key();

            // 1. Fetch Assigned Forms for this client
            $assigned_table = $wpdb->prefix . 'cree_consejeria_assigned_forms';
            $assigned_forms = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$assigned_table} WHERE client_id = %d ORDER BY created_at DESC",
                $client_id
            ) );

            // 2. Fetch Completed Intake Forms
            $forms_table     = $wpdb->prefix . 'cree_consejeria_client_forms_master';
            $completed_forms = $wpdb->get_results( $wpdb->prepare(
                "SELECT id, form_type, created_at, status FROM {$forms_table} WHERE wp_id = %d ORDER BY created_at DESC",
                $client_id
            ) );

            // Create a quick lookup map of completed form types
            $completed_map = array();
            foreach ( $completed_forms as $cf ) {
                $completed_map[ $cf->form_type ] = $cf;
            }

            // 3. Fetch Session Notes for the specific client
            $notes_table = $wpdb->prefix . 'cree_consejeria_session_notes';
            $notes = $wpdb->get_results( $wpdb->prepare(
                "SELECT id, session_date, CAST(AES_DECRYPT(note_encrypted, %s) AS CHAR) AS decrypted_note, created_at
                 FROM {$notes_table} WHERE client_id = %d ORDER BY session_date DESC",
                $encryption_key,
                $client_id
            ) );

            // 4. Fetch Appointments with duration data
            $appointments_table = $wpdb->prefix . 'cree_consejeria_appointments';
            $appointments = $wpdb->get_results( $wpdb->prepare(
                "SELECT id, appointment_date, duration_minutes, status, CAST(AES_DECRYPT(notes_encrypted, %s) AS CHAR) AS decrypted_notes
                 FROM {$appointments_table} WHERE client_id = %d ORDER BY appointment_date DESC",
                $encryption_key,
                $client_id
            ) );

            // Split into Upcoming and Past
            $upcoming_appts = array();
            $past_appts = array();
            $current_time = current_time('timestamp');

            foreach ( $appointments as $a ) {
                if ( $a->status === 'scheduled' && strtotime($a->appointment_date) >= $current_time ) {
                    $upcoming_appts[] = $a;
                } else {
                    $past_appts[] = $a;
                }
            }

            ob_start();
            ?>
            <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 20px;">
                <h2 style="margin-top:0; color: #1e3a8a;"><?php echo esc_html( $user_info->display_name ); ?></h2>
                <p style="margin: 0; color: #4b5563;">📧 <?php echo esc_html( $user_info->user_email ); ?> | 🆔 Client ID: #<?php echo intval( $user_info->ID ); ?></p>
            </div>

            <!-- Sub-Tabs Navigation -->
            <div style="border-bottom: 2px solid #e5e7eb; margin-bottom: 20px; display: flex; gap: 10px;">
                <button class="cree-prof-tab active" onclick="creeSwitchProfileTab(event, 'prof-forms')">📋 Intake & Assigned Forms</button>
                <button class="cree-prof-tab" onclick="creeSwitchProfileTab(event, 'prof-notes')">📝 Session Notes</button>
                <button class="cree-prof-tab" onclick="creeSwitchProfileTab(event, 'prof-appointments')">📅 Appointments</button>
            </div>

            <!-- TAB 1: FORMS -->
            <div id="prof-forms" class="cree-prof-content" style="display: block;">
                <div style="display: grid; grid-template-columns: 320px 1fr; gap: 20px;">
                    
                    <!-- Left Side: Assign Form Controls -->
                    <div style="background: #f9fafb; padding: 18px; border-radius: 6px; border: 1px solid #e5e7eb; height: fit-content;">
                        <h4 style="margin-top:0; color:#1f2937;">Assign Form</h4>
                        <form id="cree-profile-assign-form">
                            <input type="hidden" name="client_id" value="<?php echo intval( $client_id ); ?>">
                            <div style="margin-bottom: 12px;">
                                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:4px;">Select Form to Assign:</label>
                                <select name="form_type" style="width:100%; padding:8px; border-radius:4px; border:1px solid #d1d5db;" required>
                                    <option value="">-- Choose Form --</option>
                                    <option value="registration_short_form">Client Registration Form</option>
                                    <option value="psychosocial_evaluation">Psychosocial Evaluation</option>
                                    <option value="substance_abuse_intake">Substance Abuse Intake</option>
                                    <option value="aviso_de_practicas_de_privacidad">Aviso de Prácticas de Privacidad</option>
                                    <option value="consentimiento_informado_para_psicoterapia">Consentimiento Informado para Psicoterapia</option>
                                    <option value="consentimiento_informado_para_terapia_en_linea">Consentimiento Informado para Terapia en Línea</option>
                                    <option value="consentimiento_para_divulgacion_de_informacion">Consentimiento para Divulgación de Información</option>
                                    <option value="politicas_de_la_practica">Políticas de la Práctica</option>
                                </select>
                            </div>
                            <button type="submit" style="background:#2563eb; color:#fff; border:none; padding:10px 14px; border-radius:4px; font-weight:bold; cursor:pointer; width:100%;">Assign to Client</button>
                        </form>
                    </div>

                    <!-- Right Side: Assigned Intake Records Table -->
                    <div>
                        <h4 style="margin-top:0; color:#1f2937;">Assigned Intake Records</h4>
                        
                        <?php if ( empty( $assigned_forms ) && empty( $completed_forms ) ) : ?>
                            <p style="font-style:italic; color:#6b7280; margin: 0;">No forms assigned to or submitted by this client yet.</p>
                        <?php else : ?>
                            <table class="widefat striped" style="width:100%; border-collapse:collapse; font-size:13px;">
                                <thead>
                                    <tr style="background:#f3f4f6;">
                                        <th style="padding:10px; text-align:left;">Form Name</th>
                                        <th style="padding:10px; text-align:left;">Assigned Date</th>
                                        <th style="padding:10px; text-align:left;">Status</th>
                                        <th style="padding:10px; text-align:left;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ( $assigned_forms as $af ) : 
                                        $is_done = isset( $completed_map[ $af->form_type ] );
                                        $completed_record = $is_done ? $completed_map[ $af->form_type ] : null;
                                    ?>
                                        <tr>
                                            <td style="padding:10px; font-weight:bold;">
                                                <?php echo esc_html( ucwords( str_replace( '_', ' ', $af->form_type ) ) ); ?>
                                            </td>
                                            <td style="padding:10px;">
                                                <?php echo esc_html( wp_date( 'M j, Y', strtotime( $af->created_at ) ) ); ?>
                                            </td>
                                            <td style="padding:10px;">
                                                <?php if ( $is_done ) : ?>
                                                    <span style="background: #d1fae5; color: #065f46; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; text-transform: uppercase;">
                                                        Completed
                                                    </span>
                                                <?php else : ?>
                                                    <span style="background: #fef3c7; color: #92400e; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; text-transform: uppercase;">
                                                        Pending
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding:10px;">
                                                <?php if ( $is_done ) : ?>
                                                    <button type="button" onclick="creeViewClientFormDetails(<?php echo intval( $completed_record->id ); ?>)" style="background:#3b82f6; color:#fff; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">👁️ View</button>
                                                    <button type="button" onclick="creeExportPDF(<?php echo intval( $completed_record->id ); ?>)" style="background:#10b981; color:#fff; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">📄 PDF / Print</button>
                                                <?php else : ?>
                                                    <button type="button" onclick="creeUnassignForm(<?php echo intval( $af->id ); ?>)" style="background:#dc2626; color:#fff; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">🚫 Unassign</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- TAB 2: SESSION NOTES -->
            <div id="prof-notes" class="cree-prof-content" style="display: none;">
                <div style="background: #f9fafb; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb; margin-bottom: 20px;">
                    <h4 style="margin-top:0;">Add Session Note</h4>
                    <form id="cree-profile-add-note">
                        <input type="hidden" name="client_id" value="<?php echo intval( $client_id ); ?>">
                        <div style="margin-bottom:10px;">
                            <label style="display:block; font-size:12px; font-weight:bold;">Session Date:</label>
                            <input type="date" name="session_date" value="<?php echo date('Y-m-d'); ?>" style="padding:6px; border-radius:4px; border:1px solid #d1d5db;" required>
                        </div>
                        <div style="margin-bottom:10px;">
                            <textarea name="note_text" rows="4" placeholder="Enter encrypted session notes, SOAP observations, or clinical notes..." style="width:100%; padding:8px; border-radius:4px; border:1px solid #d1d5db;" required></textarea>
                        </div>
                        <button type="submit" style="background:#10b981; color:#fff; border:none; padding:8px 16px; border-radius:4px; font-weight:bold; cursor:pointer;">🔒 Save Encrypted Note</button>
                    </form>
                </div>
                
                <h4>Encrypted Session Note History</h4>
                <div id="cree-session-notes-history-container">
                    <?php if ( empty( $notes ) ) : ?>
                        <p id="cree-no-notes-msg" style="font-style:italic; color:#6b7280;">No session notes recorded for this client.</p>
                    <?php else : ?>
                        <?php foreach ( $notes as $n ) : ?>
                            <div class="cree-note-card" data-note-id="<?php echo intval( $n->id ); ?>" style="background:#fff; border:1px solid #e5e7eb; border-left:4px solid #10b981; padding:14px; margin-bottom:12px; border-radius:6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                    <div class="cree-note-date-text" style="font-size:12px; font-weight:bold; color:#374151;">📅 Session Date: <?php echo esc_html( $n->session_date ); ?></div>
                                    <div style="display: flex; gap: 8px;">
                                        <button type="button" onclick="creePrintNote(this)" style="background:#6b7280; color:#fff; border:none; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:bold; cursor:pointer;">🖨️ Print</button>
                                        <button type="button" class="cree-edit-note-btn" style="background:#3b82f6; color:#fff; border:none; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:bold; cursor:pointer;">✏️ Edit</button>
                                    </div>
                                </div>
                                
                                <!-- Static View Mode -->
                                <div class="cree-note-text-view" style="font-size:13.5px; color:#1f2937; white-space:pre-wrap;"><?php echo esc_html( $n->decrypted_note ? $n->decrypted_note : '(Encrypted content unavailable)' ); ?></div>
                                
                                <!-- Edit Mode (Hidden by default) -->
                                <div class="cree-note-edit-form" style="display:none; margin-top:10px;">
                                    <textarea class="cree-note-edit-textarea" rows="4" style="width:100%; padding:8px; border-radius:4px; border:1px solid #d1d5db; font-size:13.5px;"><?php echo esc_textarea( $n->decrypted_note ); ?></textarea>
                                    <div style="margin-top:8px; display:flex; gap:8px;">
                                        <button type="button" class="cree-save-note-btn" style="background:#10b981; color:#fff; border:none; padding:6px 14px; border-radius:4px; font-weight:bold; cursor:pointer; font-size:12px;">💾 Save</button>
                                        <button type="button" class="cree-cancel-note-btn" style="background:#e5e7eb; color:#374151; border:none; padding:6px 12px; border-radius:4px; font-weight:bold; cursor:pointer; font-size:12px;">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAB 3: APPOINTMENTS (OVERHAULED) -->
            <div id="prof-appointments" class="cree-prof-content" style="display: none;">
                <div style="background: #f9fafb; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb; margin-bottom: 20px;">
                    <h4 style="margin-top:0;" id="cree-appt-form-title">Schedule New Session</h4>
                    <form id="cree-profile-add-appt">
                        <input type="hidden" name="client_id" value="<?php echo intval( $client_id ); ?>">
                        <input type="hidden" name="appointment_id" id="edit_appointment_id" value="">
                        
                        <div style="display:grid; grid-template-columns: 1fr 120px 1fr; gap:15px; margin-bottom:10px;">
                            <div>
                                <label style="display:block; font-size:12px; font-weight:bold;">Date & Time:</label>
                                <input type="datetime-local" name="appointment_date" style="width:100%; padding:6px; border-radius:4px; border:1px solid #d1d5db;" required>
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:bold;">Duration:</label>
                                <select name="duration_minutes" style="width:100%; padding:6px; border-radius:4px; border:1px solid #d1d5db;">
                                    <option value="30">30 Mins</option>
                                    <option value="50" selected>50 Mins</option>
                                    <option value="90">90 Mins</option>
                                </select>
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:bold;">Notes (Optional):</label>
                                <input type="text" name="notes" placeholder="Room #, or link..." style="width:100%; padding:6px; border-radius:4px; border:1px solid #d1d5db;">
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" id="cree-appt-submit-btn" style="background:#8b5cf6; color:#fff; border:none; padding:8px 16px; border-radius:4px; font-weight:bold; cursor:pointer;">📅 Book Session</button>
                            <button type="button" id="cree-appt-cancel-edit-btn" style="display:none; background:#e5e7eb; color:#374151; border:none; padding:8px 16px; border-radius:4px; font-weight:bold; cursor:pointer;">Cancel Edit</button>
                        </div>
                    </form>
                </div>

                <!-- Upcoming Appointments -->
                <h4 style="color: #047857; margin-bottom: 10px;">Upcoming Appointments</h4>
                <?php if ( empty( $upcoming_appts ) ) : ?>
                    <p style="font-style:italic; color:#6b7280; margin-bottom: 25px;">No upcoming appointments.</p>
                <?php else : ?>
                    <table class="widefat striped" style="width:100%; border-collapse:collapse; font-size:13px; margin-bottom: 25px;">
                        <thead>
                            <tr style="background: #ecfdf5;">
                                <th style="padding:8px; text-align: left;">Date & Time</th>
                                <th style="padding:8px; text-align: left;">Duration</th>
                                <th style="padding:8px; text-align: left;">Notes</th>
                                <th style="padding:8px; text-align: left;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $upcoming_appts as $a ) : 
                                // Format date for HTML5 datetime-local input (YYYY-MM-DDThh:mm)
                                $js_date = date('Y-m-d\TH:i', strtotime($a->appointment_date));
                            ?>
                                <tr>
                                    <td style="padding:8px; font-weight:bold;"><?php echo esc_html( wp_date( 'F j, Y \a\t g:i a', strtotime( $a->appointment_date ) ) ); ?></td>
                                    <td style="padding:8px;"><?php echo intval( $a->duration_minutes ); ?>m</td>
                                    <td style="padding:8px;"><?php echo esc_html( $a->decrypted_notes ? $a->decrypted_notes : '-' ); ?></td>
                                    <td style="padding:8px;">
                                        <button type="button" onclick="creeUpdateAppointmentStatus(<?php echo intval( $a->id ); ?>, 'completed')" style="background:#10b981; color:#fff; border:none; padding:3px 6px; border-radius:3px; cursor:pointer; font-size:11px;">✔️ Complete</button>
                                        <button type="button" onclick="creeEditAppointment(<?php echo intval( $a->id ); ?>, '<?php echo esc_js($js_date); ?>', <?php echo intval($a->duration_minutes); ?>, '<?php echo esc_js($a->decrypted_notes); ?>')" style="background:#3b82f6; color:#fff; border:none; padding:3px 6px; border-radius:3px; cursor:pointer; font-size:11px;">✏️ Edit</button>
                                        <button type="button" onclick="creeUpdateAppointmentStatus(<?php echo intval( $a->id ); ?>, 'cancelled')" style="background:#ef4444; color:#fff; border:none; padding:3px 6px; border-radius:3px; cursor:pointer; font-size:11px;">🚫 Cancel</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <!-- Past Appointments History -->
                <h4 style="color: #6b7280; margin-bottom: 10px; border-top: 1px solid #e5e7eb; padding-top: 15px;">Past & Archived Appointments</h4>
                <?php if ( empty( $past_appts ) ) : ?>
                    <p style="font-style:italic; color:#6b7280;">No past history.</p>
                <?php else : ?>
                    <table class="widefat striped" style="width:100%; border-collapse:collapse; font-size:13px; opacity: 0.85;">
                        <thead>
                            <tr>
                                <th style="padding:8px; text-align: left;">Date & Time</th>
                                <th style="padding:8px; text-align: left;">Status</th>
                                <th style="padding:8px; text-align: left;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $past_appts as $a ) : ?>
                                <tr>
                                    <td style="padding:8px; font-weight:bold;"><?php echo esc_html( wp_date( 'M j, Y \a\t g:i a', strtotime( $a->appointment_date ) ) ); ?></td>
                                    <td style="padding:8px;">
                                        <span style="padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 10px; text-transform: uppercase; <?php echo $a->status === 'completed' ? 'background:#d1fae5; color:#065f46;' : ($a->status === 'cancelled' ? 'background:#fee2e2; color:#991b1b;' : 'background:#e0f2fe; color:#075985;'); ?>">
                                            <?php echo esc_html( $a->status ); ?>
                                        </span>
                                    </td>
                                    <td style="padding:8px;"><?php echo esc_html( $a->decrypted_notes ? $a->decrypted_notes : '-' ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <?php
            wp_send_json_success( array( 'html' => ob_get_clean() ) );
        }

        /**
         * AJAX Handler: Unassign a Pending Form
         */
        public function ajax_unassign_form() {
            if ( isset( $_POST['security'] ) ) {
                check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            } elseif ( isset( $_POST['nonce'] ) ) {
                check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            } else {
                wp_send_json_error( array( 'message' => __( 'Security check failed.', 'cree-consejeria' ) ) );
            }

            if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'cree-consejeria' ) ) );
            }

            $assigned_id = isset( $_POST['assigned_id'] ) ? intval( $_POST['assigned_id'] ) : 0;
            if ( ! $assigned_id ) {
                wp_send_json_error( array( 'message' => __( 'Invalid assigned record ID.', 'cree-consejeria' ) ) );
            }

            global $wpdb;
            $assigned_table = $wpdb->prefix . 'cree_consejeria_assigned_forms';

            $deleted = $wpdb->delete(
                $assigned_table,
                array( 'id' => $assigned_id, 'status' => 'assigned' ),
                array( '%d', '%s' )
            );

            if ( $deleted ) {
                wp_send_json_success( array( 'message' => __( 'Form assignment successfully removed.', 'cree-consejeria' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Could not unassign form (it may have already been completed).', 'cree-consejeria' ) ) );
            }
        }

        /**
         * AJAX Handler: Fetch Form Template for Client Completion
         */
        public function ajax_get_client_form_template() {
            if ( isset( $_POST['nonce'] ) ) {
                check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            } elseif ( isset( $_POST['security'] ) ) {
                check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            } else {
                wp_send_json_error( array( 'message' => __( 'Security verification failed.', 'cree-consejeria' ) ) );
            }

            if ( ! is_user_logged_in() ) {
                wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'cree-consejeria' ) ) );
            }

            $form_type = isset( $_POST['form_type'] ) ? sanitize_text_field( $_POST['form_type'] ) : '';

            if ( empty( $form_type ) ) {
                wp_send_json_error( array( 'message' => __( 'No form type specified.', 'cree-consejeria' ) ) );
            }

            ob_start();

            switch ( $form_type ) {
                
                // Intake Forms
                case 'registration_short_form':
                    require_once CREE_CONSEJERIA_CFM_PATH . 'views/intake-forms/client-registration-short-form_shortcode.php';
                    break;

                case 'psychosocial_evaluation':
                    require_once CREE_CONSEJERIA_CFM_PATH . 'views/intake-forms/psychosocial-evaluation_shortcode.php';
                    break;

                case 'substance_abuse_intake':
                    require_once CREE_CONSEJERIA_CFM_PATH . 'views/intake-forms/substance-abuse-intake_shortcode.php';
                    break;

                // Privacy Forms
                case 'politicas_de_la_practica':
                    require_once CREE_CONSEJERIA_CFM_PATH . 'views/privacy-forms/politicas-de-la-practica_shortcode.php';
                    break;

                case 'aviso_de_practicas_de_privacidad':
                    require_once CREE_CONSEJERIA_CFM_PATH . 'views/privacy-forms/aviso-de-practicas-de-privacidad_shortcode.php';
                    break;

                // Consent Forms
                case 'consentimiento_informado_para_psicoterapia':
                    require_once CREE_CONSEJERIA_CFM_PATH . 'views/consent-forms/consentimiento-informado-para-psicoterapia_shortcode.php';
                    break;

                case 'consentimiento_informado_para_terapia_en_linea':
                    require_once CREE_CONSEJERIA_CFM_PATH . 'views/consent-forms/consentimiento-informado-para-terapia-en-linea_shortcode.php';
                    break;

                case 'consentimiento_para_divulgacion_de_informacion':
                    require_once CREE_CONSEJERIA_CFM_PATH . 'views/consent-forms/consentimiento-para-divulgacion-de-informacion_shortcode.php';
                    break;

                default:
                    echo '<p style="color:red;">Form template not found for: ' . esc_html( $form_type ) . '</p>';
                    break;
            }

            $output = ob_get_clean();

            if ( ! empty( trim( $output ) ) ) {
                wp_send_json_success( array( 'html' => $output ) );
            } else {
                // Fallback: If view file instantiates a class or shortcode callback
                $shortcode_str = '[cree_' . $form_type . ']';
                wp_send_json_success( array( 'html' => do_shortcode( $shortcode_str ) ) );
            }
        }

        /**
         * AJAX Handler: Consentimiento Informado para Terapia en Línea Submission
         */
        public function ajax_submit_consentimiento_terapia_en_linea() {
            // 1. Nonce Check
            if ( isset( $_POST['nonce'] ) ) {
                check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            } elseif ( isset( $_POST['security'] ) ) {
                check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            } else {
                wp_send_json_error( array( 'message' => __( 'Security verification failed.', 'cree-consejeria' ) ) );
            }

            if ( ! is_user_logged_in() ) {
                wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'cree-consejeria' ) ) );
            }

            $current_user_id = get_current_user_id();

            // 2. Sanitize and collect form inputs
            $form_data = array();
            foreach ( $_POST as $key => $value ) {
                if ( in_array( $key, array( 'action', 'nonce', 'security' ), true ) ) {
                    continue;
                }
                $form_data[ sanitize_key( $key ) ] = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( $value );
            }

            $json_data      = wp_json_encode( $form_data );
            $encryption_key = $this->get_encryption_key();

            global $wpdb;
            $forms_table = $wpdb->prefix . 'cree_consejeria_client_forms_master';

            // 3. Save Encrypted Record
            $inserted = $wpdb->query( $wpdb->prepare(
                "INSERT INTO {$forms_table} (wp_id, form_type, form_data_encrypted, status, created_at) 
                VALUES (%d, %s, AES_ENCRYPT(%s, %s), %s, %s)",
                $current_user_id,
                'consentimiento_informado_para_terapia_en_linea',
                $json_data,
                'completed',
                current_time( 'mysql' )
            ) );

            if ( false !== $inserted ) {
                // Also update assigned forms tracking table if applicable
                $assigned_table = $wpdb->prefix . 'cree_consejeria_assigned_forms';
                $wpdb->update(
                    $assigned_table,
                    array( 'status' => 'completed' ),
                    array( 'client_id' => $current_user_id, 'form_type' => 'consentimiento_informado_para_terapia_en_linea' ),
                    array( '%s' ),
                    array( '%d', '%s' )
                );

                wp_send_json_success( array( 'message' => __( 'El consentimiento para terapia en línea ha sido enviado exitosamente.', 'cree-consejeria' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Error guardando el formulario en la base de datos.', 'cree-consejeria' ) ) );
            }
        }

        /**
         * AJAX Handler: Update an Existing Session Note
         */
        public function ajax_update_session_note() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
            }

            global $wpdb;
            $notes_table_name = $wpdb->prefix . 'cree_consejeria_session_notes';
            $encryption_key = $this->get_encryption_key();

            $note_id   = isset( $_POST['note_id'] ) ? intval( $_POST['note_id'] ) : 0;
            $note_text = isset( $_POST['note_text'] ) ? sanitize_textarea_field( $_POST['note_text'] ) : '';

            if ( ! $note_id || empty( $note_text ) ) {
                wp_send_json_error( array( 'message' => 'Invalid note ID or empty content.' ) );
            }

            $updated = $wpdb->query( $wpdb->prepare(
                "UPDATE $notes_table_name 
                SET note_encrypted = AES_ENCRYPT(%s, %s) 
                WHERE id = %d",
                $note_text,
                $encryption_key,
                $note_id
            ) );

            if ( false !== $updated ) {
                wp_send_json_success( array( 'message' => '✅ Note updated successfully!' ) );
            } else {
                wp_send_json_error( array( 'message' => 'Database update error: ' . $wpdb->last_error ) );
            }
        }

        private function get_encryption_key() {
            $key = get_option( 'cree_consejeria_encryption_key' );
            if ( ! $key ) {
                // Generate a persistent 64-character key on first run
                $key = wp_generate_password( 64, true, true );
                update_option( 'cree_consejeria_encryption_key', $key );
            }
            return $key;
        }

        /**
         * Deactivate the plugin
         */
        public static function deactivate() {
            flush_rewrite_rules();
        }        

        /**
         * Uninstall the plugin
         */
        public static function uninstall() {
            delete_option( 'cree_consejeria_cfm_db_version' );
            
            global $wpdb;
            $wpdb->query(
                "DELETE FROM $wpdb->posts WHERE post_name IN ('registration-short-form', 'substance-abuse-form', 'psychosocial-evaluation-form', 
                'aviso-de-practicas-de-privacidad', 'consentimiento-informado-para-psicoterapia', 
                'politicas-de-la-practica', 'consentimiento-informado-para-terapia-en-linea', 'consentimiento-para-divulgacion-de-informacion', 'client-portal', 'client-login') AND post_type = 'page'"
            );
            $wpdb->query(
                "DROP TABLE IF EXISTS {$wpdb->prefix}cree_consejeria_client_forms_master"
            );
            $wpdb->query(
                "DROP TABLE IF EXISTS {$wpdb->prefix}cree_consejeria_session_notes"
            );
        }       
    }
}

// Plugin Instantiation
if ( class_exists( 'CREE_Consejeria_CFM' ) ) {
    register_activation_hook( __FILE__, array( 'CREE_Consejeria_CFM', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'CREE_Consejeria_CFM', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'CREE_Consejeria_CFM', 'uninstall' ) );

    $cree_consejeria_cfm = new CREE_Consejeria_CFM(); 
}