<?php
require_once '../layout/header.php';
require_once '../database/Database.php';
require_once '../models/Transaction.php';
require_once '../models/Book.php';
require_once '../models/User.php';

if (!isset($_SESSION['email']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    echo '<script>
        Swal.fire({
            title: "Access Denied!",
            text: "You do not have permission to access this page.",
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

$students = User::findByRole('student');
$staff = User::findByRole('staff');
$others = User::findByRole('others');

$users = array_merge($students ?? [], $staff ?? [], $others ?? []);
$transactions = [];

foreach ($users as $user) {
    $allTransactions = Transaction::getUserId($user->id);
    foreach ($allTransactions as $transaction) {
        if ($transaction->status === 'borrowed' || $transaction->status === 'overdue') {
            $transactions[] = $transaction;
        }
    }
}
?>

<div class="container mt-5">
    <h2>Return Book</h2>
    <form action="return.php" method="POST">
        <div class="mb-3">
            <label for="transaction_id" class="form-label">Borrowed Book</label>
            <select name="transaction_id" id="transaction_id" class="form-control" required>
                <option value="" disabled selected>Select a borrowed book</option>
                <?php foreach ($transactions as $transaction): ?>
                    <?php
                    $book = Book::find($transaction->book_id);
                    $user = User::find($transaction->user_id);
                    ?>
                    <option value="<?= $transaction->id ?>">
                        <?= $book->title ?> borrowed by <?= $user->name ?> (Due: <?= $transaction->due_date ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="return_date" class="form-label">Return Date</label>
            <input type="datetime-local" name="return_date" id="return_date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?php include '../layout/footer.php'; ?>
