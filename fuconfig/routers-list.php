<?php

include_once ('FUConfig.php');

if (isset($currentOrg) == false) {
  $currentRequest = new PageRequest($_REQUEST);
  $tempRouter = new Router();
  $tempRouter->LoadFromDB($currentRequest->GetID());

  $currentOrg = new Org();
  $currentOrg->LoadFromDB($tempRouter->org_id);

}

$routerList = new RouterList();
$routerList->LoadByOrgId($currentOrg->org_id);


?>


<?php
if ($routerList->GetCount() > 0) { ?>
  <?php
  foreach ($routerList->GetList() as $router) {
    $status = "";

    if (
      $router->channel_24 == $router->channel_24_current and
      $router->channel_5 == $router->channel_5_current
    ) {
      $status = "Channels up to date.";
    } else {
      $status = "Channels not up to date. Router needs updating.";
    }


    ?>
    <div class="row">
      <div class="col" id="router_<?= $router->router_id ?>">
        <div class="row bg-secondary rounded text-light ml-4 mt-1">
          <div class="col-auto">
            <?php if ($router->router_is_deployed == 0) { ?>
              <button id="btnRouterDeployed<?= $router->router_id ?>"
                onClick="updateDeployedRouter(<?= $router->router_id ?>)" class="btn btn-warning p-2 m-1 float-right">Not
                Deployed</button>
            <?php } else if ($router->router_is_deployed == 1) { ?>
                <button id="btnRouterDeployed<?= $router->router_id ?>"
                  onClick="updateDeployedRouter(<?= $router->router_id ?>)"
                  class="btn btn-danger p-2 m-1 float-right">Deployed</button>
            <?php } else { ?>
                <button id="btnRouterDeployed<?= $router->router_id ?>"
                  onClick="updateDeployedRouter(<?= $router->router_id ?>)"
                  class="btn btn-success p-2 m-1 float-right">Returned</button>
            <?php } ?>
          </div>

          <div class="col-auto">
            <div class="row">
              <div class="col-sm-auto">
                <strong>Router: </strong><?= $router->number ?>
              </div>
              <div class="col-sm-auto">
                <strong>IP: </strong><?= $router->GetIP() ?>
              </div>
              <div class="col-sm-auto">
                <strong>Channel Settings: </strong><?= $router->channel_24 ?> / <?= $router->channel_5; ?>
              </div>
              <div class="col-sm-auto">
                <strong>Enclosed: </strong><?= $router->enclosed ?>
              </div>
              <div class="col-sm-auto">
                <strong>Notes: </strong><?= $router->notes ?>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-auto">
                <strong>Status: </strong> <?= $status ?>
              </div>
            </div>
          </div>

          <div class="col-auto">

          </div>

          <div class="col">
            <div class="row justify-content-end px-4 pt-1">
              <a id="btnChangeChannels" class="btn btn-success p-1 m-1" onClick="editRouter(<?= $router->router_id ?>)">Set
                Channels</a>
              <a id="btnUpdateRouter" class="btn btn-info p-1 m-1"
                onClick="processRouterUpdate(<?= $router->router_id ?>)">Update Router</a>
              <a id="btnRemoveRouter" class="btn btn-warning p-1 m-1"
                onClick="removeRouter(<?= $router->router_id ?>)">Remove Router</a>
            </div>
          </div>


        </div>
      </div>
    </div>

    <?php
  }

  ?>

<?php } else { ?>
  <div class="row alert-warning p-1 rounded">
    <strong>No Routers Added</strong>
  </div>
<?php } ?>