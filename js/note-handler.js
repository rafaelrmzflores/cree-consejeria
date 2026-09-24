jQuery(document).ready(function($) {

    // 1. Toggle Quick Add Client Box
    $(document).on('click', '#cree-toggle-add-client-btn', function(e) {
        e.preventDefault();
        $('#cree-add-client-wrapper').slideToggle(200);
    });

    // 2. Cancel Quick Add Button
    $(document).on('click', '#cree-cancel-add-client-btn', function(e) {
        e.preventDefault();
        $('#cree-add-client-wrapper').slideUp(200);
        $('#cree-add-client-form')[0].reset();
        $('#cree-add-client-feedback').hide();
    });

    // 3. Quick Add Client AJAX Submission
    $(document).on('submit', '#cree-add-client-form', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $form.find('.cree-submit-btn');
        const $feedback = $('#cree-add-client-feedback');
        const $clientSelect = $('#cree_client_select');

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

                    // Append new client to select dropdown and auto-select them
                    const optionText = response.data.display_name + ' (' + response.data.email + ')';
                    const newOption = new Option(optionText, response.data.user_id, true, true);
                    
                    $clientSelect.append(newOption).trigger('change');

                    setTimeout(function() {
                        $form[0].reset();
                        $('#cree-add-client-wrapper').slideUp(200);
                        $feedback.hide();
                    }, 1200);

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

    // 4. Enable Note Form & Fetch History when a Client is Selected
    $(document).on('change', '#cree_client_select', function() {
        const clientId = $(this).val();
        const $noteSubmitBtn = $('#cree-session-note-form .cree-submit-btn');
        const $historyContainer = $('#cree-client-history-container');

        if (!clientId) {
            $noteSubmitBtn.prop('disabled', true);
            $historyContainer.html('<p><em>Select a client to load previous session notes.</em></p>');
            return;
        }

        // Enable the save button in your "NEW NOTE FORM" section
        $noteSubmitBtn.prop('disabled', false);
        $historyContainer.html('<p><em>Cargando historial de notas...</em></p>');

        // Fetch client note history
        $.ajax({
            url: CREE_NOTE_FORM.ajax_url,
            type: 'POST',
            data: {
                action: 'cree_load_client_notes',
                client_id: clientId,
                security: CREE_NOTE_FORM.nonce
            },
            success: function(response) {
                if (response.success) {
                    $historyContainer.html(response.data.html);
                } else {
                    $historyContainer.html('<p style="color:#d63638;">' + (response.data || 'Error loading notes.') + '</p>');
                }
            },
            error: function() {
                $historyContainer.html('<p style="color:#d63638;">Server error loading history.</p>');
            }
        });
    });

    // 5. Submit New Session Note AJAX
    $(document).on('submit', '#cree-session-note-form', function(e) {
        e.preventDefault();

        const clientId = $('#cree_client_select').val();
        const sessionDate = $('#cree_session_date').val();
        const noteText = $('#cree_note_text').val();
        const $feedback = $('#cree-note-feedback');
        const $submitBtn = $(this).find('.cree-submit-btn');

        if (!clientId) return;

        $submitBtn.prop('disabled', true).text('Encrypted & Saving...');
        $feedback.hide();

        $.ajax({
            url: CREE_NOTE_FORM.ajax_url,
            type: 'POST',
            data: {
                action: 'cree_save_session_note',
                client_id: clientId,
                session_date: sessionDate,
                note_text: noteText,
                security: $('#cree-session-note-form input[name="security"]').val()
            },
            success: function(response) {
                if (response.success) {
                    $feedback.css({display: 'block', color: '#008a20'}).text(response.data.message);
                    $('#cree_note_text').val('');
                    // Refresh history timeline
                    $('#cree_client_select').trigger('change');
                } else {
                    $feedback.css({display: 'block', color: '#d63638'}).text('Error: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                $feedback.css({display: 'block', color: '#d63638'}).text('AJAX Error: ' + error);
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text('Save Secure Note');
            }
        });
    });

});