<?php

/**
 * Handles incoming intake form submissions.
 * Processes the dynamic payload, hooks into the CPT, sets taxonomy, and saves encrypted DB records.
 * @param array $payload Raw array data compiled from the JavaScript fetch/AJAX request.
 * @return int|WP_Error Returns the generated WP Post ID on success, or WP_Error on failure.
 */
function cree_consejeria_handle_form_submission_v1($payload) {

    if (!is_user_logged_in()) {
        return new WP_Error('not_logged_in', __('You must be logged in to submit this form.', 'cree-consejeria'));
    }
    global $wpdb;

    $wp_id = isset($payload['wp_id']) && !empty($payload['wp_id']) ? intval($payload['wp_id']) : 0;

    $existing_id = isset($payload['wp_id']) ? intval($payload['wp_id']) : 0;
    $row_exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE wp_id = %d", $existing_id));

    if ($row_exists) {
        // UPDATE existing record
        $wpdb->update($table_name, $data_to_save, ['wp_id' => $existing_id]);
        return $existing_id; 
    } else {
        // INSERT new record
        $wpdb->insert($table_name, $data_to_save);
        return $wpdb->insert_id; // Or return your custom unique ID logic
    }
    
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
    $sign_date    = isset( $payload['sign_date'] ) ? validate_html_date( sanitize_text_field( $payload['sign_date'] ), true ) : '';
    $user_ip      = sanitize_text_field($payload['user_ip']);
    $form_status  = sanitize_text_field($payload['form_status']);

    // 3. Create the Shadow CPT Record
    $post_args = array(
        'post_title'   => sprintf('%s — %s', $client_name, date('Y-m-d')),
        'post_type'    => 'cree-client-intake',
        'post_status'  => 'private', 
        'post_author'  => get_current_user_id() ? get_current_user_id() : 1, 
    );

    if ($wp_id > 0) {
        // Update existing CPT
        wp_update_post(['ID' => $wp_id, 'post_title' => sprintf('%s — %s', $client_name, date('Y-m-d'))]);
    } else {
        // Create new CPT
        $wp_id = wp_insert_post($post_args);
    }

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
    $core_keys = ['form_type', 'client_name', 'dob', 'email', 'signature', 'sign_date', 'user_ip'];
    $dynamic_fields = array_diff_key($payload, array_flip($core_keys));

    $sanitized_dynamic = array_map('sanitize_text_field', $dynamic_fields);
    $json_encrypted_blob = json_encode($sanitized_dynamic);

    // 6. Push to Encrypted HIPAA Vault Custom Table
    $table_name = $wpdb->prefix . 'cree_consejeria_client_forms_master';
    $encryption_key = SECURE_AUTH_KEY;

    $current_user_id = get_current_user_id();

    update_post_meta($wp_id, 'intake_status', $form_status);

    $db_inserted = $wpdb->query($wpdb->prepare(
        "INSERT INTO $table_name (
            wp_id, user_id, form_type, client_name, dob, email, 
            form_data_encrypted, signature, signature_date, user_ip, status
        ) VALUES (%d, %d, %s, %s, %s, %s, AES_ENCRYPT(%s, %s), %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE
            form_type = VALUES(form_type),
            client_name = VALUES(client_name),
            dob = VALUES(dob),
            email = VALUES(email),
            form_data_encrypted = VALUES(form_data_encrypted),
            signature = VALUES(signature),
            signature_date = VALUES(signature_date),
            user_ip = VALUES(user_ip),
            status = VALUES(status)",
        array(
            $wp_id,
            $current_user_id,
            $form_type,
            $client_name,
            $dob,
            $email,
            $json_encrypted_blob,
            $encryption_key,
            $signature,
            $sign_date,
            $user_ip,
            $form_status
        )
    ));

    if ($db_inserted === false) {
        wp_delete_post($wp_id, true);
        return new WP_Error('database_insertion_failed', __('Secure encryption layer injection rejected transaction.', 'cree-consejeria'));
    }

    return $wp_id;
}

function cree_consejeria_handle_form_submission($payload) {

    if (!is_user_logged_in()) {
        return new WP_Error('not_logged_in', __('You must be logged in to submit this form.', 'cree-consejeria'));
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'cree_consejeria_client_forms_master';
    $encryption_key = SECURE_AUTH_KEY;
    $current_user_id = get_current_user_id();

    // 1. Initial Validation
    if (empty($payload['client_name']) || empty($payload['email']) || empty($payload['form_type'])) {
        return new WP_Error('missing_fields', __('Required core registration fields are missing.', 'cree-consejeria'));
    }

    // 2. Extract and Sanitize Core Fields
    $wp_id        = isset($payload['wp_id']) ? intval($payload['wp_id']) : 0;
    $form_type    = sanitize_text_field($payload['form_type']);
    $client_name  = sanitize_text_field($payload['client_name']);
    $dob          = sanitize_text_field($payload['dob']);
    $email        = sanitize_email($payload['email']);
    $signature    = sanitize_text_field($payload['signature']);
    $sign_date    = !empty($payload['sign_date']) ? sanitize_text_field($payload['sign_date']) : null;
    $user_ip      = sanitize_text_field($payload['user_ip']);
    $form_status  = sanitize_text_field($payload['form_status']);

    // 3. Create or Update the Shadow CPT Record
    $post_title = sprintf('%s — %s', $client_name, date('Y-m-d'));
    
    if ($wp_id > 0) {
        wp_update_post(['ID' => $wp_id, 'post_title' => $post_title]);
    } else {
        $wp_id = wp_insert_post([
            'post_title'   => $post_title,
            'post_type'    => 'cree-client-intake',
            'post_status'  => 'private',
            'post_author'  => $current_user_id ? $current_user_id : 1,
        ]);
    }

    if (is_wp_error($wp_id) || $wp_id === 0) {
        return new WP_Error('cpt_creation_failed', __('Failed to register backend administrative post context.', 'cree-consejeria'));
    }

    // 4. Assign Taxonomy
    $term_slug = sanitize_title($form_type);
    if (!term_exists($term_slug, 'intake_form_type')) {
        wp_insert_term(ucwords(str_replace(['_', '-'], ' ', $form_type)), 'intake_form_type', ['slug' => $term_slug]);
    }
    wp_set_object_terms($wp_id, $term_slug, 'intake_form_type');

    // 5. Package JSON Payload
    $core_keys = ['form_type', 'client_name', 'dob', 'email', 'signature', 'sign_date', 'user_ip', 'wp_id', 'form_status', 'nonce', 'action', 'form_action'];
    $dynamic_fields = array_diff_key($payload, array_flip($core_keys));
    $json_encrypted_blob = json_encode(array_map('sanitize_text_field', $dynamic_fields));

    // 6. Secure Upsert to HIPAA Vault
    // Note: We use ON DUPLICATE KEY UPDATE to handle both Insert and Update in one go
    $db_result = $wpdb->query($wpdb->prepare(
        "INSERT INTO $table_name (
            wp_id, user_id, form_type, client_name, dob, email, 
            form_data_encrypted, signature, signature_date, user_ip, status
        ) VALUES (%d, %d, %s, %s, %s, %s, AES_ENCRYPT(%s, %s), %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE
            form_type = VALUES(form_type),
            client_name = VALUES(client_name),
            dob = VALUES(dob),
            email = VALUES(email),
            form_data_encrypted = VALUES(form_data_encrypted),
            signature = VALUES(signature),
            signature_date = VALUES(signature_date),
            user_ip = VALUES(user_ip),
            status = VALUES(status)",
        $wp_id, $current_user_id, $form_type, $client_name, $dob, $email, 
        $json_encrypted_blob, $encryption_key, $signature, $sign_date, $user_ip, $form_status
    ));

    if ($db_result === false) {
        return new WP_Error('database_insertion_failed', __('Secure encryption layer injection rejected.', 'cree-consejeria'));
    }

    update_post_meta($wp_id, 'intake_status', $form_status);

    return $wp_id;
}

/**
 * Sanitize and validate HTML date input
 * 
 * @param string $date_string Date from HTML input (Y-m-d format)
 * @param bool $with_time Whether to include time (00:00:00)
 * @return string|null Validated date string or null
 */
function validate_html_date( $date_string, $with_time = false ) {
    // Sanitize
    $date_string = sanitize_text_field( $date_string );
    
    // Check if empty
    if ( empty( $date_string ) ) {
        return null;
    }
    
    // Validate format (HTML date inputs return Y-m-d)
    $date_object = DateTime::createFromFormat( 'Y-m-d', $date_string );
    if ( ! $date_object || $date_object->format( 'Y-m-d' ) !== $date_string ) {
        return null; // Invalid date
    }
    
    // Return in MySQL format
    if ( $with_time ) {
        return $date_object->format( 'Y-m-d H:i:s' );
    } else {
        return $date_object->format( 'Y-m-d' );
    }
}

/**
 * Get formatted date for display
 * 
 * @param string $date_string MySQL date string
 * @param string $format Optional custom format
 * @return string Formatted date
 */
function get_formatted_submission_date( $date_string, $format = '' ) {
    // Validate input
    if ( empty( $date_string ) ) {
        return __( 'Date not available', 'cree-consejeria-cfm' );
    }
    
    // Convert to timestamp
    $timestamp = strtotime( $date_string );
    if ( ! $timestamp || $timestamp === false ) {
        return __( 'Invalid date', 'cree-consejeria-cfm' );
    }
    
    // Use WordPress default formats if no custom format provided
    if ( empty( $format ) ) {
        $date_format = get_option( 'date_format' );
        $time_format = get_option( 'time_format' );
        $format = $date_format . ' ' . $time_format;
    }
    
    // Make format translatable with context
    $translated_format = _x( $format, 'Date format for submission display', 'cree-consejeria-cfm' );
    
    // Use wp_date for proper timezone handling
    return wp_date( $translated_format, $timestamp );
}

function get_form_term_name($form_type){

    global $wpdb;

    $term_name = null;
    $human_date = null;
    $user_id = get_current_user_id();

    $query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cree_consejeria_client_forms_master WHERE form_type = %s AND user_id = %d ORDER BY created_at DESC LIMIT 1", $form_type, $user_id );

    $latest_form = $wpdb->get_row( $query, ARRAY_A );

    if ( ! $latest_form ) {

        echo '<p>No se encontró ningún formulario de registro reciente.</p>';

        return array(
            'term_name' => null,
            'human_date' => null
        );

    }else{

        $object_id = $latest_form['wp_id'];

        $submission_date = get_formatted_submission_date( $latest_form['created_at'] );

        $query = $wpdb->prepare(
            "SELECT 
                P.ID,
                P.post_title,
                P.post_type,
                T.name AS term_name,
                TT.taxonomy
            FROM {$wpdb->term_relationships} TR
            JOIN {$wpdb->term_taxonomy} TT ON TR.term_taxonomy_id = TT.term_taxonomy_id
            JOIN {$wpdb->terms} T ON TT.term_id = T.term_id
            JOIN {$wpdb->posts} P ON TR.object_id = P.ID
            WHERE TR.object_id = %d",
            $object_id
        );

        $result = $wpdb->get_row( $query, ARRAY_A );

        $term_name = $result['term_name'];
 
    }

    return array(
            'term_name' => $term_name,
            'human_date' => $submission_date
        );
}

function get_form_wp_id_v1 ($form_type){

    global $wpdb;

    $user_id = get_current_user_id();

    echo "INSIDE FUNCTION - Form Type: {$form_type} <br>";
    echo "INSIDE FUNCTION - User ID {$user_id} <br>";

    $query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cree_consejeria_client_forms_master WHERE form_type = %s AND user_id = %d ORDER BY created_at DESC LIMIT 1", $form_type, $user_id );

    $latest_form = $wpdb->get_row( $query, ARRAY_A );

    echo "INSIDE FUNCTION - Latest Form: <br>";
    echo "<pre>";
    echo print_r($latest_form);
    echo "</pre>";

}

function get_form_wp_id($form_type) {
    global $wpdb;

    $user_id = get_current_user_id();

    // 1. Validate inputs
    if (!$user_id || empty($form_type)) {
        return null;
    }

    // 2. Query
    $table_name = $wpdb->prefix . 'cree_consejeria_client_forms_master';
    $query = $wpdb->prepare(
        "SELECT wp_id FROM $table_name WHERE form_type = %s AND user_id = %d ORDER BY created_at DESC LIMIT 1",
        $form_type,
        $user_id
    );

    $wp_id = $wpdb->get_var($query);

    return $wp_id;
}

/**
 * Retrieves the status of a form based on its WordPress post ID.
 *
 * @param int $form_wp_id The WordPress ID of the form.
 * @return string|null The status (e.g., 'draft', 'complete') or null if not found.
 */
function get_form_status($form_wp_id) {
    global $wpdb;

    // Validate input
    if (empty($form_wp_id)) {
        return null;
    }

    $table_name = $wpdb->prefix . 'cree_consejeria_client_forms_master';

    // Query for the status column specifically
    $status = $wpdb->get_var($wpdb->prepare(
        "SELECT status FROM $table_name WHERE wp_id = %d LIMIT 1",
        $form_wp_id
    ));

    return $status;
}