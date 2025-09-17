<?php
// Asegúrate de que esta ruta sea correcta para tu proyecto
require('./fpdf/fpdf.php');

// Recibir los datos del formulario, que están en $_POST
if (isset($_POST['reporteData'])) {
    $data_received = json_decode($_POST['reporteData'], true);

    // Si los datos no se decodifican correctamente, detener la ejecución.
    if ($data_received === null || !isset($data_received['dataTotal']) || !isset($data_received['dataDetallado'])) {
        http_response_code(400);
        die('Error: Datos JSON no validos.');
    }

    // Acceder a los datos directamente del array recibido
    $dataTotal = $data_received['dataTotal'];
    $dataDetallado = $data_received['dataDetallado'];

    // Instanciar la clase FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetMargins(15, 10, 15);

    // --- Cabecera con logos y título ---
    // Reducimos el tamaño de la fuente para evitar el desbordamiento.
    $pdf->SetFont('Arial', 'B', 14); 
    $pdf->Cell(0, 10, 'SERVICIO SOCIALISTA DE ABASTECIMIENTO DEL ESTADO YARACUY', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'LIBRO DE VENTAS GASOLINA', 0, 1, 'C');
    $pdf->Cell(0, 10, $dataTotal['estacion'], 0, 1, 'C');
    // Asegúrate de que las rutas de las imágenes sean correctas en tu servidor
    // $pdf-Image('../src/img/logo.png', 10, 8, 30);
        // $pdf->Image('../src/img/logo2.png', 170, 8, 30);
    // $pdf->Ln(20);
    // --- Agregar la línea divisoria ---
    // Configurar el color de la línea (opcional)
    $pdf->SetDrawColor(0, 0, 0); // Color negro
    // Configurar el grosor de la línea
    $pdf->SetLineWidth(0.5); // 0.5 mm
    // Dibuja la línea desde el borde izquierdo al derecho (X1, Y1, X2, Y2)
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    // Salto de línea para dejar espacio después de la línea
    $pdf->Ln(1);
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

    // Modifica aquí la fila de Efectivo para incluir la conversión de Divisa
    //$totalEfectivoBs = ($dataTotal['total_efectivo'] ?? 0) + (($dataTotal['total_divisa'] ?? 0) * ($dataTotal['tasa_dia'] ?? 1));
    $pdf->Cell(45, 8, 'Efectivo Bs:', 1, 0, 'L', true);
    $pdf->Cell(45, 8, number_format($dataTotal['total_efectivo_bs'], 2) . ' Bs', 1, 1, 'C');
    
    // Fila para el total de débito
    $pdf->Cell(45, 8, 'Tarjeta de Debito:', 1, 0, 'L', true);
    $pdf->Cell(45, 8, number_format($dataTotal['total_debito'] ?? 0, 2) . ' Bs', 1, 1, 'C');

    
    
    // Calcula el Total General sumando el nuevo total de Efectivo Bs y el total de Débito
    $totalEfectivoBs = ($dataTotal['total_debito'] ?? 0) + (($dataTotal['total_efectivo_bs'] ?? 0));
    // $totalGeneralBs = $totalEfectivoBs + ($dataTotal['total_debito'] ?? 0);
    $pdf->Cell(45, 8, 'TOTAL GENERAL (Bs):', 1, 0, 'L', true);
    $pdf->Cell(45, 8, number_format($totalEfectivoBs, 2) . ' Bs', 1, 1, 'C');


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
    
    // Salida del PDF forzando la descarga
    $pdf->Output('D', 'Reporte_Ventas_' . $dataTotal['fecha'] . '.pdf');
    exit;
} else {
    http_response_code(400);
    echo 'Error: No se recibieron datos para generar el PDF.';
}