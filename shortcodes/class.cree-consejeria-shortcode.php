<?php

if ( ! class_exists( 'CREE_Consejeria_Shortcode' ) ) {

    class CREE_Consejeria_Shortcode
    {
        public function __construct() 
        {
            add_shortcode( 'cree_consejeria_intake_form', array( $this, 'render_form_shortcode' ) );
        }

        public function render_form_shortcode( $atts ) 
        {
            // 1. Safely handle shortcode attributes and set a default 'type'
            $attributes = shortcode_atts(
                array(
                    'type' => 'registration_short_form',
                ), 
                $atts
            );

            // Sanitize the type attribute
            $form_type = sanitize_file_name( $attributes['type'] );

            // Maps the 'type' attribute to a specific file in the /views/ folder
            $view_map = [
                'registration_short_form'                        => 'client-registration-short-form_shortcode.php',
                'psychosocial_evaluation'                        => 'psychosocial-evaluation_shortcode.php',
                'substance_abuse_intake'                         => 'substance-abuse-intake_shortcode.php',
                'aviso_de_practicas_de_privacidad'               => 'aviso-de-practicas-de-privacidad_shortcode.php',
                'consentimiento_informado_para_psicoterapia'     => 'consentimiento-informado-para-psicoterapia_shortcode.php',
                'politicas_de_la_practica'                       => 'politicas-de-la-practica_shortcode.php',
                'consentimiento_informado_para_terapia_en_linea' => 'consentimiento-informado-para-terapia-en-linea_shortcode.php',
                'consentimiento_para_divulgacion_de_informacion' => 'consentimiento-para-divulgacion-de-informacion_shortcode.php'
            ];

            wp_enqueue_style( 'cree-consejeria-form-styles-css' );

            wp_enqueue_script( 'cree-consejeria-form-handler-js' );

            // Fallback to default if the provided type doesn't exist in our map
            $filename = isset( $view_map[$form_type] ) ? $view_map[$form_type] : $view_map['registration_short_form'];
            
            $view_path = CREE_CONSEJERIA_CFM_PATH . 'views/' . $filename;

            // 2. Capture the included file template using output buffering
            if ( file_exists( $view_path ) ) {
                ob_start();
                include $view_path;
                return ob_get_clean(); // Returns the HTML content to WordPress to place correctly
            }

            return ''; // Return nothing if the file somehow wasn't found
        }

    }

}