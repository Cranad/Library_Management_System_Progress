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

<div class="container py-5">
    <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-center fw-bold text-primary"><?= ($_SESSION['role'] === 'superadmin' || $_SESSION['role'] === 'admin' ) ? 'Manage Books' : 'Available Books' ?></h2>
                <?php if ($_SESSION['role'] === 'superadmin' || $_SESSION['role'] === 'admin'): ?>
                    <div>
                        <a href="create.php" class="btn btn-primary me-2">Add New Book</a>
                        <a href="../reports/export_books.php" class="btn btn-success me-2">Export to PDF</a>
                        <?php if ($_SESSION['role'] === 'superadmin'): ?>
                            <a href="archived_books.php" class="btn btn-secondary">Archived Books</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <table id="booksTable" class="table table-striped table-hover text-center align-middle">
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
                            <?php if ($book->status !== 'archived'): ?>
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
                                            <a href="view.php?id=<?= $book->id ?>" class="btn btn-info btn-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="22.5" viewBox="0 0 576 512"><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                                            </a>
                                            <a href="edit.php?id=<?= $book->id ?>" class="btn btn-primary btn-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="22.5" viewBox="0 0 512 512"><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z"/></svg>
                                            </a>
                                            <?php
                                                $allowed_to_archive = $book->total_copies == $book->available_copies;
                                            ?>
                                            <?php if ($allowed_to_archive): ?>
                                                <a href="archive.php?id=<?= $book->id ?>" class="btn btn-danger btn-sm" onclick="event.preventDefault();
                                                    Swal.fire({
                                                        title: 'Are you sure?',
                                                        text: 'Are you sure you want to archive this book?',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#d33',
                                                        cancelButtonColor: '#3085d6',
                                                        confirmButtonText: 'Yes, archive it!'
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            window.location.href = 'archive.php?id=<?= $book->id ?>';
                                                        }
                                                    }); return false;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="20" width="22.5"viewBox="0 0 512 512"><path d="M32 32l448 0c17.7 0 32 14.3 32 32l0 32c0 17.7-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96L0 64C0 46.3 14.3 32 32 32zm0 128l448 0 0 256c0 35.3-28.7 64-64 64L96 480c-35.3 0-64-28.7-64-64l0-256zm128 80c0 8.8 7.2 16 16 16l160 0c8.8 0 16-7.2 16-16s-7.2-16-16-16l-160 0c-8.8 0-16 7.2-16 16z"/></svg>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No books found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'datatables.php'; ?>
<?php include '../layout/footer.php'; ?>