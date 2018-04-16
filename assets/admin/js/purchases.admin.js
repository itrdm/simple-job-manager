jQuery(document).ready(function() {
    function updateTotalPurchaseValue(data) {
     var hiddenArrayForPDF = [];
     hiddenArrayForPDF.discount = 0, hiddenArrayForPDF.vat = 0;
     var total = 0;
     jQuery.each(data.records, function(index, record) {
      total += Number(record.ItemTotalCost);
      var hiddenObjectForPDF = {};
      hiddenObjectForPDF.itemName = record.ItemName, hiddenObjectForPDF.ItemQuantity = record.ItemQuantity, hiddenArrayForPDF.push(hiddenObjectForPDF), hiddenArrayForPDF.purchaseItems = hiddenArrayForPDF
     }), newTotal = total.toFixed(2), jQuery("span#totalPurchaseCost").html(newTotal), calculateDiscount(newTotal, "newvalue") && (newTotal = calculateDiscount(newTotal, "newvalue"), jQuery("span#totalPurchaseCost").html(newTotal), hiddenArrayForPDF.discount = calculateDiscount(newTotal, "discountValue"));
     var VATField = jQuery("input#purchase_vat_value").val();
     jQuery.isNumeric(VATField) && (calculatedVat = (Number(newTotal) + Number(newTotal * VATField / 100)).toFixed(2), jQuery("span#totalPurchaseCost").text(calculatedVat), hiddenArrayForPDF.vat = VATField), hiddenArrayForPDF.totalValue = jQuery("span#totalPurchaseCost").text(), rdm_jobs_purchase_details = hiddenArrayForPDF
    }
   
    function calculateDiscount(actualTotal, returnNewValueOrDiscount) {
     var discountValueEntered = jQuery("input#purchase_discount_value").val(),
      discountTypeEntered = jQuery("select#purchase_discount_type option:selected").val();
     if (!jQuery.isNumeric(discountValueEntered) || "none" == discountTypeEntered) return !1;
     if ("percent" == discountTypeEntered) {
      var valueAfterDiscount = actualTotal - Number(actualTotal * discountValueEntered / 100).toFixed(2);
      return valueAfterDiscount > 0 ? "newvalue" == returnNewValueOrDiscount ? valueAfterDiscount : discountValueEntered + " %" : !1
     }
     return "amount" == discountTypeEntered ? actualTotal - discountValueEntered > 0 ? "newvalue" == returnNewValueOrDiscount ? actualTotal - discountValueEntered : discountValueEntered + " " : !1 : void 0
    }
   
    function get_Jobs_items_on_purchase_ajax() {
     jQuery.ajax({
      url: ajaxurl,
      beforeSend: function() {
       jQuery("table.rdmPurchasePageTable").css({
        opacity: "0.2"
       })
      },
      type: "POST",
      dataType: "json",
      data: {
       action: "get_and_check_Jobs_checkbox_list_ajax",
       supplierID: jQuery("select#rdm_jobs_purchases_supplier_field_id option:selected").val(),
       purchaseID: jQuery("input#post_ID").val()
      },
      success: function(response) {
       jQuery("table.rdmPurchasePageTable").css({
        opacity: "1"
       }), jQuery("#rdm_jobs_purchases_Job_list").append(jQuery.map(response, function(v) {
        var is_checked = "";
        return "yes" == v.is_on_purchase_already && (is_checked = ' checked="checked" '), jQuery('<input type="checkbox" data-attr-rdm_jobID=' + v.Job_id + ' value="' + v.Job_title + '"  data-attr-rdm_jobPrice="' + v.Job_price + '" ' + is_checked + " >" + v.Job_title + " <br>")
       }))
      },
      error: function() {}
     })
    }
    jQuery("select#rdm_jobs_purchases_supplier_field_id").on("change", function(supplierDropdown) {
     supplierDropdown.val > 0 && (jQuery("#rdm_jobs_purchases_Job_list").text(""), get_Jobs_items_on_purchase_ajax()), "-1" == supplierDropdown.val && (jQuery('#rdm_jobs_purchases_Job_field_id option[value!="-1"]').remove(), jQuery("#rdm_jobs_purchases_Job_field_id").trigger("change"))
    }), jQuery("#rdm_jobs_purchases_Job_list").on("change", "input", function() {
     if (this.checked) {
      var JobPrice = 0,
       checkboxChecked = jQuery(this),
       JobTitle = checkboxChecked.val(),
       JobID = checkboxChecked.attr("data-attr-rdm_jobID"),
       Job_price_attr = checkboxChecked.attr("data-attr-rdm_jobPrice");
      "undefined" != typeof Job_price_attr && Job_price_attr !== !1 && (JobPrice = Job_price_attr), jQuery("#PersonTableContainer").jtable("addRecord", {
       record: {
        purchaseRowId: JobID,
        ItemName: JobTitle,
        ItemUnitCost: JobPrice,
        ItemQuantity: 1,
        ItemTotalCost: 1,
        is_Job: "yes"
       }
      })
     } else {
      var checkboxUnchecked = jQuery(this),
       JobID = checkboxUnchecked.attr("data-attr-rdm_jobID");
      jQuery.ajax({
       url: ajaxurl,
       type: "POST",
       dataType: "json",
       data: {
        action: "rdm_job_items_for_purchase_ajax",
        purchaseAction: "removeExistingItemFromPurchase",
        purchaseItemID: JobID,
        purchaseID: jQuery("input#post_ID").val()
       },
       success: function() {
        jQuery("#PersonTableContainer").jtable("reload")
       },
       error: function() {}
      })
     }
    }), jQuery("#PersonTableContainer").jtable({
     title: albwppm.table_purchase_items_on_purchase_title,
     actions: {
      listAction: function() {
       return jQuery(".rdm_apply_vat_loader").css("display", "inline-block"), jQuery.Deferred(function($dfd) {
        jQuery.ajax({
         url: ajaxurl,
         type: "POST",
         dataType: "json",
         data: {
          action: "rdm_job_items_for_purchase_ajax",
          purchaseAction: "getItemsForPurchase",
          purchaseID: jQuery("input#post_ID").val()
         },
         success: function(response) {
          $dfd.resolve(response), jQuery(".rdm_apply_vat_loader").css("display", "none")
         },
         error: function() {
          $dfd.reject()
         }
        })
       })
      },
      createAction: function(postData) {
       return $.Deferred(function($dfd) {
        $.ajax({
         url: ajaxurl,
         type: "POST",
         dataType: "json",
         data: {
          action: "rdm_job_items_for_purchase_ajax",
          purchaseAction: "addItemToPurchase",
          purchaseID: jQuery("input#post_ID").val(),
          itemData: postData
         },
         success: function(data) {
          $dfd.resolve(data)
         },
         error: function() {
          $dfd.reject()
         }
        })
       })
      },
      updateAction: function(postData) {
       return $.Deferred(function($dfd) {
        $.ajax({
         url: ajaxurl,
         type: "POST",
         dataType: "json",
         data: {
          action: "rdm_job_items_for_purchase_ajax",
          purchaseAction: "updateExistingItemOnPurchase",
          itemData: postData,
          purchaseID: jQuery("input#post_ID").val()
         },
         success: function(data) {
          $dfd.resolve(data), $("#rdm_pdf_preview_in_browser").hide(), $("#rdm_preview_purchase").trigger("click"), $("#rdm_pdf_preview_in_browser").show()
         },
         error: function() {
          $dfd.reject()
         }
        })
       })
      },
      deleteAction: function(postData) {
       return $.Deferred(function($dfd) {
        $.ajax({
         url: ajaxurl,
         type: "POST",
         dataType: "json",
         data: {
          action: "rdm_job_items_for_purchase_ajax",
          purchaseAction: "removeExistingItemFromPurchase",
          purchaseItemID: postData.purchaseRowId,
          purchaseID: jQuery("input#post_ID").val()
         },
         success: function(data) {
          $dfd.resolve(data)
         },
         error: function() {
          $dfd.reject()
         }
        })
       })
      }
     },
     fields: {
      purchaseRowId: {
       key: !0,
       list: !1
      },
      ItemName: {
       title: albwppm.table_purchase_line_item,
       width: "50%"
      },
      ItemUnitCost: {
       title: albwppm.table_purchase_line_price,
       width: "15%"
      },
      ItemQuantity: {
       title: albwppm.table_purchase_line_quantity,
       width: "15%"
      },
      ItemTotalCost: {
       title: albwppm.table_purchase_line_total,
       width: "15%",
       edit: !1,
       create: !1
      }
     },
     messages: {
      addNewRecord: albwppm.table_purchase_add_record_title,
      save: albwppm.table_purchase_save_new_item_button_title,
      cancel: albwppm.table_purchase_cancel_new_item_button_title,
      editRecord: albwppm.table_purchase_edit_item_popup_title,
      areYouSure: albwppm.table_purchase_delete_item_popup_title_are_you_sure,
      deleteConfirmation: albwppm.table_purchase_delete_item_confirmation_text,
      deleteText: albwppm.table_purchase_delete_item_button_text,
      noDataAvailable: albwppm.table_purchase_no_data_available,
      loadingMessage: albwppm.table_purchase_loading_records,
     },
     recordsLoaded: function(event, data) {
      updateTotalPurchaseValue(data)
     },
     recordUpdated: function() {
      jQuery("#PersonTableContainer").jtable("load")
     },
     recordDeleted: function() {
      jQuery("#PersonTableContainer").jtable("load")
     },
     recordAdded: function() {
      jQuery("#PersonTableContainer").jtable("load")
     }
    }), jQuery("input#rdm_apply_vat_btn").click(function() {
     jQuery(".rdm_apply_vat_loader").css("display", "inline-block");
     var discountValueEntered = jQuery("input#purchase_discount_value").val(),
      discountTypeEntered = jQuery("select#purchase_discount_type option:selected").val(),
      vatValueEntered = jQuery("input#purchase_vat_value").val();
     jQuery.isNumeric(discountValueEntered) || (discountValueEntered = 0), jQuery.isNumeric(vatValueEntered) || (vatValueEntered = 0), rdm_jobs_functions.disable_purchase_buttons(), jQuery.ajax({
      url: ajaxurl,
      type: "POST",
      dataType: "json",
      data: {
       action: "rdm_job_purchase_save_vat_discount_ajax",
       vatValue: vatValueEntered,
       discountType: discountTypeEntered,
       discountValue: discountValueEntered,
       purchaseID: jQuery("input#post_ID").val()
      },
      success: function(data) {
       "yes" == data.purchaseUpdated && (jQuery("#PersonTableContainer").jtable("load"), jQuery("#rdm_preview_purchase").trigger("click")), "NoItemsOnPurchase" == data.purchaseUpdated && (alert(albwppm.table_purchase_no_items_on_purchase), rdm_jobs_functions.disable_purchase_buttons()), jQuery(".rdm_apply_vat_loader").css("display", "none"), rdm_jobs_functions.enable_purchase_buttons()
      },
      error: function() {}
     })
    }), jQuery("#PersonTableContainer").jtable("load"), get_Jobs_items_on_purchase_ajax()
   });