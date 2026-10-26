<?php
/**
 * Handles incoming intake form submissions.
 * Processes the dynamic payload, hooks into the CPT, sets taxonomy, and saves encrypted DB records.
 */
function cree_consejeria_handle_form_submission($payload) {
    global $wpdb;
    
    // 1. Initial Validation
    if (empty($payload['client_name']) || empty($payload['email']) || empty($payload['form_type'])) {
        return new WP_Error('missing_fields', __('Required core registration fields are missing.', 'cree-consejeria'));
    }

    // 2. Extract and Sanitize Core Fields
    $form_type    = sanitize_text_field($payload['form_type']);
    $client_name  = sanitize_text_field($payload['client_name']);
    $email        = sanitize_email($payload['email']);
    $signature    = isset($payload['signature']) ? sanitize_text_field($payload['signature']) : '';
    
    // Ensure date fields are never empty strings to prevent MySQL Strict Mode crashes
    $dob          = !empty($payload['dob']) ? sanitize_text_field($payload['dob']) : '1900-01-01';
    $sign_date    = !empty($payload['sign_date']) ? sanitize_text_field($payload['sign_date']) : gmdate('Y-m-d');
    $user_ip      = isset($payload['user_ip']) ? sanitize_text_field($payload['user_ip']) : '';

    // 3. Determine the actual Client WP User ID
    $client_wp_id = get_current_user_id();
    if ( ! $client_wp_id && ! empty( $email ) ) {
        $user = get_user_by( 'email', $email );
        if ( $user ) {
            $client_wp_id = $user->ID;
        }
    }

    // 4. Server-Side Duplicate Submission Check
    $table_name = $wpdb->prefix . 'cree_consejeria_client_forms_master';
    
    if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
        return new WP_Error('missing_table', 'The secure database table is missing. Please deactivate and reactivate the plugin in the admin panel to rebuild the tables.');
    }

    if ( $client_wp_id ) {
        $existing = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$table_name} WHERE wp_id = %d AND form_type = %s",
            $client_wp_id,
            $form_type
        ) );
        if ( $existing ) {
            return new WP_Error('already_submitted', __('You have already submitted this evaluation form.', 'cree-consejeria'));
        }
    }

    // 5. Create the Shadow CPT Record
    $post_args = array(
        'post_title'   => sprintf('%s - %s', $client_name, date('Y-m-d')),
        'post_type'    => 'cree-client-intake',
        'post_status'  => 'private', 
        'post_author'  => get_current_user_id() ? get_current_user_id() : 1,
    );

    $post_id = wp_insert_post($post_args);
    if (is_wp_error($post_id) || $post_id === 0) {
        return new WP_Error('cpt_creation_failed', __('Failed to register backend administrative post context.', 'cree-consejeria'));
    }

    // 6. Assign the Form Type Custom Taxonomy Term
    $term_slug = sanitize_title($form_type);
    $term_exists = term_exists($term_slug, 'intake_form_type');
    
    if (!$term_exists) {
        $term_label = ucwords(str_replace(['_', '-'], ' ', $form_type));
        wp_insert_term($term_label, 'intake_form_type', array('slug' => $term_slug));
    }
    
    wp_set_object_terms($post_id, $term_slug, 'intake_form_type');

    // 7. Isolate Dynamic Fields & Package JSON Payload
    $core_keys = ['form_type', 'client_name', 'dob', 'email', 'signature', 'sign_date', 'user_ip'];
    $dynamic_fields = array_diff_key($payload, array_flip($core_keys));
    $sanitized_dynamic = array_map('sanitize_text_field', $dynamic_fields);
    $json_encrypted_blob = wp_json_encode($sanitized_dynamic);
    
    if ( $json_encrypted_blob === false ) {
        $json_encrypted_blob = '{}'; 
    }

    // 8. Insert Metadata first using native WP core functionality (prevents strict-mode crashes)
    $insert_result = $wpdb->insert(
        $table_name,
        array(
            'wp_id'          => $client_wp_id,
            'form_type'      => $form_type,
            'client_name'    => $client_name,
            'dob'            => $dob,
            'email'          => $email,
            'signature'      => $signature,
            'signature_date' => $sign_date,
            'user_ip'        => $user_ip
        ),
        array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    // Capture the DB error immediately BEFORE executing wp_delete_post
    $db_insert_error = $wpdb->last_error;

    if ( $insert_result === false ) {
        wp_delete_post($post_id, true);
        return new WP_Error('database_insertion_failed', 'Metadata insertion failed. DB Error: ' . $db_insert_error);
    }

    $custom_table_id = $wpdb->insert_id;

    // 9. Retrieve persistent v3.0 key and securely update the payload blob via AES_ENCRYPT
    $encryption_key = get_option( 'cree_consejeria_encryption_key' );
    if ( empty( $encryption_key ) ) {
        $encryption_key = wp_generate_password( 64, true, true );
        update_option( 'cree_consejeria_encryption_key', $encryption_key );
    }

    $update_sql = $wpdb->prepare(
        "UPDATE {$table_name} SET form_data_encrypted = AES_ENCRYPT(%s, %s) WHERE id = %d",
        $json_encrypted_blob,
        $encryption_key,
        $custom_table_id
    );

    $update_result = $wpdb->query($update_sql);
    $db_update_error = $wpdb->last_error;

    if ( $update_result === false ) {
        // Rollback data if the encryption stage fails
        $wpdb->delete($table_name, array('id' => $custom_table_id));
        wp_delete_post($post_id, true);
        return new WP_Error('database_encryption_failed', 'Encryption layer failed. DB Error: ' . $db_update_error);
    }

    // 10. Link the encrypted database row directly to the Custom Post Type
    update_post_meta($post_id, '_cree_custom_table_record_id', $custom_table_id);

    return $post_id;
}