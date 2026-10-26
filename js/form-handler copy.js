console.log("Form Handler JS loaded successfully!");

document.addEventListener("DOMContentLoaded", function () {
  // 1. Date of Birth -> Age Auto-Calculation
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

      let age = today.getFullYear() - birthDate.getFullYear();
      const monthDifference = today.getMonth() - birthDate.getMonth();
      const dayDifference = today.getDate() - birthDate.getDate();

      if (monthDifference < 0 || (monthDifference === 0 && dayDifference < 0)) {
        age--;
      }

      ageInput.value = age >= 0 ? age : 0;
    });
  }

  // 2. Auto-fill Today's Date in Signature Fields
  var dateInput = document.getElementById("sign_date");
  var witnessDateInput = document.getElementById("witness_sign_date");
  if (dateInput && !dateInput.value) {
    dateInput.value = new Date().toISOString().split("T")[0];
  }
  if (witnessDateInput && !witnessDateInput.value) {
    witnessDateInput.value = new Date().toISOString().split("T")[0];
  }

  // 3. Conditional "Other" Drug Field Toggle
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
        textInput.value = "";
      }
    }

    selectElement.addEventListener("change", toggleOtherField);
    toggleOtherField();
  }
});

jQuery(document).ready(function ($) {

  // =========================================================================
  // 1. UNIFIED INTAKE FORM SUBMISSION HANDLER (Handles All Forms via Event Delegation)
  // =========================================================================
  $(document).on("submit", ".cree-form-container form", function (e) {
    e.preventDefault();

    var $form = $(this);
    var $submitButton = $form.find("button[type='submit'], .submit-btn");
    var $responseMsgContainer = $form.find("#form-response-message");

    if ($responseMsgContainer.length === 0) {
      $responseMsgContainer = $('<div id="form-response-message" style="margin-top:20px; font-weight:bold; text-align:center;"></div>');
      $form.append($responseMsgContainer);
    }

    var formType = $form.find('input[name="form_type"]').val() || "intake";
    var buttonText = "Enviar Formulario";

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
        buttonText = "Enviar Formulario";
    }

    $submitButton.prop("disabled", true).text("Procesando...");
    $responseMsgContainer
      .css("color", "#333")
      .text("Procesando cifrado seguro...")
      .removeClass("error success");

    // Gather serialized form data payload
    var formDataObj = {};
    var formDataArray = $form.serializeArray();

    $.each(formDataArray, function (_, field) {
      formDataObj[field.name] = field.value;
    });

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
          var $container = $form.closest(".cree-form-container");
          $container.addClass("form-submitted");

          $responseMsgContainer
            .css({
              color: "green",
              "background-color": "#e6f4ea",
              padding: "15px",
              "border-radius": "4px",
              border: "1px solid #1e7e34",
              "margin-top": "20px",
            })
            .text(response.data.message || "El formulario ha sido enviado exitosamente.");

          $submitButton.hide();

          // Auto-return to Client Portal view if operating inside portal container
          if ($("#cree-client-form-fill-view").length > 0) {
            setTimeout(function () {
              $("#cree-client-form-fill-view").hide();
              $("#cree-client-form-list-view").show();
              location.reload();
            }, 2000);
          }
        } else {
          $responseMsgContainer.css("color", "red").text(response.data.message || "Error al procesar el formulario.");
          $submitButton.prop("disabled", false).text(buttonText);
        }
      },
      error: function (xhr, status, error) {
        console.error("Submission Error:", error, xhr.responseText);
        $responseMsgContainer
          .css("color", "red")
          .text("Error de conexión con el servidor. Por favor intente nuevamente.");
        $submitButton.prop("disabled", false).text(buttonText);
      },
    });
  });

  // =========================================================================
  // 2. CLIENT PORTAL NAVIGATION & FORM RENDERING
  // =========================================================================
  
  // Open Pending Form for Completion
  $(document).on("click", ".cree-open-pending-form-btn", function (e) {
    e.preventDefault();
    var formType = $(this).data("form-type");

    if (!formType) {
      alert("Form type missing.");
      return;
    }

    $("#cree-client-form-list-view").hide();
    $("#cree-client-form-fill-view").show();
    $("#cree-active-form-render-target").html(
      '<p style="text-align:center; padding:30px;">Cargando plantilla del formulario...</p>'
    );

    $.post(
      CREE_INTAKE_FORM.ajax_url,
      {
        action: "cree_get_client_form_template",
        nonce: CREE_INTAKE_FORM.nonce,
        form_type: formType,
      },
      function (res) {
        if (res.success) {
          $("#cree-active-form-render-target").html(res.data.html);

          // Re-initialize dynamic date fields inside freshly loaded form HTML
          var dateInput = document.getElementById("sign_date");
          if (dateInput && !dateInput.value) {
            dateInput.value = new Date().toISOString().split("T")[0];
          }
        } else {
          $("#cree-active-form-render-target").html(
            '<p style="color:red;">' + (res.data.message || res.data) + "</p>"
          );
        }
      }
    ).fail(function (xhr, status, error) {
      $("#cree-active-form-render-target").html(
        '<p style="color:red;">Error al cargar el formulario: ' + error + "</p>"
      );
    });
  });

  // Back to Form List Button
  $(document).on("click", "#cree-back-to-list-btn", function (e) {
    e.preventDefault();
    $("#cree-client-form-fill-view").hide();
    $("#cree-client-form-list-view").show();
    location.reload();
  });

  // =========================================================================
  // 3. DECRYPTED FORM MODAL VIEWER
  // =========================================================================
  
  // Open Form Content Details Modal
  $(document).on("click", ".cree-view-form-btn", function (e) {
    e.preventDefault();
    var formId = $(this).data("form-id");

    if (!formId) {
      alert("Form ID missing.");
      return;
    }

    $("#cree-form-modal-content").html("<p>Cargando contenido del formulario cifrado...</p>");
    $("#cree-form-view-modal").css("display", "flex");

    $.post(
      CREE_INTAKE_FORM.ajax_url,
      {
        action: "cree_get_single_form_details",
        nonce: CREE_INTAKE_FORM.nonce,
        form_id: formId,
      },
      function (res) {
        if (res.success) {
          $("#cree-form-modal-content").html(res.data.html);
        } else {
          $("#cree-form-modal-content").html(
            '<p style="color:red;">' + (res.data.message || res.data) + "</p>"
          );
        }
      }
    ).fail(function (xhr, status, error) {
      $("#cree-form-modal-content").html(
        '<p style="color:red;">Error del servidor: ' + error + "</p>"
      );
    });
  });

  // Close Modal
  $(document).on("click", "#cree-close-modal-btn", function (e) {
    e.preventDefault();
    $("#cree-form-view-modal").hide();
  });

  $(window).on("click", function (e) {
    if ($(e.target).is("#cree-form-view-modal")) {
      $("#cree-form-view-modal").hide();
    }
  });
});