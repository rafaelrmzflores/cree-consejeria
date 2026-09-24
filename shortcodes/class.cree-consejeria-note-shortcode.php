<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'CREE_Consejeria_Note_Shortcode' ) ) {

    class CREE_Consejeria_Note_Shortcode 
    {
        public function __construct() 
        {
            add_shortcode( 'cree_therapist_portal', array( $this, 'render_portal_shortcode' ) );
        }

        public function render_portal_shortcode() 
        {
            // 1. Security check: Only allow users with edit permissions (Therapists / Admins)
            if ( ! current_user_can( 'edit_posts' ) ) {
                return '<div class="cree-form-container"><h2>Access Denied</h2><p>You do not have permission to view this portal.</p></div>';
            }

            // 2. Enqueue assets specific strictly to the therapist portal
            wp_enqueue_style( 'cree-consejeria-form-styles-css' );
            wp_enqueue_script( 'cree-consejeria-note-handler-js' );

            // 3. Fetch client list for the dropdown template
            $clients = get_users( array( 
                'role__in' => array( 'subscriber', 'customer' ),
                'orderby'  => 'display_name',
                'order'    => 'ASC'
            ) );

            // 4. Set template view path
            $view_path = CREE_CONSEJERIA_CFM_PATH . 'views/therapist-portal_shortcode.php';

            // 5. Render view using output buffering
            if ( file_exists( $view_path ) ) {
                ob_start();
                include $view_path;
                return ob_get_clean();
            }

            return '<div class="cree-form-container"><p>Error: Therapist Portal view template not found.</p></div>';
        }
    }

}