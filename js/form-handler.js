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

    // Get the button that was clicked
    var $submitButton = $(document.activeElement);
    var buttonName = $submitButton.attr("name");

    if (buttonName === "submit_final") {
      // Handle final submission
      console.log("Final submission");
      // Your final submit logic here
    } else if (buttonName === "submit_partial") {
      // Handle partial/save submission
      console.log("Save/partial submission");
      // Your save logic here
    }

    var form_action =
      buttonName === "submit_partial" ? "partial_save" : "final_submission";

    console.log("FORM ACTION:", form_action);

    // 1. Target feedback response container
    var $form = $(this);
    // var $submitButton = $form.find(".submit-btn");
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
        form_action: form_action,
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
