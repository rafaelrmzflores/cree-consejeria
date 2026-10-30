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

        // 3. Fetch Next Upcoming Appointment for Telehealth Banner
        $appointments_table = $wpdb->prefix . 'cree_consejeria_appointments';
        $next_appt = $wpdb->get_row( $wpdb->prepare(
            "SELECT appointment_date, telehealth_link FROM {$appointments_table} 
             WHERE client_id = %d AND status = 'scheduled' AND appointment_date >= NOW() 
             ORDER BY appointment_date ASC LIMIT 1",
            $current_user_id
        ) );

        // 4. Fetch Billing History
        $billing_table = $wpdb->prefix . 'cree_consejeria_billing';
        $client_bills = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$billing_table} WHERE client_id = %d ORDER BY created_at DESC", $current_user_id) );

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

        <style>
            /* Prevent English text from flashing during page and form loads */
            .cree-is-translating .cree-bilingual-text,
            .cree-is-translating .cree-bilingual-placeholder {
                visibility: hidden !important;
                opacity: 0 !important;
            }
            .cree-bilingual-text, .cree-bilingual-placeholder {
                transition: opacity 0.15s ease-in-out;
            }
        </style>

        <!-- <div class="cree-form-container" id="cree-client-portal-wrapper" style="max-width:900px; margin:0 auto; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;"> -->
        <div class="cree-form-container cree-is-translating" id="cree-client-portal-wrapper" style="max-width:900px; margin:0 auto; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
            <!-- HEADER -->
            <div style="background:#1e3a8a; color:#fff; padding:20px; border-radius:8px 8px 0 0; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start;">
                
                <!-- LOGO AND WELCOME TEXT -->
                <div style="display: flex; align-items: center; gap: 15px;">
                    <!-- Logo Image -->
                    <!-- <img src="<?php echo esc_url( plugins_url( 'assets/images/cree-logo-azul.png', dirname(__FILE__) ) ); ?>" alt="CREE Consejería Logo" style="max-height: 60px; width: auto; background: #fff; padding: 5px; border-radius: 6px;"> -->
                    <img src="<?php echo esc_url( plugins_url( 'assets/images/cree-logo-azul.png', dirname(__FILE__) ) ); ?>" alt="CREE Consejería Logo" style="max-height: 125px; width: auto;">

                    <div>
                        <h2 style="margin:0; font-size:22px; color:#fff;"><span class="cree-bilingual-text" data-en="Welcome," data-es="Bienvenido(a),">Welcome,</span> <?php echo esc_html( $current_user->display_name ); ?></h2>
                        <p class="cree-bilingual-text" data-en="Manage your forms, appointments, and billing securely." data-es="Administre sus formularios, citas y facturación de forma segura." style="margin:5px 0 0 0; font-size:14px; opacity:0.9;">Manage your forms, appointments, and billing securely.</p>
                    </div>
                </div>
                
                <!-- LANGUAGE TOGGLE -->
                <div style="background: rgba(255,255,255,0.2); padding: 4px; border-radius: 20px; display: flex; gap: 4px;">
                    <button type="button" class="cree-lang-btn active" data-lang="en" style="border: none; background: #fff; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: bold; cursor: pointer; color: #1e3a8a; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">EN</button>
                    <button type="button" class="cree-lang-btn" data-lang="es" style="border: none; background: transparent; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: bold; cursor: pointer; color: #fff;">ES</button>
                </div>
            </div>

            <!-- TAB NAVIGATION -->
            <div style="border-bottom: 2px solid #e5e7eb; margin-bottom: 20px; display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px;">
                <button class="cree-client-nav-btn active cree-bilingual-text" data-en="📋 My Forms" data-es="📋 Mis Formularios" onclick="creeSwitchClientTab(event, 'client-tab-forms')" style="padding: 10px 16px; background: none; border: none; border-bottom: 3px solid #2563eb; color: #2563eb; font-weight: bold; cursor: pointer; white-space: nowrap;">📋 My Forms</button>
                <button class="cree-client-nav-btn cree-bilingual-text" data-en="📅 Appointments" data-es="📅 Citas" onclick="creeSwitchClientTab(event, 'client-tab-appointments')" style="padding: 10px 16px; background: none; border: none; border-bottom: 3px solid transparent; color: #4b5563; font-weight: bold; cursor: pointer; white-space: nowrap;">📅 Appointments</button>
                <button class="cree-client-nav-btn cree-bilingual-text" data-en="🎯 Treatment Plan" data-es="🎯 Plan de Tratamiento" onclick="creeSwitchClientTab(event, 'client-tab-plan')" style="padding: 10px 16px; background: none; border: none; border-bottom: 3px solid transparent; color: #4b5563; font-weight: bold; cursor: pointer; white-space: nowrap;">🎯 Treatment Plan</button>
                <button class="cree-client-nav-btn cree-bilingual-text" data-en="💳 Billing" data-es="💳 Facturación" onclick="creeSwitchClientTab(event, 'client-tab-billing')" style="padding: 10px 16px; background: none; border: none; border-bottom: 3px solid transparent; color: #4b5563; font-weight: bold; cursor: pointer; white-space: nowrap;">💳 Billing</button>
                <button class="cree-client-nav-btn cree-bilingual-text" data-en="⚙️ Settings" data-es="⚙️ Ajustes" onclick="creeSwitchClientTab(event, 'client-tab-settings')" style="padding: 10px 16px; background: none; border: none; border-bottom: 3px solid transparent; color: #4b5563; font-weight: bold; cursor: pointer; white-space: nowrap;">⚙️ Settings</button>
            </div>

            <!-- TAB 1: FORMS -->
            <div id="client-tab-forms" class="cree-client-tab-content">
                <div id="cree-client-form-list-view">
                    <div style="background:#fff; border:1px solid #e5e7eb; padding:20px; border-radius:8px;">
                        <h3 class="cree-bilingual-text" data-en="Assigned Intake & Consent Documents" data-es="Documentos de Admisión y Consentimiento Asignados" style="margin-top:0; color:#1f2937;">Assigned Intake & Consent Documents</h3>
                        <?php if ( empty( $assigned_forms ) && empty( $completed_forms ) ) : ?>
                            <div style="background:#f0f6fc; border-left:4px solid #72aee6; padding:15px; margin-top:15px; border-radius:4px;">
                                <p class="cree-bilingual-text" data-en="You currently have no pending or completed forms assigned to your account." data-es="Actualmente no tiene formularios pendientes o completados asignados a su cuenta." style="margin:0; color:#1e3a8a;">You currently have no pending or completed forms assigned to your account.</p>
                            </div>
                        <?php else : ?>
                            <table class="cree-portal-table" style="width:100%; border-collapse:collapse; margin-top:15px; font-size:14px;">
                                <thead>
                                    <tr style="background:#f9fafb; text-align:left; border-bottom:2px solid #e5e7eb;">
                                        <th style="padding:12px;"><span class="cree-bilingual-text" data-en="Form Document" data-es="Documento">Form Document</span></th>
                                        <th style="padding:12px;"><span class="cree-bilingual-text" data-en="Assigned Date" data-es="Fecha Asignada">Assigned Date</span></th>
                                        <th style="padding:12px;"><span class="cree-bilingual-text" data-en="Status" data-es="Estado">Status</span></th>
                                        <th style="padding:12px; text-align:right;"><span class="cree-bilingual-text" data-en="Action" data-es="Acción">Action</span></th>
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
                                                    <span class="cree-bilingual-text" data-en="Completed" data-es="Completado" style="background:#d1fae5; color:#065f46; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">Completed</span>
                                                <?php else : ?>
                                                    <span class="cree-bilingual-text" data-en="Pending" data-es="Pendiente" style="background:#fef3c7; color:#92400e; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding:12px; text-align:right;">
                                                <?php if ( $is_done ) : ?>
                                                    <button type="button" class="cree-view-form-btn cree-bilingual-text" data-en="View" data-es="Ver" data-form-id="<?php echo esc_attr( $completed_record->id ); ?>" style="background:#3b82f6; color:#fff; border:none; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:bold; cursor:pointer;">View</button>
                                                <?php else : ?>
                                                    <button type="button" class="cree-open-pending-form-btn cree-bilingual-text" data-en="✍️ Complete Form" data-es="✍️ Completar" data-form-type="<?php echo esc_attr( $af->form_type ); ?>" style="background:#2563eb; color:#fff; border:none; padding:6px 14px; border-radius:4px; font-size:12px; font-weight:bold; cursor:pointer;">✍️ Complete Form</button>
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
                    <button type="button" id="cree-back-to-list-btn" class="cree-bilingual-text" data-en="← Back to Form List" data-es="← Volver a la Lista" style="background:#e5e7eb; color:#374151; border:none; padding:8px 16px; border-radius:4px; font-weight:bold; cursor:pointer; margin-bottom:20px;">← Back to Form List</button>
                    <div id="cree-active-form-render-target"></div>
                </div>
            </div>

            <!-- TAB 2: APPOINTMENTS & BOOKING -->
            <div id="client-tab-appointments" class="cree-client-tab-content" style="display:none;">
                
                <!-- TELEHEALTH BANNER -->
                <?php 
                if ( !empty($next_appt) && ! empty( $next_appt->telehealth_link ) ) : 
                    $appt_time = strtotime( $next_appt->appointment_date );
                    $time_diff = $appt_time - current_time('timestamp');
                    if ( $time_diff > -3600 && $time_diff < 86400 ) :
                ?>
                    <div style="background: #eff6ff; border: 2px solid #3b82f6; border-radius: 8px; padding: 25px; margin-bottom: 25px; text-align: center; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.1);">
                        <h3 class="cree-bilingual-text" data-en="🎥 Upcoming Video Session" data-es="🎥 Próxima Sesión por Video" style="margin-top: 0; color: #1e3a8a; font-size: 22px;">🎥 Upcoming Video Session</h3>
                        <p style="font-size: 15px; color: #374151; margin-bottom: 20px;"><span class="cree-bilingual-text" data-en="Scheduled for" data-es="Programada para">Scheduled for</span> <strong><?php echo esc_html( wp_date( 'l, F j \a\t g:i A T', $appt_time ) ); ?></strong>.</p>
                        <a href="<?php echo esc_url( $next_appt->telehealth_link ); ?>" target="_blank" class="cree-bilingual-text" data-en="Join Video Session Now" data-es="Unirse a la Sesión Ahora" style="background: #2563eb; color: #fff; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-size: 16px; font-weight: bold; display: inline-block; transition: background 0.2s;">Join Video Session Now</a>
                    </div>
                <?php 
                    endif; 
                endif; 
                ?>

                <div style="background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <h3 class="cree-bilingual-text" data-en="📅 Request a New Session" data-es="📅 Solicitar Nueva Sesión" style="margin-top: 0; color: #1e3a8a; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">📅 Request a New Session</h3>
                    <p class="cree-bilingual-text" data-en="Select a date below to view available appointment times. All times are based on your therapist's current availability." data-es="Seleccione una fecha a continuación para ver los horarios disponibles. Todos los horarios se basan en la disponibilidad actual de su terapeuta." style="font-size: 14px; color: #4b5563; margin-bottom: 20px;">Select a date below to view available appointment times. All times are based on your therapist's current availability.</p>
                    <div style="margin-bottom: 20px;">
                        <input type="date" id="cree-booking-date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" style="padding: 10px; border-radius: 6px; border: 1px solid #d1d5db; width: 100%; max-width: 250px; font-size: 15px;">
                    </div>
                    <div id="cree-available-slots-container" style="display: flex; flex-wrap: wrap; gap: 12px; min-height: 50px;">
                        <p class="cree-bilingual-text" data-en="Please select a date to view available times." data-es="Por favor seleccione una fecha para ver los horarios disponibles." style="color: #9ca3af; font-style: italic; margin: 0; padding-top: 10px;">Please select a date to view available times.</p>
                    </div>
                </div>
            </div>

            <!-- TAB 3: TREATMENT PLAN -->
            <div id="client-tab-plan" class="cree-client-tab-content" style="display:none;">
                <div style="background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <h3 class="cree-bilingual-text" data-en="🎯 My Treatment Plan" data-es="🎯 Mi Plan de Tratamiento" style="margin-top: 0; color: #1e3a8a; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">🎯 My Treatment Plan</h3>
                    <div id="cree-client-visible-plans-container">
                        <p class="cree-bilingual-text" data-en="Loading your active plans..." data-es="Cargando sus planes activos..." style="color: #6b7280; font-weight: bold;">Loading your active plans...</p>
                    </div>
                </div>
            </div>

            <!-- TAB 4: BILLING & SUPERBILLS -->
            <div id="client-tab-billing" class="cree-client-tab-content" style="display:none;">
                <div style="background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <h3 class="cree-bilingual-text" data-en="💳 Billing & Superbills" data-es="💳 Facturación y Superbills" style="margin-top: 0; color: #1e3a8a; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">💳 Billing & Superbills</h3>
                    <p class="cree-bilingual-text" data-en="Review your account balance and download Superbills for insurance reimbursement." data-es="Revise el saldo de su cuenta y descargue Superbills para el reembolso del seguro." style="font-size: 14px; color: #4b5563; margin-bottom: 15px;">Review your account balance and download Superbills for insurance reimbursement.</p>
                    
                    <?php if ( empty( $client_bills ) ) : ?>
                        <div class="cree-bilingual-text" data-en="You have no billing records at this time." data-es="No tiene registros de facturación en este momento." style="background: #f9fafb; padding: 20px; border-radius: 6px; text-align: center; color: #6b7280; font-style: italic;">
                            You have no billing records at this time.
                        </div>
                    <?php else : ?>
                        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                            <thead>
                                <tr style="background: #f3f4f6; text-align: left; border-bottom: 2px solid #e5e7eb;">
                                    <th style="padding: 12px;"><span class="cree-bilingual-text" data-en="Date" data-es="Fecha">Date</span></th>
                                    <th style="padding: 12px;"><span class="cree-bilingual-text" data-en="Description" data-es="Descripción">Description</span></th>
                                    <th style="padding: 12px;"><span class="cree-bilingual-text" data-en="Amount" data-es="Cantidad">Amount</span></th>
                                    <th style="padding: 12px;"><span class="cree-bilingual-text" data-en="Status" data-es="Estado">Status</span></th>
                                    <th style="padding: 12px; text-align: right;"><span class="cree-bilingual-text" data-en="Action" data-es="Acción">Action</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $client_bills as $bill ) : ?>
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 12px; font-weight: bold;"><?php echo wp_date('M j, Y', strtotime($bill->created_at)); ?></td>
                                        <td style="padding: 12px; color: #4b5563;"><?php echo esc_html($bill->description); ?></td>
                                        <td style="padding: 12px; color: #111827;">$<?php echo number_format($bill->amount, 2); ?></td>
                                        <td style="padding: 12px;">
                                            <span style="padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; text-transform: uppercase; <?php echo $bill->status === 'paid' ? 'background:#d1fae5; color:#065f46;' : 'background:#fee2e2; color:#991b1b;'; ?>">
                                                <?php echo esc_html($bill->status); ?>
                                            </span>
                                        </td>
                                        <td style="padding: 12px; text-align: right;">
                                            <button type="button" class="cree-bilingual-text" data-en="📄 Download" data-es="📄 Descargar" onclick="creeGenerateSuperbill(<?php echo $bill->id; ?>)" style="background: #fff; border: 1px solid #d1d5db; color: #374151; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; transition: all 0.2s;">
                                                📄 Download
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAB 5: ACCOUNT SETTINGS -->
            <div id="client-tab-settings" class="cree-client-tab-content" style="display:none;">
                <div style="background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <h3 class="cree-bilingual-text" data-en="🔐 Update Password" data-es="🔐 Actualizar Contraseña" style="margin-top: 0; color: #1e3a8a; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">🔐 Update Password</h3>
                    <p class="cree-bilingual-text" data-en="Use the form below to change your temporary password to a secure, private one that you can remember." data-es="Use el siguiente formulario para cambiar su contraseña temporal a una privada y segura que pueda recordar." style="font-size: 14px; color: #4b5563; margin-bottom: 20px;">Use the form below to change your temporary password to a secure, private one that you can remember.</p>
                    
                    <form id="cree-client-password-form" style="max-width: 400px;">
                        <div style="margin-bottom: 15px;">
                            <label class="cree-bilingual-text" data-en="New Password (Min. 8 characters)" data-es="Nueva Contraseña (Mín. 8 caracteres)" style="display: block; font-size: 13px; font-weight: bold; color: #374151; margin-bottom: 5px;">New Password (Min. 8 characters)</label>
                            <input type="password" name="new_password" required minlength="8" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label class="cree-bilingual-text" data-en="Confirm New Password" data-es="Confirmar Nueva Contraseña" style="display: block; font-size: 13px; font-weight: bold; color: #374151; margin-bottom: 5px;">Confirm New Password</label>
                            <input type="password" name="confirm_password" required minlength="8" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        </div>
                        <button type="submit" class="cree-bilingual-text" data-en="Save New Password" data-es="Guardar Nueva Contraseña" style="background: #2563eb; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: bold; cursor: pointer; transition: background 0.2s;">
                            Save New Password
                        </button>
                        <div id="cree-password-feedback" style="margin-top: 10px; font-size: 13px; font-weight: bold; display: none;"></div>
                    </form>
                </div>
            </div>

            <!-- Form Details Modal -->
            <div id="cree-form-view-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
                <div style="background:#fff; width:90%; max-width:650px; max-height:85vh; border-radius:8px; padding:25px; overflow-y:auto; position:relative;">
                    <button type="button" id="cree-close-modal-btn" style="position:absolute; top:15px; right:15px; background:none; border:none; font-size:20px; cursor:pointer; font-weight:bold;">✕</button>
                    <h3 class="cree-bilingual-text" data-en="Form Response Details" data-es="Detalles de Respuesta del Formulario" style="margin-top:0; color:#1e3a8a;">Form Response Details</h3>
                    <div id="cree-form-modal-content"><p class="cree-bilingual-text" data-en="Loading form content..." data-es="Cargando contenido del formulario...">Loading form content...</p></div>
                </div>
            </div>

        </div>

        <script>
            // Client Portal Tab Switcher
            function creeSwitchClientTab(evt, tabId) {
                evt.preventDefault();
                // Reset buttons
                jQuery('.cree-client-nav-btn').css({
                    'border-bottom': '3px solid transparent',
                    'color': '#4b5563'
                });
                // Highlight active button
                jQuery(evt.currentTarget).css({
                    'border-bottom': '3px solid #2563eb',
                    'color': '#2563eb'
                });
                // Hide all tabs, show selected
                jQuery('.cree-client-tab-content').hide();
                jQuery('#' + tabId).show();
            }

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

            // Generate & Print Superbill (Client Portal Version)
            function creeGenerateSuperbill(billingId) {
                jQuery.post(CREE_INTAKE_FORM.ajax_url, {
                    action: 'cree_generate_superbill',
                    nonce: CREE_INTAKE_FORM.nonce, // Only send the correct client nonce
                    billing_id: billingId
                }, function(res) {
                    if (res && res.success) {
                        var printWin = window.open('', '_blank');
                        printWin.document.write('<!DOCTYPE html><html><head><title>Superbill - CREE Consejería</title>');
                        printWin.document.write('<style>@media print { .no-print { display: none; } }</style></head><body>');
                        printWin.document.write('<div class="no-print" style="text-align:right; margin-bottom:20px; padding:20px;"><button onclick="window.print()" style="padding:10px 20px; background:#2563eb; color:#fff; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">🖨️ Print / Save as PDF</button></div>');
                        printWin.document.write(res.data.html);
                        printWin.document.write('</body></html>');
                        printWin.document.close();
                    } else {
                        alert('Error: ' + (res.data.message || 'Could not generate Superbill.'));
                    }
                }).fail(function() {
                    alert('Server connection error. Security check failed.');
                });
            }

            // Handle Client Password Update
            jQuery(document).on('submit', '#cree-client-password-form', function(e) {
                e.preventDefault();
                var $btn = jQuery(this).find('button[type="submit"]');
                var $feedback = jQuery('#cree-password-feedback');
                
                var pass1 = jQuery('input[name="new_password"]').val();
                var pass2 = jQuery('input[name="confirm_password"]').val();
                
                if (pass1 !== pass2) {
                    $feedback.css('color', '#dc2626').text('❌ Passwords do not match.').show();
                    return;
                }
                
                $btn.prop('disabled', true).text('Saving...');
                $feedback.hide();
                
                jQuery.post(CREE_INTAKE_FORM.ajax_url, jQuery(this).serialize() + '&action=cree_update_client_password&nonce=' + CREE_INTAKE_FORM.nonce, function(res) {
                    if (res && res.success) {
                        $feedback.css('color', '#10b981').text('✔️ ' + res.data.message).show();
                        jQuery('#cree-client-password-form')[0].reset();
                    } else {
                        $feedback.css('color', '#dc2626').text('❌ Error: ' + (res.data.message || 'Could not update password.')).show();
                    }
                    $btn.prop('disabled', false).text('Save New Password');
                }).fail(function() {
                    $feedback.css('color', '#dc2626').text('❌ Server connection error.').show();
                    $btn.prop('disabled', false).text('Save New Password');
                });
            });

            // Client Portal Language Toggle
            // jQuery(document).on('click', '.cree-lang-btn', function() {
            //     var selectedLang = jQuery(this).data('lang');
                
            //     // Update the toggle button styles visually
            //     jQuery('.cree-lang-btn').css({'background': 'transparent', 'box-shadow': 'none', 'color': '#fff'}).removeClass('active');
            //     jQuery(this).css({'background': '#fff', 'box-shadow': '0 1px 2px rgba(0,0,0,0.1)', 'color': '#1e3a8a'}).addClass('active');

            //     // Swap all text instantly
            //     jQuery('.cree-bilingual-text').each(function() {
            //         var newText = jQuery(this).data(selectedLang);
            //         if (newText) {
            //             jQuery(this).text(newText);
            //         }
            //     });

            //     // Swap any input placeholders (if you add them to forms later)
            //     jQuery('.cree-bilingual-placeholder').each(function() {
            //         var newPlaceholder = jQuery(this).data(selectedLang);
            //         if (newPlaceholder) {
            //             jQuery(this).attr('placeholder', newPlaceholder);
            //         }
            //     });
            // });

            // 1. Initialize language from localStorage
            var creeCurrentLang = localStorage.getItem('cree_portal_lang') || 'en';

            // 2. Reusable global translation function
            window.creeApplyLanguage = function(lang) {
                creeCurrentLang = lang;
                localStorage.setItem('cree_portal_lang', lang); 
                
                // Update the toggle button styles visually
                jQuery('.cree-lang-btn').css({'background': 'transparent', 'box-shadow': 'none', 'color': '#4b5563'}).removeClass('active');
                jQuery('.cree-lang-btn[data-lang="' + lang + '"]').css({'background': '#fff', 'box-shadow': '0 1px 2px rgba(0,0,0,0.1)', 'color': '#1e3a8a'}).addClass('active');

                // Swap text for labels, headings, buttons, etc.
                jQuery('.cree-bilingual-text').each(function() {
                    var newText = jQuery(this).data(lang);
                    if (newText) {
                        jQuery(this).text(newText);
                    }
                });

                // Swap placeholders for inputs/textareas
                jQuery('.cree-bilingual-placeholder').each(function() {
                    var newPlaceholder = jQuery(this).data(lang);
                    if (newPlaceholder) {
                        jQuery(this).attr('placeholder', newPlaceholder);
                    }
                });

                // Reveal the text smoothly after translation is applied
                setTimeout(function() {
                    jQuery('#cree-client-portal-wrapper').removeClass('cree-is-translating');
                }, 30); // 30ms gives the browser just enough time to process the new words
            };

            // 3. RUN INSTANTLY on load (Do not wait for document.ready)
            // This stops the initial page load blink completely
            creeApplyLanguage(creeCurrentLang);

            // 4. Handle Toggle Clicks (with smooth fade)
            jQuery(document).ready(function($) {
                $(document).on('click', '.cree-lang-btn', function() {
                    var selectedLang = $(this).data('lang');
                    
                    // Hide text, swap it, then reveal it
                    $('#cree-client-portal-wrapper').addClass('cree-is-translating');
                    setTimeout(function() {
                        creeApplyLanguage(selectedLang);
                    }, 150); 
                });
            });

            // 5. Handle AJAX Form Loads
            jQuery(document).ajaxSend(function() {
                // Hide text instantly when a new form starts loading
                jQuery('#cree-client-portal-wrapper').addClass('cree-is-translating');
            });

            jQuery(document).ajaxComplete(function() {
                // Translate and reveal when the form finishes loading
                creeApplyLanguage(creeCurrentLang);
            });

        </script>
        <?php
        return ob_get_clean();
    }
}