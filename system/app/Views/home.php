<?= head($data)?>
    <style>
        .hidden {
            display: none;
        }

        #loadingIndicator {
            backdrop-filter: blur(2px);
        }
    </style>
    <!-- Content Area -->
    <div class="content-area">

        <!-- Sección de Resumen del Día -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-lg shadow-md">
                <div class="text-blue-800 dark:text-blue-200 text-sm font-semibold">Total Ventas Hoy</div>
                <div class="text-2xl font-bold text-blue-900 dark:text-blue-100" id="totalVentasHoy">0</div>
                <div class="text-xs text-blue-600 dark:text-blue-300" id="fechaActual"></div>
            </div>
            
            <div class="bg-green-100 dark:bg-green-900 p-4 rounded-lg shadow-md">
                <div class="text-green-800 dark:text-green-200 text-sm font-semibold">Total Litros</div>
                <div class="text-2xl font-bold text-green-900 dark:text-green-100" id="totalLitrosHoy">0.00</div>
                <div class="text-xs text-green-600 dark:text-green-300">Litros vendidos</div>
            </div>
            
            <!-- <div class="bg-purple-100 dark:bg-purple-900 p-4 rounded-lg shadow-md">
                <div class="text-purple-800 dark:text-purple-200 text-sm font-semibold">Monto Total</div>
                <div class="text-2xl font-bold text-purple-900 dark:text-purple-100" id="totalMontoHoy">$0.00</div>
                <div class="text-xs text-purple-600 dark:text-purple-300">En todas las monedas</div>
            </div> -->
            
            <div class="bg-orange-100 dark:bg-orange-900 p-4 rounded-lg shadow-md">
                <div class="text-orange-800 dark:text-orange-200 text-sm font-semibold">Usuarios Activos</div>
                <div class="text-2xl font-bold text-orange-900 dark:text-orange-100" id="totalUsuariosHoy">0</div>
                <div class="text-xs text-orange-600 dark:text-orange-300">Vendedores activos</div>
            </div>
        </div>

        <!-- Gráfico de Ventas del Día -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ventas del Día por Usuario</h3>
            <div class="relative h-96">
                <canvas id="dailySalesChart"></canvas>
                <div id="noDailyDataMessage" class="absolute inset-0 flex items-center justify-center text-gray-500 dark:text-gray-400 hidden">
                    <p>No hay ventas registradas para hoy.</p>
                </div>
            </div>
        </div>

        <!-- Contenedor de notificaciones (si no existe ya en tu layout) -->
        <div id="notificationContainer" class="fixed top-4 right-4 z-50 w-80 space-y-2"></div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mt-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ventas Mensuales (Litros)</h3>
            <div class="flex flex-col md:flex-row gap-4 items-end mb-4">
                <div class="flex-1 w-full">
                    <label for="startMonth" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Desde</label>
                    <select id="startMonth" name="start_month" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600"></select>
                </div>
                <div class="flex-1 w-full">
                    <label for="endMonth" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hasta</label>
                    <select id="endMonth" name="end_month" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600"></select>
                </div>
                <button id="generateChartBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition-colors w-full md:w-auto">Generar Gráfico</button>
            </div>
            
            <div class="relative h-96">
                <canvas id="monthlyLitersChart"></canvas>
                <div id="noLitersDataMessage" class="absolute inset-0 flex items-center justify-center text-gray-500 dark:text-gray-400 hidden">
                    <p>Selecciona un rango de fechas y haz clic en 'Generar Gráfico'.</p>
                </div>
            </div>
        </div>

    </div>
<?= footer($data)?>