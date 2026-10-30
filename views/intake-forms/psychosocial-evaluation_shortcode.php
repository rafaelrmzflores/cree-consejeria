<?php
/**
 * View Template: Psychosocial Evaluation Form
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


echo $current_user->exists() ? '<p>Logged in as: ' . esc_html( $current_user->display_name ) . ' (' . esc_html( $user_email ) . ')</p>' : '<p>You are not logged in.</p>';
    
?>
<div class="cree-form-container">
    <div class="cree-loading-overlay" id="cree-loading-overlay">
        <div class="cree-loading-spinner"></div>
        <p class="cree-bilingual-text" data-en="Processing securely..." data-es="Procesando de forma segura..." style="font-weight: bold; margin-top: 10px;">Processing securely...</p>
    </div>

    <h1 class="cree-bilingual-text" data-en="Psychosocial Evaluation Form" data-es="Formulario de Evaluación Psicosocial">Psychosocial Evaluation Form</h1>

    <form id="clientRegistrationForm">

        <input type="hidden" name="form_type" value="<?php echo esc_attr( $form_type_value ); ?>">

        <!-- STEP 1 -->
        <div class="cree-form-step active-step" data-step="1">
            <h2 class="cree-bilingual-text" data-en="Step 1 of 7: Personal Information" data-es="Paso 1 de 7: Información Personal">Step 1 of 7: Personal Information</h2>
            <div class="form-row">
                <div class="form-group" style="flex: 2.5;">
                    <label for="client_name" class="cree-bilingual-text" data-en="Name" data-es="Nombre">Name</label>
                    <input type="text" id="client_name" name="client_name">
                </div>
                
                <div class="form-group">
                    <label for="dob" class="cree-bilingual-text" data-en="Date of Birth" data-es="Fecha de Nacimiento">Date of Birth</label>
                    <input type="date" id="dob" name="dob" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 2.5;">
                    <label for="email" class="cree-bilingual-text" data-en="Email" data-es="Correo Electrónico">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo esc_attr( $user_email ); ?>" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="cree-bilingual-text" data-en="Age" data-es="Edad">Age</label>
                    <input type="text" id="age" name="age" readonly>
                </div>
            </div>
            <div class="submit-btn-container" style="display: flex; justify-content: flex-end;">
                <button type="button" class="submit-btn cree-next-step cree-bilingual-text" data-en="Next Step" data-es="Siguiente Paso">Next Step</button>
            </div>
        </div>

         <!-- STEP 2 -->
        <div class="cree-form-step" data-step="2">
            <h2 class="cree-bilingual-text" data-en="Step 2 of 7: Family History" data-es="Paso 2 de 7: Historial Familiar">Step 2 of 7: Family History</h2>
            <div class="form-row">
                <div class="form-group"><label class="cree-bilingual-text" data-en="Biological Father's Name" data-es="Nombre del Padre Biológico">Biological Father's Name</label><input type="text" name="bio_father"></div>
                <div class="form-group"><label class="cree-bilingual-text" data-en="Biological Mother's Name" data-es="Nombre de la Madre Biológica">Biological Mother's Name</label><input type="text" name="bio_mother"></div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="age_divorce" class="cree-bilingual-text" data-en="Age at parent's divorce" data-es="Edad cuando los padres se divorciaron">Age at parent's divorce</label>
                    <input type="number" name="age_divorce" id="age_divorce" min="0" max="100" style="width: 47%;">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group"><label class="cree-bilingual-text" data-en="Stepfather" data-es="Padrastro">Stepfather</label><input type="text" name="stepfather"></div>
                <div class="form-group"><label class="cree-bilingual-text" data-en="Stepmother" data-es="Madrastra">Stepmother</label><input type="text" name="stepmother"></div>
            </div>
            
            <div class="form-group"><label class="cree-bilingual-text" data-en="Biological Siblings (Names/Ages)" data-es="Hermanos Biológicos (Nombres/Edades)">Biological Siblings (Names/Ages)</label><textarea name="bio_siblings"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Half-Siblings (Names/Ages)" data-es="Medios Hermanos (Nombres/Edades)">Half-Siblings (Names/Ages)</label><textarea name="half_siblings"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Describe Father (1 sentence)" data-es="Describa a su Padre (1 oración)">Describe Father (1 sentence)</label><input type="text" name="desc_father"></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Describe Mother (1 sentence)" data-es="Describa a su Madre (1 oración)">Describe Mother (1 sentence)</label><input type="text" name="desc_mother"></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Relationship with siblings" data-es="Relación con los hermanos">Relationship with siblings</label><textarea name="rel_siblings"></textarea></div>
            
            <div class="form-row">
                <div class="form-group"><label class="cree-bilingual-text" data-en="Father's Occupation" data-es="Ocupación del Padre">Father's Occupation</label><input type="text" name="occ_father"></div>
                <div class="form-group"><label class="cree-bilingual-text" data-en="Mother's Occupation" data-es="Ocupación de la Madre">Mother's Occupation</label><input type="text" name="occ_mother"></div>
            </div>
            
            <div class="form-group"><label class="cree-bilingual-text" data-en="Other Household Occupations" data-es="Otras Ocupaciones en el Hogar">Other Household Occupations</label><textarea name="occ_household"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Grandparents living? Specify:" data-es="¿Viven los abuelos? Especifique:">Grandparents living? Specify:</label><input type="text" name="grandparents_living"></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Children (Names/Ages)" data-es="Hijos (Nombres/Edades)">Children (Names/Ages)</label><textarea name="children_list"></textarea></div>
            
            <div class="submit-btn-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn cree-prev-step cree-bilingual-text" data-en="Back" data-es="Atrás">Back</button>
                <button type="button" class="submit-btn cree-next-step cree-bilingual-text" data-en="Next Step" data-es="Siguiente Paso">Next Step</button>
            </div>
        </div>

        <!-- STEP 3 -->
        <div class="cree-form-step" data-step="3">
            <h2 class="cree-bilingual-text" data-en="Step 3 of 7: Education and Employment" data-es="Paso 3 de 7: Educación y Empleo">Step 3 of 7: Education and Employment</h2>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Education (Level & Dates)" data-es="Educación (Nivel y Fechas)">Education (Level & Dates)</label><textarea name="edu_history"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Trade Schools (Names & Dates)" data-es="Escuelas Técnicas (Nombres y Fechas)">Trade Schools (Names & Dates)</label><textarea name="trade_schools"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Problems in school" data-es="Problemas en la escuela">Problems in school</label><textarea name="school_problems"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Extracurricular Activities" data-es="Actividades Extracurriculares">Extracurricular Activities</label><input type="text" name="activities"></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Job Training" data-es="Entrenamiento Laboral">Job Training</label><input type="text" name="job_training"></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Employment History (Chronological)" data-es="Historial de Empleo (Cronológico)">Employment History (Chronological)</label><textarea name="job_history"></textarea></div>
            
            <div class="form-row">
                <div class="form-group"><label class="cree-bilingual-text" data-en="Like your job?" data-es="¿Le gusta su trabajo?">Like your job?</label>
                    <select name="like_job">
                        <option value="" disabled selected class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                        <option value="no" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                        <option value="na" class="cree-bilingual-text" data-en="N/A" data-es="N/A">N/A</option>
                    </select>
                </div>
                <div class="form-group"><label class="cree-bilingual-text" data-en="Find fulfillment?" data-es="¿Se siente realizado(a)?">Find fulfillment?</label>
                    <select name="find_fulfillment">
                        <option value="" disabled selected class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                        <option value="no" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                        <option value="na" class="cree-bilingual-text" data-en="N/A" data-es="N/A">N/A</option>
                    </select>
                </div>
            </div>
            
            <div class="submit-btn-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn cree-prev-step cree-bilingual-text" data-en="Back" data-es="Atrás">Back</button>
                <button type="button" class="submit-btn cree-next-step cree-bilingual-text" data-en="Next Step" data-es="Siguiente Paso">Next Step</button>
            </div>
        </div>

        <!-- STEP 4 -->
        <div class="cree-form-step" data-step="4">
            <h2 class="cree-bilingual-text" data-en="Step 4 of 7: Relationships" data-es="Paso 4 de 7: Relaciones">Step 4 of 7: Relationships</h2>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Ever married? If yes, count and order:" data-es="¿Se ha casado? Si es así, indique cuántas veces y el orden:">Ever married? If yes, count and order:</label><textarea name="marriage_history"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Reasons for divorce/separation" data-es="Razones del divorcio/separación">Reasons for divorce/separation</label><textarea name="divorce_reasons"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Close unmarried relationships (Dates/Count)" data-es="Relaciones cercanas no matrimoniales (Fechas/Cantidad)">Close unmarried relationships (Dates/Count)</label><textarea name="unmarried_history"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Awareness of problems in relationships" data-es="Conciencia de problemas en sus relaciones">Awareness of problems in relationships</label><textarea name="rel_problems"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Attitude problems in relationships" data-es="Problemas de actitud en las relaciones">Attitude problems in relationships</label><textarea name="rel_attitude"></textarea></div>

            <div class="submit-btn-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn cree-prev-step cree-bilingual-text" data-en="Back" data-es="Atrás">Back</button>
                <button type="button" class="submit-btn cree-next-step cree-bilingual-text" data-en="Next Step" data-es="Siguiente Paso">Next Step</button>
            </div>
        </div>

        <!-- STEP 5 -->
        <div class="cree-form-step" data-step="5">
            <h2 class="cree-bilingual-text" data-en="Step 5 of 7: Medical and Mental Health" data-es="Paso 5 de 7: Salud Médica y Mental">Step 5 of 7: Medical and Mental Health</h2>
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label class="cree-bilingual-text" data-en="Last physical" data-es="Último examen físico">Last physical</label>
                    <input type="date" name="last_physical">
                </div>
                <div class="form-group" style="flex: 2.5;">
                    <label class="cree-bilingual-text" data-en="Last surgery/Reason" data-es="Última cirugía/Razón">Last surgery/Reason</label>
                    <input type="text" name="last_surgery">
                </div>
            </div>
            
            <div class="form-group"><label class="cree-bilingual-text" data-en="Severe illness (If yes, when?)" data-es="Enfermedad grave (Si es así, ¿cuándo?)">Severe illness (If yes, when?)</label><input type="text" name="severe_illness"></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Serious injuries/Broken bones (If yes, when?)" data-es="Lesiones graves/Huesos rotos (Si es así, ¿cuándo?)">Serious injuries/Broken bones (If yes, when?)</label><input type="text" name="injuries"></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Seizures/Convulsions (Cause?)" data-es="Convulsiones (¿Causa?)">Seizures/Convulsions (Cause?)</label><input type="text" name="seizures"></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Pregnancy/Miscarriage/Abortion (Chronological dates)" data-es="Embarazos/Abortos espontáneos/Abortos (Fechas cronológicas)">Pregnancy/Miscarriage/Abortion (Chronological dates)</label><textarea name="pregnancy_history"></textarea></div>
            
            <div class="form-row" style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 4;"><label class="cree-bilingual-text" data-en="Special Diet" data-es="Dieta Especial">Special Diet</label><input type="text" name="diet"></div>
                <div class="form-group" style="flex: 1;"><label class="cree-bilingual-text" data-en="Height" data-es="Altura">Height</label><input type="text" name="height"></div>
                <div class="form-group" style="flex: 1;"><label class="cree-bilingual-text" data-en="Weight" data-es="Peso">Weight</label><input type="text" name="weight"></div>
            </div>
            
            <div class="form-group"><label class="cree-bilingual-text" data-en="Allergies" data-es="Alergias">Allergies</label><input type="text" name="allergies"></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Current Meds (Strengths/Dosages)" data-es="Medicamentos Actuales (Dosis)">Current Meds (Strengths/Dosages)</label><textarea name="meds"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Mental Illness Diagnosis & Doctor" data-es="Diagnóstico de Enfermedad Mental y Médico">Mental Illness Diagnosis & Doctor</label><textarea name="mental_diagnosis"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Treatment History (Facilities, Doctors, Dates, Meds, Response)" data-es="Historial de Tratamiento (Instalaciones, Médicos, Fechas, Medicamentos, Respuesta)">Treatment History (Facilities, Doctors, Dates, Meds, Response)</label><textarea name="treatment_history"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Currently experiencing health/mental issues?" data-es="¿Experimenta actualmente problemas de salud/mentales?">Currently experiencing health/mental issues?</label><textarea name="current_health_issues"></textarea></div>
            
            <div class="submit-btn-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn cree-prev-step cree-bilingual-text" data-en="Back" data-es="Atrás">Back</button>
                <button type="button" class="submit-btn cree-next-step cree-bilingual-text" data-en="Next Step" data-es="Siguiente Paso">Next Step</button>
            </div>
        </div>

        <!-- STEP 6 -->
        <div class="cree-form-step" data-step="6">
            <h2 class="cree-bilingual-text" data-en="Step 6 of 7: Legal and Military Information" data-es="Paso 6 de 7: Información Legal y Militar">Step 6 of 7: Legal and Military Information</h2>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Military Service (Dates, Discharged?)" data-es="Servicio Militar (Fechas, ¿Dado de baja?)">Military Service (Dates, Discharged?)</label><textarea name="military_service"></textarea></div>

            <div class="form-row">
                <div class="form-group"><label class="cree-bilingual-text" data-en="Discharge Type" data-es="Tipo de Baja">Discharge Type</label><input type="text" name="military_discharge"></div>
                <div class="form-group"><label class="cree-bilingual-text" data-en="Military Job Training" data-es="Entrenamiento Laboral Militar">Military Job Training</label><input type="text" name="military_training"></div>
            </div>

            <div class="form-row">
                <div class="form-group"><label class="cree-bilingual-text" data-en="Anyone else in family in military?" data-es="¿Alguien más de la familia en el ejército?">Anyone else in family in military?</label><input type="text" name="family_military"></div>
                <div class="form-group"><label class="cree-bilingual-text" data-en="Thoughts on military?" data-es="¿Pensamientos sobre el ejército?">Thoughts on military?</label><input type="text" name="military_thoughts"></div>
            </div>

            <div class="form-row">
                <div class="form-group"><label class="cree-bilingual-text" data-en="Juvenile legal issues?" data-es="¿Problemas legales juveniles?">Juvenile legal issues?</label><input type="text" name="juv_legal"></div>
            </div>
            
            <div class="form-row">
                <div class="form-group"><label class="cree-bilingual-text" data-en="Currently on probation?" data-es="¿Actualmente en libertad condicional?">Currently on probation?</label>
                    <select name="on_probation">
                        <option value="" disabled selected class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                        <option value="no" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                    </select>
                </div>
                <div class="form-group"><label class="cree-bilingual-text" data-en="Successfully completed past probation?" data-es="¿Completó con éxito una libertad condicional pasada?">Successfully completed past probation?</label>
                    <select name="past_probation">
                        <option value="" disabled selected class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                        <option value="no" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                        <option value="na" class="cree-bilingual-text" data-en="N/A" data-es="N/A">N/A</option>
                    </select>
                </div>
            </div>

            <div class="form-group"><label class="cree-bilingual-text" data-en="Counseling/Education while incarcerated/probation?" data-es="¿Consejería/Educación mientras estuvo encarcelado/en libertad condicional?">Counseling/Education while incarcerated/probation?</label><textarea name="incarceration_counseling"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Role of substance abuse in legal issues?" data-es="¿El papel del abuso de sustancias en problemas legales?">Role of substance abuse in legal issues?</label><textarea name="legal_substance_role"></textarea></div>
            
            <div class="submit-btn-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn cree-prev-step cree-bilingual-text" data-en="Back" data-es="Atrás">Back</button>
                <button type="button" class="submit-btn cree-next-step cree-bilingual-text" data-en="Next Step" data-es="Siguiente Paso">Next Step</button>
            </div>
        </div>

        <!-- STEP 7 -->
        <div class="cree-form-step" data-step="7">
            <h2 class="cree-bilingual-text" data-en="Step 7 of 7: Substance Use History" data-es="Paso 7 de 7: Historia de Uso de Sustancias">Step 7 of 7: Substance Use History</h2>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Age first tried drugs/alcohol?" data-es="¿A qué edad probó drogas/alcohol por primera vez?">Age first tried drugs/alcohol?</label><input type="number" name="age_first_use" style="width: 45%;" min="0" max="120"></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Substances used & count?" data-es="¿Sustancias utilizadas y cantidad?">Substances used & count?</label><textarea name="substances_used"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Quantity/Frequency consumed?" data-es="¿Cantidad/Frecuencia de consumo?">Quantity/Frequency consumed?</label><textarea name="substance_quantity"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Current Frequency?" data-es="¿Frecuencia Actual?">Current Frequency?</label><input type="text" name="current_freq"></div>
        
            <div class="form-row">
                <div class="form-group"><label class="cree-bilingual-text" data-en="Addicted?" data-es="¿Adicto(a)?">Addicted?</label>
                    <select name="is_addicted">
                        <option value="" disabled selected class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                        <option value="no" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                    </select>
                </div>
                <div class="form-group"><label class="cree-bilingual-text" data-en="Interfere with jobs/relationships?" data-es="¿Interfiere con trabajos/relaciones?">Interfere with jobs/relationships?</label>
                    <select name="substance_interference">
                        <option value="" disabled selected class="cree-bilingual-text" data-en="-- Please select --" data-es="-- Por favor seleccione --">-- Please select --</option>
                        <option value="yes" class="cree-bilingual-text" data-en="Yes" data-es="Sí">Yes</option>
                        <option value="no" class="cree-bilingual-text" data-en="No" data-es="No">No</option>
                        <option value="na" class="cree-bilingual-text" data-en="N/A" data-es="N/A">N/A</option>
                    </select>
                </div>
            </div>

            <div class="form-group"><label class="cree-bilingual-text" data-en="Periods of abstinence (When?)" data-es="Períodos de abstinencia (¿Cuándo?)">Periods of abstinence (When?)</label><textarea name="abstinence_history"></textarea></div>
            <div class="form-group"><label class="cree-bilingual-text" data-en="Other addictive behaviors (Food, Porn, Gaming)?" data-es="¿Otros comportamientos adictivos (Comida, Pornografía, Videojuegos)?">Other addictive behaviors (Food, Porn, Gaming)?</label><textarea name="other_addictions"></textarea></div>

            <div class="signature-section">
                <div class="form-row" style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 2.5;">
                        <label for="signature" class="cree-bilingual-text" data-en="Signature" data-es="Firma">Signature</label>
                        <input type="text" name="signature" id="signature" required class="cree-bilingual-placeholder" data-en="Type Name" data-es="Escriba su Nombre" placeholder="Type Name">
                    </div>
                    <div class="form-group">
                        <label for="sign_date" class="cree-bilingual-text" data-en="Date" data-es="Fecha">Date</label>
                        <input type="date" name="sign_date" id="sign_date" required>
                    </div>
                </div>
            </div>

            <div class="submit-btn-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn cree-prev-step cree-bilingual-text" data-en="Back" data-es="Atrás">Back</button>
                <button type="submit" class="submit-btn cree-bilingual-text" data-en="Submit Form" data-es="Enviar Formulario">Submit Form</button>
            </div>
        </div>

        <!-- ARIA Live region for Accessibility -->
        <div id="form-response-message" aria-live="polite" style="margin-top:20px; font-weight:bold; text-align:center;"></div>
    </form>  
</div>