<?php
/**
 * View Template: Psychosocial Evaluation Form
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 

$functions_path = defined('CREE_CONSEJERIA_CFM_PATH') ? CREE_CONSEJERIA_CFM_PATH . 'functions/cree_consejeria_functions.php' : '';

if (!empty($functions_path) && file_exists($functions_path)) {
    require_once $functions_path;
}

$form_info = get_form_term_name($attributes['type']);

// echo "INSIDE FORM: {$form_info}<br>";
// echo "<pre>";
// echo print_r($form_info);
// echo "</pre>";

$form_wp_id = get_form_wp_id($attributes['type']);

// echo "FROM_WP_ID: {$form_wp_id} <br>";

$form_status = get_form_status($form_wp_id);

// echo "FROM_STATUS: {$form_status}";

if ($form_wp_id && $form_status === 'draft') {
    // Logic for resuming a draft
    ?>
    <div class="cree-form-container">
        <form id="clientRegistrationForm">

            <input type="hidden" name="form_type" value="<?php echo esc_attr( $attributes['type'] ); ?>">
             <input type="hidden" name="wp_id" id="wp_id" value="<?php echo esc_attr($form_wp_id); ?>">

            <h1>Psychosocial Evaluation Form <br> DRAFT</h1>

            <h2>Personal Information:</h2>

            <div class="form-row">

                <div class="form-group" style="flex: 2.5;">
                    <label for="client_name">Name</label>
                    <input type="text" id="client_name" name="client_name">
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" required>
                </div>

            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 2.5;">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" style="flex: 2;" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Age</label>
                    <input type="text" id="age" name="age" readonly>
                </div>
            </div>

            <h2>Family History</h2>
            <div class="form-row">
                <div class="form-group"><label>Biological Father's Name</label><input type="text" name="bio_father"> </div>
                <div class="form-group"><label>Biological Mother's Name</label><input type="text" name="bio_mother"></div>
            </div>

            <div class="form-row">
                <div class="form-group" >
                    <label for="age_divorce">Age at parent's divorce</label>
                    <input type="number" name="age_divorce" id="age_divorce" min="0" max="100" style="width: 47%;">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Stepfather</label><input type="text" name="stepfather"></div>
                <div class="form-group"><label>Stepmother</label><input type="text" name="stepmother"></div>
            </div>
            <div class="form-group"><label>Biological Siblings (Names/Ages)</label><textarea name="bio_siblings"></textarea></div>
            <div class="form-group"><label>Half-Siblings (Names/Ages)</label><textarea name="half_siblings"></textarea></div>
            <div class="form-group"><label>Describe Father (1 sentence)</label><input type="text" name="desc_father"></div>
            <div class="form-group"><label>Describe Mother (1 sentence)</label><input type="text" name="desc_mother"></div>
            <div class="form-group"><label>Relationship with siblings</label><textarea name="rel_siblings"></textarea></div>
            <div class="form-row">
                <div class="form-group"><label>Father's Occupation</label><input type="text" name="occ_father"></div>
                <div class="form-group"><label>Mother's Occupation</label><input type="text" name="occ_mother"></div>
            </div>
            <div class="form-group"><label>Other Household Occupations</label><textarea name="occ_household"></textarea></div>
            <div class="form-group"><label>Grandparents living? Specify:</label><input type="text" name="grandparents_living"></div>
            <div class="form-group"><label>Children (Names/Ages)</label><textarea name="children_list"></textarea></div>

            
            <h2>Education & Employment</h2>
            <div class="form-group"><label>Education (Level & Dates)</label><textarea name="edu_history"></textarea></div>
            <div class="form-group"><label>Trade Schools (Names & Dates)</label><textarea name="trade_schools"></textarea></div>
            <div class="form-group"><label>Problems in school</label><textarea name="school_problems"></textarea></div>
            <div class="form-group"><label>Extracurricular Activities</label><input type="text" name="activities"></div>
            <div class="form-group"><label>Job Training</label><input type="text" name="job_training"></div>
            <div class="form-group"><label>Employment History (Chronological)</label><textarea name="job_history"></textarea></div>
            <div class="form-row">
                <div class="form-group"><label>Like your job?</label><select name="like_job">
                    <option value="" disabled selected>-- Please select --</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="na">N/A</option>
                </select></div>

                <div class="form-group"><label>Find fulfillment?</label><select name="find_fulfillment">
                    <option value="" disabled selected>-- Please select --</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="na">N/A</option>
                </select></div>
            </div>

            <h2>Relationships</h2>
            <div class="form-group"><label>Ever married? If yes, count and order:</label><textarea name="marriage_history"></textarea></div>
            <div class="form-group"><label>Reasons for divorce/separation</label><textarea name="divorce_reasons"></textarea></div>
            <div class="form-group"><label>Close unmarried relationships (Dates/Count)</label><textarea name="unmarried_history"></textarea></div>
            <div class="form-group"><label>Awareness of problems in relationships</label><textarea name="rel_problems"></textarea></div>
            <div class="form-group"><label>Attitude problems in relationships</label><textarea name="rel_attitude"></textarea></div>

            <h2>Medical & Mental Health</h2>
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label>Last physical</label>
                    <input type="date" name="last_physical">
                </div>
                <div class="form-group" style="flex: 2.5;">
                    <label>Last surgery/Reason</label>
                    <input type="text" name="last_surgery">
                </div>
            </div>
            <div class="form-group"><label>Severe illness (If yes, when?)</label><input type="text" name="severe_illness"></div>
            <div class="form-group"><label>Serious injuries/Broken bones (If yes, when?)</label><input type="text" name="injuries"></div>
            <div class="form-group"><label>Seizures/Convulsions (Cause?)</label><input type="text" name="seizures"></div>
            <div class="form-group"><label>Pregnancy/Miscarriage/Abortion (Chronological dates)</label><textarea name="pregnancy_history"></textarea></div>
            <div class="form-row" style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 4;"><label>Special Diet</label><input type="text" name="diet"></div>
                <div class="form-group" style="flex: 1;"><label>Height</label><input type="text" name="height"></div>
                <div class="form-group" style="flex: 1;"><label>Weight</label><input type="text" name="weight"></div>
            </div>
            <div class="form-group"><label>Allergies</label><input type="text" name="allergies"></div>
            <div class="form-group"><label>Current Meds (Strengths/Dosages)</label><textarea name="meds"></textarea></div>
            <div class="form-group"><label>Mental Illness Diagnosis & Doctor</label><textarea name="mental_diagnosis"></textarea></div>
            <div class="form-group"><label>Treatment History (Facilities, Doctors, Dates, Meds, Response)</label><textarea name="treatment_history"></textarea></div>
            <div class="form-group"><label>Currently experiencing health/mental issues?</label><textarea name="current_health_issues"></textarea></div>

            <h2>Military & Legal</h2>
            <div class="form-group"><label>Military Service (Dates, Discharged?)</label><textarea name="military_service"></textarea></div>

            <div class="form-row">
                <div class="form-group"><label>Discharge Type</label><input type="text" name="military_discharge"></div>
                <div class="form-group"><label>Military Job Training</label><input type="text" name="military_training"></div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Anyone else in family in military?</label><input type="text" name="family_military"></div>
                <div class="form-group"><label>Thoughts on military?</label><input type="text" name="military_thoughts"></div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Juvenile legal issues?</label><input type="text" name="juv_legal"></div>
                
            </div>
            
            <div class="form-row">
                <div class="form-group"><label>Currently on probation?</label>
                    <select name="on_probation">
                        <option value="" disabled selected>-- Please select --</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="form-group"><label>Successfully completed past probation?</label>
                    <select name="past_probation">
                        <option value="" disabled selected>-- Please select --</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                        <option value="na">N/A</option>
                    </select>
                </div>
            </div>

            <div class="form-group"><label>Counseling/Education while incarcerated/probation?</label><textarea name="incarceration_counseling"></textarea></div>
            <div class="form-group"><label>Role of substance abuse in legal issues?</label><textarea name="legal_substance_role"></textarea></div>

            <h2>Substance Abuse History</h2>
            <div class="form-group"><label>Age first tried drugs/alcohol?</label><input type="number" name="age_first_use" style="width: 45%;" min="0" max="120"></div>
            <div class="form-group"><label>Substances used & count?</label><textarea name="substances_used"></textarea></div>
            <div class="form-group"><label>Quantity/Frequency consumed?</label><textarea name="substance_quantity"></textarea></div>
            <div class="form-group"><label>Current Frequency?</label><input type="text" name="current_freq"></div>
        
        
            <div class="form-row">
                <div class="form-group"><label>Addicted?</label><select name="is_addicted">
                    <option value="" disabled selected>-- Please select --</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select></div>
                <div class="form-group"><label>Interfere with jobs/relationships?</label><select name="substance_interference">
                    <option value="" disabled selected>-- Please select --</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="na">N/A</option>
                </select></div>
            </div>

            <div class="form-group"><label>Periods of abstinence (When?)</label><textarea name="abstinence_history"></textarea></div>
            <div class="form-group"><label>Other addictive behaviors (Food, Porn, Gaming)?</label><textarea name="other_addictions"></textarea></div>

            <div class="signature-section">
                <div class="form-row" style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 2.5;">
                        <label for="signature">Signature</label>
                        <input type="text" name="signature" id="signature" required placeholder="Type Name">
                    </div>
                    <div class="form-group">
                        <label for="sign_date">Date</label>
                        <input type="date" name="sign_date" id="sign_date" required>
                    </div>
                </div>
            </div>

            <div class="submit-btn-container form-actions" style="margin-top: 20px; border-top: 1px solid #ccc; padding-top: 15px;">
                <button type="submit" name="submit_final" class="submit-btn">Submit Evaluation</button>
                
                <button type="submit" name="submit_partial" class="save-btn">Save Evaluation</button>
            </div>
            <div id="form-response-message" style="margin-top:20px; font-weight:bold; text-align:center;"></div>
            
        </form>  
    </div>
    <?php
} else if ($form_status === 'complete') {
    // Logic for viewing a completed form
    ?>   
        <div class="cree-form-container message">
            <h1><?php echo esc_html( $form_info['term_name'] ) ?></h1>
  
            <p><?php printf( 
                esc_html__( 'Submitted on %s', 'cree-consejeria-cfm' ), 
                esc_html( $form_info['human_date'] ) ); ?></p>

        </div>
    <?php
} else {
    // Fill out a new form
    ?>
    <div class="cree-form-container">
        <form id="clientRegistrationForm">

            <input type="hidden" name="form_type" value="<?php echo esc_attr( $attributes['type'] ); ?>">
             <input type="hidden" name="wp_id" id="wp_id" value="">

            <h1>Psychosocial Evaluation Form</h1>

            <h2>Personal Information:</h2>

            <div class="form-row">

                <div class="form-group" style="flex: 2.5;">
                    <label for="client_name">Name</label>
                    <input type="text" id="client_name" name="client_name">
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" required>
                </div>

            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 2.5;">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" style="flex: 2;" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Age</label>
                    <input type="text" id="age" name="age" readonly>
                </div>
            </div>

            <h2>Family History</h2>
            <div class="form-row">
                <div class="form-group"><label>Biological Father's Name</label><input type="text" name="bio_father"> </div>
                <div class="form-group"><label>Biological Mother's Name</label><input type="text" name="bio_mother"></div>
            </div>

            <div class="form-row">
                <div class="form-group" >
                    <label for="age_divorce">Age at parent's divorce</label>
                    <input type="number" name="age_divorce" id="age_divorce" min="0" max="100" style="width: 47%;">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Stepfather</label><input type="text" name="stepfather"></div>
                <div class="form-group"><label>Stepmother</label><input type="text" name="stepmother"></div>
            </div>
            <div class="form-group"><label>Biological Siblings (Names/Ages)</label><textarea name="bio_siblings"></textarea></div>
            <div class="form-group"><label>Half-Siblings (Names/Ages)</label><textarea name="half_siblings"></textarea></div>
            <div class="form-group"><label>Describe Father (1 sentence)</label><input type="text" name="desc_father"></div>
            <div class="form-group"><label>Describe Mother (1 sentence)</label><input type="text" name="desc_mother"></div>
            <div class="form-group"><label>Relationship with siblings</label><textarea name="rel_siblings"></textarea></div>
            <div class="form-row">
                <div class="form-group"><label>Father's Occupation</label><input type="text" name="occ_father"></div>
                <div class="form-group"><label>Mother's Occupation</label><input type="text" name="occ_mother"></div>
            </div>
            <div class="form-group"><label>Other Household Occupations</label><textarea name="occ_household"></textarea></div>
            <div class="form-group"><label>Grandparents living? Specify:</label><input type="text" name="grandparents_living"></div>
            <div class="form-group"><label>Children (Names/Ages)</label><textarea name="children_list"></textarea></div>

            
            <h2>Education & Employment</h2>
            <div class="form-group"><label>Education (Level & Dates)</label><textarea name="edu_history"></textarea></div>
            <div class="form-group"><label>Trade Schools (Names & Dates)</label><textarea name="trade_schools"></textarea></div>
            <div class="form-group"><label>Problems in school</label><textarea name="school_problems"></textarea></div>
            <div class="form-group"><label>Extracurricular Activities</label><input type="text" name="activities"></div>
            <div class="form-group"><label>Job Training</label><input type="text" name="job_training"></div>
            <div class="form-group"><label>Employment History (Chronological)</label><textarea name="job_history"></textarea></div>
            <div class="form-row">
                <div class="form-group"><label>Like your job?</label><select name="like_job">
                    <option value="" disabled selected>-- Please select --</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="na">N/A</option>
                </select></div>

                <div class="form-group"><label>Find fulfillment?</label><select name="find_fulfillment">
                    <option value="" disabled selected>-- Please select --</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="na">N/A</option>
                </select></div>
            </div>

            <h2>Relationships</h2>
            <div class="form-group"><label>Ever married? If yes, count and order:</label><textarea name="marriage_history"></textarea></div>
            <div class="form-group"><label>Reasons for divorce/separation</label><textarea name="divorce_reasons"></textarea></div>
            <div class="form-group"><label>Close unmarried relationships (Dates/Count)</label><textarea name="unmarried_history"></textarea></div>
            <div class="form-group"><label>Awareness of problems in relationships</label><textarea name="rel_problems"></textarea></div>
            <div class="form-group"><label>Attitude problems in relationships</label><textarea name="rel_attitude"></textarea></div>

            <h2>Medical & Mental Health</h2>
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label>Last physical</label>
                    <input type="date" name="last_physical">
                </div>
                <div class="form-group" style="flex: 2.5;">
                    <label>Last surgery/Reason</label>
                    <input type="text" name="last_surgery">
                </div>
            </div>
            <div class="form-group"><label>Severe illness (If yes, when?)</label><input type="text" name="severe_illness"></div>
            <div class="form-group"><label>Serious injuries/Broken bones (If yes, when?)</label><input type="text" name="injuries"></div>
            <div class="form-group"><label>Seizures/Convulsions (Cause?)</label><input type="text" name="seizures"></div>
            <div class="form-group"><label>Pregnancy/Miscarriage/Abortion (Chronological dates)</label><textarea name="pregnancy_history"></textarea></div>
            <div class="form-row" style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 4;"><label>Special Diet</label><input type="text" name="diet"></div>
                <div class="form-group" style="flex: 1;"><label>Height</label><input type="text" name="height"></div>
                <div class="form-group" style="flex: 1;"><label>Weight</label><input type="text" name="weight"></div>
            </div>
            <div class="form-group"><label>Allergies</label><input type="text" name="allergies"></div>
            <div class="form-group"><label>Current Meds (Strengths/Dosages)</label><textarea name="meds"></textarea></div>
            <div class="form-group"><label>Mental Illness Diagnosis & Doctor</label><textarea name="mental_diagnosis"></textarea></div>
            <div class="form-group"><label>Treatment History (Facilities, Doctors, Dates, Meds, Response)</label><textarea name="treatment_history"></textarea></div>
            <div class="form-group"><label>Currently experiencing health/mental issues?</label><textarea name="current_health_issues"></textarea></div>

            <h2>Military & Legal</h2>
            <div class="form-group"><label>Military Service (Dates, Discharged?)</label><textarea name="military_service"></textarea></div>

            <div class="form-row">
                <div class="form-group"><label>Discharge Type</label><input type="text" name="military_discharge"></div>
                <div class="form-group"><label>Military Job Training</label><input type="text" name="military_training"></div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Anyone else in family in military?</label><input type="text" name="family_military"></div>
                <div class="form-group"><label>Thoughts on military?</label><input type="text" name="military_thoughts"></div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Juvenile legal issues?</label><input type="text" name="juv_legal"></div>
                
            </div>
            
            <div class="form-row">
                <div class="form-group"><label>Currently on probation?</label>
                    <select name="on_probation">
                        <option value="" disabled selected>-- Please select --</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="form-group"><label>Successfully completed past probation?</label>
                    <select name="past_probation">
                        <option value="" disabled selected>-- Please select --</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                        <option value="na">N/A</option>
                    </select>
                </div>
            </div>

            <div class="form-group"><label>Counseling/Education while incarcerated/probation?</label><textarea name="incarceration_counseling"></textarea></div>
            <div class="form-group"><label>Role of substance abuse in legal issues?</label><textarea name="legal_substance_role"></textarea></div>

            <h2>Substance Abuse History</h2>
            <div class="form-group"><label>Age first tried drugs/alcohol?</label><input type="number" name="age_first_use" style="width: 45%;" min="0" max="120"></div>
            <div class="form-group"><label>Substances used & count?</label><textarea name="substances_used"></textarea></div>
            <div class="form-group"><label>Quantity/Frequency consumed?</label><textarea name="substance_quantity"></textarea></div>
            <div class="form-group"><label>Current Frequency?</label><input type="text" name="current_freq"></div>
        
        
            <div class="form-row">
                <div class="form-group"><label>Addicted?</label><select name="is_addicted">
                    <option value="" disabled selected>-- Please select --</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select></div>
                <div class="form-group"><label>Interfere with jobs/relationships?</label><select name="substance_interference">
                    <option value="" disabled selected>-- Please select --</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="na">N/A</option>
                </select></div>
            </div>

            <div class="form-group"><label>Periods of abstinence (When?)</label><textarea name="abstinence_history"></textarea></div>
            <div class="form-group"><label>Other addictive behaviors (Food, Porn, Gaming)?</label><textarea name="other_addictions"></textarea></div>

            <div class="signature-section">
                <div class="form-row" style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 2.5;">
                        <label for="signature">Signature</label>
                        <input type="text" name="signature" id="signature" required placeholder="Type Name">
                    </div>
                    <div class="form-group">
                        <label for="sign_date">Date</label>
                        <input type="date" name="sign_date" id="sign_date" required>
                    </div>
                </div>
            </div>

            <div class="submit-btn-container form-actions" style="margin-top: 20px; border-top: 1px solid #ccc; padding-top: 15px;">
                <button type="submit" name="submit_final" class="submit-btn">Submit Evaluation</button>
                
                <button type="submit" name="submit_partial" class="save-btn">Save Draft</button>
            </div>
            <div id="form-response-message" style="margin-top:20px; font-weight:bold; text-align:center;"></div>
            
        </form>  
    </div>
    <?php
}