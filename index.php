<?php
require_once 'database/Database.php';
require_once 'models/Book.php';
require_once 'models/User.php';
require_once 'models/Transaction.php';
require_once 'models/Penalty.php';
include 'layout/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

Book::setConnection($db);
User::setConnection($db);
Transaction::setConnection($db);
Penalty::setConnection($db);

$userRole = $_SESSION['role'] ?? '';
$book_count = Book::countBooks();
$borrowed_books = count(Transaction::getBorrowedBooks());
$overdue_returns = count(Transaction::getOverdueReturns());

$recent_activity = Transaction::all();

if ($userRole === 'superadmin') {
    $students = User::countByRole('student');
    $staff = User::countByRole('staff');
    $admins = User::countByRole('admin');
    $others = User::countByRole('others');
    $penalty_count = count(Penalty::all()); 
} elseif ($userRole === 'admin') {
    $borrowers = User::countByRole('student') + User::countByRole('staff') + User::countByRole('others');
    $penalty_count = count(Penalty::all()); 
} elseif ($userRole === 'student' || $userRole === 'staff' ||$userRole === 'others' ) {
    $borrowed_books_list = Transaction::getUserId($_SESSION['user_id']);
    $available_books = Book::getAvailableBooks(); 
}
?>

<div class="container rounded bg-dark-subtle mt-5 align-items-center">
    <?php if ($userRole === 'superadmin'): ?>
        <h2>Super-Admin Dashboard</h2>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-warning text-dark m-4">
                    <div class="card-body">
                        <h5>Total Books</h5>
                        <h3><?= $book_count ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-dark m-4">
                    <div class="card-body">
                        <h5>Borrowed Books</h5>
                        <h3><?= $borrowed_books ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-dark m-4">
                    <div class="card-body">
                        <h5>Overdue Returns</h5>
                        <h3><?= $overdue_returns ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-dark m-4">
                    <div class="card-body">
                        <h5>Total Penalties</h5>
                        <h3><?= $penalty_count ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-success text-dark m-4">
                    <div class="card-body">
                        <h5>Students</h5>
                        <h3><?= $students ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-dark m-4">
                    <div class="card-body">
                        <h5>Staff</h5>
                        <h3><?= $staff ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-dark m-4">
                    <div class="card-body">
                        <h5>Admins</h5>
                        <h3><?= $admins ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark m-4">
                    <div class="card-body">
                        <h5>Others</h5>
                        <h3><?= $others ?></h3>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($userRole === 'admin'): ?>
        <h2>Admin Dashboard</h2>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-warning text-dark m-4">
                    <div class="card-body">
                        <h5>Total Books</h5>
                        <h3><?= $book_count ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-dark m-4">
                    <div class="card-body">
                        <h5>Borrowed Books</h5>
                        <h3><?= $borrowed_books ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-dark m-4">
                    <div class="card-body">
                        <h5>Overdue Returns</h5>
                        <h3><?= $overdue_returns ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-dark m-4">
                    <div class="card-body">
                        <h5>Total Penalties</h5>
                        <h3><?= $penalty_count ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-success text-dark m-4">
                    <div class="card-body">
                        <h5>Total Borrowers</h5>
                        <h3><?= $borrowers ?></h3>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($userRole === 'student' || $userRole === 'staff' ||$userRole === 'others' ): ?>
        <h2>Borrower Dashboard</h2>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card bg-primary text-dark m-4">
                    <div class="card-body">
                        <h5>Borrowed Books</h5>
                        <table class="table table-striped table-hover text-center align-middle mt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Book Title</th>
                                    <th>Borrow Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($borrowed_books_list): ?>
                                    <?php $i = 1; ?>
                                    <?php foreach ($borrowed_books_list as $transaction): ?>
                                        <?php $book = Book::find($transaction->book_id); ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= $book->title ?></td>
                                            <td><?= $transaction->borrow_date ?></td>
                                            <td><?= $transaction->due_date ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?= $transaction->status === 'overdue' ? 'bg-danger' : 
                                                        ($transaction->status === 'returned' ? 'bg-success' : 'bg-primary') ?>">
                                                    <?= ucfirst($transaction->status) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">No borrowed books found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-success text-dark m-4">
                    <div class="card-body">
                        <h5>Available Books</h5>
                        <table class="table table-striped table-hover text-center align-middle mt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>Available Copies</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($available_books): ?>
                                    <?php $i = 1; ?>
                                    <?php foreach ($available_books as $book): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= $book->title ?></td>
                                            <td><?= $book->author ?></td>
                                            <td><?= $book->category ?></td>
                                            <td><?= $book->available_copies ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">No available books for borrowing.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include 'layout/footer.php'; ?>