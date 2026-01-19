<?php
function getMediaSensoreOggi($conn, $sensoreId) {
    $oggi = date('Y-m-d');
    $sql = "SELECT ROUND(AVG(Misurazione.Valore), 2) AS Media
        FROM Misurazione
        WHERE DATE(Misurazione.Timestamp) = ? AND FK_sensore = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $oggi, $sensoreId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result === false) {
        die("Errore nell'esecuzione della query: " . $stmt->error);
    }
    $media = $result->fetch_assoc()['Media'];
    $stmt->close();
    return $media;
}

function getMisurazioniOggi($gateway) {
    $conn = $gateway->getConnection();
    
    $mediaTemperatura = getMediaSensoreOggi($conn, 2);
    $mediaUmidita = getMediaSensoreOggi($conn, 3);
    $mediaMonossido = getMediaSensoreOggi($conn, 4);
    
    $response = [
        'mediaTemperatura' => $mediaTemperatura,
        'mediaUmidita' => $mediaUmidita,
        'mediaMonossido' => $mediaMonossido
    ];

    return json_encode($response);
}
?>
