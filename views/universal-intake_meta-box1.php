<?php

// Fetch the form type term
$terms = wp_get_post_terms($post->ID, 'intake_form_type');
$form_type = !empty($terms) ? esc_html($terms[0]->name) : __('Unknown', 'cree-consejeria');

// Fetch the encrypted form data from the custom table
global $wpdb;
$table_name = $wpdb->universal_intake;
$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE wp_id = %d", $post->ID));

if ($row) {
    // Decrypt the form data
    $encryption_key = SECURE_AUTH_KEY; // Managed securely by WordPress core environment salts
    $decrypted_data_json = openssl_decrypt($row->form_data_encrypted, 'AES-256-CBC', $encryption_key);
    $decrypted_data = json_decode($decrypted_data_json, true);
} else {
    $decrypted_data = [];
}

echo '<p><strong>' . __('Form Type:', 'cree-consejeria') . '</strong> ' . esc_html($form_type) . '</p>';
echo '<p><strong>' . __('Client Name:', 'cree-consejeria') . '</strong> ' . esc_html($row->client_name ?? '') . '</p>';
echo '<p><strong>' . __('Date of Birth:', 'cree-consejeria') . '</strong> ' . esc_html($row->dob ?? '') . '</p>';
echo '<p><strong>' . __('Email:', 'cree-consejeria') . '</strong> ' . esc_html($row->email ?? '') . '</p>';
echo '<p><strong>' . __('Signature Date:', 'cree-consejeria') . '</strong> ' . esc_html($row->signature_date ?? '') . '</p>';

if (!empty($decrypted_data)) {
    echo '<h4>' . __('Form Responses:', 'cree-consejeria') . '</h4>';
    echo '<ul>';
    foreach ($decrypted_data as $key => $value) {
        echo '<li><strong>' . esc_html(ucwords(str_replace('_', ' ', $key))) . ':</strong> ' . esc_html($value) . '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>' . __('No additional form responses found.', 'cree-consejeria') . '</p>';
}