<?= head($data)?>
    <style>
        .form-input-custom {
            height: 2.5rem; /* Ajuste para inputs más pequeños */
            border-radius: 0.375rem; /* rounded-md */
            padding-left: 0.75rem; /* pl-3 */
            padding-right: 0.75rem; /* pr-3 */
            font-size: 0.875rem; /* text-sm */
        }
        .dark-mode .bg-white {
            background-color: var(--dark-secondary);
            color: var(--dark-text);
        }
        .dark-mode .text-gray-700 {
            color: var(--dark-text);
        }
        .dark-mode .border-gray-300 {
            border-color: var(--dark-border);
        }
    </style>
    <!-- Content Area -->
    <div class="content-area">
        <div class="bg-gray-100 dark:bg-gray-800 transition-colors duration-300">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Estación de Servicio</h2>
                            <div class="flex items-center">
                                <span id="tasaDisplay" class="text-sm font-semibold text-gray-500 dark:text-gray-400">Tasa del Día: </span>
                                <input type="text" id="txtTasa" style="font-size: 30px" class="form-input-custom w-24 ml-2 text-center" placeholder="0.00">
                                <button id="btnUpdateTasa" class="bg-blue-500 text-white p-1.5 rounded-md ml-2 hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-sync"></i>
                                </button>
                            </div>
                        </div>

                        <form id="ventaForm" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="col-span-1">
                                <label for="txtListTipoVehiculo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Vehículo</label>
                                <select id="txtListTipoVehiculo" class="form-select w-full form-input-custom border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></select>
                            </div>
                            <div class="col-span-1">
                                <label for="txtLTS" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Litros</label>
                                <input type="number" id="txtLTS" placeholder="0.00" step="0.01" class="form-input w-full form-input-custom border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                            </div>
                            <div class="col-span-1">
                                <label for="txtListTipoPago" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Pago</label>
                                <select id="txtListTipoPago" class="form-select w-full form-input-custom border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></select>
                            </div>
                            <div class="col-span-1">
                                <label for="txtMonto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monto a Pagar</label>
                                <input type="text" id="txtMonto" placeholder="0.00" class="form-input w-full form-input-custom border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white" readonly>
                            </div>
                        
                            <div class="col-span-2 text-right mt-4">
                                <button type="submit" id="btnRegisterSale" class="bg-green-500 text-white px-6 py-2 rounded-md font-semibold hover:bg-green-600 transition-colors">
                                    <i class="fas fa-save mr-2"></i> Registrar Venta
                                </button>
                            </div>
                        </form>

                        <hr class="my-6 border-gray-200 dark:border-gray-600">

                        <div id="resumenVentas">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Resumen de Ventas del Día</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-inner flex flex-col items-center">
                                    <i class="fas fa-gas-pump text-3xl text-blue-500"></i>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center">Total Litros</p>
                                        <p class="text-xl font-semibold text-gray-800 dark:text-white text-center"><span id="totalLitros">0.00</span> Lts</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-inner flex flex-col items-center">
                                    <i class="fas fa-car text-3xl text-green-500"></i>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center">Atendidos</p>
                                        <p class="text-xl font-semibold text-gray-800 dark:text-white text-center"><span id="totalVehiculos">0</span></p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-inner flex flex-col items-center">
                                    <i class="fas fa-money-check-alt text-3xl text-yellow-500"></i>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center">Total Ingreso</p>
                                        <p class="text-xl font-semibold text-gray-800 dark:text-white text-center">
                                            <span id="totalBolivares">0.00</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div id="tiposPagosContainer" class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow" style="display: none;">
                                    <h4 class="font-bold text-gray-800 dark:text-white mb-2">Por Tipo de Pago</h4>
                                    <ul id="tiposPagosList" class="text-sm text-gray-600 dark:text-gray-300 space-y-1"></ul>
                                </div>
                                
                                <div id="tiposVehiculosContainer" class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow" style="display: none;">
                                    <h4 class="font-bold text-gray-800 dark:text-white mb-2">Por Tipo de Vehículo</h4>
                                    <ul id="tiposVehiculosList" class="text-sm text-gray-600 dark:text-gray-300 space-y-1"></ul>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 mt-6">
                                <button id="btnCerrarDia" class="bg-red-500 text-white px-4 py-2 rounded-md font-semibold hover:bg-red-600 transition-colors">
                                    <i class="fas fa-door-closed mr-2"></i> Cerrar Día
                                </button>
                                <button id="btnImprimirDetallado" class="bg-purple-500 text-white px-4 py-2 rounded-md font-semibold hover:bg-purple-600 transition-colors">
                                    <i class="fas fa-print mr-2"></i> Imprimir Detallado
                                </button>
                                <button id="btnGenerarPDF" class="bg-gray-500 text-white px-4 py-2 rounded-md font-semibold hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-file-pdf mr-2"></i> Generar PDF
                                </button>
                            </div>
                        </div>

                        <hr class="my-6 border-gray-200 dark:border-gray-600">

                        <div id="cierrePendienteSection" class="mt-6" style="display: none;">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Ventas Pendientes por Cerrar</h3>
                            <div id="cierrePendienteButtons" class="space-y-2"></div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Vista Previa del Ticket</h3>
                            <pre id="ticketPreview" class="bg-gray-100 dark:bg-gray-800 p-4 rounded-md text-sm text-gray-700 dark:text-gray-300 overflow-auto"></pre>
                        </div>

                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Tickets de Venta del Día</h3>
                            <div class="relative overflow-x-auto">
                                <table id="ventasTable" class="min-w-full table-auto text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="py-3 px-6">Ticket</th>
                                            <th scope="col" class="py-3 px-6">Fecha</th>
                                            <th scope="col" class="py-3 px-6">Vehículo</th>
                                            <th scope="col" class="py-3 px-6">Litros</th>
                                            <th scope="col" class="py-3 px-6">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= footer($data)?>