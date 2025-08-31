// Fichier JavaScript principal
console.log('Application JavaScript chargée');

// Initialisation des graphiques du tableau de bord
function initDashboardCharts() {
    const chartEl = document.getElementById('activityChart');
    if (!chartEl) return;

    console.log('Initialisation du graphique...');
    
    // Vérifier si Chart.js est disponible
    if (typeof Chart === 'undefined') {
        console.error('Chart.js n\'est pas chargé');
        return;
    }

    // Créer un graphique simple
    new Chart(chartEl, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr'],
            datasets: [{
                label: 'Activité',
                data: [30, 45, 28, 50],
                borderColor: '#10B981',
                borderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// Initialiser les composants au chargement du document
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, initialisation...');
    initDashboardCharts();
});
