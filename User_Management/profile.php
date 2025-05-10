<?php
require_once '../Database/Database.php';
require_once '../Models/User.php';
include '../layout/header.php';

if (!isset($_SESSION['email'])) {
    header('Location: ../auth/login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();
User::setConnection($conn);

$currentUser = User::findEmail($_SESSION['email']);
$id = $_GET['id'] ?? null;
$user = User::find($id);
if (!$currentUser || ($currentUser->role !== 'student' && $currentUser->role !== 'staff' && $currentUser->role !== 'others')) {
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

<div class="container p-4 shadow rounded bg-light" style="max-width: 600px; margin: auto;">
    <div class="text-center">
        <h3 class="fw-bold text-primary">MY PROFILE</h3>
    </div>
    <form action="update.php?id=<?= $id ?>" method="POST">
        <div class="mb-3">
            <label for="name" class="form-label text-secondary">Full Name</label>
            <input type="text" class="form-control border-primary" id="name" name="name" value="<?= $currentUser->name ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label text-secondary">Email</label>
            <input type="email" class="form-control border-primary" id="email" name="email" value="<?= $currentUser->email ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone_number" class="form-label text-secondary">Phone Number</label>
            <input type="text" class="form-control border-primary" id="phone_number" name="phone_number" value="<?= $currentUser->phone_number ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label text-secondary">New Password (optional)</label>
            <input type="password" class="form-control border-primary" id="password" name="password" placeholder="Leave blank to keep current password.">
        </div>
        <div class="d-flex justify-content-between">
            <a href="index.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
</div>

<?php include '../layout/footer.php'; ?>
