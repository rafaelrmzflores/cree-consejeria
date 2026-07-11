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
/*
CREE Consejeria CFM is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
CREE Consejeria CFM is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with CREE Consejeria CFM. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !class_exists( 'CREE_Consejeria_CFM' )){

	class CREE_Consejeria_CFM{

		public function __construct(){

			$this->define_constants(); 
            			
		}

		public function define_constants(){
            // Path/URL to root of this plugin, with trailing slash.
			define ( 'CREE_CFM_PATH', plugin_dir_path( __FILE__ ) );
            define ( 'CREE_CFM_URL', plugin_dir_url( __FILE__ ) );
            define ( 'CREE_CFM_VERSION', '1.0.0' );
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
                    form_type varchar(100) NOT NULL,
                    client_name varchar(255) DEFAULT NULL,
                    dob date DEFAULT NULL,
                    email varchar(100) DEFAULT NULL,
                    form_data_encrypted blob DEFAULT NULL,
                    signature varchar(255) DEFAULT NULL,
                    status varchar(20) DEFAULT 'complete',
                    signature_date date DEFAULT NULL,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY  (id),
                    KEY wp_id (wp_id),
                    KEY form_type (form_type),
                    KEY client_name (client_name),
                    KEY dob (dob),
                    KEY status (status)
                    ) $charset_collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $query );
            }

            if ( empty( $cree_consejeria_cfm_db_version ) || version_compare( $cree_consejeria_cfm_db_version, '1.0.0', '<' ) ) {
                update_option( 'cree_consejeria_cfm_db_version', '1.0.0' );
            }

            // Define the pages to check and create
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

            // Get current user ID once outside the loop to improve efficiency
            $current_user_id = wp_get_current_user()->ID;

            // Loop through and programmatically generate pages
            foreach ( $pages_to_create as $page_data ) {
                
                // Check if the page already exists
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
                        'post_content' => '<!-- wp:shortcode -->[cree_consejeria_intake_form type="' . $page_data['form_type'] . '"] <!-- /wp:shortcode -->'
                    );
                    
                    $page_id = wp_insert_post( $page );  
                }
            }

        }
       
        /**
         * Deactivate the plugin
         */
        public static function deactivate(){
            flush_rewrite_rules();
            // unregister_post_type ('cree-client-intake');
        }        

        /**
         * Uninstall the plugin
         */
        public static function uninstall(){

            delete_option('cree_consejeria_cfm_db_version');
            global $wpdb;
            $wpdb->query(
                "DELETE FROM $wpdb->posts WHERE post_name IN ('registration-short-form', 'substance-abuse-form', 'psychosocial-evaluation-form', 
                'aviso-de-practicas-de-privacidad', 
                'consentimiento-informado-para-psicoterapia', 
                'politicas-de-la-practica', 'consentimiento_informado_para_terapia_en_linea', 'consentimiento-para-divulgacion-de-informacion') AND post_type = 'page'"
            );
            $wpdb->query(
                "DELETE FROM {$wpdb->prefix}cree_consejeria_client_forms_master"
            );
            $wpdb->query(
                "DROP TABLE IF EXISTS {$wpdb->prefix}cree_consejeria_client_forms_master"
            );

        }       

	}
}

// Plugin Instantiation
if (class_exists( 'CREE_Consejeria_CFM' )){

    // Installation and uninstallation hooks
    register_activation_hook( __FILE__, array( 'CREE_Consejeria_CFM', 'activate'));
    register_deactivation_hook( __FILE__, array( 'CREE_Consejeria_CFM', 'deactivate'));
    register_uninstall_hook( __FILE__, array( 'CREE_Consejeria_CFM', 'uninstall' ) );

    // Instatiate the plugin class
    $cree_consejeria_cfm = new CREE_Consejeria_CFM(); 
}