<x-app-layout>
    <x-slot name="header">
        <!-- Contenedor superior estilizado como un rectángulo verde corporativo con texto en blanco -->
        <div class="rounded-2xl p-6 shadow-sm" style="background-color: #004d2e;">
            <h2 class="font-bold text-2xl leading-tight m-0" style="color: #ffffff;">
                {{ __('Cuadro de Mando: Estadísticas de Deserción Estudiantil') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen" style="background-color: #f4f6f8;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Tarjetas de Estadísticas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-5" style="border-left: 5px solid #004d2e; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                    <p class="text-xs uppercase font-bold tracking-wider" style="color: #000000;">Total Estudiantes</p>
                    <p class="text-2xl font-black mt-2" style="color: #004d2e;">{{ $statsEstudiantes['total_estudiantes'] }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-5" style="border-left: 5px solid #ef4444; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                    <p class="text-xs uppercase font-bold tracking-wider" style="color: #ef4444;">Riesgo Alto</p>
                    <p class="text-2xl font-black text-red-600 mt-2">{{ $statsEstudiantes['riesgo_alto'] }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-5" style="border-left: 5px solid #f17a28; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                    <p class="text-xs uppercase font-bold tracking-wider" style="color: #f17a28;">Riesgo Medio</p>
                    <p class="text-2xl font-black mt-2" style="color: #f17a28;">{{ $statsEstudiantes['riesgo_medio'] }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-5" style="border-left: 5px solid #10b981; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                    <p class="text-xs uppercase font-bold tracking-wider" style="color: #10b981;">Riesgo Bajo</p>
                    <p class="text-2xl font-black text-emerald-600 mt-2">{{ $statsEstudiantes['riesgo_bajo'] }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-5" style="border-left: 5px solid #3b82f6; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                    <p class="text-xs uppercase font-bold tracking-wider" style="color: #3b82f6;">En Orientación</p>
                    <p class="text-2xl font-black text-blue-600 mt-2">{{ $statsEstudiantes['con_psicoorientacion'] }}</p>
                </div>
            </div>

            <!-- Sección de Gráficos de Visualización de Datos -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-3xl p-6" style="border: 1px solid #e2e8f0;">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                    <div>
                        <h3 class="text-lg font-bold" style="color: #000000; margin: 0;">Análisis y Tendencias de Alertas</h3>
                        <p class="text-xs text-slate-500 mt-1">Representación gráfica del estado actual de la deserción en la institución.</p>
                    </div>
                </div>

                <!-- Grid para organizar los dos gráficos cara a cara -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Gráfico de Barras - Distribución de Riesgos -->
                    <div class="p-4 border border-slate-100 rounded-2xl bg-slate-50/50">
                        <h4 class="text-sm font-bold text-slate-700 mb-4 text-center">Distribución por Niveles de Riesgo</h4>
                        <div class="relative w-full" style="height: 300px;">
                            <canvas id="barChartRiesgos"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico de Pastel - Proporción General -->
                    <div class="p-4 border border-slate-100 rounded-2xl bg-slate-50/50">
                        <h4 class="text-sm font-bold text-slate-700 mb-4 text-center">Proporción del Universo Analizado</h4>
                        <div class="relative w-full flex justify-center" style="height: 300px;">
                            <canvas id="pieChartEstudiantes"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts de Chart.js y construcción dinámica de los gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const alto = Number("{{ $statsEstudiantes['riesgo_alto'] ?? 0 }}");
            const medio = Number("{{ $statsEstudiantes['riesgo_medio'] ?? 0 }}");
            const bajo = Number("{{ $statsEstudiantes['riesgo_bajo'] ?? 0 }}");
            const orientacion = Number("{{ $statsEstudiantes['con_psicoorientacion'] ?? 0 }}");

            // 1. Configuración del Gráfico de Barras
            const ctxBar = document.getElementById('barChartRiesgos').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['Riesgo Alto', 'Riesgo Medio', 'Riesgo Bajo'],
                    datasets: [{
                        label: 'Cantidad de Estudiantes',
                        data: [alto, medio, bajo],
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.85)',
                            'rgba(241, 122, 40, 0.85)',
                            'rgba(16, 185, 129, 0.85)'
                        ],
                        borderColor: ['#ef4444', '#f17a28', '#10b981'],
                        borderWidth: 1.5,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });

            // 2. Configuración del Gráfico de Pastel
            const ctxPie = document.getElementById('pieChartEstudiantes').getContext('2d');
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: ['Riesgo Alto', 'Riesgo Medio', 'Riesgo Bajo', 'En Orientación'],
                    datasets: [{
                        data: [alto, medio, bajo, orientacion],
                        backgroundColor: ['#ef4444', '#f17a28', '#004d2e', '#3b82f6'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                    }
                }
            });
        });
    </script>
</x-app-layout>