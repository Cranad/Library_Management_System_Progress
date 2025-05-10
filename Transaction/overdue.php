<?php
require_once '../database/Database.php';
require_once '../models/Transaction.php';
require_once '../models/Book.php';
require_once '../models/User.php';
require_once '../models/Penalty.php';
include '../layout/header.php';

$db = new Database();
$conn = $db->getConnection();
Book::setConnection($conn);
User::setConnection($conn);
Transaction::setConnection($conn);

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





Transaction::getOverdue();
$overdueTransactions = Transaction::getOverdue();

?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Overdue Books</h2>
    <table class="table table-striped table-hover text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Book Title</th>
                <th>Borrower</th>
                <th>Due Date</th>
                <th>Days Late</th>
                <th>Penalty</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($overdueTransactions): ?>
                <?php $i = 1; ?>
                <?php foreach ($overdueTransactions as $transaction): ?>
                    <?php
                    $book = Book::find($transaction->book_id);
                    $user = User::find($transaction->user_id);
                    $penalty = Transaction::calculatePenalty($transaction->id);
                    $daysLate = ceil((strtotime(date('Y-m-d')) - strtotime($transaction->due_date)) / (60 * 60 * 24));
                    ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $book->title ?></td>
                        <td><?= $user->name ?></td>
                        <td><?= $transaction->due_date ?></td>
                        <td><?= $daysLate ?></td>
                        <td>₱<?= $penalty ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No overdue books found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="mt-4">
        <a href="index.php" class="btn btn-secondary">Back</a>
    </div>
</div>

<?php include '../layout/footer.php'; ?>