<?php
/**
 * View Template: Intake Form Layout
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 

// Ensures the form type matches the assigned database slug exactly
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
    <form id="clientRegistrationForm">
        <input type="hidden" name="form_type" value="<?php echo esc_attr( $form_type_value ); ?>">
        <h1 class="cree-bilingual-text" data-en="Client Registration Short Form" data-es="Formulario Corto de Registro de Cliente">Client Registration Short Form</h1>
        <h2 class="cree-bilingual-text" data-en="Client Information:" data-es="Información del Cliente:">Client Information:</h2>
        
        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label for="client_name" class="cree-bilingual-text" data-en="Name" data-es="Nombre">Name</label>
                <input type="text" id="client_name" name="client_name" required>
            </div>
            <div class="form-group">
                <label for="dob" class="cree-bilingual-text" data-en="Date of Birth" data-es="Fecha de Nacimiento">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label for="address" class="cree-bilingual-text" data-en="Address" data-es="Dirección">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="city" class="cree-bilingual-text" data-en="City" data-es="Ciudad">City</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group" style="flex: 0.5; min-width: 80px;">
                <label for="zip" class="cree-bilingual-text" data-en="Zip" data-es="Código Postal">Zip</label>
                <input type="text" id="zip" name="zip" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="mailing_address" class="cree-bilingual-text" data-en="Mailing Address (if different)" data-es="Dirección Postal (si es diferente)">Mailing Address (if different)</label>
                <input type="text" id="mailing_address" name="mailing_address">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="cell_phone" class="cree-bilingual-text" data-en="Cell Phone" data-es="Teléfono Celular">Cell Phone</label>
                <input type="tel" id="cell_phone" name="cell_phone">
            </div>
            <div class="form-group">
                <label for="home_phone" class="cree-bilingual-text" data-en="Home Phone" data-es="Teléfono de Casa">Home Phone</label>
                <input type="tel" id="home_phone" name="home_phone">
            </div>
            <div class="form-group">
                <label for="work_phone" class="cree-bilingual-text" data-en="Work Phone" data-es="Teléfono del Trabajo">Work Phone</label>
                <input type="tel" id="work_phone" name="work_phone">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="email" class="cree-bilingual-text" data-en="Email" data-es="Correo Electrónico">Email</label>
                <input type="email" id="email" name="email" value="<?php echo esc_attr( $user_email ); ?>" required>
            </div>
        </div>
        <div class="checkbox-group">
            <div class="checkbox-item">
                <input type="radio" id="gender_male" name="gender" value="Male">
                <label for="gender_male" class="cree-bilingual-text" data-en="Male" data-es="Hombre">Male</label>
            </div>
            <div class="checkbox-item">
                <input type="radio" id="gender_female" name="gender" value="Female">
                <label for="gender_female" class="cree-bilingual-text" data-en="Female" data-es="Mujer">Female</label>
            </div>
            <div class="checkbox-item" style="margin-left: 15px;">
                <input type="radio" id="status_married" name="marital_status" value="Married">
                <label for="status_married" class="cree-bilingual-text" data-en="Married" data-es="Casado(a)">Married</label>
            </div>
            <div class="checkbox-item">
                <input type="radio" id="status_divorced" name="marital_status" value="Divorced">
                <label for="status_divorced" class="cree-bilingual-text" data-en="Divorced" data-es="Divorciado(a)">Divorced</label>
            </div>
            <div class="checkbox-item">
                <input type="radio" id="status_single" name="marital_status" value="Single">
                <label for="status_single" class="cree-bilingual-text" data-en="Single" data-es="Soltero(a)">Single</label>
            </div>
            <div class="checkbox-item">
                <input type="radio" id="status_widowed" name="marital_status" value="Widowed">
                <label for="status_widowed" class="cree-bilingual-text" data-en="Widowed" data-es="Viudo(a)">Widowed</label>
            </div>
        </div>
        <h2 class="cree-bilingual-text" data-en="Medical Information:" data-es="Información Médica:">Medical Information:</h2>
        <div class="form-row" style="align-items: center; margin-bottom: 15px;">
            <label class="cree-bilingual-text" data-en="Is the client on medication Y/N" data-es="¿El cliente toma medicamentos? S/N" style="font-weight: bold; margin-right: 15px;">Is the client on medication Y/N</label>
            <div class="checkbox-item">
                <input type="radio" id="med_yes" name="on_medication" value="Y">
                <label for="med_yes" class="cree-bilingual-text" data-en="Y" data-es="S">Y</label>
            </div>
            <div class="checkbox-item">
                <input type="radio" id="med_no" name="on_medication" value="N">
                <label for="med_no" class="cree-bilingual-text" data-en="N" data-es="N">N</label>
            </div>
        </div>
        <div class="full-width-textarea">
            <label for="medications_list" class="cree-bilingual-text" data-en="List medications" data-es="Enumere los medicamentos">List medications</label>
            <textarea id="medications_list" name="medications_list" rows="2"></textarea>
        </div>
        <div class="full-width-textarea">
            <label for="medical_conditions" class="cree-bilingual-text" data-en="List any medical conditions we should be aware of" data-es="Enumere cualquier condición médica que debamos tener en cuenta">List any medical conditions we should be aware of</label>
            <textarea id="medical_conditions" name="medical_conditions" rows="2"></textarea>
        </div>
        <h2 class="cree-bilingual-text" data-en="Emergency Contact Information:" data-es="Información de Contacto de Emergencia:">Emergency Contact Information:</h2>
        <div class="form-row">
            <div class="form-group">
                <label for="emergency_name" class="cree-bilingual-text" data-en="Name of nearest relative not living with you" data-es="Nombre del familiar más cercano que no vive con usted">Name of nearest relative not living with you</label>
                <input type="text" id="emergency_name" name="emergency_name" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="emergency_address" class="cree-bilingual-text" data-en="Address" data-es="Dirección">Address</label>
                <input type="text" id="emergency_address" name="emergency_address">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="emergency_phone" class="cree-bilingual-text" data-en="Phone" data-es="Teléfono">Phone</label>
                <input type="tel" id="emergency_phone" name="emergency_phone" required>
            </div>
            <div class="form-group">
                <label for="emergency_relationship" class="cree-bilingual-text" data-en="Relationship" data-es="Relación / Parentesco">Relationship</label>
                <input type="text" id="emergency_relationship" name="emergency_relationship" required>
            </div>
        </div>
        <div class="signature-section">
            <div class="form-row" style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 2;">
                    <label for="signature" class="cree-bilingual-text" data-en="Signature" data-es="Firma">Signature</label>
                    <input type="text" id="signature" name="signature" class="cree-bilingual-placeholder" data-en="E-Signature" data-es="Firma Electrónica" placeholder="E-Signature" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="sign_date" class="cree-bilingual-text" data-en="Date" data-es="Fecha">Date</label>
                    <input type="date" id="sign_date" name="sign_date" required>
                </div>
            </div>
        </div>
        <div class="submit-btn-container" style="margin-top: 20px;">
            <button type="submit" class="submit-btn cree-bilingual-text" data-en="Submit Registration" data-es="Enviar Registro">Submit Registration</button>
        </div>
        <div id="form-response-message" style="margin-top:20px; font-weight:bold; text-align:center;"></div>
    </form>
</div>