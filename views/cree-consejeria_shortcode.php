<?php
/**
 * View Template: Intake Form Layout
 * Variable $attributes is inherited directly from the class controller file.
 */
// Safety check: block direct entry
if ( ! defined( 'ABSPATH' ) ) { exit; } 
?>
<div class="cree-form-container">
    <form id="clientRegistrationForm">
        <input type="hidden" name="form_type" value="<?php echo esc_attr( $attributes['type'] ); ?>">
        
        <h1>Client Registration Short Form</h1>

        <h2>Client Information:</h2>
        
        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label for="client_name">Name</label>
                <input type="text" id="client_name" name="client_name" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group" style="flex: 0.5; min-width: 80px;">
                <label for="zip">Zip</label>
                <input type="text" id="zip" name="zip" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="mailing_address">Mailing Address (if different)</label>
                <input type="text" id="mailing_address" name="mailing_address">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="cell_phone">Cell Phone</label>
                <input type="tel" id="cell_phone" name="cell_phone">
            </div>
            <div class="form-group">
                <label for="home_phone">Home Phone</label>
                <input type="tel" id="home_phone" name="home_phone">
            </div>
            <div class="form-group">
                <label for="work_phone">Work Phone</label>
                <input type="tel" id="work_phone" name="work_phone">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
        </div>

        <div class="checkbox-group">
            <div class="checkbox-item">
                <input type="radio" id="gender_male" name="gender" value="Male">
                <label for="gender_male">Male</label>
            </div>
            <div class="checkbox-item">
                <input type="radio" id="gender_female" name="gender" value="Female">
                <label for="gender_female">Female</label>
            </div>
            <div class="checkbox-item" style="margin-left: 15px;">
                <input type="radio" id="status_married" name="marital_status" value="Married">
                <label for="status_married">Married</label>
            </div>
            <div class="checkbox-item">
                <input type="radio" id="status_divorced" name="marital_status" value="Divorced">
                <label for="status_divorced">Divorced</label>
            </div>
            <div class="checkbox-item">
                <input type="radio" id="status_single" name="marital_status" value="Single">
                <label for="status_single">Single</label>
            </div>
            <div class="checkbox-item">
                <input type="radio" id="status_widowed" name="marital_status" value="Widowed">
                <label for="status_widowed">Widowed</label>
            </div>
        </div>

        <h2>Medical Information:</h2>
        <div class="form-row" style="align-items: center; margin-bottom: 15px;">
            <label style="font-weight: bold; margin-right: 15px;">Is the client on medication Y/N</label>
            <div class="checkbox-item">
                <input type="radio" id="med_yes" name="on_medication" value="Y">
                <label for="med_yes">Y</label>
            </div>
            <div class="checkbox-item">
                <input type="radio" id="med_no" name="on_medication" value="N">
                <label for="med_no">N</label>
            </div>
        </div>

        <div class="full-width-textarea">
            <label for="medications_list">List medications</label>
            <textarea id="medications_list" name="medications_list" rows="2"></textarea>
        </div>

        <div class="full-width-textarea">
            <label for="medical_conditions">List any medical conditions we should be aware of</label>
            <textarea id="medical_conditions" name="medical_conditions" rows="2"></textarea>
        </div>

        <h2>Emergency Contact Information:</h2>
        <div class="form-row">
            <div class="form-group">
                <label for="emergency_name">Name of nearest relative not living with you</label>
                <input type="text" id="emergency_name" name="emergency_name" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="emergency_address">Address</label>
                <input type="text" id="emergency_address" name="emergency_address">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="emergency_phone">Phone</label>
                <input type="tel" id="emergency_phone" name="emergency_phone" required>
            </div>
            <div class="form-group">
                <label for="emergency_relationship">Relationship</label>
                <input type="text" id="emergency_relationship" name="emergency_relationship" required>
            </div>
        </div>

        <div class="signature-section">
            <div class="form-group" style="flex: 2;">
                <label for="signature">Signature</label>
                <input type="text" id="signature" name="signature" placeholder="E-Signature" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label for="sign_date">Date</label>
                <input type="date" id="sign_date" name="sign_date" required>
            </div>
        </div>

        <div class="submit-btn-container">
            <button type="submit" class="submit-btn">Submit Registration</button>
        </div>
        <div id="form-response-message" style="margin-top:20px; font-weight:bold; text-align:center;"></div>
    </form>
</div>