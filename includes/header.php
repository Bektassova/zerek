<?php
/*
    This is a partial page, used for the header/nav.
    This page will be included in every other page to have
    the same header/nav everywhere.
*/

// Start session BEFORE any HTML output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Global base path for file links (prevents 404 issues)
if (!isset($BASE_PATH)) {
    $BASE_PATH = "/zerek/";   // local MAMP project folder
}

/*
|--------------------------------------------------------------------------
| Disable caching during development (VALID way)
|--------------------------------------------------------------------------
| NOTE:
| We use real HTTP headers (PHP header()) instead of meta http-equiv,
| because HTML validators flag those meta values as invalid.
| Remove these headers when development is complete.
*/
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2025 - PHP & Datbases</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
          crossorigin="anonymous">
</head>

  <body class="d-flex flex-column min-vh-100">


<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Navbar</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>

        <!-- If user is logged in -> show logout link -->
        <?php if (isset($_SESSION["userId"])) { ?>
          <li class="nav-item">
            <a class="nav-link" href="includes/logout-inc.php">Logout</a>
          </li>
        <?php } else { ?>
          <!-- If user is logged out -> show login link -->
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login</a>
          </li>
        <?php } ?>

      </ul>

      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>

    </div>
  </div>
</nav>
<main class="flex-grow-1">

