<?php
require_once '../layout/header.php';
require_once '../database/Database.php';
require_once '../models/Book.php';
require_once '../models/Transaction.php';
require_once '../models/User.php';


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
    echo "<div class='alert alert-danger'>No book ID provided.</div>";
    require_once '../layout/footer.php';
    exit;
}

$bookId = $_GET['id'];
$db = new Database();
$conn = $db->getConnection();

Book::setConnection($conn);
Transaction::setConnection($conn);
User::setConnection($conn);

$book = Book::find($bookId);

if (!$book) {
    echo "<div class='alert alert-warning'>Book not found.</div>";
    require_once '../layout/footer.php';
    exit;
}

$transactions = Transaction::getBookId($bookId);
?>

<div class="container mt-5">
    <h2>Book Details</h2>

    <?php if ($book): ?>
        <div class="mb-4">
            <p><strong>Title:</strong> <?= $book->title ?></p>
            <p><strong>Author:</strong> <?= $book->author ?></p>
            <p><strong>Category:</strong> <?= $book->category ?></p>
            <p><strong>Published Year:</strong> <?= $book->published_year ?></p>
            <p><strong>Total Copies:</strong> <?= $book->total_copies?></p>
            <p><strong>Available Copies:</strong> <?= $book->available_copies ?></p>
        </div>

        <h4>Borrowing History</h4>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Borrower</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($transactions): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <?php $borrower = User::find($transaction->user_id); ?>
                        <tr>
                            <td><?= $borrower->name ?></td>
                            <td><?= $transaction->borrow_date ?></td>
                            <td><?= $transaction->due_date ?></td>
                            <td>
                                <?= $transaction->return_date ? $transaction->return_date : 'Not returned' ?>
                            </td>
                            <td style="color: <?= $transaction->status === 'overdue' ? 'red' : 'black' ?>">
                                <?= ucfirst($transaction->status) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No borrow history found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    <?php else: ?>
        <div class="alert alert-warning">Book not found.</div>
    <?php endif; ?>
</div>

<?php require_once '../layout/footer.php'; ?>
