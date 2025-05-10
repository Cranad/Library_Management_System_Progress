<?php
include '../layout/header.php';
require_once '../database/Database.php';
require_once '../models/Book.php';

$db = new Database();
$conn = $db->getConnection();

Book::setConnection($conn);

$id = $_GET['id'];
$book = Book::find($id);



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
if (!isset($_GET['id'])) {
    echo "<script>
        Swal.fire({
            title: 'Error',
            text: 'Missing book ID.',
            icon: 'error',
            confirmButtonText: 'Ok'
        }).then(() => window.location.href='index.php');
    </script>";
    exit();
}

if (!$book) {
    echo "<script>
        Swal.fire({
            title: 'Error',
            text: 'Book not found.',
            icon: 'error',
            confirmButtonText: 'Ok'
        }).then(() => window.location.href='index.php');
    </script>";
    exit();
}

// Soft delete the book by updating its status
$book->status = 'archived';
if ($book->save()) {
    echo "<script>
        Swal.fire({
            title: 'Success',
            text: 'Book archived successfully.',
            icon: 'success',
            confirmButtonText: 'Ok'
        }).then(() => window.location.href='index.php');
    </script>";
} else {
    echo "<script>
        Swal.fire({
            title: 'Error',
            text: 'Failed to archive book. Please try again.',
            icon: 'error',
            confirmButtonText: 'Ok'
        }).then(() => window.location.href='index.php');
    </script>";
}
?>
