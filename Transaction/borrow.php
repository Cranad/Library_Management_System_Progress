<?php
require_once '../models/Transaction.php';
require_once '../models/Book.php';
require_once '../database/Database.php';
include '../layout/header.php';

// pag di logged, di maaaccess yung page
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
Transaction::setConnection($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'];
    $borrowerId = $_POST['borrower_id'];
    $borrowDate = date('Y-m-d H:i:s', strtotime($_POST['borrow_date']));
    $dueDate = date('Y-m-d H:i:s', strtotime($_POST['due_date']));

    $book = Book::find($bookId);

    if (!$book || $book->available_copies <= 0) {
        echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Book is not available for borrowing.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.history.back();
            });
        </script>';
        exit();
    }

    $transaction = Transaction::borrowBook($borrowerId, $bookId, $borrowDate, $dueDate);

    if ($transaction) {
        echo '<script>
            Swal.fire({
                title: "Success!",
                text: "Book borrowed successfully.",
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
                text: "Failed to borrow book.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.history.back();
            });
        </script>';
    }
}
?>