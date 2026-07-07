<?php

/**
* Plugin Name: CREE Consejeria
* Plugin URI: https://www.wordpress.org/cree-consejeria
* Description: My plugin's description
* Version: 1.0
* Requires at least: 5.6
* Requires PHP: 7.0
* Author: Rafael Ramírez Flores
* Author URI: https://www.codigowp.net
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: cree-consejeria
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

if( !class_exists( 'CREE_Consejeria' )){

	class CREE_Consejeria{

		public function __construct(){

			$this->define_constants(); 

            require_once CREE_CONSEJERIA_PATH . 'functions/cree_consejeria_functions.php';
            
            require_once CREE_CONSEJERIA_PATH . 'post-types/class.cree-consejeria-cpt.php';
            $Cree_Consejeria_Post_Type = new CREE_Consejeria_Post_Type();

            require_once CREE_CONSEJERIA_PATH . 'shortcodes/class.cree-consejeria-shortcode.php';
            $Cree_Consejeria_Shortcode = new CREE_Consejeria_Shortcode();

            add_action('admin_enqueue_scripts', [$this, 'register_admin_styles'], 999);

            add_action('wp_enqueue_scripts', [$this, 'register_wp_scripts'], 999);
            			
		}

		public function define_constants(){
            // Path/URL to root of this plugin, with trailing slash.
			define ( 'CREE_CONSEJERIA_PATH', plugin_dir_path( __FILE__ ) );
            define ( 'CREE_CONSEJERIA_URL', plugin_dir_url( __FILE__ ) );
            define ( 'CREE_CONSEJERIA_VERSION', '1.0.0' );
		}

        /**
         * Activate the plugin
         */
        public static function activate(){
            update_option('rewrite_rules', '' );

            global $wpdb;

            $table_name = $wpdb->prefix . 'cree_consejeria_universal_intake';

            $charset_collate = $wpdb->get_charset_collate();

            $cree_consejeria_db_version = get_option( 'cree_consejeria_db_version' );

            if(empty($cree_consejeria_db_version)) {

                $query = "CREATE TABLE $table_name (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    wp_id bigint(20) unsigned NOT NULL,
                    form_type varchar(100) NOT NULL,
                    client_name varchar(255) NOT NULL,
                    dob date NOT NULL,
                    email varchar(100) NOT NULL,
                    form_data_encrypted blob DEFAULT NULL,
                    signature varchar(255) NOT NULL,
                    signature_date date NOT NULL,
                    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY  (id),
                    KEY wp_id (wp_id),
                    KEY form_type (form_type),
                    KEY client_name (client_name),
                    KEY dob (dob)
                    ) $charset_collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $query );
                $cree_consejeria_db_version = '1.0.0';
                add_option( 'cree_consejeria_db_version', $cree_consejeria_db_version );
            }

            if ( $wpdb->get_row("SELECT post_name FROM $wpdb->posts WHERE post_name = 'universal-intake-form' AND post_type = 'page'") === null ) {
                $current_user = wp_get_current_user();
                $page = array(
                    'post_title' => __('Universal Intake Form', 'cree-consejeria'),
                    'post_name' => 'universal-intake-form',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[cree_consejeria_universal_intake_form]<!-- /wp:shortcode -->'
                );
                $page_id = wp_insert_post( $page );
                
            }

        }

        public function register_admin_styles($hook)
        {

            // if ($hook == 'toplevel_page_online-bible-school-gms' || $hook == 'toplevel_page_online_bible_school_gms_main_section') {

                $cree_consejeria_admin_css_path = CREE_CONSEJERIA_PATH . 'assets/css/admin.css';
                $cree_consejeria_admin_css_url  = CREE_CONSEJERIA_URL . 'assets/css/admin.css';
                $cree_consejeria_admin_css_version = file_exists($cree_consejeria_admin_css_path) ? filemtime($cree_consejeria_admin_css_path) : null;

                wp_enqueue_style(
                    'cree-consejeria-admin-css',
                    $cree_consejeria_admin_css_url,
                    [],
                    $cree_consejeria_admin_css_version
                );
            // }

        }

        public function register_wp_scripts(){

            $cree_consejeria_frontend_css_path = CREE_CONSEJERIA_PATH . 'assets/css/frontend.css';
            $cree_consejeria_frontend_css_url  = CREE_CONSEJERIA_URL . 'assets/css/frontend.css';
            $cree_consejeria_frontend_css_version = file_exists($cree_consejeria_frontend_css_path) ? filemtime($cree_consejeria_frontend_css_path) : null;

            wp_enqueue_style(
                'cree-consejeria-frontend-css',
                $cree_consejeria_frontend_css_url,
                [],
                $cree_consejeria_frontend_css_version
            );

            $cree_consejeria_frontend_js_path = CREE_CONSEJERIA_PATH . 'assets/js/client-registration-form.js';
            $cree_consejeria_frontend_js_url  = CREE_CONSEJERIA_URL . 'assets/js/client-registration-form.js';
            $cree_consejeria_frontend_js_version = file_exists($cree_consejeria_frontend_js_path) ? filemtime($cree_consejeria_frontend_js_path) : null;

            wp_enqueue_script(
                'cree-consejeria-frontend-js',
                $cree_consejeria_frontend_js_url,
                ['jquery'],
                $cree_consejeria_frontend_js_version,
                true
            );

        }

        /**
         * Deactivate the plugin
         */
        public static function deactivate(){
            flush_rewrite_rules();
            unregister_post_type ('cree-client-intake');
        }        

        /**
         * Uninstall the plugin
         */
        public static function uninstall(){

            delete_option('cree_consejeria_db_version');
            global $wpdb;
            $wpdb->query(
                "DELETE FROM $wpdb->posts WHERE post_name = 'universal-intake-form' AND post_type = 'page'"
            );
            $wpdb->query(
                "DELETE FROM {$wpdb->prefix}cree_consejeria_universal_intake"
            );
            $wpdb->query(
                "DROP TABLE IF EXISTS {$wpdb->prefix}cree_consejeria_universal_intake"
            );

        }       

	}
}

// Plugin Instantiation
if (class_exists( 'CREE_Consejeria' )){

    // Installation and uninstallation hooks
    register_activation_hook( __FILE__, array( 'CREE_Consejeria', 'activate'));
    register_deactivation_hook( __FILE__, array( 'CREE_Consejeria', 'deactivate'));
    register_uninstall_hook( __FILE__, array( 'CREE_Consejeria', 'uninstall' ) );

    // Instatiate the plugin class
    $cree_consejeria = new CREE_Consejeria(); 
}