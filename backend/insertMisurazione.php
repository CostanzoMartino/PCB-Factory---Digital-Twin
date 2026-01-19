<?php
function insertMisurazione($gateway) {
	// Imposta l'header per indicare che la risposta in formato JSON
	header('Content-Type: application/json');

	// Recupero del payload JSON
	$json = file_get_contents('php://input');
	$data = json_decode($json, true);
    
	// Controlla se i dati sono presenti
	if (isset($data['cameraId'])) {
		$fk_camera = $data['cameraId'];
		$responses = [];
		$timestamp = date('Y-m-d H:i:s');
		
		$conn = $gateway->getConnection();
		// Prepara la dichiarazione SQL
		$stmt = $conn->prepare("INSERT INTO Misurazione (Timestamp, Valore, FK_Sensore, FK_Camera) VALUES (?, ?, ?, ?)");
		// Inserisci temperatura
		if (isset($data['temperatura'])) {
			$valore = $data['temperatura'];
			if (isset($data['sensorId'])) {
				$fk_sensore = $data['sensorId'];
				$stmt->bind_param("sdii", $timestamp, $valore, $fk_sensore, $fk_camera);
				if ($stmt->execute()) {
					$responses['temperatura'] = ["success" => true];
				} else {
					$responses['temperatura'] = ["success" => false, "error" => $stmt->error];
				}
			}
		}

		// Inserisci umidita
		if (isset($data['umidita'])) {
			$valore = $data['umidita'];
			if (isset($data['sensorId2'])) {
				$fk_sensore = $data['sensorId2'];
				$stmt->bind_param("sdii", $timestamp, $valore, $fk_sensore, $fk_camera);
				if ($stmt->execute()) {
					$responses['umidita'] = ["success" => true];
				} else {
					$responses['umidita'] = ["success" => false, "error" => $stmt->error];
				}
			}
		}

		// Inserisci monossido di carbonio
		if (isset($data['monossido_di_carbonio'])) {
			$valore = $data['monossido_di_carbonio'];
			if (isset($data['sensorId'])) {
				$fk_sensore = $data['sensorId'];
				$stmt->bind_param("sdii", $timestamp, $valore, $fk_sensore, $fk_camera);
				if ($stmt->execute()) {
					$responses['monossido_di_carbonio'] = ["success" => true];
				} else {
					$responses['monossido_di_carbonio'] = ["success" => false, "error" => $stmt->error];
				}
			}
		}

		// Inserisci movimento
		if (isset($data['movimento'])) {
			$valore = $data['movimento'];
			if (isset($data['sensorId'])) {
				$fk_sensore = $data['sensorId'];
				$stmt->bind_param("sdii", $timestamp, $valore, $fk_sensore, $fk_camera);
				if ($stmt->execute()) {
					$responses['movimento'] = ["success" => true];
				} else {
					$responses['movimento'] = ["success" => false, "error" => $stmt->error];
				}
			}
		}

		// Chiudi la dichiarazione
		$stmt->close();

		// Invia le risposte in formato JSON
		echo json_encode($responses);
	} else {
		// Invia una risposta di errore se i dati sono mancanti
		echo json_encode(array("success" => false, "error" => "Dati mancanti"));
	}
}
?>
