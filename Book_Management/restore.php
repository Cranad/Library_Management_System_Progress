<?php
require_once '../database/Database.php';
require_once '../models/Book.php';

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super-admin') {
    die("Access denied.");
}

if (!isset($_GET['id'])) {
    die("Missing book ID.");
}

$id = $_GET['id'];
$db = new Database();
$conn = $db->getConnection();
$book = new Book($conn);

if ($book->restore($id)) {
    header("Location: archived_books.php");
} else {
    echo "Failed to restore book.";
}
?>
