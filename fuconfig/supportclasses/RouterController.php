<?php

class RouterController extends Controller
{

  private $pageRequest = null;

  public function ProcessRequest($pageRequest)
  {
    $this->pageRequest = $pageRequest;

    $this->error = "";

    if ($pageRequest->IsUpdateRequest()) {
      $this->router_id = $pageRequest->router_id;
      $this->ProcessUpdateRequest($pageRequest);
    }

    if ($pageRequest->IsCreateRequest()) {
      $this->ProcessCreateRequest($pageRequest);
    }
  }

  //TODO: Cleanup code duplication.
  private function ProcessUpdateRequest($pageRequest)
  {
    if ($pageRequest->request == "deployed") {
      //Switch the deployed flags.
      $this->UpdateDeployed($pageRequest->GetID());

    } else if ($pageRequest->request == "edit") {
      //Traditional update request.
      $router = new Router();
      $router->LoadFromDB($pageRequest->router_id);

      $this->request = "edit";

      $this->UpdateRouter($router);

    } else if ($pageRequest->request == "remove") {
      //Request to remove Org assignment.
      $router = new Router();
      $router->LoadFromDB($pageRequest->router_id);

      $router->org_id = null;
      $router->SaveToDB();

      $this->request = "remove";
    }
  }

  private function ProcessCreateRequest()
  {
    $router = new Router();

    $this->request = "create";

    $this->UpdateRouter($router);
  }

  private function UpdateDeployed($router_id)
  {
    $router = new Router();
    $router->LoadFromDB($router_id);

    $router->router_is_deployed = ($router->router_is_deployed + 1) % 3;
    $router->SaveToDB();

    $this->router_id = $router_id;
    $this->router_is_deployed = $router->router_is_deployed;
  }

  private function UpdateRouter(&$router)
  {
    $router->channel_24 = $this->pageRequest->channel_24;
    $router->channel_5 = $this->pageRequest->channel_5;
    $router->number = $this->pageRequest->number;
    $router->enclosed = $this->pageRequest->enclosed;
    $router->notes = $this->pageRequest->notes;

    $router->SaveToDB();
    $this->saved = 1;
    $this->router = $router->router_id;
  }
}


?>