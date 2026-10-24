<?php
/**
 * Therapist Portal View Template
 * 
 * @package CreeConsejeriaCFM
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="cree-portal-wrapper" style="max-width: 1100px; margin: 30px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <h2 style="color: #1d2327; border-bottom: 2px solid #f0f0f1; padding-bottom: 12px; margin-bottom: 20px;">
        <?php esc_html_e( 'Portal del Terapeuta (Clinical Dashboard)', 'cree-consejeria' ); ?>
    </h2>

    <!-- Tab Navigation -->
    <div class="cree-portal-tabs" style="display: flex; border-bottom: 2px solid #e5e7eb; margin-bottom: 24px; gap: 8px; flex-wrap: wrap;">
        <button class="cree-tab-btn active" onclick="creeSwitchTab(event, 'cree-tab-intakes')" style="padding: 12px 18px; font-weight: 600; font-size: 14px; background: none; border: none; border-bottom: 3px solid #2563eb; cursor: pointer; color: #2563eb;">📋 <?php esc_html_e( 'Client Intakes', 'cree-consejeria' ); ?></button>
        <button class="cree-tab-btn" onclick="creeSwitchTab(event, 'cree-tab-notes')" style="padding: 12px 18px; font-weight: 600; font-size: 14px; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; color: #4b5563;">📝 <?php esc_html_e( 'Session Notes', 'cree-consejeria' ); ?></button>
        <button class="cree-tab-btn" onclick="creeSwitchTab(event, 'cree-tab-create-client')" style="padding: 12px 18px; font-weight: 600; font-size: 14px; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; color: #4b5563;">➕ <?php esc_html_e( 'Create Client', 'cree-consejeria' ); ?></button>
        <button class="cree-tab-btn" onclick="creeSwitchTab(event, 'cree-tab-appointments')" style="padding: 12px 18px; font-weight: 600; font-size: 14px; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; color: #4b5563;">📅 <?php esc_html_e( 'Appointments', 'cree-consejeria' ); ?></button>
        <button class="cree-tab-btn" onclick="creeSwitchTab(event, 'cree-tab-goals')" style="padding: 12px 18px; font-weight: 600; font-size: 14px; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; color: #4b5563;">🎯 <?php esc_html_e( 'Treatment Goals', 'cree-consejeria' ); ?></button>
    </div>

    <!-- TAB 1: SUBMITTED INTAKE FORMS -->
    <div id="cree-tab-intakes" class="cree-tab-content" style="display: block; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
        <h3 style="margin-top: 0;"><?php esc_html_e( 'Submitted Intake Evaluations', 'cree-consejeria' ); ?></h3>
        <?php
        global $wpdb;
        $forms_table = $wpdb->prefix . 'cree_consejeria_client_forms_master';
        $forms = $wpdb->get_results( "SELECT id, wp_id, form_type, client_name, email, created_at FROM {$forms_table} ORDER BY created_at DESC" );

        if ( empty( $forms ) ) {
            echo '<p><em>' . esc_html__( 'No submitted intake forms found.', 'cree-consejeria' ) . '</em></p>';
        } else {
            echo '<table class="widefat striped" style="width:100%; border-collapse:collapse;">';
            echo '<thead><tr><th>' . esc_html__( 'ID', 'cree-consejeria' ) . '</th><th>' . esc_html__( 'Client', 'cree-consejeria' ) . '</th><th>' . esc_html__( 'Form Type', 'cree-consejeria' ) . '</th><th>' . esc_html__( 'Date Submitted', 'cree-consejeria' ) . '</th><th>' . esc_html__( 'Actions', 'cree-consejeria' ) . '</th></tr></thead><tbody>';
            foreach ( $forms as $f ) {
                echo '<tr>';
                echo '<td>#' . intval( $f->id ) . '</td>';
                echo '<td><strong>' . esc_html( $f->client_name ) . '</strong><br><small>' . esc_html( $f->email ) . '</small></td>';
                echo '<td>' . esc_html( ucwords( str_replace( '_', ' ', $f->form_type ) ) ) . '</td>';
                echo '<td>' . esc_html( $f->created_at ) . '</td>';
                echo '<td><button class="button" onclick="creeExportPDF(' . intval( $f->id ) . ')">🖨️ ' . esc_html__( 'Export PDF', 'cree-consejeria' ) . '</button></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
        ?>
    </div>

    <!-- TAB 2: SESSION NOTES (Notes History at the Bottom) -->
    <div id="cree-tab-notes" class="cree-tab-content" style="display: none; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
        <h3 style="margin-top: 0;"><?php esc_html_e( 'Record Clinical Session Note', 'cree-consejeria' ); ?></h3>
        <form id="cree-session-note-form" style="max-width: 600px; margin-bottom: 25px;">
            <?php wp_nonce_field( 'cree_secure_note_nonce', 'security' ); ?>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php esc_html_e( 'Select Client:', 'cree-consejeria' ); ?></label>
                <select id="cree_note_client_id" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" required>
                    <option value="">-- <?php esc_html_e( 'Select Client', 'cree-consejeria' ); ?> --</option>
                    <?php if ( ! empty( $clients ) ) : ?>
                        <?php foreach ( $clients as $client ) : ?>
                            <option value="<?php echo intval( $client->ID ); ?>">
                                <?php echo esc_html( $client->display_name . ' (' . $client->user_email . ')' ); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php esc_html_e( 'Session Date:', 'cree-consejeria' ); ?></label>
                <input type="date" id="cree_session_date" value="<?php echo esc_attr( date( 'Y-m-d' ) ); ?>" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" required>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php esc_html_e( 'Clinical Note (Encrypted Storage):', 'cree-consejeria' ); ?></label>
                <textarea id="cree_note_text" rows="5" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" required placeholder="<?php esc_attr_e( 'Enter encrypted progress notes here...', 'cree-consejeria' ); ?>"></textarea>
            </div>
            
            <button type="submit" class="button button-primary" style="background: #2563eb; border-color: #2563eb; color: #fff; padding: 8px 16px; font-weight: bold; cursor: pointer;"><?php esc_html_e( 'Save Session Note', 'cree-consejeria' ); ?></button>
            <div id="cree-note-response" style="margin-top: 10px; font-weight: bold;"></div>
        </form>

        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 25px 0;">
        
        <h3><?php esc_html_e( 'Previous Session Notes', 'cree-consejeria' ); ?></h3>
        <div id="cree-client-notes-history">
            <p><em><?php esc_html_e( 'Select a client above to view their history.', 'cree-consejeria' ); ?></em></p>
        </div>
    </div>

    <!-- TAB 3: CREATE CLIENT -->
    <div id="cree-tab-create-client" class="cree-tab-content" style="display: none; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
        <h3 style="margin-top: 0;"><?php esc_html_e( 'Create New Client Account', 'cree-consejeria' ); ?></h3>
        <p style="color: #4b5563; font-size: 14px; margin-bottom: 20px;"><?php esc_html_e( 'Creating a client account automatically generates secure login credentials and emails a temporary password setup link.', 'cree-consejeria' ); ?></p>
        
        <form id="cree-add-client-form" style="max-width: 600px;">
            <?php wp_nonce_field( 'cree_secure_note_nonce', 'security' ); ?>
            
            <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 15px;">
                <div style="flex: 1; min-width: 200px;">
                    <label for="cree_new_first_name" style="font-weight: 600; display: block; margin-bottom: 5px;"><?php esc_html_e( 'First Name:', 'cree-consejeria' ); ?></label>
                    <input type="text" id="cree_new_first_name" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label for="cree_new_last_name" style="font-weight: 600; display: block; margin-bottom: 5px;"><?php esc_html_e( 'Last Name:', 'cree-consejeria' ); ?></label>
                    <input type="text" id="cree_new_last_name" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="cree_new_email" style="font-weight: 600; display: block; margin-bottom: 5px;"><?php esc_html_e( 'Email Address:', 'cree-consejeria' ); ?></label>
                <input type="email" id="cree_new_email" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
            </div>
            
            <button type="submit" class="button button-primary" style="background: #2563eb; border-color: #2563eb; color: #fff; padding: 8px 18px; font-weight: bold; cursor: pointer;"><?php esc_html_e( 'Save & Register Client', 'cree-consejeria' ); ?></button>
            
            <p id="cree-add-client-feedback" style="display:none; margin-top: 12px; font-size: 14px; font-weight: bold;"></p>
        </form>
    </div>

    <!-- TAB 4: APPOINTMENTS -->
    <div id="cree-tab-appointments" class="cree-tab-content" style="display: none; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
        <h3 style="margin-top: 0;"><?php esc_html_e( 'Schedule New Appointment', 'cree-consejeria' ); ?></h3>
        <form id="cree-add-appointment-form" style="max-width: 600px; margin-bottom: 30px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php esc_html_e( 'Select Client:', 'cree-consejeria' ); ?></label>
                <select id="appointment_client_id" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" required>
                    <option value="">-- <?php esc_html_e( 'Select Client', 'cree-consejeria' ); ?> --</option>
                    <?php if ( ! empty( $clients ) ) : ?>
                        <?php foreach ( $clients as $client ) : ?>
                            <option value="<?php echo intval( $client->ID ); ?>">
                                <?php echo esc_html( $client->display_name . ' (' . $client->user_email . ')' ); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php esc_html_e( 'Appointment Date & Time:', 'cree-consejeria' ); ?></label>
                <input type="datetime-local" id="appointment_date" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php esc_html_e( 'Session Notes / Type:', 'cree-consejeria' ); ?></label>
                <textarea id="appointment_notes" rows="2" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" placeholder="<?php esc_attr_e( 'Initial Consultation, Follow-up...', 'cree-consejeria' ); ?>"></textarea>
            </div>
            <button type="submit" class="button button-primary" style="background: #2563eb; border-color: #2563eb; color: #fff; padding: 8px 16px; font-weight: bold; cursor: pointer;"><?php esc_html_e( 'Schedule Session', 'cree-consejeria' ); ?></button>
        </form>
        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 25px 0;">
        <h3><?php esc_html_e( 'Upcoming & Past Sessions', 'cree-consejeria' ); ?></h3>
        <div id="cree-appointments-list"><p><em><?php esc_html_e( 'Loading appointments...', 'cree-consejeria' ); ?></em></p></div>
    </div>

    <!-- TAB 5: TREATMENT GOALS -->
    <div id="cree-tab-goals" class="cree-tab-content" style="display: none; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
        <h3 style="margin-top: 0;"><?php esc_html_e( 'Add Treatment Plan Goal', 'cree-consejeria' ); ?></h3>
        <form id="cree-add-goal-form" style="max-width: 600px; margin-bottom: 30px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php esc_html_e( 'Select Client:', 'cree-consejeria' ); ?></label>
                <select id="goal_client_id" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" required>
                    <option value="">-- <?php esc_html_e( 'Select Client', 'cree-consejeria' ); ?> --</option>
                    <?php if ( ! empty( $clients ) ) : ?>
                        <?php foreach ( $clients as $client ) : ?>
                            <option value="<?php echo intval( $client->ID ); ?>">
                                <?php echo esc_html( $client->display_name . ' (' . $client->user_email . ')' ); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php esc_html_e( 'Goal Title:', 'cree-consejeria' ); ?></label>
                <input type="text" id="goal_title" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" placeholder="<?php esc_attr_e( 'e.g. Anxiety Reduction Techniques', 'cree-consejeria' ); ?>" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php esc_html_e( 'Description & Interventions:', 'cree-consejeria' ); ?></label>
                <textarea id="goal_description" rows="3" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" placeholder="<?php esc_attr_e( 'Outline specific clinical targets...', 'cree-consejeria' ); ?>"></textarea>
            </div>
            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php esc_html_e( 'Target Completion Date:', 'cree-consejeria' ); ?></label>
                    <input type="date" id="goal_target_date" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php esc_html_e( 'Progress (%):', 'cree-consejeria' ); ?></label>
                    <input type="number" id="goal_progress" min="0" max="100" value="0" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
            </div>
            <button type="submit" class="button button-primary" style="background: #2563eb; border-color: #2563eb; color: #fff; padding: 8px 16px; font-weight: bold; cursor: pointer;"><?php esc_html_e( 'Save Goal', 'cree-consejeria' ); ?></button>
        </form>
        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 25px 0;">
        <h3><?php esc_html_e( 'Active Client Goals', 'cree-consejeria' ); ?></h3>
        <div id="cree-goals-list"><p><em><?php esc_html_e( 'Select a client above to view goals...', 'cree-consejeria' ); ?></em></p></div>
    </div>
</div>

<script>
function creeSwitchTab(evt, tabId) {
    evt.preventDefault();
    document.querySelectorAll('.cree-tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.color = '#4b5563';
        btn.style.borderBottomColor = 'transparent';
    });
    document.querySelectorAll('.cree-tab-content').forEach(tab => tab.style.display = 'none');
    
    evt.currentTarget.classList.add('active');
    evt.currentTarget.style.color = '#2563eb';
    evt.currentTarget.style.borderBottomColor = '#2563eb';
    document.getElementById(tabId).style.display = 'block';

    if (tabId === 'cree-tab-appointments') {
        creeLoadAppointments();
    }
}

function creeLoadAppointments() {
    jQuery.post(CREE_NOTE_FORM.ajax_url, {
        action: 'cree_get_user_appointments',
        security: CREE_NOTE_FORM.nonce
    }, function(res) {
        if(res.success) {
            jQuery('#cree-appointments-list').html(res.data.html);
        }
    });
}

function creeUpdateAppointmentStatus(appointmentId, newStatus) {
    if (!confirm('Are you sure you want to mark this appointment as ' + newStatus + '?')) {
        return;
    }

    jQuery.post(CREE_NOTE_FORM.ajax_url, {
        action: 'cree_update_appointment_status',
        security: CREE_NOTE_FORM.nonce,
        appointment_id: appointmentId,
        status: newStatus
    }, function(res) {
        console.log('Status Update Response:', res);
        if (res && res.success) {
            // Force re-fetch the appointments table
            creeLoadAppointments();
        } else {
            alert('Error updating status: ' + (res.data ? (res.data.message || res.data) : 'Unknown error'));
        }
    }).fail(function(xhr, status, error) {
        console.error('AJAX Error:', error, xhr.responseText);
        alert('Server Error: ' + error);
    });
}

function creeLoadGoals(clientId) {
    if (!clientId) {
        jQuery('#cree-goals-list').html('<p><em>Select a client above to view goals...</em></p>');
        return;
    }
    
    jQuery('#cree-goals-list').html('<p>Loading treatment goals...</p>');

    jQuery.post(CREE_NOTE_FORM.ajax_url, {
        action: 'cree_get_client_treatment_goals',
        security: CREE_NOTE_FORM.nonce,
        client_id: clientId
    }, function(res) {
        if (res.success) {
            jQuery('#cree-goals-list').html(res.data.html);
        } else {
            jQuery('#cree-goals-list').html('<p style="color:red;">' + (res.data.message || res.data) + '</p>');
        }
    });
}

jQuery(document).ready(function($) {
    // Handle Client Creation Form Submission
    $('#cree-add-client-form').on('submit', function(e) {
        e.preventDefault();
        var $feedback = $('#cree-add-client-feedback');
        $feedback.show().css('color', '#1d2327').text('Creating client account...');

        $.post(CREE_NOTE_FORM.ajax_url, {
            action: 'cree_add_new_client',
            security: CREE_NOTE_FORM.nonce,
            first_name: $('#cree_new_first_name').val(),
            last_name: $('#cree_new_last_name').val(),
            email: $('#cree_new_email').val()
        }, function(response) {
            if (response.success) {
                $feedback.css('color', 'green').text(response.data.message);
                $('#cree-add-client-form')[0].reset();
                
                var newOption = $('<option>', {
                    value: response.data.user_id,
                    text: response.data.display_name + ' (' + response.data.email + ')'
                });
                $('#cree_note_client_id, #appointment_client_id, #goal_client_id').append(newOption);
            } else {
                $feedback.css('color', '#d63638').text(response.data);
            }
        });
    });

    // Load Client Notes History when dropdown changes
    $('#cree_note_client_id').on('change', function() {
        var clientId = $(this).val();
        if (!clientId) {
            $('#cree-client-notes-history').html('<p><em>Select a client above to view their history.</em></p>');
            return;
        }
        $('#cree-client-notes-history').html('<p>Loading notes...</p>');
        $.post(CREE_NOTE_FORM.ajax_url, {
            action: 'cree_load_client_notes',
            security: CREE_NOTE_FORM.nonce,
            client_id: clientId
        }, function(response) {
            if (response.success) {
                $('#cree-client-notes-history').html(response.data.html);
            } else {
                $('#cree-client-notes-history').html('<p style="color:red;">' + response.data + '</p>');
            }
        });
    });

    // Save Session Note
    $('#cree-session-note-form').on('submit', function(e) {
        e.preventDefault();
        var clientId = $('#cree_note_client_id').val();
        var $response = $('#cree-note-response');
        
        $.post(CREE_NOTE_FORM.ajax_url, {
            action: 'cree_save_session_note',
            security: CREE_NOTE_FORM.nonce,
            client_id: clientId,
            session_date: $('#cree_session_date').val(),
            note_text: $('#cree_note_text').val()
        }, function(response) {
            $response.css('color', response.success ? 'green' : 'red').text(response.data.message || response.data);
            if (response.success) {
                $('#cree_note_text').val('');
                $('#cree_note_client_id').trigger('change');
            }
        });
    });

    // Handle Appointment Submit
    $('#cree-add-appointment-form').on('submit', function(e) {
        e.preventDefault();
        $.post(CREE_NOTE_FORM.ajax_url, {
            action: 'cree_book_appointment',
            security: CREE_NOTE_FORM.nonce,
            client_id: $('#appointment_client_id').val(),
            appointment_date: $('#appointment_date').val(),
            notes: $('#appointment_notes').val()
        }, function(res) {
            alert(res.data.message);
            if(res.success) {
                $('#cree-add-appointment-form')[0].reset();
                creeLoadAppointments();
            }
        });
    });

    // Handle Goal Submit
    $('#cree-add-goal-form').on('submit', function(e) {
        e.preventDefault();
        $.post(CREE_NOTE_FORM.ajax_url, {
            action: 'cree_save_treatment_goal',
            security: CREE_NOTE_FORM.nonce,
            client_id: $('#goal_client_id').val(),
            goal_title: $('#goal_title').val(),
            goal_description: $('#goal_description').val(),
            target_date: $('#goal_target_date').val(),
            progress_percentage: $('#goal_progress').val()
        }, function(res) {
            alert(res.data.message);
            if(res.success) {
                $('#cree-add-goal-form')[0].reset();
                creeLoadGoals($('#goal_client_id').val());
            }
        });
    });

    // Load goals when changing client selection dropdown
    $('#goal_client_id').on('change', function() {
        creeLoadGoals($(this).val());
    });
});

// Trigger PDF Window Export
function creeExportPDF(formId) {
    jQuery.post(CREE_NOTE_FORM.ajax_url, {
        action: 'cree_export_intake_pdf_html',
        security: CREE_NOTE_FORM.nonce,
        form_id: formId
    }, function(res) {
        if(res.success) {
            var win = window.open('', '_blank');
            win.document.write(res.data.html);
            win.document.close();
        } else {
            alert(res.data);
        }
    });
}
</script>