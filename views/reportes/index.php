<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes de Ventas - Rinconcito Norteño</title>
    <link rel="stylesheet" href="../css/reporte.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="contenedor-reportes">
        <div class="cabecera-reporte">
            <div>
                <a href="../dashboard/index.php" class="btn-regresar">
                    <i class="fas fa-arrow-left"></i> Panel
                </a>
                <h1>📊 Dashboard de Rendimiento</h1>
                <p>Monitorea la recaudación real y los platos más pedidos.</p>
            </div>
            <div class="selectores-tiempo">
                <button class="btn-tiempo active" onclick="cambiarFiltro('dia', this)">Hoy</button>
                <button class="btn-tiempo" onclick="cambiarFiltro('semana', this)">Esta Semana</button>
                <button class="btn-tiempo" onclick="cambiarFiltro('mes', this)">Este Mes</button>
                <button class="btn-tiempo" onclick="cambiarFiltro('anio', this)">Este Año</button>
            </div>
        </div>

        <div class="tarjetas-reporte-dinero">
            <div class="card-dinero">
                <div class="icono-dinero total"><i class="fas fa-sack-dollar"></i></div>
                <div>
                    <h3>TOTAL RECAUDADO</h3>
                    <p id="txtTotalRecaudado">S/ 0.00</p>
                </div>
            </div>
            <div class="card-dinero">
                <div class="icono-dinero cantidad"><i class="fas fa-utensils"></i></div>
                <div>
                    <h3>PLATOS VENDIDOS</h3>
                    <p id="txtPlatosVendidos">0 unidades</p>
                </div>
            </div>
            <div class="card-dinero">
                <div class="icono-dinero promedio"><i class="fas fa-chart-line"></i></div>
                <div>
                    <h3>PLATO MÁS RENTABLE</h3>
                    <p id="txtPlatoEstrella" style="font-size: 16px; margin-top: 5px;">Ninguno</p>
                </div>
            </div>
        </div>

        <div class="export-herramientas">
            <button onclick="exportar('excel')" class="btn-export xls">
                <i class="fas fa-file-excel"></i> Descargar Excel
            </button>
            <button onclick="exportar('pdf')" class="btn-export pdf">
                <i class="fas fa-file-pdf"></i> Imprimir Reporte PDF
            </button>
        </div>

        <div class="grid-graficos">
            <div class="card-grafico">
                <h3>🔥 El Top 7 Más Vendido (Unidades)</h3>
                <div class="canvas-container">
                    <canvas id="graficoBarras"></canvas>
                </div>
            </div>
            <div class="card-grafico">
                <h3>💰 Distribución de Ingresos (S/.)</h3>
                <div class="canvas-container">
                    <canvas id="graficoPastel"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        let filtroActual = 'dia';
        let chartBarras = null;
        let chartPastel = null;

        function cargarReportes() {
            const tbody = document.querySelector('.tabla-modal tbody');
            const totalCaja = document.getElementById('txtTotalRecaudado');
            const totalPlatos = document.getElementById('txtPlatosVendidos');
            const platoMasRentable = document.getElementById('txtPlatoEstrella');
            
            // 1. Limpiar los contadores de dinero al iniciar
            totalCaja.innerText = "S/ 0.00";
            totalPlatos.innerText = "0 unidades";
            platoMasRentable.innerText = "Ninguno";
            
            // 2. PETICIÓN AJAX SEGÚN EL FILTRO ACTIVO
            fetch(`../../controllers/ObtenerDatosReporte.php?filtro=${filtroActual}`)
                .then(res => res.json())
                .then(datos => {
                    let sumaDinero = 0;
                    let sumaPlatos = 0;
                    let platoEstrella = "Ninguno";
                    let maxRecaudado = 0;

                    // 3. VALIDACIÓN DE DATOS VACÍOS: Si no hay ventas, destruir gráficos viejos y salir
                    if (!datos || datos.length === 0) {
                        if (chartBarras) chartBarras.destroy();
                        if (chartPastel) chartPastel.destroy();
                        return;
                    }

                    // Preparar los datos para Chart.js
                    const nombres = datos.map(item => item.nombre);
                    const cantidades = datos.map(item => item.total_vendido);
                    const recaudado = datos.map(item => item.total_recaudado);

                    // Calcular los totales de dinero dinámicamente
                    datos.forEach(item => {
                        sumaDinero += parseFloat(item.total_recaudado);
                        sumaPlatos += parseInt(item.total_vendido);
                        if (parseFloat(item.total_recaudado) > maxRecaudado) {
                            maxRecaudado = parseFloat(item.total_recaudado);
                            platoEstrella = item.nombre;
                        }
                    });

                    // Pintar los valores en las tarjetas de dinero superiores
                    totalCaja.innerText = `S/ ${sumaDinero.toFixed(2)}`;
                    totalPlatos.innerText = `${sumaPlatos} und.`;
                    platoMasRentable.innerText = platoEstrella;

                    // 4. DESTRUIR GRÁFICOS ANTERIORES PARA EVITAR SOLAPAMIENTOS
                    if (chartBarras) chartBarras.destroy();
                    if (chartPastel) chartPastel.destroy();

                    // 5. RENDERIZAR GRÁFICO DE BARRAS (AZUL ELEGANTE)
                    const ctxBarras = document.getElementById('graficoBarras').getContext('2d');
                    chartBarras = new Chart(ctxBarras, {
                        type: 'bar',
                        data: {
                            labels: nombres,
                            datasets: [{
                                label: 'Unidades Vendidas',
                                data: cantidades,
                                backgroundColor: '#2563eb',
                                borderRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1 // Forzar saltos de uno en uno en unidades
                                    }
                                }
                            }
                        }
                    });

                    // 6. RENDERIZAR GRÁFICO DE PASTEL (MULTICOLOR INGRESOS)
                    const ctxPastel = document.getElementById('graficoPastel').getContext('2d');
                    chartPastel = new Chart(ctxPastel, {
                        type: 'doughnut',
                        data: {
                            labels: nombres,
                            datasets: [{
                                data: recaudado,
                                backgroundColor: ['#ff9f43', '#28c76f', '#ea5455', '#00cfcf', '#7367f0', '#ffc107', '#6c757d']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let value = context.raw;
                                            return ` Recaudado: S/ ${value.toFixed(2)}`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error("Error al cargar el JSON de reportes:", error);
                    alert("Error en el servidor al cargar las estadísticas.");
                });
        }

        // Función para cambiar de filtro (Día, Semana, Mes, Año)
        function cambiarFiltro(tipo, boton) {
            filtroActual = tipo;
            document.querySelectorAll('.btn-tiempo').forEach(b => b.classList.remove('active'));
            boton.classList.add('active');
            cargarReportes();
        }

        // Función de exportación de archivos
        function exportar(formato) {
            window.location.href = `../../controllers/ExportarReportes.php?formato=${formato}&filtro=${filtroActual}`;
        }

        // Ejecutar carga inicial al abrir la página
        document.addEventListener("DOMContentLoaded", cargarReportes);
    </script>
</body>
</html>