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


if (!$bookData) {
    echo "<script>
        Swal.fire({
            title: 'Error!',
            text: 'Book not found.',
            icon: 'error',
            confirmButtonText: 'Ok'
        }).then(function() {
            window.location.href = 'index.php';
        });
    </script>";
    require_once '../layout/footer.php';
    exit;
}

$transactions = Transaction::getBookId($bookId);
?>

<div class="container py-5">
    <div class="card shadow-lg p-4 rounded-4 mx-auto" style="max-width: 600px;">
        <h2 class="text-center fw-bold text-primary">Book Details</h2>
        <hr>
        <div class="table-responsive">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <th>Title</th>
                        <td><?= $book->title ?></td>
                    </tr>
                    <tr>
                        <th>Author</th>
                        <td><?= $book->author ?></td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td><?= $book->category ?></td>
                    </tr>
                    <tr>
                        <th>Published Year</th>
                        <td><?= $book->published_year ?></td>
                    </tr>
                    <tr>
                        <th>Total Copies</th>
                        <td><?= $book->total_copies ?></td>
                    </tr>
                    <tr>
                        <th>Available Copies</th>
                        <td><?= $book->available_copies ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-lg p-4 rounded-4 mx-auto mt-4">
        <h2 class="text-center fw-bold text-primary">Borrowing History</h2>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">Borrower</th>
                        <th class="text-center">Borrow Date</th>
                        <th class="text-center">Due Date</th>
                        <th class="text-center">Return Date</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($transactions): ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <?php 
                                $borrower = User::find($transaction->user_id);
                                $status = 'borrowed';
                                $statusClass = 'text-primary fw-bold';
                                if ($transaction->return_date) {
                                    $status = 'returned';
                                    $statusClass = 'text-success fw-bold';
                                } elseif (strtotime($transaction->due_date) < time()) {
                                    $status = 'overdue';
                                    $statusClass = 'text-danger fw-bold';
                                }
                            ?>
                            <tr>
                                <td class="text-center"><?= $borrower->name?></td>
                                <td class="text-center"><?= date('M d, Y h:i A', strtotime($transaction->borrow_date)) ?></td>
                                <td class="text-center"><?= date('M d, Y h:i A', strtotime($transaction->due_date)) ?></td>
                                <td class="text-center"><?= $transaction->return_date ? date('M d, Y h:i A', strtotime($transaction->return_date)) : 'Not Returned' ?></td>
                                <td class="text-center">
                                    <span class="px-2 py-1 rounded 
                                    <?php if ($status === 'returned'): ?>bg-success-subtle text-success fw-bold
                                        <?php elseif ($status === 'overdue'): ?>bg-danger-subtle text-danger fw-bold
                                            <?php else: ?>bg-primary-subtle text-primary fw-bold
                                                <?php endif; ?>
                                    ">
                                    <?= ucfirst($status) ?>
                                </span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">No borrow history found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mb-4 p-4">
        <a href="index.php" class="btn btn-secondary">Back</a>
    </div>
</div>

<?php require_once '../layout/footer.php'; ?>
