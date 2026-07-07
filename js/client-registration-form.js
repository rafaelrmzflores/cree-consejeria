jQuery(document).ready(function ($) {
  console.log("Client Registration Form JS loaded successfully!");
  // Target the form submit button loop
  $("#clientRegistrationForm").on("submit", function (e) {
    e.preventDefault();

    // 1. Target feedback response container
    var $messageContainer = $("#form-response-message");
    var $submitButton = $(this).find(".submit-btn");

    // 2. Serialize all form input field metrics into an array payload
    var rawFormData = {};
    var formDataArray = $(this).serializeArray();

    $.each(formDataArray, function () {
      rawFormData[this.name] = this.value;
    });

    // UI Feedback Status: Processing State
    $submitButton.prop("disabled", true).text("Saving Submission...");
    $messageContainer
      .css("color", "#333")
      .text("Processing secure encryption synchronization...");

    // 3. Fire the real asynchronous pipeline call straight to WordPress admin-ajax
    $.ajax({
      url: cree_intake_app.ajax_url,
      type: "POST",
      dataType: "json",
      data: {
        action: "cree_submit_intake_form",
        nonce: cree_intake_app.nonce,
        form_data: rawFormData,
      },
      success: function (response) {
        if (response.success) {
          $messageContainer.css("color", "green").text(response.data.message);
          // Clear the form elements layout cleanly on complete success
          $("#clientRegistrationForm")[0].reset();
          $submitButton.prop("disabled", false).text("Submit Registration");
        } else {
          $messageContainer.css("color", "red").text(response.data.message);
          $submitButton.prop("disabled", false).text("Submit Registration");
        }
      },
      error: function (xhr, status, error) {
        $messageContainer
          .css("color", "red")
          .text("Server link dropped. Please check connection logs.");
        $submitButton.prop("disabled", false).text("Submit Registration");
      },
    });
  });
});
