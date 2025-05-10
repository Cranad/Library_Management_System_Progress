<?php
require_once '../database/Database.php';
require_once '../models/User.php';
require '../plugins/fpdf/fpdf.php';

if (!isset($_GET['id'])) {
    die("Admin ID is required.");
}

$id = $_GET['id'];

$db = new Database();
$conn = $db->getConnection();

User::setConnection($conn);


$admin = User::find($id);

if (!$admin) {
    die("Admin profile not found or invalid.");
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, 'Admin Profile', 1, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, 'Field', 1);
$pdf->Cell(140, 10, 'Details', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Name', 1);
$pdf->Cell(140, 10, $admin->name, 1);
$pdf->Ln();

$pdf->Cell(50, 10, 'Email', 1);
$pdf->Cell(140, 10, $admin->email, 1);
$pdf->Ln();

$pdf->Cell(50, 10, 'Phone Number', 1);
$pdf->Cell(140, 10, $admin->phone_number, 1);
$pdf->Ln();

$pdf->Cell(50, 10, 'Role', 1);
$pdf->Cell(140, 10, ucfirst($admin->role), 1);
$pdf->Ln();

$pdf->Cell(50, 10, 'Account Status', 1);
$pdf->Cell(140, 10, ucfirst($admin->account_status), 1);
$pdf->Ln();

$pdf->Output('I', 'Admin_Profile.pdf');
?>
