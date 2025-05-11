<?php
require_once '../database/Database.php';
require_once '../layout/header.php';
require_once '../models/Book.php';

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
    die("Missing book ID.");
}

$id = $_GET['id'];
$db = new Database();
$conn = $db->getConnection();
Book::setConnection($conn);
$book = Book::find($id);

if ($book && $book->restore()) {
    echo '<script>
        Swal.fire({
            title: "Success!",
            text: "Book successfully restored.",
            icon: "success",
            confirmButtonText: "OK"
        }).then(function() {
            window.location.href = "archived_books.php";
        });
    </script>';
    exit();
} else {
    echo '<script>
        Swal.fire({
            title: "Error!",
            text: "Failed to restore book.",
            icon: "error",
            confirmButtonText: "OK"
        }).then(function() {
            window.location.href = "archived_books.php";
        });
    </script>';
    exit();
}
?>
