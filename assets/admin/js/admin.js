jQuery(document).ready(function() {
    if ("dont_associate" != jQuery("select[name='rdm_client_asociate_with_existing_wp_account_field'] option:selected").val()) {
     var valueFromWPUserTable = '<p class="valueFromWpUser">' + rdmJobsadmin.value_from_wp_user + '</span>';
     jQuery("#rdm_client_first_name_field_id").attr("readonly", "readonly"), 
     jQuery("#rdm_client_first_name_field_id").after(valueFromWPUserTable), 
     jQuery("#rdm_client_last_name_field_id").attr("readonly", "readonly"), 
     jQuery("#rdm_client_last_name_field_id").after(valueFromWPUserTable), 
     jQuery("#rdm_client_email_field_id").attr("readonly", "readonly"), 
     jQuery("#rdm_client_email_field_id").after(valueFromWPUserTable)
    }
    jQuery("select[name='rdm_client_asociate_with_existing_wp_account_field']").on("change", function(e) {
     var currentSelectInput = jQuery("select[name='rdm_client_asociate_with_existing_wp_account_field']"),
      valueFromWPUserTable = '<p class="valueFromWpUser">' + rdmJobsadmin.value_from_wp_user + '</span>';
     currentSelectInput.parents().find("div#clients_meta_box div.inside p.valueFromWpUser").remove(), e.val > 0 && jQuery.ajax({
      type: "POST",
      url: ajaxurl,
      beforeSend: function() {
       currentSelectInput.parents().find("div#clients_meta_box div.inside").css({
        opacity: "0.2"
       })
      },
      data: {
       action: "update_client_infos_if_associated_ajax",
       userID: e.val
      },
      dataType: "json",
      success: function(response) {
       currentSelectInput.parents().find("div#clients_meta_box div.inside").css({
        opacity: "1"
       }), "yes" === response.rdm_found_user && (jQuery("#rdm_client_first_name_field_id").val(response.user_first_name), 
       jQuery("#rdm_client_first_name_field_id").attr("readonly", "readonly"), 
       jQuery("#rdm_client_first_name_field_id").after(valueFromWPUserTable), 
       jQuery("#rdm_client_last_name_field_id").val(response.user_last_name), 
       jQuery("#rdm_client_last_name_field_id").attr("readonly", "readonly"), 
       jQuery("#rdm_client_last_name_field_id").after(valueFromWPUserTable), 
       jQuery("#rdm_client_email_field_id").val(response.user_email), 
       jQuery("#rdm_client_email_field_id").attr("readonly", "readonly"), 
       jQuery("#rdm_client_email_field_id").after(valueFromWPUserTable)), jQuery(this).show()
      },
      error: function(MLHttpRequest, textStatus, errorThrown) {
       console.log(errorThrown)
      }
     }), "dont_associate" == e.val && (jQuery("#rdm_client_first_name_field_id").val(""), 
     jQuery("#rdm_client_first_name_field_id").removeAttr("readonly", "readonly"), 
     jQuery("#rdm_client_last_name_field_id").val(""), 
     jQuery("#rdm_client_last_name_field_id").removeAttr("readonly", "readonly"), 
     jQuery("#rdm_client_email_field_id").val(""), 
     jQuery("#rdm_client_email_field_id").removeAttr("readonly", "readonly"), 
     jQuery("#rdm_client_middle_name_field_id").val(""), 
     jQuery("#rdm_client_phone_field_id").val(""), 
     jQuery("#rdm_client_mobile_field_id").val(""))
    }), 
    rdm_jobs_functions = [], 
    rdm_jobs_functions.disable_invoice_buttons = function() {
     jQuery("input#rdm_apply_vat_btn,#rdm_preview_invoice,#rdm_jobs_download_invoice").attr("disabled", "disabled")
    }, 
    rdm_jobs_functions.enable_invoice_buttons = function() {
     jQuery("input#rdm_apply_vat_btn,#rdm_preview_invoice,#rdm_jobs_download_invoice").removeAttr("disabled")
    }


/* Javascript for purchases 
 */


jQuery(document).ready(function() {
    if ("dont_associate" != jQuery("select[name='rdm_supplier_asociate_with_existing_wp_account_field'] option:selected").val()) {
     var valueFromWPUserTable = '<p class="valueFromWpUser">' + rdmJobsadmin.value_from_wp_user + '</span>';
     jQuery("#rdm_supplier_first_name_field_id").attr("readonly", "readonly"), 
     jQuery("#rdm_supplier_first_name_field_id").after(valueFromWPUserTable), 
     jQuery("#rdm_supplier_last_name_field_id").attr("readonly", "readonly"), 
     jQuery("#rdm_supplier_last_name_field_id").after(valueFromWPUserTable), 
     jQuery("#rdm_supplier_email_field_id").attr("readonly", "readonly"), 
     jQuery("#rdm_supplier_email_field_id").after(valueFromWPUserTable)
    }
    jQuery("select[name='rdm_supplier_asociate_with_existing_wp_account_field']").on("change", function(e) {
     var currentSelectInput = jQuery("select[name='rdm_supplier_asociate_with_existing_wp_account_field']"),
      valueFromWPUserTable = '<p class="valueFromWpUser">' + rdmJobsadmin.value_from_wp_user + '</span>';
     currentSelectInput.parents().find("div#suppliers_meta_box div.inside p.valueFromWpUser").remove(), e.val > 0 && jQuery.ajax({
      type: "POST",
      url: ajaxurl,
      beforeSend: function() {
       currentSelectInput.parents().find("div#suppliers_meta_box div.inside").css({
        opacity: "0.2"
       })
      },
      data: {
       action: "update_supplier_infos_if_associated_ajax",
       userID: e.val
      },
      dataType: "json",
      success: function(response) {
       currentSelectInput.parents().find("div#suppliers_meta_box div.inside").css({
        opacity: "1"
       }), "yes" === response.rdm_found_user && (jQuery("#rdm_supplier_first_name_field_id").val(response.user_first_name), 
       jQuery("#rdm_supplier_first_name_field_id").attr("readonly", "readonly"), 
       jQuery("#rdm_supplier_first_name_field_id").after(valueFromWPUserTable), 
       jQuery("#rdm_supplier_last_name_field_id").val(response.user_last_name), 
       jQuery("#rdm_supplier_last_name_field_id").attr("readonly", "readonly"), 
       jQuery("#rdm_supplier_last_name_field_id").after(valueFromWPUserTable), 
       jQuery("#rdm_supplier_email_field_id").val(response.user_email), 
       jQuery("#rdm_supplier_email_field_id").attr("readonly", "readonly"), 
       jQuery("#rdm_supplier_email_field_id").after(valueFromWPUserTable)), jQuery(this).show()
      },
      error: function(MLHttpRequest, textStatus, errorThrown) {
       console.log(errorThrown)
      }
     }), "dont_associate" == e.val && (jQuery("#rdm_supplier_first_name_field_id").val(""), 
     jQuery("#rdm_supplier_first_name_field_id").removeAttr("readonly", "readonly"), 
     jQuery("#rdm_supplier_last_name_field_id").val(""), 
     jQuery("#rdm_supplier_last_name_field_id").removeAttr("readonly", "readonly"), 
     jQuery("#rdm_supplier_email_field_id").val(""), 
     jQuery("#rdm_supplier_email_field_id").removeAttr("readonly", "readonly"), 
     jQuery("#rdm_supplier_middle_name_field_id").val(""), 
     jQuery("#rdm_supplier_phone_field_id").val(""), 
     jQuery("#rdm_supplier_mobile_field_id").val(""))
    }), rdm_jobs_functions = [], rdm_jobs_functions.disable_purchase_buttons = function() {
     jQuery("input#rdm_apply_vat_btn,#rdm_preview_purchase,#rdm_jobs_download_purchase").attr("disabled", "disabled")
    }, rdm_jobs_functions.enable_purchase_buttons = function() {
     jQuery("input#rdm_apply_vat_btn,#rdm_preview_purchase,#rdm_jobs_download_purchase").removeAttr("disabled")
    }
   });







   });