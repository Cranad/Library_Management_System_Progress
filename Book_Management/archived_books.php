<?php
require_once '../layout/header.php';
require_once '../database/Database.php';
require_once '../models/Book.php';

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super-admin') {
    die("Access denied.");
}

$db = new Database();
$conn = $db->getConnection();
$book = new Book($conn);
$archivedBooks = $book->getArchived();
?>

<h3>Archived Books</h3>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Title</th><th>Author</th><th>Category</th><th>Year</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($archivedBooks as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['author']) ?></td>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td><?= $row['published_year'] ?></td>
                <td>
                    <a href="restore.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Restore</a>
                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to permanently delete this book?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
