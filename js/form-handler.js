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
          $responseMsgContainer
            .css("color", "green")
            .text(response.data.message);
          // Clear the form elements layout cleanly on complete success
          // $("#clientRegistrationForm")[0].reset();
          $form[0].reset();
          $submitButton.prop("disabled", false).text("Submit Registration");
        } else {
          $responseMsgContainer.css("color", "red").text(response.data.message);
          $submitButton.prop("disabled", false).text("Submit Registration");
        }
      },
      error: function (xhr, status, error) {
        console.error("Status:", status);
        console.error("Error Text:", error);
        console.log("Raw Server Response Payload:", xhr.responseText);
        $responseMsgContainer
          .css("color", "red")
          .text("Server link dropped. Please check connection logs.");
        $submitButton.prop("disabled", false).text("Submit Registration");
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
