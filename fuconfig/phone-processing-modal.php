<?php

include_once ('FUConfig.php');


?>

<div class="modal-dialog modal-dialog-centered" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="processingModalTitle">Processing Phones to Asterisk</h5>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col alert alert-danger m-2" id="processingAlert"></div>
      </div>

      <div class="row mt-2">
        <div class="col-3"><strong>Total Modified: </strong></div>
        <div class="col-6" id="processingTotalModified"></div>
      </div>

      <div class="row mt-2">
        <div class="col-3"><strong>Added: </strong></div>
        <div class="col-6" id="processingAdded"></div>
      </div>

      <div class="row mt-2">
        <div class="col-3"><strong>Edited: </strong></div>
        <div class="col-6" id="processingEdited"></div>
      </div>

      <div class="row mt-2">
        <div class="col-3"><strong>Deleted: </strong></div>
        <div class="col-6" id="processingDeleted"></div>
      </div>

      <div class="row mt-2" id="processingErrorList">
        <div class="col-3"><strong>Error List: </strong></div>
        <div class="col-6" id="processingErrorListField"></div>
      </div>

      <div class="row">
        <div class="col" id="processingFullLog"></div>
      </div>
    </div>

    <div class="modal-footer">
      <button id="btnShowFullLog" class="btn btn-dark" onClick="$('#processingFullLog').show()">Show Full Log</button>
      <button id="btnCloseProcessing" class="btn btn-success" data-dismiss="modal"
        onClick="$('#processPhonesModal').modal('hide')">Close</button>
    </div>

  </div>
</div>
</div>