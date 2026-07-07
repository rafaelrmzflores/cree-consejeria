<?php

if ( ! class_exists( 'CREE_Consejeria_Post_Type' ) ) {
    class CREE_Consejeria_Post_Type{

        public function __construct(){
            // FIX: Bind the database property immediately on class startup so it's always accessible to AJAX threads
            $this->register_cree_consejeria_universal_intake_table();

            add_action('init', array($this, 'create_intake_post_type'));
            add_action('init', array($this, 'create_intake_taxonomy'));
            add_action('add_meta_boxes', array($this, 'add_intake_data_metabox'));
        }

        public function create_intake_post_type() {
            register_post_type(
                'cree-client-intake',
                array(
                    'label'        => esc_html__( 'Client Intakes', 'cree-consejeria' ),
                    'description'  => esc_html__( 'Secure Client Intake Form Submissions', 'cree-consejeria' ),
                    'labels'       => array(
                        'name'               => esc_html__( 'Client Intakes', 'cree-consejeria' ),
                        'singular_name'      => esc_html__( 'Client Intake', 'cree-consejeria' ),
                        'menu_name'          => esc_html__( 'Intake Forms', 'cree-consejeria' ),
                        'all_items'          => esc_html__( 'All Submissions', 'cree-consejeria' ),
                        'view_item'          => esc_html__( 'View Submission', 'cree-consejeria' ),
                        'search_items'       => esc_html__( 'Search Submissions', 'cree-consejeria' ),
                        'not_found'          => esc_html__( 'No submissions found', 'cree-consejeria' ),
                    ),
                    
                    // SECURITY: Hide completely from the public facing website
                    'public'              => false,
                    'publicly_queryable'  => false,
                    'exclude_from_search' => true,
                    'has_archive'         => false,
                    'rewrite'             => false, 
                    
                    // BACKEND UI: Keep it visible and functional for admins
                    'show_ui'             => true,
                    'show_in_menu'        => true,
                    'menu_position'       => 25, 
                    'menu_icon'           => 'dashicons-forms',
                    'show_in_admin_bar'   => false, 
                    'show_in_nav_menus'   => false,
                    'can_export'          => true,
                    'hierarchical'        => false,
                    
                    'supports'            => array( 'title', 'author' ),
                    'show_in_rest'        => true, 
                    'capability_type'     => 'post', 
                )
            );
        }

        public function create_intake_taxonomy() {
            $labels = array(
                'name'              => _x( 'Form Types', 'taxonomy general name', 'cree-consejeria' ),
                'singular_name'     => _x( 'Form Type', 'taxonomy singular name', 'cree-consejeria' ),
                'search_items'      => __( 'Search Form Types', 'cree-consejeria' ),
                'all_items'         => __( 'All Form Types', 'cree-consejeria' ),
                'edit_item'         => __( 'Edit Form Type', 'cree-consejeria' ),
                'update_item'       => __( 'Update Form Type', 'cree-consejeria' ),
                'add_new_item'      => __( 'Add New Form Type', 'cree-consejeria' ),
                'new_item_name'     => __( 'New Form Type Name', 'cree-consejeria' ),
                'menu_name'         => __( 'Form Types', 'cree-consejeria' ),
            );

            $args = array(
                'hierarchical'      => true, 
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true, 
                'query_var'         => true,
                'rewrite'           => false, 
                'public'            => false, 
                'publicly_queryable'=> false, 
            );

            register_taxonomy( 'intake_form_type', array( 'cree-client-intake' ), $args );
        }

        public function register_cree_consejeria_universal_intake_table() {
            global $wpdb;
            $wpdb->universal_intake = $wpdb->prefix . 'cree_consejeria_universal_intake';
        }

        public function add_intake_data_metabox() {
            add_meta_box(
                'cree_intake_secure_data',
                __( 'Secure Intake Form Data (Encrypted Decrypted on Display)', 'cree-consejeria' ),
                array( $this, 'render_intake_data_metabox' ),
                'cree-client-intake', 
                'normal',             
                'high'                
            );
        }

        public function render_intake_data_metabox($post) {
            require_once (CREE_CONSEJERIA_PATH . 'views/universal-intake_meta-box.php');
        }
    }
}