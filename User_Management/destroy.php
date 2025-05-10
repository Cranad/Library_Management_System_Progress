<?php 
require_once '../database/Database.php';
require_once '../models/User.php';
include '../layout/header.php';

if (!isset($_SESSION['email'])) {
    header('Location: ../auth/login.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();
User::setConnection($conn);

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

if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    if ($user->delete()) {
        echo '<script>
                Swal.fire({
                    title: "Deleted!",
                    text: "The user record has been deleted.",
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
                    text: "Failed to delete the user record. Please try again.",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location.href = "index.php";
                });
            </script>';
    }
} else {
    echo '<script>
            Swal.fire({
                title: "Are you sure?",
                text: "You are about to delete this user record. This action cannot be undone!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "destroy.php?id=' . $id . '&confirm=yes";
                } else {
                    window.location.href = "index.php";
                }
            });
        </script>';
}
?>

<?php include '../layout/footer.php'; ?>
