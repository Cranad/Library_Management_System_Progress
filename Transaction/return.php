<?php
require_once '../models/Transaction.php';
require_once '../models/Book.php';
require_once '../models/Penalty.php';
require_once '../database/Database.php';
include '../layout/header.php';

$db = new Database();
$conn = $db->getConnection();
Transaction::setConnection($conn);
Book::setConnection($conn);
User::setConnection($conn);

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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionId = $_POST['transaction_id'];
    $returnDate = $_POST['return_date'];

    $transaction = Transaction::find($transactionId);

    $penaltyAmount = 0;
    if (strtotime($returnDate) > strtotime($transaction->due_date)) {
        $penaltyAmount = Transaction::calculatePenalty($transactionId);
        $data = [
            'user_id' => $transaction->user_id,
            'transaction_id' => $transactionId,
            'penalty_amount' => $penaltyAmount,
            'penalty_date' => date('Y-m-d')
        ];
        Penalty::recordPenalty($data); 
    }

    $returned = Transaction::returnBook($transactionId, $returnDate);

    if ($returned) {
        if ($penaltyAmount > 0) {
            echo '<script>
                Swal.fire({
                    title: "Book Returned with Penalty!",
                    text: "The book was returned late. A penalty of ₱' . $penaltyAmount . ' has been recorded.",
                    icon: "warning",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location.href = "index.php";
                });
            </script>';
        } else {
            echo '<script>
                Swal.fire({
                    title: "Success!",
                    text: "Book returned successfully.",
                    icon: "success",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location.href = "index.php";
                });
            </script>';
        }
    } else {
        echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Failed to return the book. Please try again.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.history.back();
            });
        </script>';
    }
}

?>