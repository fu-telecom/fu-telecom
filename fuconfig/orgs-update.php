<?php

include_once ('FUConfig.php');

main();

function main(): void
{
  $request = new PageRequest($_REQUEST);
  $org = loadOrgFromRequest($request);

  if ($request->IsUpdateRequest()) {
    handleUpdateRequest($org);
  } else if ($request->IsCreateRequest()) {
    handleCreateRequest($org);
  } else if ($request->IsDeleteRequest()) {
    handleDeleteRequest($request, $org);
  } else {
    handleUnknownRequest();
  }

  Redirect('/index.php');
}

function handleUpdateRequest(Org $org): void
{
  $org->SaveToDB();
}

function handleCreateRequest(Org $org): void
{
  $org->SaveToDB();
}

function handleDeleteRequest(PageRequest $request, Org $org): void
{
  $org_id = $request->GetID();
  throwIfOrgHasPhonesAssigned($org_id);
  throwIfOrgHasRoutersAssigned($org_id);

  $org->LoadFromDB($org_id);
  $org->DeleteFromDB();
}

function handleUnknownRequest(): void
{
  throwErrorWithMessage('Invalid request type');
}

function throwIfOrgHasPhonesAssigned(string $org_id): void
{
  $phoneList = new PhoneList();
  $phoneList->LoadOrgPhones($org_id);

  if ($phoneList->GetCount() > 0) {
    throwErrorWithMessage('Cannot delete org with assigned phones');
  }
}
function throwIfOrgHasRoutersAssigned(string $org_id): void
{
  $routerList = new RouterList();
  $routerList->LoadByOrgId($org_id);

  if ($routerList->GetCount() > 0) {
    throwErrorWithMessage('Cannot delete org with assigned routers');
  }
}

function loadOrgFromRequest(PageRequest $request): Org
{
  $org = new Org();
  $org->LoadFromPageRequest($request);
  return $org;
}

function throwErrorWithMessage(string $message): void
{
  $trace = debug_backtrace();
  trigger_error(
    $message . ':' .
    ' in ' . $trace[0]['file'] .
    ' on line ' . $trace[0]['line'],
    E_USER_ERROR
  );
}
