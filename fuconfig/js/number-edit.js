function handleNumberEditFormSubmit(xml) {
  var added = $(xml).find('added').text();
  var edited = $(xml).find('edited').text();
  var validate = $(xml).find('validate').text();
  var phoneid = $(xml).find('phone_id').text();
  var exists = $(xml).find('already_exists').text();
  var toomany = $(xml).find('toomany_numbers').text();
  var format = $(xml).find('format_problem').text();
  var max = $(xml).find('max_numbers').text();
  var duplicate_violation = $(xml).find('duplicate_violation').text();


  if (validate == 0) {
    //Invalid, notify.
    $("#frmAddNumberAlert").show();
    $("#frmAddNumberAlert").html('');

    if (exists > 0) {
      $("#frmAddNumberAlert").append("<p>Number already exists in database. Add existing instead.</p>");
    }

    if (format == 0) {
      $("#frmAddNumberAlert").append("<p>Data is not valid as entered.</p>");
    }

    if (toomany > 0) {
      $("#frmAddNumberAlert").append("<p>You can't add more lines to this phone! Remove one first. Max: " + max + "</p>");
    }

    if (duplicate_violation > 0) {
      $("#frmAddNumberAlert").append("<p>Error! Duplicate entry. You can't add the same number to the same phone.</p>");
    }

  } else {
    //Valid, added or edited, clear form and update.

    $("#addNumberModal").modal('hide');
    $("#addNumberModal").html('');

    reloadPhoneNumbers(phoneid);

  }
}

function setupNumberAddEditFormButton(urlToSubmit) {
  $.ajax({
    method: "POST",
    url: urlToSubmit,
    data: $("#frmAddNumber").serialize(),
    dataType: 'xml'
  })
    .done(function (xml) {
      handleNumberEditFormSubmit(xml);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Add number request failed: " + textStatus);
    });
}

function setupNumberEditForm(formData, action) {
  $("#addNumberModal").html(formData);
  $("#frmAddNumberAlert").hide();
  $("#addNumberModal").modal('show');

  $("#btnSubmitNumberAddEditForm").click(function () {
    if (action == 'add') {
      setupNumberAddEditFormButton("numbers-edit-ajax.php?create=1&isnew=1");
    } else {
      setupNumberAddEditFormButton("numbers-edit-ajax.php?update=1");
    }

    return false;
  });

  $("#btnCancelNumberAddEditForm").click(function () {
    $("#addNumberModal").modal('hide');

    return false;
  });
}

function addNewNumber(phoneId) {
  $.ajax({
    method: "GET",
    url: "number-edit-form.php",
    data: { phone_id: phoneId, create: 1 },
    dataType: 'html'
  })
    .done(function (formData) {
      setupNumberEditForm(formData, 'add');
    })
    .fail(function (jqXHR, textStatus) {
      alert("Add new form request failed: " + textStatus);
    });

}

function editNumber(phoneId, numberId) {
  $.ajax({
    method: "GET",
    url: "number-edit-form.php",
    data: { phone_id: phoneId, number_id: numberId, update: 1 },
    dataType: 'html'
  })
    .done(function (formData) {
      setupNumberEditForm(formData, 'edit');
    })
    .fail(function (jqXHR, textStatus) {
      alert("Add new form request failed: " + textStatus);
    });
}

function deleteNumber(phoneId, numberId, assignmentId) {
  $.ajax({
    method: "GET",
    url: "numbers-edit-ajax.php",
    data: { phone_id: phoneId, number_id: numberId, assignment_id: assignmentId, delete: 1 },
    dataType: 'xml'
  })
    .done(function (xml) {
      var phoneid = $(xml).find('phone_id').text();

      reloadPhoneNumbers(phoneid);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Add new form request failed: " + textStatus);
    });
}