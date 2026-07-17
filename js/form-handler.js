jQuery(document).ready(function ($) {
  console.log("Form Handler JS loaded successfully!");
  // ==========================================
  // 1. AGE & DATE AUTOMATION
  // ==========================================
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

  const dateInput = document.getElementById("sign_date");
  const witnessDateInput = document.getElementById("witness_sign_date");
  const todayISO = new Date().toISOString().split("T")[0];

  if (dateInput) {
    dateInput.value = todayISO;
  }
  if (witnessDateInput) {
    witnessDateInput.value = todayISO;
  }

  // ==========================================
  // 2. CONDITIONAL FIELD TOGGLES (Moved inside ready block)
  // ==========================================
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
    toggleOtherField(); // Run initial check
  }

  // ==========================================
  // 3. DRAFT RECOVERY & PIPELINE INITIALIZATION
  // ==========================================
  const initialWpId = $("#wp_id").val();
  console.log("Checking for draft ID:", initialWpId); // Debugging

  if (initialWpId) {
    loadDraft(initialWpId);
  }

  function loadDraft(wp_id) {
    $.ajax({
      url: CREE_INTAKE_FORM.ajax_url,
      type: "GET",
      data: {
        action: "get_draft_form_data",
        wp_id: wp_id,
        nonce: CREE_INTAKE_FORM.nonce,
      },
      success: function (response) {
        if (response.success && response.data.form_data) {
          // 1. Point to the specific data object
          const data = response.data.form_data;

          // 2. Loop through the key-value pairs
          $.each(data, function (name, value) {
            const $input = $(`[name="${name}"]`);

            if ($input.length) {
              if ($input.is(":checkbox") || $input.is(":radio")) {
                // Handle Radios and Checkboxes
                $input
                  .filter(`[value="${value}"]`)
                  .prop("checked", true)
                  .change();
              } else if ($input.is("select")) {
                // Handle Select boxes
                $input.val(value).change();
              } else {
                // Handle Text, Textarea, Date, Hidden, etc.
                $input.val(value).change();
              }
            }
          });
          console.log("Form hydrated successfully!");
        }
      },
      error: function (xhr, status, thrownError) {
        console.error("AJAX Failed:", status, thrownError);
      },
    });
  }

  // ==========================================
  // 4. SECURE FORM SUBMISSION DISPATCHER
  // ==========================================
  $(".cree-form-container form").on("submit", function (e) {
    e.preventDefault();

    var $form = $(this);
    var $submitButton = $(document.activeElement);

    // Fallback to finding the form submit button if activeElement isn't the button
    if (!$submitButton.is("button, input[type='submit']")) {
      $submitButton = $form.find('button[type="submit"], input[type="submit"]');
    }

    var buttonName = $submitButton.attr("name");
    var form_action =
      buttonName === "submit_partial"
        ? "partial_submission"
        : "final_submission";

    console.log("FORM ACTION:", form_action);
    var $responseMsgContainer = $("#form-response-message");

    // Form title matching suite
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

    console.log("=== PRUEBA DE ENVÍO ===");
    console.log("Formulario detectado:", formType);
    console.log("Texto del botón restaurado si falla:", buttonText);
    console.log("=======================");

    // Lock UI to prevent race condition multi-posting
    $submitButton.prop("disabled", true).text("Saving Submission...");
    $responseMsgContainer
      .css("color", "#333")
      .text("Processing secure encryption synchronization...")
      .removeClass("error success");

    // Serialization
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
        form_action: form_action,
        wp_id: $("#wp_id").val(),
      },
      success: function (response) {
        if (response.success) {
          if (response.data && response.data.wp_id) {
            $("#wp_id").val(response.data.wp_id);
          }

          var $container = $form.closest(".cree-form-container");
          $container.addClass("form-submitted");

          $responseMsgContainer
            .css({
              color: "green",
              "background-color": "#e6f4ea",
              padding: "15px",
              "border-radius": "4px",
              border: "1px solid #1e7b1e", // Fixed malformed hex value code
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
        console.error("Raw Server Response:", xhr.responseText);
        $responseMsgContainer
          .css("color", "red")
          .text("Server link dropped. Please check connection logs.");
        $submitButton.prop("disabled", false).text(buttonText);
      },
    });
  });
});
