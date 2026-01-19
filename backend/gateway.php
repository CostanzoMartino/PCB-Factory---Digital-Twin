<?php
include 'config.php';
include 'insertMisurazione.php';
include 'getLatestMeasurement.php';
include 'getMisurazioni.php';
include 'getMisurazioniOggi.php';
include 'getAndamentoGiornaliero.php';
include 'getNomeSensore.php';

class Gateway {
    private $conn;

    public function __construct() {
        $this->conn = getDbConnection();
    }
    
    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

$gateway = new Gateway();

// Recupera i parametri dalla query string
$servizio = isset($_GET['nomeServizio']) ? $_GET['nomeServizio'] : '';
// Verifica che i parametri siano validi
if ($servizio == 'getMovimento' ||
    $servizio == 'getTemperatura' ||
    $servizio == 'getUmidita' ||
    $servizio == 'getMonossido') {
    if (isset($_GET['sensor_id']) && isset($_GET['camera_id'])) {
        $sensor_id = intval($_GET['sensor_id']);
        $camera_id = intval($_GET['camera_id']);
        $latestValue = getLatestMeasurement($gateway, $sensor_id, $camera_id);
        echo "$latestValue";
    } else {
        echo json_encode(['error' => 'Parametri sensor_id e camera_id mancanti o non validi.']);
    }
} elseif ($servizio == 'insertMisurazione') {
    insertMisurazione($gateway);
} elseif ($servizio == 'getMisurazioni'){
    echo json_encode(getMisurazioni($gateway));
} elseif ($servizio == 'getMisurazioniOggi') {
    echo getMisurazioniOggi($gateway);
} elseif ($servizio == 'getAndamentoGiornaliero') {
    echo getAndamentoGiornaliero($gateway);
} else if ($servizio == 'getNomeSensore') {
	echo getNomeSensore($gateway);
}
else {
    echo json_encode(['error' => 'Servizio non esistente o non consentito.']);
}

$gateway->closeConnection();
?>
