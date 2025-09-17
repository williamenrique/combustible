<?php
require_once __DIR__ . '/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configurar opciones de Dompdf (opcional, pero recomendado)
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

if (isset($_POST['reporteData'])) {
    $data = json_decode($_POST['reporteData'], true);

    // Separar los datos para mayor claridad
    $dataTotal = $data['dataTotal'];
    $dataDetallado = $data['dataDetallado'];

    // Iniciar la creación del HTML para el PDF
    $html = '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Ventas</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 10px; }
            .header { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            .header img { width: 50px; height: auto; }
            .header .logo-left { text-align: left; }
            .header .logo-right { text-align: right; }
            .header .title { text-align: center; font-size: 20px; font-weight: bold; }
            .info-line { width: 100%; margin-bottom: 10px; }
            .info-line .date { float: left; }
            .info-line .employee { float: right; }
            .info-line::after { content: ""; display: table; clear: both; }
            .summary { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            .summary td, .summary th { border: 1px solid #ddd; padding: 8px; text-align: left; }
            .summary th { background-color: #f2f2f2; }
            .table-ventas { width: 100%; border-collapse: collapse; margin-top: 20px; }
            .table-ventas th, .table-ventas td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            .table-ventas th { background-color: #f2f2f2; }
            .center-text { text-align: center; }
        </style>
    </head>
    <body>';

    // Cabecera del documento con logos y título
    $html .= '<table class="header">
        <tr>
            <td class="logo-left"><img src="https://via.placeholder.com/50x50.png?text=Logo1" alt="Logo Izquierdo"></td>
            <td class="title">REPORTE DIARIO DE VENTAS</td>
            <td class="logo-right"><img src="https://via.placeholder.com/50x50.png?text=Logo2" alt="Logo Derecho"></td>
        </tr>
    </table>';

    // Línea de información de fecha y empleado
    $html .= '<div class="info-line">
        <span class="date">Fecha del Reporte: ' . $dataTotal['fecha'] . '</span>
        <span class="employee">Empleado: ' . $dataTotal['empleado'] . '</span>
    </div>';

    // Resumen de ventas
    $html .= '<h3>Resumen de Ventas</h3>
    <table class="summary">
        <thead>
            <tr>
                <th>Vehículos Atendidos</th>
                <th>Litros Vendidos</th>
                <th>Total Bs (Efectivo y Débito)</th>
                <th>Efectivo Bs</th>
                <th>Tarjeta de Débito</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>' . $dataTotal['total_ventas'] . '</td>
                <td>' . $dataTotal['total_litros'] . ' L</td>
                <td>' . ($dataTotal['total_efectivo'] + $dataTotal['total_debito']) . ' Bs</td>
                <td>' . $dataTotal['total_efectivo'] . ' Bs</td>
                <td>' . $dataTotal['total_debito'] . ' Bs</td>
            </tr>
        </tbody>
    </table>';

    // Tabla de ventas detallada
    $html .= '<h3>Ventas Diarias Detalladas</h3>
    <table class="table-ventas">
        <thead>
            <tr>
                <th>Nro Ticket</th>
                <th>Tipo de Vehículo</th>
                <th>Litros</th>
                <th>Efectivo Bs</th>
                <th>Tarjeta de Débito</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($dataDetallado as $venta) {
        $efectivoBs = $venta['efectivob'] ?? 0;
        $tarjetaDebito = $venta['tarjeta_debito'] ?? 0;
        
        $html .= '<tr>';
        $html .= '<td class="center-text">' . $venta['numero_venta'] . '</td>';
        $html .= '<td class="center-text">' . $venta['tipo_vehiculo'] . '</td>';
        $html .= '<td class="center-text">' . $venta['cantidad_litros'] . '</td>';
        $html .= '<td class="center-text">' . $efectivoBs . '</td>';
        $html .= '<td class="center-text">' . $tarjetaDebito . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody>
    </table>';
    
    // Cierre del HTML
    $html .= '</body></html>';

    // Cargar el HTML en Dompdf
    $dompdf->loadHtml($html);
    
    // Establecer el tamaño de papel y la orientación (A4, vertical)
    $dompdf->setPaper('A4', 'portrait');

    // Renderizar el HTML a PDF
    $dompdf->render();

    // Generar el nombre del archivo
    $filename = "Reporte_Ventas_" . $dataTotal['fecha'] . ".pdf";

    // Enviar el PDF al navegador para que se abra o descargue
    $dompdf->stream($filename, ["Attachment" => false]);
    exit();
}