<?php
require('fpdf/fpdf.php');

$conn = new mysqli("127.0.0.1", "Andina", "Grado2025.45", "dbpelicula");
if ($conn->connect_error) die(utf8_decode("Error de conexión: ") . $conn->connect_error);

$result = $conn->query("SELECT id_estudio, nombre, direccion FROM estudio ORDER BY nombre");

class PDF extends FPDF {
    function Header() {
        $this->Image('logo2.jpg', 10, 8, 25);
        
        // Título principal con estilo moderno
        $this->SetFont('Arial', 'B', 22);
        $this->SetTextColor(0, 80, 160);
        $this->Cell(0, 15, utf8_decode('Directorio de Estudios Cinematográficos'), 0, 1, 'C');
        
        // Línea decorativa
        $this->SetDrawColor(0, 80, 160);
        $this->SetLineWidth(0.8);
        $this->Line(10, 30, $this->GetPageWidth()-10, 30);
        $this->Ln(15);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, utf8_decode('© ').date('Y').utf8_decode(' Alfredo Contreras') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    function StudioCard($id, $nombre, $direccion) {
        // Estilo de tarjeta
        $this->SetDrawColor(200, 200, 200);
        $this->SetFillColor(240, 245, 255);
        $this->SetLineWidth(0.3);
        
        // Fondo de tarjeta
        $this->Rect($this->GetX(), $this->GetY(), 180, 25, 'DF');
        
        // Contenido de la tarjeta
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 60, 120);
        $this->Cell(0, 8, utf8_decode($nombre), 0, 1);
        
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(30, 6, utf8_decode('ID: ').$id, 0, 0);
        
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 6, utf8_decode('Dirección: ').utf8_decode($direccion), 0, 1);
        
        $this->Ln(12); // Espacio entre tarjetas
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->StudioCard($row['id_estudio'], $row['nombre'], $row['direccion']);
        
        // Salto de página si se acerca al final
        if ($pdf->GetY() > 250) {
            $pdf->AddPage();
        }
    }
    
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('Total de estudios registrados: ').$result->num_rows, 0, 1, 'R');
} else {
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, utf8_decode('No se encontraron estudios cinematográficos'), 0, 1);
}

$conn->close();
$pdf->Output('reporte_estudios_diseno.pdf', 'I');
?>