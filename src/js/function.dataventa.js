document.addEventListener('DOMContentLoaded', async function() {
    const totalLitrosSistemaSpan = document.getElementById('totalLitrosSistema')
    const selectFechaCierre = document.getElementById('selectFechaCierre')
    const totalLitrosFechaSpan = document.getElementById('totalLitrosFecha')
    const cierresTableBody = document.getElementById('cierresTableBody')
    const ventasCierreSection = document.getElementById('ventasCierreSection')
    const cierreIdTitle = document.getElementById('cierreIdTitle')
    const fechaCierreTitle = document.getElementById('fechaventa')
    const usuarioTitle = document.getElementById('usuario')
    const ventasCierreList = document.getElementById('ventasCierreList')
    const btnImprimirCierre = document.getElementById('btnImprimirCierre')
    const btnImprimirPdf = document.getElementById('btnImprimirPdf')
    const btnAccionCierre = document.getElementById('btnAccionCierre')
    const openSalesTableBody = document.getElementById('openSalesTableBody')
    const noOpenSalesMessage = document.getElementById('noOpenSalesMessage')
    
    // Función para cargar todos los datos iniciales
    async function loadInitialData() {
        // Cargar total de litros del sistema
        try {
            const response = await fetch(base_url + 'Estacion/getLitrosTotales')
            const result = await response.json()
            if (result.success) {
                totalLitrosSistemaSpan.textContent = `${parseFloat(result.totalLitros).toFixed(2)} L`
            }
        } catch (error) {
            console.error('Error al cargar total de litros:', error)
        }
        // Cargar historial de cierres
        try {
            const response = await fetch(base_url + 'Estacion/getHistorialCierres')
            const result = await response.json()
            if (result.success && result.data.length > 0) {
                renderCierresTable(result.data)
            } else {
                cierresTableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-gray-500 dark:text-gray-400">No hay cierres registrados.</td></tr>'
            }
        } catch (error) {
            console.error('Error al cargar historial de cierres:', error)
            cierresTableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-red-500">Error al cargar datos.</td></tr>'
        }
    }

    /**
     * Cargar y mostrar la lista de ventas abiertas
     */
    async function loadOpenSales() {
        try {
            const response = await fetch(base_url + 'Estacion/getOpenSales')
            const result = await response.json()
            
            if (result.success && result.data.length > 0) {
                noOpenSalesMessage.classList.add('hidden')
                renderOpenSalesTable(result.data)
            } else {
                noOpenSalesMessage.classList.remove('hidden')
                openSalesTableBody.innerHTML = ''
            }
        } catch (error) {
            console.error('Error al cargar ventas abiertas:', error)
            notifi('Error al cargar las ventas abiertas.', 'error')
        }
    }
    // Función para renderizar la tabla de ventas abiertas
    function renderOpenSalesTable(data) {
        let html = ''
        data.forEach(sale => {
            html += `
                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${sale.nombre} ${sale.apellido}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-200">${sale.fecha_venta}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-200">${parseFloat(sale.total_litros).toFixed(2)} L</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 close-sale-btn" data-fecha="${sale.fecha_venta}" data-iduser="${sale.id_user}">
                            Cerrar Venta
                        </button>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 print-pdf-btn" data-id="${sale.id_cierre}" data-iduser="${sale.id_user}" data-fecha="${sale.fecha_venta}">
                            Imprimir PDF
                        </button>
                    </td>
                </tr>
            `
        })
        openSalesTableBody.innerHTML = html
    }
    // Event listener para los botones de ventas abiertas
    openSalesTableBody.addEventListener('click', async function(e) {
        if (e.target.classList.contains('close-sale-btn')) {
            const fechaVenta = e.target.dataset.fecha
            const userId = e.target.dataset.iduser
            Swal.fire({
                title: '¿Deseas cerrar esta venta?',
                text: `Esto generará el reporte de cierre para la venta seleccionada.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(base_url + 'Estacion/cerrarTurnoPendiente', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({userId: userId, fecha_cierre: fechaVenta})
                        })
                        const result = await response.json()
                        if (result.success) {
                            notifi('Venta cerrada exitosamente', 'success')
                            // 1. Llama a la función para imprimir el resumen de cierre
                            if (typeof fntImprimirCierre === 'function') {
                                fntImprimirCierre(result.dataCierre)
                            }
                            // 2. Realiza una nueva solicitud para obtener los datos detallados
                            try {
                                const detailedResponse = await fetch(base_url + 'Estacion/getDetalleVentas', {
                                    method: 'POST',
                                    headers: {'Content-Type': 'application/json'},
                                    body: JSON.stringify({idUser: userId, fecha_detalle: fechaVenta})
                                })
                                const detailedResult = await detailedResponse.json()
                                
                                if (detailedResult.success) {
                                    // 3. Llama a la función para imprimir el reporte detallado
                                    if (typeof fntImprimirDetallado === 'function') {
                                        fntImprimirDetallado(detailedResult.ticketData)
                                    }
                                } else {
                                    // Manejar error si no se pueden obtener los datos detallados
                                    notifi(detailedResult.message, 'error')
                                }
                            } catch (detailError) {
                                console.error('Error al obtener datos detallados:', detailError)
                                notifi('Error al obtener el detalle de ventas', 'error')
                            }
                            // Recargar ventas abiertas y datos iniciales
                            loadOpenSales()
                            loadInitialData()
                            
                        } else {
                            notifi(result.message, 'error')
                        }
                    } catch (error) {
                        console.error('Error al cerrar venta:', error)
                        notifi('Error al cerrar la venta', 'error')
                    }
                }
            })
        }
        if (e.target.classList.contains('print-pdf-btn')) {
            try {
                // Obtener los datos directamente del botón que se hizo clic
                // const idVenta = e.target.dataset.id;
                const fechaVenta = e.target.dataset.fecha;
                const userId = e.target.dataset.iduser; // Asegúrate de que este atributo existe en el botón
                const response = await fetch(base_url + 'Estacion/generarReportePdf', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        fecha: fechaVenta,    // Fecha de la venta
                        idUser: userId        // ID del usuario
                    })
                });
                const result = await response.json();
                if (result.success) {
                    notifi('Generando PDF...', 'success');
                    if (typeof fntGenerarPDF === 'function') {
                        fntGenerarPDF(result.data);
                    } else {
                        notifi('Error: Función de PDF no disponible', 'error');
                    }
                } else {
                    notifi(result.message, 'error');
                }
            } catch (error) {
                console.error('Error al generar PDF:', error);
                notifi('Error al generar el PDF de ventas.', 'error');
            }
        }
    })
    // Función para renderizar la tabla de cierres
    function renderCierresTable1(data) {
        let html = ''
        data.forEach(cierre => {
            html += `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${cierre.id_cierre}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${cierre.usuario_nombres} ${cierre.usuario_apellidos}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${parseFloat(cierre.tasa_dia).toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${cierre.fecha_cierre}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${parseFloat(cierre.efectivo_bs).toFixed(2)} Bs</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${parseFloat(cierre.debito_bs).toFixed(2)} Bs</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${parseFloat(cierre.total_bs).toFixed(2)} Bs</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${parseFloat(cierre.total_litros_vendidos).toFixed(2)} L</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 show-ventas-btn" data-id="${cierre.id_cierre}" data-iduser="${cierre.id_user}" data-fecha="${cierre.fecha_cierre}">Ver Ventas</button>
                    </td>
                </tr>
            `
        })
        cierresTableBody.innerHTML = html
    }
    function renderCierresTable(data) {
        let html = ''
        data.forEach(cierre => {
            html += `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${cierre.id_cierre}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${cierre.usuario_nombres} ${cierre.usuario_apellidos}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${parseFloat(cierre.tasa_dia).toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${cierre.fecha_cierre}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${parseFloat(cierre.efectivo_bs).toFixed(2)} Bs</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${parseFloat(cierre.debito_bs).toFixed(2)} Bs</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${parseFloat(cierre.total_bs).toFixed(2)} Bs</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${parseFloat(cierre.total_litros_vendidos).toFixed(2)} L</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 show-ventas-btn" data-id="${cierre.id_cierre}" data-iduser="${cierre.id_user}" data-fecha="${cierre.fecha_cierre}">Ver Ventas</button>
                    </td>
                </tr>
            `
        })
        cierresTableBody.innerHTML = html
        
        // Inicializar DataTables después de renderizar la tabla
        initializeDataTable();
    }

    function initializeDataTable() {
        // Destruir DataTable si ya existe
        if ($.fn.DataTable.isDataTable('#cierresTable')) {
            $('#cierresTable').DataTable().destroy();
        }
        
        // Inicializar DataTable con configuración
        $('#cierresTable').DataTable({
            dom: '<"flex justify-between items-center mb-4"<"flex"l><"flex"f>><"bg-white dark:bg-gray-800 rounded-lg"rt><"flex justify-between items-center mt-4"<"flex"i><"flex"p>>',
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                }
            },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order: [[3, 'desc']], // Ordenar por fecha (columna 4) descendente
            columnDefs: [
                { orderable: false, targets: [8] } // Hacer que la columna de acciones no sea ordenable
            ],
            initComplete: function() {
                // Aplicar clases de Tailwind a los elementos de DataTables
                $('.dataTables_filter input').addClass('px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500');
                $('.dataTables_length select').addClass('px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500');
                $('.dataTables_info').addClass('text-sm text-gray-600 dark:text-gray-400');
                $('.dataTables_paginate').addClass('flex space-x-2');
                $('.paginate_button').addClass('px-3 py-1 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600');
                $('.paginate_button.current').addClass('bg-indigo-600 text-white hover:bg-indigo-700');
            }
        });
    }
    // Función para renderizar la lista de ventas de un cierre
    function renderVentasList(data) {
        // const nombreUsuario = result.data.usuarios_nombres + ' ' + result.data.usuarios_apellidos
        usuarioTitle.textContent = data[0].empleado
        const idUser = (data.length > 0) ?  data[0].id_user : null
        const fecha_venta = (data.length > 0) ?  data[0].fecha_venta : null
        let html = ''
        if (data.length === 0) {
            html = '<p class="text-center text-gray-500 dark:text-gray-400">No se encontraron ventas para este cierre.</p>'
        } else {
            // Encabezados de la tabla
            html += `
                <div data-iduser="${idUser}" data-fecha="${fecha_venta}" class=" tableTickets hidden md:grid grid-cols-7 gap-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase pb-2 border-b border-gray-200 dark:border-gray-700">
                    <div class="col-span-1">Ticket</div>
                    <div class="col-span-1">Vehículo</div>
                    <div class="col-span-1">Litros</div>
                    <div class="col-span-1">Monto Efectivo</div>
                    <div class="col-span-1">Monto Débito</div>
                    <div class="col-span-1 text-right">Acciones</div>
                </div>
            `
            // Filas de datos
            data.forEach(venta => {
                // Verificar si el monto_debito es nulo o no está definido
                const montoDebito = venta.tarjeta_debito === null ? '0.00' : parseFloat(venta.tarjeta_debito).toFixed(2)
                const montoEfectivo = venta.efectivob === null ? '0.00' : parseFloat(venta.efectivob).toFixed(2)
                
                html += `
                    <div class="grid grid-cols-1 md:grid-cols-7 gap-4 py-4 md:py-2 border-b border-gray-200 dark:border-gray-700 last:border-b-0 items-center">
                        <div class="col-span-1">
                            <span class="md:hidden text-xs font-medium text-gray-500 dark:text-gray-400">Ticket: </span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">${venta.numero_venta}</span>
                        </div>
                        <div class="col-span-1">
                            <span class="md:hidden text-xs font-medium text-gray-500 dark:text-gray-400">Vehículo: </span>
                            <span class="text-sm text-gray-900 dark:text-white">${venta.tipo_vehiculo}</span>
                        </div>
                        <div class="col-span-1">
                            <span class="md:hidden text-xs font-medium text-gray-500 dark:text-gray-400">Litros: </span>
                            <span class="text-sm text-gray-900 dark:text-white">${parseFloat(venta.cantidad_litros).toFixed(2)} L</span>
                        </div>
                        <div class="col-span-1">
                            <span class="md:hidden text-xs font-medium text-gray-500 dark:text-gray-400">Monto Efectivo: </span>
                            <span class="text-sm text-gray-900 dark:text-white">${montoEfectivo} Bs</span>
                        </div>
                        <div class="col-span-1">
                            <span class="md:hidden text-xs font-medium text-gray-500 dark:text-gray-400">Monto Débito: </span>
                            <span class="text-sm text-gray-900 dark:text-white">${montoDebito} Bs</span>
                        </div>
                        <div class="col-span-1 flex justify-start md:justify-end">
                            <button class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-venta-btn" data-id="${venta.numero_venta}" data-iduser="${venta.id_user}" data-fecha="${venta.fecha_venta}">Eliminar</button>
                        </div>
                    </div>
                `
            })
        }
        ventasCierreList.innerHTML = html
        ventasCierreSection.classList.remove('hidden')
    }
    // Event listener para los botones de la tabla de cierres
    cierresTableBody.addEventListener('click', async function(e) {
        if (e.target.classList.contains('show-ventas-btn')) {
            const idCierre = e.target.dataset.id
            const iduser = e.target.dataset.iduser
            cierreIdTitle.textContent = idCierre
            const fechaCierre = e.target.dataset.fecha
            fechaCierreTitle.textContent = fechaCierre
            try {
                const response = await fetch(base_url + 'Estacion/getVentasByCierre', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({iduser: iduser, idCierre: idCierre ,fechaCierre: fechaCierre})
                })
                const result = await response.json()
                if (result.success) {
                    
                    renderVentasList(result.data)
                } else {
                    renderVentasList([])
                    notifi(result.message, 'error')
                }
            } catch (error) {
                console.error('Error al obtener las ventas del cierre:', error)
                notifi('Error al cargar las ventas. Intenta de nuevo.', 'error')
            }
        }
    })
   // Event listener para los botones de eliminar en la lista de ventas
    ventasCierreList.addEventListener('click', async function(e) {
        if (e.target.classList.contains('delete-venta-btn')) {
            const idVenta = e.target.dataset.id
            const idUser = e.target.dataset.iduser
            const fechaTicket = e.target.dataset.fecha
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir. Se eliminará el ticket de venta permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(base_url + 'Estacion/deleteVenta', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({idVenta: idVenta ,fechaTicket: fechaTicket,idUser:idUser})
                        })
                        const result = await response.json()
                        if (result.success) {
                            notifi('¡Eliminado!', 'success')
                            // Recargar la lista de ventas después de la eliminación
                            const idCierre = cierreIdTitle.textContent
                            const responseRefresh = await fetch(base_url + 'Estacion/getVentasByCierre', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/json'},
                                body: JSON.stringify({iduser: idUser, idCierre: idCierre, fechaCierre: fechaTicket})
                            })
                            const resultRefresh = await responseRefresh.json()
                            if (resultRefresh.success) {
                                renderVentasList(resultRefresh.data)
                                loadInitialData()
                            } else {
                                renderVentasList([])
                            }
                        } else {
                            notifi(result.message, 'error')
                        }
                    } catch (error) {
                        notifi('Error al eliminar el ticket. Intenta de nuevo.', 'error')
                    }
                }
            })
        }
    })
    // Event listener para el botón de imprimir
    btnImprimirCierre.addEventListener('click', async function() {
        try {
        // const idUsuario = tableTickets.dataset.iduser
            // Generar la fecha actual en formato 'd-m-y'
            const tableTickets = document.querySelector('.tableTickets')
            const idCierre = cierreIdTitle.textContent
            const fecha = tableTickets.dataset.fecha
            const idUsuario = tableTickets.dataset.iduser
            const response = await fetch(base_url + 'Estacion/getDataCierre', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ idUser: idUsuario, fecha_venta: fecha })
            })
            const result = await response.json()
            if (result.success) {
                fntImprimirCierre(result.cierreData)
            } else {
                notifi(result.message, 'error')
            }
        } catch (error) {
            notifi('Error al obtener los detalles de ventas.', 'error')
        }
    })
    btnImprimirPdf.addEventListener('click', async function(){
        try {
            // Buscar el elemento tableTickets dentro de ventasCierreList
            const tableTickets = ventasCierreList.querySelector('.tableTickets');
            if (!tableTickets) {
                notifi('No hay datos de ventas para generar el PDF', 'error');
                return;
            }
            const fecha = tableTickets.dataset.fecha;
            const idUsuario = tableTickets.dataset.iduser
            console.log('ID Usuario:', idUsuario, 'Fecha:', fecha);
            const response = await fetch(base_url + 'Estacion/generarReportePdf', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({idUser: idUsuario, fecha: fecha })
            });
            const result = await response.json();
            if (result.success) {
                notifi('Generando PDF...', 'success');
                if (typeof fntGenerarPDF === 'function') {
                    fntGenerarPDF(result.data);
                } else {
                    notifi('La función fntGenerarPDF no está definida', 'error');
                }
            } else {
                notifi(result.message, 'error');
            }
        } catch (error) {
            console.error('Error al generar PDF:', error);
            notifi('Error al generar el PDF de ventas.', 'error');
        }
    });
    /**
     * Función para cargar dinámicamente las fechas disponibles con ventas
     * y mostrar el total de litros vendidos al seleccionar una fecha
     */
    async function loadFechasConVentas() {
        try {
            const response = await fetch(base_url + 'Estacion/getFechasConVentas')
            const result = await response.json()
            if (result.success && result.fechas.length > 0) {
                const selectFecha = document.getElementById('selectFechaCierre')
                selectFecha.innerHTML = '' // Limpiar opciones existentes
                // Agregar opción por defecto
                const defaultOption = document.createElement('option')
                defaultOption.value = ''
                defaultOption.textContent = 'Selecciona una fecha'
                defaultOption.disabled = true
                defaultOption.selected = true
                selectFecha.appendChild(defaultOption)
                // Agregar todas las fechas disponibles
                result.fechas.forEach(fecha => {
                    const option = document.createElement('option')
                    option.value = fecha.fecha_venta
                    // Formatear la fecha para mostrarla en formato más legible
                    const [day, month, year] = fecha.fecha_venta.split('-')
                    const dateObj = new Date(`20${year}`, month - 1, day)
                    const formattedDate = dateObj.toLocaleDateString('es-ES', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })
                    option.textContent = formattedDate
                    option.dataset.originalDate = fecha.fecha_venta // Guardar fecha original
                    selectFecha.appendChild(option)
                })
                // Agregar event listener para cuando se seleccione una fecha
                selectFecha.addEventListener('change', async function() {
                    const fechaSeleccionada = this.options[this.selectedIndex].dataset.originalDate
                    if (fechaSeleccionada) {
                        await loadLitrosPorFecha(fechaSeleccionada)
                    } else {
                        document.getElementById('totalLitrosFecha').textContent = '0 L'
                    }
                })
            } else {
                notifi('No se encontraron fechas con ventas registradas.', 'info')
            }
        } catch (error) {
            console.error('Error al cargar las fechas:', error)
            notifi('Error al cargar las fechas disponibles.', 'error')
        }
        // Cargar ventas abiertas
        await loadOpenSales()
    }
    /**
     * Función para cargar los litros vendidos en una fecha específica
     * @param {string} fecha - Fecha en formato dd-mm-yy
     */
    async function loadLitrosPorFecha(fecha) {
        try {
            const response = await fetch(base_url + 'Estacion/getLitrosPorFecha', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ fecha: fecha })
            })
            const result = await response.json()
            if (result.success) {
                const totalLitros = parseFloat(result.totalLitros) || 0
                document.getElementById('totalLitrosFecha').textContent = `${totalLitros.toFixed(2)} L`
                // Mostrar notificación de éxito
                notifi(`Total de litros vendidos: ${totalLitros.toFixed(2)} L`, 'success')
            } else {
                document.getElementById('totalLitrosFecha').textContent = '0 L'
                notifi(result.message || 'No hay datos para esta fecha.', 'info')
            }
        } catch (error) {
            console.error('Error al cargar litros por fecha:', error)
            document.getElementById('totalLitrosFecha').textContent = '0 L'
            notifi('Error al cargar los litros vendidos.', 'error')
        }
    }
    // Llamar a la función para cargar las fechas cuando el DOM esté listo
    // Cargar datos al iniciar
    loadFechasConVentas()
    loadInitialData()
})