<?php
/**
 * View Template: Practice Policies
 * Language: Spanish
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 
?>
<div class="cree-form-container">
    <form id="practicePoliciesForm">
        <input type="hidden" name="form_type" value="<?php echo esc_attr( $attributes['type'] ); ?>">
        <input type="hidden" name="user_ip" value="<?php echo esc_attr( ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' ); ?>">

        <h1>Políticas de la Práctica</h1>
        
        <div class="therapist-badge-info" style="background: #f7f9fa; border-left: 4px solid #0056b3; padding: 15px; margin-bottom: 25px; font-size: 0.9em; color: #555;">
            <strong>Agencia:</strong> CREE Consejeria<br>
            <strong>Dirección:</strong> 1219 E Broadway St., Sweetwater, TX 79556 • Abilene Christian University Alumni<br>
            <strong>Contacto:</strong> 325-219-4039 | Rafael Ramirez (Licencia LPC-A #90612)
        </div>

        <h2>Citas y Cancelaciones</h2>
        <p>
            Nuestra agencia utiliza un servicio electrónico para notificar a los clientes sobre sus citas. Este recordatorio de cortesía se envía 24 horas antes de la cita, tanto por mensaje de texto como por correo electrónico, a menos que se indique lo contrario. No estamos obligados a enviar recordatorios para confirmar sus citas. Es responsabilidad exclusiva del cliente programar y asistir a su cita, no de la agencia. Por favor, llame a nuestra oficina si tiene alguna pregunta sobre los horarios de sus citas o para reprogramarlas.
        </p>
        <p>
            Recuerde cancelar o reprogramar su cita con <strong>24 horas de antelación</strong>. Usted será responsable de pagar la tarifa completa si la cancelación se realiza con menos de 24 horas de aviso. La duración estándar de una sesión de psicoterapia es de 50 minutos; sin embargo, usted puede determinar la duración de sus sesiones. Las solicitudes para modificar la sesión de 50 minutos deben discutirse con el terapeuta para poder programar el tiempo con antelación.
        </p>
        <p>
            Se cobrará un cargo por servicio de <strong>$45.00</strong> por cualquier cheque devuelto por cualquier motivo, debido al manejo especial requerido.
        </p>
        <p style="background: #fff5f5; color: #bd2130; padding: 10px; border-left: 4px solid #dc3545; font-weight: bold;">
            Las cancelaciones y reprogramaciones de sesiones conllevarán el cobro de la tarifa completa si NO SE NOTIFICAN CON AL MENOS 24 HORAS DE ANTELACIÓN. Esto es necesario porque se reserva un horario exclusivamente para usted. Si llega tarde a una sesión, es posible que pierda parte del tiempo de dicha sesión.
        </p>
        <p>
            A los clientes que programen su primera cita de la mañana y no cancelen con 24 horas de antelación se les cobrará la tarifa completa de la sesión. Esto también resultará en la imposibilidad de programar citas a primera hora de la mañana en el futuro.
        </p>

        <h2>Tarifas por Servicios Forenses</h2>
        <p>
            Se recomienda a los clientes no solicitar la comparecencia de su terapeuta mediante citación judicial ni requerir la entrega de expedientes con fines de litigio. Aunque usted es responsable de pagar la tarifa por el testimonio, esto no significa que mi testimonio será exclusivamente a su favor. Solo puedo testificar sobre los hechos del caso y mi opinión profesional. 
        </p>
        <p>
            Si su consejero va a recibir una citación judicial (<em>subpoena</em>), el abogado o el personal de la oficina debe llamar a su consultorio para concertar una hora para la entrega de la citación durante el horario de atención. Es necesario avisar con un mínimo de <strong>72 horas de antelación</strong> sobre cualquier comparecencia ante el tribunal para poder realizar los cambios de horario de los clientes dentro de un plazo razonable. Tenga en cuenta lo siguiente: si se recibe una citación o una notificación para reunirse con abogados sin un aviso previo mínimo de 72 horas, se aplicará un cargo adicional de <strong>$250 por trámite urgente</strong>. 
        </p>
        
        <p>La estructura de tarifas para las comparecencias ante el tribunal es la siguiente:</p>
        <ul style="margin-bottom: 20px; padding-left: 20px; line-height: 1.6;">
            <li><strong>Tiempo de preparación (incluida la presentación de registros):</strong> $200/hora (facturable en intervalos de 15 minutos)</li>
            <li><strong>Llamadas telefónicas:</strong> $200/hora (facturable en intervalos de 15 minutos)</li>
            <li><strong>Declaraciones juradas extrajudiciales (<em>depositions</em>):</strong> $250/hora</li>
            <li><strong>Tiempo transcurrido en las instalaciones del tribunal (con o sin prestar testimonio):</strong> $250/hora</li>
            <li><strong>Compensación por el traslado de ida y vuelta al tribunal:</strong> $125/hora o fracción de hora</li>
            <li>Todos los honorarios de abogados y costos en los que incurra el terapeuta como resultado de la acción legal.</li>
            <li><strong>Presentación de documentos ante el tribunal:</strong> $100</li>
            <li><strong>El cargo mínimo por una comparecencia ante el tribunal es de $1500.00.</strong> Se debe abonar un depósito inicial (<em>retainer</em>) de $1500 al menos 72 horas hábiles antes de la comparecencia programada.</li>
        </ul>
        <p>
            El resto de los costos se facturará después de la comparecencia y deberá pagarse al recibirse la factura. Si el terapeuta es citado y la fecha del caso se reprograma con menos de 72 horas hábiles de antelación respecto al inicio del día de la audiencia programada, se cobrará al cliente una tarifa de $500 (adicional al depósito inicial de $1500 por la comparecencia). Las facturas se presentan a los clientes semanalmente y se espera el pago al recibirlas. Cualquier reembolso del anticipo adeudado al cliente se enviará por correo a la dirección registrada del cliente no antes de 30 días después de la conclusión de la comparecencia ante el tribunal.
        </p>

        <h2>Disponibilidad Telefónica</h2>
        <p>
            Si necesita comunicarse conmigo entre sesiones, por favor deje un mensaje en mi buzón de voz. A menudo no estoy disponible de inmediato; sin embargo, intentaré devolverle la llamada en un plazo de 24 horas. Tenga en cuenta que las sesiones presenciales son preferibles a las sesiones telefónicas. No obstante, si usted se encuentra fuera de la ciudad, enfermo o necesita apoyo adicional, las sesiones telefónicas están disponibles. Si surge una verdadera emergencia, por favor llame al 911 o a cualquier servicio de urgencias local.
        </p>

        <h2>Redes Sociales y Telecomunicaciones</h2>
        <p>
            Debido a la importancia de su confidencialidad y a la necesidad de minimizar las relaciones duales, no acepto solicitudes de amistad o contacto de clientes actuales o anteriores en ninguna red social (Facebook, LinkedIn, etc.). Considero que agregar a clientes como amigos o contactos en estos sitios puede comprometer su confidencialidad y nuestra privacidad respectiva. También puede desdibujar los límites de nuestra relación terapéutica. Si tiene preguntas al respecto, por favor plantéelas cuando nos reunamos y podremos hablar más sobre ello.
        </p>

        <h2>Comunicación Electrónica y Riesgos de Telemedicina</h2>
        <p>
            No puedo garantizar la confidencialidad de ninguna forma de comunicación a través de medios electrónicos, incluidos los mensajes de texto. Si prefiere comunicarse por correo electrónico o mensaje de texto para asuntos relacionados con la programación o cancelaciones, lo haré. Si bien intentaré responder a los mensajes con prontitud, no puedo garantizar una respuesta inmediata y le pido que no utilice estos métodos de comunicación para tratar temas terapéuticos ni para solicitar asistencia en casos de emergencia.
        </p>
        <p>
            No se recomienda comunicarse directamente con los profesionales clínicos mediante mensajes de texto o correo electrónico no cifrado, debido a la inseguridad de estos medios y, por lo tanto, no cumplen con los más altos estándares de confidencialidad. En raras ocasiones, los clientes pueden querer proporcionar información al profesional clínico por correo electrónico, pero la política de CREE Consejeria establece que los terapeutas solo deben responder confirmando la recepción del mensaje, sin incluir información médica confidencial. 
        </p>
        <p>
            Si usted y su terapeuta deciden utilizar la tecnología de la información para parte o la totalidad de su tratamiento, debe comprender que:
        </p>
        <ol style="margin-bottom: 20px; padding-left: 20px; line-height: 1.6;">
            <li>Usted conserva la opción de no dar su consentimiento en cualquier momento, sin que esto afecte su derecho a recibir atención o tratamiento futuros ni corra el riesgo de perder o retirar los beneficios del programa a los que tendría derecho.</li>
            <li>Todas las protecciones de confidencialidad existentes son igualmente aplicables.</li>
            <li>Su acceso a toda la información médica transmitida durante una consulta de telemedicina está garantizado, y puede obtener copias de esta información por un precio razonable.</li>
            <li>No se divulgará ninguna imagen o información suya que permita identificarle, obtenida durante la interacción de telemedicina, a investigadores u otras entidades sin su consentimiento.</li>
            <li>La telemedicina presenta riesgos, consecuencias y beneficios potenciales.</li>
        </ol>
        <p>
            <strong>Riesgos específicos de la terapia virtual:</strong> Al utilizar tecnología de la información en los servicios de terapia, los riesgos potenciales incluyen, entre otros, la incapacidad del terapeuta para realizar observaciones visuales y olfativas de cuestiones clínica o terapéuticamente potencialmente relevantes, tales como: su condición física (lesiones, hematomas), marcha, coordinación motora, postura, gestos destacables, aseo e higiene básicos, contacto visual y lenguaje corporal facial. Por lo tanto, las posibles consecuencias incluyen que el terapeuta no sea consciente de lo que él o ella consideraría información importante, que usted podría no reconocer como significativa para presentar verbalmente.
        </p>

        <h2>Menores de Edad</h2>
        <p>
            Si usted es menor de edad, sus padres pueden tener derecho legal a cierta información sobre su terapia. Hablaré con usted y sus padres sobre qué información es apropiada que reciban y qué asuntos deben mantenerse confidenciales.
        </p>

        <h2>Límites de la Confidencialidad</h2>
        <p>
            La ley protege la privacidad de todas las comunicaciones entre un paciente y un profesional de la salud mental. En la mayoría de las situaciones, solo podemos divulgar información sobre su tratamiento a terceros si usted firma un formulario de autorización por escrito que cumpla con ciertos requisitos legales impuestos por HIPAA. Existen situaciones en las que se nos permite o se nos exige divulgar información sin su consentimiento ni autorización:
        </p>
        <ul style="margin-bottom: 20px; padding-left: 20px; line-height: 1.6;">
            <li><strong>Consultas Clínicas:</strong> En ocasiones consideramos útil consultar a otros profesionales sobre un caso. Hacemos todo lo posible por evitar revelar la identidad de nuestro paciente y registramos las consultas en su expediente clínico (PHI).</li>
            <li><strong>Procedimientos Judiciales:</strong> Si está involucrado en un procedimiento judicial y se solicita información relacionada con su diagnóstico, esta se encuentra protegida por el privilegio clínico-paciente. No proporcionaremos información sin autorización o una orden judicial.</li>
            <li><strong>Supervisión Sanitaria:</strong> Si una agencia gubernamental solicita la información para actividades de supervisión de la salud, podríamos estar obligados a proporcionársela.</li>
        </ul>

        <h2>Finalización del Tratamiento</h2>
        <p>
            Poner fin a las relaciones puede resultar difícil. Por ello, es importante contar con un proceso de finalización para lograr un cierre adecuado. Podré finalizar el tratamiento si determino que la psicoterapia no se está aprovechando eficazmente o si usted incumple con los pagos. No daré por terminada la relación terapéutica sin antes hablar y analizar las razones de dicha terminación. Si la terapia finaliza por cualquier motivo, le facilitaré una lista de psicoterapeutas cualificados.
        </p>
        <p style="background: #eef2f7; padding: 10px; border-left: 4px solid #6c757d; font-style: italic;">
            <strong>Regla de Inactividad:</strong> Si no programa una cita durante tres semanas consecutivas —salvo que se haya acordado lo contrario con antelación—, por razones legales y éticas, deberé considerar que la relación profesional ha quedado interrumpida de forma automática.
        </p>

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
                        AL MARCAR LA CASILLA A CONTINUACIÓN, CONFIRMO QUE HE LEÍDO Y COMPRENDIDO LOS PUNTOS CONTENIDOS EN ESTE DOCUMENTO, Y QUE ACEPTO LO EN ELLOS ESTABLECIDO.
                    </label>
                </div>
            </div>
        </div>

        <div class="signature-section">
            <div class="form-group">
                <label for="signature">Firma Digital (Escriba su Nombre Completo)</label>
                <input type="text" name="signature" id="signature" placeholder="Ej. Juan Pérez" required>
            </div>
            <div class="form-group">
                <label for="sign_date">Fecha de la Firma</label>
                <input type="date" name="sign_date" id="sign_date" required>
            </div>
        </div>

        <div class="submit-btn-container" style="margin-top: 30px;">
            <button type="submit" class="submit-btn">Aceptar y Enviar Políticas</button>
        </div>
        
        <div id="form-response-message" style="margin-top: 20px; font-weight: bold; text-align: center;"></div>
    </form>
</div>