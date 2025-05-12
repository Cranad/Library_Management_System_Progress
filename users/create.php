<?php
require_once '../database/Database.php';
require_once '../models/User.php';
include '../layout/header.php';

$database = new Database();
$conn = $database->getConnection();

// pag di logged, di maaaccess yung page
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

//para super admin lang maka acces ng file nato
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
User::setConnection($conn);
?>


<div class="container-xxl p-4 shadow rounded mt-4">
    <h1 class="text-center mb-4">Create User</h1>

    <form action="store.php" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="first_name">Full Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter Full"required>
            </div>
            <div class="col-md-6">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="superadmin">Superadmin</option>
                    <option value="admin">Admin</option>
                    <option value="librarian">Librarian</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary w-100">Create</button>
            </div>
        </div>
    </form>
</div>

<?php include '../layout/footer.php'; ?>