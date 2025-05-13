<?php
require_once '../database/Database.php';
require_once '../models/User.php';
include '../layout/header.php'; 

$database = new Database();
$db = $database->getConnection();
User::setConnection($db);
$id = $_GET['id'] ?? null;

$user = User::find($id);


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

if ($_SESSION['role'] !== 'superadmin' && $_SESSION['role'] !== 'admin') {
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
?>
    <div class="container py-5 d-flex justify-content-center align-items-center">
        <div class="card shadow-lg p-4" style="width: 50%;">
        <div class="card-body">
            <h1 class="text-center fw-bold text-primary mb-4">Create Admin User</h1>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger" role="alert">';
                echo $_SESSION['error'];
                echo '</div>';
                unset($_SESSION['error']);
            }
            ?>
            <form action="store.php" method="POST">
                <div class="mb-3">
                    <label for="name">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Full Name" required>
                </div>
                <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" id="phone_number" required>
                </div>
                <div class="mb-3">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="role">Role</label>
                    <select type="text" class="form-select" id="role" name="role" required>
                        <option value="">-- Select Role --</option>
                        <option value="superadmin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="librarian">Librarian</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="status">Status</label>
                    <select type="text" class="form-select" id="account_status" name="account_status" required>
                        <option value="">-- Select Status --</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button class="btn btn-primary">Create Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include '../layout/footer.php'; ?>