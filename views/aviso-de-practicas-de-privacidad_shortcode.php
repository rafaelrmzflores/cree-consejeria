<?php
/**
 * View Template: Notice of Privacy Practices (HIPAA)
 * Language: Spanish
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 
?>
<div class="cree-form-container">
    <form id="privacyPracicesNoticeForm">
        <input type="hidden" name="form_type" value="<?php echo esc_attr( $attributes['type'] ); ?>">
        <input type="hidden" name="user_ip" value="<?php echo esc_attr( ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' ); ?>">

        <h1>Aviso de Prácticas de Privacidad</h1>
        <p style="text-transform: uppercase; font-weight: bold; font-size: 0.95em; color: #333; background: #fff8e1; border-left: 4px solid #ffb300; padding: 12px; margin-bottom: 25px;">
            Este aviso describe cómo se puede utilizar y divulgar la información de salud y cómo usted puede acceder a esta información. Por favor, léalo detenidamente.
        </p>
        
        <div class="therapist-badge-info" style="background: #f7f9fa; border-left: 4px solid #0056b3; padding: 15px; margin-bottom: 25px; font-size: 0.9em; color: #555;">
            <strong>Terapeuta:</strong> Rafael Ramírez (Licencia LPC-A #906126)<br>
            <strong>Centro:</strong> CREE Consejeria <br>
            <strong>Contacto:</strong> (325) 219-4039
        </div>

        <h2>I. Mi Compromiso Respecto a la Información de Salud</h2>
        <p>
            Entiendo que la información de salud sobre usted y su atención médica es personal. Me comprometo a proteger la información de salud que le concierne. Elaboro un registro de la atención y los servicios que usted recibe de mi parte. Necesito este registro para brindarle una atención de calidad y para cumplir con ciertos requisitos legales. Este aviso se aplica a todos los registros de su atención generados por este consultorio de salud mental. 
        </p>
        <p>
            Este aviso le informará sobre las formas en que puedo utilizar y divulgar su información de salud. También describo sus derechos respecto a la información de salud que conservo sobre usted y detallo ciertas obligaciones que tengo en cuanto al uso y la divulgación de dicha información. Por ley, estoy obligado a:
        </p>
        <ul style="margin-bottom: 20px; padding-left: 20px; line-height: 1.6;">
            <li>Asegurarse de que la información de salud protegida ("PHI", por sus siglas en inglés) que le identifica se mantenga privada.</li>
            <li>Entregarle este aviso sobre mis obligaciones legales y prácticas de privacidad con respecto a la información de salud.</li>
            <li>Cumplir con los términos del aviso que esté vigente en ese momento.</li>
            <li>Puedo modificar los términos de este Aviso, y dichos cambios se aplicarán a toda la información que posea sobre usted. El nuevo Aviso estará disponible previa solicitud, en mi consultorio y en mi sitio web.</li>
        </ul>

        <h2>II. Cómo Puedo Utilizar y Divulgar su Información de Salud</h2>
        <p>
            Las siguientes categorías describen las diferentes formas en que utilizo y divulgo la información de salud. Para cada categoría de usos o divulgaciones, explicaré a qué me refiero e intentaré ofrecer algunos ejemplos. No se enumerarán todos los usos o divulgaciones posibles dentro de una categoría; sin embargo, todas las formas en que tengo permitido utilizar y divulgar información se incluirán en una de estas categorías.
        </p>
        <p>
            <strong>Para fines de tratamiento, pago u operaciones de atención médica:</strong> Las normas federales de privacidad (reglamentos) permiten a los proveedores de atención médica que tienen una relación directa de tratamiento con el paciente/cliente utilizar o divulgar la información de salud personal del paciente/cliente sin su autorización por escrito, con el fin de llevar a cabo sus propias actividades de tratamiento, pago u operaciones de atención médica. También puedo divulgar su información de salud protegida para las actividades de tratamiento de cualquier proveedor de atención médica. Esto también puede hacerse sin su autorización por escrito. 
        </p>
        <p>
            Por ejemplo, si un profesional clínico consultara a otro proveedor de atención médica autorizado sobre su afección, estaríamos autorizados a utilizar y divulgar su información de salud personal —que de otro modo sería confidencial— para ayudar al profesional en el diagnóstico y tratamiento de su afección de salud mental. Las divulgaciones con fines de tratamiento no están sujetas a la norma de "mínimo necesario", ya que los terapeutas y otros proveedores de atención médica necesitan acceso al expediente completo y/o a información íntegra y detallada para brindar una atención de calidad. El término "tratamiento" incluye, entre otras cosas, la coordinación y gestión de la atención médica con terceros, las consultas entre proveedores de atención médica y la derivación de un paciente de un proveedor de atención médica a otro.
        </p>
        <p>
            <strong>Demandas y disputas:</strong> Si usted está involucrado en una demanda, puedo divulgar información de salud en respuesta a una orden judicial o administrativa. También puedo divulgar información de salud sobre su hijo en respuesta a una citación, una solicitud de exhibición de pruebas (<em>discovery</em>) u otros procesos legales iniciados por otra parte involucrada en la disputa, pero solo si se han realizado esfuerzos para informarle sobre la solicitud u obtener una orden que proteja la información requerida.
        </p>

        <h2>III. Ciertos Usos y Divulgaciones Requieren su Autorización</h2>
        <p>
            <strong>1. Notas de psicoterapia:</strong> Llevo "notas de psicoterapia", tal como se define ese término en el reglamento 45 CFR § 164.501, y cualquier uso o divulgación de dichas notas requiere su autorización, a menos que el uso o la divulgación sea:
        </p>
        <ul style="margin-bottom: 20px; padding-left: 20px; line-height: 1.6; list-style-type: lower-alpha;">
            <li>Para mi uso al tratarle.</li>
            <li>Para mi uso en la capacitación o supervisión de profesionales de la salud mental, con el fin de ayudarles a mejorar sus habilidades en consejería o terapia grupal, conjunta, familiar o individual.</li>
            <li>Para mi uso en mi defensa ante procedimientos legales iniciados por usted.</li>
            <li>Para uso del Secretario de Salud y Servicios Humanos con el fin de investigar mi cumplimiento de la normativa HIPAA.</li>
            <li>Requerido por ley, siempre que el uso o la divulgación se limiten a lo exigido por dicha ley.</li>
            <li>Requerido por ley para ciertas actividades de supervisión sanitaria relacionadas con el autor de las notas de psicoterapia.</li>
            <li>Requerido por un forense que esté desempeñando funciones autorizadas por ley.</li>
            <li>Requerido para ayudar a evitar una amenaza grave para la salud y la seguridad de otras personas.</li>
        </ul>
        <p>
            <strong>2. Fines de marketing:</strong> Como psicoterapeuta, no utilizaré ni divulgaré su información de salud protegida (PHI) con fines de marketing.
        </p>
        <p>
            <strong>3. Venta de información de salud protegida (PHI):</strong> Como psicoterapeuta, no venderé su información de salud protegida (PHI) en el curso habitual de mi actividad profesional.
        </p>

        <h2>IV. Ciertos Usos y Divulgaciones No Requieren su Autorización</h2>
        <p>
            Sujeto a ciertas limitaciones legales, puedo utilizar y divulgar su información de salud protegida (PHI) sin su autorización por las siguientes razones:
        </p>
        <ol style="margin-bottom: 20px; padding-left: 20px; line-height: 1.6;">
            <li>Cuando la divulgación sea requerida por leyes estatales o federales, y el uso o la divulgación cumpla con los requisitos pertinentes de dicha ley y se limite a ellos.</li>
            <li>Para actividades de salud pública, incluida la notificación de sospechas de abuso infantil, de personas mayores o de adultos dependientes, o para prevenir o reducir una amenaza grave para la salud o la seguridad de cualquier persona.</li>
            <li>Para actividades de supervisión sanitaria, incluidas auditorías e investigaciones.</li>
            <li>Para procedimientos judiciales y administrativos, incluida la respuesta a una orden judicial o administrativa, aunque mi preferencia es obtener su autorización antes de hacerlo.</li>
            <li>Para fines de las fuerzas del orden, incluida la notificación de delitos ocurridos en mis instalaciones.</li>
            <li>A forenses o médicos examinadores, cuando dichas personas desempeñen funciones autorizadas por la ley.</li>
            <li>Para fines de investigación, incluido el estudio y la comparación de la salud mental de pacientes que recibieron una forma de terapia frente a aquellos que recibieron otra forma de terapia para la misma afección.</li>
            <li>Funciones gubernamentales especializadas, que incluyen: garantizar la ejecución adecuada de misiones militares; proteger al Presidente de los Estados Unidos; realizar operaciones de inteligencia o contrainteligencia; o ayudar a garantizar la seguridad de quienes trabajan o se alojan en instituciones correccionales.</li>
            <li>Para fines relacionados con la compensación para trabajadores. Aunque mi preferencia es obtener su autorización, puedo proporcionar su PHI para cumplir con las leyes de compensación para trabajadores.</li>
            <li>Recordatorios de citas y beneficios o servicios relacionados con la salud. Puedo utilizar y divulgar su PHI para comunicarme con usted y recordarle que tiene una cita conmigo. También puedo utilizar y divulgar su PHI para informarle sobre alternativas de tratamiento u otros servicios o beneficios de atención médica que ofrezco.</li>
        </ol>

        <h2>V. Ciertos Usos y Divulgaciones Requieren Oportunidad de Oponerse</h2>
        <p>
            <strong>1. Divulgaciones a familiares, amigos u otras personas:</strong> Puedo proporcionar su información de salud protegida (PHI) a un familiar, amigo u otra persona que usted indique que participa en su atención o en el pago de su atención médica, a menos que usted se oponga total o parcialmente. La oportunidad de dar su consentimiento puede obtenerse de manera retroactiva en situaciones de emergencia.
        </p>

        <h2>VI. Usted Tiene los Siguientes Derechos con Respecto a su PHI</h2>
        <ol style="margin-bottom: 25px; padding-left: 20px; line-height: 1.6;">
            <li><strong>Derecho a solicitar límites en los usos y divulgaciones de su PHI:</strong> Usted tiene derecho a pedirme que no utilice ni divulgue cierta PHI para fines de tratamiento, pago u operaciones de atención médica. No estoy obligado a aceptar su solicitud y puedo decir "no" si considero que ello afectaría su atención médica.</li>
            <li><strong>Derecho a solicitar restricciones para gastos pagados totalmente de su propio bolsillo:</strong> Usted tiene derecho a solicitar restricciones en la divulgación de su PHI a planes de salud para fines de pago u operaciones de atención médica, si dicha PHI se refiere exclusivamente a un artículo o servicio de atención médica que usted haya pagado en su totalidad de su propio bolsillo.</li>
            <li><strong>Derecho a elegir cómo le envío la PHI:</strong> Usted tiene derecho a pedirme que me comunique con usted de una manera específica (por ejemplo, por teléfono en casa o en la oficina) o que envíe correspondencia a una dirección diferente; accederé a todas las solicitudes razonables.</li>
            <li><strong>Derecho a ver y obtener copias de su PHI:</strong> A excepción de las "notas de psicoterapia", usted tiene derecho a obtener una copia electrónica o en papel de su expediente médico y de otra información que yo tenga sobre usted. Le proporcionaré una copia de su expediente, o un resumen del mismo (si usted acepta recibir un resumen), dentro de los 30 días siguientes a la recepción de su solicitud por escrito; es posible que cobre una tarifa razonable basada en los costos por realizar este trámite.</li>
            <li><strong>Derecho a obtener una lista de las divulgaciones que he realizado:</strong> Usted tiene derecho a solicitar una lista de las ocasiones en las que he divulgado su PHI para fines distintos al tratamiento, el pago o las operaciones de atención médica, o para las cuales usted me haya otorgado una autorización. Responderé a su solicitud en un plazo de 60 días. La lista incluirá las divulgaciones realizadas en los últimos seis años. La primera lista es gratuita; solicitudes adicionales en el mismo año tendrán una tarifa razonable.</li>
            <li><strong>Derecho a corregir o actualizar su PHI:</strong> Si considera que existe un error en su PHI, o que falta información importante en ella, tiene derecho a solicitar que corrija la información existente o que añada la información faltante. Es posible que rechace su solicitud, pero le comunicaré el motivo por escrito en un plazo de 60 días.</li>
            <li><strong>Derecho a obtener una copia impresa o electrónica de este aviso:</strong> Usted tiene derecho a obtener una copia impresa de este aviso y también tiene derecho a recibir una copia del mismo por correo electrónico.</li>
        </ol>

        <p style="font-size: 0.9em; color: #666; font-style: italic; margin-bottom: 25px;">
            <strong>FECHA DE VIGENCIA DE ESTE AVISO:</strong> Este aviso entró en vigor el 20 de septiembre de 2013.
        </p>

        <h2>Acuse de Recibo del Aviso de Privacidad</h2>
        <p>
            En virtud de la Ley de Portabilidad y Responsabilidad del Seguro Médico de 1996 (HIPAA), usted tiene ciertos derechos con respecto al uso y la divulgación de su información de salud protegida. Al marcar la casilla a continuación, usted reconoce haber recibido una copia del Aviso de prácticas de privacidad de HIPAA.
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
                        AL MARCAR LA CASILLA A CONTINUACIÓN, CONFIRMO QUE HE LEÍDO Y COMPRENDIDO LOS PUNTOS CONTENIDOS EN ESTE DOCUMENTO Y QUE ESTOY DE ACUERDO CON ELLOS.
                    </label>
                </div>
            </div>
        </div>

        <div class="signature-section">
            <div class="form-row" style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 2;">
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
            <button type="submit" class="submit-btn">Enviar Acuse de Privacidad</button>
        </div>
        
        <div id="form-response-message" style="margin-top: 20px; font-weight: bold; text-align: center;"></div>
    </form>
</div>