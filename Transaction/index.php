<?php
require_once '../layout/header.php';
require_once '../database/Database.php';
require_once '../models/Transaction.php';
require_once '../models/Book.php';
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
$db = new Database();
$conn = $db->getConnection();
Transaction::setConnection($conn);
Book::setConnection($conn);
User::setConnection($conn);

Transaction::updateOverdue(date('Y-m-d H:i:s'));

$transactions = Transaction::all();
?>

<div class="container py-5">
    <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary mb-0">Manage Transactions</h2>
                <div>
                    <a href="borrow_form.php" class="btn btn-primary me-2">Borrow Book</a>
                    <a href="return_form.php" class="btn btn-success me-2">Return Book</a>
                    <a href="../reports/export_transaction.php" class="btn btn-warning me-2">Export Transactions</a>
                    <a href="overdue.php" class="btn btn-danger">View Overdue Books</a>
                </div>
            </div>
            <table id="transactionTable" class="table table-striped table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Book Title</th>
                        <th>Borrower</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($transactions): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <?php
                            $book = Book::find($transaction->book_id);
                            $user = User::find($transaction->user_id);

                            $status = 'borrowed';
                            $color = 'bg-primary';
                            $currentDate = date('Y-m-d H:i:s'); 
                            if ($transaction->return_date) {
                                $status = 'returned';
                                $color = 'bg-success';
                            } elseif ($transaction->status === 'overdue' ) { 
                                $status = 'overdue';
                                $color = 'bg-danger';
                            }
                            ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $book->title ?></td>
                                <td><?= $user->name ?></td>
                                <td><?= $transaction->borrow_date ?></td>
                                <td><?= $transaction->due_date ?></td>
                                <td><?= $transaction->return_date ?? 'Not returned' ?></td>
                                <td>
                                    <span class="badge <?= $color ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No transactions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'datatables.php'; 
include '../layout/footer.php';

 ?>
