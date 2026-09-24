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

            require_once CREE_CONSEJERIA_CFM_PATH . 'shortcodes/class.cree-consejeria-form-shortcode.php';
            $CREE_Consejeria_Form_Shortcode = new CREE_Consejeria_Form_Shortcode();

            require_once CREE_CONSEJERIA_CFM_PATH . 'shortcodes/class.cree-consejeria-note-shortcode.php';
            $Cree_Consejeria_Note_Shortcode = new CREE_Consejeria_Note_Shortcode();

            require_once CREE_CONSEJERIA_CFM_PATH . 'shortcodes/class.cree-consejeria-client-portal-shortcode.php';
            $Cree_Consejeria_Client_Portal = new CREE_Consejeria_Client_Portal();

            require_once CREE_CONSEJERIA_CFM_PATH . 'shortcodes/class.cree-client-login-shortcode.php';
            $Cree_Consejeria_Client_Login = new CREE_Client_Login_Shortcode();

            require_once CREE_CONSEJERIA_CFM_PATH . 'includes/class-cree-security-redirects.php';

            add_action('wp_enqueue_scripts', [$this, 'register_wp_styles'], 999);

            add_action('wp_ajax_cree_submit_intake_form', [$this, 'cree_ajax_intake_form_handler']);
            add_action('wp_ajax_nopriv_cree_submit_intake_form', [$this, 'cree_ajax_intake_form_handler']);

            // add_action('wp_ajax_cree_save_session_note', [$this,'cree_save_session_note' ]);
            // add_action( 'wp_ajax_cree_get_client_notes', [$this, 'cree_ajax_get_client_notes' ]);
            
            add_action( 'wp_ajax_cree_add_new_client', [$this,'cree_add_new_client' ]);
            add_action( 'wp_ajax_cree_load_client_notes', array( $this, 'ajax_load_client_notes' ) );
            add_action( 'wp_ajax_cree_save_session_note', array( $this, 'ajax_save_session_note' ) );

            add_action( 'wp_ajax_cree_get_client_form_content', array( $this,'cree_ajax_get_client_form_content' ) );
                        
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

            $forms_table_name = $wpdb->prefix . 'cree_consejeria_client_forms_master';
            $notes_table_name = $wpdb->prefix . 'cree_consejeria_session_notes';
            $charset_collate = $wpdb->get_charset_collate();

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
                    client_id bigint(20) unsigned NOT NULL, /* The WP user_id of the patient */
                    therapist_id bigint(20) unsigned NOT NULL, /* The WP user_id of the counselor */
                    session_date date NOT NULL,
                    note_encrypted blob NOT NULL, /* AES_ENCRYPT storage */
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY  (id),
                    KEY client_id (client_id),
                    KEY therapist_id (therapist_id),
                    KEY session_date (session_date)
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
                )
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

            $therapist_portal_page_exists = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND post_type = 'page'",
                    'cree-therapist-portal'
                )
            );

            $therapist_portal_page_data = array(
                array(
                    'slug'       => 'cree-therapist-portal',
                    'title'      => __('CREE Portal del Terapeuta', 'cree-consejeria-cfm')
                ),
            );

            if ( $therapist_portal_page_exists === null ) {
                $therapist_portal_page = array(
                    'post_title'   => $therapist_portal_page_data['title'],
                    'post_name'    => $therapist_portal_page_data['slug'],
                    'post_status'  => 'publish',
                    'post_author'  => $current_user_id,
                    'post_type'    => 'page',
                    'post_content' => '[cree_therapist_portal]'
                );
                
                wp_insert_post( $therapist_portal_page );  
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

            $cree_consejeria_note_handler_js_path = CREE_CONSEJERIA_CFM_PATH . 'js/note-handler.js';
            $cree_consejeria_note_handler_js_url  = CREE_CONSEJERIA_CFM_URL . 'js/note-handler.js';
            $cree_consejeria_note_handler_js_version = file_exists( $cree_consejeria_note_handler_js_path ) ? filemtime( $cree_consejeria_note_handler_js_path ) : false;

            wp_register_script(
                'cree-consejeria-note-handler-js', 
                $cree_consejeria_note_handler_js_url,
                array( 'jquery' ),
                $cree_consejeria_note_handler_js_version,
                true
            );

            // FIX: Changed 'cree_secure_form_submission' to 'cree_secure_note_nonce' to match PHP verification
            wp_localize_script( 
                'cree-consejeria-note-handler-js',
                'CREE_NOTE_FORM',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce'    => wp_create_nonce( 'cree_secure_note_nonce' )
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

                wp_send_json_success( array( 'message' => $success_message ) );
            }

            wp_die();
        }

        public function cree_save_session_note() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can('edit_posts') ) {
                wp_send_json_error('Unauthorized access.');
            }

            global $wpdb;
            $notes_table_name = $wpdb->prefix . 'cree_consejeria_session_notes';
            $encryption_key = SECURE_AUTH_KEY;

            $client_id    = intval($_POST['client_id']);
            $therapist_id = get_current_user_id();
            $session_date = sanitize_text_field($_POST['session_date']);
            $note_text    = sanitize_textarea_field($_POST['note_text']);

            $inserted = $wpdb->query( $wpdb->prepare(
                "INSERT INTO $notes_table_name (client_id, therapist_id, session_date, note_encrypted) 
                VALUES (%d, %d, %s, AES_ENCRYPT(%s, %s))",
                $client_id,
                $therapist_id,
                $session_date,
                $note_text,
                $encryption_key
            ));

            if ( $inserted ) {
                wp_send_json_success('Session note securely saved.');
            } else {
                wp_send_json_error('Database error.');
            }
        }
        
        public function cree_ajax_get_client_notes() {
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );
            if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( 'Unauthorized' );

            global $wpdb;
            $notes_table_name = $wpdb->prefix . 'cree_consejeria_session_notes';
            $encryption_key = SECURE_AUTH_KEY;
            $client_id = intval( $_POST['client_id'] );

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
                wp_send_json_success( '<p>No previous notes found for this client.</p>' );
            }

            $html = '';
            foreach ( $notes as $note ) {
                $date = wp_date( 'F j, Y', strtotime( $note->session_date ) );
                $html .= '<div style="background:#fff; padding:10px; border-left:4px solid #0073aa; margin-bottom:10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">';
                $html .= '<strong>' . esc_html( $date ) . '</strong>';
                $html .= '<p style="margin:5px 0 0 0; font-size:14px;">' . nl2br( esc_html( $note->decrypted_note ) ) . '</p>';
                $html .= '</div>';
            }

            wp_send_json_success( $html );
        }

        /**
         * AJAX Handler: Create a New Client User
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

            wp_send_new_user_notifications( $user_id, 'user' );

            wp_send_json_success( array(
                'user_id'      => $user_id,
                'display_name' => $display_name,
                'email'        => $email,
                'message'      => 'Client created successfully!'
            ) );
        }
       
        public function ajax_load_client_notes() {
            // 1. Validate Nonce & Capabilities
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );

            if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( 'Unauthorized permission level.' );
            }

            $client_id = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;

            if ( ! $client_id ) {
                wp_send_json_error( 'Invalid Client ID.' );
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_session_notes'; // Adjust if your table name differs

            // Check if table exists before querying
            if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
                // Fallback: If using wp_posts CPT for notes instead of custom DB table:
                $notes = get_posts( array(
                    'post_type'      => 'cree_session_note',
                    'meta_key'       => '_cree_client_id',
                    'meta_value'     => $client_id,
                    'posts_per_page' => -1,
                    'orderby'        => 'date',
                    'order'          => 'DESC'
                ) );

                if ( empty( $notes ) ) {
                    wp_send_json_success( array( 'html' => '<p><em>No session notes recorded for this client yet.</em></p>' ) );
                }

                ob_start();
                foreach ( $notes as $note ) {
                    $session_date = get_post_meta( $note->ID, '_cree_session_date', true );
                    $raw_content  = $note->post_content;
                    
                    // If content is encrypted, decrypt here (e.g., $raw_content = cree_decrypt_data($raw_content);)
                    ?>
                    <div class="cree-note-card" style="background: #ffffff; border: 1px solid #dcdcde; border-left: 4px solid #2271b1; padding: 12px; margin-bottom: 12px; border-radius: 4px;">
                        <div style="font-size: 12px; color: #646970; margin-bottom: 6px; font-weight: 600;">
                            📅 <?php echo esc_html( $session_date ? $session_date : get_the_date( 'Y-m-d', $note ) ); ?>
                        </div>
                        <div style="font-size: 14px; color: #1d2327; white-space: pre-wrap;"><?php echo esc_html( $raw_content ); ?></div>
                    </div>
                    <?php
                }
                $html = ob_get_clean();
                wp_send_json_success( array( 'html' => $html ) );
            }

            // Custom SQL query if using custom DB table
            $results = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE client_id = %d ORDER BY session_date DESC",
                $client_id
            ) );

            if ( empty( $results ) ) {
                wp_send_json_success( array( 'html' => '<p><em>No session notes recorded for this client yet.</em></p>' ) );
            }

            ob_start();
            foreach ( $results as $row ) {
                // Decrypt note text if stored encrypted
                $note_text = ! empty( $row->encrypted_note ) ? $row->encrypted_note : $row->note_text;
                
                // Example decryption check:
                if ( function_exists( 'cree_decrypt_string' ) ) {
                    $note_text = cree_decrypt_string( $note_text );
                }
                ?>
                <div class="cree-note-card" style="background: #ffffff; border: 1px solid #dcdcde; border-left: 4px solid #2271b1; padding: 12px; margin-bottom: 12px; border-radius: 4px;">
                    <div style="font-size: 12px; color: #646970; margin-bottom: 6px; font-weight: 600;">
                        📅 <?php echo esc_html( $row->session_date ); ?>
                    </div>
                    <div style="font-size: 14px; color: #1d2327; white-space: pre-wrap;"><?php echo esc_html( $note_text ); ?></div>
                </div>
                <?php
            }
            $html = ob_get_clean();

            wp_send_json_success( array( 'html' => $html ) );
        }

        public function ajax_save_session_note() {
            // 1. Validate Nonce & Permissions
            check_ajax_referer( 'cree_secure_note_nonce', 'security' );

            if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error( 'Unauthorized permission level.' );
            }

            // 2. Sanitize Inputs
            $client_id    = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
            $session_date = isset( $_POST['session_date'] ) ? sanitize_text_field( $_POST['session_date'] ) : '';
            $note_text    = isset( $_POST['note_text'] ) ? sanitize_textarea_field( $_POST['note_text'] ) : '';
            $therapist_id = get_current_user_id();

            if ( ! $client_id || empty( $session_date ) || empty( $note_text ) ) {
                wp_send_json_error( 'Please fill in all required fields.' );
            }

            // 3. Optional Encryption (If encryption function exists)
            if ( function_exists( 'cree_encrypt_string' ) ) {
                $stored_note = cree_encrypt_string( $note_text );
            } else {
                $stored_note = $note_text;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_session_notes'; // Adjust to your table name if different

            // OPTION A: Custom Database Table Insertion
            if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name ) {
                
                $inserted = $wpdb->insert(
                    $table_name,
                    array(
                        'client_id'      => $client_id,
                        'therapist_id'   => $therapist_id,
                        'session_date'   => $session_date,
                        'encrypted_note' => $stored_note,
                        'created_at'     => current_time( 'mysql' )
                    ),
                    array( '%d', '%d', '%s', '%s', '%s' )
                );

                if ( $inserted === false ) {
                    wp_send_json_error( 'Database error: ' . $wpdb->last_error );
                }

                wp_send_json_success( array( 'message' => '✅ Session note saved successfully!' ) );
            }

            // OPTION B: Fallback Custom Post Type Insertion (if no custom table exists)
            $post_id = wp_insert_post( array(
                'post_type'    => 'cree_session_note',
                'post_title'   => 'Session Note - Client #' . $client_id . ' - ' . $session_date,
                'post_content' => $stored_note,
                'post_status'  => 'publish',
                'post_author'  => $therapist_id,
            ) );

            if ( is_wp_error( $post_id ) ) {
                wp_send_json_error( $post_id->get_error_message() );
            }

            // Save Meta Attributes
            update_post_meta( $post_id, '_cree_client_id', $client_id );
            update_post_meta( $post_id, '_cree_session_date', $session_date );

            wp_send_json_success( array( 'message' => '✅ Session note saved successfully!' ) );
        }

        /**
         * AJAX Handler: Decrypt form content for the logged-in client owner
         */
        function cree_ajax_get_client_form_content() {
            // 1. Verify Nonce & Login
            check_ajax_referer( 'cree_secure_form_submission', 'security' );

            if ( ! is_user_logged_in() ) {
                wp_send_json_error( 'You must be logged in to view form details.' );
            }

            $current_user_id = get_current_user_id();
            $form_id         = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;

            global $wpdb;
            $forms_table    = $wpdb->prefix . 'cree_consejeria_client_forms_master';
            $encryption_key = SECURE_AUTH_KEY;

            // 2. Strict Ownership Lock: user_id MUST match logged-in user
            $query = $wpdb->prepare(
                "SELECT CAST(AES_DECRYPT(form_data_encrypted, %s) AS CHAR) AS decrypted_data, created_at 
                FROM {$forms_table} 
                WHERE id = %d AND user_id = %d",
                $encryption_key,
                $form_id,
                $current_user_id
            );

            $record = $wpdb->get_row( $query );

            if ( ! $record || empty( $record->decrypted_data ) ) {
                wp_send_json_error( 'Form record not found or access denied.' );
            }

            // 3. Unserialize / Decode JSON payload
            $form_fields = json_decode( $record->decrypted_data, true );

            if ( ! is_array( $form_fields ) ) {
                // Fallback if data was stored via serialize()
                $form_fields = maybe_unserialize( $record->decrypted_data );
            }

            // 4. Build HTML View of submitted fields
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
         * Deactivate the plugin
         */
        public static function deactivate(){
            flush_rewrite_rules();
        }        

        /**
         * Uninstall the plugin
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
            $wpdb->query(
                "DROP TABLE IF EXISTS {$wpdb->prefix}cree_consejeria_session_notes"
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