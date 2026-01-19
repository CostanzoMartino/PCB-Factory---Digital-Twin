<?php
function getNomeSensore($gateway) {
    $conn = $gateway->getConnection();

    if (!isset($_GET['id'])) {
        echo json_encode(array('error' => 'Missing ID parameter'));
        return;
    }

    $id = $_GET['id'];

    // Query per recuperare il nome del sensore
    $sql = "SELECT nome FROM Sensore WHERE Id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(array('Nome' => $row['nome']));
    } else {
        echo json_encode(array('error' => 'Sensor not found'));
    }

    $stmt->close();
}

?>