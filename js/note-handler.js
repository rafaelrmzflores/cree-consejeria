jQuery(document).ready(function ($) {
  // =========================================================================
  // 1. QUICK ADD CLIENT INTERACTIONS
  // =========================================================================
  $(document).on("click", "#cree-toggle-add-client-btn", function (e) {
    e.preventDefault();
    $("#cree-add-client-wrapper").slideToggle(200);
  });

  $(document).on("click", "#cree-cancel-add-client-btn", function (e) {
    e.preventDefault();
    $("#cree-add-client-wrapper").slideUp(200);
    $("#cree-add-client-form")[0].reset();
    $("#cree-add-client-feedback").hide();
  });

  // Quick Add Client AJAX Submission
  $(document).on("submit", "#cree-add-client-form", function (e) {
    e.preventDefault();

    const $form = $(this);
    const $submitBtn = $form.find(".cree-submit-btn");
    const $feedback = $("#cree-add-client-feedback");

    $submitBtn.prop("disabled", true).text("Saving...");
    $feedback.hide();

    $.ajax({
      url: CREE_NOTE_FORM.ajax_url,
      type: "POST",
      data: {
        action: "cree_add_new_client",
        first_name: $("#cree_new_first_name").val(),
        last_name: $("#cree_new_last_name").val(),
        email: $("#cree_new_email").val(),
        security: CREE_NOTE_FORM.nonce,
      },
      success: function (response) {
        if (response.success) {
          $feedback
            .css({ display: "block", color: "#008a20" })
            .text(response.data.message);
          setTimeout(function () {
            $form[0].reset();
            $("#cree-add-client-wrapper").slideUp(200);
            $feedback.hide();
            location.reload(); // Refresh to list new client in sidebar directory
          }, 1000);
        } else {
          $feedback
            .css({ display: "block", color: "#d63638" })
            .text("Error: " + response.data);
        }
      },
      error: function (xhr, status, error) {
        $feedback
          .css({ display: "block", color: "#d63638" })
          .text("AJAX Error: " + error);
      },
      complete: function () {
        $submitBtn.prop("disabled", false).text("Save Client");
      },
    });
  });

  // =========================================================================
  // 2. SESSION NOTE EDIT, SAVE, & CANCEL (Dynamic Event Delegation)
  // =========================================================================

  // Toggle to Edit Mode
  $(document).on("click", ".cree-edit-note-btn", function (e) {
    e.preventDefault();
    var $card = $(this).closest(".cree-note-card");
    $card.find(".cree-note-text-view").hide();
    $card.find(".cree-note-edit-form").show();
    $(this).hide();
  });

  // Cancel Edit Mode
  $(document).on("click", ".cree-cancel-note-btn", function (e) {
    e.preventDefault();
    var $card = $(this).closest(".cree-note-card");
    $card.find(".cree-note-edit-form").hide();
    $card.find(".cree-note-text-view").show();
    $card.find(".cree-edit-note-btn").show();
  });

  // Save Edited Note via AJAX
  $(document).on("click", ".cree-save-note-btn", function (e) {
    e.preventDefault();
    var $card = $(this).closest(".cree-note-card");
    var noteId = $card.data("note-id");
    var newText = $card.find(".cree-note-edit-textarea").val();
    var $btn = $(this);

    if (!newText.trim()) {
      alert("Note cannot be empty.");
      return;
    }

    $btn.prop("disabled", true).text("Saving...");

    $.post(
      CREE_NOTE_FORM.ajax_url,
      {
        action: "cree_update_session_note",
        security: CREE_NOTE_FORM.nonce,
        note_id: noteId,
        note_text: newText,
      },
      function (res) {
        $btn.prop("disabled", false).text("💾 Save");

        if (res && res.success) {
          $card.find(".cree-note-text-view").text(newText).show();
          $card.find(".cree-note-edit-form").hide();
          $card.find(".cree-edit-note-btn").show();
        } else {
          alert(
            "Error updating note: " +
              (res.data ? res.data.message || res.data : "Unknown error"),
          );
        }
      },
    ).fail(function (xhr, status, error) {
      $btn.prop("disabled", false).text("💾 Save");
      alert("Server error: " + error);
    });
  });
});

// Load the EMR template checkboxes based on dropdown selection
function creeLoadEMRTemplate(category, clientId) {
  if (!category) {
    jQuery("#cree-emr-workspace-container").html(
      '<div style="padding: 30px; text-align: center; border: 2px dashed #d1d5db; border-radius: 6px; color: #6b7280; font-style: italic;">Please select a category above to load the treatment plan criteria.</div>',
    );
    return;
  }

  jQuery("#cree-emr-workspace-container").html(
    '<p style="color:#6b7280; font-weight:bold;">Loading EMR templates...</p>',
  );

  jQuery.post(
    CREE_NOTE_FORM.ajax_url,
    {
      action: "cree_load_emr_template",
      security: CREE_NOTE_FORM.nonce,
      category: category,
      client_id: clientId,
    },
    function (res) {
      if (res && res.success) {
        jQuery("#cree-emr-workspace-container").html(res.data.html);
      } else {
        jQuery("#cree-emr-workspace-container").html(
          '<p style="color:red;">Error loading templates.</p>',
        );
      }
    },
  );
}

// Handle saving the Encrypted Treatment Plan
jQuery(document).on("click", "#cree-save-emr-plan-btn", function (e) {
  e.preventDefault();
  var $btn = jQuery(this);
  var $form = jQuery("#cree-emr-save-plan-form");
  var formData = $form.serialize(); // Grabs all checked boxes and text fields
  var clientId = $form.find('input[name="client_id"]').val();

  $btn.prop("disabled", true).text("Encrypting...");

  jQuery
    .post(
      CREE_NOTE_FORM.ajax_url,
      formData + "&action=cree_save_emr_plan&security=" + CREE_NOTE_FORM.nonce,
      function (res) {
        $btn.prop("disabled", false).text("💾 Encrypt & Save Plan");

        if (res && res.success) {
          alert("✔️ Treatment Plan encrypted and saved securely!");
          jQuery("#cree-emr-category-select").val("").trigger("change"); // Reset workspace
          creeLoadClientPlans(clientId); // Refresh history
        } else {
          alert("❌ Error: " + (res.data.message || "Could not save plan."));
        }
      },
    )
    .fail(function () {
      $btn.prop("disabled", false).text("💾 Encrypt & Save Plan");
      alert("Server error while saving plan.");
    });
});

// Load the client's saved EMR plans history
function creeLoadClientPlans(clientId) {
  jQuery("#cree-client-plans-history").html(
    '<p style="color:#6b7280; font-weight:bold;">Decrypting saved plans...</p>',
  );

  jQuery.post(
    CREE_NOTE_FORM.ajax_url,
    {
      action: "cree_load_client_plans",
      security: CREE_NOTE_FORM.nonce,
      client_id: clientId,
    },
    function (res) {
      if (res && res.success) {
        jQuery("#cree-client-plans-history").html(res.data.html);
      } else {
        jQuery("#cree-client-plans-history").html(
          '<p style="color:red;">Error loading plans.</p>',
        );
      }
    },
  );
}

// Automatically load history when the Treatment Plan tab is clicked
jQuery(document).on(
  "click",
  'button[onclick*="prof-treatment-plan"]',
  function () {
    var clientId = jQuery('input[name="client_id"]').val();
    if (clientId) {
      creeLoadClientPlans(clientId);
    }
  },
);

// EMR Print Function
function creePrintTreatmentPlan(btn) {
  var $card = jQuery(btn).closest(".cree-goal-card").clone();
  $card.find(".no-print").remove(); // Strip out badges and buttons for clean print

  var printWindow = window.open("", "_blank", "width=800,height=900");
  printWindow.document.write(
    "<!DOCTYPE html><html><head><title>CREE Consejería - Treatment Plan</title>",
  );
  printWindow.document.write("<style>");
  printWindow.document.write(
    "body { font-family: Arial, sans-serif; color: #333; padding: 40px; line-height: 1.5; }",
  );
  printWindow.document.write(
    "h2 { color: #1e3a8a; border-bottom: 2px solid #03A9F4; padding-bottom: 10px; }",
  );
  printWindow.document.write(
    "h5 { font-size: 14px; color: #01579B; margin: 20px 0 5px 0; }",
  );
  printWindow.document.write("ul { margin-top: 0; padding-left: 20px; }");
  printWindow.document.write(".cree-goal-card { padding: 0; }");
  printWindow.document.write("</style></head><body>");

  printWindow.document.write("<h2>Confidential Treatment Plan</h2>");
  printWindow.document.write($card.html());

  printWindow.document.write(
    '<div style="margin-top: 40px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 11px; color: #777; text-align: center;">CREE Consejería - Encrypted Medical Record</div>',
  );
  printWindow.document.write("</body></html>");
  printWindow.document.close();

  setTimeout(function () {
    printWindow.print();
  }, 250);
}

// Quick Toggle for Plan Visibility
function creeTogglePlanVisibility(planId, newStatus) {
  jQuery.post(
    CREE_NOTE_FORM.ajax_url,
    {
      action: "cree_toggle_plan_visibility",
      security: CREE_NOTE_FORM.nonce,
      plan_id: planId,
      is_visible: newStatus,
    },
    function (res) {
      if (res.success) {
        var clientId = jQuery('input[name="client_id"]').val();
        creeLoadClientPlans(clientId); // Instantly refresh the UI
      }
    },
  );
}

// Load an existing plan into the EMR Builder for editing
function creeEditTreatmentPlan(planId, category) {
  var clientId = jQuery('input[name="client_id"]').val();

  // Set the master dropdown to match
  jQuery("#cree-emr-category-select").val(category);

  // Load the template and pass the planId to trigger pre-population
  jQuery("#cree-emr-workspace-container").html(
    '<p style="color:#6b7280; font-weight:bold;">Loading plan for editing...</p>',
  );

  jQuery.post(
    CREE_NOTE_FORM.ajax_url,
    {
      action: "cree_load_emr_template",
      security: CREE_NOTE_FORM.nonce,
      category: category,
      client_id: clientId,
      plan_id: planId,
    },
    function (res) {
      if (res && res.success) {
        jQuery("#cree-emr-workspace-container").html(res.data.html);

        // Smoothly scroll the user back to the top of the builder
        jQuery("html, body").animate(
          {
            scrollTop: jQuery("#prof-treatment-plan").offset().top - 50,
          },
          500,
        );
      }
    },
  );
}

// =========================================================================
// QUALITY OF LIFE: Accordion Toggles & Auto-Save
// =========================================================================

// 1. Accordion Toggle Logic (Keeps long lists clean)
jQuery(document).on("click", ".cree-accordion-toggle", function () {
  var $content = jQuery(this).next(".cree-accordion-content");
  var $icon = jQuery(this).find(".toggle-icon");

  $content.slideToggle(200);
  $icon.text($icon.text() === "▼" ? "▲" : "▼");
});

// 2. Auto-Save for Session Notes (Prevents data loss on crash/refresh)
jQuery(document).ready(function ($) {
  // Only run if we are on a page with the note form
  if ($("#cree-profile-add-note").length) {
    var clientId = $('input[name="client_id"]').val();
    var draftKey = "cree_draft_note_" + clientId;
    var $noteArea = $('textarea[name="note_text"]');

    // Restore draft on load
    var savedDraft = localStorage.getItem(draftKey);
    if (savedDraft) {
      $noteArea.val(savedDraft);
      $noteArea.after(
        '<div id="cree-draft-notice" style="font-size:11px; color:#f59e0b; margin-top:4px;">⚠️ Unsaved draft restored.</div>',
      );
    }

    // Save to browser storage as they type
    $noteArea.on("input", function () {
      localStorage.setItem(draftKey, $(this).val());
      $("#cree-draft-notice").fadeOut(); // Hide notice once they start editing again
    });

    // Clear draft when successfully saved
    $("#cree-profile-add-note").on("submit", function () {
      localStorage.removeItem(draftKey);
    });
  }
});

// Delete a Treatment Plan
function creeDeleteTreatmentPlan(planId) {
  if (
    confirm(
      "Are you sure you want to permanently delete this treatment plan? This action cannot be undone.",
    )
  ) {
    jQuery
      .post(
        CREE_NOTE_FORM.ajax_url,
        {
          action: "cree_delete_emr_plan",
          security: CREE_NOTE_FORM.nonce,
          plan_id: planId,
        },
        function (res) {
          if (res && res.success) {
            var clientId = jQuery('input[name="client_id"]').val();
            creeLoadClientPlans(clientId); // Instantly refresh the UI
          } else {
            alert("Error: " + (res.data.message || "Could not delete plan."));
          }
        },
      )
      .fail(function () {
        alert("Server error while trying to delete plan.");
      });
  }
}
