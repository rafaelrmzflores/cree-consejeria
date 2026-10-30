<?php
/**
 * View Template: Informed Consent for Online Therapy (Telehealth)
 * Language: Spanish
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 

$form_type_value = 'registration_short_form'; // (or whatever the specific form slug is)
if ( isset( $attributes ) && is_array( $attributes ) && ! empty( $attributes['type'] ) ) {
    $form_type_value = $attributes['type'];
} elseif ( isset( $_POST['form_type'] ) && ! empty( $_POST['form_type'] ) ) {
    $form_type_value = sanitize_text_field( $_POST['form_type'] );
}

$current_user = wp_get_current_user();
$user_email   = ( $current_user->exists() ) ? $current_user->user_email : '';

?>
<div class="cree-form-container">
    <form id="cree-online-therapy-consent-form">
        <input type="hidden" name="form_type" value="<?php echo esc_attr( $form_type_value ); ?>">
        <input type="hidden" name="user_ip" value="<?php echo esc_attr( ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' ); ?>">

        <h1>Informed Consent for Online Therapy</h1>
        
        <div class="therapist-badge-info" style="background: #f7f9fa; border-left: 4px solid #0056b3; padding: 15px; margin-bottom: 25px; font-size: 0.9em; color: #555;">
            <strong>Therapist:</strong> Rafael Ramírez (License LPC-A #906126)<br>
            <strong>Center:</strong> CREE Consejeria <br>
            <strong>Contact:</strong> +1 (325) 276-2576
        </div>

        <h2>Telemedicine Service Terms</h2>
        <ol style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
            <li>I understand that my healthcare provider wishes me to engage in a telemedicine consultation.</li>
            <li>My healthcare provider has explained to me that the video conferencing technology used for this consultation will not be the same as a direct in-person client/provider visit, due to the fact that I will not be in the same room as my provider.</li>
            <li>I understand there are potential benefits to this technology, including easier access to care and the convenience of meeting from a location of my choosing.</li>
            <li>I understand there are potential risks associated with this technology, including interruptions, unauthorized access, and technical difficulties. I understand that either my healthcare provider or I can discontinue the telemedicine consultation or visit if we feel that the video conferencing connection is not adequate for the situation.</li>
            <li>I have had a direct conversation with my provider, during which I had the opportunity to ask questions regarding this procedure. My questions have been answered, and the risks, benefits, and any practical alternatives have been discussed with me in a language that I understand.</li>
        </ol>

        <h2>Consent for the Use of Telehealth by SimplePractice Service</h2>
        <p>
            Telehealth by SimplePractice is the technology service we will use to conduct telemedicine video conferencing appointments. It is simple to use and there are no passwords required to log in. By signing this document, I acknowledge the following:
        </p>
        <ol style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
            <li>Telehealth by SimplePractice is <strong>NOT an Emergency Service</strong> and in the event of an emergency, I will use a phone to call 911.</li>
            <li>Though my provider and I may be in direct, virtual contact through the telemedicine service, neither SimplePractice nor the Telehealth Service provides any medical or healthcare services or advice including, but not limited to, emergency or urgent medical services.</li>
            <li>The Telehealth by SimplePractice Service facilitates video conferencing and is not responsible for the delivery of any healthcare, medical advice, or care.</li>
            <li>I do not assume that my provider has access to any or all of the technical information in the Telehealth by SimplePractice Service, or that such information is current, accurate, or up-to-date. I will not rely on my healthcare provider to have this information available in the Telehealth by SimplePractice Service.</li>
            <li>To maintain confidentiality, I will not share my telehealth appointment link with anyone who is not authorized to attend the appointment.</li>
        </ol>

        <h2>Client Certification</h2>
        <p>By signing this form, I certify:</p>
        <ul style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
            <li>That I have read this form, or had it read and/or explained to me.</li>
            <li>That I fully understand its contents, including the risks and benefits of the procedure(s).</li>
            <li>That I have been given ample opportunity to ask questions and that any questions have been answered to my satisfaction.</li>
        </ul>

        <h2>Client Identification Information</h2>
        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label for="client_name">Client's Full Name</label>
                <input type="text" id="client_name" name="client_name" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo esc_attr( $user_email ); ?>" required>
            </div>
        </div>

        <h2>Declaration of Acceptance and Signature</h2>
        <div class="form-group" style="margin-bottom: 25px;">
            <div style="display: table; width: 100%;">
                <div style="display: table-cell; width: 30px; vertical-align: top; padding-top: 2px;">
                    <input type="checkbox" id="terms_acceptance" name="terms_acceptance" value="Accepted" required>
                </div>
                <div style="display: table-cell; vertical-align: top;">
                    <label for="terms_acceptance" style="font-weight: bold; color: #c00; display: inline;">
                        BY CHECKING THE BOX BELOW, I AGREE THAT I HAVE READ, UNDERSTOOD, AND AGREE TO THE ITEMS CONTAINED IN THIS DOCUMENT.
                    </label>
                </div>
            </div>
        </div>

        <div class="signature-section">
            <div class="form-row" style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 2.5;">
                    <label for="signature">Digital Signature (Type your Full Name)</label>
                    <input type="text" name="signature" id="signature" placeholder="e.g., John Doe" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="sign_date">Date of Signature</label>
                    <input type="date" name="sign_date" id="sign_date" required>
                </div>
            </div>
        </div>

        <div class="submit-btn-container" style="margin-top: 30px;">
            <button type="submit" class="submit-btn">Submit Consent</button>
        </div>
        
        <div id="form-response-message" style="margin-top: 20px; font-weight: bold; text-align: center;"></div>
    </form>
</div>