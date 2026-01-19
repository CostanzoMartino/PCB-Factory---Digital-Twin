<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            margin-bottom: 20px; /* Spazio tra i grafici */
        }
    </style>
</head>
<body>
    <div class="container mt-3">
        <h1 class="mb-4">Dashboard Misurazioni</h1>

        <!-- Sezione delle carte -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card card-temperatura">
                    <div class="card-body">
                        <h5 class="card-title">Temperatura media odierna</h5>
                        <p class="card-text" id="mediaTemperatura">Loading...</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-umidita">
                    <div class="card-body">
                        <h5 class="card-title">Umidità media odierna</h5>
                        <p class="card-text" id="mediaUmidita">Loading...</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-monossido">
                    <div class="card-body">
                        <h5 class="card-title">CO media odierna</h5>
                        <p class="card-text" id="mediaMonossido">Loading...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sezione dei grafici -->
        <div class="row">
            <div class="col-md-4">
                <div class="chart-container">
                    <canvas id="graficoTemperatura" width="400" height="400"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <canvas id="graficoUmidita" width="400" height="400"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <canvas id="graficoMonossido" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
		<h1 class="mb-3">Tabella Misurazioni</h1>
        <!-- Tabella delle misurazioni -->
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered mt-5">
                    <thead class="thead-dark">
                        <tr>
                            <th>Id</th>
                            <th>Timestamp</th>
                            <th>Valore</th>
                            <th>Sensore</th>
                            <th>FK_Camera</th>
                        </tr>
                    </thead>
                    <tbody id="misurazioniTableBody">
                    </tbody>
                </table>
                <br><br><br>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <span class="text-muted">© 2024 Progetto PCB Factory - Costanzo Martino Tutti i diritti riservati.</span>
        </div>
    </footer>
    
    <!-- Script -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Funzione per aggiornare i dati delle carte (temperatura, umidità, CO)
        function updateCards(data) {
            document.getElementById('mediaTemperatura').textContent = data.mediaTemperatura+ '°C';
            document.getElementById('mediaUmidita').textContent = data.mediaUmidita+ '%';
            document.getElementById('mediaMonossido').textContent = data.mediaMonossido+ 'PPM';
        }

        // Funzione per disegnare i grafici utilizzando Chart.js
function drawCharts(data) {
    // Grafico Temperatura
    new Chart(document.getElementById('graficoTemperatura').getContext('2d'), {
        type: 'line',
        data: {
            labels: data.Temperature.map(entry => entry.Timestamp).reverse(), // Inverti l'ordine delle etichette (timestamp)
            datasets: [{
                label: 'Temperatura',
                data: data.Temperature.map(entry => entry.Valore).reverse(), // Inverti l'ordine dei dati di temperatura
                borderColor: 'rgb(255, 99, 132)',
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    type: 'time', // Tipo asse x come tempo
                    time: {
                        displayFormats: {
                            hour: 'HH:mm' // Formato di visualizzazione per l'asse x (ore e minuti)
                        }
                    }
                }]
            }
        }
    });

    // Grafico Umidità
    new Chart(document.getElementById('graficoUmidita').getContext('2d'), {
        type: 'line',
        data: {
            labels: data.Humidity.map(entry => entry.Timestamp).reverse(), // Inverti l'ordine delle etichette (timestamp)
            datasets: [{
                label: 'Umidità',
                data: data.Humidity.map(entry => entry.Valore).reverse(), // Inverti l'ordine dei dati di umidità
                borderColor: 'rgb(54, 162, 235)',
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    type: 'time', // Tipo asse x come tempo
                    time: {
                        displayFormats: {
                            hour: 'HH:mm' // Formato di visualizzazione per l'asse x (ore e minuti)
                        }
                    }
                }]
            }
        }
    });

    // Grafico Monossido di carbonio
    new Chart(document.getElementById('graficoMonossido').getContext('2d'), {
        type: 'line',
        data: {
            labels: data.CarbonMonoxide.map(entry => entry.Timestamp).reverse(), // Inverti l'ordine delle etichette (timestamp)
            datasets: [{
                label: 'CO',
                data: data.CarbonMonoxide.map(entry => entry.Valore).reverse(), // Inverti l'ordine dei dati di CO
                borderColor: 'rgb(75, 192, 192)',
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    type: 'time', // Tipo asse x come tempo
                    time: {
                        displayFormats: {
                            hour: 'HH:mm' // Formato di visualizzazione per l'asse x (ore e minuti)
                        }
                    }
                }]
            }
        }
    });
}


        // Fetch dei dati per le carte (temperatura, umidità, CO)
        fetch('gateway.php?nomeServizio=getMisurazioniOggi')
            .then(response => response.json())
            .then(data => {
                updateCards(data); // Aggiorna le carte con i dati ricevuti
            })
            .catch(error => console.error('Errore nel recupero dei dati per le carte:', error));

        // Fetch dei dati per i grafici
        fetch('gateway.php?nomeServizio=getAndamentoGiornaliero')
            .then(response => response.json())
            .then(data => {
                drawCharts(data); // Disegna i grafici con i dati ricevuti
            })
            .catch(error => console.error('Errore nel recupero dei dati per i grafici:', error));
              
            
        // Fetch data for table
        fetch('gateway.php?nomeServizio=getMisurazioni')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('misurazioniTableBody');
                data.forEach(row => {
                    // Fetch per ottenere il nome del sensore
                    fetch(`gateway.php?nomeServizio=getNomeSensore&id=${row.FK_Sensore}`)
                        .then(response => response.json())
                        .then(sensorData => {
                            // Costruisci la riga della tabella con il nome del sensore invece dell'ID
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.Id}</td>
                                <td>${row.Timestamp}</td>
                                <td>${row.Valore}</td>
                                <td>${sensorData.Nome}</td>
                                <td>${row.FK_Camera}</td>
                            `;
                            tableBody.appendChild(tr);
                        })
                        .catch(error => console.error('Error fetching sensor name:', error));
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    
    </script>
</body>
</html>
