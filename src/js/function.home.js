document.addEventListener('DOMContentLoaded', () => {
    const startMonthSelect = document.getElementById('startMonth');
    const endMonthSelect = document.getElementById('endMonth');
    const generateChartBtn = document.getElementById('generateChartBtn');
    const chartCanvas = document.getElementById('monthlyLitersChart');
    const noDataMessage = document.getElementById('noLitersDataMessage');
    // Agregar estas variables al inicio del DOMContentLoaded
    let dailyChart = null;
    noDataMessage.classList.remove('hidden');

    // Función para obtener y popular los meses disponibles
    async function populateMonthSelects() {
        try {
            const response = await fetch(base_url + 'Home/getAvailableMonths');
            const result = await response.json();
            if (result.success && result.data.length > 0) {
                result.data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.mes;
                    option.textContent = item.mes;
                    startMonthSelect.appendChild(option.cloneNode(true));
                    endMonthSelect.appendChild(option);
                });
                endMonthSelect.value = result.data[result.data.length - 1].mes;
            } else {
                notifi('No se encontraron meses en la base de datos.', 'warning');
                noDataMessage.textContent = "No se encontraron meses en la base de datos.";
                noDataMessage.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error al cargar los meses:', error);
            notifi('Error de conexión. Intenta recargar la página.', 'error');
            noDataMessage.textContent = "Error de conexión. Intenta recargar la página.";
            noDataMessage.classList.remove('hidden');
        }
    }
    // funciones para chart por dia
    // Agregar esta función después de populateMonthSelects()
    async function loadDailySales() {
        try {
            const response = await fetch(base_url + 'Home/getDailySales', {
                method: 'POST'
            });
            
            const result = await response.json();
            
            if (result.success) {
                renderDailySalesChart(result.data);
                updateDailySummary(result.data.daily_summary, result.data.fecha);
            } else {
                notifi('Error', result.message || 'Error al cargar ventas del día', 'error');
            }
        } catch (error) {
            console.error('Error al cargar ventas del día:', error);
            notifi('Error', 'Error de conexión al cargar ventas del día', 'error');
        }
    }

    function renderDailySalesChart(data) {
        const ctx = document.getElementById('dailySalesChart').getContext('2d');
        // Destruir gráfico anterior si existe
        if (dailyChart) {
            dailyChart.destroy();
        }
        const users = data.sales_by_user.map(item => item.usuario);
        const liters = data.sales_by_user.map(item => parseFloat(item.total_litros) || 0);
        const sales = data.sales_by_user.map(item => parseFloat(item.total_monto) || 0);
        dailyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: users,
                datasets: [
                    {
                        label: 'Litros Vendidos',
                        data: liters,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Monto Total ($)',
                        data: sales,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        yAxisID: 'y1',
                        type: 'line'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Litros'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Monto ($)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Ventas del Día por Usuario'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0) {
                                    label += context.raw.toFixed(2) + ' Litros';
                                } else {
                                    label += '$' + context.raw.toFixed(2);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    function updateDailySummary(summary, fecha) {
        document.getElementById('totalVentasHoy').textContent = summary.total_ventas || 0;
        document.getElementById('totalLitrosHoy').textContent = (parseFloat(summary.total_litros) || 0).toFixed(2);
        // document.getElementById('totalMontoHoy').textContent = '$' + (parseFloat(summary.total_monto) || 0).toFixed(2);
        document.getElementById('totalUsuariosHoy').textContent = summary.total_usuarios || 0;
        document.getElementById('fechaActual').textContent = formatFecha(fecha);
    }
    function formatFecha(fechaStr) {
        // Convertir de dd-mm-yy a formato más legible
        const parts = fechaStr.split('-');
        if (parts.length === 3) {
            const date = new Date(`20${parts[2]}`, parts[1] - 1, parts[0]);
            return date.toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        return fechaStr;
    }
    // Función para generar el gráfico por mes
    async function generateChart() {
        const startMonth = startMonthSelect.value;
        const endMonth = endMonthSelect.value;
        
        if (!startMonth || !endMonth) {
            notifi('Por favor, selecciona un rango de fechas válido.', 'warning');
            return;
        }
        // Validar que la fecha final no sea anterior a la inicial
        if (new Date(startMonth) > new Date(endMonth)) {
            notifi('La fecha de inicio no puede ser mayor a la fecha final.', 'warning');
            return;
        }
        const myChart = Chart.getChart(chartCanvas);
        if (myChart) {
            myChart.destroy();
        } 
        noDataMessage.classList.add('hidden');
        chartCanvas.classList.add('hidden');
        showLoading(true);
        try {
            const formData = new FormData();
            formData.append('start_month', startMonth);
            formData.append('end_month', endMonth);
            
            const response = await fetch(base_url + 'Home/getMonthlyLiters', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success && result.data && result.data.length > 0) {
                renderDoughnutChart(result.data);
                chartCanvas.classList.remove('hidden');
                notifi('Gráfico generado correctamente.', 'success');
            } else {
                notifi(result.message || 'No hay datos de ventas para el rango de fechas seleccionado.', 'info');
                noDataMessage.textContent = result.message || 'No hay datos de ventas para el rango de fechas seleccionado.';
                noDataMessage.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error al generar el gráfico:', error);
            notifi('Error al generar el gráfico. Inténtalo de nuevo.', 'error');
            noDataMessage.textContent = 'Error al generar el gráfico. Inténtalo de nuevo.';
            noDataMessage.classList.remove('hidden');
        } finally {
            showLoading(false);
        }
    }
    // Función para mostrar/ocultar carga
    function showLoading(show) {
        const loadingElement = document.getElementById('loadingIndicator') || createLoadingElement();
        loadingElement.style.display = show ? 'flex' : 'none';
    }
    function createLoadingElement() {
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'loadingIndicator';
        loadingDiv.className = 'absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 dark:bg-gray-800 dark:bg-opacity-80 hidden z-10';
        loadingDiv.innerHTML = '<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>';
        chartCanvas.parentElement.appendChild(loadingDiv);
        return loadingDiv;
    }
    // Función para renderizar el gráfico de dona
    function renderDoughnutChart(data) {
        const labels = data.map(item => {
            // Formatear el mes para mostrar en formato más amigable (ej: "Enero 2024")
            const [year, month] = item.mes_venta.split('-');
            const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                               'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });
        
        const liters = data.map(item => parseFloat(item.total_litros));
        const totalLiters = liters.reduce((sum, current) => sum + current, 0);
        
        const ctx = chartCanvas.getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Litros Vendidos',
                    data: liters,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(147, 51, 234, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(131, 24, 67, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(120, 113, 108, 0.8)'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const currentValue = tooltipItem.raw;
                                const percentage = ((currentValue / totalLiters) * 100).toFixed(1);
                                return `${tooltipItem.label}: ${currentValue.toFixed(2)} Litros (${percentage}%)`;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: `Ventas de Combustible - Total: ${totalLiters.toFixed(2)} Litros`,
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                }
            }
        });
    }
    // Eventos
    populateMonthSelects();
    generateChartBtn.addEventListener('click', generateChart);
    loadDailySales();
});