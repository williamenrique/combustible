<?php
require './autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'error' => '', 'warning' => ''];
$printer = null;
$connector = null;

try {
 if (!isset($_POST['dataTicket'])) {
 throw new Exception("No se recibieron datos del ticket");
 }

 $data = json_decode($_POST['dataTicket'], true);

 if (!isset($data['ticketData'])) {
 throw new Exception("Datos del ticket incompletos");
 }

 $ticketData = $data['ticketData'];
 
 // Recibir la variable 'copia' del JSON
 $copia = $data['copia'] ?? 0;
 
 // **OPCIÓN 3 - Conexión con respaldo**
 try {
 // Primero intentar con WindowsPrintConnector
 $connector = new WindowsPrintConnector("XP-80C");
 $response['message'] = 'Conectado a impresora física';
 } catch (Exception $e) {
 // Si falla, usar FilePrintConnector como respaldo
 $connector = new FilePrintConnector("php://stdout");
 $response['warning'] = 'Impresora no disponible. Simulando impresión en consola.';
 error_log("Impresora no disponible, usando simulador: " . $e->getMessage());
 }

 $printer = new Printer($connector);

 // Determina el tipo de pago y el monto
 $tipoPago = "";
 if ($ticketData['id_tipo_pago'] == 1) {
 $tipoPago = "Divisa " . $ticketData['monto'] . '$';
 } elseif ($ticketData['id_tipo_pago'] == 2) {
 $tipoPago = "Efectivo " . $ticketData['monto'] . 'Bs';
 } else {
 $tipoPago = "Punto de venta " . $ticketData['monto'] . 'Bs';
 }
 
 // Lógica para el texto final del ticket (Copia u Original)
 $footerText = "";
 if ($copia == 1) {
 $footerText = "COPIA";
 } else {
 $footerText = "ORIGINAL";
 }

 // Imprime el ticket
 $printer->setTextSize(2, 2);
 $printer->text("Ticket #" . $ticketData['id_venta'] . "\n");
 $printer->setTextSize(1, 1);
 $printer->text("------------------------------\n");
 $printer->text($ticketData['estacion'] . "\n");
 $printer->text($ticketData['fecha_venta'] . " - " . $ticketData['hora_venta'] . "\n");
 $printer->text("Operador : " . $ticketData['usuario_nombres'] . " " . $ticketData['usuario_apellidos'] . "\n");
 $printer->text("------------------------------\n");
 $printer->text("Pago : " . $tipoPago . "\n");
 $printer->text("Vehiculo : " . $ticketData['tipoVehiculo'] . "\n");
 $printer->text("\n");
 $printer->setTextSize(2, 2);
 $printer->setJustification(Printer::JUSTIFY_CENTER);
 $printer->text($ticketData['litros'] . "L\n");
 $printer->text("\n");
 $printer->setTextSize(1, 1);
 $printer->setJustification(Printer::JUSTIFY_CENTER);
 $printer->text("AUTOPISTA CIMARRON ANDRESOTE \n" . "CHIVACOA YARACUY" . "\n");
 
 // Impresión condicional del texto de pie de página
 $printer->setJustification(Printer::JUSTIFY_CENTER); // Centrar el texto
 if ($copia == 1) {
 $printer->setTextSize(2, 2); // Fuente grande
 $printer->setEmphasis(true); // Negrita
 $printer->text($footerText);
 } else {
 $printer->setTextSize(1, 1); // Fuente pequeña
 $printer->setEmphasis(false); // No negrita
 $printer->text($footerText);
 }
 
 $printer->feed(2);
 $printer->cut();

 $printer->close();
 $response['success'] = true;
 if (empty($response['message'])) {
 $response['message'] = 'Ticket procesado correctamente';
 }

} catch (Exception $e) {
 // ... (Manejo de errores sin cambios)
}

echo json_encode($response);
exit();