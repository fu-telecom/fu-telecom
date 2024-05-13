<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title><?php echo isset($pageTitle) ? $pageTitle : "FU Config"; ?></title>

  <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/vendor/jquery/css/jquery-ui.min.css" rel="stylesheet">
  <link href="/css/fuconfig.css" rel="stylesheet">

  <script src="vendor/jquery/js/jquery-3.3.1.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery/js/jquery-ui.min.js"></script>

  <script src="/js/number-add-existing.js"></script>
  <script src="/js/number-edit.js"></script>
  <script src="/js/router-edit.js"></script>
  <script src="/js/index.js"></script>
</head>

<body>

  <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
    <a class="navbar-brand" href="http://fuconfig.fuckyou/">FU Config</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavBar"
      aria-controls="mainNavBar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavBar">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="inventory.php">Inventory</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="dropdown03" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">Admin Functions</a>
          <div class="dropdown-menu" aria-labelledby="dropdown03">
            <a class="dropdown-item" href="#" onClick="ResetAllAsteriskData()">Reset All Asterisk Data</a>
          </div>
        </li>
      </ul>
      <form class="form-inline my-w my-lg-0">
        <a class="btn btn-success my-2 my-sm-0" onClick="processPhonesToAsterisk()">Process Phones</a>
      </form>
    </div>
  </nav>