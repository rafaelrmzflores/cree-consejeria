<?php
if ( ! class_exists( 'CREE_Consejeria_Post_Type' ) ) {

    class CREE_Consejeria_Post_Type{

        public function __construct(){

            add_action('init', array($this, 'create_client_intake_post_type'));
            add_action('init', array($this, 'create_client_intake_taxonomy'));
            add_action('init', array($this, 'register_client_forms_master_table'));
            add_action('add_meta_boxes', array($this, 'add_cliet_forms_metabox'));

            // Hook to delete custom database row when post is trashed and permanently deleted
            add_action('before_delete_post', array($this, 'delete_secure_table_record'));

        }

        public function create_client_intake_post_type() {
            $labels = array(
                'name'               => esc_html__( 'Client Intakes', 'cree-consejeria' ),
                'singular_name'      => esc_html__( 'Client Intake', 'cree-consejeria' ),
                'menu_name'          => esc_html__( 'Intake Forms', 'cree-consejeria' ),
                'all_items'          => esc_html__( 'All Submissions', 'cree-consejeria' ),
                'view_item'          => esc_html__( 'View Submission', 'cree-consejeria' ),
                'search_items'       => esc_html__( 'Search Submissions', 'cree-consejeria' ),
                'not_found'          => esc_html__( 'No submissions found', 'cree-consejeria' ),
            );

            $args = array(
                'label'               => esc_html__( 'Client Intakes', 'cree-consejeria' ),
                'description'         => esc_html__( 'Secure Client Intake Form Submissions', 'cree-consejeria' ),
                'labels'              => $labels,
                
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
            );

            register_post_type( 'cree-client-intake', $args );
        }

        public function create_client_intake_taxonomy() {
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

        public function register_client_forms_master_table(){

            global $wpdb;
            $wpdb->universal_intake = $wpdb->prefix . 'cree_consejeria_client_forms_master';

        }

        public function add_cliet_forms_metabox() {
            add_meta_box(
                'cree_intake_secure_data',
                __( 'Secure Intake Form Data (Encrypted Decrypted on Display)', 'cree-consejeria' ),
                array( $this, 'render_intake_data_metabox' ),
                'cree-client-intake', 
                'normal',             
                'high'                
            );
        }

        /**
         * Automatically deletes the encrypted custom table row when the CPT is permanently deleted
         */
        public function delete_secure_table_record($post_id) {
            // Only execute if the post being deleted is our specific intake form
            if (get_post_type($post_id) !== 'cree-client-intake') {
                return;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'cree_consejeria_client_forms_master';
            
            // Grab the exact database row ID we linked to this post during submission
            $custom_table_id = get_post_meta($post_id, '_cree_custom_table_record_id', true);

            if ($custom_table_id) {
                // Delete the exact linked row
                $wpdb->delete(
                    $table_name,
                    array('id' => $custom_table_id),
                    array('%d')
                );
            } else {
                // Fallback for older legacy forms that didn't have the ID linked
                $post = get_post($post_id);
                $client_name = explode(' - ', $post->post_title)[0];
                
                // Safely delete only the most recent form matching this client to avoid wiping all their forms
                $wpdb->query( $wpdb->prepare(
                    "DELETE FROM {$table_name} WHERE client_name = %s ORDER BY id DESC LIMIT 1",
                    trim($client_name)
                ) );
            }
        }

        public function render_intake_data_metabox($post) {
            require_once (CREE_CONSEJERIA_CFM_PATH . 'views/client-forms_meta-box.php');
        }

    }

}