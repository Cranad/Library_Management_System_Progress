<?php
require_once '../layout/header.php';
require_once '../database/Database.php';
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
Book::setConnection($conn);
User::setConnection($conn);

$books = Book::getAvailableBooks();


$students = User::findByRole('student');
$staff = User::findByRole('staff');
$others = User::findByRole('others');

$users = array_merge($students ?? [], $staff ?? [], $others ?? []);

?>

<div class="container mt-5 d-flex justify-content-center align-items-center">
    <div class="card shadow-lg p-4" style="width: 50%;">
        <div class="card-body">
            <h2 class="text-center fw-bold text-primary mb-4">Borrow Book</h2>
            <form action="borrow.php" method="POST">
                <div class="mb-3">
                    <label for="book_id" class="form-label">Book</label>
                    <select name="book_id" id="book_id" class="form-control" required>
                        <option value="" disabled selected>Select a book</option>
                        <?php foreach ($books as $book): ?>
                            <option value="<?= $book->id ?>"><?= $book->title ?> (Available: <?= $book->available_copies ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="borrower_id" class="form-label">Borrower</label>
                    <select name="borrower_id" id="borrower_id" class="form-control" required>
                        <option value="" disabled selected>Select a borrower</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user->id ?>"><?= $user->name?> (<?= $user->role ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="borrow_date" class="form-label">Borrow Date</label>
                    <input type="datetime-local" name="borrow_date" id="borrow_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="datetime-local" name="due_date" id="due_date" class="form-control" required>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="index.php" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
