<?php


function getMisurazioni($gateway) {
    $conn = $gateway->getConnection();
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }
    $query = "SELECT * FROM Misurazione";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Errore nella preparazione della query: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result === false) {
        die("Errore nell'esecuzione della query: " . $stmt->error);
    }
    $misurazioni = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $misurazioni[] = $row;
        }
    }
    $stmt->close();
    return $misurazioni;
}

?>
