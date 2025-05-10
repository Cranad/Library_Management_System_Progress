<?php
require_once '../layout/header.php';
require_once '../database/Database.php';
require_once '../models/Book.php';
require_once '../models/Transaction.php';
require_once '../models/User.php';

if (!isset($_SESSION['email'])) {
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
Book::setConnection($conn);
Transaction::setConnection($conn);
User::setConnection($conn);

$books = Book::all();
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= ($_SESSION['role'] === 'superadmin' || $_SESSION['role'] === 'admin' ) ? 'Manage Books' : 'Available Books' ?></h2>
        <?php if ($_SESSION['role'] === 'superadmin' || $_SESSION['role'] === 'admin'): ?>
            <div>
                <a href="create.php" class="btn btn-primary me-2">Add New Book</a>
                <a href="../reports/book_borrower.php" class="btn btn-success">Export Books and Borrowers</a>
            </div>
        <?php endif; ?>
    </div>

    <table id="booksTable"class="table table-striped table-hover text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Published Year</th>
                <th>Total Copies</th>
                <th>Available Copies</th>
                <?php if ($_SESSION['role'] === 'superadmin' || $_SESSION['role'] === 'admin'): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($books): ?>
                <?php $i = 1; ?>
                <?php foreach ($books as $book): ?>
                    <?php $categoryName = Book::CategoryName($book->category); ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $book->title ?></td>
                        <td><?= $book->author ?></td>
                        <td><?= $categoryName ?></td>
                        <td><?= $book->published_year ?></td>
                        <td><?= $book->total_copies ?></td>
                        <td><?= $book->available_copies ?></td>
                        <?php if ($_SESSION['role'] === 'superadmin' || $_SESSION['role'] === 'admin' ): ?>
                            <td>
                                <a href="view.php?id=<?= $book->id ?>" class="btn btn-info btn-sm">View</a>
                                <a href="edit.php?id=<?= $book->id ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="destroy.php?id=<?= $book->id ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= ($_SESSION['role'] === 'superadmin') ? '8' : '7' ?>" class="text-center">No books found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($_SESSION['role'] === 'superadmin' || $_SESSION['role'] === 'admin'): ?>
        <div class="mt-4">
            <a href="export_books.php" class="btn btn-success">Export to PDF</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../layout/footer.php'; ?>