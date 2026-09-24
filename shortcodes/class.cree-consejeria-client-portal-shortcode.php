<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CREE_Consejeria_Client_Portal {

    public function __construct() {
        add_shortcode( 'cree_client_portal', array( $this, 'render_client_portal' ) );
    }

    public function render_client_portal() {
        // 1. Force Login Check
        if ( ! is_user_logged_in() ) {
            return '<div class="cree-form-container">
                <h2>Client Portal</h2>
                <p>Please <a href="' . esc_url( site_url( '/client-login/' ) ) . '">log in</a> to view your submitted forms.</p>
            </div>';
        }

        $current_user_id = get_current_user_id();
        global $wpdb;
        // Fix #1: Corrected table name (removed extra 'c')
        $forms_table =$wpdb->prefix . 'cree_consejeria_client_forms_master';

        // Fix #2: Changed `user_id` to `wp_id` to match database schema
        $submitted_forms = $wpdb->get_results($wpdb->prepare(
            "SELECT id, form_type, status, created_at 
             FROM {$forms_table} 
             WHERE wp_id = %d 
             ORDER BY created_at DESC",
            $current_user_id
        ) );

        // Friendly label mapping for form types
        $form_labels = array(
            'registration_short_form'                    => 'Client Registration Form',
            'psychosocial_evaluation'                    => 'Psychosocial Evaluation',
            'substance_abuse_intake'                     => 'Substance Abuse Intake',
            'aviso_de_practicas_de_privacidad'           => 'Notice of Privacy Practices',
            'consentimiento_informado_para_psicoterapia' => 'Informed Consent for Psychotherapy',
            'politicas_de_la_practica'                   => 'Practice Policies',
        );

        wp_enqueue_style( 'cree-consejeria-form-styles-css' );
        wp_enqueue_script( 'cree-consejeria-form-handler-js' );

        ob_start();
        ?>
        <div class="cree-form-container" id="cree-client-portal">
            <h2>Welcome, <?php echo esc_html( wp_get_current_user()->display_name ); ?></h2>
            <p>Below is the record of your submitted intake documents and forms.</p>

            <?php if ( empty( $submitted_forms ) ) : ?>
                <div style="background:#f0f6fc; border-left:4px solid #72aee6; padding:12px; margin-top:15px;">
                    <p style="margin:0;">No submitted forms found for your account.</p>
                </div>
            <?php else : ?>
                <table class="cree-portal-table" style="width:100%; border-collapse:collapse; margin-top:20px;">
                    <thead>
                        <tr style="background:#f6f7f7; text-align:left; border-bottom:2px solid #dcdcde;">
                            <th style="padding:10px;">Form Name</th>
                            <th style="padding:10px;">Date Submitted</th>
                            <th style="padding:10px;">Status</th>
                            <th style="padding:10px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $submitted_forms as$form ) : 
                            $label  = isset($form_labels[ $form->form_type ] ) ?$form_labels[ $form->form_type ] :$form->form_type;
                            $date   = wp_date( 'M j, Y', strtotime( $form->created_at ) );$status = ! empty( $form->status ) ? ucfirst( $form->status ) : 'Submitted';
                        ?>
                            <tr style="border-bottom:1px solid #f0f0f1;">
                                <td style="padding:10px; font-weight:bold;"><?php echo esc_html( $label ); ?></td>
                                <td style="padding:10px;"><?php echo esc_html( $date ); ?></td>
                                <td style="padding:10px;">
                                    <span style="background:#e7f5ea; color:#007017; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">
                                        <?php echo esc_html( $status ); ?>
                                    </span>
                                </td>
                                <td style="padding:10px;">
                                    <button class="cree-view-form-btn" data-form-id="<?php echo esc_attr( $form->id ); ?>" style="padding:4px 10px; font-size:12px; cursor:pointer;">
                                        View Content
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- Modal for displaying decrypted form details -->
            <div id="cree-form-detail-modal" style="display:none; margin-top:20px; padding:15px; background:#fff; border:1px solid #ccc; border-radius:4px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <h3 style="margin:0;">Form Content</h3>
                    <button id="cree-close-modal-btn" style="cursor:pointer;">&times; Close</button>
                </div>
                <div id="cree-form-detail-content">Loading...</div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
