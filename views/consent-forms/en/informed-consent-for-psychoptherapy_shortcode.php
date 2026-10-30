<?php
/**
 * View Template: Informed Consent Form - Psychotherapy
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
    <form id="informedConsentPsychotherapyForm">
        <input type="hidden" name="form_type" value="<?php echo esc_attr( $form_type_value ); ?>">
        <input type="hidden" name="user_ip" value="<?php echo esc_attr( ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' ); ?>">

        <h1>Informed Consent for Psychotherapy</h1>
        
        <div class="therapist-badge-info" style="background: #f7f9fa; border-left: 4px solid #0056b3; padding: 15px; margin-bottom: 25px; font-size: 0.9em; color: #555;">
            <strong>Therapist:</strong> Rafael Ramírez (License LPC-A #906126)<br>
            <strong>Center:</strong> CREE Consejeria <br>
            <strong>Contact:</strong> (325) 276-2476
        </div>

        <h2>General Information</h2>
        <p>
            CREE Consejeria (CREE) is a professional Christian counseling center. Each of our therapists has diverse academic training and professional experience. Likewise, each therapist has varied Christian backgrounds and experiences. Similarly, each therapist has a distinct professional approach to integrating clinical and faith-based principles. What all CREE therapists share is the desire to be instruments of a loving God.
        </p>
        <p>
            At CREE, we firmly believe in the importance of psychological, emotional, relational, spiritual, and physical health. CREE also highly values the autonomy of each client. Therefore, we encourage you to ask your therapist about their professional and personal approach to Christian counseling if you have any concerns or questions.
        </p>
        <p>
            The therapeutic relationship is unique, as it is highly personal and, at the same time, a contractual agreement. Thus, it is important that we reach a clear understanding of how our relationship will work and what we can mutually expect. This consent will provide a clear framework for our work together. Feel free to discuss any aspect of this with me. Please read this information, confirm it, and accept the terms by checking the corresponding box at the end of this document.
        </p>

        <h2>The Therapeutic Process</h2>
        <p>
            You have taken a very positive step by deciding to seek therapy. The outcome of your treatment depends largely on your willingness to participate in this process, which can sometimes cause considerable discomfort. Remembering unpleasant events and becoming aware of the feelings associated with them can provoke strong feelings of anger, depression, anxiety, etc.
        </p>
        <p>
            Naturally, we are convinced that the benefits of psychotherapy outweigh the risks. Therapy often leads to better relationships, the resolution of specific problems, and a significant reduction in emotional distress. However, there are no guarantees about what you will experience. I cannot promise that your behavior or circumstances will change. I am committed to supporting you and doing my best to understand you and your recurring patterns, as well as helping you clarify what you want for yourself.
        </p>

        <h2>Confidentiality and Legal Limitations</h2>
        <p>
            The content of the sessions and all materials related to the client's treatment will be kept confidential, unless the client requests in writing that all or part of such content be disclosed to one or more specifically named individuals. There are limitations to this right of client confidentiality, which are detailed below:
        </p>
        <ol style="margin-bottom: 20px; padding-left: 20px; color: #444; line-height: 1.6;">
            <li>If a client threatens or attempts suicide, or acts in a way that implies a substantial risk of serious bodily injury.</li>
            <li>If a client threatens to cause serious bodily injury or death to another person.</li>
            <li>If the therapist has reasonable suspicion that a client or other identified victim is the perpetrator, witness, or actual victim of physical, emotional, or sexual abuse against minors under 18 years of age.</li>
            <li>Suspicions, as indicated above, in the case of an elderly person who may be the subject of such abuse.</li>
            <li>Suspicion of neglect towards the individuals mentioned in points 3 and 4.</li>
            <li>If a court issues a legitimate court order (subpoena) to obtain the information specified in said order.</li>
            <li>If the client is in therapy or receiving treatment by court order, or if the information is obtained for the purpose of preparing an expert report for an attorney.</li>
        </ol>
        <p>
            Occasionally, I may need to consult with other professional experts in their respective fields to provide you with the best possible treatment. In this context, information about you may be shared without revealing your name.
        </p>
        <p>
            If we happen to meet outside the therapy office, I will not greet you first. Your right to privacy and confidentiality is of utmost importance to me, and I do not wish to risk your privacy. However, if you greet me first, I will be more than willing to speak with you briefly; nevertheless, I consider it appropriate not to engage in lengthy conversations in public or outside the therapy office.
        </p>

        <h2>About the Therapist</h2>
        <p>
            Rafael Ramírez is a Licensed Professional Counselor Associate in the state of Texas supervised by Lisa Powel, LPC-S. He works in various areas, including overcoming addictions and providing care for children, individuals, couples, and families, aiming to foster divine principle-based changes in the lives of the people he counsels. Mr. Ramírez earned his Master's degree in Professional Counseling from Amridge University. He has nearly five years of practice with various clients online.
        </p>

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
                        BY CHECKING THE BOX BELOW, I CONFIRM THAT I HAVE READ AND UNDERSTOOD THE CONTENT OF THIS DOCUMENT AND THAT I ACCEPT ITS TERMS.
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