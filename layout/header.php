<?php
  session_start(['cookie_lifetime'=> 86400]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Datatables CSS -->
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.2/af-2.7.0/b-3.2.2/b-colvis-3.2.2/b-html5-3.2.2/b-print-3.2.2/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.4/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.2/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css" rel="stylesheet" integrity="sha384-6gM1RUmcWWtU9mNI98EhVNlLX1LDErxSDu2o/YRIeXq34o77tQYTXLzJ/JLBNkNV" crossorigin="anonymous">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/Library_Management_System/style.css">
</head>
<body style="background-color:#CAE0BC">
   
<nav class="navbar navbar-expand-lg bg-#CAE0BC">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">Library</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link <?=dirname($_SERVER['REQUEST_URI']) == '/Library_Management_System' ? 'active text-primary fw-bold' : null ?>" href="/Library_Management_System/index.php"><span>Dashboard</span></a>

        <a class="nav-link <?=dirname($_SERVER['REQUEST_URI']) == '/Library_Management_System/Book_Management' ? 'active text-primary fw-bold' : ''; ?>" href="/Library_Management_System/Book_Management/index.php">
          <?= isset($_SESSION['role']) && ($_SESSION['role'] === 'student' || $_SESSION['role'] === 'staff' || $_SESSION['role'] === 'others') ? 'Books' : 'Manage books' ?>
        </a>

        <a class="nav-link <?=dirname($_SERVER['REQUEST_URI']) == '/Library_Management_System/User_Management' ? 'active text-primary fw-bold' : ''; ?>" href="/Library_Management_System/User_Management/index.php">
          <?= isset($_SESSION['role']) && ($_SESSION['role'] === 'student' || $_SESSION['role'] === 'staff' || $_SESSION['role'] === 'others') ? 'My Profile' : 'User Management' ?>
        </a>

        <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin')): ?>
          <a class="nav-link <?= dirname($_SERVER['REQUEST_URI']) == '/Library_Management_System/Transaction' ? 'active text-primary fw-bold' : '' ?>" href="/Library_Management_System/Transaction/index.php">Transactions</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'superadmin'): ?>
          <a class="nav-link <?=dirname($_SERVER['REQUEST_URI']) == '/Library_Management_System/Admin_Management' ? 'active text-primary fw-bold' : ''; ?>" href="/Library_Management_System/Admin_Management/index.php">Admin Management</a>
        <?php endif; ?>
      </div>

      <div class="ms-auto">
        <a href="/Library_Management_System/auth/logout.php" class="btn btn-danger">Logout</a>
      </div>
    </div>
  </div>
</nav>

<?php include 'footer.php'?>
