<?php

/**
* Plugin Name: CREE Consejeria CFM
* Plugin URI: https://www.wordpress.org/cree-consejeria
* Description: Sistema de Consejería y Formulario de Intake Encryptado
* Version: 6.0.1
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

            add_action( 'wp_ajax_cree_load_schedule_dashboard', array( $this, 'ajax_load_schedule_dashboard' ) );

            add_action( 'wp_ajax_cree_save_master_schedule', array( $this, 'ajax_save_master_schedule' ) );
            add_action( 'wp_ajax_cree_add_schedule_exception', array( $this, 'ajax_add_schedule_exception' ) );
            add_action( 'wp_ajax_cree_remove_schedule_exception', array( $this, 'ajax_remove_schedule_exception' ) );
            add_action( 'wp_ajax_cree_handle_request', array( $this, 'ajax_handle_request' ) );

            add_action( 'wp_ajax_cree_get_available_slots', array( $this, 'ajax_get_available_slots' ) );
            add_action( 'wp_ajax_cree_submit_appointment_request', array( $this, 'ajax_submit_appointment_request' ) );

            add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
            add_action( 'wp_ajax_cree_load_emr_template', array( $this, 'ajax_load_emr_template' ) );
            add_action( 'wp_ajax_cree_save_emr_plan', array( $this, 'ajax_save_emr_plan' ) );
            add_action( 'wp_ajax_cree_load_client_plans', array( $this, 'ajax_load_client_plans' ) );

            add_action( 'wp_ajax_cree_toggle_plan_visibility', array( $this, 'ajax_toggle_plan_visibility' ) );

            add_action( 'wp_ajax_cree_get_client_visible_plans', array( $this, 'ajax_get_client_visible_plans' ) );

            add_action( 'cree_daily_appointment_reminders', array( $this, 'cron_send_appointment_reminders' ) );
            if ( ! wp_next_scheduled( 'cree_daily_appointment_reminders' ) ) {
                wp_schedule_event( time(), 'hourly', 'cree_daily_appointment_reminders' );
            }

            add_action( 'wp_ajax_cree_delete_emr_plan', array( $this, 'ajax_delete_emr_plan' ) );

            add_action( 'wp_ajax_cree_save_billing', array( $this, 'ajax_save_billing' ) );
            add_action( 'wp_ajax_cree_update_billing_status', array( $this, 'ajax_update_billing_status' ) );
            add_action( 'wp_ajax_cree_generate_superbill', array( $this, 'ajax_generate_superbill' ) );

            add_action( 'wp_ajax_cree_load_therapist_dashboard', array( $this, 'ajax_load_therapist_dashboard' ) );

            add_action( 'wp_ajax_cree_delete_client', array( $this, 'ajax_delete_client' ) );

            add_action( 'wp_ajax_cree_toggle_client_status', array( $this, 'ajax_toggle_client_status' ) );

            add_action( 'wp_ajax_cree_update_client_password', array( $this, 'ajax_update_client_password' ) );

            add_action( 'plugins_loaded', array( $this, 'check_plugin_version' ) );
        }

        public function define_constants() {
            define( 'CREE_CONSEJERIA_CFM_PATH', plugin_dir_path( __FILE__ ) );
            define( 'CREE_CONSEJERIA_CFM_URL', plugin_dir_url( __FILE__ ) );
            define( 'CREE_CONSEJERIA_CFM_VERSION', '6.0.1' );
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
            $plan_templates_table = $wpdb->prefix . 'cree_consejeria_plan_templates';
            $client_plans_table = $wpdb->prefix . 'cree_consejeria_client_plans';
            $messages_table = $wpdb->prefix . 'cree_consejeria_secure_messages';
            $billing_table = $wpdb->prefix . 'cree_consejeria_billing';

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
                    status varchar(30) DEFAULT 'scheduled',
                    notes_encrypted blob DEFAULT NULL,
                    series_id varchar(50) DEFAULT NULL, /* Added to activate method */
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY client_id (client_id),
                    KEY therapist_id (therapist_id),
                    KEY appointment_date (appointment_date),
                    KEY status (status),
                    KEY series_id (series_id)
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

            // 8. v5.0 Treatment Plan Templates (Unencrypted CSV Data)
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $plan_templates_table ) ) !== $plan_templates_table ) {
                $sql_templates = "CREATE TABLE $plan_templates_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    category varchar(150) NOT NULL, /* e.g., Anger Control Problems */
                    section varchar(100) NOT NULL,  /* e.g., Behavioral Definitions, Long-Term Goals */
                    item_text text NOT NULL,        /* The specific criteria or goal */
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY category (category),
                    KEY section (section)
                ) $charset_collate;";
                dbDelta( $sql_templates );
            }

            // 9. v5.0 Client Encrypted Treatment Plans
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $client_plans_table ) ) !== $client_plans_table ) {
                $sql_client_plans = "CREATE TABLE $client_plans_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    client_id bigint(20) unsigned NOT NULL,
                    therapist_id bigint(20) unsigned NOT NULL,
                    primary_category varchar(150) NOT NULL,
                    plan_data_encrypted blob NOT NULL, /* Stores the JSON array of all checked items */
                    is_visible_to_client tinyint(1) DEFAULT 0,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY client_id (client_id),
                    KEY therapist_id (therapist_id)
                ) $charset_collate;";
                dbDelta( $sql_client_plans );
            }

            // 10. v6.0 Secure Messaging & File Drop
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $messages_table ) ) !== $messages_table ) {
                $sql_messages = "CREATE TABLE $messages_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    sender_id bigint(20) unsigned NOT NULL,
                    receiver_id bigint(20) unsigned NOT NULL,
                    message_encrypted blob NOT NULL,
                    attachment_url varchar(255) DEFAULT NULL,
                    is_read tinyint(1) DEFAULT 0,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY sender_id (sender_id),
                    KEY receiver_id (receiver_id),
                    KEY is_read (is_read)
                ) $charset_collate;";
                dbDelta( $sql_messages );
            }

            // 11. v6.0 Billing & Superbills
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $billing_table ) ) !== $billing_table ) {
                $sql_billing = "CREATE TABLE $billing_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    client_id bigint(20) unsigned NOT NULL,
                    therapist_id bigint(20) unsigned NOT NULL,
                    appointment_id bigint(20) unsigned DEFAULT NULL,
                    amount decimal(10,2) NOT NULL,
                    description varchar(255) NOT NULL,
                    cpt_code varchar(20) DEFAULT NULL,
                    status varchar(20) DEFAULT 'unpaid', /* unpaid, paid, void */
                    superbill_generated tinyint(1) DEFAULT 0,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    paid_at datetime DEFAULT NULL,
                    PRIMARY KEY (id),
                    KEY client_id (client_id),
                    KEY appointment_id (appointment_id),
                    KEY status (status)
                ) $charset_collate;";
                dbDelta( $sql_billing );
            }

            // 12. v6.0 Telehealth Link Upgrade for Appointments
            $appointments_table = $wpdb->prefix . 'cree_consejeria_appointments';
            $row = $wpdb->get_results( "SHOW COLUMNS FROM {$appointments_table} LIKE 'telehealth_link'" );
            if ( empty( $row ) ) {
                $wpdb->query( "ALTER TABLE {$appointments_table} ADD telehealth_link varchar(255) DEFAULT NULL AFTER notes_encrypted" );
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

            // $headers = array(
            //     'Content-Type: text/plain; charset=UTF-8',
            //     'From: CREE Consejería <' . $from_email . '>'
            // );

            // wp_mail( $to, $subject, $message, $headers );

            // Let WP Mail SMTP handle the Sender Name and Email automatically
            $headers = array( 
                'Content-Type: text/plain; charset=UTF-8'
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
                            <!-- Print button -->
                            <button type="button" onclick="creePrintNote(this)" style="background:#6b7280; color:#fff; border:none; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:bold; cursor:pointer;">🖨️ Print</button>
                            <!-- Edit button -->
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
            // $this->send_client_welcome_email( $user_id, $display_name, $email );

            // Send custom welcome email with temporary password
            $this->send_client_welcome_email( $user_id, $display_name, $email, $random_password );

            wp_send_json_success( array(
                'user_id'      => $user_id,
                'display_name' => $display_name,
                'email'        => $email,
                'message'      => 'Client created successfully and welcome email sent!'
            ) );
        }

        private function send_client_welcome_email_deprecated( $user_id, $display_name, $email ) {
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

        private function send_client_welcome_email_test1( $user_id, $display_name, $email ) {
            $user = get_userdata( $user_id );
            $key  = get_password_reset_key( $user );
            if ( is_wp_error( $key ) ) {
                return;
            }
            $reset_url = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' );
            $login_url = home_url( '/client-login/' );

            $subject = '[CREE Consejería] Bienvenido(a) - Instrucciones de Acceso al Portal';
            
            $message  = "Hola " . $display_name . ",\n\n";
            $message .= "Bienvenido(a) a CREE Consejería. He creado su expediente clínico digital seguro. Para comenzar y prepararnos para nuestra sesión, por favor siga estos 3 sencillos pasos:\n\n";
            
            $message .= "PASO 1: Cree su contraseña privada\n";
            $message .= "Haga clic en el enlace a continuación para establecer una contraseña segura para su expediente:\n";
            $message .= $reset_url . "\n\n";
            
            $message .= "PASO 2: Inicie sesión en el Portal del Cliente\n";
            $message .= "Una vez creada su contraseña, inicie sesión utilizando su correo electrónico aquí:\n";
            $message .= $login_url . "\n";
            $message .= "Usuario: " . $email . "\n\n";
            
            $message .= "PASO 3: Complete sus documentos iniciales\n";
            $message .= "Al ingresar al portal, verá una pestaña llamada '📋 My Forms'. Por favor, revise y complete todos los documentos pendientes antes de nuestra cita. Esto nos permitirá aprovechar al máximo nuestro tiempo juntos.\n\n";
            
            $message .= "Si tiene alguna pregunta o dificultad técnica para ingresar, por favor responda a este correo o comuníquese al 325-219-4039.\n\n";
            $message .= "Atentamente,\nRafael Ramírez (LPC-A)\nCREE Consejería";

            // Use the site domain for a proper sender address to prevent spam filtering
            $site_name = wp_parse_url( home_url(), PHP_URL_HOST );
            $from_email = 'no-reply@' . $site_name;

            $headers = array( 
                'Content-Type: text/plain; charset=UTF-8',
                'From: CREE Consejería <' . $from_email . '>'
            );
            
            wp_mail( $email, $subject, $message, $headers );
        }

        private function send_client_welcome_email_test2( $user_id, $display_name, $email, $random_password ) {
            $login_url = home_url( '/client-login/' );

            $subject = '[CREE Consejeria] Bienvenido(a) - Instrucciones de Acceso al Portal';
            
            $message  = "Hola " . $display_name . ",\n\n";
            $message .= "Bienvenido(a) a CREE Consejeria. He creado su expediente clínico digital seguro. Para comenzar y prepararnos para nuestra sesión, por favor siga estos pasos:\n\n";
            
            $message .= "PASO 1: Inicie sesión en el Portal del Cliente\n";
            $message .= "Acceda al portal utilizando el siguiente enlace:\n";
            $message .= $login_url . "\n\n";
            
            $message .= "Sus credenciales de acceso son:\n";
            $message .= "Usuario: " . $email . "\n";
            $message .= "Contraseña Temporal: " . $random_password . "\n\n";
            
            $message .= "PASO 2: Complete sus documentos iniciales\n";
            $message .= "Al ingresar al portal, verá una pestaña llamada '📋 My Forms'. Por favor, revise y complete todos los documentos pendientes antes de nuestra cita. Esto nos permitirá aprovechar al máximo nuestro tiempo juntos.\n\n";
            
            $message .= "Si tiene alguna pregunta o dificultad técnica para ingresar, por favor comuníquese al 325-219-4039.\n\n";
            $message .= "Atentamente,\nRafael Ramírez (LPC-A)\nCREE Consejeria";

            // Dynamically fetch your verified admin email address
            $sender_email = get_option( 'admin_email' );

            // Explicitly set a clean sender name (NO ACCENTS) to satisfy Brevo's API
            $headers = array( 
                'Content-Type: text/plain; charset=UTF-8',
                'From: CREE Consejeria <' . $sender_email . '>'
            );
            
            wp_mail( $email, $subject, $message, $headers );
        }

        private function send_client_welcome_email( $user_id, $display_name, $email, $random_password ) {
            $login_url = home_url( '/client-login/' );

            $subject = '[CREE Consejeria] Bienvenido(a) - Instrucciones de Acceso al Portal';
            
            $message  = "Hola " . $display_name . ",\n\n";
            $message .= "Bienvenido(a) a CREE Consejeria. He creado su expediente clínico digital seguro. Para comenzar y prepararnos para nuestra sesión, por favor siga estos pasos:\n\n";
            
            $message .= "PASO 1: Inicie sesión en el Portal del Cliente\n";
            $message .= "Acceda al portal utilizando el siguiente enlace:\n";
            $message .= $login_url . "\n\n";
            
            $message .= "Sus credenciales de acceso son:\n";
            $message .= "Usuario: " . $email . "\n";
            $message .= "Contraseña Temporal: " . $random_password . "\n\n";
            
            $message .= "PASO 2: Complete sus documentos iniciales\n";
            $message .= "Al ingresar al portal, verá una pestaña llamada '📋 My Forms'. Por favor, revise y complete todos los documentos pendientes antes de nuestra cita. Esto nos permitirá aprovechar al máximo nuestro tiempo juntos.\n\n";
            
            $message .= "Si tiene alguna pregunta o dificultad técnica para ingresar, por favor comuníquese al 325-219-4039.\n\n";
            $message .= "Atentamente,\nRafael Ramírez (LPC-A)\nCREE Consejeria";

            $headers = array( 'Content-Type: text/plain; charset=UTF-8' );

            // BULLETPROOF BYPASS: Inject the sender name and email directly into the core mailer
            $custom_mail_name = function( $name ) { return 'CREE Consejeria'; };
            $custom_mail_from = function( $email ) { return get_option( 'admin_email' ); };

            add_filter( 'wp_mail_from_name', $custom_mail_name );
            add_filter( 'wp_mail_from', $custom_mail_from );
            
            wp_mail( $email, $subject, $message, $headers );

            // Remove the filters immediately after sending so it doesn't affect other plugins
            remove_filter( 'wp_mail_from_name', $custom_mail_name );
            remove_filter( 'wp_mail_from', $custom_mail_from );
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
         * Book or Edit an Appointment (V4: Intelligent Recurring & Skip-and-Extend)
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
            $telehealth_link  = isset( $_POST['telehealth_link'] ) ? esc_url_raw( $_POST['telehealth_link'] ) : ''; // NEW
            
            // Recurrence logic
            $recurrence       = isset( $_POST['recurrence'] ) ? sanitize_text_field( $_POST['recurrence'] ) : 'none';
            $recurrence_count = isset( $_POST['recurrence_count'] ) ? intval( $_POST['recurrence_count'] ) : 1;
            
            if ( $appointment_id > 0 || $recurrence === 'none' || $recurrence_count < 1 ) {
                $recurrence_count = 1; // Editing or one-time sessions only loop once
            }

            if ( empty( $appointment_date ) ) wp_send_json_error( array( 'message' => __( 'Please select a valid date and time.', 'cree-consejeria' ) ) );
            $appointment_date = str_replace('T', ' ', $appointment_date);
            if ( strlen($appointment_date) == 16 ) $appointment_date .= ':00';

            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_appointments';
            $exceptions_table = $wpdb->prefix . 'cree_consejeria_schedule_exceptions';
            $encryption_key = $this->get_encryption_key();

            $series_id = ($recurrence_count > 1) ? uniqid('ser_') : null;
            $sessions_booked = 0;
            $attempts = 0;
            $max_attempts = $recurrence_count + 15; // Failsafe to prevent infinite loops
            $current_appt_date = $appointment_date;
            $success_msg = '';

            while ( $sessions_booked < $recurrence_count && $attempts < $max_attempts ) {
                $attempts++;

                // 1. CONFLICT CHECK: Is there an existing appointment?
                $overlap_query = $wpdb->prepare(
                    "SELECT id FROM {$table_name} 
                     WHERE therapist_id = %d AND status = 'scheduled' AND id != %d 
                     AND appointment_date < DATE_ADD(%s, INTERVAL %d MINUTE) 
                     AND DATE_ADD(appointment_date, INTERVAL duration_minutes MINUTE) > %s",
                    $therapist_id, $appointment_id, $current_appt_date, $duration_minutes, $current_appt_date
                );
                
                // 2. EXCEPTION CHECK: Is this day blocked for a vacation?
                $exception_query = $wpdb->prepare(
                    "SELECT id FROM {$exceptions_table} WHERE therapist_id = %d AND exception_date = DATE(%s)",
                    $therapist_id, $current_appt_date
                );

                if ( $wpdb->get_var( $overlap_query ) || $wpdb->get_var( $exception_query ) ) {
                    if ( $recurrence === 'weekly' && $appointment_id == 0 ) {
                        // SKIP AND EXTEND: Add 1 week and restart the loop without incrementing $sessions_booked
                        $current_appt_date = date('Y-m-d H:i:s', strtotime($current_appt_date . ' +1 week'));
                        continue;
                    } else {
                        // Hard fail for single-bookings or edits
                        wp_send_json_error( array( 'message' => __( 'Schedule Conflict: The slot at ' . $current_appt_date . ' is already booked or blocked.', 'cree-consejeria' ) ) );
                    }
                }

                // 3. EXECUTE DATABASE WRITE - No Link
                // if ( $appointment_id > 0 ) {
                //     $result = $wpdb->query( $wpdb->prepare(
                //         "UPDATE {$table_name} SET appointment_date = %s, duration_minutes = %d, notes_encrypted = AES_ENCRYPT(%s, %s) WHERE id = %d",
                //         $current_appt_date, $duration_minutes, $notes, $encryption_key, $appointment_id
                //     ) );
                //     $success_msg = 'Appointment successfully updated.';
                // } else {
                //     $result = $wpdb->query( $wpdb->prepare(
                //         "INSERT INTO {$table_name} (client_id, therapist_id, appointment_date, duration_minutes, status, notes_encrypted, series_id)
                //          VALUES (%d, %d, %s, %d, 'scheduled', AES_ENCRYPT(%s, %s), %s)",
                //         $client_id, $therapist_id, $current_appt_date, $duration_minutes, $notes, $encryption_key, $series_id
                //     ) );
                //     $success_msg = ($recurrence_count > 1) ? $recurrence_count . ' recurring appointments scheduled successfully.' : 'Appointment successfully scheduled.';
                // }

                // 3. EXECUTE DATABASE WRITE
                if ( $appointment_id > 0 ) {
                    $result = $wpdb->query( $wpdb->prepare(
                        "UPDATE {$table_name} SET appointment_date = %s, duration_minutes = %d, notes_encrypted = AES_ENCRYPT(%s, %s), telehealth_link = %s WHERE id = %d",
                        $current_appt_date, $duration_minutes, $notes, $encryption_key, $telehealth_link, $appointment_id
                    ) );
                    $success_msg = 'Appointment successfully updated.';
                } else {
                    $result = $wpdb->query( $wpdb->prepare(
                        "INSERT INTO {$table_name} (client_id, therapist_id, appointment_date, duration_minutes, status, notes_encrypted, series_id, telehealth_link)
                         VALUES (%d, %d, %s, %d, 'scheduled', AES_ENCRYPT(%s, %s), %s, %s)",
                        $client_id, $therapist_id, $current_appt_date, $duration_minutes, $notes, $encryption_key, $series_id, $telehealth_link
                    ) );
                    $success_msg = ($recurrence_count > 1) ? $recurrence_count . ' recurring appointments scheduled successfully.' : 'Appointment successfully scheduled.';
                }
                
                $sessions_booked++;
                $current_appt_date = date('Y-m-d H:i:s', strtotime($current_appt_date . ' +1 week')); // Prep date for next iteration
            }

            if ( $sessions_booked > 0 ) {
                $client_info = get_userdata( $client_id );
                if ( $client_info ) {
                    $formatted_time = wp_date( 'l, F j, Y \a\t g:i A', strtotime( $appointment_date ) );
                    $subject = $appointment_id > 0 ? '[CREE Consejería] Actualización de Cita' : '[CREE Consejería] Nueva Cita Programada';
                    
                    $message = "Hola {$client_info->first_name},\n\nSu sesión ha sido confirmada/actualizada.\n\n";
                    if ($recurrence_count > 1) {
                        $message .= "Frecuencia: Semanal ($sessions_booked sesiones)\nInicio: {$formatted_time}\n";
                    } else {
                        $message .= "Fecha y Hora: {$formatted_time}\n";
                    }
                    $message .= "Duración: {$duration_minutes} minutos\nNotas / Enlace: {$notes}\n\nSi necesita reprogramar, por favor hágalo con al menos 24 horas de antelación.\n\nAtentamente,\nEquipo de CREE Consejería";
                    
                    wp_mail( $client_info->user_email, $subject, $message, array('Content-Type: text/plain; charset=UTF-8') );
                }
                wp_send_json_success( array( 'message' => __( $success_msg, 'cree-consejeria' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to save appointments. It is possible all requested dates were blocked.', 'cree-consejeria' ) ) );
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
            
            // 1. Appointments & Calendar Management Table
            $sql_appointments = "CREATE TABLE $appointments_table (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                client_id bigint(20) unsigned NOT NULL,
                therapist_id bigint(20) unsigned NOT NULL,
                appointment_date datetime NOT NULL,
                duration_minutes int(11) DEFAULT 50,
                status varchar(30) DEFAULT 'scheduled',
                notes_encrypted blob DEFAULT NULL,
                series_id varchar(50) DEFAULT NULL, 
                created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY client_id (client_id),
                KEY therapist_id (therapist_id),
                KEY appointment_date (appointment_date),
                KEY status (status),
                KEY series_id (series_id)
            ) $charset_collate;";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql_appointments );

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

            // 5. v4.0 Master Schedule Rules Table
            $master_schedule_table = $wpdb->prefix . 'cree_consejeria_master_schedule';
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $master_schedule_table ) ) !== $master_schedule_table ) {
                $sql_master = "CREATE TABLE $master_schedule_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    therapist_id bigint(20) unsigned NOT NULL,
                    day_of_week tinyint(1) NOT NULL, /* 0=Sunday, 1=Monday, 2=Tuesday, etc. */
                    start_time time NOT NULL,
                    end_time time NOT NULL,
                    is_active tinyint(1) DEFAULT 1,
                    PRIMARY KEY (id),
                    KEY therapist_id (therapist_id)
                ) $charset_collate;";
                dbDelta( $sql_master );
            }

            // 6. v4.0 Schedule Exceptions Table (Time Off / Blocked Dates)
            $exceptions_table = $wpdb->prefix . 'cree_consejeria_schedule_exceptions';
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $exceptions_table ) ) !== $exceptions_table ) {
                $sql_exceptions = "CREATE TABLE $exceptions_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    therapist_id bigint(20) unsigned NOT NULL,
                    exception_date date NOT NULL,
                    is_blocked tinyint(1) DEFAULT 1,
                    reason varchar(255) DEFAULT NULL,
                    PRIMARY KEY (id),
                    KEY therapist_id (therapist_id),
                    KEY exception_date (exception_date)
                ) $charset_collate;";
                dbDelta( $sql_exceptions );
            }

            // 7. v4.0 Client Appointment Requests Queue
            $requests_table = $wpdb->prefix . 'cree_consejeria_appointment_requests';
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $requests_table ) ) !== $requests_table ) {
                $sql_requests = "CREATE TABLE $requests_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    client_id bigint(20) unsigned NOT NULL,
                    therapist_id bigint(20) unsigned NOT NULL,
                    requested_datetime datetime NOT NULL,
                    duration_minutes int(11) DEFAULT 50,
                    status varchar(20) DEFAULT 'pending', /* pending, approved, denied, expired */
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY client_id (client_id),
                    KEY therapist_id (therapist_id),
                    KEY status (status)
                ) $charset_collate;";
                dbDelta( $sql_requests );
            }

            // 8. v5.0 Treatment Plan Templates (Unencrypted CSV Data)
            $plan_templates_table = $wpdb->prefix . 'cree_consejeria_plan_templates';
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $plan_templates_table ) ) !== $plan_templates_table ) {
                $sql_templates = "CREATE TABLE $plan_templates_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    category varchar(150) NOT NULL, /* e.g., Anger Control Problems */
                    section varchar(100) NOT NULL,  /* e.g., Behavioral Definitions, Long-Term Goals */
                    item_text text NOT NULL,        /* The specific criteria or goal */
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY category (category),
                    KEY section (section)
                ) $charset_collate;";
                dbDelta( $sql_templates );
            }

            // 9. v5.0 Client Encrypted Treatment Plans
            $client_plans_table = $wpdb->prefix . 'cree_consejeria_client_plans';
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $client_plans_table ) ) !== $client_plans_table ) {
                $sql_client_plans = "CREATE TABLE $client_plans_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    client_id bigint(20) unsigned NOT NULL,
                    therapist_id bigint(20) unsigned NOT NULL,
                    primary_category varchar(150) NOT NULL,
                    plan_data_encrypted blob NOT NULL, /* Stores the JSON array of all checked items */
                    is_visible_to_client tinyint(1) DEFAULT 0,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY client_id (client_id),
                    KEY therapist_id (therapist_id)
                ) $charset_collate;";
                dbDelta( $sql_client_plans );
            }

            // 10. v6.0 Secure Messaging & File Drop
            $messages_table = $wpdb->prefix . 'cree_consejeria_secure_messages';
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $messages_table ) ) !== $messages_table ) {
                $sql_messages = "CREATE TABLE $messages_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    sender_id bigint(20) unsigned NOT NULL,
                    receiver_id bigint(20) unsigned NOT NULL,
                    message_encrypted blob NOT NULL,
                    attachment_url varchar(255) DEFAULT NULL,
                    is_read tinyint(1) DEFAULT 0,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY sender_id (sender_id),
                    KEY receiver_id (receiver_id),
                    KEY is_read (is_read)
                ) $charset_collate;";
                dbDelta( $sql_messages );
            }

            // 11. v6.0 Billing & Superbills
            $billing_table = $wpdb->prefix . 'cree_consejeria_billing';
            if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $billing_table ) ) !== $billing_table ) {
                $sql_billing = "CREATE TABLE $billing_table (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    client_id bigint(20) unsigned NOT NULL,
                    therapist_id bigint(20) unsigned NOT NULL,
                    appointment_id bigint(20) unsigned DEFAULT NULL,
                    amount decimal(10,2) NOT NULL,
                    description varchar(255) NOT NULL,
                    cpt_code varchar(20) DEFAULT NULL,
                    status varchar(20) DEFAULT 'unpaid', /* unpaid, paid, void */
                    superbill_generated tinyint(1) DEFAULT 0,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    paid_at datetime DEFAULT NULL,
                    PRIMARY KEY (id),
                    KEY client_id (client_id),
                    KEY appointment_id (appointment_id),
                    KEY status (status)
                ) $charset_collate;";
                dbDelta( $sql_billing );
            }

            // 12. v6.0 Telehealth Link Upgrade for Appointments
            $appointments_table = $wpdb->prefix . 'cree_consejeria_appointments';
            $row = $wpdb->get_results( "SHOW COLUMNS FROM {$appointments_table} LIKE 'telehealth_link'" );
            if ( empty( $row ) ) {
                $wpdb->query( "ALTER TABLE {$appointments_table} ADD telehealth_link varchar(255) DEFAULT NULL AFTER notes_encrypted" );
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
            //No telehealth link
            // $appointments = $wpdb->get_results( $wpdb->prepare(
            //     "SELECT id, appointment_date, duration_minutes, status, CAST(AES_DECRYPT(notes_encrypted, %s) AS CHAR) AS decrypted_notes
            //      FROM {$appointments_table} WHERE client_id = %d ORDER BY appointment_date DESC",
            //     $encryption_key,
            //     $client_id
            // ) );

            $appointments = $wpdb->get_results( $wpdb->prepare(
                "SELECT id, appointment_date, duration_minutes, status, telehealth_link, CAST(AES_DECRYPT(notes_encrypted, %s) AS CHAR) AS decrypted_notes
                 FROM {$appointments_table} WHERE client_id = %d ORDER BY appointment_date DESC",
                $encryption_key, $client_id
            ) );

            // 5. Fetch Billing Records
            $billing_table = $wpdb->prefix . 'cree_consejeria_billing';
            $billings = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$billing_table} WHERE client_id = %d ORDER BY created_at DESC", $client_id) );

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
             <!-- Version 1 -->
            <!-- <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 20px;">
                <h2 style="margin-top:0; color: #1e3a8a;"><?php echo esc_html( $user_info->display_name ); ?></h2>
                <p style="margin: 0; color: #4b5563;">📧 <?php echo esc_html( $user_info->user_email ); ?> | 🆔 Client ID: #<?php echo intval( $user_info->ID ); ?></p>
            </div> -->

             <!-- Version 2 -->
            <!-- <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h2 style="margin-top:0; color: #1e3a8a;"><?php echo esc_html( $user_info->display_name ); ?></h2>
                    <p style="margin: 0; color: #4b5563;">📧 <?php echo esc_html( $user_info->user_email ); ?> | 🆔 Client ID: #<?php echo intval( $user_info->ID ); ?></p>
                </div>
                <button type="button" onclick="creeDeleteClient(<?php echo intval( $user_info->ID ); ?>)" style="background: #ef4444; color: #fff; border: none; padding: 8px 14px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; transition: background 0.2s;">
                    🗑️ Delete Client
                </button>
            </div> -->

            <!-- Version 3 -->
            <?php
            // Fetch status (default to active if not set)
            $client_status = get_user_meta( $client_id, 'cree_client_status', true );
            if ( empty( $client_status ) ) $client_status = 'active';
            ?>
            <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <h2 style="margin:0; color: #1e3a8a;"><?php echo esc_html( $user_info->display_name ); ?></h2>
                        <?php if ( $client_status === 'inactive' ) : ?>
                            <span style="background: #e5e7eb; color: #4b5563; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase;">Archived</span>
                        <?php endif; ?>
                    </div>
                    <p style="margin: 5px 0 0 0; color: #4b5563;">📧 <?php echo esc_html( $user_info->user_email ); ?> | 🆔 Client ID: #<?php echo intval( $user_info->ID ); ?></p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <?php if ( $client_status === 'active' ) : ?>
                        <button type="button" onclick="creeToggleClientStatus(<?php echo intval( $user_info->ID ); ?>, 'inactive')" style="background: #f59e0b; color: #fff; border: none; padding: 8px 14px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer;">📦 Archive</button>
                    <?php else : ?>
                        <button type="button" onclick="creeToggleClientStatus(<?php echo intval( $user_info->ID ); ?>, 'active')" style="background: #10b981; color: #fff; border: none; padding: 8px 14px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer;">✅ Reactivate</button>
                    <?php endif; ?>
                    <button type="button" onclick="creeDeleteClient(<?php echo intval( $user_info->ID ); ?>)" style="background: #ef4444; color: #fff; border: none; padding: 8px 14px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer;">🗑️ Delete</button>
                </div>
            </div>

            <!-- Sub-Tabs Navigation -->
            <!-- <div style="border-bottom: 2px solid #e5e7eb; margin-bottom: 20px; display: flex; gap: 10px;">
                <button class="cree-prof-tab active" onclick="creeSwitchProfileTab(event, 'prof-forms')">📋 Intake & Assigned Forms</button>
                <button class="cree-prof-tab" onclick="creeSwitchProfileTab(event, 'prof-notes')">📝 Session Notes</button>
                <button class="cree-prof-tab" onclick="creeSwitchProfileTab(event, 'prof-appointments')">📅 Appointments</button>
            </div> -->

            <!-- Sub-Tabs Navigation -->
            <div style="border-bottom: 2px solid #e5e7eb; margin-bottom: 20px; display: flex; gap: 10px;">
                <button class="cree-prof-tab active" onclick="creeSwitchProfileTab(event, 'prof-forms')">📋 Intake & Assigned Forms</button>
                <button class="cree-prof-tab" onclick="creeSwitchProfileTab(event, 'prof-notes')">📝 Session Notes</button>
                <button class="cree-prof-tab" onclick="creeSwitchProfileTab(event, 'prof-appointments')">📅 Appointments</button>
                <button class="cree-prof-tab" onclick="creeSwitchProfileTab(event, 'prof-treatment-plan')">🎯 Treatment Plan</button>
                <button class="cree-prof-tab" onclick="creeSwitchProfileTab(event, 'prof-billing')">💳 Billing</button>
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
                                                    <button type="button" onclick="creeExportPDF(<?php echo intval( $completed_record->id ); ?>)" style="background:#10b981; color:#fff; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">📄 Print</button>
                                                <?php else : ?>
                                                    <!-- <button type="button" onclick="creeUnassignForm(<?php echo intval( $af->id ); ?>)" style="background:#dc2626; color:#fff; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">🚫 Unassign</button> -->
                                                    <button type="button" onclick="creeUnassignForm(<?php echo intval( $af->id ); ?>)" style="background:#f1f5f9; color:#0f172a; border:1px solid #cbd5e1; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">🚫 Unassign</button>
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
                        
                        <div style="display:grid; grid-template-columns: 1fr 120px 1fr 1fr; gap:15px; margin-bottom:10px;">
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
                                <input type="text" name="notes" placeholder="Room #..." style="width:100%; padding:6px; border-radius:4px; border:1px solid #d1d5db;">
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:bold;">Video Link (Optional):</label>
                                <input type="url" name="telehealth_link" placeholder="https://zoom.us/j/..." style="width:100%; padding:6px; border-radius:4px; border:1px solid #d1d5db;">
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:15px; padding-top: 10px; border-top: 1px solid #e5e7eb;" id="cree-recurrence-section">
                            <div>
                                <label style="display:block; font-size:12px; font-weight:bold;">Recurrence:</label>
                                <select name="recurrence" style="width:100%; padding:6px; border-radius:4px; border:1px solid #d1d5db;" onchange="if(this.value==='weekly'){jQuery('#cree-recurrence-count-wrap').show();}else{jQuery('#cree-recurrence-count-wrap').hide();}">
                                    <option value="none">One-Time Session</option>
                                    <option value="weekly">Weekly</option>
                                </select>
                            </div>
                            <div id="cree-recurrence-count-wrap" style="display:none;">
                                <label style="display:block; font-size:12px; font-weight:bold;">Number of Sessions:</label>
                                <input type="number" name="recurrence_count" value="1" min="1" max="52" style="width:100%; padding:6px; border-radius:4px; border:1px solid #d1d5db;">
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
                                    <!-- <td style="padding:8px; font-weight:bold;"><?php echo esc_html( wp_date( 'F j, Y \a\t g:i a', strtotime( $a->appointment_date ) ) ); ?></td> -->
                                    <td style="padding:8px; font-weight:bold;">
                                        <?php echo esc_html( wp_date( 'F j, Y \a\t g:i a T', strtotime( $a->appointment_date ) ) ); ?>
                                        <?php if ( ! empty( $a->telehealth_link ) ) : ?>
                                            <br><a href="<?php echo esc_url( $a->telehealth_link ); ?>" target="_blank" style="font-size:11px; color:#2563eb; text-decoration:none;">🎥 Join Video</a>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td style="padding:8px;"><?php echo intval( $a->duration_minutes ); ?>m</td>
                                    <td style="padding:8px;"><?php echo esc_html( $a->decrypted_notes ? $a->decrypted_notes : '-' ); ?></td>

                                    <td style="padding:8px;">
                                        <button type="button" onclick="creeUpdateAppointmentStatus(<?php echo intval( $a->id ); ?>, 'completed')" style="background:#10b981; color:#fff; border:none; padding:3px 6px; border-radius:3px; cursor:pointer; font-size:11px;">✔️ Complete</button>
                                        <button type="button" onclick="creeEditAppointment(<?php echo intval( $a->id ); ?>, '<?php echo esc_js($js_date); ?>', <?php echo intval($a->duration_minutes); ?>, '<?php echo esc_js($a->decrypted_notes); ?>', '<?php echo esc_js($a->telehealth_link); ?>')" style="background:#3b82f6; color:#fff; border:none; padding:3px 6px; border-radius:3px; cursor:pointer; font-size:11px;">✏️ Edit</button>
                                        <button type="button" onclick="creeUpdateAppointmentStatus(<?php echo intval( $a->id ); ?>, 'cancelled')" style="background:#f1f5f9; color:#0f172a; border:1px solid #cbd5e1; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">❌ Cancel</button>
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
            
            <!-- TAB 4: TREATMENT PLAN (EMR) -->
            <div id="prof-treatment-plan" class="cree-prof-content" style="display: none;">
                <div style="background: #f9fafb; padding: 20px; border-radius: 6px; border: 1px solid #e5e7eb; margin-bottom: 20px;">
                    <h4 style="margin-top:0; color: #1e3a8a;">Create / Update Treatment Plan</h4>
                    <p style="font-size: 13px; color: #4b5563; margin-bottom: 15px;">Select a psychological issue below to load the EMR templates. Check the boxes that apply to this client to build their encrypted plan.</p>
                    
                    <?php
                    // Fetch unique categories from the templates table
                    $templates_table = $wpdb->prefix . 'cree_consejeria_plan_templates';
                    $categories = $wpdb->get_col( "SELECT DISTINCT category FROM {$templates_table} ORDER BY category ASC" );
                    ?>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Primary Psychological Issue:</label>
                        <select id="cree-emr-category-select" style="width: 100%; max-width: 400px; padding: 8px; border-radius: 4px; border: 1px solid #d1d5db;" onchange="creeLoadEMRTemplate(this.value, <?php echo intval($client_id); ?>)">
                            <option value="">-- Select Category --</option>
                            <?php foreach ( $categories as $cat ) : ?>
                                <option value="<?php echo esc_attr( $cat ); ?>"><?php echo esc_html( $cat ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- The dynamic workspace where checkboxes will load -->
                    <div id="cree-emr-workspace-container">
                        <div style="padding: 30px; text-align: center; border: 2px dashed #d1d5db; border-radius: 6px; color: #6b7280; font-style: italic;">
                            Please select a category above to load the treatment plan criteria.
                        </div>
                    </div>
                </div>
                
                <h4 style="color: #047857; margin-bottom: 10px; border-top: 1px solid #e5e7eb; padding-top: 15px;">Encrypted Treatment Plan History</h4>
                <div id="cree-client-plans-history">
                    <p style="font-style:italic; color:#6b7280;">Loading saved plans...</p>
                </div>
            </div>

            <!-- TAB 5: BILLING & SUPERBILLS -->
            <div id="prof-billing" class="cree-prof-content" style="display: none;">
                <div style="background: #f9fafb; padding: 20px; border-radius: 6px; border: 1px solid #e5e7eb; margin-bottom: 20px;">
                    <h4 style="margin-top:0; color: #1e3a8a;">Add New Charge</h4>
                    <form id="cree-add-billing-form" style="display: grid; grid-template-columns: 1fr 1fr 120px 150px; gap: 15px; align-items: end;">
                        <input type="hidden" name="client_id" value="<?php echo intval( $client_id ); ?>">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:bold;">Description (e.g., 50 Min Session):</label>
                            <input type="text" name="description" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:4px;">
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:bold;">CPT Code (Optional):</label>
                            <input type="text" name="cpt_code" placeholder="90834" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:4px;">
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:bold;">Amount ($):</label>
                            <input type="number" step="0.01" name="amount" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:4px;">
                        </div>
                        <button type="submit" style="background:#10b981; color:#fff; border:none; padding:10px 14px; border-radius:4px; font-weight:bold; cursor:pointer;">➕ Add Charge</button>
                    </form>
                </div>

                <h4 style="color: #4b5563; margin-bottom: 10px;">Billing History & Superbills</h4>
                <?php if ( empty( $billings ) ) : ?>
                    <p style="font-style:italic; color:#6b7280;">No billing records found.</p>
                <?php else : ?>
                    <table class="widefat striped" style="width:100%; border-collapse:collapse; font-size:13px;">
                        <thead>
                            <tr style="background:#f3f4f6;">
                                <th style="padding:10px; text-align:left;">Date</th>
                                <th style="padding:10px; text-align:left;">Description</th>
                                <th style="padding:10px; text-align:left;">CPT</th>
                                <th style="padding:10px; text-align:left;">Amount</th>
                                <th style="padding:10px; text-align:left;">Status</th>
                                <th style="padding:10px; text-align:left;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $billings as $b ) : ?>
                                <tr>
                                    <td style="padding:10px;"><strong><?php echo wp_date('M j, Y', strtotime($b->created_at)); ?></strong></td>
                                    <td style="padding:10px;"><?php echo esc_html($b->description); ?></td>
                                    <td style="padding:10px;"><?php echo esc_html($b->cpt_code ? $b->cpt_code : '-'); ?></td>
                                    <td style="padding:10px;">$<?php echo number_format($b->amount, 2); ?></td>
                                    <td style="padding:10px;">
                                        <span style="padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; text-transform: uppercase; <?php echo $b->status === 'paid' ? 'background:#d1fae5; color:#065f46;' : 'background:#fee2e2; color:#991b1b;'; ?>">
                                            <?php echo esc_html($b->status); ?>
                                        </span>
                                    </td>
                                    <td style="padding:10px;">
                                        <?php if ( $b->status === 'unpaid' ) : ?>
                                            <button type="button" onclick="creeUpdateBillingStatus(<?php echo $b->id; ?>, 'paid')" style="background:#3b82f6; color:#fff; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px; margin-right:4px;">💰 Mark Paid</button>
                                        <?php endif; ?>
                                        <button type="button" onclick="creeGenerateSuperbill(<?php echo $b->id; ?>)" style="background:#4b5563; color:#fff; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">🖨️ Superbill</button>
                                    </td>
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

        /**
         * AJAX Handler: Load Global Schedule & Requests Dashboard
         */
        public function ajax_load_schedule_dashboard() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );

            global $wpdb;
            $therapist_id = get_current_user_id();

            // 1. Fetch Pending Requests
            $requests_table = $wpdb->prefix . 'cree_consejeria_appointment_requests';
            $pending_requests = $wpdb->get_results( $wpdb->prepare(
                "SELECT r.*, u.display_name AS client_name 
                 FROM {$requests_table} r 
                 LEFT JOIN {$wpdb->users} u ON r.client_id = u.ID 
                 WHERE r.therapist_id = %d AND r.status = 'pending' 
                 ORDER BY r.requested_datetime ASC",
                $therapist_id
            ) );

            // 2. Fetch Master Schedule
            $master_table = $wpdb->prefix . 'cree_consejeria_master_schedule';
            $schedule_rules = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$master_table} WHERE therapist_id = %d", $therapist_id ) );
            $master = array();
            foreach ( $schedule_rules as $rule ) { $master[$rule->day_of_week] = $rule; }

            // 3. Fetch Upcoming Exceptions
            $exceptions_table = $wpdb->prefix . 'cree_consejeria_schedule_exceptions';
            $exceptions = $wpdb->get_results( $wpdb->prepare( 
                "SELECT * FROM {$exceptions_table} WHERE therapist_id = %d AND exception_date >= CURDATE() ORDER BY exception_date ASC", 
                $therapist_id 
            ) );

            ob_start();
            ?>
            <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 20px;">
                <h2 style="margin-top:0; color: #1e3a8a;">Availability & Requests Dashboard</h2>
                <p style="margin: 0; color: #4b5563;">Manage your recurring schedule, block off time, and approve client booking requests.</p>
            </div>

            <!-- SECTION 1: PENDING REQUESTS -->
            <div style="background: #f9fafb; padding: 18px; border-radius: 6px; border: 1px solid #e5e7eb; margin-bottom: 25px;">
                <h3 style="margin-top:0; color: #d97706;">Pending Client Requests</h3>
                <?php if ( empty( $pending_requests ) ) : ?>
                    <p style="font-style:italic; color:#6b7280; margin:0;">No pending appointment requests.</p>
                <?php else : ?>
                    <table class="widefat striped" style="width:100%; border-collapse:collapse; font-size:13px;">
                        <thead>
                            <tr>
                                <th style="padding:8px; text-align: left;">Client</th>
                                <th style="padding:8px; text-align: left;">Requested Time</th>
                                <th style="padding:8px; text-align: left;">Duration</th>
                                <th style="padding:8px; text-align: left;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $pending_requests as $req ) : ?>
                                <tr>
                                    <td style="padding:8px; font-weight:bold;"><?php echo esc_html( $req->client_name ); ?></td>
                                    <td style="padding:8px;"><?php echo esc_html( wp_date( 'D, M j \a\t g:i a', strtotime( $req->requested_datetime ) ) ); ?></td>
                                    <td style="padding:8px;"><?php echo intval( $req->duration_minutes ); ?>m</td>
                                    <td style="padding:8px;">
                                        <button type="button" onclick="creeHandleRequest(<?php echo intval( $req->id ); ?>, 'approve')" style="background:#10b981; color:#fff; border:none; padding:4px 8px; border-radius:3px; cursor:pointer;">✔️ Approve</button>
                                        <!-- <button type="button" onclick="creeHandleRequest(<?php echo intval( $req->id ); ?>, 'deny')" style="background:#ef4444; color:#fff; border:none; padding:4px 8px; border-radius:3px; cursor:pointer;">🚫 Deny</button> -->
                                        <button type="button" onclick="creeHandleRequest(<?php echo intval( $req->id ); ?>, 'deny')" style="background:#f1f5f9; color:#0f172a; border:1px solid #cbd5e1; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">🚫 Deny</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- SECTION 2: MASTER SCHEDULE & EXCEPTIONS (Side by side) -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                
                <!-- Master Weekly Schedule -->
                <div style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px;">
                    <h4 style="margin-top:0;">Master Weekly Schedule</h4>
                    <form id="cree-master-schedule-form">
                        <?php 
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        foreach ( $days as $index => $day_name ) : 
                            // Map to 1-7 for consistency
                            $db_index = $index + 1; 
                            $is_active = isset($master[$db_index]) ? $master[$db_index]->is_active : 0;
                            $start = isset($master[$db_index]) ? $master[$db_index]->start_time : '09:00:00';
                            $end = isset($master[$db_index]) ? $master[$db_index]->end_time : '17:00:00';
                        ?>
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #f3f4f6;">
                            <div style="width: 100px;">
                                <label style="font-weight:bold; font-size:13px;">
                                    <input type="checkbox" name="day_<?php echo $db_index; ?>_active" value="1" <?php checked( $is_active, 1 ); ?>> 
                                    <?php echo $day_name; ?>
                                </label>
                            </div>
                            <input type="time" name="day_<?php echo $db_index; ?>_start" value="<?php echo esc_attr( $start ); ?>" style="padding:4px; border:1px solid #d1d5db; border-radius:4px;">
                            <span>to</span>
                            <input type="time" name="day_<?php echo $db_index; ?>_end" value="<?php echo esc_attr( $end ); ?>" style="padding:4px; border:1px solid #d1d5db; border-radius:4px;">
                        </div>
                        <?php endforeach; ?>
                        <button type="submit" style="background:#2563eb; color:#fff; border:none; padding:8px 16px; border-radius:4px; font-weight:bold; cursor:pointer; margin-top:10px; width:100%;">💾 Save Master Schedule</button>
                    </form>
                </div>

                <!-- Blocked Dates (Exceptions) -->
                <div style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px;">
                    <h4 style="margin-top:0;">Time Off & Blocked Dates</h4>
                    <form id="cree-add-exception-form" style="margin-bottom: 20px;">
                        <div style="margin-bottom: 10px;">
                            <label style="display:block; font-size:12px; font-weight:bold;">Date to Block:</label>
                            <input type="date" name="exception_date" required style="width:100%; padding:6px; border:1px solid #d1d5db; border-radius:4px;">
                        </div>
                        <div style="margin-bottom: 10px;">
                            <label style="display:block; font-size:12px; font-weight:bold;">Reason (Optional):</label>
                            <input type="text" name="reason" placeholder="e.g. Thanksgiving, Vacation" style="width:100%; padding:6px; border:1px solid #d1d5db; border-radius:4px;">
                        </div>
                        <button type="submit" style="background:#4b5563; color:#fff; border:none; padding:8px 16px; border-radius:4px; font-weight:bold; cursor:pointer; width:100%;">🔒 Add Blocked Date</button>
                    </form>

                    <h5 style="margin-bottom:5px;">Upcoming Blocked Dates:</h5>
                    <?php if ( empty( $exceptions ) ) : ?>
                        <p style="font-size:12px; color:#6b7280; font-style:italic;">No dates currently blocked.</p>
                    <?php else : ?>
                        <ul style="padding-left: 20px; font-size: 13px; margin:0;">
                            <?php foreach ( $exceptions as $exc ) : ?>
                                <li style="margin-bottom: 5px;">
                                    <strong><?php echo esc_html( wp_date( 'M j, Y', strtotime( $exc->exception_date ) ) ); ?></strong> 
                                    <?php echo $exc->reason ? '- ' . esc_html($exc->reason) : ''; ?>
                                    <button type="button" onclick="creeRemoveException(<?php echo intval($exc->id); ?>)" style="background:none; border:none; color:#dc2626; cursor:pointer; font-size:11px; text-decoration:underline;">Remove</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

            </div>
            <?php
            wp_send_json_success( array( 'html' => ob_get_clean() ) );
        }

        public function ajax_save_master_schedule() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            global $wpdb;
            $therapist_id = get_current_user_id();
            $table = $wpdb->prefix . 'cree_consejeria_master_schedule';

            for ( $i = 1; $i <= 7; $i++ ) {
                $is_active = isset($_POST["day_{$i}_active"]) ? 1 : 0;
                $start = sanitize_text_field($_POST["day_{$i}_start"]);
                $end = sanitize_text_field($_POST["day_{$i}_end"]);

                $existing_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE therapist_id = %d AND day_of_week = %d", $therapist_id, $i ) );

                if ( $existing_id ) {
                    $wpdb->update( $table, array('is_active' => $is_active, 'start_time' => $start, 'end_time' => $end), array('id' => $existing_id) );
                } else {
                    $wpdb->insert( $table, array('therapist_id' => $therapist_id, 'day_of_week' => $i, 'start_time' => $start, 'end_time' => $end, 'is_active' => $is_active) );
                }
            }
            wp_send_json_success( array( 'message' => 'Master schedule saved successfully.' ) );
        }

        public function ajax_add_schedule_exception() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            global $wpdb;
            $date = sanitize_text_field( $_POST['exception_date'] );
            $reason = sanitize_text_field( $_POST['reason'] );

            if ( empty($date) ) wp_send_json_error( array( 'message' => 'Date is required.' ) );

            $wpdb->insert( $wpdb->prefix . 'cree_consejeria_schedule_exceptions', array(
                'therapist_id' => get_current_user_id(),
                'exception_date' => $date,
                'reason' => $reason,
                'is_blocked' => 1
            ) );
            wp_send_json_success( array( 'message' => 'Date blocked successfully.' ) );
        }

        public function ajax_remove_schedule_exception() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            global $wpdb;
            $wpdb->delete( $wpdb->prefix . 'cree_consejeria_schedule_exceptions', array( 'id' => intval($_POST['exception_id']) ) );
            wp_send_json_success();
        }

        public function ajax_handle_request() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            global $wpdb;
            $request_id = intval( $_POST['request_id'] );
            $action = sanitize_text_field( $_POST['request_action'] ); // 'approve' or 'deny'
            $requests_table = $wpdb->prefix . 'cree_consejeria_appointment_requests';
            
            $request = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$requests_table} WHERE id = %d", $request_id ) );
            if ( ! $request ) wp_send_json_error( array( 'message' => 'Request not found.' ) );

            $new_status = $action === 'approve' ? 'approved' : 'denied';
            $wpdb->update( $requests_table, array('status' => $new_status), array('id' => $request_id) );

            if ( $action === 'approve' ) {
                // Convert to actual appointment
                $encryption_key = $this->get_encryption_key();
                $wpdb->insert( $wpdb->prefix . 'cree_consejeria_appointments', array(
                    'client_id' => $request->client_id,
                    'therapist_id' => $request->therapist_id,
                    'appointment_date' => $request->requested_datetime,
                    'duration_minutes' => $request->duration_minutes,
                    'status' => 'scheduled',
                    'notes_encrypted' => $wpdb->get_var($wpdb->prepare("SELECT AES_ENCRYPT(%s, %s)", 'Requested via portal', $encryption_key))
                ) );
                
                // Auto-deny overlapping pending requests for this exact slot
                $wpdb->query( $wpdb->prepare(
                    "UPDATE {$requests_table} SET status = 'denied' WHERE therapist_id = %d AND requested_datetime = %s AND status = 'pending'",
                    $request->therapist_id, $request->requested_datetime
                ) );
            }
            wp_send_json_success( array( 'message' => 'Request ' . $new_status . ' successfully.' ) );
        }

        /**
         * AJAX Handler: Calculate Available Slots for a Given Date
         */
        public function ajax_get_available_slots() {
            // check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            // if ( ! is_user_logged_in() ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            // $date = isset( $_POST['date'] ) ? sanitize_text_field( $_POST['date'] ) : '';
            // $therapist_id = isset( $_POST['therapist_id'] ) ? intval( $_POST['therapist_id'] ) : 1; // Default to main therapist if 1-on-1

            // if ( empty( $date ) ) wp_send_json_error( array( 'message' => 'Date is required.' ) );

            if ( isset( $_POST['security'] ) ) { check_ajax_referer( 'cree_secure_note_nonce', 'security' ); } 
            elseif ( isset( $_POST['nonce'] ) ) { check_ajax_referer( 'cree_secure_form_submission', 'nonce' ); } 
            else { wp_send_json_error( array( 'message' => __( 'Security verification failed.', 'cree-consejeria' ) ) ); }

            if ( ! is_user_logged_in() ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            $date = isset( $_POST['date'] ) ? sanitize_text_field( $_POST['date'] ) : '';
            $therapist_id = isset( $_POST['therapist_id'] ) ? intval( $_POST['therapist_id'] ) : 1; 

            // Dynamically find the primary therapist (admin) ID for solo practices
            // $therapists = get_users( array( 'role__in' => array( 'administrator' ), 'number' => 1 ) );
            // $therapist_id = !empty( $therapists ) ? $therapists[0]->ID : 1;

            if ( empty( $date ) ) wp_send_json_error( array( 'message' => 'Date is required.' ) );

            global $wpdb;
            $current_time = current_time('timestamp');
            $requested_timestamp = strtotime( $date );
            
            // Rule 1: Minimum 24-hour notice
            if ( $requested_timestamp < ($current_time + 86400) ) {
                wp_send_json_success( array( 'slots' => array(), 'message' => 'Appointments must be booked at least 24 hours in advance.' ) );
            }

            // Rule 2: Check for Blocked Exceptions
            $is_blocked = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}cree_consejeria_schedule_exceptions WHERE therapist_id = %d AND exception_date = %s",
                $therapist_id, $date
            ) );
            if ( $is_blocked ) wp_send_json_success( array( 'slots' => array(), 'message' => 'This date is blocked by the therapist.' ) );

            // Rule 3: Get Master Schedule for this Day of the Week
            // MySQL DAYOFWEEK returns 1=Sunday, 2=Monday, etc. Our table uses 1=Monday, 7=Sunday.
            $day_of_week = date('N', $requested_timestamp); 
            $master = $wpdb->get_row( $wpdb->prepare(
                "SELECT start_time, end_time FROM {$wpdb->prefix}cree_consejeria_master_schedule WHERE therapist_id = %d AND day_of_week = %d AND is_active = 1",
                $therapist_id, $day_of_week
            ) );
            
            if ( ! $master ) wp_send_json_success( array( 'slots' => array(), 'message' => 'No active hours for this day.' ) );

            // Fetch existing approved/scheduled appointments for this date
            $appointments = $wpdb->get_results( $wpdb->prepare(
                "SELECT appointment_date, duration_minutes FROM {$wpdb->prefix}cree_consejeria_appointments 
                 WHERE therapist_id = %d AND DATE(appointment_date) = %s AND status IN ('scheduled', 'completed')",
                $therapist_id, $date
            ) );

            // Fetch existing pending requests for this date (so we can label them "High Demand")
            $requests = $wpdb->get_results( $wpdb->prepare(
                "SELECT requested_datetime FROM {$wpdb->prefix}cree_consejeria_appointment_requests 
                 WHERE therapist_id = %d AND DATE(requested_datetime) = %s AND status = 'pending'",
                $therapist_id, $date
            ) );

            // Generate 50-min slots + 10-min buffer (60 min intervals)
            $slots = array();
            $start = strtotime( $date . ' ' . $master->start_time );
            $end = strtotime( $date . ' ' . $master->end_time );

            while ( $start + 3000 <= $end ) { // 3000 seconds = 50 mins
                $slot_time_mysql = date('Y-m-d H:i:s', $start);
                
                // Check if overlapping with existing appointments
                $conflict = false;
                foreach ( $appointments as $appt ) {
                    $appt_start = strtotime($appt->appointment_date);
                    $appt_end = $appt_start + ($appt->duration_minutes * 60);
                    if ( $start < $appt_end && ($start + 3000) > $appt_start ) {
                        $conflict = true; break;
                    }
                }

                if ( ! $conflict ) {
                    // Check if there are pending requests for this exact slot
                    $high_demand = false;
                    foreach ( $requests as $req ) {
                        if ( $req->requested_datetime === $slot_time_mysql ) {
                            $high_demand = true; break;
                        }
                    }

                    $slots[] = array(
                        'time' => date('h:i A', $start),
                        'mysql_datetime' => $slot_time_mysql,
                        'high_demand' => $high_demand
                    );
                }
                $start += 3600; // Increment by 60 mins (50 min session + 10 min buffer)
            }

            wp_send_json_success( array( 'slots' => $slots ) );
        }

        /**
         * AJAX Handler: Submit Client Request
         */
        public function ajax_submit_appointment_request_deprecated() {
            // check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            // if ( ! is_user_logged_in() ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            if ( isset( $_POST['security'] ) ) { check_ajax_referer( 'cree_secure_note_nonce', 'security' ); } 
            elseif ( isset( $_POST['nonce'] ) ) { check_ajax_referer( 'cree_secure_form_submission', 'nonce' ); } 
            else { wp_send_json_error( array( 'message' => __( 'Security verification failed.', 'cree-consejeria' ) ) ); }

            if ( ! is_user_logged_in() ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            global $wpdb;
            $client_id = get_current_user_id();
            $therapist_id = isset( $_POST['therapist_id'] ) ? intval( $_POST['therapist_id'] ) : 1;
            $requested_datetime = sanitize_text_field( $_POST['requested_datetime'] );

            if ( empty($requested_datetime) ) wp_send_json_error( array( 'message' => 'Datetime required.' ) );

            $wpdb->insert(
                $wpdb->prefix . 'cree_consejeria_appointment_requests',
                array(
                    'client_id' => $client_id,
                    'therapist_id' => $therapist_id,
                    'requested_datetime' => $requested_datetime,
                    'duration_minutes' => 50,
                    'status' => 'pending'
                )
            );

            wp_send_json_success( array( 'message' => 'Request sent successfully. You will be notified once the therapist reviews it.' ) );
        }

        /**
         * AJAX Handler: Submit Client Request
         */
        public function ajax_submit_appointment_request() {
            if ( isset( $_POST['security'] ) ) { check_ajax_referer( 'cree_secure_note_nonce', 'security' ); } 
            elseif ( isset( $_POST['nonce'] ) ) { check_ajax_referer( 'cree_secure_form_submission', 'nonce' ); } 
            else { wp_send_json_error( array( 'message' => __( 'Security verification failed.', 'cree-consejeria' ) ) ); }

            if ( ! is_user_logged_in() ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            global $wpdb;
            $client_id = get_current_user_id();
            
            // Fix 1: Ensure therapist ID defaults correctly even if an empty string was passed
            $therapist_id = !empty( $_POST['therapist_id'] ) ? intval( $_POST['therapist_id'] ) : 1;

            // Dynamically find the primary therapist (admin) ID for solo practices
            // $therapists = get_users( array( 'role__in' => array( 'administrator' ), 'number' => 1 ) );
            // $therapist_id = !empty( $therapists ) ? $therapists[0]->ID : 1;
            
            $requested_datetime = sanitize_text_field( $_POST['requested_datetime'] );

            if ( empty($requested_datetime) ) wp_send_json_error( array( 'message' => 'Datetime required.' ) );

            // Fix 2: Convert HTML5 datetime format to MySQL format (Remove the "T")
            $requested_datetime = str_replace('T', ' ', $requested_datetime);
            if ( strlen($requested_datetime) == 16 ) {
                $requested_datetime .= ':00';
            }

            // Fix 3: Check if the database actually accepted the insert
            $inserted = $wpdb->insert(
                $wpdb->prefix . 'cree_consejeria_appointment_requests',
                array(
                    'client_id'          => $client_id,
                    'therapist_id'       => $therapist_id,
                    'requested_datetime' => $requested_datetime,
                    'duration_minutes'   => 50,
                    'status'             => 'pending'
                )
            );

            if ( $inserted ) {
                wp_send_json_success( array( 'message' => 'Request sent successfully. You will be notified once the therapist reviews it.' ) );
            } else {
                wp_send_json_error( array( 'message' => 'Database error: Could not save the appointment request.' ) );
            }
        }

        /**
         * Register the EMR Admin Menu
         */
        public function register_admin_menu() {
            add_menu_page(
                'CREE EMR Settings',
                'CREE EMR',
                'manage_options',
                'cree-emr-settings',
                array( $this, 'admin_emr_settings_page' ),
                'dashicons-clipboard',
                55
            );
        }

        /**
         * The EMR Settings Page & CSV Uploader
         */
        public function admin_emr_settings_page() {
            if ( ! current_user_can( 'manage_options' ) ) return;

            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_plan_templates';

            // Handle the CSV Upload
            if ( isset( $_POST['cree_import_csv'] ) && isset( $_FILES['csv_file'] ) ) {
                check_admin_referer( 'cree_import_csv_nonce' );

                $file = $_FILES['csv_file'];
                if ( $file['error'] === 0 && ( $file['type'] === 'text/csv' || end(explode('.', $file['name'])) === 'csv' ) ) {
                    
                    if ( isset( $_POST['clear_existing'] ) && $_POST['clear_existing'] == '1' ) {
                        $wpdb->query("TRUNCATE TABLE {$table_name}");
                    }

                    $handle = fopen( $file['tmp_name'], 'r' );
                    $row_count = 0;
                    $inserted = 0;
                    
                    if ( $handle !== FALSE ) {
                        while ( ( $data = fgetcsv( $handle, 10000, "," ) ) !== FALSE ) {
                            $row_count++;
                            
                            // Skip the first row if it is a header
                            if ( $row_count === 1 && stripos( $data[0], 'category' ) !== false ) continue;

                            if ( count( $data ) >= 3 ) {
                                $category  = sanitize_text_field( trim( $data[0] ) );
                                $section   = sanitize_text_field( trim( $data[1] ) );
                                $item_text = sanitize_textarea_field( trim( $data[2] ) );

                                if ( ! empty( $category ) && ! empty( $section ) && ! empty( $item_text ) ) {
                                    $wpdb->insert(
                                        $table_name,
                                        array(
                                            'category'  => $category,
                                            'section'   => $section,
                                            'item_text' => $item_text
                                        )
                                    );
                                    $inserted++;
                                }
                            }
                        }
                        fclose( $handle );
                        echo '<div class="notice notice-success is-dismissible"><p>✔️ Successfully imported <strong>' . $inserted . '</strong> treatment plan items!</p></div>';
                    } else {
                        echo '<div class="notice notice-error is-dismissible"><p>❌ Failed to open the uploaded file.</p></div>';
                    }
                } else {
                    echo '<div class="notice notice-error is-dismissible"><p>❌ Please upload a valid CSV file.</p></div>';
                }
            }

            // Count existing items
            $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
            ?>
            <div class="wrap">
                <h1 style="color: #01579B;">CREE EMR — Treatment Plan Templates</h1>
                <p>Import your psychological issues, behavioral definitions, and goals from a CSV file. The CSV must have exactly 3 columns: <strong>Category</strong>, <strong>Section</strong>, and <strong>Item Text</strong>.</p>
                
                <div style="background:#fff; border:1px solid #ccd0d4; padding:20px; max-width: 600px; margin-top:20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="margin-top:0;">Upload CSV Data</h3>
                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field( 'cree_import_csv_nonce' ); ?>
                        <input type="hidden" name="cree_import_csv" value="1">
                        
                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: bold; display:block; margin-bottom: 10px;">Select CSV File:</label>
                            <input type="file" name="csv_file" accept=".csv" required style="border: 1px solid #ccc; padding: 10px; width: 100%; border-radius: 4px;">
                        </div>
                        
                        <div style="margin-bottom: 20px; background: #fff8e1; padding: 10px; border-left: 4px solid #ffca28;">
                            <label style="font-size: 13px;">
                                <input type="checkbox" name="clear_existing" value="1"> 
                                <strong>Clear existing templates before importing</strong><br>
                                <span style="color: #666; margin-left: 24px;">Warning: This will delete all currently saved dropdown items.</span>
                            </label>
                        </div>
                        
                        <button type="submit" class="button button-primary" style="background: #03A9F4; border-color: #0288D1; text-shadow: none;">Upload & Import CSV</button>
                    </form>
                </div>

                <div style="margin-top:20px; padding: 15px; background: #e1f5fe; border-left: 4px solid #03A9F4; max-width: 600px; border-radius: 4px;">
                    <p style="margin:0; font-size: 14px;"><strong>Total Template Items in Database:</strong> <?php echo intval($total_items); ?></p>
                </div>
            </div>
            <?php
        }

        /**
         * AJAX Handler: Load the EMR Checkbox Workspace for a Specific Category
         */
        public function ajax_load_emr_template_deprecated() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            
            if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
            }

            global $wpdb;
            $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
            $client_id = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;

            if ( empty( $category ) ) {
                wp_send_json_error( array( 'message' => 'No category specified.' ) );
            }

            $table_name = $wpdb->prefix . 'cree_consejeria_plan_templates';
            
            // Fetch all items for this category
            $items = $wpdb->get_results( $wpdb->prepare( 
                "SELECT id, section, item_text FROM {$table_name} WHERE category = %s ORDER BY id ASC", 
                $category 
            ) );

            if ( empty( $items ) ) {
                wp_send_json_error( array( 'html' => '<p style="color:red;">No template data found for this category.</p>' ) );
            }

            // Group items by Section
            $grouped_items = array();
            foreach ( $items as $item ) {
                $grouped_items[ $item->section ][] = $item;
            }

            ob_start();
            ?>
            <form id="cree-emr-save-plan-form">
                <input type="hidden" name="client_id" value="<?php echo intval( $client_id ); ?>">
                <input type="hidden" name="primary_category" value="<?php echo esc_attr( $category ); ?>">
                
                <div style="background: #ffffff; padding: 20px; border: 1px solid #e5e7eb; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="margin-top: 0; color: #1e3a8a; border-bottom: 2px solid #e1f5fe; padding-bottom: 10px;">
                        Treatment Plan Builder: <?php echo esc_html( $category ); ?>
                    </h3>
                    
                    <?php foreach ( $grouped_items as $section_name => $section_items ) : ?>
                        <div class="emr-section-block" style="margin-top: 25px;">
                            <h4 style="color: #01579B; margin-bottom: 15px; text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px;"><?php echo esc_html( $section_name ); ?></h4>
                            
                            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 15px;">
                                <?php foreach ( $section_items as $item ) : ?>
                                    <label style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: #374151; cursor: pointer; padding: 8px; background: #f9fafb; border-radius: 4px; border: 1px solid #f3f4f6; transition: background 0.2s;">
                                        <input type="checkbox" name="emr_items[<?php echo esc_attr( $section_name ); ?>][]" value="<?php echo esc_attr( $item->item_text ); ?>" style="margin-top: 3px;">
                                        <span style="line-height: 1.4;"><?php echo esc_html( $item->item_text ); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Custom Free-Text Addition for this Section -->
                            <div style="margin-top: 10px; padding-left: 28px;">
                                <input type="text" name="emr_custom[<?php echo esc_attr( $section_name ); ?>]" placeholder="+ Add a custom item for <?php echo esc_attr( $section_name ); ?>..." style="width: 100%; padding: 8px; font-size: 13px; border: 1px dashed #d1d5db; border-radius: 4px; background: #fff;">
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="margin-top: 35px; padding-top: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                        <label style="font-size: 13px; font-weight: bold; color: #4b5563; display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="is_visible_to_client" value="1"> 
                            Publish to Client Portal
                        </label>
                        <button type="button" id="cree-save-emr-plan-btn" class="thai-btn-sapphire" style="padding: 12px 24px; font-size: 14px;">💾 Encrypt & Save Plan</button>
                    </div>
                </div>
            </form>
            <?php
            wp_send_json_success( array( 'html' => ob_get_clean() ) );
        }

        /**
         * AJAX Handler: Load the EMR Checkbox Workspace (Supports New and Editing)
         */
        public function ajax_load_emr_template() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );

            global $wpdb;
            $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
            $client_id = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
            $plan_id = isset( $_POST['plan_id'] ) ? intval( $_POST['plan_id'] ) : 0;

            if ( empty( $category ) ) wp_send_json_error( array( 'message' => 'No category specified.' ) );

            $saved_plan_data = array();
            $is_visible = 0;

            // If editing, fetch the existing plan data
            if ( $plan_id > 0 ) {
                $plan_table = $wpdb->prefix . 'cree_consejeria_client_plans';
                $encryption_key = $this->get_encryption_key();
                $saved_record = $wpdb->get_row( $wpdb->prepare(
                    "SELECT is_visible_to_client, CAST(AES_DECRYPT(plan_data_encrypted, %s) AS CHAR) AS decrypted_data FROM {$plan_table} WHERE id = %d",
                    $encryption_key, $plan_id
                ) );
                if ( $saved_record ) {
                    $saved_plan_data = json_decode( $saved_record->decrypted_data, true );
                    $is_visible = $saved_record->is_visible_to_client;
                }
            }

            $table_name = $wpdb->prefix . 'cree_consejeria_plan_templates';
            $items = $wpdb->get_results( $wpdb->prepare( "SELECT id, section, item_text FROM {$table_name} WHERE category = %s ORDER BY id ASC", $category ) );

            if ( empty( $items ) ) wp_send_json_error( array( 'html' => '<p style="color:red;">No template data found for this category.</p>' ) );

            $grouped_items = array();
            foreach ( $items as $item ) {
                $grouped_items[ $item->section ][] = $item;
            }

            ob_start();
            ?>
            <form id="cree-emr-save-plan-form">
                <input type="hidden" name="client_id" value="<?php echo intval( $client_id ); ?>">
                <input type="hidden" name="primary_category" value="<?php echo esc_attr( $category ); ?>">
                <input type="hidden" name="plan_id" value="<?php echo intval( $plan_id ); ?>">
                
                <div style="background: #ffffff; padding: 20px; border: 1px solid #e5e7eb; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="margin-top: 0; color: #1e3a8a; border-bottom: 2px solid #e1f5fe; padding-bottom: 10px;">
                        <?php echo $plan_id > 0 ? 'Editing Plan: ' : 'Treatment Plan Builder: '; ?><?php echo esc_html( $category ); ?>
                    </h3>
                    
                    <?php foreach ( $grouped_items as $section_name => $section_items ) : ?>
                        <div class="emr-section-block" style="margin-top: 25px;">
                            <h4 style="color: #01579B; margin-bottom: 15px; text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px;"><?php echo esc_html( $section_name ); ?></h4>
                            
                            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 15px;">
                                <?php 
                                $template_texts = array();
                                foreach ( $section_items as $item ) : 
                                    $template_texts[] = $item->item_text;
                                    $is_checked = ( isset($saved_plan_data[$section_name]) && in_array($item->item_text, $saved_plan_data[$section_name]) ) ? 'checked' : '';
                                ?>
                                    <label style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: #374151; cursor: pointer; padding: 8px; background: #f9fafb; border-radius: 4px; border: 1px solid #f3f4f6; transition: background 0.2s;">
                                        <input type="checkbox" name="emr_items[<?php echo esc_attr( $section_name ); ?>][]" value="<?php echo esc_attr( $item->item_text ); ?>" style="margin-top: 3px;" <?php echo $is_checked; ?>>
                                        <span style="line-height: 1.4;"><?php echo esc_html( $item->item_text ); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php
                            // Extract any custom typed text that wasn't in the standard CSV list
                            $custom_text = '';
                            if ( isset($saved_plan_data[$section_name]) ) {
                                $custom_items = array_diff($saved_plan_data[$section_name], $template_texts);
                                if ( ! empty($custom_items) ) {
                                    $custom_text = implode(' | ', $custom_items);
                                }
                            }
                            ?>
                            <div style="margin-top: 10px; padding-left: 28px;">
                                <input type="text" name="emr_custom[<?php echo esc_attr( $section_name ); ?>]" value="<?php echo esc_attr($custom_text); ?>" placeholder="+ Add a custom item for <?php echo esc_attr( $section_name ); ?>..." style="width: 100%; padding: 8px; font-size: 13px; border: 1px dashed #d1d5db; border-radius: 4px; background: #fff;">
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="margin-top: 35px; padding-top: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                        <label style="font-size: 13px; font-weight: bold; color: #4b5563; display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="is_visible_to_client" value="1" <?php checked( $is_visible, 1 ); ?>> 
                            Publish to Client Portal
                        </label>
                        <button type="button" id="cree-save-emr-plan-btn" class="thai-btn-sapphire" style="padding: 12px 24px; font-size: 14px;">
                            <?php echo $plan_id > 0 ? '💾 Update & Encrypt Plan' : '💾 Encrypt & Save Plan'; ?>
                        </button>
                    </div>
                </div>
            </form>
            <?php
            wp_send_json_success( array( 'html' => ob_get_clean() ) );
        }

        /**
         * AJAX Handler: Save & Encrypt the Treatment Plan
         */
        public function ajax_save_emr_plan_deprecated() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            $client_id = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
            $primary_category = isset( $_POST['primary_category'] ) ? sanitize_text_field( $_POST['primary_category'] ) : '';
            $is_visible = isset( $_POST['is_visible_to_client'] ) ? 1 : 0;
            
            if ( ! $client_id || empty( $primary_category ) ) {
                wp_send_json_error( array( 'message' => 'Missing client ID or category.' ) );
            }

            // 1. Process Checked Items and Custom Text
            $emr_items  = isset( $_POST['emr_items'] ) && is_array( $_POST['emr_items'] ) ? $_POST['emr_items'] : array();
            $emr_custom = isset( $_POST['emr_custom'] ) && is_array( $_POST['emr_custom'] ) ? $_POST['emr_custom'] : array();
            
            $plan_data = array();
            
            // Merge all sections securely
            $all_sections = array_unique( array_merge( array_keys( $emr_items ), array_keys( $emr_custom ) ) );
            
            foreach ( $all_sections as $section ) {
                $section_clean = sanitize_text_field( $section );
                $plan_data[$section_clean] = array();
                
                // Add checked boxes
                if ( ! empty( $emr_items[$section] ) && is_array( $emr_items[$section] ) ) {
                    foreach ( $emr_items[$section] as $item ) {
                        $plan_data[$section_clean][] = sanitize_text_field( $item );
                    }
                }
                
                // Add custom typed text
                if ( ! empty( $emr_custom[$section] ) ) {
                    $custom_clean = sanitize_text_field( trim( $emr_custom[$section] ) );
                    if ( ! empty( $custom_clean ) ) {
                        $plan_data[$section_clean][] = $custom_clean;
                    }
                }
                
                // Remove empty sections to save database space
                if ( empty( $plan_data[$section_clean] ) ) {
                    unset( $plan_data[$section_clean] );
                }
            }

            if ( empty( $plan_data ) ) {
                wp_send_json_error( array( 'message' => 'Cannot save an empty plan. Please check at least one box or add custom text.' ) );
            }

            // 2. Encrypt and Save
            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_client_plans';
            $encryption_key = $this->get_encryption_key();
            $json_payload = wp_json_encode( $plan_data );

            $inserted = $wpdb->query( $wpdb->prepare(
                "INSERT INTO {$table_name} (client_id, therapist_id, primary_category, plan_data_encrypted, is_visible_to_client)
                 VALUES (%d, %d, %s, AES_ENCRYPT(%s, %s), %d)",
                $client_id, get_current_user_id(), $primary_category, $json_payload, $encryption_key, $is_visible
            ) );

            // if ( $inserted ) {
            //     wp_send_json_success();
            // } else {
            //     wp_send_json_error( array( 'message' => 'Database encryption/insertion failed.' ) );
            // }

            if ( $inserted ) {
                // FEATURE 3: Auto-generate Trackable Goals from Short Term Objectives
                if ( ! empty( $plan_data['Short Term Objectives'] ) ) {
                    $goals_table = $wpdb->prefix . 'cree_consejeria_treatment_goals';
                    foreach ( $plan_data['Short Term Objectives'] as $objective ) {
                        $wpdb->query( $wpdb->prepare(
                            "INSERT INTO {$goals_table} (client_id, therapist_id, goal_title, goal_description_encrypted, status, progress_percentage) 
                             VALUES (%d, %d, %s, AES_ENCRYPT(%s, %s), 'in_progress', 0)",
                            $client_id, get_current_user_id(), 'Short Term Objective', $objective, $encryption_key
                        ) );
                    }
                }
                wp_send_json_success();
            } else {
                wp_send_json_error( array( 'message' => 'Database encryption/insertion failed.' ) );
            }
        }

        /**
         * AJAX Handler: Save, Update & Encrypt the Treatment Plan
         */
        public function ajax_save_emr_plan() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            $client_id = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
            $plan_id = isset( $_POST['plan_id'] ) ? intval( $_POST['plan_id'] ) : 0;
            $primary_category = isset( $_POST['primary_category'] ) ? sanitize_text_field( $_POST['primary_category'] ) : '';
            $is_visible = isset( $_POST['is_visible_to_client'] ) ? 1 : 0;
            
            if ( ! $client_id || empty( $primary_category ) ) {
                wp_send_json_error( array( 'message' => 'Missing client ID or category.' ) );
            }

            $emr_items  = isset( $_POST['emr_items'] ) && is_array( $_POST['emr_items'] ) ? $_POST['emr_items'] : array();
            $emr_custom = isset( $_POST['emr_custom'] ) && is_array( $_POST['emr_custom'] ) ? $_POST['emr_custom'] : array();
            
            $plan_data = array();
            $all_sections = array_unique( array_merge( array_keys( $emr_items ), array_keys( $emr_custom ) ) );
            
            foreach ( $all_sections as $section ) {
                $section_clean = sanitize_text_field( $section );
                $plan_data[$section_clean] = array();
                
                if ( ! empty( $emr_items[$section] ) && is_array( $emr_items[$section] ) ) {
                    foreach ( $emr_items[$section] as $item ) {
                        $plan_data[$section_clean][] = sanitize_text_field( $item );
                    }
                }
                
                if ( ! empty( $emr_custom[$section] ) ) {
                    $custom_clean = sanitize_text_field( trim( $emr_custom[$section] ) );
                    if ( ! empty( $custom_clean ) ) {
                        $plan_data[$section_clean][] = $custom_clean;
                    }
                }
                
                if ( empty( $plan_data[$section_clean] ) ) {
                    unset( $plan_data[$section_clean] );
                }
            }

            if ( empty( $plan_data ) ) {
                wp_send_json_error( array( 'message' => 'Cannot save an empty plan. Please check at least one box or add custom text.' ) );
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_client_plans';
            $encryption_key = $this->get_encryption_key();
            $json_payload = wp_json_encode( $plan_data );
            $success = false;

            if ( $plan_id > 0 ) {
                // UPDATE EXISTING PLAN
                $success = $wpdb->query( $wpdb->prepare(
                    "UPDATE {$table_name} SET primary_category = %s, plan_data_encrypted = AES_ENCRYPT(%s, %s), is_visible_to_client = %d WHERE id = %d",
                    $primary_category, $json_payload, $encryption_key, $is_visible, $plan_id
                ) ) !== false;
            } else {
                // INSERT NEW PLAN
                $success = $wpdb->query( $wpdb->prepare(
                    "INSERT INTO {$table_name} (client_id, therapist_id, primary_category, plan_data_encrypted, is_visible_to_client)
                     VALUES (%d, %d, %s, AES_ENCRYPT(%s, %s), %d)",
                    $client_id, get_current_user_id(), $primary_category, $json_payload, $encryption_key, $is_visible
                ) );

                // Auto-generate Trackable Goals only on initial creation
                if ( $success && ! empty( $plan_data['Short Term Objectives'] ) ) {
                    $goals_table = $wpdb->prefix . 'cree_consejeria_treatment_goals';
                    foreach ( $plan_data['Short Term Objectives'] as $objective ) {
                        $wpdb->query( $wpdb->prepare(
                            "INSERT INTO {$goals_table} (client_id, therapist_id, goal_title, goal_description_encrypted, status, progress_percentage) 
                             VALUES (%d, %d, %s, AES_ENCRYPT(%s, %s), 'in_progress', 0)",
                            $client_id, get_current_user_id(), 'Short Term Objective', $objective, $encryption_key
                        ) );
                    }
                }
            }

            if ( $success ) {
                wp_send_json_success();
            } else {
                wp_send_json_error( array( 'message' => 'Database encryption/insertion failed.' ) );
            }
        }

        /**
         * AJAX Handler: Fetch and Decrypt Client Plans History
         */
        public function ajax_load_client_plans() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            $client_id = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
            if ( ! $client_id ) wp_send_json_error( array( 'message' => 'No client ID.' ) );

            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_client_plans';
            $encryption_key = $this->get_encryption_key();

            $plans = $wpdb->get_results( $wpdb->prepare(
                "SELECT id, primary_category, created_at, is_visible_to_client, CAST(AES_DECRYPT(plan_data_encrypted, %s) AS CHAR) AS decrypted_data 
                 FROM {$table_name} WHERE client_id = %d ORDER BY created_at DESC",
                $encryption_key, $client_id
            ) );

            ob_start();
            if ( empty( $plans ) ) {
                echo '<p style="font-style:italic; color:#6b7280;">No treatment plans have been created for this client yet.</p>';
            } else {
                foreach ( $plans as $plan ) {
                    $plan_data = json_decode( $plan->decrypted_data, true );
                    if ( ! is_array( $plan_data ) ) continue;
                    ?>
                    <div class="cree-goal-card" style="background: #ffffff; border: 1px solid #e5e7eb; border-left: 5px solid #03A9F4; padding: 16px; margin-bottom: 15px; border-radius: 6px;">
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #f3f4f6; padding-bottom: 10px;">
                            <strong style="font-size: 16px; color: #111827;"><?php echo esc_html( $plan->primary_category ); ?></strong>

                            <div style="display: flex; gap: 10px; align-items: center;" class="no-print">
                                <button type="button" onclick="creeTogglePlanVisibility(<?php echo intval( $plan->id ); ?>, <?php echo $plan->is_visible_to_client ? 0 : 1; ?>)" style="font-size: 11px; background: <?php echo $plan->is_visible_to_client ? '#e1f5fe' : '#f3f4f6'; ?>; color: <?php echo $plan->is_visible_to_client ? '#01579B' : '#4b5563'; ?>; padding: 3px 8px; border-radius: 4px; border: 1px solid <?php echo $plan->is_visible_to_client ? '#03A9F4' : '#d1d5db'; ?>; font-weight: bold; cursor:pointer; transition: all 0.2s;">
                                    <?php echo $plan->is_visible_to_client ? '👁️ Client Visible (Click to Hide)' : '🔒 Therapist Only (Click to Show)'; ?>
                                </button>
                                <span style="font-size: 12px; color: #6b7280; font-weight: bold;"><?php echo esc_html( wp_date( 'M j, Y', strtotime( $plan->created_at ) ) ); ?></span>
                                <!-- Edit button -->
                                <button type="button" onclick="creeEditTreatmentPlan(<?php echo intval( $plan->id ); ?>, '<?php echo esc_js( $plan->primary_category ); ?>')" style="background:#3b82f6; color:#fff; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">✏️</button>
                                <!-- Print button -->
                                <button type="button" onclick="creePrintTreatmentPlan(this)" style="background:#10b981; color:#fff; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">🖨️</button>
                                <!-- Delete button -->
                                <button type="button" onclick="creeDeleteTreatmentPlan(<?php echo intval( $plan->id ); ?>)" style="background:#ef4444; color:#fff; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">🗑️</button>
                            </div>

                        </div>
                        
                        <!-- Accordion Wrapper -->
                        <div class="cree-accordion-toggle" style="cursor: pointer; color: #03A9F4; font-size: 13px; font-weight: bold; margin-bottom: 10px;">
                            <span class="toggle-icon">▼</span> View Full Plan Details
                        </div>

                        <div class="cree-accordion-content" style="display: none; padding-top: 10px; border-top: 1px dashed #e5e7eb;">
                            <?php foreach ( $plan_data as $section => $items ) : ?>
                                <div style="margin-bottom: 15px;">
                                    <h5 style="margin: 0 0 5px 0; color: #4b5563; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo esc_html( $section ); ?></h5>
                                    <ul style="margin: 0; padding-left: 20px; font-size: 13.5px; color: #1f2937; line-height: 1.5;">
                                        <?php foreach ( $items as $item ) : ?>
                                            <li style="margin-bottom: 4px;"><?php echo esc_html( $item ); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div> <!-- Closes the main cree-goal-card div -->
                    <?php
                }
            }
            wp_send_json_success( array( 'html' => ob_get_clean() ) );
        }

        /**
         * AJAX Handler: Fetch Client Visible Treatment Plans
         */
        public function ajax_get_client_visible_plans() {
            if ( isset( $_POST['security'] ) ) { check_ajax_referer( 'cree_secure_note_nonce', 'security' ); } 
            elseif ( isset( $_POST['nonce'] ) ) { check_ajax_referer( 'cree_secure_form_submission', 'nonce' ); } 
            else { wp_send_json_error( array( 'message' => 'Security failed.' ) ); }

            if ( ! is_user_logged_in() ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            $client_id = get_current_user_id();
            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_client_plans';
            $encryption_key = $this->get_encryption_key();

            $plans = $wpdb->get_results( $wpdb->prepare(
                "SELECT primary_category, created_at, CAST(AES_DECRYPT(plan_data_encrypted, %s) AS CHAR) AS decrypted_data 
                 FROM {$table_name} WHERE client_id = %d AND is_visible_to_client = 1 ORDER BY created_at DESC",
                $encryption_key, $client_id
            ) );

            ob_start();
            if ( empty( $plans ) ) {
                echo '<p style="font-style:italic; color:#6b7280;">No active treatment plans are currently available for review.</p>';
            } else {
                foreach ( $plans as $plan ) {
                    $plan_data = json_decode( $plan->decrypted_data, true );
                    if ( ! is_array( $plan_data ) ) continue;
                    ?>
                    <div style="background: #ffffff; border: 1px solid #e5e7eb; border-left: 5px solid #03A9F4; padding: 20px; margin-bottom: 20px; border-radius: 8px;">
                        <div style="border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 15px;">
                            <h3 style="margin: 0; color: #1e3a8a;"><?php echo esc_html( $plan->primary_category ); ?></h3>
                            <span style="font-size: 12px; color: #6b7280;">Created: <?php echo esc_html( wp_date( 'F j, Y', strtotime( $plan->created_at ) ) ); ?></span>
                        </div>
                        <?php foreach ( $plan_data as $section => $items ) : ?>
                            <div style="margin-bottom: 15px;">
                                <h4 style="margin: 0 0 8px 0; color: #01579B; font-size: 14px;"><?php echo esc_html( $section ); ?></h4>
                                <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #374151; line-height: 1.5;">
                                    <?php foreach ( $items as $item ) : ?>
                                        <li style="margin-bottom: 6px;"><?php echo esc_html( $item ); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php
                }
            }
            wp_send_json_success( array( 'html' => ob_get_clean() ) );
        }

        /**
         * AJAX Handler: Toggle Treatment Plan Visibility
         */
        public function ajax_toggle_plan_visibility() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            $plan_id = isset( $_POST['plan_id'] ) ? intval( $_POST['plan_id'] ) : 0;
            $new_status = isset( $_POST['is_visible'] ) ? intval( $_POST['is_visible'] ) : 0;

            if ( $plan_id > 0 ) {
                global $wpdb;
                $table = $wpdb->prefix . 'cree_consejeria_client_plans';
                $wpdb->update( $table, array( 'is_visible_to_client' => $new_status ), array( 'id' => $plan_id ), array( '%d' ), array( '%d' ) );
                wp_send_json_success();
            } else {
                wp_send_json_error( array( 'message' => 'Invalid plan ID.' ) );
            }
        }

        /**
         * WP Cron: Send 24-Hour Appointment Reminders
         */
        public function cron_send_appointment_reminders() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_appointments';
            
            // Find appointments scheduled between 24 and 25 hours from current server time
            $query = "SELECT * FROM {$table_name} 
                      WHERE status = 'scheduled' 
                      AND appointment_date >= DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 24 HOUR) 
                      AND appointment_date < DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 25 HOUR)";
                      
            $upcoming_appts = $wpdb->get_results( $query );

            foreach ( $upcoming_appts as $appt ) {
                $client = get_userdata( $appt->client_id );
                if ( $client ) {
                    // Added 'T' to wp_date to automatically output the Time Zone (e.g., CST/EST)
                    $time = wp_date( 'l, F j, Y \a\t g:i A T', strtotime( $appt->appointment_date ) );
                    $subject = '[CREE Consejería] Recordatorio de Cita / Appointment Reminder';
                    
                    $message = "Hola {$client->first_name},\n\nEste es un recordatorio automático de que su próxima sesión está programada para mañana:\n\n";
                    $message .= "Fecha y Hora: {$time}\n\n";
                    $message .= "Si necesita reprogramar, por favor acceda a su portal lo antes posible.\n\nAtentamente,\nEquipo de CREE Consejería";
                    
                    wp_mail( $client->user_email, $subject, $message, array('Content-Type: text/plain; charset=UTF-8') );
                }
            }
        }

        /**
         * AJAX Handler: Delete a Treatment Plan
         */
        public function ajax_delete_emr_plan() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );

            $plan_id = isset( $_POST['plan_id'] ) ? intval( $_POST['plan_id'] ) : 0;

            if ( $plan_id > 0 ) {
                global $wpdb;
                $table = $wpdb->prefix . 'cree_consejeria_client_plans';
                $deleted = $wpdb->delete( $table, array( 'id' => $plan_id ), array( '%d' ) );
                
                if ( $deleted ) {
                    wp_send_json_success();
                } else {
                    wp_send_json_error( array( 'message' => 'Could not delete the plan from the database.' ) );
                }
            } else {
                wp_send_json_error( array( 'message' => 'Invalid plan ID.' ) );
            }
        }

        /**
         * AJAX: Save New Billing Charge
         */
        public function ajax_save_billing() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            $client_id = intval( $_POST['client_id'] );
            $amount = floatval( $_POST['amount'] );
            $description = sanitize_text_field( $_POST['description'] );
            $cpt_code = sanitize_text_field( $_POST['cpt_code'] );
            
            if ( ! $client_id || ! $amount ) wp_send_json_error( array( 'message' => 'Client ID and Amount are required.' ) );

            global $wpdb;
            $wpdb->insert( $wpdb->prefix . 'cree_consejeria_billing', array(
                'client_id'    => $client_id,
                'therapist_id' => get_current_user_id(),
                'amount'       => $amount,
                'description'  => $description,
                'cpt_code'     => $cpt_code,
                'status'       => 'unpaid'
            ) );
            
            wp_send_json_success( array( 'message' => 'Charge added successfully.' ) );
        }

        /**
         * AJAX: Update Billing Status (Paid/Void)
         */
        public function ajax_update_billing_status() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            $billing_id = intval( $_POST['billing_id'] );
            $status = sanitize_text_field( $_POST['status'] );
            $paid_at = ($status === 'paid') ? current_time('mysql') : null;

            global $wpdb;
            $wpdb->update( 
                $wpdb->prefix . 'cree_consejeria_billing', 
                array( 'status' => $status, 'paid_at' => $paid_at ), 
                array( 'id' => $billing_id ) 
            );
            wp_send_json_success( array( 'message' => 'Status updated successfully.' ) );
        }

        /**
         * AJAX: Generate Superbill HTML for Printing
         */
        public function ajax_generate_superbill() {
            // Check Client Nonce first, then Therapist Nonce
            if ( isset( $_POST['nonce'] ) ) { check_ajax_referer( 'cree_secure_form_submission', 'nonce' ); } 
            elseif ( isset( $_POST['security'] ) ) { check_ajax_referer( 'cree_secure_note_nonce', 'security' ); } 
            else { wp_send_json_error( array( 'message' => 'Security check failed.' ) ); }

            $billing_id = intval( $_POST['billing_id'] );
            global $wpdb;
            $billing = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cree_consejeria_billing WHERE id = %d", $billing_id ) );
            if ( ! $billing ) wp_send_json_error( array( 'message' => 'Record not found.' ) );

            // Security: Ensure clients can only download their own bills
            if ( ! current_user_can( 'edit_posts' ) && $billing->client_id != get_current_user_id() ) {
                wp_send_json_error( array( 'message' => 'Unauthorized access to this billing record.' ) );
            }

            $client = get_userdata( $billing->client_id );
            $therapist = get_userdata( $billing->therapist_id );
            
            // Try to find a DOB from the intake records
            $dob = $wpdb->get_var($wpdb->prepare("SELECT dob FROM {$wpdb->prefix}cree_consejeria_client_forms_master WHERE wp_id = %d LIMIT 1", $billing->client_id));
            $dob = $dob ? wp_date('m/d/Y', strtotime($dob)) : 'N/A';

            ob_start();
            ?>
            <div style="font-family: Arial, sans-serif; color: #333; max-width: 800px; margin: 0 auto; padding: 40px; line-height: 1.5;">
                <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 30px;">
                    <div>
                        <h1 style="margin: 0; color: #1e3a8a; font-size: 24px;">CREE Consejería</h1>
                        <p style="margin: 5px 0 0 0; font-size: 14px;">1219 E Broadway St., Sweetwater, TX 79556</p>
                        <p style="margin: 0; font-size: 14px;">Phone: 325-219-4039</p>
                        <p style="margin: 0; font-size: 14px;">Provider: <?php echo esc_html($therapist->display_name); ?> (LPC-A #90612)</p>
                    </div>
                    <div style="text-align: right;">
                        <h2 style="margin: 0; font-size: 28px; color: #4b5563;">SUPERBILL</h2>
                        <p style="margin: 5px 0 0 0;"><strong>Date:</strong> <?php echo wp_date('m/d/Y', strtotime($billing->created_at)); ?></p>
                        <p style="margin: 0;"><strong>Invoice #:</strong> INV-<?php echo 1000 + $billing->id; ?></p>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 40px;">
                    <div>
                        <h3 style="margin: 0 0 5px 0; font-size: 16px;">Patient Information:</h3>
                        <p style="margin: 0;"><strong>Name:</strong> <?php echo esc_html($client->display_name); ?></p>
                        <p style="margin: 0;"><strong>DOB:</strong> <?php echo esc_html($dob); ?></p>
                    </div>
                    <div style="text-align: right;">
                        <h3 style="margin: 0 0 5px 0; font-size: 16px;">Account Status:</h3>
                        <p style="margin: 0; font-weight: bold; color: <?php echo $billing->status === 'paid' ? '#10b981' : '#dc2626'; ?>;">
                            <?php echo strtoupper($billing->status); ?>
                        </p>
                    </div>
                </div>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
                    <thead>
                        <tr style="background: #f3f4f6; text-align: left;">
                            <th style="padding: 12px; border: 1px solid #d1d5db;">Date of Service</th>
                            <th style="padding: 12px; border: 1px solid #d1d5db;">Description</th>
                            <th style="padding: 12px; border: 1px solid #d1d5db;">CPT Code</th>
                            <th style="padding: 12px; border: 1px solid #d1d5db; text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 12px; border: 1px solid #d1d5db;"><?php echo wp_date('m/d/Y', strtotime($billing->created_at)); ?></td>
                            <td style="padding: 12px; border: 1px solid #d1d5db;"><?php echo esc_html($billing->description); ?></td>
                            <td style="padding: 12px; border: 1px solid #d1d5db;"><?php echo esc_html($billing->cpt_code ? $billing->cpt_code : 'N/A'); ?></td>
                            <td style="padding: 12px; border: 1px solid #d1d5db; text-align: right;">$<?php echo number_format($billing->amount, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
                <div style="text-align: center; color: #6b7280; font-size: 12px; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                    This document serves as a receipt for services rendered. It may be submitted to your insurance carrier for out-of-network reimbursement.
                </div>
            </div>
            <?php
            wp_send_json_success( array( 'html' => ob_get_clean() ) );
        }

        /**
         * AJAX: Load Therapist Home Analytics Dashboard
         */
        public function ajax_load_therapist_dashboard() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            global $wpdb;
            $therapist_id = get_current_user_id();
            $today_date   = current_time( 'Y-m-d' );
            
            // 1. Calculate Unpaid Billing
            $unpaid_total = $wpdb->get_var( $wpdb->prepare( 
                "SELECT SUM(amount) FROM {$wpdb->prefix}cree_consejeria_billing WHERE therapist_id = %d AND status = 'unpaid'", 
                $therapist_id 
            ) );
            
            // 2. Count Pending Appointment Requests
            $pending_reqs = $wpdb->get_var( $wpdb->prepare( 
                "SELECT COUNT(id) FROM {$wpdb->prefix}cree_consejeria_appointment_requests WHERE therapist_id = %d AND status = 'pending'", 
                $therapist_id 
            ) );
            
            // 3. Count Total Clients
            // $total_clients = count( get_users( array( 'role__in' => array( 'subscriber', 'customer', 'client' ) ) ) );

            // 3. Count Total Active Clients Only
            $active_clients_args = array(
                'role__in'   => array( 'subscriber', 'customer', 'client' ),
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'cree_client_status',
                        'value'   => 'inactive',
                        'compare' => '!='
                    ),
                    array(
                        'key'     => 'cree_client_status',
                        'compare' => 'NOT EXISTS' // Catches older accounts that don't have a status yet
                    )
                )
            );
            $total_clients = count( get_users( $active_clients_args ) );
            
            // 4. Fetch Today's Schedule
            $appointments_table = $wpdb->prefix . 'cree_consejeria_appointments';
            $todays_appts = $wpdb->get_results( $wpdb->prepare(
                "SELECT a.id, a.client_id, a.appointment_date, a.duration_minutes, a.telehealth_link, u.display_name AS client_name 
                 FROM {$appointments_table} a
                 LEFT JOIN {$wpdb->users} u ON a.client_id = u.ID
                 WHERE a.therapist_id = %d AND DATE(a.appointment_date) = %s AND a.status = 'scheduled'
                 ORDER BY a.appointment_date ASC",
                $therapist_id, $today_date
            ) );

            ob_start();
            ?>
            <div style="animation: fadeIn 0.3s ease-in-out;">
                <h2 style="margin-top: 0; color: #1e3a8a; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">Practice Overview</h2>
                
                <!-- Key Metrics Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; margin-top: 20px;">
                    <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; border-top: 4px solid #3b82f6; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <p style="margin: 0; font-size: 13px; color: #6b7280; font-weight: bold; text-transform: uppercase;">Active Clients</p>
                        <h3 style="margin: 5px 0 0 0; font-size: 28px; color: #111827;"><?php echo intval( $total_clients ); ?></h3>
                    </div>
                    
                    <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; border-top: 4px solid #f59e0b; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <p style="margin: 0; font-size: 13px; color: #6b7280; font-weight: bold; text-transform: uppercase;">Pending Requests</p>
                        <h3 style="margin: 5px 0 0 0; font-size: 28px; color: #111827;"><?php echo intval( $pending_reqs ); ?></h3>
                        <?php if ( $pending_reqs > 0 ) : ?>
                            <a href="#" onclick="creeLoadGlobalSchedule(); return false;" style="font-size: 12px; color: #d97706; text-decoration: none; font-weight: bold; margin-top: 5px; display: inline-block;">Review Requests →</a>
                        <?php endif; ?>
                    </div>
                    
                    <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; border-top: 4px solid #ef4444; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <p style="margin: 0; font-size: 13px; color: #6b7280; font-weight: bold; text-transform: uppercase;">Unpaid Balances</p>
                        <h3 style="margin: 5px 0 0 0; font-size: 28px; color: #111827;">$<?php echo number_format( floatval( $unpaid_total ), 2 ); ?></h3>
                    </div>
                </div>

                <!-- Today's Itinerary -->
                <h3 style="color: #374151; margin-bottom: 15px;">📅 Today's Itinerary</h3>
                <?php if ( empty( $todays_appts ) ) : ?>
                    <div style="background: #f9fafb; padding: 25px; border-radius: 8px; border: 1px dashed #d1d5db; text-align: center; color: #6b7280;">
                        You have no appointments scheduled for today.
                    </div>
                <?php else : ?>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <?php foreach ( $todays_appts as $appt ) : 
                            $appt_time = strtotime( $appt->appointment_date );
                        ?>
                            <div style="background: #fff; border: 1px solid #e5e7eb; border-left: 4px solid #10b981; padding: 15px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="font-size: 16px; color: #111827; display: block; margin-bottom: 4px;">
                                        <?php echo esc_html( wp_date( 'g:i A', $appt_time ) ); ?> - <?php echo esc_html( $appt->client_name ); ?>
                                    </strong>
                                    <span style="font-size: 13px; color: #4b5563;">Duration: <?php echo intval( $appt->duration_minutes ); ?> Min</span>
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <?php if ( ! empty( $appt->telehealth_link ) ) : ?>
                                        <a href="<?php echo esc_url( $appt->telehealth_link ); ?>" target="_blank" style="background: #2563eb; color: #fff; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold;">🎥 Launch Video</a>
                                    <?php endif; ?>
                                    <button onclick="creeSelectClient(<?php echo intval( $appt->client_id ); ?>, null, 'prof-notes')" style="background: #4b5563; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer;">📝 Take Notes</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            wp_send_json_success( array( 'html' => ob_get_clean() ) );
        }

        /**
         * AJAX: Permanently Delete Client & All Associated Records
         */
        public function ajax_delete_client() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( 'Unauthorized' );

            $client_id = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
            if ( $client_id <= 0 ) wp_send_json_error( 'Invalid client ID.' );

            global $wpdb;

            // 1. Scrub all clinical and billing data from custom tables to prevent orphaned records
            $wpdb->delete( $wpdb->prefix . 'cree_consejeria_client_forms_master', array( 'wp_id' => $client_id ) );
            $wpdb->delete( $wpdb->prefix . 'cree_consejeria_session_notes', array( 'client_id' => $client_id ) );
            $wpdb->delete( $wpdb->prefix . 'cree_consejeria_appointments', array( 'client_id' => $client_id ) );
            $wpdb->delete( $wpdb->prefix . 'cree_consejeria_treatment_goals', array( 'client_id' => $client_id ) );
            $wpdb->delete( $wpdb->prefix . 'cree_consejeria_assigned_forms', array( 'client_id' => $client_id ) );
            $wpdb->delete( $wpdb->prefix . 'cree_consejeria_appointment_requests', array( 'client_id' => $client_id ) );
            $wpdb->delete( $wpdb->prefix . 'cree_consejeria_client_plans', array( 'client_id' => $client_id ) );
            $wpdb->delete( $wpdb->prefix . 'cree_consejeria_billing', array( 'client_id' => $client_id ) );
            
            // 2. Delete the actual WordPress User Account
            require_once( ABSPATH . 'wp-admin/includes/user.php' );
            if ( wp_delete_user( $client_id ) ) {
                wp_send_json_success( 'Client and all associated records have been permanently deleted.' );
            } else {
                wp_send_json_error( 'Failed to delete the WordPress user account.' );
            }
        }

        /**
         * AJAX: Toggle Client Status (Active/Inactive)
         */
        public function ajax_toggle_client_status() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( 'Unauthorized' );

            $client_id = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
            $new_status = sanitize_text_field( $_POST['status'] ); // 'active' or 'inactive'

            if ( $client_id > 0 ) {
                update_user_meta( $client_id, 'cree_client_status', $new_status );
                wp_send_json_success();
            } else {
                wp_send_json_error( 'Invalid client ID.' );
            }
        }

        /**
         * AJAX: Client Updates Their Own Password
         */
        public function ajax_update_client_password() {
            check_ajax_referer( 'cree_secure_form_submission', 'nonce' );
            if ( ! is_user_logged_in() ) wp_send_json_error( array( 'message' => 'Unauthorized' ) );

            $new_pass = isset( $_POST['new_password'] ) ? $_POST['new_password'] : '';
            $confirm_pass = isset( $_POST['confirm_password'] ) ? $_POST['confirm_password'] : '';

            if ( empty( $new_pass ) || $new_pass !== $confirm_pass ) {
                wp_send_json_error( array( 'message' => 'Passwords do not match.' ) );
            }
            if ( strlen( $new_pass ) < 8 ) {
                wp_send_json_error( array( 'message' => 'Password must be at least 8 characters.' ) );
            }

            $user_id = get_current_user_id();
            
            // Update the password in the database
            wp_set_password( $new_pass, $user_id );
            
            // wp_set_password() destroys the active session. We need to instantly log them back in.
            $user = get_user_by( 'id', $user_id );
            clean_user_cache( $user_id );
            wp_clear_auth_cookie();
            wp_set_current_user( $user_id, $user->user_login );
            wp_set_auth_cookie( $user_id );

            wp_send_json_success( array( 'message' => 'Password updated successfully!' ) );
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
            delete_option( 'cree_consejeria_cfm_version' );
            delete_option( 'cree_consejeria_encryption_key' ); // Optional: Removes the encryption key if you want a true hard reset
            
            global $wpdb;
            
            // 1. Delete generated pages
            $wpdb->query(
                "DELETE FROM $wpdb->posts WHERE post_name IN (
                    'registration-short-form', 'substance-abuse-form', 'psychosocial-evaluation-form', 
                    'aviso-de-practicas-de-privacidad', 'consentimiento-informado-para-psicoterapia', 
                    'politicas-de-la-practica', 'consentimiento-informado-para-terapia-en-linea', 
                    'consentimiento-para-divulgacion-de-informacion', 'client-portal', 'client-login', 'cree-therapist-portal'
                ) AND post_type = 'page'"
            );
            
            // 2. Drop all custom database tables
            $tables_to_drop = array(
                $wpdb->prefix . 'cree_consejeria_client_forms_master',
                $wpdb->prefix . 'cree_consejeria_session_notes',
                $wpdb->prefix . 'cree_consejeria_appointments',
                $wpdb->prefix . 'cree_consejeria_treatment_goals',
                $wpdb->prefix . 'cree_consejeria_assigned_forms',
                $wpdb->prefix . 'cree_consejeria_master_schedule',
                $wpdb->prefix . 'cree_consejeria_schedule_exceptions',
                $wpdb->prefix . 'cree_consejeria_appointment_requests',
                $wpdb->prefix . 'cree_consejeria_plan_templates',
                $wpdb->prefix . 'cree_consejeria_client_plans',
                $wpdb->prefix . 'cree_consejeria_secure_message',
                $wpdb->prefix . 'cree_consejeria_billing'
            );

            foreach ( $tables_to_drop as $table ) {
                $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
            }
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