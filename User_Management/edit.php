<?php 
require_once '../database/Database.php';
require_once '../models/User.php';
include '../layout/header.php'; 

if (!isset($_SESSION['email'])) {
    header('Location: ../auth/login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();
User::setConnection($conn);

$currentUser = User::findEmail($_SESSION['email']);
$id = $_GET['id'] ?? null;
$user = User::find($id);


if (!$user) {
    http_response_code(404);
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
<div class="container p-4 shadow rounded" style="max-width: 600px; margin: auto;">
    <div class="text-center">
        <h3 class="fw-bold">EDIT BORROWER</h3>
    </div>
    <form action="update.php?id=<?= $id ?>" method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= $user->name ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= $user->email ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= $user->phone_number ?>" required>
        </div>
        <div class="mb-3">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password.">
        </div>
        <div class="mb-3">
            <label for="role">Role</label>
            <select class="form-select" id="role" name="role" required>
                <option value="student" <?= $user->role == 'student' ? 'selected' : '' ?>>Student</option>
                <option value="staff" <?= $user->role == 'staff' ? 'selected' : '' ?>>Staff</option>
                <option value="others" <?= $user->role == 'others' ? 'selected' : '' ?>>Others</option>
            </select>
        </div>
        <div class="d-flex justify-content-between">
            <a href="index.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update User</button>
        </div>
    </form>
</div>

<?php include '../layout/footer.php'; ?>
