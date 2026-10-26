<?php 
global $wpdb;
wp_enqueue_style('cree-consejeria-admin-css');

$table_name = $wpdb->prefix . 'cree_consejeria_client_forms_master';

// FIX: Retrieve the v3.0 persistent encryption key
$encryption_key = get_option( 'cree_consejeria_encryption_key' );
if ( empty( $encryption_key ) ) {
    $encryption_key = SECURE_AUTH_KEY; // Fallback for highly legacy data
}

// FIX: Retrieve the exact database row linked to this post
$custom_table_id = get_post_meta( $post->ID, '_cree_custom_table_record_id', true );

if ( $custom_table_id ) {
    $submission = $wpdb->get_row( $wpdb->prepare(
        "SELECT id, form_type, client_name, dob, email, signature, signature_date, user_ip,
                CAST(AES_DECRYPT(form_data_encrypted, %s) AS CHAR) AS decrypted_json
         FROM $table_name 
         WHERE id = %d",
        $encryption_key, $custom_table_id
    ), ARRAY_A );
} else {
    // Fallback for older forms that weren't linked by meta ID
    $client_name = explode(' - ', $post->post_title)[0];
    $submission = $wpdb->get_row( $wpdb->prepare(
        "SELECT id, form_type, client_name, dob, email, signature, signature_date, user_ip,
                CAST(AES_DECRYPT(form_data_encrypted, %s) AS CHAR) AS decrypted_json
         FROM $table_name 
         WHERE client_name = %s ORDER BY id DESC LIMIT 1",
        $encryption_key, trim($client_name)
    ), ARRAY_A );
}

// 2. Fallback check if the secure table line doesn't match up
if ( ! $submission ) {
    echo '<p style="color:red; font-weight:bold;">' . __( 'Error: Secure data record matching this post was not found or the encryption key is invalid.', 'cree-consejeria' ) . '</p>';
    return;
}

// 3. Unpack the dynamic fields back into a clean array
$dynamic_answers = ! empty( $submission['decrypted_json'] ) ? json_decode( $submission['decrypted_json'], true ) : array();
     
// Security: Use nonces even for viewing layouts if you plan to introduce quick-actions inside the meta box later
wp_nonce_field( 'cree_viewing_secure_intake', 'cree_intake_nonce' );
?>
<div class="intake-display-wrapper">
    <div class="intake-group">
        <span class="intake-label">Form Schema Type:</span>
        <span class="intake-value"><strong><?php echo esc_html( strtoupper( str_replace( '_', ' ', $submission['form_type'] ) ) ); ?></strong></span>
    </div>
    <div class="intake-group">
        <span class="intake-label">Client Name:</span>
        <span class="intake-value"><?php echo esc_html( $submission['client_name'] ); ?></span>
    </div>
    <div class="intake-group">
        <span class="intake-label">Date of Birth:</span>
        <span class="intake-value"><?php echo esc_html( $submission['dob'] ); ?></span>
    </div>
    <div class="intake-group">
        <span class="intake-label">Email:</span>
        <span class="intake-value"><?php echo esc_html( $submission['email'] ); ?></span>
    </div>

    <?php if ( ! empty( $dynamic_answers ) && is_array( $dynamic_answers ) ) : ?>
        <h4 style="margin-top:20px; border-bottom: 2px solid #0056b3; padding-bottom:3px;"><?php _e('Dynamic Form Submissions Answers', 'cree-consejeria'); ?></h4>
        <?php foreach ( $dynamic_answers as $key => $value ) : ?>
            <div class="intake-group">
                <span class="intake-label"><?php echo esc_html( ucwords( str_replace( array('_', '-'), ' ', $key ) ) ); ?>:</span>
                <span class="intake-value"><?php echo esc_html( is_array($value) ? implode(', ', $value) : ( $value ? $value : '—' ) ); ?></span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h4 style="margin-top:20px; border-bottom: 2px solid #ccd0d4; padding-bottom:3px;"><?php _e('Legal Declarations / Tracking', 'cree-consejeria'); ?></h4>
    <div class="intake-group">
        <span class="intake-label">Digital Signature:</span>
        <span class="intake-value"><i>/s/ <?php echo esc_html( $submission['signature'] ); ?></i></span>
    </div>
    <div class="intake-group">
        <span class="intake-label">Signed Date:</span>
        <span class="intake-value"><?php echo esc_html( $submission['signature_date'] ); ?></span>
    </div>
    <div class="intake-group">
        <span class="intake-label">Submission IP Address:</span>
        <span class="intake-value"><?php echo esc_html( ! empty( $submission['user_ip'] ) ? $submission['user_ip'] : '—' ); ?></span>
    </div>
</div>