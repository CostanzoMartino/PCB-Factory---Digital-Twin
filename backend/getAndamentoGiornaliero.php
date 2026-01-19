<?php
function getAndamentoGiornaliero($gateway) {
    $conn = $gateway->getConnection();
    $oggi = date('Y-m-d');
    
    //Please replaca this query with your db scheme
    // Query per recuperare i dati giornalieri di temperatura, umidità e monossido di carbonio
    $sql = "SELECT m.Timestamp, m.Valore, s.nome AS Sensore
            FROM Misurazione m
            JOIN Sensore s ON m.FK_Sensore = s.Id
            WHERE DATE(m.Timestamp) = ? AND s.Id IN (2, 3, 4)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $oggi);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Array per salvare i risultati
    $andamentoGiornaliero = [
        'Temperature' => [],
        'Humidity' => [],
        'CarbonMonoxide' => []
    ];

    // Itera sui risultati e salva i dati nell'array appropriato
    while ($row = $result->fetch_assoc()) {
        $timestamp = $row['Timestamp'];
        $valore = floatval($row['Valore']); // Converte in float

        switch ($row['Sensore']) {
            case 'Temperature':
                $andamentoGiornaliero['Temperature'][] = [
                    'Timestamp' => $timestamp,
                    'Valore' => $valore
                ];
                break;
            case 'Humidity':
                $andamentoGiornaliero['Humidity'][] = [
                    'Timestamp' => $timestamp,
                    'Valore' => $valore
                ];
                break;
            case 'Carbon Monoxide':
                $andamentoGiornaliero['CarbonMonoxide'][] = [
                    'Timestamp' => $timestamp,
                    'Valore' => $valore
                ];
                break;
            default:
                // Gestione per sensori non previsti, se necessario
                break;
        }
    }

    $stmt->close();

    return json_encode($andamentoGiornaliero);
}
?>
