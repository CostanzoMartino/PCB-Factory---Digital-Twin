<?php
function getLatestMeasurement($gateway, $sensor_id, $camera_id) {
	$conn = $gateway->getConnection();
	$query = "SELECT Valore FROM Misurazione WHERE FK_Sensore = ? AND FK_Camera = ? ORDER BY id DESC LIMIT 1";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("ii", $sensor_id, $camera_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$latestValue = null;
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$latestValue = $row['Valore'];
	} else {
		echo "Nessun dato trovato per il sensore e la camera specificati.";
	}
	$stmt->close();
	return $latestValue;
}
?>
