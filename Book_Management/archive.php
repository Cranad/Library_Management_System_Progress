<?php
require_once '../database/Database.php';
require_once '../models/Book.php';
include '../layout/header.php';

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

if ($_SESSION['role'] !== 'superadmin' && $_SESSION['role'] !== 'admin' ) {
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

$id = $_GET['id'];
$db = new Database();
$conn = $db->getConnection();
Book::setConnection($conn);

// Get the book first
$bookData = Book::getById($id);
if ($bookData && $bookData->available_copies == $bookData->total_copies) {
    if ($bookData->archive()) {
        echo "<script>
            Swal.fire({
                title: 'Archived!',
                text: 'Book has been successfully archived.',
                icon: 'success',
                confirmButtonText: 'Ok'
            }).then(() => window.location.href='index.php');
        </script>";
    } else {
                echo "<script>
            Swal.fire({
                title: 'Archived!',
                text: 'Failed to archive book.',
                icon: 'error',
                confirmButtonText: 'Ok'
            }).then(() => window.location.href='index.php');
        </script>";
    }
} else {
    echo "<script>
        Swal.fire('Cannot Archive', 'Book is currently borrowed.', 'warning')
            .then(() => window.location.href='index.php');
    </script>";
}
?>
