<?php
include '../layout/header.php';
require_once '../database/Database.php';
require_once '../models/User.php';
$database = new Database();
$conn = $database->getConnection();
User::setConnection($conn);

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


$users = User::all();
?>

<h1 class="container-xxl shadow rounder text-center p-4 mt-4 mx-auto">User List</h1>


<div class="container-xxl p-4 shadow rounded mt-4">
    <a href="create.php" class="btn btn-success mb-3">Create User</a>
    <a href="../reports/users_report.php" target="_blank" class="btn btn-primary mb-3 ">Generate All Users Report</a> 
    <a href="../reports/users_report.php?status=active" target="_blank" class="btn btn-info mb-3 ">Generate All Active Users Report</a> 
    <a href="../reports/users_report.php?status=inactive" target="_blank" class="btn btn-danger mb-3 ">Generate All Inactive Users Report</a> 
<table id="usersTable" class="table table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th style="background-color:#FFFACD;">Name</th>
                <th style="background-color:#FFFACD;">Email</th>
                <th style="background-color:#FFFACD;">Role</th>
                <th style="background-color:#FFFACD;">Status</th>
                <th style="background-color:#FFFACD;">Actions</th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td style="background-color: #ADD8E6"><?= $user->first_name . ' ' . $user->last_name ?></td>
                        <td style="background-color: #ADD8E6"><?= $user->email ?></td>
                        <td style="background-color: #ADD8E6"><?= ucfirst($user->role) ?></td>
                        <td style="background-color: #ADD8E6"><?= ucfirst($user->status) ?></td>
                        <td class="text-center" style="background-color: #ADD8E6">
                            <a href="edit.php?id=<?=$user->id ?>" class="btn btn-primary">Edit</a>
                            <a href="destroy.php?id=<?=$user->id ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>