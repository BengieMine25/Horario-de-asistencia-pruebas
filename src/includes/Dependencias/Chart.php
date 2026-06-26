<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>  
// Gráfico de horas por período  
const ctx = document.getElementById('horasChart').getContext('2d');  
new Chart(ctx, {  
    type: 'bar',  
    data: {  
        labels: ['Semana', 'Quincena', 'Mes', 'Año'],  
        datasets: [{  
            label: 'Horas Trabajadas',  
            data: [<?php echo $totalSemana; ?>, <?php echo $totalQuincena; ?>, <?php echo $totalMes; ?>, <?php echo $totalAnio; ?>],  
            backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545']  
        }]  
    },  
    options: {  
        responsive: true,  
        plugins: {  
            legend: {  
                labels: {  
                    color: '#ffffff'  
                }  
            }, 
        },  
        scales: {  
            x: {  
                ticks: { color: '#ffffff' }  
            },  
            y: {  
                beginAtZero: true,  
                ticks: { color: '#ffffff' }  
            }  
        }  
    }  
});  
</script>