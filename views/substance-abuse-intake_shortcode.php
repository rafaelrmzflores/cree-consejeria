<?php
/**
 * View Template: Substance Abuse Intake Form
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 
?>
<div class="cree-form-container">
    <form id="substanceAbuseIntakeForm">
        <input type="hidden" name="form_type" value="<?php echo esc_attr( $attributes['type'] ); ?>">

        <h1>Substance Abuse Intake Form</h1>

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
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
        </div>

        <h2>Treatment History</h2>
        <div class="form-row">
            <div class="form-group">
                <label for="mh_treatment">History of treatment from mental health professionals?</label>
                <textarea name="mh_treatment" id="mh_treatment" rows="3"></textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="mh_currently_seeing">Are you currently seeing a mental health professional?</label>
                <select name="mh_currently_seeing" id="mh_currently_seeing" style="width: 100%;">
                    <option value="">-- Please select --</option>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </div>

            <div class="form-group">
                <label for="mh_years_total">Total years receiving services?</label>
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
                <label for="mh_hospitalized">Ever hospitalized for mental health reasons?</label>
                <select name="mh_hospitalized" id="mh_hospitalized" style="width: 100%;">
                    <option value="">-- Please select --</option>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </div>
        
        </div>

        <h2>Substance Use History</h2>
<div class="substance-grid">
    <?php
    $substances = ['Cocaine', 'RX Amphetamines', 'Street Opioids', 'Methamphetamine', 'Inhalants', 'Sedatives', 'Prescription Opioids', 'Hallucinogens', 'Alcohol', 'Tobacco products'];
    $frequencies = ['Never', 'Monthly', 'Weekly', 'Daily'];

    foreach ($substances as $sub) :
        $slug = sanitize_title($sub);
    ?>
    <div class="substance-comparison-row" style="border-bottom: 1px solid #ddd; padding: 15px 0;">
        <label><strong><?php echo esc_html($sub); ?></strong></label>
        
        <div class="box-group" style="margin: 5px 0;">
            <small>Lifetime:</small>
            <?php foreach ($frequencies as $freq) : ?>
                <input type="radio" id="life_<?php echo $slug . '_' . $freq; ?>" name="life_<?php echo $slug; ?>" value="<?php echo $freq; ?>">
                <label for="life_<?php echo $slug . '_' . $freq; ?>" class="box-button"><?php echo $freq; ?></label>
            <?php endforeach; ?>
        </div>

        <div class="box-group" style="margin: 5px 0;">
            <small>Past Year:</small>
            <?php foreach ($frequencies as $freq) : ?>
                <input type="radio" id="year_<?php echo $slug . '_' . $freq; ?>" name="year_<?php echo $slug; ?>" value="<?php echo $freq; ?>">
                <label for="year_<?php echo $slug . '_' . $freq; ?>" class="box-button"><?php echo $freq; ?></label>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
        <h2>History & Social</h2>
        <div class="form-row">
                      <div class="form-group">
    <label for="drug_of_choice">Drug of choice:</label>
    <select name="drug_of_choice" id="drug_of_choice">
        <option value="">-- Please select --</option>
        <option value="Alcohol">Alcohol</option>
        <option value="Cocaine">Cocaine</option>
        <option value="Methamphetamine">Methamphetamine</option>
        <option value="Tobacco">Tobacco</option>
        <option value="Hallucinogens">Hallucinogens</option>
        <option value="Inhalants">Inhalants</option>
        <option value="Sedatives">Sedatives</option>
        <option value="Prescription Opioids">Prescription Opioids</option>
        <option value="Street Opioids">Street Opioids</option>
        <option value="RX Amphetamines">RX Amphetamines</option>
        <option value="Other">Other (Please specify)</option>
    </select>
</div>

<div class="form-group is-hidden" id="other_drug_container">
    <label for="other_drug_text">Please specify:</label>
    <input type="text" name="other_drug_text" id="other_drug_text">
</div>

            <div class="form-group">
                <label for="date_last_use">Date of last use:</label>
                <input type="date" name="date_last_use" id="date_last_use">
            </div>
        </div>
        <div class="form-row">
    <div class="form-group">
        <label for="quit_before">Have you tried to quit before?</label>
        <select name="quit_before" id="quit_before">
            <option value="">-- Please select --</option>
            <option value="No">No</option>
            <option value="Yes">Yes</option>
        </select>
    </div>
    <div class="form-group">
        <label for="longest_abstinence">Longest period of abstinence?</label>
        <input type="text" name="longest_abstinence" id="longest_abstinence" placeholder="e.g., 6 months">
    </div>
</div>
        <div class="form-row">
            <div class="form-group" style="flex: 0 0 10%;">
                <label for="children_count">How many children do you have?</label>
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
                <label for="family_impact">How has use affected relationships/children?</label>
                <textarea name="family_impact" id="family_impact" rows="2"></textarea>
            </div>
        </div>
        <div class="form-row">
            
                <div class="form-group">
                    <label for="abuse_history">Have you ever been physically or sexually abused?</label>
                    <select name="abuse_history" id="abuse_history" style="width: 100%;">
                        <option value="">-- Please select --</option>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>
            
                <div class="form-group">
                    <label for="family_chem_problem">Does anyone in your immediate family have a problem with chemicals?</label>
                    <select name="family_chem_problem" id="family_chem_problem" style="width: 100%;">
                        <option value="">-- Please select --</option>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="family_chem_complaints">Have concerned person(s) complained about your use of chemicals?</label>
                    <select name="family_chem_complaints" id="family_chem_complaints" style="width: 100%;">
                        <option value="">-- Please select --</option>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>
            
        </div>

        <div class="full-width-textarea">
            <label for="leisure_time">What do you normally do with your leisure time?</label>
            <textarea name="leisure_time" id="leisure_time" rows="3"></textarea>
        </div>

        <h2>Family & Social History</h2>


        <div class="form-row">
            
            <div class="form-group">
                <label for="interests_hobbies">What are your interests/hobbies?</label>
                <textarea name="interests_hobbies" id="interests_hobbies" rows="2"></textarea>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="close_friends">How many close friends do you have?</label>
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
                <label for="socialize_with_users">Do you socialize with people who are substance users?</label>
                <select name="socialize_with_users" id="socialize_with_users" style="width: 100%;">
                    <option value="">-- Please select --</option>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </div>

            <div class="form-group">
                <label for="percent_leisure_using">Percent of leisure time spent drinking/using?</label>
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

        <h2>Additional Information</h2>
        <div class="full-width-textarea">
            <label for="additional_history">Is there anything else about either your history or your current condition that you feel is important to mention?</label>
            <textarea name="additional_history" id="additional_history" rows="3"></textarea>
        </div>

        <div class="signature-section">
            <div class="form-row" style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 2.5;">
                    <label for="signature">Signature</label>
                    <input type="text" name="signature" id="signature" required placeholder="Type Name">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="sign_date">Date</label>
                    <input type="date" name="sign_date" id="sign_date" required>
                </div>
            </div>
        </div>

        <div class="submit-btn-container" style="margin-top: 20px;">
            <button type="submit" class="submit-btn">Submit Intake</button>
        </div>
        <div id="form-response-message" style="margin-top:20px; font-weight:bold; text-align:center;"></div>

    </form>
</div>
