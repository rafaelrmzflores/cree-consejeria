jQuery(document).ready(function($) {

    // =========================================================================
    // 1. QUICK ADD CLIENT INTERACTIONS
    // =========================================================================
    $(document).on('click', '#cree-toggle-add-client-btn', function(e) {
        e.preventDefault();
        $('#cree-add-client-wrapper').slideToggle(200);
    });

    $(document).on('click', '#cree-cancel-add-client-btn', function(e) {
        e.preventDefault();
        $('#cree-add-client-wrapper').slideUp(200);
        $('#cree-add-client-form')[0].reset();
        $('#cree-add-client-feedback').hide();
    });

    // Quick Add Client AJAX Submission
    $(document).on('submit', '#cree-add-client-form', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $form.find('.cree-submit-btn');
        const $feedback = $('#cree-add-client-feedback');

        $submitBtn.prop('disabled', true).text('Saving...');
        $feedback.hide();

        $.ajax({
            url: CREE_NOTE_FORM.ajax_url,
            type: 'POST',
            data: {
                action: 'cree_add_new_client',
                first_name: $('#cree_new_first_name').val(),
                last_name: $('#cree_new_last_name').val(),
                email: $('#cree_new_email').val(),
                security: CREE_NOTE_FORM.nonce
            },
            success: function(response) {
                if (response.success) {
                    $feedback.css({display: 'block', color: '#008a20'}).text(response.data.message);
                    setTimeout(function() {
                        $form[0].reset();
                        $('#cree-add-client-wrapper').slideUp(200);
                        $feedback.hide();
                        location.reload(); // Refresh to list new client in sidebar directory
                    }, 1000);
                } else {
                    $feedback.css({display: 'block', color: '#d63638'}).text('Error: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                $feedback.css({display: 'block', color: '#d63638'}).text('AJAX Error: ' + error);
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text('Save Client');
            }
        });
    });

    // =========================================================================
    // 2. SESSION NOTE EDIT, SAVE, & CANCEL (Dynamic Event Delegation)
    // =========================================================================

    // Toggle to Edit Mode
    $(document).on('click', '.cree-edit-note-btn', function(e) {
        e.preventDefault();
        var $card = $(this).closest('.cree-note-card');
        $card.find('.cree-note-text-view').hide();
        $card.find('.cree-note-edit-form').show();
        $(this).hide();
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

});