console.log("Form Handler JS loaded successfully!");

document.addEventListener("DOMContentLoaded", function () {
  const dobInput = document.getElementById("dob");
  const ageInput = document.getElementById("age");

  if (dobInput && ageInput) {
    dobInput.addEventListener("change", function () {
      const dobValue = this.value;
      if (!dobValue) {
        ageInput.value = "";
        return;
      }

      const birthDate = new Date(dobValue);
      const today = new Date();

      // Cálculo inicial de la diferencia de años
      let age = today.getFullYear() - birthDate.getFullYear();
      const monthDifference = today.getMonth() - birthDate.getMonth();
      const dayDifference = today.getDate() - birthDate.getDate();

      // Ajuste si el cumpleaños no ha ocurrido este año todavía
      if (monthDifference < 0 || (monthDifference === 0 && dayDifference < 0)) {
        age--;
      }

      // Asigna el valor asegurando que no sea un número negativo por error de entrada
      ageInput.value = age >= 0 ? age : 0;
    });
  }

  var dateInput = document.getElementById("sign_date");
  var witnessDateInput = document.getElementById("witness_sign_date");
  if (dateInput) {
    var today = new Date().toISOString().split("T")[0];
    dateInput.value = today;
  }
  if (witnessDateInput) {
    var today = new Date().toISOString().split("T")[0];
    witnessDateInput.value = today;
  }
});

const selectElement = document.getElementById("drug_of_choice");
const container = document.getElementById("other_drug_container");
const textInput = document.getElementById("other_drug_text");

if (selectElement && container && textInput) {
  function toggleOtherField() {
    if (selectElement.value === "Other") {
      container.classList.remove("is-hidden");
      textInput.setAttribute("required", "required");
    } else {
      container.classList.add("is-hidden");
      textInput.removeAttribute("required");
      textInput.value = ""; // Limpia el campo si cambian de opinión
    }
  }

  // 1. Escuchar los cambios del usuario
  selectElement.addEventListener("change", toggleOtherField);

  // 2. Ejecutar al cargar la página para sincronizar estados guardados
  toggleOtherField();
}

jQuery(document).ready(function ($) {
  // Target the form submit button loop
  $(".cree-form-container form").on("submit", function (e) {
    // Stop standard HTML page reload
    e.preventDefault();

    // 1. Target feedback response container
    var $form = $(this);
    var $submitButton = $form.find(".submit-btn");
    var $responseMsgContainer = $("#form-response-message");

    // ==========================================
    // 🧪 DETECCIÓN DE PRUEBA (Antes de enviar)
    // ==========================================
    var formType = $form.find('input[name="form_type"]').val();
    var buttonText = "Submit Form";
    var formTitle = $form.find('input[name="form_title"]').val();

    switch (formType) {
      case "substance_abuse_intake":
        buttonText = "Submit Intake";
        break;
      case "aviso_de_practicas_de_privacidad":
        buttonText = "Enviar Acuse de Privacidad";
        break;
      case "consentimiento_informado_para_psicoterapia":
        buttonText = "Enviar Consentimiento";
        break;
      case "politicas_de_la_practica":
        buttonText = "Enviar Políticas";
        break;
      case "consentimiento_informado_para_terapia_en_linea":
        buttonText = "Enviar Consentimiento";
        break;
      case "consentimiento_para_divulgacion_de_informacion":
        buttonText = "Enviar Autorización";
        break;
      case "registration_short_form":
        buttonText = "Submit Registration";
        break;
      case "psychosocial_evaluation":
        buttonText = "Submit Evaluation";
        break;
      default:
        buttonText = "Submit Form";
    }

    // Estas líneas te dirán exactamente en la consola del navegador qué se detectó al enviar
    console.log("=== PRUEBA DE ENVÍO ===");
    console.log("Formulario detectado:", formType);
    console.log("Texto del botón restaurado si falla:", buttonText);
    console.log("Título del formulario:", formTitle);
    console.log("=======================");

    // Prevent double-submissions
    $submitButton.prop("disabled", true).text("Saving Submission...");
    // UI Feedback Status: Processing State
    $responseMsgContainer
      .css("color", "#333")
      .text("Processing secure encryption synchronization...")
      .removeClass("error success");

    // 2. Serialize all form input field metrics into an array payload
    var formDataObj = {};
    var formDataArray = $form.serializeArray();

    $.each(formDataArray, function (_, field) {
      formDataObj[field.name] = field.value;
    });

    // 3. Fire the real asynchronous pipeline call straight to WordPress admin-ajax
    $.ajax({
      url: CREE_INTAKE_FORM.ajax_url,
      type: "POST",
      dataType: "json",
      data: {
        action: "cree_submit_intake_form",
        nonce: CREE_INTAKE_FORM.nonce,
        form_data: formDataObj,
      },
      success: function (response) {
        if (response.success) {
          // $responseMsgContainer
          //   .css("color", "green")
          //   .text(response.data.message);
          // Clear the form elements layout cleanly on complete success
          // $form[0].reset();
          // $submitButton.prop("disabled", false).text("Submit Registration");

          // 1. Ocultar todos los elementos e hijos dentro del formulario
          // EXCEPTO el contenedor final del botón y el mensaje
          /* $form
            .children()
            .not(".submit-btn-container, #form-response-message")
            .hide(); */

          // 2. Ocultar o remover el botón de envío para que no estorbe
          //   $submitButton.hide();

          // 1. Buscamos el contenedor principal (sirve para cualquier formulario)
          var $container = $form.closest(".cree-form-container");

          // 2. Añadimos la clase de estado completado
          $container.addClass("form-submitted");

          // 3. Estilizar y colocar el mensaje de éxito dentro del contenedor limpio
          $responseMsgContainer
            .css({
              color: "green",
              "background-color": "#e6f4ea",
              padding: "15px",
              "border-radius": "4px",
              border: "1px solid #mx1e7b",
              "margin-top": "20px",
            })
            .text(response.data.message);
        } else {
          $responseMsgContainer.css("color", "red").text(response.data.message);
          $submitButton.prop("disabled", false).text(buttonText);
        }
      },
      error: function (xhr, status, error) {
        console.error("Status:", status);
        console.error("Error Text:", error);
        console.log("Raw Server Response Payload:", xhr.responseText);
        $responseMsgContainer
          .css("color", "red")
          .text("Server link dropped. Please check connection logs.");
        $submitButton.prop("disabled", false).text(buttonText);
      },
    });
  });
});

jQuery(document).ready(function($) {
    // 1. Handle "View Content" Button Clicks
    $(document).on('click', '.cree-view-form-btn', function(e) {
        e.preventDefault();
        var formId = $(this).data('form-id');
        
        if (!formId) {
            alert('Form ID missing.');
            return;
        }

        // Show modal and loading message
        $('#cree-form-detail-content').html('<p>Loading secure form contents...</p>');
        $('#cree-form-detail-modal').show();

        // Send AJAX request to fetch decrypted details
        $.post(CREE_INTAKE_FORM.ajax_url, {
            action: 'cree_get_single_form_details',
            nonce: CREE_INTAKE_FORM.nonce,
            form_id: formId
        }, function(res) {
            if (res.success) {
                $('#cree-form-detail-content').html(res.data.html);
            } else {
                $('#cree-form-detail-content').html('<p style="color:red;">' + (res.data.message || res.data) + '</p>');
            }
        }).fail(function(xhr, status, error) {
            $('#cree-form-detail-content').html('<p style="color:red;">Server error: ' + error + '</p>');
        });
    });

    // 2. Close Modal Handler
    $('#cree-close-modal-btn').on('click', function(e) {
        e.preventDefault();
        $('#cree-form-detail-modal').hide();
    });
});

jQuery(document).ready(function($) {
    $('#cree-intake-submission-form').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $('#cree-submit-form-btn');
        const $responseMsg = $('#cree-form-response-msg');

        // Disable submit button to prevent double-clicks
        $submitBtn.prop('disabled', true).text('Encrypting & Submitting...');
        $responseMsg.text('').css('color', '#1d2327');

        // Gather serialized form data
        // Note: Individual inputs in your views are named like name="form_data[client_name]"
        const formData = $form.serialize();

        $.ajax({
            url: CREE_INTAKE_FORM.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'cree_submit_intake_form',
                nonce: CREE_INTAKE_FORM.nonce,
                form_data: $form.serializeArray().reduce(function(acc, field) {
                    // Extract field name inside brackets if using form_data[key], 
                    // or pass serialized data directly if handled by PHP mapping
                    acc[field.name] = field.value;
                    return acc;
                }, {})
            },
            success: function(response) {
                $submitBtn.prop('disabled', false).text('Submit Form Securely');

                if (response.success) {
                    // 1. Handle Successful Submission
                    $responseMsg.css('color', 'green').html('✅ ' + response.data.message);
                    $form[0].reset(); // Clear form inputs

                    // Optional: Redirect client back to portal after 2 seconds
                    setTimeout(function() {
                        window.location.href = window.location.origin + '/client-portal/';
                    }, 2000);

                } else {
                    // 2. Handle Errors (Catches 'already_submitted' or validation failures)
                    const errorMsg = response.data && response.data.message 
                        ? response.data.message 
                        : 'An unexpected error occurred.';
                    
                    $responseMsg.css('color', '#d63638').html('⚠️ ' + errorMsg);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // 3. Handle Network or Server Failure
                $submitBtn.prop('disabled', false).text('Submit Form Securely');
                $responseMsg.css('color', '#d63638').html('⚠️ Connection error. Please check your network and try again.');
            }
        });
    });
});

/* function checkOther(selectElement) {
  var container = document.getElementById("other_drug_container");
  if (container) {
    if (selectElement.value === "Other") {
      container.style.display = "block";
      document
        .getElementById("other_drug_text")
        .setAttribute("required", "required");
    } else {
      container.style.display = "none";
      document.getElementById("other_drug_text").removeAttribute("required");
      document.getElementById("other_drug_text").value = "";
    }
  }
}
// Run initialization to hide it initially
document.addEventListener("DOMContentLoaded", function () {
  var container = document.getElementById("other_drug_container");
  if (container) {
    container.style.display = "none";
  }
}); */
