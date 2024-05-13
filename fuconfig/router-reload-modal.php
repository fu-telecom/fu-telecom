<div class="modal-dialog modal-dialog-centered" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="routerUpdateTitle">Router Update</h5>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col alert alert-danger m-2" id="routerUpdateAlert"></div>
      </div>

      <div class="row mt-2">
        <div class="col-3"><strong>Status: </strong></div>
        <div class="col-6" id="routerUpdateStatus">Sending update...</div>
      </div>


    </div>

    <div class="modal-footer">
      <button id="btnTryAgainRouterUpdate" class="btn btn-primary">Try Again</button>
      <button id="btnCancelRouterUpdate" class="btn btn-dark" data-dismiss="modal"
        onClick="$('#routerUpdateModal').modal('hide');">Close</button>
    </div>

  </div>
</div>