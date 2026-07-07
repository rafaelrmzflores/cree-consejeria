jQuery(document).ready(function ($) {
  $("#clientRegistrationForm").on("submit", function (e) {
    // Stop standard HTML page reload
    e.preventDefault();

    var $form = $(this);
    var $submitBtn = $form.find(".submit-btn");
    var $responseMsg = $("#form-response-message");

    // Prevent double-submissions
    $submitBtn.prop("disabled", true).text("Saving Securely...");
    $responseMsg.html("").removeClass("error success");

    // Serialize all form fields cleanly
    var formDataArray = $form.serializeArray();
    var formDataObj = {};

    $.each(formDataArray, function (_, field) {
      formDataObj[field.name] = field.value;
    });

    // Fire the WordPress AJAX pipeline
    $.ajax({
      url: cree_intake_app.ajax_url, // FIXED: Matches your shortcode localization object handle name
      type: "POST",
      dataType: "json",
      data: {
        action: "cree_submit_intake_form",
        nonce: cree_intake_app.nonce, // FIXED: Matches your shortcode localization object handle name
        form_data: formDataObj,
      },
      success: function (response) {
        if (response.success) {
          $responseMsg.css("color", "#155724").html(response.data.message);
          $form.slideUp(400); // Smoothly hide form on success
        } else {
          $responseMsg.css("color", "#721c24").html(response.data.message);
          $submitBtn.prop("disabled", false).text("Submit Registration");
        }
      },
      error: function () {
        $responseMsg
          .css("color", "#721c24")
          .html("A server connection error occurred. Please try again.");
        $submitBtn.prop("disabled", false).text("Submit Registration");
      },
    });
  });
});
