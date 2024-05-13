
function addExistingNumber(phoneId) {
  $.ajax({
    method: "GET",
    url: "number-add-existing-form.php",
    data: { phone_id: phoneId },
    dataType: 'html'
  })
    .done(function (formData) {
      setupNumberAddExistingForm(formData);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Add new form request failed: " + textStatus);
    });
}

function processAdd() {
  $.ajax({
    method: "POST",
    url: 'numbers-edit-ajax.php?create=1&existing=1',
    data: $("#frmAddExistingNumber").serialize(),
    dataType: 'xml'
  })
    .done(function (xml) {
      var phoneid = $(xml).find('phone_id').text();
      var added = $(xml).find("added").text();
      var validate = $(xml).find("validate").text();
      var duplicate_violation = $(xml).find("duplicate_violation").text();

      if (added == "1") {
        $("#addNumberModal").modal('hide');
        reloadPhoneNumbers(phoneid);
      } else if (validate == 0) {
        $("#frmAddNumberAlert").show();
        $("#frmAddNumberAlert").html('');

        if (duplicate_violation > 0) {
          $("#frmAddNumberAlert").append("<p>Error! Duplicate entry. You can't add the same number to the same phone twice.</p>");
        }
      }
    })
    .fail(function (jqXHR, textStatus) {
      alert("Submit add existing form failed: " + textStatus);
    });
}

function setupNumberAddExistingForm(formData) {
  $("#addNumberModal").html(formData);
  $("#frmAddNumberAlert").hide();
  $("#addNumberModal").modal('show');

  $("#btnSubmitNumberAddExistingForm").click(function () {
    processAdd();

    return false;
  });

  $("#btnCancelNumberAddExistingForm").click(function () {
    $("#addNumberModal").modal('hide');

    return false;
  });

  $("input[name='sortby']").change(function () {
    updateExistingNumberList();
  });

  updateExistingNumberList();
}

function fillNumberList(xml) {
  var options = "";
  var numberList = xml.getElementsByTagName("numberItem");

  for (var i = 0; i < numberList.length; i++) {
    var num = numberList[i];
    var val = num.getElementsByTagName("number_id")[0].childNodes[0].nodeValue;
    var title = num.getElementsByTagName("text")[0].childNodes[0].nodeValue;

    options += '<option value="' + val + '">' + title + '</option>\n';
  }

  $("#number_id").html(options);
}

function updateExistingNumberList() {
  var sortBy = $("input[name='sortby']:checked").val();

  $.ajax({
    method: "GET",
    url: 'number-add-existing-ajax.php',
    data: { type_id: $("#type").val(), review: 1, request: 'gettype', sortby: sortBy },
    dataType: 'xml'
  })
    .done(function (xml) {
      fillNumberList(xml);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Get type number list request failed: " + textStatus);
    });
}