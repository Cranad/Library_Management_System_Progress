<?php
require_once '../database/Database.php';
require_once '../models/User.php';
include '../layout/header.php';

$database = new Database();
$db = $database->getConnection();
User::setConnection($db);

$id = $_GET['id'] ?? null;

$user = User::find($id);
if (!$user) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "User not found.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location.href = "index.php";
            });
          </script>';
    exit();
}

if ($_SESSION['role'] !== 'superadmin') {
    http_response_code(403);
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "No user access.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location.href = "../index.php";
            });
          </script>';
    exit();
}
if (!isset($_SESSION['email'])) {
    http_response_code(404);
    echo '<script>
    Swal.fire({
        title: "Error!",
        text: "Login first!",
        icon: "error",
        confirmButtonText: "Ok"
    }).then(function() {
        window.location.href = "../auth/login.php";
    });
    </script>';
    exit();
}

?>

<div class="container-xxl p-4 shadow rounded">
    <h1 class="text-center">User Profile</h1>
    <div class="mt-4 d-flex justify-content-between">
        <a href="index.php" class="btn btn-secondary">Back to Index</a>
        <a href="../reports/user_profile.php?id=<?= $user->id ?>" target="_blank" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="22.5" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M0 64C0 28.7 28.7 0 64 0L224 0l0 128c0 17.7 14.3 32 32 32l128 0 0 128-168 0c-13.3 0-24 10.7-24 24s10.7 24 24 24l168 0 0 112c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 64zM384 336l0-48 110.1 0-39-39c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l80 80c9.4 9.4 9.4 24.6 0 33.9l-80 80c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l39-39L384 336zm0-208l-128 0L256 0 384 128z"/></svg>
        </a> 
    </div>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $user->id ?></td>
                <td><?= $user->name ?></td>
                <td><?= $user->email ?></td>
                <td><?= $user->phone_number ?></td>
                <td>
                    <span class="badge <?= $user->account_status === 'active' ? 'bg-success' : 'bg-danger' ?>">
                        <?= ucfirst($user->account_status) ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php include '../layout/footer.php'; ?>
