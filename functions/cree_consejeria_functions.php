<?php

/**
 * Handles incoming intake form submissions.
 * Processes the dynamic payload, hooks into the CPT, sets taxonomy, and saves encrypted DB records.
 * @param array $payload Raw array data compiled from the JavaScript fetch/AJAX request.
 * @return int|WP_Error Returns the generated WP Post ID on success, or WP_Error on failure.
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
    $dob          = sanitize_text_field($payload['dob']);
    $email        = sanitize_email($payload['email']);
    $signature    = sanitize_text_field($payload['signature']);
    $sign_date    = sanitize_text_field($payload['sign_date']);

    // 3. Create the Shadow CPT Record
    $post_args = array(
        'post_title'   => sprintf('%s — %s', $client_name, date('Y-m-d')),
        'post_type'    => 'cree-client-intake',
        'post_status'  => 'private', 
        'post_author'  => get_current_user_id() ? get_current_user_id() : 1, 
    );

    $wp_id = wp_insert_post($post_args);

    if (is_wp_error($wp_id) || $wp_id === 0) {
        return new WP_Error('cpt_creation_failed', __('Failed to register backend administrative post context.', 'cree-consejeria'));
    }

    // 4. Assign the Form Type Custom Taxonomy Term
    $term_slug = sanitize_title($form_type);
    $term_exists = term_exists($term_slug, 'intake_form_type');
    
    if (!$term_exists) {
        $term_label = ucwords(str_replace(['_', '-'], ' ', $form_type));
        wp_insert_term($term_label, 'intake_form_type', array('slug' => $term_slug));
    }
    
    wp_set_object_terms($wp_id, $term_slug, 'intake_form_type');

    // 5. Isolate Dynamic Fields & Package JSON Payload
    $core_keys = ['form_type', 'client_name', 'dob', 'email', 'signature', 'sign_date'];
    $dynamic_fields = array_diff_key($payload, array_flip($core_keys));

    $sanitized_dynamic = array_map('sanitize_text_field', $dynamic_fields);
    $json_encrypted_blob = json_encode($sanitized_dynamic);

    // 6. Push to Encrypted HIPAA Vault Custom Table
    $table_name = $wpdb->prefix . 'cree_consejeria_universal_intake';
    $encryption_key = SECURE_AUTH_KEY; 

    // FIX: Pass the raw payload strings straight into the escaped structure block variables array
    $db_inserted = $wpdb->query($wpdb->prepare(
        "INSERT INTO $table_name (
            wp_id, form_type, client_name, dob, email, 
            form_data_encrypted, 
            signature, signature_date
        ) VALUES (%d, %s, %s, %s, %s, AES_ENCRYPT(%s, %s), %s, %s)",
        array(
            $wp_id,
            $form_type,
            $client_name,
            $dob,
            $email,
            $json_encrypted_blob, 
            $encryption_key,
            $signature,
            $sign_date
        )
    ));

    if ($db_inserted === false) {
        wp_delete_post($wp_id, true);
        return new WP_Error('database_insertion_failed', __('Secure encryption layer injection rejected transaction.', 'cree-consejeria'));
    }

    return $wp_id;
}