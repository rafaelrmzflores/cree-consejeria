<?php
/**
 * View Template: Authorization for Release of Information (ROI)
 * Language: Spanish
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 
?>
<div class="cree-form-container">
    <form id="releaseOfInformationForm">
        <input type="hidden" name="form_type" value="<?php echo esc_attr( $attributes['type'] ); ?>">
        <input type="hidden" name="user_ip" value="<?php echo esc_attr( ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' ); ?>">

        <h1>Autorización para la Divulgación de Información Médica / Psicológica</h1>
        
        <div class="therapist-badge-info" style="background: #f7f9fa; border-left: 4px solid #0056b3; padding: 15px; margin-bottom: 25px; font-size: 0.9em; color: #555;">
            <strong>Proveedor:</strong> Rafael Ramirez | CREE Consejeria<br>
            <strong>Centro:</strong><br>
            <strong>Contacto:</strong> (325) 219-4039
        </div>

        <h2>Información de Identificación del Cliente</h2>
        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label for="client_name">Nombre Completo del Cliente</label>
                <input type="text" id="client_name" name="client_name" required>
            </div>
            <div class="form-group">
                <label for="dob">Fecha de Nacimiento</label>
                <input type="date" id="dob" name="dob" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Teléfono de Contacto</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="relationship">Relación con el Cliente</label>
                <select id="relationship" name="relationship" required>
                    <option value="Self" selected>El mismo cliente (Titular)</option>
                    <option value="Padre/Madre">Padre / Madre</option>
                    <option value="Tutor Legal">Tutor Legal</option>
                    <option value="Representante de la Corte">Representante designado por el Tribunal</option>
                </select>
            </div>
        </div>

        <div class="info-note" style="background: #fdfefe; border: 1px solid #dcdfe3; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; font-weight: bold; color: #333;">
                Autorizo expresamente a Rafael Ramirez / CREE Consejeria a:
                <span style="color: #0056b3; text-transform: uppercase; display: block; margin-top: 5px;">Enviar y Recibir información recíproca con</span>
            </p>
            <div class="form-group" style="margin-top: 10px;">
                <label for="authorized_agencies" style="font-weight: normal;">Todas y cada una de las agencias o profesionales con permisos legales que soliciten o provean información relevante a/de este caso.</label>
                <input type="text" id="authorized_agencies" name="authorized_agencies" placeholder="Ej. Escuelas, Médicos de cabecera, u especificar agencias concretas" style="width: 100%; margin-top: 5px;">
            </div>
        </div>

        <h2>1. Naturaleza de la Información a Divulgar</h2>
        <p class="form-instruction" style="font-style: italic; color: #666; font-size: 0.9em;">Seleccione los registros que autoriza compartir:</p>
        
        <div class="checkbox-group" style="margin-bottom: 25px;">
            <div class="form-checkbox-row" style="margin-bottom: 10px;">
                <input type="checkbox" id="info_medical_history" name="info_records[]" value="Historial médico y evaluación(es)">
                <label for="info_medical_history" style="display:inline; font-weight: normal;">Historial médico y evaluación(es)</label>
            </div>
            <div class="form-checkbox-row" style="margin-bottom: 10px;">
                <input type="checkbox" id="info_mental_health" name="info_records[]" value="Evaluaciones de salud mental">
                <label for="info_mental_health" style="display:inline; font-weight: normal;">Evaluaciones de salud mental</label>
            </div>
            <div class="form-checkbox-row" style="margin-bottom: 10px;">
                <input type="checkbox" id="info_developmental" name="info_records[]" value="Historia del desarrollo y/o social">
                <label for="info_developmental" style="display:inline; font-weight: normal;">Historia del desarrollo y/o social</label>
            </div>
            <div class="form-checkbox-row" style="margin-bottom: 10px;">
                <input type="checkbox" id="info_educational" name="info_records[]" value="Registros educativos">
                <label for="info_educational" style="display:inline; font-weight: normal;">Registros educativos</label>
            </div>
            <div class="form-checkbox-row" style="margin-bottom: 10px;">
                <input type="checkbox" id="info_progress_notes" name="info_records[]" value="Notas de progreso y resumen de tratamiento o cierre">
                <label for="info_progress_notes" style="display:inline; font-weight: normal;">Notas de progreso y resumen de tratamiento o cierre</label>
            </div>
            <div class="form-checkbox-row" style="margin-bottom: 10px; background: #fff5f5; padding: 8px; border-radius: 4px;">
                <input type="checkbox" id="info_full_disclosure" name="info_records[]" value="Divulgación de registros médicos completos">
                <label for="info_full_disclosure" style="display:inline; font-weight: bold; color: #bd2130;">
                    Divulgación de mis registros médicos completos (incluidos registros relacionados con la atención de salud mental, enfermedades transmisibles, VIH o SIDA y tratamiento del abuso de alcohol o drogas).
                </label>
            </div>
        </div>

        <h2>2. Propósito o Fin de la Divulgación</h2>
        <p class="form-instruction" style="font-style: italic; color: #666; font-size: 0.9em;">Marque todos los propósitos aplicables:</p>
        
        <div class="checkbox-group" style="margin-bottom: 25px;">
            <div class="form-checkbox-row" style="margin-bottom: 10px;">
                <input type="checkbox" id="purpose_treatment_plan" name="disclosure_purposes[]" value="Planificar un tratamiento o programa adecuado">
                <label for="purpose_treatment_plan" style="display:inline; font-weight: normal;">Planificar un tratamiento o programa adecuado</label>
            </div>
            <div class="form-checkbox-row" style="margin-bottom: 10px;">
                <input type="checkbox" id="purpose_continuation" name="disclosure_purposes[]" value="Continuar con el tratamiento o programa adecuado">
                <label for="purpose_continuation" style="display:inline; font-weight: normal;">Continuar con el tratamiento o programa adecuado</label>
            </div>
            <div class="form-checkbox-row" style="margin-bottom: 10px;">
                <input type="checkbox" id="purpose_eligibility" name="disclosure_purposes[]" value="Determinar la elegibilidad para beneficios o programa">
                <label for="purpose_eligibility" style="display:inline; font-weight: normal;">Determinar la elegibilidad para beneficios o programa</label>
            </div>
            <div class="form-checkbox-row" style="margin-bottom: 10px;">
                <input type="checkbox" id="purpose_case_review" name="disclosure_purposes[]" value="Revisión de caso">
                <label for="purpose_case_review" style="display:inline; font-weight: normal;">Revisión de caso</label>
            </div>
            <div class="form-checkbox-row" style="margin-bottom: 10px;">
                <input type="checkbox" id="purpose_update_files" name="disclosure_purposes[]" value="Actualizando archivos">
                <label for="purpose_update_files" style="display:inline; font-weight: normal;">Actualización de expedientes y archivos</label>
            </div>
        </div>

        <h2>Marco Legal y Entendimiento de Privacidad</h2>
        <p>
            Entiendo que esta información puede estar protegida por el Título 42 (Código de Reglas Federales de Privacidad de Información de Salud de Identificación Individual, Partes 160 y 164) y el Título 45 (Reglas Federales de Confidencialidad de los Registros de Pacientes por Abuso de Alcohol y Drogas, Capítulo 1, Parte 2), además de las leyes estatales aplicables. Además, entiendo que la información divulgada al destinatario puede no estar protegida según estas pautas si este no es un proveedor de atención médica cubierto por las reglas estatales o federales.
        </p>
        <p>
            Entiendo que esta autorización es voluntaria y puedo revocar este consentimiento en cualquier momento mediante notificación por escrito. Salvo revocación previa, este consentimiento expira automáticamente un (1) año después de la fecha de la firma (la vigencia exacta puede variar según las normativas estatales locales). Me han informado qué información se proporcionará, su propósito y quién recibirá la información. Entiendo que tengo derecho a recibir una copia de esta autorización, así como el derecho implícito a negarme a firmar este documento.
        </p>
        <p style="background: #eef2f7; padding: 12px; border-radius: 4px; font-size: 0.9em; font-style: italic;">
            *Nota: Si usted es el tutor legal o representante designado por el tribunal para el cliente, recuerde adjuntar o proveer la documentación legal correspondiente que valide su facultad para recibir esta información de salud protegida (PHI).
        </p>

        <h2>Declaración de Aceptación y Firma</h2>
        <div class="form-group" style="margin-bottom: 25px;">
            <div style="display: table; width: 100%;">
                <div style="display: table-cell; width: 30px; vertical-align: top; padding-top: 2px;">
                    <input type="checkbox" id="terms_acceptance" name="terms_acceptance" value="Accepted" required>
                </div>
                <div style="display: table-cell; vertical-align: top;">
                    <label for="terms_acceptance" style="font-weight: bold; color: #c00; display: inline;">
                        ESTOY DE ACUERDO. AL MARCAR ESTA CASILLA CONFIRMO QUE ENTIENDO TOTALMENTE EL ALCANCE DE ESTA AUTORIZACIÓN DE DIVULGACIÓN DE INFORMACIÓN.
                    </label>
                </div>
            </div>
        </div>

        <div class="signature-section">
            <div class="form-group">
                <label for="signature">Firma Digital del Cliente o Representante</label>
                <input type="text" name="signature" id="signature" placeholder="Escriba su Nombre Completo" required>
            </div>
            <div class="form-group">
                <label for="sign_date">Fecha de la Firma</label>
                <input type="date" name="sign_date" id="sign_date" required>
            </div>
        </div>

        <div class="witness-section" style="border-top: 1px dashed #ccc; margin-top: 25px; padding-top: 20px;">
            <p style="font-size: 0.9em; color: #555; margin-bottom: 10px;">
                <strong>Sección de Testigo:</strong> Completar únicamente si el cliente no puede firmar por sí mismo por limitaciones físicas, de edad o de capacidad legal.
            </p>
            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label for="witness_signature">Firma del Testigo / Representante Adicional</label>
                    <input type="text" name="witness_signature" id="witness_signature" placeholder="Nombre completo del testigo">
                </div>
                <div class="form-group">
                    <label for="witness_date">Fecha del Testigo</label>
                    <input type="date" name="witness_date" id="witness_date">
                </div>
            </div>
        </div>

        <div class="submit-btn-container" style="margin-top: 30px;">
            <button type="submit" class="submit-btn">Enviar Autorización de Divulgación</button>
        </div>
        
        <div id="form-response-message" style="margin-top: 20px; font-weight: bold; text-align: center;"></div>
    </form>
</div>