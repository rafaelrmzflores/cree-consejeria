<?php
/**
 * View Template: Informed Consent for Online Therapy (Telehealth)
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
    <form id="cree-online-therapy-consent-form">
        <input type="hidden" name="form_type" value="<?php echo esc_attr( $form_type_value ); ?>">
        <input type="hidden" name="user_ip" value="<?php echo esc_attr( ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' ); ?>">

        <h1>Consentimiento Informado para Terapia en Línea</h1>
        
        <div class="therapist-badge-info" style="background: #f7f9fa; border-left: 4px solid #0056b3; padding: 15px; margin-bottom: 25px; font-size: 0.9em; color: #555;">
            <strong>Terapeuta:</strong> Rafael Ramírez (Licencia LPC-A #906126)<br>
            <strong>Centro:</strong> CREE Consejeria <br>
            <strong>Contacto:</strong> +1 (325) 2276-2476
        </div>

        <h2>Términos del Servicio de Telemedicina</h2>
        <ol style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
            <li>Entiendo que mi proveedor de atención médica desea que participe en una consulta de telemedicina.</li>
            <li>Mi proveedor de atención médica me explicó que la tecnología de videoconferencia que se utilizará para dicha consulta no será igual a una visita presencial entre cliente y proveedor, debido a que no estaré en la misma habitación que mi proveedor.</li>
            <li>Entiendo que una consulta de telemedicina conlleva beneficios potenciales, como un acceso más fácil a la atención y la comodidad de reunirme desde un lugar de mi elección.</li>
            <li>Entiendo que existen riesgos potenciales asociados a esta tecnología, incluidas interrupciones, acceso no autorizado y dificultades técnicas. Entiendo que tanto mi proveedor de atención médica como yo podemos interrumpir la consulta o visita de telemedicina si consideramos que la conexión de videoconferencia no es adecuada para la situación.</li>
            <li>He mantenido una conversación directa con mi proveedor, durante la cual tuve la oportunidad de hacer preguntas sobre este procedimiento. Mis preguntas han sido respondidas y se han discutido conmigo los riesgos, beneficios y cualquier alternativa práctica en un idioma que comprendo.</li>
        </ol>

        <!-- <h2>Consentimiento para el Uso del Servicio Telehealth by SimplePractice</h2>
        <p>
            Telehealth by SimplePractice es el servicio tecnológico que utilizaremos para llevar a cabo citas de videoconferencia de telemedicina. Es fácil de usar y no requiere contraseñas para iniciar sesión. Al firmar este documento, reconozco lo siguiente:
        </p>
        <ol style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
            <li>Telehealth by SimplePractice <strong>NO es un servicio de emergencia</strong> y, en caso de emergencia, utilizaré el teléfono para llamar al 911.</li>
            <li>Aunque mi proveedor y yo podamos estar en contacto virtual directo a través del servicio de telemedicina, ni SimplePractice ni dicho servicio proporcionan servicios o asesoramiento médico o de atención sanitaria, incluidos, entre otros, servicios médicos de emergencia o de urgencia.</li>
            <li>El servicio Telehealth by SimplePractice facilita la videoconferencia y no es responsable de la prestación de atención sanitaria, asesoramiento médico o cuidados de salud.</li>
            <li>No asumo que mi proveedor tenga acceso a parte o a toda la información técnica del servicio Telehealth by SimplePractice, ni que dicha información esté vigente, sea precisa o esté actualizada. No confiaré en que mi proveedor de atención médica disponga de esta información en el servicio Telehealth by SimplePractice.</li>
            <li>Para mantener la confidencialidad, no compartiré el enlace de mi cita de telesalud con ninguna persona no autorizada para asistir a la misma.</li>
        </ol> -->

        <h2>Consentimiento para el Uso de WhatsApp para Sesiones en Línea</h2>
        <p>
            WhatsApp es la plataforma que utilizaremos para llevar a cabo nuestras citas de videoconferencia de teleterapia. Elegimos este servicio por su accesibilidad y porque cuenta con cifrado de extremo a extremo para proteger su privacidad. Al firmar este documento, reconozco lo siguiente:
        </p>
        <ol style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
            <li>WhatsApp <strong>NO es un servicio de emergencia</strong> y, en caso de emergencia, utilizaré mi línea telefónica regular para llamar al 911 o acudiré a la sala de urgencias más cercana.</li>
            <li>Entiendo que las videollamadas de WhatsApp están protegidas con <strong>cifrado de extremo a extremo</strong>. Esto significa que la conexión es privada y segura; solo mi proveedor y yo podemos ver o escuchar lo que ocurre durante la sesión, y terceros (incluido Meta/WhatsApp) no pueden interceptar el contenido.</li>
            <li>Reconozco que el uso de cualquier tecnología de terceros conlleva riesgos inherentes. Aunque la comunicación está encriptada por la aplicación, WhatsApp no es un proveedor de atención médica ni es responsable de la prestación de servicios de salud.</li>
            <li>Dado que utilizaré mi cuenta personal, es mi responsabilidad proteger mi propio teléfono o dispositivo móvil mediante el uso de contraseñas, huella dactilar o reconocimiento facial para evitar que personas no autorizadas accedan a mi cuenta.</li>
            <li>Para mantener la confidencialidad clínica, me comprometo a realizar mis videollamadas desde un entorno privado y cerrado donde otras personas no puedan escuchar la conversación, y no grabaré ni permitiré que terceros asistan a la sesión sin autorización previa.</li>
        </ol>

        <!-- <h2>Consentimiento para el Uso de Google Meet para Sesiones en Línea</h2>
        <p>
            Google Meet es la plataforma que utilizaremos para llevar a cabo nuestras citas de videoconferencia. Elegimos este servicio por su facilidad de acceso y sus altos estándares de seguridad. Al firmar este documento, reconozco lo siguiente:
        </p>
        <ol style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
            <li>Google Meet <strong>NO es un servicio de emergencia</strong> y, en caso de emergencia, utilizaré mi línea telefónica regular para llamar al 911 o acudiré a la sala de urgencias más cercana.</li>
            <li>Entiendo que las sesiones a través de Google Meet están protegidas con <strong>cifrado de datos en tránsito</strong>. Esto protege la privacidad de la sesión para que personas ajenas no puedan interceptar la comunicación en la red.</li>
            <li>Reconozco que Google proporciona únicamente la infraestructura tecnológica. No es un proveedor de atención médica ni asume responsabilidad por la prestación de servicios de salud.</li>
            <li>Es mi responsabilidad proteger el acceso a mi cuenta de correo, así como mi computadora o dispositivo móvil, para evitar que personas no autorizadas accedan a mi información o al enlace de la sesión.</li>
            <li>Para mantener la confidencialidad clínica, me comprometo a realizar mis videollamadas desde un entorno privado, no compartiré el enlace de la reunión con terceros y no grabaré la sesión sin autorización previa.</li>
        </ol> -->

        <!-- <h2>Consentimiento para el Uso de Doxy.me para Sesiones en Línea</h2>
        <p>
            Doxy.me es una plataforma diseñada específicamente para la telemedicina. Elegimos este servicio porque cumple con estrictas leyes de privacidad en salud (como HIPAA en EE. UU.) y no requiere que usted descargue aplicaciones ni cree cuentas. Al firmar este documento, reconozco lo siguiente:
        </p>
        <ol style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
            <li>Doxy.me <strong>NO es un servicio de emergencia</strong> y, en caso de emergencia, utilizaré mi línea telefónica regular para llamar al 911 o acudiré a la sala de urgencias más cercana.</li>
            <li>Entiendo que Doxy.me utiliza <strong>cifrado de grado médico</strong> y comunicaciones punto a punto. La plataforma es completamente privada y no graba ni almacena información médica personal de nuestras sesiones de video.</li>
            <li>Reconozco que Doxy.me es únicamente una herramienta tecnológica para facilitar la videoconferencia y no es responsable de la prestación de atención sanitaria o asesoramiento médico.</li>
            <li>Aunque la plataforma es segura, es mi responsabilidad asegurar que mi conexión a internet (Wi-Fi) sea segura y proteger mi dispositivo para evitar accesos no autorizados durante la consulta.</li>
            <li>Para mantener la confidencialidad, no compartiré el enlace de mi "sala de espera virtual" con ninguna persona y me aseguraré de estar en un espacio cerrado y privado durante la duración de la cita.</li>
        </ol> -->

        <!-- <h2>Consentimiento para el Uso de Zoom para Sesiones en Línea</h2>
        <p>
            Zoom es el servicio tecnológico que utilizaremos para llevar a cabo nuestras citas de teleterapia. Elegimos esta plataforma por su estabilidad de conexión y sus funciones de seguridad integradas. Al firmar este documento, reconozco lo siguiente:
        </p>
        <ol style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
            <li>Zoom <strong>NO es un servicio de emergencia</strong> y, en caso de emergencia, utilizaré mi línea telefónica regular para llamar al 911 o acudiré a la sala de urgencias más cercana.</li>
            <li>Entiendo que nuestras sesiones de Zoom están protegidas mediante <strong>cifrado avanzado</strong>, y que utilizaremos medidas de seguridad adicionales como "salas de espera" y contraseñas para garantizar que solo las personas autorizadas ingresen a la reunión.</li>
            <li>Reconozco que Zoom Video Communications, Inc. facilita la plataforma tecnológica, pero no proporciona asesoramiento, servicios médicos ni de salud mental.</li>
            <li>Es mi responsabilidad no compartir el enlace de la reunión, el ID de la sesión ni el código de acceso con ninguna persona no autorizada, así como mantener seguro mi propio dispositivo y red de internet.</li>
            <li>Para proteger mi privacidad clínica, me comprometo a conectarme desde un entorno privado donde no pueda ser escuchado, y acepto que está estrictamente prohibido grabar la sesión por cualquiera de las partes sin el consentimiento explícito de ambos.</li>
        </ol> -->

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
                <input type="email" id="email" name="email" value="<?php echo esc_attr( $user_email ); ?>" required>
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