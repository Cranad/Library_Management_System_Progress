<?php
    require_once '../database/Database.php';
    require_once '../models/User.php';
    include '../layout/header.php'; 
    require_once '../models/Transaction.php';
    require_once '../models/Book.php';

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
User::setConnection($conn);

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo "<div class='mx-auto text-center'><h1>400 Bad Request</h1><p>No user ID provided.</p></div>";
    exit;
}


$user = User::find($_GET['id']);


if (!$user) {
    http_response_code(404);
    echo "<div class='mx-auto text-center'><h1>404 Not Found</h1><p>User does not exist.</p></div>";
    exit;
}

// Fetch transactions for the borrower
$transactions = Transaction::getUserId($user->id);
?>

<div class="container py-5">
    <div class="card shadow-lg p-4 rounded-4 mx-auto" style="max-width: 600px;">
        <h2 class="text-center fw-bold text-primary">User Details</h2>
        <hr>
        <div class="table-responsive">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <th>ID</th>
                        <td><?= $user->id ?></td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td><?= $user->name ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $user->email?></td>
                    </tr>
                    <tr>
                        <th>Phone Number</th>
                        <td><?= $user->phone_number ?></td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td><?= $user->role ?></td>
                    </tr>
                    <tr>
                        <th>Account Status</th>
                        <td><?= $user->account_status ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-lg p-4 rounded-4 mx-auto mt-4">
        <h2 class="text-center fw-bold text-primary">Borrowed Books</h2>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Published Year</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($transactions): ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <?php 
                            $book = Book::find($transaction->book_id); 
                            $categoryName = Book::CategoryName($book->category);


                            $status = 'borrowed';
                            $statusClass = 'text-primary fw-bold';
                            if ($transaction->return_date) {
                                $status = 'returned';
                                $statusClass = 'text-success fw-bold';
                            } elseif ($transaction->due_date < date('Y-m-d H:i:s')) {
                                $status = 'overdue';
                                $statusClass = 'text-danger fw-bold';
                            }
                            ?>
                            <tr>
                                <td><?= $book->title ?></td>
                                <td><?= $book->author ?></td>
                                <td><?= $categoryName ?></td>
                                <td><?= $book->published_year ?></td>
                                <td><?= date('M d, Y h:i A', strtotime($transaction->borrow_date)) ?></td>
                                <td><?= date('M d, Y h:i A', strtotime($transaction->due_date)) ?></td>
                                <td><?= $transaction->return_date ? date('M d, Y h:i A', strtotime($transaction->return_date)) : 'Not Returned' ?></td>
                                <td class="<?= $statusClass ?>">
                                    <?= ucfirst($status) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No borrowed books found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mb-4 p-4">
        <a href="index.php" class="btn btn-secondary">Back</a>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
