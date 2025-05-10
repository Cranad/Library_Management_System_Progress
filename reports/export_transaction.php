<?php
require_once '../database/Database.php';
require_once '../models/Book.php';
require_once '../models/Transaction.php';
require_once '../models/User.php';
require '../plugins/fpdf/fpdf.php';

$database = new Database();
$db = $database->getConnection();
Transaction::setConnection($db);
User::setConnection($db);
Book::setConnection($db);

$transactions = Transaction::all();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetLeftMargin(8);
$pdf->Cell(0, 10, 'Library Transactions Report', 0, 1, 'C');
$pdf->Ln();

$pdf->SetLeftMargin(8);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, '#', 1);
$pdf->Cell(50, 10, 'Book Title', 1);
$pdf->Cell(40, 10, 'Borrower', 1);
$pdf->Cell(25, 10, 'Borrow Date', 1);
$pdf->Cell(25, 10, 'Due Date', 1);
$pdf->Cell(25, 10, 'Return Date', 1);
$pdf->Cell(20, 10, 'Status', 1);
$pdf->Ln();

$pdf->SetLeftMargin(8);
$pdf->SetFont('Arial', '', 10);
$i = 1;
foreach ($transactions as $transaction) {
    $pdf->SetLeftMargin(8);
    $book = Book::find($transaction->book_id);
    $user = User::find($transaction->user_id);

    $pdf->Cell(10, 10, $i++, 1);
    $pdf->Cell(50, 10, $book->title, 1);
    $pdf->Cell(40, 10, $user->name, 1);
    $pdf->Cell(25, 10, substr($transaction->borrow_date,0,10), 1);
    $pdf->Cell(25, 10, substr($transaction->due_date,0,10),1);
    $pdf->Cell(25, 10, substr($transaction->return_date,0,10) ?? 'Pending', 1);
    $pdf->Cell(20, 10, ucfirst($transaction->status), 1);
    $pdf->Ln();
}

$pdf->Output('I', 'Library_Transactions_Report.pdf');