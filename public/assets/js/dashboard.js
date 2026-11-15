document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('ticketsChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
                datasets: [{
                    label: 'Tickets Nuevos',
                    data: [12, 19, 3, 5, 2, 3, 7],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#4e73df',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Tickets Resueltos',
                    data: [10, 15, 6, 8, 4, 1, 5],
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#1cc88a',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#e3e6f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
