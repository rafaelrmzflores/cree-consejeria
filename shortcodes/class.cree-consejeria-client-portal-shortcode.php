<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CREE_Consejeria_Client_Portal {

    public function __construct() {
        add_shortcode( 'cree_client_portal', array( $this, 'render_client_portal' ) );
    }

    public function render_client_portal() {
        if ( ! is_user_logged_in() ) {
            return '<div class="cree-form-container" style="padding:20px; text-align:center;">
                <h2>Client Portal</h2>
                <p>Por favor, <a href="' . esc_url( site_url( '/client-login/' ) ) . '">inicie sesión</a> para acceder a su portal de cliente y los documentos de evaluación asignados.</p>
            </div>';
        }

        $current_user    = wp_get_current_user();
        $current_user_id = $current_user->ID;

        global $wpdb;
        $assigned_table  = $wpdb->prefix . 'cree_consejeria_assigned_forms';
        $completed_table = $wpdb->prefix . 'cree_consejeria_client_forms_master';

        // 1. Fetch Assigned Forms for this client
        $assigned_forms = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$assigned_table} WHERE client_id = %d ORDER BY created_at DESC",
            $current_user_id
        ) );

        // 2. Fetch Completed Forms
        $completed_forms = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, form_type, created_at, status FROM {$completed_table} WHERE wp_id = %d ORDER BY created_at DESC",
            $current_user_id
        ) );

        // Create completed map
        $completed_map = array();
        foreach ( $completed_forms as $cf ) {
            $completed_map[ $cf->form_type ] = $cf;
        }

        // Friendly label mapping
        $form_labels = array(
            'registration_short_form'                        => 'Client Registration Form',
            'psychosocial_evaluation'                        => 'Psychosocial Evaluation',
            'substance_abuse_intake'                         => 'Substance Abuse Intake',
            'aviso_de_practicas_de_privacidad'               => 'Aviso de Prácticas de Privacidad',
            'consentimiento_informado_para_psicoterapia'     => 'Consentimiento Informado para Psicoterapia',
            'consentimiento_informado_para_terapia_en_linea' => 'Consentimiento Informado para Terapia en Línea',
            'consentimiento_para_divulgacion_de_informacion' => 'Consentimiento para Divulgación de Información',
            'politicas_de_la_practica'                       => 'Políticas de la Práctica',
        );

        wp_enqueue_style( 'cree-consejeria-form-styles-css' );
        wp_enqueue_script( 'cree-consejeria-form-handler-js' );

        ob_start();
        ?>
        <div class="cree-form-container" id="cree-client-portal-wrapper" style="max-width:900px; margin:0 auto; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
            
            <!-- LIST VIEW CONTAINER -->
            <div id="cree-client-form-list-view">
                <div style="background:#1e3a8a; color:#fff; padding:20px; border-radius:8px 8px 0 0;">
                    <h2 style="margin:0; font-size:22px; color:#fff;">Welcome, <?php echo esc_html( $current_user->display_name ); ?></h2>
                    <p style="margin:5px 0 0 0; font-size:14px; opacity:0.9;">Please review and complete your pending intake forms below.</p>
                </div>

                <div style="background:#fff; border:1px solid #e5e7eb; border-top:none; padding:20px; border-radius:0 0 8px 8px;">
                    <h3 style="margin-top:0; color:#1f2937;">Assigned Intake & Consent Documents</h3>

                    <?php if ( empty( $assigned_forms ) && empty( $completed_forms ) ) : ?>
                        <div style="background:#f0f6fc; border-left:4px solid #72aee6; padding:15px; margin-top:15px; border-radius:4px;">
                            <p style="margin:0; color:#1e3a8a;">You currently have no pending or completed forms assigned to your account.</p>
                        </div>
                    <?php else : ?>
                        <table class="cree-portal-table" style="width:100%; border-collapse:collapse; margin-top:15px; font-size:14px;">
                            <thead>
                                <tr style="background:#f9fafb; text-align:left; border-bottom:2px solid #e5e7eb;">
                                    <th style="padding:12px;">Form Document</th>
                                    <th style="padding:12px;">Assigned Date</th>
                                    <th style="padding:12px;">Status</th>
                                    <th style="padding:12px; text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $assigned_forms as $af ) : 
                                    $label = isset( $form_labels[ $af->form_type ] ) ? $form_labels[ $af->form_type ] : ucwords( str_replace( '_', ' ', $af->form_type ) );
                                    $is_done = isset( $completed_map[ $af->form_type ] );
                                    $completed_record = $is_done ? $completed_map[ $af->form_type ] : null;
                                ?>
                                    <tr style="border-bottom:1px solid #f3f4f6;">
                                        <td style="padding:12px; font-weight:bold; color:#111827;"><?php echo esc_html( $label ); ?></td>
                                        <td style="padding:12px; color:#4b5563;"><?php echo esc_html( wp_date( 'M j, Y', strtotime( $af->created_at ) ) ); ?></td>
                                        <td style="padding:12px;">
                                            <?php if ( $is_done ) : ?>
                                                <span style="background:#d1fae5; color:#065f46; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">
                                                    Completed
                                                </span>
                                            <?php else : ?>
                                                <span style="background:#fef3c7; color:#92400e; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">
                                                    Pending
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:12px; text-align:right;">
                                            <?php if ( $is_done ) : ?>
                                                <button type="button" class="cree-view-form-btn" data-form-id="<?php echo esc_attr( $completed_record->id ); ?>" style="background:#3b82f6; color:#fff; border:none; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:bold; cursor:pointer;">
                                                    View Submission
                                                </button>
                                            <?php else : ?>
                                                <button type="button" class="cree-open-pending-form-btn" data-form-type="<?php echo esc_attr( $af->form_type ); ?>" style="background:#2563eb; color:#fff; border:none; padding:6px 14px; border-radius:4px; font-size:12px; font-weight:bold; cursor:pointer;">
                                                    ✍️ Complete Form
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ACTIVE FORM COMPLETION CONTAINER (Hidden by default) -->
            <div id="cree-client-form-fill-view" style="display:none; background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:24px; margin-top:20px;">
                <button type="button" id="cree-back-to-list-btn" style="background:#e5e7eb; color:#374151; border:none; padding:8px 16px; border-radius:4px; font-weight:bold; cursor:pointer; margin-bottom:20px;">
                    ← Back to Form List
                </button>
                <div id="cree-active-form-render-target"></div>
            </div>

            <!-- Form Details Modal -->
            <div id="cree-form-view-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
                <div style="background:#fff; width:90%; max-width:650px; max-height:85vh; border-radius:8px; padding:25px; overflow-y:auto; position:relative;">
                    <button type="button" id="cree-close-modal-btn" style="position:absolute; top:15px; right:15px; background:none; border:none; font-size:20px; cursor:pointer; font-weight:bold;">✕</button>
                    <h3 style="margin-top:0; color:#1e3a8a;">Form Response Details</h3>
                    <div id="cree-form-modal-content"><p>Loading form content...</p></div>
                </div>
            </div>

            <!-- V4.0 SMART BOOKING CALENDAR -->
            <div style="background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #e5e7eb; margin-top: 30px;">
                <h3 style="margin-top: 0; color: #1e3a8a; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">📅 Request a New Session</h3>
                <p style="font-size: 14px; color: #4b5563; margin-bottom: 20px;">Select a date below to view available appointment times. All times are based on your therapist's current availability.</p>
                
                <div style="margin-bottom: 20px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 8px; font-size: 14px;">Select a Date (Minimum 24-hr notice):</label>
                    <input type="date" id="cree-booking-date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" style="padding: 10px; border-radius: 6px; border: 1px solid #d1d5db; width: 100%; max-width: 250px; font-size: 15px;">
                </div>
                
                <div id="cree-available-slots-container" style="display: flex; flex-wrap: wrap; gap: 12px; min-height: 50px;">
                    <p style="color: #9ca3af; font-style: italic; margin: 0; padding-top: 10px;">Please select a date to view available times.</p>
                </div>
            </div>

            <!-- V5.0 MY TREATMENT PLAN SECTION -->
            <div style="background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #e5e7eb; margin-top: 30px;">
                <h3 style="margin-top: 0; color: #1e3a8a; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">🎯 My Treatment Plan</h3>
                <div id="cree-client-visible-plans-container">
                    <p style="color: #6b7280; font-weight: bold;">Loading your active plans...</p>
                </div>
            </div>

            <script>
            // Fetch visible plans when the portal loads
            jQuery(document).ready(function() {
                if (typeof CREE_INTAKE_FORM !== 'undefined') {
                    jQuery.post(CREE_INTAKE_FORM.ajax_url, {
                        action: 'cree_get_client_visible_plans',
                        nonce: CREE_INTAKE_FORM.nonce
                    }, function(res) {
                        if (res && res.success) {
                            jQuery('#cree-client-visible-plans-container').html(res.data.html);
                        } else {
                            jQuery('#cree-client-visible-plans-container').html('<p style="color:red;">Error loading plans.</p>');
                        }
                    });
                }
            });
            </script>

        </div>
        <script>
            // Listen for date selection
            jQuery('#cree-booking-date').on('change', function() {
                var selectedDate = jQuery(this).val();
                if (!selectedDate) return;
                
                jQuery('#cree-available-slots-container').html('<p style="color:#6b7280; font-weight:bold;">Loading available times...</p>');
                
                jQuery.post(CREE_INTAKE_FORM.ajax_url, {
                    action: 'cree_get_available_slots',
                    nonce: CREE_INTAKE_FORM.nonce,
                    date: selectedDate,
                    therapist_id: 1 
                }, function(res) {
                    if (res && res.success) {
                        var slotsHTML = '';
                        if (res.data.slots && res.data.slots.length > 0) {
                            res.data.slots.forEach(function(slot) {
                                var demandStyle = slot.high_demand ? 'border-color:#d97706; background:#fffbeb;' : 'border-color:#d1d5db; background:#f9fafb;';
                                var demandBadge = slot.high_demand ? '<span style="display:block; font-size:11px; color:#d97706; margin-top:4px;">🔥 High Demand</span>' : '';
                                
                                slotsHTML += '<button type="button" onclick="creeRequestSlot(\'' + slot.mysql_datetime + '\')" style="' + demandStyle + ' padding:12px 18px; border-radius:6px; border-width:1px; border-style:solid; cursor:pointer; text-align:center; transition:all 0.2s ease;">' + 
                                            '<span style="font-weight:bold; font-size:14px; color:#1f2937;">' + slot.time + '</span>' + demandBadge + 
                                            '</button>';
                            });
                        } else {
                            slotsHTML = '<p style="color:#ef4444; font-weight:bold;">' + (res.data.message || 'No available slots for this date.') + '</p>';
                        }
                        jQuery('#cree-available-slots-container').html(slotsHTML);
                    } else {
                        jQuery('#cree-available-slots-container').html('<p style="color:red;">Error loading schedule: ' + (res.data ? res.data.message : '') + '</p>');
                    }
                });
            });

            // Handle the request submission
            function creeRequestSlot(datetime) {
                if (!confirm('Are you sure you want to request this appointment time?')) return;
                
                jQuery.post(CREE_INTAKE_FORM.ajax_url, {
                    action: 'cree_submit_appointment_request',
                    nonce: CREE_INTAKE_FORM.nonce,
                    requested_datetime: datetime,
                    therapist_id: 1
                }, function(res) {
                    if (res && res.success) {
                        alert('✔️ ' + res.data.message);
                        jQuery('#cree-booking-date').trigger('change');
                    } else {
                        alert('❌ ' + (res.data.message || 'Error submitting request.'));
                    }
                });
            }
        </script>
        <?php
        return ob_get_clean();
    }
}