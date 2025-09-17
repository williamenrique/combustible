<?php
// Asegúrate de que esta ruta sea correcta para tu proyecto
require('./fpdf/fpdf.php');

// Recibir el JSON directamente del cuerpo de la solicitud
$json = file_get_contents('php://input');
$data_received = json_decode($json, true);

// Verificar que los datos JSON se hayan recibido y decodificado correctamente
if ($data_received === null || !isset($data_received['dataTotal']) || !isset($data_received['dataDetallado'])) {
    http_response_code(400);
    die('Error: No se recibieron datos válidos para generar el PDF. (faltan dataTotal o dataDetallado)');
}

// Acceder a los datos directamente del array recibido
$dataTotal = $data_received['dataTotal'];
$dataDetallado = $data_received['dataDetallado'];

// Instanciar la clase FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(15, 10, 15);

// --- Cabecera con logos y título ---
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 10, 'REPORTE DIARIO DE VENTAS', 0, 1, 'C');
$pdf->Image('../src/img/logo.png', 10, 8, 30);
$pdf->Image('../src/img/logo2.png', 170, 8, 30);
$pdf->Ln(20);

// --- Línea de información de fecha y empleado ---
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Fecha del Reporte: ' . $dataTotal['fecha'], 0, 0, 'L');
$pdf->Cell(0, 10, 'Empleado: ' . $dataTotal['empleado'], 0, 1, 'R');
$pdf->Ln(5);
    
// --- Resumen de ventas ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Resumen de Ventas', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(45, 8, 'Vehiculos Atendidos:', 1, 0, 'L', true);
$pdf->Cell(45, 8, $dataTotal['total_ventas'], 1, 1, 'C');

$pdf->Cell(45, 8, 'Litros Vendidos:', 1, 0, 'L', true);
$pdf->Cell(45, 8, $dataTotal['total_litros'] . ' L', 1, 1, 'C');

$totalBs = ($dataTotal['total_efectivo'] ?? 0) + ($dataTotal['total_debito'] ?? 0);
$pdf->Cell(45, 8, 'Total Bs (Efectivo y Debito):', 1, 0, 'L', true);
$pdf->Cell(45, 8, number_format($totalBs, 2) . ' Bs', 1, 1, 'C');
    
$pdf->Cell(45, 8, 'Efectivo Bs:', 1, 0, 'L', true);
$pdf->Cell(45, 8, number_format($dataTotal['total_efectivo'] ?? 0, 2) . ' Bs', 1, 1, 'C');
    
$pdf->Cell(45, 8, 'Tarjeta de Debito:', 1, 0, 'L', true);
$pdf->Cell(45, 8, number_format($dataTotal['total_debito'] ?? 0, 2) . ' Bs', 1, 1, 'C');

$pdf->Ln(10);
    
// --- Tabla de ventas detallada ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Ventas Diarias Detalladas', 0, 1, 'L');
    
// Cabecera de la tabla
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(30, 8, 'Nro Ticket', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Tipo de Vehiculo', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Litros', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'Efectivo Bs', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'Tarjeta de Debito', 1, 1, 'C', true);

// Datos de la tabla
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(255, 255, 255);
foreach ($dataDetallado as $venta) {
    $efectivoBs = $venta['efectivob'] ?? 0;
    $tarjetaDebito = $venta['tarjeta_debito'] ?? 0;
        
    $pdf->Cell(30, 8, $venta['numero_venta'], 1, 0, 'C', true);
    $pdf->Cell(40, 8, $venta['tipo_vehiculo'], 1, 0, 'C', true);
    $pdf->Cell(25, 8, $venta['cantidad_litros'], 1, 0, 'C', true);
    $pdf->Cell(45, 8, number_format($efectivoBs, 2), 1, 0, 'C', true);
    $pdf->Cell(45, 8, number_format($tarjetaDebito, 2), 1, 1, 'C', true);
}
    
// Salida del PDF.
$pdf->Output('I', 'Reporte_Ventas_' . $dataTotal['fecha'] . '.pdf');
exit;