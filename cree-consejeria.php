<?php

/**
* Plugin Name: CREE Clients and Forms Manager
* Plugin URI: https://www.wordpress.org/cree-consejeria
* Description: My plugin's description
* Version: 1.0
* Requires at least: 5.6
* Requires PHP: 7.0
* Author: Rafael Ramírez Flores
* Author URI: https://www.codigowp.net
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: cree-cfm
* Domain Path: /languages
*/
/*
CREE CONSEJERIA is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
CREE CONSEJERIA is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with CREE CONSEJERIA. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !class_exists( 'CREE_CFM' )){

	class CREE_CFM{

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

            $table_name = $wpdb->prefix . 'cree_clients_forms_master';

            $charset_collate = $wpdb->get_charset_collate();

            $cree_cfm_db_version = get_option( 'cree_cfm_db_version' );
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

            if ( empty( $cree_cfm_db_version ) || version_compare( $cree_cfm_db_version, '1.0.0', '<' ) ) {
                update_option( 'cree_cfm_db_version', '1.0.0' );
            }

            // 
            if ( $wpdb->get_row("SELECT post_name FROM $wpdb->posts WHERE post_name = 'registration-short-form' AND post_type = 'page'") === null ) {
                $current_user = wp_get_current_user();
                $page = array(
                    'post_title' => __('Registration Short Form', 'cree-cfm'),
                    'post_name' => 'registration-short-form',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[cree_consejeria_intake_form type="registration_short_form"]<!-- /wp:shortcode -->'
                );
                $page_id = wp_insert_post( $page );
                
            }

            if ( $wpdb->get_row("SELECT post_name FROM $wpdb->posts WHERE post_name = 'substance-abuse-form' AND post_type = 'page'") === null ) {
                $current_user = wp_get_current_user();
                $page = array(
                    'post_title' => __('Substance Abuse Form', 'cree-cfm'),
                    'post_name' => 'substance-abuse-form',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[cree_consejeria_intake_form type="substance_abuse_intake"]<!-- /wp:shortcode -->'
                );
                $page_id = wp_insert_post( $page );
                
            }

            if ( $wpdb->get_row("SELECT post_name FROM $wpdb->posts WHERE post_name = 'psychosocial-evaluation-form' AND post_type = 'page'") === null ) {
                $current_user = wp_get_current_user();
                $page = array(
                    'post_title' => __('Psychosocial Evaluation Form', 'cree-cfm'),
                    'post_name' => 'psychosocial-evaluation-form',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[cree_consejeria_intake_form type="psychosocial_evaluation"]<!-- /wp:shortcode -->'
                );
                $page_id = wp_insert_post( $page );
                
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

            delete_option('cree_cfm_db_version');
            global $wpdb;
            $wpdb->query(
                "DELETE FROM $wpdb->posts WHERE post_name IN ('registration-short-form', 'substance-abuse-form', 'psychosocial-evaluation-form') AND post_type = 'page'"
            );
            $wpdb->query(
                "DELETE FROM {$wpdb->prefix}cree_clients_forms_master"
            );
            $wpdb->query(
                "DROP TABLE IF EXISTS {$wpdb->prefix}cree_clients_forms_master"
            );

        }       

	}
}

// Plugin Instantiation
if (class_exists( 'CREE_CFM' )){

    // Installation and uninstallation hooks
    register_activation_hook( __FILE__, array( 'CREE_CFM', 'activate'));
    register_deactivation_hook( __FILE__, array( 'CREE_CFM', 'deactivate'));
    register_uninstall_hook( __FILE__, array( 'CREE_CFM', 'uninstall' ) );

    // Instatiate the plugin class
    $cree_cfm = new CREE_CFM(); 
}