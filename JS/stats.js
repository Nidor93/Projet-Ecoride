fetch('api_stats.php')
    .then(response => response.json())
    .then(stats => {
        new Chart(document.getElementById('chartTrajets'), {
            type: 'line',
            data: {
                labels: stats.labels,
                datasets: [{ 
                    label: 'Covoiturages / jour', 
                    data: stats.trajets, 
                    borderColor: 'green',
                    backgroundColor: 'rgba(0, 128, 0, 0.1)',
                    fill: true
                }]
            }
    });

        new Chart(document.getElementById('chartGains'), {
            type: 'bar',
            data: {
                labels: stats.labels,
                datasets: [{ 
                    label: 'Crédits gagnés / jour', 
                    data: stats.gains, 
                    backgroundColor: 'green' 
                }]
            }
        });
    })
    .catch(error => console.error('Erreur lors du chargement des stats:', error));