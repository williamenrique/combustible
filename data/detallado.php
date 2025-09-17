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
		
		if (!empty($decoded_json)) {
			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setTextSize(1, 1);
			$printer->text("REPORTE DETALLADO\n");
			$printer->setTextSize(1, 1);
			$printer->text("------------------------------\n");
			$printer->setJustification(Printer::JUSTIFY_LEFT);
			
			$estacion = $decoded_json[0]["estacion"];
			$operador = $decoded_json[0]["operador"];
			$fecha_venta = $decoded_json[0]["fecha_venta"];
			$tasa_dia = $decoded_json[0]["tasa_dia"];
	
			$printer->text("Estacion: " . $estacion . "\n");
			$printer->text("Operador: " . $operador . "\n");
			$printer->text("Fecha del dia: " . $fecha_venta . "\n");
			$printer->text("Tasa del dia: " . $tasa_dia . " Bs\n");
	
			$printer->text("------------------------------\n");
			$printer->text("DETALLE DE VENTAS\n");
			$printer->text("------------------------------\n");
			
			$montoTotalVenta = 0;
			$totalLitros = 0;
	
			foreach ($decoded_json as $value) {
				$monto_venta = $value['monto'];
				if ($value['tipo_pago'] == "Efectivo Divisa") {
					$monto_venta = floatval($value['monto']) * floatval($value['tasa_dia']);
				} else {
					$monto_venta = floatval($value['monto']);
				}
				
				$printer->text("#" . $value['numero_venta'] . " | " . $value['tipo_vehiculo'] . " | " . number_format($monto_venta, 2) . " Bs | " . $value["cantidad_litros"] . " L\n");
				
				$montoTotalVenta += $monto_venta;
				$totalLitros += floatval($value["cantidad_litros"]);
			}
			
			$numero_de_registros = count($decoded_json);
	
			$printer->text("------------------------------\n");
			$printer->text("TOTAL ATENDIDOS: " . $numero_de_registros . "\n");
			$printer->text("TOTAL LITROS: " . number_format($totalLitros, 2) . " L\n");
			$printer->text("TOTAL VENDIDO: " . number_format($montoTotalVenta, 2) . " Bs\n");
			$printer->text("------------------------------\n");
			
			$printer->feed(2);
			$printer->cut();
		}
	}
	$printer->close();
?>