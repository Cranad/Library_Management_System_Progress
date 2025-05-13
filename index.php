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
    $students = count(User::findByRole('student')?? []);
    $staff = count(User::findByRole('staff')?? []);
    $admins = count(User::findByRole('admin') ?? []);
    $others = count(User::findByRole('others')??[]);
    $penalty_count = count(Penalty::all() ?? []); 
} elseif ($userRole === 'admin') {
    $borrowers = count(User::findByRole('student')?? []) + count(User::findByRole('staff')?? []) + count(User::findByRole('others')?? []);
    $penalty_count = count(Penalty::all() ?? []); 
} elseif ($userRole === 'student' || $userRole === 'staff' ||$userRole === 'others' ) {
    $borrowed_books_list = Transaction::getUserId($_SESSION['user_id']);
    $available_books = Book::getAvailableBooks(); 
}
?>

<div class="container rounded mt-5 p-4 bg-success-subtle align-items-center">
    <?php if ($userRole === 'superadmin'): ?>
        <h2>Super-Admin Dashboard</h2>
        <div class="row row-cols-1 row-cols-md-4 g-4 mt-4">
            <div class="col">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M96 0C43 0 0 43 0 96L0 416c0 53 43 96 96 96l288 0 32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-64c17.7 0 32-14.3 32-32l0-320c0-17.7-14.3-32-32-32L384 0 96 0zm0 384l256 0 0 64L96 448c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16l192 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16zm16 48l192 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>
                        <h5>Total Books</h5>
                        <h3><?= $book_count ?></h3>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-primary text-dark h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M96 0C60.7 0 32 28.7 32 64l0 384c0 35.3 28.7 64 64 64l288 0c35.3 0 64-28.7 64-64l0-384c0-35.3-28.7-64-64-64L96 0zM208 288l64 0c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16c0-44.2 35.8-80 80-80zm-32-96a64 64 0 1 1 128 0 64 64 0 1 1 -128 0zM512 80c0-8.8-7.2-16-16-16s-16 7.2-16 16l0 64c0 8.8 7.2 16 16 16s16-7.2 16-16l0-64zM496 192c-8.8 0-16 7.2-16 16l0 64c0 8.8 7.2 16 16 16s16-7.2 16-16l0-64c0-8.8-7.2-16-16-16zm16 144c0-8.8-7.2-16-16-16s-16 7.2-16 16l0 64c0 8.8 7.2 16 16 16s16-7.2 16-16l0-64z"/></svg>
                        <h5>Borrowed Books</h5>
                        <h3><?= $borrowed_books ?></h3>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-danger text-dark h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120l0 136c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2 280 120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/></svg>
                        <h5>Overdue Returns</h5>
                        <h3><?= $overdue_returns ?></h3>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-danger text-dark h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 128 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M96 64c0-17.7-14.3-32-32-32S32 46.3 32 64l0 256c0 17.7 14.3 32 32 32s32-14.3 32-32L96 64zM64 480a40 40 0 1 0 0-80 40 40 0 1 0 0 80z"/></svg>                        
                        <h5>Total Penalties</h5>
                        <h3><?= $penalty_count ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-4 g-4 mt-4">
            <div class="col">
                <div class="card bg-success text-dark h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>
                        <h5>Students</h5>
                        <h3><?= $students ?></h3>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-info text-dark h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>
                        <h5>Staff</h5>
                        <h3><?= $staff ?></h3>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-secondary text-dark h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>
                        <h5>Admins</h5>
                        <h3><?= $admins ?></h3>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M211.2 96a64 64 0 1 0 -128 0 64 64 0 1 0 128 0zM32 256c0 17.7 14.3 32 32 32l85.6 0c10.1-39.4 38.6-71.5 75.8-86.6c-9.7-6-21.2-9.4-33.4-9.4l-96 0c-35.3 0-64 28.7-64 64zm461.6 32l82.4 0c17.7 0 32-14.3 32-32c0-35.3-28.7-64-64-64l-96 0c-11.7 0-22.7 3.1-32.1 8.6c38.1 14.8 67.4 47.3 77.7 87.4zM391.2 226.4c-6.9-1.6-14.2-2.4-21.6-2.4l-96 0c-8.5 0-16.7 1.1-24.5 3.1c-30.8 8.1-55.6 31.1-66.1 60.9c-3.5 10-5.5 20.8-5.5 32c0 17.7 14.3 32 32 32l224 0c17.7 0 32-14.3 32-32c0-11.2-1.9-22-5.5-32c-10.8-30.7-36.8-54.2-68.9-61.6zM563.2 96a64 64 0 1 0 -128 0 64 64 0 1 0 128 0zM321.6 192a80 80 0 1 0 0-160 80 80 0 1 0 0 160zM32 416c-17.7 0-32 14.3-32 32s14.3 32 32 32l576 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L32 416z"/></svg>
                        <h5>Others</h5>
                        <h3><?= $others ?></h3>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($userRole === 'admin'): ?>
        <h2>Admin Dashboard</h2>
        <div class="row mt-4 justify-content-center">
            <div class="col-md-4">
                <div class="card bg-warning text-dark m-4">
                    <div class="card-body text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M96 0C43 0 0 43 0 96L0 416c0 53 43 96 96 96l288 0 32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-64c17.7 0 32-14.3 32-32l0-320c0-17.7-14.3-32-32-32L384 0 96 0zm0 384l256 0 0 64L96 448c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16l192 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16zm16 48l192 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>
                        <h5>Total Books</h5>
                        <h3><?= $book_count ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-dark m-4">
                    <div class="card-body text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M96 0C60.7 0 32 28.7 32 64l0 384c0 35.3 28.7 64 64 64l288 0c35.3 0 64-28.7 64-64l0-384c0-35.3-28.7-64-64-64L96 0zM208 288l64 0c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16c0-44.2 35.8-80 80-80zm-32-96a64 64 0 1 1 128 0 64 64 0 1 1 -128 0zM512 80c0-8.8-7.2-16-16-16s-16 7.2-16 16l0 64c0 8.8 7.2 16 16 16s16-7.2 16-16l0-64zM496 192c-8.8 0-16 7.2-16 16l0 64c0 8.8 7.2 16 16 16s16-7.2 16-16l0-64c0-8.8-7.2-16-16-16zm16 144c0-8.8-7.2-16-16-16s-16 7.2-16 16l0 64c0 8.8 7.2 16 16 16s16-7.2 16-16l0-64z"/></svg>
                        <h5>Borrowed Books</h5>
                        <h3><?= $borrowed_books ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-dark m-4">
                    <div class="card-body text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120l0 136c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2 280 120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/></svg>
                        <h5>Overdue Returns</h5>
                        <h3><?= $overdue_returns ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4 justify-content-center">
            <div class="col-md-4">
                <div class="card bg-danger text-dark m-4">
                    <div class="card-body text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 128 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M96 64c0-17.7-14.3-32-32-32S32 46.3 32 64l0 256c0 17.7 14.3 32 32 32s32-14.3 32-32L96 64zM64 480a40 40 0 1 0 0-80 40 40 0 1 0 0 80z"/></svg>                        
                        <h5>Total Penalties</h5>
                        <h3><?= $penalty_count ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-dark m-4">
                    <div class="card-body text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M211.2 96a64 64 0 1 0 -128 0 64 64 0 1 0 128 0zM32 256c0 17.7 14.3 32 32 32l85.6 0c10.1-39.4 38.6-71.5 75.8-86.6c-9.7-6-21.2-9.4-33.4-9.4l-96 0c-35.3 0-64 28.7-64 64zm461.6 32l82.4 0c17.7 0 32-14.3 32-32c0-35.3-28.7-64-64-64l-96 0c-11.7 0-22.7 3.1-32.1 8.6c38.1 14.8 67.4 47.3 77.7 87.4zM391.2 226.4c-6.9-1.6-14.2-2.4-21.6-2.4l-96 0c-8.5 0-16.7 1.1-24.5 3.1c-30.8 8.1-55.6 31.1-66.1 60.9c-3.5 10-5.5 20.8-5.5 32c0 17.7 14.3 32 32 32l224 0c17.7 0 32-14.3 32-32c0-11.2-1.9-22-5.5-32c-10.8-30.7-36.8-54.2-68.9-61.6zM563.2 96a64 64 0 1 0 -128 0 64 64 0 1 0 128 0zM321.6 192a80 80 0 1 0 0-160 80 80 0 1 0 0 160zM32 416c-17.7 0-32 14.3-32 32s14.3 32 32 32l576 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L32 416z"/></svg>
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
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M96 0C60.7 0 32 28.7 32 64l0 384c0 35.3 28.7 64 64 64l288 0c35.3 0 64-28.7 64-64l0-384c0-35.3-28.7-64-64-64L96 0zM208 288l64 0c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16c0-44.2 35.8-80 80-80zm-32-96a64 64 0 1 1 128 0 64 64 0 1 1 -128 0zM512 80c0-8.8-7.2-16-16-16s-16 7.2-16 16l0 64c0 8.8 7.2 16 16 16s16-7.2 16-16l0-64zM496 192c-8.8 0-16 7.2-16 16l0 64c0 8.8 7.2 16 16 16s16-7.2 16-16l0-64c0-8.8-7.2-16-16-16zm16 144c0-8.8-7.2-16-16-16s-16 7.2-16 16l0 64c0 8.8 7.2 16 16 16s16-7.2 16-16l0-64z"/></svg>
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
                        <svg xmlns="http://www.w3.org/2000/svg" height="50" width="" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M249.6 471.5c10.8 3.8 22.4-4.1 22.4-15.5l0-377.4c0-4.2-1.6-8.4-5-11C247.4 52 202.4 32 144 32C93.5 32 46.3 45.3 18.1 56.1C6.8 60.5 0 71.7 0 83.8L0 454.1c0 11.9 12.8 20.2 24.1 16.5C55.6 460.1 105.5 448 144 448c33.9 0 79 14 105.6 23.5zm76.8 0C353 462 398.1 448 432 448c38.5 0 88.4 12.1 119.9 22.6c11.3 3.8 24.1-4.6 24.1-16.5l0-370.3c0-12.1-6.8-23.3-18.1-27.6C529.7 45.3 482.5 32 432 32c-58.4 0-103.4 20-123 35.6c-3.3 2.6-5 6.8-5 11L304 456c0 11.4 11.7 19.3 22.4 15.5z"/></svg>
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