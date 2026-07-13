<?php
/**
 * View Template: Informed Consent Form - Psychotherapy
 * Language: Spanish
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 
?>
<div class="cree-form-container">
    <form id="informedConsentPsychotherapyForm">
        <input type="hidden" name="form_type" value="<?php echo esc_attr( $attributes['type'] ); ?>">
        <input type="hidden" name="user_ip" value="<?php echo esc_attr( ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' ); ?>">

        <h1>Consentimiento Informado para Psicoterapia</h1>
        
        <div class="therapist-badge-info" style="background: #f7f9fa; border-left: 4px solid #0056b3; padding: 15px; margin-bottom: 25px; font-size: 0.9em; color: #555;">
            <strong>Terapeuta:</strong> Rafael Ramirez (Licencia LPC-A #906126)<br>
            <strong>Centro:</strong> CREE Consejeria <br>
            <strong>Contacto:</strong> (325) 219-4039
        </div>

        <h2>Información General</h2>
        <p>
            CREE Consejeria (HU) es un centro profesional de consejería cristiana. Cada uno de nuestros terapeutas cuenta con una formación académica y una experiencia profesional diversas. Asimismo, cada terapeuta posee antecedentes y experiencias cristianas variadas. De igual manera, cada terapeuta tiene un enfoque profesional distinto respecto a cómo integra los principios clínicos con los basados en la fe. Lo que todos los terapeutas de HU tienen en común es el deseo de ser instrumentos de un Dios amoroso.
        </p>
        <p>
            En HU creemos firmemente en la importancia de la salud psicológica, emocional, relacional, espiritual y física. HU también valora enormemente la autonomía de cada cliente. Por ello, le animamos a que, si tiene alguna inquietud o pregunta, consulte a su terapeuta sobre su enfoque profesional y personal respecto a la consejería cristiana.
        </p>
        <p>
            La relación terapéutica es única, ya que es altamente personal y, al mismo tiempo, un acuerdo contractual. Por tanto, es importante que lleguemos a un entendimiento claro sobre cómo funcionará nuestra relación y qué podemos esperar mutuamente. Este consentimiento proporcionará un marco claro para nuestro trabajo conjunto. Siéntase libre de hablar conmigo sobre cualquier aspecto de esto. Por favor, lea esta información, confírmela y acepte los términos marcando la casilla correspondiente al final de este documento.
        </p>

        <h2>El Proceso Terapéutico</h2>
        <p>
            Usted ha dado un paso muy positivo al decidir buscar terapia. El resultado de su tratamiento depende en gran medida de su disposición para participar en este proceso, el cual puede, en ocasiones, generar una incomodidad considerable. Recordar sucesos desagradables y tomar conciencia de los sentimientos asociados a ellos puede provocar fuertes sentimientos de ira, depresión, ansiedad, etc.
        </p>
        <p>
            Naturalmente, estamos convencidos de que los beneficios de la psicoterapia superan los riesgos. La terapia a menudo conduce a mejores relaciones, a la solución de problemas específicos y a una reducción significativa del malestar emocional. Sin embargo, no existen garantías sobre lo que usted experimentará. No puedo prometerle que su comportamiento o sus circunstancias cambiarán. Me comprometo a apoyarle y a esforzarme al máximo por comprenderle a usted y sus patrones recurrentes, así como a ayudarle a aclarar qué es lo que desea para sí mismo/a.
        </p>

        <h2>Confidencialidad y Limitaciones Legales</h2>
        <p>
            El contenido de las sesiones y todos los materiales relacionados con el tratamiento del cliente se mantendrán confidenciales, a menos que el cliente solicite por escrito que la totalidad o parte de dicho contenido se divulgue a una o varias personas específicamente nombradas. Existen limitaciones a este derecho de confidencialidad del cliente, las cuales se detallan a continuación:
        </p>
        <ol style="margin-bottom: 20px; padding-left: 20px; color: #444; line-height: 1.6;">
            <li>Si un cliente threatener o intenta suicidarse, o actúa de manera que implique un riesgo sustancial de sufrir lesiones corporales graves.</li>
            <li>Si un cliente amenaza con causar lesiones corporales graves o la muerte a otra persona.</li>
            <li>Si el terapeuta tiene sospechas razonables de que un cliente u otra víctima identificada es el autor, testigo o víctima real de abuso físico, emocional o sexual contra menores de 18 años.</li>
            <li>Sospechas, tal como se ha indicado anteriormente, en el caso de una persona mayor que pueda estar siendo objeto de dichos abusos.</li>
            <li>Sospecha de negligencia hacia las personas mencionadas en los puntos 3 y 4.</li>
            <li>Si un tribunal emite una orden judicial legítima (citación) para obtener la información especificada en dicha orden.</li>
            <li>Si el cliente está en terapia o recibiendo tratamiento por orden judicial, o si la información se obtiene con el fin de elaborar un informe pericial para un abogado.</li>
        </ol>
        <p>
            En ocasiones, es posible que necesite consultar con otros profesionales expertos en sus respectivas áreas para ofrecerle el mejor tratamiento posible. En este contexto, se podría compartir información sobre usted sin revelar su nombre.
        </p>
        <p>
            Si nos encontramos fortuitamente fuera del consultorio de terapia, no le saludaré primero. Su derecho a la privacidad y a la confidencialidad es de suma importancia para mí, y no deseo poner en riesgo su privacidad. Sin embargo, si usted me saluda primero, estaré más que dispuesto a hablar brevemente con usted; no obstante, considero apropiado no entablar conversaciones extensas en público ni fuera del consultorio de terapia.
        </p>

        <h2>Sobre el Terapeuta</h2>
        <p>
            Rafael Ramirez es terapeuta matrimonial y familiar y consejero autorizado en dependencia química. Trabaja en diversas áreas, incluyendo la superación de adicciones y la atención a niños, individuos, parejas y familias, con el fin de fomentar cambios basados en principios divinos en la vida de las personas a las que asesora. El Sr. Hall obtuvo su maestría en Terapia Matrimonial y Familiar en la Abilene Christian University. Cuenta con casi cinco años de práctica privada en CREE Consejeria.
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
                        AL MARCAR LA CASILLA A CONTINUACIÓN, CONFIRMO QUE HE LEÍDO Y COMPRENDIDO EL CONTENIDO DE ESTE DOCUMENTO Y QUE ACEPTO SUS TÉRMINOS.
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
            <button type="submit" class="submit-btn">Enviar Consentimiento Informado</button>
        </div>
        
        <div id="form-response-message" style="margin-top: 20px; font-weight: bold; text-align: center;"></div>   
    </form>
</div>