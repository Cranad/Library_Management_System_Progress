<?php
require_once '../database/Database.php';
require_once '../models/Book.php';

session_start();
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'super-admin'])) {
    die("Access denied.");
}

if (!isset($_GET['id'])) {
    die("Missing book ID.");
}

$id = $_GET['id'];
$db = new Database();
$conn = $db->getConnection();
$book = new Book($conn);

// Get the book first
$bookData = $book->getById($id);
if ($bookData && $bookData['available_copies'] == $bookData['total_copies']) {
    if ($book->archive($id)) {
        header("Location: index.php");
    } else {
        echo "Failed to archive book.";
    }
} else {
    echo "<script>
        Swal.fire('Cannot Archive', 'Book is currently borrowed.', 'warning')
            .then(() => window.location.href='index.php');
    </script>";
}
?>
