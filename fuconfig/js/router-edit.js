function reloadRouter(routerId) {
  $.ajax({
    method: "GET",
    url: "routers-list.php?review=1&id=" + routerId,
    dataType: 'html'
  })
    .done(function (data) {
      $("#router_" + routerId).html(data);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Reload router request failed: " + textStatus);
    });
}

function editRouter(routerId) {
  $.ajax({
    method: "POST",
    url: "routers-edit.php",
    data: { router_id: routerId, update: 1, request: 'edit' },
    dataType: 'html'
  })
    .done(function (formData) {
      setupRouterEditForm(formData, 'edit');
    })
    .fail(function (jqXHR, textStatus) {
      alert("Add new form request failed: " + textStatus);
    });

  return false;
}

function removeRouter(routerId) {
  $.ajax({
    method: "POST",
    url: "routers-edit-ajax.php",
    data: { router_id: routerId, update: 1, request: 'remove' },
    dataType: 'xml'
  })
    .done(function (formData) {
      window.location.reload(false);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Add new form request failed: " + textStatus);
    });

  return false;
}

function processRouterUpdate(routerId) {
  $("#routerUpdateModal").modal('show');
  $("#btnTryAgainRouterUpdate").click(function () {
    sendRouterUpdateRequest(routerId);
  });

  sendRouterUpdateRequest(routerId);

  return false;
}

function sendRouterUpdateRequest(routerId) {
  $("#routerUpdateStatus").html('Sending update...');
  $("#routerUpdateAlert").hide();

  $.ajax({
    method: "POST",
    url: "routers-reload.php",
    data: { router_id: routerId },
    dataType: 'xml'
  })
    .done(function (xml) {
      var routerId = $(xml).find('router_id').text();
      var error = $(xml).find('error').text();
      var message = $(xml).find('message').text();
      var complete = $(xml).find('complete').text();

      if (error == 1) {
        $("#routerUpdateAlert").html(message);
        $("#routerUpdateAlert").show();
        $("#routerUpdateStatus").html('Could not update router.');

      } else if (error == 0 && complete == 1) {
        $("#routerUpdateStatus").html('Router is updated.');
      } else {
        $("#routerUpdateAlert").html("Timeout occurred! (Or another error.)");
        $("#routerUpdateAlert").show();
        $("#routerUpdateStatus").html('Contact your system administrator.');
      }

      reloadRouter(routerId);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Add new form request failed: " + textStatus);
    });
}

function setupRouterEditForm(formData, action) {
  $("#editRouterModal").html(formData);
  $("#frmRouterAlert").hide();
  $("#editRouterModal").modal('show');

  $("#btnSubmitRouterForm").click(function () {
    $.ajax({
      method: "POST",
      url: "routers-edit-ajax.php",
      data: $("#frmRouter").serialize(),
      dataType: 'xml'
    })
      .done(function (xml) {
        handleRouterEditFormSubmit(xml);
      })
      .fail(function (jqXHR, textStatus) {
        alert("Router request failed: " + textStatus);
      });
  });

  $("#btnCancelRouterForm").click(function () {
    $("#editRouterModal").modal('hide');
  });

}

function handleRouterEditFormSubmit(xml) {
  var routerId = $(xml).find('router_id').text();
  var error = $(xml).find('error').text();

  if (error.length > 0) {

  } else {
    $("#editRouterModal").modal('hide');
  }
  reloadRouter(routerId);
}
