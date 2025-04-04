<?php
require('fpdf/fpdf.php');

$conn = new mysqli("127.0.0.1", "Andina", "Grado2025.45", "dbpelicula");
if ($conn->connect_error) die(utf8_decode("Error de conexión: ") . $conn->connect_error);

$result = $conn->query("SELECT id_actor, nombre, apellido FROM actor ORDER BY apellido, nombre");

class PDF extends FPDF {
    private $current_letter = '';
    
    function Header() {
        $this->Image('logo1.jpg', 10, 8, 30);
        
        // Título principal
        $this->SetFont('Arial', 'B', 24);
        $this->SetTextColor(189, 16, 224);
        $this->Cell(0, 20, utf8_decode('Directorio de Actores'), 0, 1, 'C');
        $this->Ln(10);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, utf8_decode(' Alfredo Contreras') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    function LetterHeader($letter) {
        $this->current_letter = $letter; // Actualizar la propiedad interna
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor(120, 50, 150);
        $this->Cell(0, 10, utf8_decode($letter), 0, 1, 'C', true);
        $this->Ln(5);
    }
    
    function ActorCard($id, $nombre, $apellido) {
        // Tarjeta con borde y sombra
        $this->SetDrawColor(200, 200, 200);
        $this->SetFillColor(245, 245, 255);
        $this->SetLineWidth(0.3);
        
        // Fondo de tarjeta
        $this->Rect($this->GetX(), $this->GetY(), 180, 15, 'DF');
        
        // Contenido de tarjeta
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(30, 15, utf8_decode('ID: '.$id), 0, 0, 'L');
        
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 15, utf8_decode($apellido).', '.utf8_decode($nombre), 0, 1, 'L');
        
        $this->Ln(8);
    }
    
    // Método público para verificar la letra actual
    public function getCurrentLetter() {
        return $this->current_letter;
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $first_letter = strtoupper(substr($row['apellido'], 0, 1));
        
        // Mostrar encabezado de letra si cambió (usando el método público)
        if ($pdf->getCurrentLetter() != $first_letter) {
            $pdf->LetterHeader($first_letter);
        }
        
        $pdf->ActorCard($row['id_actor'], $row['nombre'], $row['apellido']);
        
        // Salto de página si se acerca al final
        if ($pdf->GetY() > 250) {
            $pdf->AddPage();
        }
    }
    
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('Total de actores: ').$result->num_rows, 0, 1, 'R');
} else {
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, utf8_decode('No se encontraron actores'), 0, 1);
}

$conn->close();
$pdf->Output('reporte_actores_estilo.pdf', 'I');
?>