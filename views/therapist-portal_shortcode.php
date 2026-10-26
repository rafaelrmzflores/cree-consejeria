<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$current_user = wp_get_current_user();

// Fetch all registered clients
$clients = get_users( array(
    'role__in' => array( 'subscriber', 'customer', 'client' ),
    'orderby'  => 'display_name',
    'order'    => 'ASC',
) );
?>

<div class="cree-portal-wrapper" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 1200px; margin: 0 auto; color: #111827;">

    <!-- Header Navigation -->
    <div style="display: flex; justify-content: space-between; align-items: center; background: #1e3a8a; color: #fff; padding: 16px 24px; border-radius: 8px 8px 0 0;">
        <h2 style="margin: 0; font-size: 20px; color: #fff;">CREE Consejería — Therapist Workspace</h2>
        <span style="font-size: 14px;">Logged in: <strong><?php echo esc_html( $current_user->display_name ); ?></strong></span>
    </div>

    <!-- Main Workspace Area -->
    <div style="display: grid; grid-template-columns: 300px 1fr; background: #fff; border: 1px solid #e5e7eb; border-top: none; min-height: 600px;">
        
        <!-- SIDEBAR: CLIENT LIST -->
        <div style="border-right: 1px solid #e5e7eb; background: #f9fafb; padding: 16px;">
            <h3 style="margin-top: 0; font-size: 15px; color: #374151; text-transform: uppercase; letter-spacing: 0.5px;">Client Directory</h3>
            <input type="text" id="cree-client-search" placeholder="🔍 Search clients..." style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; margin-bottom: 12px; font-size: 13px;">

            <div id="cree-client-directory-list" style="display: flex; flex-direction: column; gap: 4px; max-height: 500px; overflow-y: auto;">
                <?php if ( ! empty( $clients ) ) : ?>
                    <?php foreach ( $clients as $c ) : ?>
                        <div class="cree-client-card" data-client-id="<?php echo intval( $c->ID ); ?>" onclick="creeSelectClient(<?php echo intval( $c->ID ); ?>, this)" style="padding: 10px 12px; background: #fff; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                            <strong style="display: block; font-size: 14px; color: #1f2937;"><?php echo esc_html( $c->display_name ); ?></strong>
                            <span style="font-size: 12px; color: #6b7280;"><?php echo esc_html( $c->user_email ); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p style="font-size: 13px; color: #6b7280;">No client accounts found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- MAIN PANEL: CLIENT PROFILE WORKSPACE -->
        <div style="padding: 24px;" id="cree-profile-main-panel">
            <div id="cree-profile-placeholder" style="text-align: center; padding: 80px 20px; color: #6b7280;">
                <div style="font-size: 48px; margin-bottom: 16px;">👤</div>
                <h3 style="margin: 0 0 8px 0; color: #374151;">Select a Client Profile</h3>
                <p style="margin: 0;">Click on any client name from the list on the left to manage their forms, notes, and session schedule.</p>
            </div>
            <div id="cree-profile-active-content" style="display: none;"></div>
        </div>

    </div>
</div>

<!-- Modal for viewing form details -->
<div id="cree-form-view-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:#fff; width:90%; max-width:650px; max-height:85vh; border-radius:8px; padding:25px; overflow-y:auto; position:relative;">
        <button type="button" onclick="jQuery('#cree-form-view-modal').hide()" style="position:absolute; top:15px; right:15px; background:none; border:none; font-size:20px; cursor:pointer;">✕</button>
        <h3 style="margin-top:0; color:#1e3a8a;">Decrypted Form Record</h3>
        <div id="cree-form-modal-content"><p>Loading details...</p></div>
    </div>
</div>

<style>
.cree-client-card:hover { border-color: #2563eb !important; background: #eff6ff !important; }
.cree-client-card.active-client { border-color: #2563eb !important; background: #dbeafe !important; border-left: 4px solid #2563eb !important; }
.cree-prof-tab { padding: 8px 16px; background: none; border: none; border-bottom: 2px solid transparent; font-weight: bold; color: #4b5563; cursor: pointer; }
.cree-prof-tab.active { color: #2563eb; border-bottom-color: #2563eb; }
</style>

<script>
var activeClientId = 0;

function creeUnassignForm(assignedId) {
    if (!confirm('Are you sure you want to unassign this pending form?')) {
        return;
    }

    jQuery.post(CREE_NOTE_FORM.ajax_url, {
        action: 'cree_unassign_form',
        security: CREE_NOTE_FORM.nonce,
        assigned_id: assignedId
    }, function(res) {
        if (res && res.success) {
            // Instantly refresh workspace view without full page reload
            creeSelectClient(activeClientId, jQuery('.cree-client-card.active-client'));
        } else {
            alert('Error: ' + (res.data ? (res.data.message || res.data) : 'Could not unassign form.'));
        }
    });
}

function creeSelectClient(clientId, elem) {
    activeClientId = clientId;
    jQuery('.cree-client-card').removeClass('active-client');
    jQuery(elem).addClass('active-client');

    jQuery('#cree-profile-placeholder').hide();
    jQuery('#cree-profile-active-content').show().html('<p>Loading client workspace...</p>');

    jQuery.post(CREE_NOTE_FORM.ajax_url, {
        action: 'cree_get_client_profile_data',
        security: CREE_NOTE_FORM.nonce,
        client_id: clientId
    }, function(res) {
        if (res && res.success) {
            jQuery('#cree-profile-active-content').html(res.data.html);
            // CRITICAL: Re-bind all tab switching, notes editing, and form submission events for this client view
            creeBindProfileEvents();
        } else {
            jQuery('#cree-profile-active-content').html('<p style="color:red;">' + (res.data ? (res.data.message || res.data) : 'Failed to retrieve profile data.') + '</p>');
        }
    }).fail(function(xhr, status, error) {
        jQuery('#cree-profile-active-content').html('<p style="color:red;">Server error: ' + error + '</p>');
    });
}

function creeSwitchProfileTab(evt, tabId) {
    evt.preventDefault();
    jQuery('.cree-prof-tab').removeClass('active');
    jQuery(evt.currentTarget).addClass('active');
    jQuery('.cree-prof-content').hide();
    jQuery('#' + tabId).show();
}

// function creeBindProfileEvents() {
//     // Submit Assign Form
//     jQuery('#cree-profile-assign-form').off('submit').on('submit', function(e) {
//         e.preventDefault();
//         jQuery.post(CREE_NOTE_FORM.ajax_url, jQuery(this).serialize() + '&action=cree_assign_form_to_client&security=' + CREE_NOTE_FORM.nonce, function(res) {
//             alert(res.data.message || res.data);
//             if (res.success) creeSelectClient(activeClientId, jQuery('.cree-client-card.active-client'));
//         });
//     });

//     // Submit Note
//     jQuery('#cree-profile-add-note').off('submit').on('submit', function(e) {
//         e.preventDefault();
//         jQuery.post(CREE_NOTE_FORM.ajax_url, jQuery(this).serialize() + '&action=cree_save_session_note&security=' + CREE_NOTE_FORM.nonce, function(res) {
//             alert(res.data.message || res.data);
//             if (res.success) creeSelectClient(activeClientId, jQuery('.cree-client-card.active-client'));
//         });
//     });

//     // Submit Appointment
//     jQuery('#cree-profile-add-appt').off('submit').on('submit', function(e) {
//         e.preventDefault();
//         jQuery.post(CREE_NOTE_FORM.ajax_url, jQuery(this).serialize() + '&action=cree_book_appointment&security=' + CREE_NOTE_FORM.nonce, function(res) {
//             alert(res.data.message || res.data);
//             if (res.success) creeSelectClient(activeClientId, jQuery('.cree-client-card.active-client'));
//         });
//     });
// }

function creeBindProfileEvents() {
    // 1. Submit Assign Form
    jQuery('#cree-profile-assign-form').off('submit').on('submit', function(e) {
        e.preventDefault();
        var $btn = jQuery(this).find('button[type="submit"]');
        $btn.prop('disabled', true).text('Assigning...');

        var formData = jQuery(this).serialize() + '&action=cree_assign_form_to_client&security=' + CREE_NOTE_FORM.nonce;

        jQuery.post(CREE_NOTE_FORM.ajax_url, formData, function(res) {
            $btn.prop('disabled', false).text('Assign to Client');
            if (res && res.success) {
                alert(res.data.message || 'Form successfully assigned!');
                if (typeof creeSelectClient === 'function' && typeof activeClientId !== 'undefined') {
                    creeSelectClient(activeClientId, jQuery('.cree-client-card.active-client'));
                } else {
                    location.reload();
                }
            } else {
                alert('Error assigning form: ' + (res.data ? (res.data.message || res.data) : 'Unknown error'));
            }
        });
    });

    // 2. Submit Session Note (Dynamic Append without page refresh)
    jQuery('#cree-profile-add-note').off('submit').on('submit', function(e) {
        e.preventDefault();
        var $form = jQuery(this);
        var $btn  = $form.find('button[type="submit"]');
        
        $btn.prop('disabled', true).text('Saving...');

        jQuery.post(CREE_NOTE_FORM.ajax_url, $form.serialize() + '&action=cree_save_session_note&security=' + CREE_NOTE_FORM.nonce, function(res) {
            $btn.prop('disabled', false).text('💾 Save Encrypted Note');

            if (res && res.success) {
                // Remove 'No session notes recorded' fallback message if present
                jQuery('#cree-no-notes-msg').remove();

                // Prepend new note card dynamically to the top of the history list
                jQuery('#cree-session-notes-history-container').prepend(res.data.note_html);

                // Clear the textarea for the next entry
                $form.find('textarea[name="note_text"]').val('');
            } else {
                alert('Error: ' + (res.data ? (res.data.message || res.data) : 'Could not save note.'));
            }
        }).fail(function(xhr, status, error) {
            $btn.prop('disabled', false).text('💾 Save Encrypted Note');
            alert('Server error: ' + error);
        });
    });

    // 3. Submit Appointment
    jQuery('#cree-profile-add-appt').off('submit').on('submit', function(e) {
        e.preventDefault();
        jQuery.post(CREE_NOTE_FORM.ajax_url, jQuery(this).serialize() + '&action=cree_book_appointment&security=' + CREE_NOTE_FORM.nonce, function(res) {
            alert(res.data.message || res.data);
            if (res.success && typeof creeSelectClient === 'function' && typeof activeClientId !== 'undefined') {
                creeSelectClient(activeClientId, jQuery('.cree-client-card.active-client'));
            }
        });
    });
}

function creeViewClientFormDetails(formId) {
    jQuery('#cree-form-modal-content').html('<p>Loading form details...</p>');
    jQuery('#cree-form-view-modal').css('display', 'flex');

    jQuery.post(CREE_NOTE_FORM.ajax_url, {
        action: 'cree_get_single_form_details',
        security: CREE_NOTE_FORM.nonce,
        form_id: formId
    }, function(res) {
        if (res.success) {
            jQuery('#cree-form-modal-content').html(res.data.html);
        } else {
            jQuery('#cree-form-modal-content').html('<p style="color:red;">' + (res.data.message || res.data) + '</p>');
        }
    });
}

function creeExportPDF(formId) {
    jQuery.post(CREE_NOTE_FORM.ajax_url, {
        action: 'cree_export_intake_pdf_html',
        security: CREE_NOTE_FORM.nonce,
        form_id: formId
    }, function(res) {
        if (res.success) {
            var win = window.open('', '_blank');
            win.document.write(res.data.html);
            win.document.close();
        } else {
            alert(res.data);
        }
    });
}

function creeUpdateAppointmentStatus(appointmentId, newStatus) {
    if (!confirm('Are you sure you want to change appointment status to ' + newStatus + '?')) return;

    jQuery.post(CREE_NOTE_FORM.ajax_url, {
        action: 'cree_update_appointment_status',
        security: CREE_NOTE_FORM.nonce,
        appointment_id: appointmentId,
        status: newStatus
    }, function(res) {
        if (res.success) {
            creeSelectClient(activeClientId, jQuery('.cree-client-card.active-client'));
        } else {
            alert(res.data.message || res.data);
        }
    });
}

// Client Directory Search Filter
jQuery(document).ready(function($) {
    $('#cree-client-search').on('keyup', function() {
        var term = $(this).val().toLowerCase();
        $('.cree-client-card').each(function() {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(term) > -1);
        });
    });
});

// Toggle to Edit Mode
$(document).on('click', '.cree-edit-note-btn', function(e) {
    e.preventDefault();
    var $card = $(this).closest('.cree-note-card');
    $card.find('.cree-note-text-view').hide();
    $card.find('.cree-edit-note-form').show();
    $(this).hide(); // Hide Edit button while editing
});

// Cancel Edit Mode
$(document).on('click', '.cree-cancel-note-btn', function(e) {
    e.preventDefault();
    var $card = $(this).closest('.cree-note-card');
    $card.find('.cree-note-edit-form').hide();
    $card.find('.cree-note-text-view').show();
    $card.find('.cree-edit-note-btn').show();
});

// Save Edited Note via AJAX
$(document).on('click', '.cree-save-note-btn', function(e) {
    e.preventDefault();
    var $card   = $(this).closest('.cree-note-card');
    var noteId  = $card.data('note-id');
    var newText = $card.find('.cree-note-edit-textarea').val();
    var $btn    = $(this);

    if (!newText.trim()) {
        alert('Note cannot be empty.');
        return;
    }

    $btn.prop('disabled', true).text('Saving...');

    $.post(CREE_NOTE_FORM.ajax_url, {
        action: 'cree_update_session_note',
        security: CREE_NOTE_FORM.nonce,
        note_id: noteId,
        note_text: newText
    }, function(res) {
        $btn.prop('disabled', false).text('💾 Save');

        if (res && res.success) {
            // Update static view text and switch back to view mode
            $card.find('.cree-note-text-view').text(newText).show();
            $card.find('.cree-note-edit-form').hide();
            $card.find('.cree-edit-note-btn').show();
        } else {
            alert('Error updating note: ' + (res.data ? (res.data.message || res.data) : 'Unknown error'));
        }
    }).fail(function(xhr, status, error) {
        $btn.prop('disabled', false).text('💾 Save');
        alert('Server error: ' + error);
    });
});

</script>