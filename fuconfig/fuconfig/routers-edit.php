<?php

include_once ('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);
$router = new Router();

if ($currentRequest->IsCreateRequest()) {
  $router->CreateEmpty();
} else if ($currentRequest->IsUpdateRequest()) {
  $router->LoadFromDB($currentRequest->router_id);
}



?>

<form id="frmRouter">
  <input type="hidden" id="router_id" name="router_id" value="<?= $router->router_id ?>" />
  <?php if ($currentRequest->IsUpdateRequest()) { ?>
    <input type="hidden" id="update" name="update" value="1" />
    <input type="hidden" id="request" name="request" value="edit" />
  <?php } else { ?>
    <input type="hidden" id="create" name="create" value="1" />
  <?php } ?>

  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" id="routerEditTitle">
        <h5 class="modal-title"><?= $currentRequest->IsUpdateRequest() ? 'Edit Router' : 'Add Router' ?></h5>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col alert alert-danger m-2" id="frmRouterAlert"></div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>IP Address: </strong></div>

          <div class="col-6"><?= $router->GetIP() ?></div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>Number: </strong></div>
          <div class="col-6"><input type="text" id="number" name="number" class="form-control"
              value="<?= $router->number ?>" /></div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>2.4Ghz Channel: </strong></div>
          <div class="col-6">
            <select class="form-control" id="channel_24" name="channel_24">
              <option value="1" <?= $router->channel_24 == 1 ? 'selected="selected"' : ''; ?>>1</option>
              <option value="6" <?= $router->channel_24 == 6 ? 'selected="selected"' : ''; ?>>6</option>
              <option value="11" <?= $router->channel_24 == 11 ? 'selected="selected"' : ''; ?>>11</option>
            </select>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>5Ghz Channel: </strong></div>
          <div class="col-6">
            <select class="form-control" id="channel_5" name="channel_5">
              <option value="36" <?= $router->channel_5 == 36 ? 'selected="selected"' : ''; ?>>36</option>
              <option value="44" <?= $router->channel_5 == 44 ? 'selected="selected"' : ''; ?>>44</option>
              <option value="149" <?= $router->channel_5 == 149 ? 'selected="selected"' : ''; ?>>149</option>
              <option value="157" <?= $router->channel_5 == 157 ? 'selected="selected"' : ''; ?>>157</option>
            </select>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>Enclosed: </strong></div>
          <div class="col-6"><input type="text" id="enclosed" name="enclosed" class="form-control"
              value="<?= $router->enclosed ?>" /></div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>Notes: </strong></div>
          <div class="col-6"><input type="text" id="notes" name="notes" class="form-control"
              value="<?= $router->notes ?>" /></div>
        </div>

      </div>

      <div class="modal-footer">
        <button id="btnSubmitRouterForm"
          class="btn btn-primary"><?= $currentRequest->IsUpdateRequest() ? 'Edit' : 'Add' ?></button>
        <button id="btnCancelRouterForm" class="btn btn-dark" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>


</form>