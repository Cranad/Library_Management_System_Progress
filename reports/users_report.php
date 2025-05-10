<?php
require_once '../database/Database.php';
require_once '../models/User.php';
require '../plugins/fpdf/fpdf.php';

$db = new Database();
$conn = $db->getConnection();

User::setConnection($conn);


$admins = User::findByRole('admin');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, 'All Registered Admins', 1, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(10, 10, '#', 1);
$pdf->Cell(50, 10, 'Name', 1);
$pdf->Cell(60, 10, 'Email', 1);
$pdf->Cell(40, 10, 'Phone Number', 1);
$pdf->Cell(30, 10, 'Status', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
if ($admins) {
    $i = 1;
    foreach ($admins as $admin) {
        $pdf->Cell(10, 10, $i++, 1);
        $pdf->Cell(50, 10, $admin->name, 1);
        $pdf->Cell(60, 10, $admin->email, 1);
        $pdf->Cell(40, 10, $admin->phone_number, 1);
        $pdf->Cell(30, 10, ucfirst($admin->account_status), 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(190, 10, 'No admin profiles found.', 1, 1, 'C');
}

$pdf->Output('I', 'Admin_Profiles.pdf');
?>
