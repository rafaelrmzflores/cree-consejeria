console.log("Form Handler JS loaded successfully!");

// Global variables for multi-step and autosave
let currentStep = 0;
let autoSaveKey = "";

jQuery(document).ready(function ($) {
  // =========================================================================
  // 1. CLIENT PORTAL NAVIGATION & FORM RENDERING
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
      '<p style="text-align:center; padding:30px;">Cargando plantilla del formulario...</p>',
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

          // Initialize Dynamic Dates
          var dateInput = document.getElementById("sign_date");
          if (dateInput && !dateInput.value) {
            dateInput.value = new Date().toISOString().split("T")[0];
          }

          // Initialize Wizard & Autosave for the dynamically loaded form
          initFormWizardAndAutosave(formType);
        } else {
          $("#cree-active-form-render-target").html(
            '<p style="color:red;">' + (res.data.message || res.data) + "</p>",
          );
        }
      },
    ).fail(function (xhr, status, error) {
      $("#cree-active-form-render-target").html(
        '<p style="color:red;">Error al cargar el formulario: ' +
          error +
          "</p>",
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

  // Open Form Content Details Modal (Decrypted Viewer)
  $(document).on("click", ".cree-view-form-btn", function (e) {
    e.preventDefault();
    var formId = $(this).data("form-id");
    if (!formId) {
      alert("Form ID missing.");
      return;
    }

    $("#cree-form-modal-content").html(
      "<p>Cargando contenido del formulario cifrado...</p>",
    );
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
            '<p style="color:red;">' + (res.data.message || res.data) + "</p>",
          );
        }
      },
    ).fail(function (xhr, status, error) {
      $("#cree-form-modal-content").html(
        '<p style="color:red;">Error del servidor: ' + error + "</p>",
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

  // =========================================================================
  // 2. WIZARD & AUTOSAVE INITIALIZATION
  // =========================================================================

  // Auto-init if the form is already on the page (not in portal view)
  if (
    $(".cree-form-container form").length > 0 &&
    $("#cree-client-portal-wrapper").length === 0
  ) {
    let initialFormType =
      $(".cree-form-container form").find('input[name="form_type"]').val() ||
      "generic";
    initFormWizardAndAutosave(initialFormType);
  }

  function initFormWizardAndAutosave(formType) {
    const form = document.querySelector(".cree-form-container form");
    if (!form) return;

    autoSaveKey = `cree_autosave_${formType}`;
    currentStep = 0;

    // --- Local Auto-Save ---
    function loadAutoSave() {
      const savedData = localStorage.getItem(autoSaveKey);
      if (savedData) {
        const data = JSON.parse(savedData);
        Object.keys(data).forEach((key) => {
          const input = form.querySelector(`[name="${key}"]`);
          if (input) {
            if (input.type === "radio" || input.type === "checkbox") {
              if (input.value === data[key]) input.checked = true;
            } else {
              // Only overwrite the PHP-rendered value if the saved draft actually contains text
              if (data[key] && data[key].trim() !== "") {
                input.value = data[key];
              }
            }
          }
        });
      }
    }

    function saveFormData() {
      const formData = new FormData(form);
      const dataObj = {};
      formData.forEach((value, key) => {
        if (key !== "nonce" && key !== "action") dataObj[key] = value;
      });
      localStorage.setItem(autoSaveKey, JSON.stringify(dataObj));
    }

    form.addEventListener("input", saveFormData);
    form.addEventListener("change", saveFormData);
    loadAutoSave();

    // Clear validation error on input
    form.addEventListener("input", (e) => {
      if (e.target.classList.contains("cree-invalid-field")) {
        e.target.classList.remove("cree-invalid-field");
      }
    });
  }

  // =========================================================================
  // 3. MULTI-STEP NAVIGATION LOGIC (Event Delegation)
  // =========================================================================

  function validateStep($step) {
    const requiredInputs = $step.find("[required]");
    let isValid = true;

    requiredInputs.each(function () {
      if (!$(this).val().trim()) {
        isValid = false;
        $(this).addClass("cree-invalid-field");
      } else {
        $(this).removeClass("cree-invalid-field");
      }
    });

    const $responseMsg = $("#form-response-message");
    if (!isValid) {
      if ($responseMsg.length) {
        $responseMsg
          .css("color", "red")
          .text("Por favor complete los campos requeridos marcados en rojo.");
      }
    } else {
      if ($responseMsg.length) $responseMsg.text("");
    }
    return isValid;
  }

  $(document).on("click", ".cree-next-step", function (e) {
    e.preventDefault();
    var $form = $(this).closest("form");
    var $steps = $form.find(".cree-form-step");
    var $currentStepEl = $steps.eq(currentStep);

    if (validateStep($currentStepEl)) {
      $currentStepEl.removeClass("active-step");
      currentStep++;
      $steps.eq(currentStep).addClass("active-step");
      window.scrollTo({ top: $form.offset().top - 50, behavior: "smooth" });
    }
  });

  $(document).on("click", ".cree-prev-step", function (e) {
    e.preventDefault();
    var $form = $(this).closest("form");
    var $steps = $form.find(".cree-form-step");

    $steps.eq(currentStep).removeClass("active-step");
    currentStep--;
    $steps.eq(currentStep).addClass("active-step");
    window.scrollTo({ top: $form.offset().top - 50, behavior: "smooth" });
  });

  // =========================================================================
  // 4. AJAX SUBMISSION
  // =========================================================================

  $(document).on("submit", ".cree-form-container form", function (e) {
    e.preventDefault();

    var $form = $(this);
    var $steps = $form.find(".cree-form-step");

    // Final validation check for the last step (or whole form if no steps)
    var $stepToValidate = $steps.length > 0 ? $steps.eq(currentStep) : $form;
    if (!validateStep($stepToValidate)) return;

    var $submitButton = $form.find("button[type='submit'], .submit-btn").last();
    var $responseMsg = $form.find("#form-response-message");
    if ($responseMsg.length === 0) {
      $responseMsg = $(
        '<div id="form-response-message" aria-live="polite" style="margin-top:20px; font-weight:bold; text-align:center;"></div>',
      );
      $form.append($responseMsg);
    }

    var $overlay = $form
      .closest(".cree-form-container")
      .find(".cree-loading-overlay");
    if ($overlay.length === 0) {
      $overlay = $(
        '<div class="cree-loading-overlay" id="cree-loading-overlay"><div class="cree-loading-spinner"></div><p style="font-weight: bold; margin-top: 10px;">Procesando seguro...</p></div>',
      );
      $form.closest(".cree-form-container").prepend($overlay);
    }

    $overlay.css("display", "flex");
    $submitButton.prop("disabled", true);
    $responseMsg.text("").removeClass("error success");

    var formDataArray = $form.serializeArray();
    var formDataObj = {};
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
        $overlay.hide();
        if (response.success) {
          localStorage.removeItem(autoSaveKey); // Clear autosave on success
          $responseMsg
            .css({
              color: "green",
              padding: "15px",
              background: "#e6f4ea",
              borderRadius: "4px",
            })
            .text(response.data.message || "Enviado exitosamente.");
          $form.find(".cree-form-step, .submit-btn-container").hide();

          if ($("#cree-client-form-fill-view").length > 0) {
            setTimeout(() => location.reload(), 2000);
          }
        } else {
          $responseMsg
            .css("color", "red")
            .text(response.data.message || "Error al procesar.");
          $submitButton.prop("disabled", false);
        }
      },
      error: function () {
        $overlay.hide();
        $responseMsg
          .css("color", "red")
          .text("Error de conexión. Intente nuevamente.");
        $submitButton.prop("disabled", false);
      },
    });
  });

  // =========================================================================
  // 5. AGE AUTO-CALCULATOR (Event Delegation)
  // =========================================================================
  $(document).on("change", "#dob", function () {
    const dobValue = $(this).val();
    const $ageInput = $("#age");

    // Only run if the age field exists on this specific form
    if ($ageInput.length === 0) return;

    if (!dobValue) {
      $ageInput.val("");
      // Dispatch native event so auto-save catches the clearing of the field
      $ageInput[0].dispatchEvent(new Event("input", { bubbles: true }));
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

    // Set the value
    $ageInput.val(age >= 0 ? age : 0);

    // FIX: Dispatch a native DOM event so the vanilla JS auto-save listener catches it
    $ageInput[0].dispatchEvent(new Event("input", { bubbles: true }));
  });
});
