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

$db = new Database();
$conn = $db->getConnection();
Book::setConnection($conn);

$data = [
    'title' => $_POST['title'],
    'author' => $_POST['author'],
    'category' => $_POST['category'], // Corrected column name
    'published_year' => $_POST['published_year'],
    'total_copies' => $_POST['total_copies'],
    'available_copies' => $_POST['available_copies']
];

$book = Book::create($data);

if ($book) {
    echo "<script>Swal.fire('Success', 'Book added!', 'success').then(() => { window.location.href = 'index.php'; });</script>";
} else {
    echo "<script>Swal.fire('Error', 'Failed to add book.', 'error');</script>";
}

