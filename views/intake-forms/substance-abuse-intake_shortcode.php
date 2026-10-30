<?php
/**
 * View Template: Substance Abuse Intake Form
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 

// Set the correct default slug for this specific form
$form_type_value = 'substance_abuse_intake'; 
if ( isset( $attributes ) && is_array( $attributes ) && ! empty( $attributes['type'] ) ) {
    $form_type_value = $attributes['type'];
} elseif ( isset( $_POST['form_type'] ) && ! empty( $_POST['form_type'] ) ) {
    $form_type_value = sanitize_text_field( $_POST['form_type'] );
}

$current_user = wp_get_current_user();
$user_email   = ( $current_user->exists() ) ? $current_user->user_email : '';

?>
<div class="cree-form-container">
    <div class="cree-loading-overlay" id="cree-loading-overlay">
        <div class="cree-loading-spinner"></div>
        <p class="cree-bilingual-text" data-en="Processing securely..." data-es="Procesando de forma segura..." style="font-weight: bold; margin-top: 10px;">Processing securely...</p>
    </div>

    <h1 class="cree-bilingual-text" data-en="Substance Abuse Intake Form" data-es="Formulario de Admisión de Abuso de Sustancias">Substance Abuse Intake Form</h1>

    <!-- <div style="text-align: center; margin-bottom: 20px;">
        <img src="<?php echo esc_url( plugins_url( 'assets/images/cree-logo.png', dirname(dirname(__FILE__)) ) ); ?>" alt="CREE Consejería" style="max-height: 80px; width: auto;">
    </div> -->

    <form id="substanceAbuseIntakeForm">
        <input type="hidden" name="form_type" value="<?php echo esc_attr( $form_type_value ); ?>">

        <!-- STEP 1 -->
        <div class="cree-form-step active-step" data-step="1">
            <h2 class="cree-bilingual-text" data-en="Step 1 of 6: Client Information" data-es="Paso 1 de 6: Información del Cliente">Step 1 of 6: Client Information</h2>
            
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
                <div class="form-group">
                    <label for="email" class="cree-bilingual-text" data-en="Email" data-es="Correo Electrónico">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo esc_attr( $user_email ); ?>" required>
                </div>
            </div>

            <div class="submit-btn-container" style="display: flex; justify-content: flex-end;">
                <button type="button" class="submit-btn cree-next-step cree-bilingual-text" data-en="Next Step" data-es="Siguiente Paso">Next Step</button>
            </div>
        </div>

        <!-- STEP 2 -->
        <div class="cree-form-step" data-step="2">
            <h2 class="cree-bilingual-text" data-en="Step 2 of 6: Treatment History" data-es="Paso 2 de 6: Historial de Tratamiento">Step 2 of 6: Treatment History</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="mh_treatment" class="cree-bilingual-text" data-en="History of treatment from mental health professionals?" data-es="¿Historial de tratamiento con profesionales de salud mental?">History of treatment from mental health professionals?</label>
                    <textarea name="mh_treatment" id="mh_treatment" rows="3"></textarea>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="mh_currently_seeing" class="cree-bilingual-text" data-en="Are you currently seeing a mental health professional?" data-es="¿Actualmente ve a un profesional de salud mental?">Are you currently seeing a mental health professional?</label>
                    <select name="mh_currently_seeing" id="mh_currently_seeing" style="width: 100%;">
                        <option value="" class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="No" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                        <option value="Yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="mh_years_total" class="cree-bilingual-text" data-en="Total years receiving services?" data-es="¿Total de años recibiendo servicios?">Total years receiving services?</label>
                    <input 
                        type="number" 
                        name="mh_years_total" 
                        id="mh_years_total" 
                        placeholder="0"
                        min="0"
                        max="100"
                        style="width: 100%;">
                </div>
                
                <div class="form-group">
                    <label for="mh_hospitalized" class="cree-bilingual-text" data-en="Ever hospitalized for mental health reasons?" data-es="¿Alguna vez hospitalizado(a) por razones de salud mental?">Ever hospitalized for mental health reasons?</label>
                    <select name="mh_hospitalized" id="mh_hospitalized" style="width: 100%;">
                        <option value="" class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="No" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                        <option value="Yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                    </select>
                </div>
            </div>

            <div class="submit-btn-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn cree-prev-step cree-bilingual-text" data-en="Back" data-es="Atrás" style="background: #6c757d;">Back</button>
                <button type="button" class="submit-btn cree-next-step cree-bilingual-text" data-en="Next Step" data-es="Siguiente Paso">Next Step</button>
            </div>
        </div>

        <!-- STEP 3 -->
        <div class="cree-form-step" data-step="3">
            <h2 class="cree-bilingual-text" data-en="Step 3 of 6: Substance Use History" data-es="Paso 3 de 6: Historial de Uso de Sustancias">Step 3 of 6: Substance Use History</h2>
            
            <div class="substance-grid">
                <?php
                // Bilingual dictionary for the PHP loop
                $substances = [
                    'Cocaine' => 'Cocaína',
                    'RX Amphetamines' => 'Anfetaminas Recetadas',
                    'Street Opioids' => 'Opioides de Calle',
                    'Methamphetamine' => 'Metanfetamina',
                    'Inhalants' => 'Inhalantes',
                    'Sedatives' => 'Sedantes',
                    'Prescription Opioids' => 'Opioides Recetados',
                    'Hallucinogens' => 'Alucinógenos',
                    'Alcohol' => 'Alcohol',
                    'Tobacco products' => 'Productos de Tabaco'
                ];
                $frequencies = [
                    'Never' => 'Nunca',
                    'Monthly' => 'Mensualmente',
                    'Weekly' => 'Semanalmente',
                    'Daily' => 'Diariamente'
                ];

                foreach ($substances as $sub_en => $sub_es) :
                    $slug = sanitize_title($sub_en);
                ?>
                <div class="substance-comparison-row" style="border-bottom: 1px solid #ddd; padding: 15px 0;">
                    <label><strong class="cree-bilingual-text" data-en="<?php echo esc_attr($sub_en); ?>" data-es="<?php echo esc_attr($sub_es); ?>"><?php echo esc_html($sub_en); ?></strong></label>
                    
                    <div class="box-group" style="margin: 5px 0;">
                        <small class="cree-bilingual-text" data-en="Lifetime:" data-es="De por vida:">Lifetime:</small>
                        <?php foreach ($frequencies as $freq_en => $freq_es) : ?>
                            <input type="radio" id="life_<?php echo $slug . '_' . sanitize_title($freq_en); ?>" name="life_<?php echo $slug; ?>" value="<?php echo esc_attr($freq_en); ?>">
                            <label for="life_<?php echo $slug . '_' . sanitize_title($freq_en); ?>" class="box-button cree-bilingual-text" data-en="<?php echo esc_attr($freq_en); ?>" data-es="<?php echo esc_attr($freq_es); ?>"><?php echo esc_html($freq_en); ?></label>
                        <?php endforeach; ?>
                    </div>

                    <div class="box-group" style="margin: 5px 0;">
                        <small class="cree-bilingual-text" data-en="Past Year:" data-es="Último Año:">Past Year:</small>
                        <?php foreach ($frequencies as $freq_en => $freq_es) : ?>
                            <input type="radio" id="year_<?php echo $slug . '_' . sanitize_title($freq_en); ?>" name="year_<?php echo $slug; ?>" value="<?php echo esc_attr($freq_en); ?>">
                            <label for="year_<?php echo $slug . '_' . sanitize_title($freq_en); ?>" class="box-button cree-bilingual-text" data-en="<?php echo esc_attr($freq_en); ?>" data-es="<?php echo esc_attr($freq_es); ?>"><?php echo esc_html($freq_en); ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="submit-btn-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn cree-prev-step cree-bilingual-text" data-en="Back" data-es="Atrás" style="background: #6c757d;">Back</button>
                <button type="button" class="submit-btn cree-next-step cree-bilingual-text" data-en="Next Step" data-es="Siguiente Paso">Next Step</button>
            </div>
        </div>

        <!-- STEP 4 -->
        <div class="cree-form-step" data-step="4">
            <h2 class="cree-bilingual-text" data-en="Step 4 of 6: History & Social" data-es="Paso 4 de 6: Historial y Social">Step 4 of 6: History & Social</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="drug_of_choice" class="cree-bilingual-text" data-en="Drug of choice:" data-es="Droga de preferencia:">Drug of choice:</label>
                    <select name="drug_of_choice" id="drug_of_choice">
                        <option value="" class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="Alcohol" class="cree-bilingual-text" data-en="Alcohol" data-es="Alcohol">Alcohol</option>
                        <option value="Cocaine" class="cree-bilingual-text" data-en="Cocaine" data-es="Cocaína">Cocaine</option>
                        <option value="Methamphetamine" class="cree-bilingual-text" data-en="Methamphetamine" data-es="Metanfetamina">Methamphetamine</option>
                        <option value="Tobacco" class="cree-bilingual-text" data-en="Tobacco" data-es="Tabaco">Tobacco</option>
                        <option value="Hallucinogens" class="cree-bilingual-text" data-en="Hallucinogens" data-es="Alucinógenos">Hallucinogens</option>
                        <option value="Inhalants" class="cree-bilingual-text" data-en="Inhalants" data-es="Inhalantes">Inhalants</option>
                        <option value="Sedatives" class="cree-bilingual-text" data-en="Sedatives" data-es="Sedantes">Sedatives</option>
                        <option value="Prescription Opioids" class="cree-bilingual-text" data-en="Prescription Opioids" data-es="Opioides Recetados">Prescription Opioids</option>
                        <option value="Street Opioids" class="cree-bilingual-text" data-en="Street Opioids" data-es="Opioides de Calle">Street Opioids</option>
                        <option value="RX Amphetamines" class="cree-bilingual-text" data-en="RX Amphetamines" data-es="Anfetaminas Recetadas">RX Amphetamines</option>
                        <option value="Other" class="cree-bilingual-text" data-en="Other (Please specify)" data-es="Otro (Por favor especifique)">Other (Please specify)</option>
                    </select>
                </div>

                <div class="form-group is-hidden" id="other_drug_container">
                    <label for="other_drug_text" class="cree-bilingual-text" data-en="Please specify:" data-es="Por favor especifique:">Please specify:</label>
                    <input type="text" name="other_drug_text" id="other_drug_text">
                </div>

                <div class="form-group">
                    <label for="date_last_use" class="cree-bilingual-text" data-en="Date of last use:" data-es="Fecha de último uso:">Date of last use:</label>
                    <input type="date" name="date_last_use" id="date_last_use">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="quit_before" class="cree-bilingual-text" data-en="Have you tried to quit before?" data-es="¿Ha intentado dejarlo antes?">Have you tried to quit before?</label>
                    <select name="quit_before" id="quit_before">
                        <option value="" class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="No" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                        <option value="Yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="longest_abstinence" class="cree-bilingual-text" data-en="Longest period of abstinence?" data-es="¿Período más largo de abstinencia?">Longest period of abstinence?</label>
                    <input type="text" name="longest_abstinence" id="longest_abstinence" class="cree-bilingual-placeholder" data-en="e.g., 6 months" data-es="ej., 6 meses" placeholder="e.g., 6 months">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group" style="flex: 0 0 10%;">
                    <label for="children_count" class="cree-bilingual-text" data-en="How many children do you have?" data-es="¿Cuántos hijos tiene?">How many children do you have?</label>
                    <input 
                        type="number" 
                        name="children_count" 
                        id="children_count"
                        placeholder="0"
                        min="0" 
                        max="100"
                        style="width: 100%;"
                    >
                </div>
                <div class="form-group">
                    <label for="family_impact" class="cree-bilingual-text" data-en="How has use affected relationships/children?" data-es="¿Cómo ha afectado el uso a sus relaciones/hijos?">How has use affected relationships/children?</label>
                    <textarea name="family_impact" id="family_impact" rows="2"></textarea>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="abuse_history" class="cree-bilingual-text" data-en="Have you ever been physically or sexually abused?" data-es="¿Alguna vez ha sido abusado(a) física o sexualmente?">Have you ever been physically or sexually abused?</label>
                    <select name="abuse_history" id="abuse_history" style="width: 100%;">
                        <option value="" class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="No" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                        <option value="Yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                    </select>
                </div>
            
                <div class="form-group">
                    <label for="family_chem_problem" class="cree-bilingual-text" data-en="Does anyone in your immediate family have a problem with chemicals?" data-es="¿Alguien en su familia inmediata tiene problemas con sustancias químicas?">Does anyone in your immediate family have a problem with chemicals?</label>
                    <select name="family_chem_problem" id="family_chem_problem" style="width: 100%;">
                        <option value="" class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="No" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                        <option value="Yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="family_chem_complaints" class="cree-bilingual-text" data-en="Have concerned person(s) complained about your use of chemicals?" data-es="¿Personas cercanas se han quejado de su uso de sustancias químicas?">Have concerned person(s) complained about your use of chemicals?</label>
                    <select name="family_chem_complaints" id="family_chem_complaints" style="width: 100%;">
                        <option value="" class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="No" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                        <option value="Yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                    </select>
                </div>
            </div>

            <div class="full-width-textarea">
                <label for="leisure_time" class="cree-bilingual-text" data-en="What do you normally do with your leisure time?" data-es="¿Qué hace normalmente en su tiempo libre?">What do you normally do with your leisure time?</label>
                <textarea name="leisure_time" id="leisure_time" rows="3"></textarea>
            </div>

            <div class="submit-btn-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn cree-prev-step cree-bilingual-text" data-en="Back" data-es="Atrás" style="background: #6c757d;">Back</button>
                <button type="button" class="submit-btn cree-next-step cree-bilingual-text" data-en="Next Step" data-es="Siguiente Paso">Next Step</button>
            </div>
        </div>

        <!-- STEP 5 -->
        <div class="cree-form-step" data-step="5">
            <h2 class="cree-bilingual-text" data-en="Step 5 of 6: Family & Social History" data-es="Paso 5 de 6: Historial Familiar y Social">Step 5 of 6: Family & Social History</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="interests_hobbies" class="cree-bilingual-text" data-en="What are your interests/hobbies?" data-es="¿Cuáles son sus intereses/pasatiempos?">What are your interests/hobbies?</label>
                    <textarea name="interests_hobbies" id="interests_hobbies" rows="2"></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="close_friends" class="cree-bilingual-text" data-en="How many close friends do you have?" data-es="¿Cuántos amigos cercanos tiene?">How many close friends do you have?</label>
                    <input 
                        type="number" 
                        name="close_friends" 
                        id="close_friends"
                        placeholder="0"
                        min="0" 
                        max="100"
                        style="width: 100%;"
                    >
                </div>
                
                <div class="form-group">
                    <label for="socialize_with_users" class="cree-bilingual-text" data-en="Do you socialize with people who are substance users?" data-es="¿Socializa con personas que consumen sustancias?">Do you socialize with people who are substance users?</label>
                    <select name="socialize_with_users" id="socialize_with_users" style="width: 100%;">
                        <option value="" class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="No" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                        <option value="Yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="percent_leisure_using" class="cree-bilingual-text" data-en="Percent of leisure time spent drinking/using?" data-es="¿Porcentaje de tiempo libre que pasa bebiendo/consumiendo?">Percent of leisure time spent drinking/using?</label>
                    <input 
                        type="number" 
                        name="percent_leisure_using" 
                        id="percent_leisure_using" 
                        placeholder="0" 
                        min="0" 
                        max="100" 
                        step="5"
                        style="width: 100%;"
                        >
                </div>
            </div>

            <div class="submit-btn-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn cree-prev-step cree-bilingual-text" data-en="Back" data-es="Atrás" style="background: #6c757d;">Back</button>
                <button type="button" class="submit-btn cree-next-step cree-bilingual-text" data-en="Next Step" data-es="Siguiente Paso">Next Step</button>
            </div>
        </div>

        <!-- STEP 6 -->
        <div class="cree-form-step" data-step="6">
            <h2 class="cree-bilingual-text" data-en="Step 6 of 6: Additional Information" data-es="Paso 6 de 6: Información Adicional">Step 6 of 6: Additional Information</h2>
            
            <div class="full-width-textarea">
                <label for="additional_history" class="cree-bilingual-text" data-en="Is there anything else about either your history or your current condition that you feel is important to mention?" data-es="¿Hay algo más sobre su historial o condición actual que considere importante mencionar?">Is there anything else about either your history or your current condition that you feel is important to mention?</label>
                <textarea name="additional_history" id="additional_history" rows="3"></textarea>
            </div>

            <div class="signature-section">
                <div class="form-row" style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 2.5;">
                        <label for="signature" class="cree-bilingual-text" data-en="Signature" data-es="Firma">Signature</label>
                        <input type="text" name="signature" id="signature" required class="cree-bilingual-placeholder" data-en="Type Name" data-es="Escriba su Nombre" placeholder="Type Name">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="sign_date" class="cree-bilingual-text" data-en="Date" data-es="Fecha">Date</label>
                        <input type="date" name="sign_date" id="sign_date" required>
                    </div>
                </div>
            </div>

            <div class="submit-btn-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn cree-prev-step cree-bilingual-text" data-en="Back" data-es="Atrás" style="background: #6c757d;">Back</button>
                <button type="submit" class="submit-btn cree-bilingual-text" data-en="Submit Intake" data-es="Enviar Formulario">Submit Intake</button>
            </div>
        </div>

        <!-- ARIA Live region for Accessibility -->
        <div id="form-response-message" aria-live="polite" style="margin-top:20px; font-weight:bold; text-align:center;"></div>

    </form>
</div>