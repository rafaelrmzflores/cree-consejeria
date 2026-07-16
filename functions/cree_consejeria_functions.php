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
    $sign_date    = isset( $payload['sign_date'] ) ? validate_html_date( sanitize_text_field( $payload['sign_date'] ), true ) : '';
    $user_ip      = sanitize_text_field($payload['user_ip']);
    $form_status = sanitize_text_field($payload ['status ']);

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
    $core_keys = ['form_type', 'client_name', 'dob', 'email', 'signature', 'sign_date', 'user_ip'];
    $dynamic_fields = array_diff_key($payload, array_flip($core_keys));

    $sanitized_dynamic = array_map('sanitize_text_field', $dynamic_fields);
    $json_encrypted_blob = json_encode($sanitized_dynamic);

    // 6. Push to Encrypted HIPAA Vault Custom Table
    $table_name = $wpdb->prefix . 'cree_consejeria_client_forms_master';
    $encryption_key = SECURE_AUTH_KEY;

    $user_id = get_current_user_id();

    // Pass the raw payload strings straight into the escaped structure block variables array
    $db_inserted = $wpdb->query($wpdb->prepare(
        "INSERT INTO $table_name (
            wp_id, user_id, form_type, client_name, dob, email, 
            form_data_encrypted, 
            signature, signature_date, user_ip
        ) VALUES (%d, %d, %s, %s, %s, %s, AES_ENCRYPT(%s, %s), %s, %s, %s)",
        array(
            $wp_id,
            $user_id,
            $form_type,
            $client_name,
            $dob,
            $email,
            $json_encrypted_blob, 
            $encryption_key,
            $signature,
            $sign_date,
            $user_ip
        )
    ));

    // $db_inserted = $wpdb->query($wpdb->prepare(
    //     "INSERT INTO $table_name (
    //         wp_id, user_id, form_type, client_name, dob, email, 
    //         form_data_encrypted, 
    //         signature, signature_date, user_ip, created_at
    //     ) VALUES (%d, %d, %s, %s, %s, %s, AES_ENCRYPT(%s, %s), %s, %s, %s, %s)",
    //     array(
    //         $wp_id,
    //         $user_id,
    //         $form_type,
    //         $client_name,
    //         $dob,
    //         $email,
    //         $json_encrypted_blob, 
    //         $encryption_key,
    //         $signature,
    //         $sign_date,
    //         $user_ip,
    //         current_time ('mysql')
    //     )
    // ));

    $db_inserted = $wpdb->query($wpdb->prepare(
        "INSERT INTO $table_name (
            wp_id, form_type, client_name, dob, email, 
            form_data_encrypted, 
            signature, signature_date, user_ip
        ) VALUES (%d, %s, %s, %s, %s, AES_ENCRYPT(%s, %s), %s, %s, %s)
        ON DUPLICATE KEY UPDATE
            form_type = VALUES(form_type),
            client_name = VALUES(client_name),
            dob = VALUES(dob),
            email = VALUES(email),
            form_data_encrypted = VALUES(form_data_encrypted),
            signature = VALUES(signature),
            signature_date = VALUES(signature_date),
            user_ip = VALUES(user_ip)",
        array(
            $wp_id,
            $form_type,
            $client_name,
            $dob,
            $email,
            $json_encrypted_blob, 
            $encryption_key,
            $signature,
            $sign_date,
            $user_ip
        )
    ));

    if ($db_inserted === false) {
        wp_delete_post($wp_id, true);
        return new WP_Error('database_insertion_failed', __('Secure encryption layer injection rejected transaction.', 'cree-consejeria'));
    }

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

        // $timestamp = strtotime( $latest_form['created_at'] );

        // $human_date_english = wp_date( 'F j Y @ g:i a', $timestamp );

        // $human_date = wp_date( 'j \d\e F \d\e Y, g:i a', $timestamp );

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