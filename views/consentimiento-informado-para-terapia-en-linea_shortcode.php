<?php
/**
 * View Template: Informed Consent for Online Therapy (Telehealth)
 * Language: Spanish
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 

$functions_path = defined('CREE_CONSEJERIA_CFM_PATH') ? CREE_CONSEJERIA_CFM_PATH . 'functions/cree_consejeria_functions.php' : '';

if (!empty($functions_path) && file_exists($functions_path)) {
    require_once $functions_path;
}

$form_info = get_form_term_name($attributes['type']);

if ( $form_info && isset( $form_info['term_name'] ) )  {

    ?>   
        <div class="cree-form-container message">
            <h1><?php echo esc_html( $form_info['term_name'] ) ?></h1>
  
            <p><?php printf( 
                esc_html__( 'Submitted on %s', 'cree-consejeria-cfm' ), 
                esc_html( $form_info['human_date'] ) ); ?></p>

        </div>
    <?php
    
} else {

    ?>
    <div class="cree-form-container">
        <form id="onlineTherapyConsentForm">
            <input type="hidden" name="form_type" value="<?php echo esc_attr( $attributes['type'] ); ?>">
            <input type="hidden" name="user_ip" value="<?php echo esc_attr( ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' ); ?>">

            <h1>Consentimiento Informado para Terapia en Línea</h1>
            
            <div class="therapist-badge-info" style="background: #f7f9fa; border-left: 4px solid #0056b3; padding: 15px; margin-bottom: 25px; font-size: 0.9em; color: #555;">
                <strong>Terapeuta:</strong> Rafael Ramírez (Licencia LPC-A #906126)<br>
                <strong>Centro:</strong> CREE Consejeria <br>
                <strong>Contacto:</strong> (325) 219-4039
            </div>

            <h2>Términos del Servicio de Telemedicina</h2>
            <ol style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
                <li>Entiendo que mi proveedor de atención médica desea que participe en una consulta de telemedicina.</li>
                <li>Mi proveedor de atención médica me explicó que la tecnología de videoconferencia que se utilizará para dicha consulta no será igual a una visita presencial entre cliente y proveedor, debido a que no estaré en la misma habitación que mi proveedor.</li>
                <li>Entiendo que una consulta de telemedicina conlleva beneficios potenciales, como un acceso más fácil a la atención y la comodidad de reunirme desde un lugar de mi elección.</li>
                <li>Entiendo que existen riesgos potenciales asociados a esta tecnología, incluidas interrupciones, acceso no autorizado y dificultades técnicas. Entiendo que tanto mi proveedor de atención médica como yo podemos interrumpir la consulta o visita de telemedicina si consideramos que la conexión de videoconferencia no es adecuada para la situación.</li>
                <li>He mantenido una conversación directa con mi proveedor, durante la cual tuve la oportunidad de hacer preguntas sobre este procedimiento. Mis preguntas han sido respondidas y se han discutido conmigo los riesgos, beneficios y cualquier alternativa práctica en un idioma que comprendo.</li>
            </ol>

            <h2>Consentimiento para el Uso del Servicio Telehealth by SimplePractice</h2>
            <p>
                Telehealth by SimplePractice es el servicio tecnológico que utilizaremos para llevar a cabo citas de videoconferencia de telemedicina. Es fácil de usar y no requiere contraseñas para iniciar sesión. Al firmar este documento, reconozco lo siguiente:
            </p>
            <ol style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
                <li>Telehealth by SimplePractice <strong>NO es un servicio de emergencia</strong> y, en caso de emergencia, utilizaré el teléfono para llamar al 911.</li>
                <li>Aunque mi proveedor y yo podamos estar en contacto virtual directo a través del servicio de telemedicina, ni SimplePractice ni dicho servicio proporcionan servicios o asesoramiento médico o de atención sanitaria, incluidos, entre otros, servicios médicos de emergencia o de urgencia.</li>
                <li>El servicio Telehealth by SimplePractice facilita la videoconferencia y no es responsable de la prestación de atención sanitaria, asesoramiento médico o cuidados de salud.</li>
                <li>No asumo que mi proveedor tenga acceso a parte o a toda la información técnica del servicio Telehealth by SimplePractice, ni que dicha información esté vigente, sea precisa o esté actualizada. No confiaré en que mi proveedor de atención médica disponga de esta información en el servicio Telehealth by SimplePractice.</li>
                <li>Para mantener la confidencialidad, no compartiré el enlace de mi cita de telesalud con ninguna persona no autorizada para asistir a la misma.</li>
            </ol>

            <h2>Certificación del Cliente</h2>
            <p>Al firmar este formulario, certifico:</p>
            <ul style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
                <li>Que he leído este formulario, o que se me ha leído y/o explicado.</li>
                <li>Que comprendo plenamente su contenido, incluidos los riesgos y beneficios del procedimiento o los procedimientos.</li>
                <li>Que he tenido amplia oportunidad de hacer preguntas y que estas han sido respondidas a mi entera satisfacción.</li>
            </ul>

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
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>

            <h2>Declaración de Aceptación y Firma</h2>
            <div class="form-group" style="margin-bottom: 25px;">
                <div style="display: table; width: 100%;">
                    <div style="display: table-cell; width: 30px; vertical-align: top; padding-top: 2px;">
                        <input type="checkbox" id="terms_acceptance" name="terms_acceptance" value="Accepted" required>
                    </div>
                    <div style="display: table-cell; vertical-align: top;">
                        <label for="terms_acceptance" style="font-weight: bold; color: #c00; display: inline;">
                            AL MARCAR LA CASILLA DE VERIFICACIÓN A CONTINUACIÓN, ACEPTO QUE HE LEÍDO Y COMPRENDIDO LOS PUNTOS CONTENIDOS EN ESTE DOCUMENTO Y QUE ESTOY DE ACUERDO CON ELLOS.
                        </label>
                    </div>
                </div>
            </div>

            <div class="signature-section">
                <div class="form-row" style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 2.5;">
                        <label for="signature">Firma Digital (Escriba su Nombre Completo)</label>
                        <input type="text" name="signature" id="signature" placeholder="Ej. Juan Pérez" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="sign_date">Fecha de la Firma</label>
                        <input type="date" name="sign_date" id="sign_date" required>
                    </div>
                </div>
            </div>

            <div class="submit-btn-container" style="margin-top: 30px;">
                <button type="submit" class="submit-btn">Enviar Consentimiento</button>
            </div>
            
            <div id="form-response-message" style="margin-top: 20px; font-weight: bold; text-align: center;"></div>
        </form>
    </div>
    <?php
}