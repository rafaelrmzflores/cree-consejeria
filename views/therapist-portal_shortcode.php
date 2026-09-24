<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="cree-form-container" id="cree-therapist-portal">
    <h2>Therapist Dashboard: Session Notes</h2>
    
    <!-- Client Selection & Quick Add Toggle -->
    <div class="cree-form-group">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label for="cree_client_select" style="margin-bottom:0;">Select Client:</label>
            <button type="button" id="cree-toggle-add-client-btn" style="background: none; border: none; color: #0073aa; cursor: pointer; font-weight: bold; padding: 0; text-decoration: underline;">
                + Quick Add New Client
            </button>
        </div>

        <select id="cree_client_select" name="client_id">
            <option value="">-- Choose a Client --</option>
            <?php if ( ! empty( $clients ) ) : ?>
                <?php foreach ( $clients as $client ) : ?>
                    <option value="<?php echo esc_attr( $client->ID ); ?>">
                        <?php echo esc_html( $client->display_name . ' (' . $client->user_email . ')' ); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- QUICK ADD CLIENT FORM (COLLAPSIBLE) -->
    <div id="cree-add-client-wrapper" style="display: none; background: #ffffff; border: 1px solid #ccd0d4; padding: 15px; border-radius: 4px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h4 style="margin-top: 0; margin-bottom: 12px; color: #1d2327;">Create Client Account</h4>
        <form id="cree-add-client-form">
            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;">
                <div style="flex: 1; min-width: 140px;">
                    <label for="cree_new_first_name" style="font-size: 12px; display: block; margin-bottom: 4px;">First Name:</label>
                    <input type="text" id="cree_new_first_name" required style="width: 100%;">
                </div>
                <div style="flex: 1; min-width: 140px;">
                    <label for="cree_new_last_name" style="font-size: 12px; display: block; margin-bottom: 4px;">Last Name:</label>
                    <input type="text" id="cree_new_last_name" required style="width: 100%;">
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <label for="cree_new_email" style="font-size: 12px; display: block; margin-bottom: 4px;">Email Address:</label>
                <input type="email" id="cree_new_email" required style="width: 100%;">
            </div>
            
            <button type="submit" class="cree-submit-btn" style="padding: 6px 12px; font-size: 13px;">Save Client</button>
            <button type="button" id="cree-cancel-add-client-btn" style="padding: 6px 12px; font-size: 13px; background: #f0f0f1; color: #2c3338; border: 1px solid #8c8f94; cursor: pointer; margin-left: 5px; border-radius: 3px;">Cancel</button>
            
            <p id="cree-add-client-feedback" style="display:none; margin-top: 8px; font-size: 13px; font-weight: bold;"></p>
        </form>
    </div>

    <hr style="margin: 20px 0; border-top: 1px solid #e0e0e0;">

    <!-- Two-column layout: Left for New Note, Right for Past Notes -->
    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
        
        <!-- NEW NOTE FORM -->
        <div style="flex: 1; min-width: 300px;">
            <h3>Add New Session Note</h3>
            <form id="cree-session-note-form">
                <?php wp_nonce_field( 'cree_secure_note_nonce', 'security' ); ?>
                
                <div class="cree-form-group">
                    <label for="cree_session_date">Session Date:</label>
                    <input type="date" id="cree_session_date" required value="<?php echo date( 'Y-m-d' ); ?>">
                </div>

                <div class="cree-form-group">
                    <label for="cree_note_text">Encrypted Note:</label>
                    <textarea id="cree_note_text" rows="6" required placeholder="Type session details here..."></textarea>
                </div>

                <button type="submit" class="cree-submit-btn" disabled>Save Secure Note</button>
                <p id="cree-note-feedback" style="display:none; margin-top:10px;"></p>
            </form>
        </div>

        <!-- PAST NOTES TIMELINE -->
        <div style="flex: 1; min-width: 300px; background: #f9f9f9; padding: 15px; border-radius: 8px;">
            <h3>Client History</h3>
            <div id="cree-client-history-container">
                <p><em>Select a client to load previous session notes.</em></p>
            </div>
        </div>

    </div>
</div>