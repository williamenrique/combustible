<?= head($data)?>
<style>
    .btn-icon {
        padding: 0.4rem 0.8rem !important;
        font-size: 0.85rem !important;
        border-radius: 0.375rem !important;
        font-weight: 500 !important;
        transition: all 0.2s ease-in-out !important;
        border: 1px solid transparent !important;
    }

    .btn-icon i {
        margin-right: 0.4rem !important;
        font-size: 0.9rem !important;
    }

    .btn-icon:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-icon:active {
        transform: translateY(0);
    }

    /* Estilos específicos para cada botón */
    #btnImprimirCierre {
        margin-right: 5px;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        border-color: #0056b3 !important;
    }

    #btnImprimirPdf {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        border-color: #c82333 !important;
    }

    /* Para hover states */
    #btnImprimirCierre:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%) !important;
    }

    #btnImprimirPdf:hover {
        background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%) !important;
    }

    /* Versión compacta extra */
    .btn-icon-compact {
        padding: 0.3rem 0.6rem !important;
        font-size: 0.8rem !important;
    }

    .btn-icon-compact i {
        margin-right: 0.3rem !important;
        font-size: 0.85rem !important;
    }

    /* Agregar estos estilos si es necesario */
#selectFechaCierre {
    cursor: pointer;
}

#selectFechaCierre:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

#totalLitrosFecha {
    transition: all 0.3s ease;
}

/* Efecto de hover para las opciones del select */
#selectFechaCierre option:hover {
    background-color: #f3f4f6;
}
</style>
<div class="content-area">

    <!-- <div class="min-h-screen p-6 bg-gray-100 dark:bg-gray-900 transition-colors duration-300"> -->
    <div class="p-6  dark:bg-gray-900 transition-colors duration-300">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col items-start space-y-2 transition-colors duration-300">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-gas-pump text-2xl text-blue-500 dark:text-blue-400"></i>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Litros Vendidos (Total)</h3>
                </div>
                <p id="totalLitrosSistema" class="text-4xl font-bold text-gray-900 dark:text-white">0 L</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total acumulado desde el inicio del sistema.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col items-start space-y-2 transition-colors duration-300">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-calendar-alt text-2xl text-green-500 dark:text-green-400"></i>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Litros por Fecha</h3>
                </div>
                <div class="w-full">
                    <label for="selectFechaCierre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Selecciona una Fecha:</label>
                    <select id="selectFechaCierre" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md transition-colors duration-300"></select>
                </div>
                <p id="totalLitrosFecha" class="text-4xl font-bold text-gray-900 dark:text-white mt-2">0 L</p>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 transition-colors duration-300">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Historial de Cierres Realizados</h3>
            <div class="overflow-x-auto">
                <table id="cierresTable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"># Cierre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Empleado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tasa del Día</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha del Cierre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Efectivo Bs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Punto de Venta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total de Ambos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Litros</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="cierresTableBody">
                    </tbody>
                </table>
            </div>
        </div>
        
        <div id="ventasCierreSection" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-colors duration-300 hidden">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Ventas del Cierre #<span id="cierreIdTitle" class="text-blue-500">  </span>
            </h3>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                Fecha: <span id="fechaventa" class="text-blue-500"></span>
            </h3>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                Usuario: <span id="usuario" class="text-blue-500"></span>
            </h3>
            
            <div id="ventasCierreList" class="space-y-4">
                </div>
            <div class="mt-6 flex justify-end">
                <button id="btnImprimirCierre" class="btn btn-primary btn-sm btn-icon">
                    <i class="fas fa-print"></i>Imprimir Cierre
                </button>
                <button id="btnImprimirPdf" class="btn btn-danger btn-sm btn-icon">
                    <i class="fas fa-file-pdf"></i>PDF
                </button>
            </div>
        </div>

    </div>
</div>

<?= footer($data)?>