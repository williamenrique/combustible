document.addEventListener('DOMContentLoaded', async function() {
    const totalLitrosSistemaSpan = document.getElementById('totalLitrosSistema')
    const selectFechaCierre = document.getElementById('selectFechaCierre')
    const totalLitrosFechaSpan = document.getElementById('totalLitrosFecha')
    const cierresTableBody = document.getElementById('cierresTableBody')
    const ventasCierreSection = document.getElementById('ventasCierreSection')
    const cierreIdTitle = document.getElementById('cierreIdTitle')
    const ventasCierreList = document.getElementById('ventasCierreList')
    const btnImprimirCierre = document.getElementById('btnImprimirCierre')
    const btnImprimirPdf = document.getElementById('btnImprimirPdf')
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
    // Función para renderizar la tabla de cierres
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                        <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 show-ventas-btn" 
                            data-id="${cierre.id_cierre}" 
                            data-iduser="${cierre.id_user}" 
                            data-fecha="${cierre.fecha_cierre}">Ver Ventas</button>
                        <button class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 delete-cierre-btn" 
                            data-id="${cierre.id_cierre}" 
                            data-iduser="${cierre.id_user}" 
                            data-idstation="${cierre.id_estacion}" 
                            data-fecha="${cierre.fecha_cierre}">Eliminar Cierre</button>
                    </td>
                </tr>
            `
        })
        cierresTableBody.innerHTML = html
    }
    // Función para renderizar la lista de ventas de un cierre
    function renderVentasList(data) {
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
            const fechaCierre = e.target.dataset.fecha
            const empleado = e.target.dataset.empleado
            cierreIdTitle.textContent = idCierre
            const elemento = document.getElementById('fechaventa')
            elemento.textContent = fechaCierre
            const usuario = document.getElementById('usuario')
            try {
                const response = await fetch(base_url + 'Estacion/getVentasByCierre', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({iduser: iduser, idCierre: idCierre ,fechaCierre: fechaCierre})
                })
                const result = await response.json()
                if (result.success) {
                    renderVentasList(result.data)
                    usuario.textContent = result.data[2].empleado
                } else {
                    renderVentasList([])
                    notifi(result.message, 'error')
                }
            } catch (error) {
                console.error('Error al obtener las ventas del cierre:', error)
                notifi('Error al cargar las ventas. Intenta de nuevo.', 'error')
            }
        }
        // boton de eliminar cierres
        if (e.target.classList.contains('delete-cierre-btn')) {
            const idCierre = e.target.dataset.id;
            const idUser = e.target.dataset.iduser;
            const idStation = e.target.dataset.idstation;
            const fechaCierre = e.target.dataset.fecha;
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir. El cierre será eliminado y los tickets de venta asociados volverán a estar activos.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(base_url + 'Estacion/deleteCierre', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({
                                id_cierre: idCierre,
                                id_usuario: idUser,
                                id_estacion: idStation,
                                fecha_cierre: fechaCierre
                            })
                        });
                        const result = await response.json();
                        if (result.success) {
                            notifi('¡Eliminado!', 'success');
                            // Recargar la lista de cierres para mostrar el estado actualizado
                            loadInitialData();
                        } else {
                            notifi(result.message || 'Error al eliminar el cierre.', 'error');
                        }
                    } catch (error) {
                        console.error('Error al eliminar el cierre:', error);
                        notifi('Error de red o servidor.', 'error');
                    }
                }
            });
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
                            // Swal.fire('¡Eliminado!', result.message, 'success')
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
        // console.log(`La fecha del reporte es: ${fecha}`)
        const idUsuario = tableTickets.dataset.iduser
        // console.log(`El ID de usuario es: ${idUsuario}`)
        try {
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
            const tableTickets = document.querySelector('.tableTickets')
            const idCierre = cierreIdTitle.textContent
            const fecha = tableTickets.dataset.fecha
            const idUsuario = tableTickets.dataset.iduser
            // Obtén el ID de usuario, por ejemplo, de una variable de sesión global o un campo oculto
            const response = await fetch(base_url + 'Estacion/generarReportePdf', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({idUser: idUsuario, fecha: fecha }) // <-- ¡Agrega el idUser aquí!
            })
            const result = await response.json()
            if (result.success) {
                notifi('Generando PDF...', 'success')
                // console.log(result.data.dataTotal)
                fntGenerarPDF(result.data) 
            } else {
                notifi(result.message, 'error')
            }
        } catch (error) {
            notifi('Error al generar el PDF de ventas.', 'error')
        }
    })
    // funcion para generar el pdf
    fntGenerarPDF = (reporteData) => {
        // Verificar que los datos existan antes de enviarlos
        if (reporteData) {
            // Crear un formulario oculto
            const form = document.createElement('form')
            form.method = 'POST'
            form.action = base_url + "data/reporte.php"
            form.target = '_blank' // Abrir en una nueva pestaña
            // Crear un input para los datos y asignarle el JSON
            const input = document.createElement('input')
            input.type = 'hidden'
            input.name = 'reporteData'
            input.value = JSON.stringify(reporteData)
            // Agregar el input y el formulario al cuerpo del documento
            form.appendChild(input)
            document.body.appendChild(form)
            // Enviar el formulario
            form.submit()
            // Limpiar el formulario después del envío
            document.body.removeChild(form)
        } else {
            console.error("Error: Los datos para el PDF están incompletos.")
        }
    }
    // La función ahora recibe el objeto de datos directamente
    fntImprimirCierre = (dataCierre) => {
        // Verificar que los datos de cierre existan antes de enviarlos
        if (dataCierre) {
            // Enviar los datos de cierre a la URL de impresión
            $.ajax({
                type: 'post',
                cache: false,
                url: base_url + "data/cierre.php",
                data: { dataTicket: JSON.stringify(dataCierre) },
                success: function (server) {
                    console.log(server) // Imprime la respuesta del servidor
                },
                error: function(xhr) {
                    console.error("Error al enviar los datos de cierre para impresión.")
                }
            })
        } else {
            notifi("Error: Los datos de cierre están incompletos.", 'error')
            // console.error("Error: Los datos de cierre están incompletos.")
        }
    }
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