<?php
require('fpdf/fpdf.php');

// Datos de conexión a la base de datos
$hostDB = "127.0.0.1";
$nombreDB = "dbpelicula";
$usuarioDB = "Andina";
$password = "Grado2025.45";

// Conectar a MySQL
$conn = new mysqli($hostDB, $usuarioDB, $password, $nombreDB);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener las películas con actores, ordenadas por año
$sql = "SELECT p.anio, p.nombre AS pelicula, 
               GROUP_CONCAT(CONCAT(a.nombre, ' ', a.apellido) SEPARATOR '\n') AS actores
        FROM pelicula p
        JOIN actua ac ON p.id_pelicula = ac.id_pelicula
        JOIN actor a ON ac.id_actor = a.id_actor
        GROUP BY p.id_pelicula
        ORDER BY p.anio ASC";
$result = $conn->query($sql);

// Crear PDF con diseño mejorado
class PDF extends FPDF {
    private $headerColor = array(50, 100, 150); // Azul oscuro
    private $movieColor = array(70, 130, 180); // Azul acero
    private $actorColor = array(100, 100, 100); // Gris
    
    function Header() {
        // Logo centrado en la parte superior
        $this->Image('logo.jpg', ($this->GetPageWidth() - 30) / 2, 10, 30, 30);
        
        // Configurar color para el encabezado
        $this->SetFillColor($this->headerColor[0], $this->headerColor[1], $this->headerColor[2]);
        $this->SetTextColor(255, 255, 255); // Texto blanco
        
        // Título con fondo de color
        $this->SetFont('Arial', 'B', 18);
        $this->SetY(45); // Posición después del logo
        $this->Cell(0, 12, utf8_decode('Catálogo de Películas'), 0, 1, 'C', true);
        
        // Línea decorativa
        $this->SetDrawColor($this->headerColor[0], $this->headerColor[1], $this->headerColor[2]);
        $this->SetLineWidth(0.5);
        $this->Line(10, 60, $this->GetPageWidth() - 10, 60);
        
        $this->Ln(15); // Espacio después del encabezado
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, utf8_decode('Alfredo Contreras ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    function MovieTitle($title, $year) {
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor($this->movieColor[0], $this->movieColor[1], $this->movieColor[2]);
        $this->Cell(0, 8, utf8_decode($title) . ' (' . $year . ')', 0, 1, 'C');
        $this->Ln(2);
    }
    
    function ActorList($actors) {
        $this->SetFont('Arial', '', 11);
        $this->SetTextColor($this->actorColor[0], $this->actorColor[1], $this->actorColor[2]);
        
        $actores = explode("\n", $actors);
        foreach ($actores as $actor) {
            // Centrar cada actor con un bullet point (usando un carácter estándar)
            $this->Cell(($this->GetPageWidth() - 30) / 2); // Centrar
            $this->Cell(10, 6, chr(149), 0, 0, 'C'); // Usamos chr(149) para el bullet
            $this->Cell(0, 6, utf8_decode(trim($actor)), 0, 1);
        }
        $this->Ln(8);
    }
    
    function SectionSeparator() {
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.3);
        $this->Line(($this->GetPageWidth() - 100) / 2, $this->GetY(), ($this->GetPageWidth() + 100) / 2, $this->GetY());
        $this->Ln(10);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Mostrar título de película centrado
        $pdf->MovieTitle($row['pelicula'], $row['anio']);
        
        // Mostrar lista de actores centrada
        $pdf->ActorList($row['actores']);
        
        // Separador entre películas (solo si no es la última)
        if ($result->num_rows > 1) {
            $pdf->SectionSeparator();
        }
    }
} else {
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->SetTextColor(150, 150, 150);
    $pdf->Cell(0, 10, utf8_decode('No se encontraron películas en la base de datos'), 0, 1, 'C');
}

$conn->close();
$pdf->Output();
?>