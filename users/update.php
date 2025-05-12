<?php
require_once "../database/Database.php";
require_once "../models/User.php";
include '../layout/header.php';

$database = new Database();
$db = $database->getConnection();
User::setConnection($db);

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

if (!empty($_POST['password'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'email' => $_POST['email'],
            'role' => $_POST['role'],
            'status' => $_POST['status']
        ];
    }
}else{
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'role' => $_POST['role'],
        'status' => $_POST['status']
    ];
}
    if ($user->update($data)) {
        echo '<script>
                Swal.fire({
                    title: "Success!",
                    text: "User updated successfully.",
                    icon: "success",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location.href = "index.php";
                });
              </script>';
    } else {
        echo '<script>
                Swal.fire({
                    title: "Error!",
                    text: "Failed to update user. Please try again.",
                    icon: "error",
                    confirmButtonText: "Ok"
                });
              </script>';
    }

?>

<div class="container-xxl p-4 shadow rounded mt-4">
    <h1 class="text-center mb-4">Edit User</h1>

    <form action="update.php?id=<?= $user->id ?>" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= $user->first_name ?>" required>
            </div>
            <div class="col-md-6">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= $user->last_name ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $user->email ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password">
                <small class="text-muted">Leave blank if you do not want to change the password.</small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="superadmin" <?= $user->role == 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                    <option value="admin" <?= $user->role == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="librarian" <?= $user->role == 'librarian' ? 'selected' : '' ?>>Librarian</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active" <?= $user->status == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $user->status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary w-100">Update</button>
            </div>
        </div>
    </form>
</div>

<?php include '../layout/footer.php'; ?>
