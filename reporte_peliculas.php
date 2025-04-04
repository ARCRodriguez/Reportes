<?php
require('fpdf/fpdf.php');

$conn = new mysqli("127.0.0.1", "Andina", "Grado2025.45", "dbpelicula");
if ($conn->connect_error) die(utf8_decode("Error de conexión: ") . $conn->connect_error);

$result = $conn->query("SELECT id_pelicula, nombre, anio FROM pelicula ORDER BY anio DESC, nombre");

class PDF extends FPDF {
    protected $current_year = null; // Cambiado de private a protected
    
    function Header() {
        $this->Image('logo3.jpg', 10, 8, 30);
        
        // Título con estilo moderno
        $this->SetFont('Helvetica', 'B', 24);
        $this->SetTextColor(220, 20, 60);
        $this->Cell(0, 20, utf8_decode('Catálogo Cinematográfico'), 0, 1, 'C');
        
        // Línea decorativa
        $this->SetDrawColor(220, 20, 60);
        $this->SetLineWidth(0.8);
        $this->Line(10, 40, $this->GetPageWidth()-10, 40);
        $this->Ln(15);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, utf8_decode('© ').date('Y').utf8_decode(' Alfredo Contreras') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    function YearHeader($year) {
        $this->current_year = $year; // Actualizamos la propiedad desde dentro de la clase
        $this->SetFont('Helvetica', 'B', 16);
        $this->SetTextColor(50, 50, 50);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(0, 10, utf8_decode('Año: ').$year, 0, 1, 'L', true);
        $this->Ln(5);
    }
    
    function MovieItem($id, $title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(30, 30, 30);
        $this->Cell(30, 8, utf8_decode('ID: '.$id), 0, 0);
        
        $this->SetFont('Arial', '', 12);
        $this->SetTextColor(70, 70, 70);
        $this->MultiCell(0, 8, utf8_decode($title), 0, 'L');
        
        $this->Ln(5);
    }
    
    // Método público para acceder al año actual
    public function getCurrentYear() {
        return $this->current_year;
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Mostrar encabezado de año si cambió (usando el método público)
        if ($pdf->getCurrentYear() != $row['anio']) {
            $pdf->YearHeader($row['anio']);
        }
        
        $pdf->MovieItem($row['id_pelicula'], $row['nombre']);
        
        // Salto de página si se acerca al final
        if ($pdf->GetY() > 250) {
            $pdf->AddPage();
        }
    }
    
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('Total de películas: ').$result->num_rows, 0, 1, 'R');
} else {
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, utf8_decode('No se encontraron películas'), 0, 1);
}

$conn->close();
$pdf->Output('reporte_peliculas_estilo.pdf', 'I');
?>