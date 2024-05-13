function processPhonesToAsterisk() {
  setupProcessingModalLoading();

  $.ajax({
    method: "GET",
    url: 'phone-processing.php',
    dataType: 'xml'
  })
    .done(function (xml) {
      setupProcessingModal(xml);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Processing request form load failed: " + textStatus);
    });

  return false;
}

function setupProcessingModalLoading() {
  $("#processingTotalModified").html("Loading...");
  $("#processingAdded").html("Loading...");
  $("#processingEdited").html("Loading...");
  $("#processingDeleted").html("Loading...");

  //Hide log bits.
  $("#processingFullLog").hide();
  $("#processingFullLog").html("Loading...");

  //Hide error reporting bits.
  $("#processingAlert").hide();
  $("#processingErrorList").hide();
  $("#processingErrorListField").html("Loading...");

  //Everything is setup -- show the modal.
  $("#processPhonesModal").modal('show');
}

function setupProcessingModal(xml) {
  var overall = $(xml).find("PhoneProcessorResult");
  var error = overall.find('error').text();
  var totalModified = overall.find('totalModified').text();
  var errorCount = overall.find('errorCount').text();
  var editedCount = overall.find('editedCount').text();
  var addedCount = overall.find('addedCount').text();
  var deletedCount = overall.find('deletedCount').text();

  var fullLog = $(xml).find("FullLog").text();

  $("#processingTotalModified").html(totalModified);
  $("#processingAdded").html(addedCount);
  $("#processingEdited").html(editedCount);
  $("#processingDeleted").html(deletedCount);

  $("#processingFullLog").hide();
  $("#processingFullLog").html(fullLog);

  //Error Reporting Section
  var errorList = "Phones with errors: <br>";
  var phone_serial = "";

  $(xml).find('Phone').each(function () {
    errorCheck = $(this).find('error').text();
    phoneId = $(this).find('phone_id').text();

    if (errorCheck == 1) {
      phone_serial = $(this).find('phone_serial').text();

      errorList = errorList + "Phone Serial: " + phone_serial + "<br>";
    }

    reloadPhoneNumbers(phoneId);
  });

  if (error == 1) {
    $("#processingErrorListField").html(errorList);
    $("#processingErrorList").show();
    $("#processingAlert").html("At least one error occurred in processing! Log written to /asterisk_scripts/error_log");
    $("#processingAlert").show();
  } else {
    $("#processingAlert").hide();
    $("#processingErrorList").hide();
  }


}

function sureDeleteOrg(idToDelete, name) {
  if (confirm("Are you sure you want to delete " + name + "?")) {
    $.ajax({
      method: "GET",
      url: "orgs-update.php",
      data: { delete: 1, id: idToDelete }
    })
      .always(function () {
        location.reload();
      })
      .fail(function (jqXHR, textStatus) {
        alert("Delete request failed: " + textStatus);
      });
  }
  else {
    return false;
  }
}

function updateDeployed(idToUpdate) {
  $.ajax({
    method: "GET",
    url: "phone-edit-ajax.php",
    data: { request: "deployed", id: idToUpdate },
    dataType: 'xml'
  })
    .done(function (xml) {
      var id = $(xml).find('phone_id').text();
      var isDeployed = $(xml).find('phone_is_deployed').text();
      var btn = "#btnDeployed" + id;

      if (isDeployed == 1) {
        $(btn).html("Deployed");
        $(btn).removeClass("btn-warning");
        $(btn).addClass("btn-danger");
        $(btn).removeClass("btn-success");
      } else if (isDeployed == 0) {
        $(btn).html("Not Deployed");
        $(btn).addClass("btn-warning");
        $(btn).removeClass("btn-danger");
        $(btn).removeClass("btn-success");
      } else {
        $(btn).html("Returned");
        $(btn).addClass("btn-success");
        $(btn).removeClass("btn-danger");
        $(btn).removeClass("btn-warning");
      }

    })
    .fail(function (jqXHR, textStatus) {
      alert("Update request failed: " + textStatus);
    });
}

function updateDeployedRouter(idToUpdate) {
  $.ajax({
    method: "GET",
    url: "routers-edit-ajax.php",
    data: { update: 1, request: "deployed", id: idToUpdate },
    dataType: 'xml'
  })
    .done(function (xml) {
      var id = $(xml).find('router_id').text();
      var isDeployed = $(xml).find('router_is_deployed').text();
      var btn = "#btnRouterDeployed" + id;

      if (isDeployed == 1) {
        $(btn).html("Deployed");
        $(btn).removeClass("btn-warning");
        $(btn).addClass("btn-danger");
        $(btn).removeClass("btn-success");
      } else if (isDeployed == 0) {
        $(btn).html("Not Deployed");
        $(btn).addClass("btn-warning");
        $(btn).removeClass("btn-danger");
        $(btn).removeClass("btn-success");
      } else {
        $(btn).html("Returned");
        $(btn).addClass("btn-success");
        $(btn).removeClass("btn-danger");
        $(btn).removeClass("btn-warning");
      }

    })
    .fail(function (jqXHR, textStatus) {
      alert("Update request failed: " + textStatus);
    });
}

function removePhone(idToRemove) {
  $.ajax({
    method: "GET",
    url: "phone-edit-ajax.php",
    data: { request: "remove", id: idToRemove },
    dataType: 'xml'
  })
    .done(function (xml) {
      var id = $(xml).find('phone_id').text();
      var isDeployed = $(xml).find('phone_is_deployed').text();
      var isMarkedDelete = $(xml).find('todelete_phone').text();

      var btn = "#btnDeletePhone" + id;
      var notify = "#phoneNotification" + id;

      if (isDeployed == 1) {
        //Can't do any deletion!
        alert("Phone cannot be removed while deployed!");
      } else {
        if (isMarkedDelete == 1) {
          $(notify).html("----------- Phone Marked For Deletion!!! -----------");

          $(btn).removeClass("btn-alert");
          $(btn).addClass("btn-danger");

          $(btn).html("Removed");
        } else {
          $(notify).html("");
          $(btn).addClass("btn-alert");
          $(btn).removeClass("btn-danger");


          $(btn).html("Remove Phone");
        }
      }

    })
    .fail(function (jqXHR, textStatus) {
      alert("Update request failed: " + textStatus);
    });

}

function reloadPhoneNumbers(phoneid) {
  $.ajax({
    method: "POST",
    url: "numbers-list.php?id=" + phoneid,
    dataType: 'html'
  })
    .done(function (listData) {
      $("#numberList" + phoneid).html(listData);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Add number request failed: " + textStatus);
    });
}

function redoSCCPPhone(phoneid) {
  $.ajax({
    method: "GET",
    url: "sccp-functions-ajax.php",
    data: { phone_id: phoneid, request: "redo", update: 1 },
    dataType: 'xml'
  })
    .done(function (xml) {
      var phoneid = $(xml).find('phoneid').text();
      var result = $(xml).find('result').text();

      alert("Result: " + result);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Reload Number Failed: " + textStatus);
    });
}

function reloadSCCPPhone(phoneid) {
  $.ajax({
    method: "GET",
    url: "sccp-functions-ajax.php",
    data: { phone_id: phoneid, request: "reload" },
    dataType: 'xml'
  })
    .done(function (xml) {
      var phoneid = $(xml).find('phoneid').text();
      var result = $(xml).find('result').text();

      alert("Result: " + result);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Reload Number Failed: " + textStatus);
    });
}

function restartSCCPPhone(phoneid) {
  $.ajax({
    method: "GET",
    url: "sccp-functions-ajax.php",
    data: { phone_id: phoneid, request: "restart" },
    dataType: 'xml'
  })
    .done(function (xml) {
      var phoneid = $(xml).find('phoneid').text();
      var result = $(xml).find('result').text();

      alert("Result: " + result);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Reload Number Failed: " + textStatus);
    });
}

function ResetAllAsteriskData() {
  if (confirm("Are you sure? This will remove all phones from asterisk.")) {
    $.ajax({
      method: "GET",
      url: "phone-processing-asteriskreset.php",
      dataType: 'html'
    })
      .done(function (result) {
        alert("Remove all command sent - Be sure to reprocess the phones.");
      })
      .fail(function (jqXHR, textStatus) {
        alert("This didn't work: " + textStatus);
      });
  }

  return false;
}
