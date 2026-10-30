<?php
/**
 * View Template: Practice Policies
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
    <form id="practicePoliciesForm">
        <input type="hidden" name="form_type" value="<?php echo esc_attr( $form_type_value ); ?>">
        <input type="hidden" name="user_ip" value="<?php echo esc_attr( ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' ); ?>">

        <h1>Practice Policies</h1>
        
        <div class="therapist-badge-info" style="background: #f7f9fa; border-left: 4px solid #0056b3; padding: 15px; margin-bottom: 25px; font-size: 0.9em; color: #555;">
            <strong>Agency:</strong> CREE Consejeria<br>
            <strong>Address:</strong> Maxwell, TX 78656<br>
            <strong>Contact:</strong> +1 325-276-2476 | Rafael Ramírez (License LPC-A #90612)
        </div>

        <h2>Appointments and Cancellations</h2>
        <p>
            Our agency utilizes an electronic service to notify clients of their appointments. This courtesy reminder is sent 24 hours prior to the appointment via both text message and email, unless otherwise instructed. We are not obligated to send reminders to confirm your appointments. It is solely the client's responsibility to schedule and attend their appointment, not the agency's. Please call our office if you have any questions regarding your appointment times or need to reschedule.
        </p>
        <p>
            Please remember to cancel or reschedule your appointment with <strong>24 hours advance notice</strong>. You will be responsible for paying the full fee if the cancellation is made with less than 24 hours' notice. The standard duration of a psychotherapy session is 50 minutes; however, you may determine the length of your sessions. Requests to modify the 50-minute session must be discussed with the therapist so the time can be scheduled in advance.
        </p>
        <p>
            A service charge of <strong>$45.00</strong> will be applied for any returned checks for any reason, due to the special handling required.
        </p>
        <p style="background: #fff5f5; color: #bd2130; padding: 10px; border-left: 4px solid #dc3545; font-weight: bold;">
            Session cancellations and rescheduling will incur the full fee if NOT NOTIFIED WITH AT LEAST 24 HOURS ADVANCE NOTICE. This is necessary because a time slot is reserved exclusively for you. If you are late for a session, you may lose part of that session's time.
        </p>
        <p>
            Clients who schedule the first morning appointment and do not cancel with 24 hours' notice will be charged the full session fee. This will also result in the inability to schedule early morning appointments in the future.
        </p>

        <h2>Fees for Forensic Services</h2>
        <p>
            Clients are advised not to request their therapist's appearance via subpoena nor require the release of records for litigation purposes. Although you are responsible for paying the fee for testimony, this does not mean my testimony will be exclusively in your favor. I can only testify to the facts of the case and my professional opinion. 
        </p>
        <p>
            If your counselor is to receive a subpoena, the attorney or office staff must call their office to arrange a time for the delivery of the subpoena during business hours. A minimum of <strong>72 hours advance notice</strong> is required for any court appearance so that client schedule changes can be made within a reasonable timeframe. Please note: if a subpoena or notice to meet with attorneys is received without a minimum of 72 hours prior notice, an additional rush fee of <strong>$250</strong> will be applied. 
        </p>
        
        <p>The fee structure for court appearances is as follows:</p>
        <ul style="margin-bottom: 20px; padding-left: 20px; line-height: 1.6;">
            <li><strong>Preparation time (including records submission):</strong> $200/hour (billable in 15-minute increments)</li>
            <li><strong>Phone calls:</strong> $200/hour (billable in 15-minute increments)</li>
            <li><strong>Depositions:</strong> $250/hour</li>
            <li><strong>Time spent at the courthouse (whether testifying or not):</strong> $250/hour</li>
            <li><strong>Travel time to and from the court:</strong> $125/hour or fraction thereof</li>
            <li>All attorney fees and costs incurred by the therapist as a result of the legal action.</li>
            <li><strong>Filing documents with the court:</strong> $100</li>
            <li><strong>The minimum charge for a court appearance is $1500.00.</strong> A retainer of $1500 must be paid at least 72 business hours prior to the scheduled appearance.</li>
        </ul>
        <p>
            The remainder of the costs will be billed after the appearance and must be paid upon receipt of the invoice. If the therapist is subpoenaed and the case date is rescheduled with less than 72 business hours' notice prior to the start of the scheduled hearing day, the client will be charged a fee of $500 (in addition to the initial $1500 retainer for the appearance). Invoices are presented to clients weekly and payment is expected upon receipt. Any retainer refund owed to the client will be mailed to the client's address on file no earlier than 30 days after the conclusion of the court appearance.
        </p>

        <h2>Telephone Availability</h2>
        <p>
            If you need to contact me between sessions, please leave a message on my voicemail. I am often not immediately available; however, I will attempt to return your call within 24 hours. Please note that in-person sessions are preferable to phone sessions. However, if you are out of town, ill, or require additional support, phone sessions are available. If a true emergency arises, please call 911 or any local emergency service.
        </p>

        <h2>Social Media and Telecommunications</h2>
        <p>
            Due to the importance of your confidentiality and the need to minimize dual relationships, I do not accept friend or contact requests from current or former clients on any social network (Facebook, LinkedIn, etc.). I believe that adding clients as friends or contacts on these sites can compromise your confidentiality and our respective privacy. It can also blur the boundaries of our therapeutic relationship. If you have questions about this, please bring them up when we meet and we can discuss it further.
        </p>

        <h2>Electronic Communication and Telemedicine Risks</h2>
        <p>
            I cannot guarantee the confidentiality of any form of communication via electronic media, including text messages. If you prefer to communicate by email or text message for matters regarding scheduling or cancellations, I will do so. While I will try to respond to messages promptly, I cannot guarantee an immediate response and ask that you do not use these communication methods to discuss therapeutic issues or to request assistance in emergencies.
        </p>
        <p>
            Direct communication with clinical professionals via text messages or unencrypted email is not recommended due to the insecurity of these mediums and, therefore, they do not meet the highest standards of confidentiality. Rarely, clients may want to provide information to the clinical professional via email, but CREE Consejeria policy states that therapists should only respond confirming receipt of the message, without including confidential medical information. 
        </p>
        <p>
            If you and your therapist decide to use information technology for some or all of your treatment, you must understand that:
        </p>
        <ol style="margin-bottom: 20px; padding-left: 20px; line-height: 1.6;">
            <li>You retain the option to withhold your consent at any time, without affecting your right to future care or treatment or risking the loss or withdrawal of any program benefits to which you would otherwise be entitled.</li>
            <li>All existing confidentiality protections apply equally.</li>
            <li>Your access to all medical information transmitted during a telemedicine consultation is guaranteed, and you may obtain copies of this information for a reasonable fee.</li>
            <li>No images or identifiable information about you obtained during the telemedicine interaction will be disclosed to researchers or other entities without your consent.</li>
            <li>Telemedicine presents potential risks, consequences, and benefits.</li>
        </ol>
        <p>
            <strong>Risks specific to virtual therapy:</strong> When using information technology in therapy services, potential risks include, but are not limited to, the therapist's inability to make visual and olfactory observations of potentially clinically or therapeutically relevant issues, such as: your physical condition (injuries, bruises), gait, motor coordination, posture, notable gestures, basic grooming and hygiene, eye contact, and facial body language. Therefore, possible consequences include the therapist being unaware of what they would consider important information, which you might not recognize as significant to present verbally.
        </p>

        <h2>Minors</h2>
        <p>
            If you are a minor, your parents may be legally entitled to certain information about your therapy. I will discuss with you and your parents what information is appropriate for them to receive and what matters should be kept confidential.
        </p>

        <h2>Limits of Confidentiality</h2>
        <p>
            The law protects the privacy of all communications between a patient and a mental health professional. In most situations, we can only release information about your treatment to third parties if you sign a written authorization form that meets certain legal requirements imposed by HIPAA. There are situations where we are permitted or required to disclose information without your consent or authorization:
        </p>
        <ul style="margin-bottom: 20px; padding-left: 20px; line-height: 1.6;">
            <li><strong>Clinical Consultations:</strong> Occasionally, we find it helpful to consult other professionals about a case. We make every effort to avoid revealing the identity of our patient and we record the consultations in their clinical file (PHI).</li>
            <li><strong>Judicial Proceedings:</strong> If you are involved in a legal proceeding and information related to your diagnosis is requested, it is protected by the clinician-patient privilege. We will not provide information without authorization or a court order.</li>
            <li><strong>Health Oversight:</strong> If a government agency requests the information for health oversight activities, we may be required to provide it.</li>
        </ul>

        <h2>Termination of Treatment</h2>
        <p>
            Ending relationships can be difficult. Therefore, it is important to have a termination process to achieve proper closure. I may terminate treatment if I determine that psychotherapy is not being used effectively or if you default on payments. I will not terminate the therapeutic relationship without first discussing and analyzing the reasons for such termination. If therapy ends for any reason, I will provide you with a list of qualified psychotherapists.
        </p>
        <p style="background: #eef2f7; padding: 10px; border-left: 4px solid #6c757d; font-style: italic;">
            <strong>Inactivity Rule:</strong> If you do not schedule an appointment for three consecutive weeks—unless otherwise agreed upon in advance—I must, for legal and ethical reasons, consider the professional relationship to be automatically discontinued.
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
                        BY CHECKING THE BOX BELOW, I CONFIRM THAT I HAVE READ AND UNDERSTOOD THE ITEMS CONTAINED IN THIS DOCUMENT, AND THAT I AGREE TO WHAT IS ESTABLISHED THEREIN.
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
            <button type="submit" class="submit-btn">Submit Policies</button>
        </div>
        
        <div id="form-response-message" style="margin-top: 20px; font-weight: bold; text-align: center;"></div>
    </form>
</div>