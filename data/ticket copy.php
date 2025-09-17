<?php
require './autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
$connector = new WindowsPrintConnector("XP-80C");
$printer = new Printer($connector);
if (isset($_POST['dataTicket'])) {
    $data = json_decode($_POST['dataTicket'], true);
    if (isset($data['ticketData'])) {
        $ticketData = $data['ticketData'];
        // Determina el tipo de pago y el monto
        $tipoPago = "";
        if ($ticketData['id_tipo_pago'] == 1) {
            $tipoPago = "Divisa " . $ticketData['monto'] . '$';
        } elseif ($ticketData['id_tipo_pago'] == 2) {
            $tipoPago = "Efectivo " . $ticketData['monto'] . 'Bs';
        } else {
            $tipoPago = "Punto de venta " . $ticketData['monto'] . 'Bs';
        }
        $detalle = ($ticketData['status_ticket'] == 1) ? ' ' : 'COPIA';
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
        $printer->setTextSize(2, 2);
        $printer->text($detalle);
        $printer->feed(2);
        $printer->cut();
        $printer->close();
    }
}
