<?php
	require  './autoload.php';
	use Mike42\Escpos\Printer;
	use Mike42\Escpos\EscposImage;
	use Mike42\Escpos\PrintConnectors\FilePrintConnector;
	use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
	
	$connector = new WindowsPrintConnector("XP-80C");
	$printer = new Printer($connector);
	
	if (isset($_POST['dataTicket'])) {
		$decoded_json = json_decode($_POST['dataTicket'], true);
	
		// Preparar la información de los vehículos
		$carro = "";
		if (isset($decoded_json["Automovil"]) && $decoded_json["Automovil"] > 0) {
			$carro = $decoded_json["Automovil"] . ' Carro ' . $decoded_json["litrosAuto"] . 'L';
		}
	
		$camion = "";
		if (isset($decoded_json["Camion"]) && $decoded_json["Camion"] > 0) {
			$camion = $decoded_json["Camion"] . ' Camion ' . $decoded_json["litrosCamion"] . 'L';
		}
	
		$moto = "";
		if (isset($decoded_json["Motocicleta"]) && $decoded_json["Motocicleta"] > 0) {
			$moto = $decoded_json["Motocicleta"] . ' Moto ' . $decoded_json["litrosMoto"] . 'L';
		}
	
		// Preparar la información de los pagos
		$divisa = "";
		if (isset($decoded_json["total_divisa"]) && $decoded_json["total_divisa"] > 0) {
			$divisa = ' Efectivo Divisa ' . $decoded_json["total_divisa"] . '$';
		}
	
		$efectivo = "";
		if (isset($decoded_json["total_efectivo"]) && $decoded_json["total_efectivo"] > 0) {
			$efectivo = ' Efectivo ' . round($decoded_json["total_efectivo"], 2) . 'Bs';
		}
	
		$punto = "";
		if (isset($decoded_json["total_debito"]) && $decoded_json["total_debito"] > 0) {
			$punto = ' Punto de venta ' . round($decoded_json["total_debito"], 2) . 'Bs';
		}
	
		// Imprimir el ticket de cierre
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(1, 1);
		$printer->text("CIERRE DEL DIA\n");
		$printer->text($decoded_json["estacion"] . "\n");
		$printer->setTextSize(1, 1);
		$printer->text("------------------------------\n");
		
		$printer->setJustification(Printer::JUSTIFY_LEFT);
		$printer->text("Operador: " . $decoded_json["operador"] . "\n");
		$printer->text("Fecha: " . $decoded_json["fecha"] . "\n");
		$printer->text("Tasa del dia: " . $decoded_json["tasa_dia"] . " Bs\n");
		$printer->text("------------------------------\n");
	
		// Detalle de vehículos
		$printer->text("VENTAS POR TIPO DE VEHICULO\n");
		$printer->text("------------------------------\n");
		if (!empty($carro)) {
			$printer->text($carro . "\n");
		}
		if (!empty($camion)) {
			$printer->text($camion . "\n");
		}
		if (!empty($moto)) {
			$printer->text($moto . "\n");
		}
		$printer->text("------------------------------\n");
		
		// Detalle de ventas totales
		$printer->text("RESUMEN GENERAL\n");
		$printer->text("------------------------------\n");
		$printer->text("TOTAL ATENDIDOS: " . $decoded_json["total_ventas"] . "\n");
		$printer->text("CANTIDAD LITROS: " . $decoded_json["total_litros"] . "L\n");
		$printer->text("TOTAL VENDIDO: " . $decoded_json["total_general_bs"] . " Bs\n");
		$printer->text("------------------------------\n");
	
		// Detalle de pagos
		$printer->text("VENTAS POR TIPO DE PAGO\n");
		$printer->text("------------------------------\n");
		if (!empty($divisa)) {
			$printer->text($divisa . "\n");
		}
		if (!empty($efectivo)) {
			$printer->text($efectivo . "\n");
		}
		if (!empty($punto)) {
			$printer->text($punto . "\n");
		}
		$printer->text("------------------------------\n");
	
		$printer->feed(2);
		$printer->cut();
	}
	$printer->close();
?>