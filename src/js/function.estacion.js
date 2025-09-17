document.addEventListener('DOMContentLoaded', function() {
    const ventaForm = document.getElementById('ventaForm')
    const tasaInput = document.querySelector('#txtTasa')
    const ltsInput = document.querySelector('#txtLTS')
    const montoInput = document.querySelector('#txtMonto')
    const selectTipoVehiculo = document.getElementById('txtListTipoVehiculo')
    const selectTipoPago = document.getElementById('txtListTipoPago')
    const ticketPreview = document.getElementById('ticketPreview')
    const btnUpdateTasa = document.getElementById('btnUpdateTasa')
    const ventasTableBody = document.querySelector('#ventasTable tbody')
    const totalVehiculosSpan = document.getElementById('totalVehiculos')
    const totalLitrosSpan = document.getElementById('totalLitros')
    const totalBolivaresSpan = document.getElementById('totalBolivares')
    const totalDivisasSpan = document.getElementById('totalDivisas')
    const tiposVehiculosContainer = document.getElementById('tiposVehiculosContainer')
    const tiposVehiculosList = document.getElementById('tiposVehiculosList')
    const tiposPagosContainer = document.getElementById('tiposPagosContainer')
    const tiposPagosList = document.getElementById('tiposPagosList')
    const btnCerrarDia = document.getElementById('btnCerrarDia')
    const btnImprimirDetallado = document.getElementById('btnImprimirDetallado')
    const btnGenerarPDF = document.getElementById('btnGenerarPDF')
    const cierrePendienteSection = document.getElementById('cierrePendienteSection')
    const cierrePendienteButtons = document.getElementById('cierrePendienteButtons')
    // Inicializar DataTables
    const ventasDataTable = $('#ventasTable').DataTable({
        "dom": 'lfrtip',
        "language": {
            // Cambia la URL para que apunte a tu archivo local
            "url": base_url + "src/plugins/js/datatable/es_es.json"
        }
    })
    // Función para cargar los datos iniciales
    async function loadInitialData() {
        try {
            const response = await fetch(base_url + 'Estacion/initialData')
            const data = await response.json()
            if (data.success) {
                // Cargar tipos de vehículo
                selectTipoVehiculo.innerHTML = ''
                data.tiposVehiculo.forEach(vehiculo => {
                    const option = document.createElement('option')
                    option.value = vehiculo.id_tipo_vehiculo
                    option.textContent = vehiculo.nombre
                    selectTipoVehiculo.appendChild(option)
                })
                // Cargar tipos de pago
                selectTipoPago.innerHTML = ''
                data.tiposPago.forEach(pago => {
                    const option = document.createElement('option')
                    option.value = pago.id_tipo_pago
                    option.textContent = pago.nombre
                    selectTipoPago.appendChild(option)
                })
                // Cargar la tasa del día
                if (data.tasa) {
                    tasaInput.value = data.tasa.tasa_dia
                }
                // Cargar tickets recientes
                updateVentasTable(data.ultimosTickets)
                // Cargar resumen de ventas
                updateDailySummary(data.resumen)
                // Cargar ventas pendientes
                updateVentasPendientes(data.ventasPendientes)
            } else {
                notifi(data.message, 'error')
            }
        } catch (error) {
            notifi('Error al cargar los datos iniciales: ' + error.message, 'error')
        }
    }
    // Funciones de actualización de la UI
    function updateVentasTable(tickets) {
        ventasDataTable.clear()
        tickets.forEach(ticket => {
            const rowNode = ventasDataTable.row.add([
                ticket.id_venta,
                ticket.fecha_venta,
                ticket.tipoVehiculo,
                ticket.litros,
                `<button class="bg-blue-500 text-white p-1.5 rounded-md hover:bg-blue-600 print-ticket-btn" data-id="${ticket.id_venta}" data-fecha="${ticket.fecha_venta}" data-iduser="${ticket.id_user}"><i class="fas fa-print"></i></button>`
            ]).draw(false).node()
            $(rowNode).addClass('bg-white dark:bg-gray-800 border-b dark:border-gray-700')
        })
    }
    // tarjetas de resumen diario
    function updateDailySummary(resumen) {
        if (resumen) {
            // Calcular el total de efectivo en bolívares (Efectivo Bs + Divisa convertida)
            const efectivoMasDivisaBs = (parseFloat(resumen.total_bs) || 0)
            // Calcular el Total General
            totalVehiculosSpan.textContent = resumen.total_ventas
            totalLitrosSpan.textContent = parseFloat(resumen.total_litros).toFixed(2) + " L"
            totalBolivaresSpan.textContent = efectivoMasDivisaBs.toFixed(2) + " Bs"
            // Actualizar Tipos de Pago y mostrar solo si hay datos
            tiposPagosList.innerHTML = ''
            const pagosExistentes = (resumen.total_divisa > 0) || (resumen.total_efectivo > 0) || (resumen.total_debito > 0)
            if (pagosExistentes) {
                if (resumen.total_divisa > 0) {
                    tiposPagosList.innerHTML += `<li><i class="fas fa-dollar-sign text-green-500 mr-2"></i>Divisa: ${parseFloat(resumen.total_divisa).toFixed(2)} $</li>`
                }
                if (resumen.total_efectivo > 0) {
                    tiposPagosList.innerHTML += `<li><i class="fas fa-money-bill-wave text-purple-500 mr-2"></i>Efectivo: ${parseFloat(resumen.total_efectivo).toFixed(2)} Bs</li>`
                }
                if (resumen.total_debito > 0) {
                    tiposPagosList.innerHTML += `<li><i class="fas fa-credit-card text-blue-500 mr-2"></i>Punto de Venta: ${parseFloat(resumen.total_debito).toFixed(2)} Bs</li>`
                }
                tiposPagosContainer.style.display = 'block'
            } else {
                tiposPagosContainer.style.display = 'none'
            }

            // Actualizar Tipos de Vehículo y mostrar si hay datos
            tiposVehiculosList.innerHTML = ''
            if (resumen.tiposVehiculo && resumen.tiposVehiculo.length > 0) {
                resumen.tiposVehiculo.forEach(item => {
                    let iconClass = "fa-car-side" // Ícono por defecto
                    if (item.tipo_vehiculo.includes('Moto')) {
                        iconClass = "fa-motorcycle"
                    } else if (item.tipo_vehiculo.includes('Camion')) {
                        iconClass = "fa-solid fa-truck"
                    }
                    tiposVehiculosList.innerHTML += `<li><i class="fas ${iconClass} text-yellow-500 mr-2"></i>${item.tipo_vehiculo}: ${item.cantidad}</li>`
                })
                tiposVehiculosContainer.style.display = 'block'
            } else {
                tiposVehiculosContainer.style.display = 'none'
            }

            // Ocultar o mostrar los botones si no hay ventas
            if (resumen.total_ventas === 0) {
                btnCerrarDia.style.display = 'none'
                btnImprimirDetallado.style.display = 'none'
                btnGenerarPDF.style.display = 'none'
            } else {
                btnCerrarDia.style.display = 'block'
                btnImprimirDetallado.style.display = 'block'
                btnGenerarPDF.style.display = 'block'
            }
        }
    }
    // Función para actualizar la UI de ventas pendientes
    function updateVentasPendientes(ventas) {
        if (ventas && ventas.length > 0) {
            cierrePendienteSection.style.display = 'block'
            cierrePendienteButtons.innerHTML = ''
            ventas.forEach(venta => {
                cierrePendienteButtons.innerHTML += `
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-red-500">Día sin cierre: ${venta.fecha_venta}</span>
                        <button class="bg-red-500 text-white text-xs px-2 py-1 rounded-md hover:bg-red-600 close-pending-btn" data-fecha="${venta.fecha_venta}" data-iduser="${venta.id_user}">Cerrar Día</button>
                        <button class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-md hover:bg-yellow-600 pdf-btn" data-fecha="${venta.fecha_venta}" data-iduser="${venta.id_user}">PDF</button>
                    </div>
                `
            })
        } else {
            cierrePendienteSection.style.display = 'none'
        }
    }
    // Lógica de cálculo dinámico del monto a pagar
    function calcularMonto() {
        const selectedOption = selectTipoPago.options[selectTipoPago.selectedIndex]
        const tasa = parseFloat(tasaInput.value) || 0
        const lts = parseFloat(ltsInput.value) || 0
        const calcularMonto = {
            '1': () => lts * 0.5,
            '2': () => lts * 0.5 * tasa,
            '3': () => lts * 0.5 * tasa
        }
        const resultado = calcularMonto[selectedOption.value]?.()
        if (resultado !== undefined) {
            montoInput.value = Number(resultado.toFixed(2))
        }
        updateTicketPreview()
    }
    // Lógica de vista previa del ticket
    function updateTicketPreview() {
        const tipoVehiculo = selectTipoVehiculo.options[selectTipoVehiculo.selectedIndex]?.text || ''
        const tipoPago = selectTipoPago.options[selectTipoPago.selectedIndex]?.text || ''
        const cantidad = ltsInput.value
        const precioTotal = montoInput.value
        const tipoPagoId = selectTipoPago.value
        const simbolo = tipoPagoId === '1' ? '$' : 'BS'
        const fecha = new Date().toLocaleString('es-ES', { dateStyle: 'short', timeStyle: 'short' })

        const previewText = `
        ====== ESTACIÓN DE SERVICIO ======

        TICKET DE VENTA
        Fecha: ${fecha}
        ----------------------------------
        Tipo Pago: ${tipoPago}
        Tipo Vehículo: ${tipoVehiculo}
        Cantidad: ${cantidad} Litros
        Precio Total: ${precioTotal} ${simbolo}
        =============================
        ¡Gracias por su compra!
        `
        ticketPreview.textContent = previewText
    }
    // Nueva función para imprimir el detalle de ventas
    fntImprimirDetallado = (dataCierre) => {
        // Verificar que los datos de cierre existan antes de enviarlos
        if (dataCierre) {
            // Enviar los datos de cierre a la URL de impresión
            $.ajax({
                type: 'post',
                cache: false,
                url: base_url + "data/detallado.php",
                data: { dataTicket: JSON.stringify(dataCierre) },
                success: function (server) {
                    console.log(server) // Imprime la respuesta del servidor
                },
                error: function(xhr) {
                    console.error("Error al enviar los datos de cierre para impresión.")
                }
            })
        } else {
            console.error("Error: Los datos de cierre están incompletos.")
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
            console.error("Error: Los datos de cierre están incompletos.")
        }
    }
    fntImprimirTicket = (datTicket) => {
        // Verificar que los datos de cierre existan antes de enviarlos
        if (datTicket) {
            // Enviar los datos de cierre a la URL de impresión
            $.ajax({
                type: 'post',
                cache: false,
                url: base_url + "data/ticket.php",
                data: { dataTicket: JSON.stringify(datTicket) },
                success: function (server) {
                    console.log(server) // Imprime la respuesta del servidor
                },
                error: function(xhr) {
                    console.error("Error al enviar los datos de cierre para impresión.")
                }
            })
        } else {
            console.error("Error: Los datos de cierre están incompletos.")
        }
    }
    // probando que si no hay conexion coin la impresora muestre una advertencia
    fntImprimirTicket1 = (datTicket) => {
        if (!datTicket) {
            notifi('Error: Los datos del ticket están incompletos.','error')
            console.error("Error: Los datos del ticket están incompletos.")
            return
        }
        // Mostrar loading
        notifi('Imprimiendo ticket...')
        $.ajax({
            type: 'POST',
            cache: false,
            url: base_url + "data/ticket.php",
            data: { dataTicket: JSON.stringify(datTicket) },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    notifi( response.message,'success')
                    console.log("Ticket impreso correctamente")
                } else {
                    notifi( response.error || 'Error al imprimir el ticket','error')
                    console.error("Error de impresión:", response.error)
                }
            },
            error: function(xhr, status, error) {
                const errorMsg = 'Error de conexión con la impresora: ' + error
                notifi(errorMsg,'error')
                console.error("Error AJAX:", errorMsg)
            },
            complete: function() {
                // Ocultar loading si es necesario
                // hideLoading()
            }
        })
    }
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
    // funcion para generar el pdf
    fntGenerarPDF2 = (reporteData) => {
        // Verificar que los datos existan antes de enviarlos
        if (!reporteData) {
            console.error("Error: Los datos para el PDF están incompletos.")
            return
        }
        console.log(JSON.stringify(reporteData))
        // Usar fetch para enviar los datos como JSON
        fetch(base_url + "data/reporte.php", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(reporteData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok')
            }
            return response.blob() // Obtener la respuesta como un objeto Blob
        })
        .then(blob => {
            // Crear una URL para el Blob y abrirlo en una nueva ventana
            const url = window.URL.createObjectURL(blob)
            const a = document.createElement('a')
            a.href = url
            a.target = '_blank'
            a.click()
            window.URL.revokeObjectURL(url) // Liberar la URL del Blob
        })
        .catch(error => {
            console.error('Hubo un problema con la petición fetch:', error)
            notifi('Ocurrio un error al generar el PDF.', 'error')
        })
    }
    // Event Listeners
    selectTipoPago.addEventListener('change', calcularMonto)
    ltsInput.addEventListener('input', calcularMonto)
    tasaInput.addEventListener('input', calcularMonto)
    selectTipoVehiculo.addEventListener('change', updateTicketPreview)
    // Formulario de venta
    ventaForm.addEventListener('submit', async function(e) {
        e.preventDefault()
        const lts = parseFloat(ltsInput.value)
        if (lts <= 0 || isNaN(lts)) {
            notifi('Por favor, ingrese una cantidad de litros válida.', 'warning')
            return
        }
        const formData = new FormData(ventaForm)
        formData.append('txtListTipoVehiculo', selectTipoVehiculo.value)
        formData.append('txtListTipoPago', selectTipoPago.value)
        formData.append('txtLTS', ltsInput.value)
        formData.append('txtMonto', montoInput.value)
        formData.append('txtTasa', tasaInput.value)
        formData.append('action', 'registrarVenta')
        try {
            const response = await fetch(base_url + 'Estacion/registrarVenta', {
                method: 'POST',
                body: formData
            })
            const result = await response.json()
            if (result.success) {
                notifi(result.message, 'success')
                result.copia = 0
                // Llamar a la función de impresión con los datos recibidos del servidor
                fntImprimirTicket(result)
                // Limpiar formulario y actualizar UI
                ventaForm.reset()
                ticketPreview.textContent = ''
                updateTicketPreview()
                await loadInitialData()
            } else {
                notifi(result.message, 'error')
            }
        } catch (error) {
            Swal.fire('Error en la solicitud: ' + error.message, 'error')
        }
    })
    // Botón para actualizar la tasa
    btnUpdateTasa.addEventListener('click', async function() {
        const nuevaTasa = tasaInput.value
        if (nuevaTasa > 0) {
            try {
                const response = await fetch(base_url + 'Estacion/updateTasa', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ action: 'updateTasa', tasa: nuevaTasa })
                })
                const result = await response.json()
                if (result.success) {
                    notifi(result.message, 'success')
                } else {
                    notifi(result.message, 'error')
                }
            } catch (error) {
                notifi('Error al actualizar la tasa.', 'error')
            }
        } else {
            notifi('Ingrese una tasa válida.', 'warning')
        }
    })
    // Botones del resumen
    btnCerrarDia.addEventListener('click', async function() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esto cerrará el día y no se podrán registrar más ventas para esta fecha.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cerrar día',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    // Generar la fecha actual en formato 'd-m-y'
                    const today = new Date()
                    const day = String(today.getDate()).padStart(2, '0')
                    const month = String(today.getMonth() + 1).padStart(2, '0')
                    const year = today.getFullYear().toString().slice(-2)
                    const fechaCierre = `${day}-${month}-${year}`
                    // Enviar la solicitud POST con la fecha en el cuerpo JSON
                    const response = await fetch(base_url + 'Estacion/cerrarDia', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ userId: userId, fecha_cierre: fechaCierre })
                    })
                    const result = await response.json()
                    if (result.success) {
                        notifi('Cierre de Día exitoso', 'success')
                        // notifi('Cierre de Día', result.message, 'success')
                        fntImprimirCierre(result.dataCierre)
                        // Usamos los datos devueltos por el servidor para actualizar
                        updateDailySummary(result.resumen)
                        // Ocultamos la sección de cierres pendientes
                        // updateVentasPendientes([])
                        // Limpiamos la tabla de ventas
                        ventasDataTable.clear().draw()
                    } else {
                        notifi(result.message, 'error')
                    }
                } catch (error) {
                    notifi('Error al cerrar el día.', 'error')
                }
            }
        })
    })
    //TODO: Botones de impresión y PDF
    btnImprimirDetallado.addEventListener('click', async () => {
        try {
            // Generar la fecha actual en formato 'd-m-y'
            const today = new Date()
            const day = String(today.getDate()).padStart(2, '0')
            const month = String(today.getMonth() + 1).padStart(2, '0')
            const year = today.getFullYear().toString().slice(-2)
            const fechaDetalle = `${day}-${month}-${year}`
            const response = await fetch(base_url + 'Estacion/getDetalleVentas', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ fecha_detalle: fechaDetalle })
            })
            const result = await response.json()
            if (result.success) {
                fntImprimirDetallado(result.ticketData)
            } else {
                notifi( result.message, 'error')
            }
        } catch (error) {
            notifi('Error al obtener los detalles de ventas.', 'error')
        }
    })
    // En tu archivo function.estacion.js
    btnGenerarPDF.addEventListener('click', async () => {
        try {
            const today = new Date()
            const day = String(today.getDate()).padStart(2, '0')
            const month = String(today.getMonth() + 1).padStart(2, '0')
            const year = today.getFullYear().toString().slice(-2)
            const fechaReporte = `${day}-${month}-${year}`
            // Obtén el ID de usuario, por ejemplo, de una variable de sesión global o un campo oculto
            const response = await fetch(base_url + 'Estacion/generarReportePdf', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ idUser: userId,fecha: fechaReporte }) // <-- ¡Agrega el idUser aquí!
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
    // generar pdf de cierre pendiente
    document.addEventListener('click', async (event) => {
        // Verificar si el elemento clicado o alguno de sus padres es el botón PDF
        const button = event.target.closest('.pdf-btn')
        if (button) {
            const fechaReporte = button.dataset.fecha
            const idUser = button.dataset.iduser
            try {
                const response = await fetch(base_url + 'Estacion/generarReportePdf', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        fecha: fechaReporte,
                        idUser: idUser
                    })
                })
                const result = await response.json()
                if (result.success) {
                    notifi('Generando PDF...', 'success')
                    fntGenerarPDF(result.data) 
                } else {
                    notifi(result.message, 'error')
                }
            } catch (error) {
                notifi('Error al generar el PDF de ventas. Inténtalo de nuevo.', 'error')
            }
        }
    })
    // Tu función fntGenerarPDF está bien como está, ya que la lógica del formulario es la correcta
    // Delegación de eventos para la tabla y cierres pendientes
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.print-ticket-btn')) {
            try {
                const idVenta = e.target.closest('.print-ticket-btn').dataset.id
                const dataFecha = e.target.closest('.print-ticket-btn').dataset.fecha
                const idUser = e.target.closest('.print-ticket-btn').dataset.iduser
                // console.log('aqui ' + idVenta)
                const response = await fetch(base_url + 'Estacion/getTicket', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({idVenta : idVenta, idUser : idUser, fechaTicket: dataFecha })
                })
                const result = await response.json()
                if (result.success) {
                    result.copia = 1
                    fntImprimirTicket(result)
                } else {
                    notifi( result.message, 'error')
                }
            } catch (error) {
                notifi( 'Error al obtener el ticket.', 'error')
            }
        }
        if (e.target.closest('.close-pending-btn')) {
            const fechaCierre = e.target.closest('.close-pending-btn').dataset.fecha
            const idUser = e.target.closest('.close-pending-btn').dataset.iduser
            Swal.fire({
                title: '¿Deseas cerrar las ventas de este día?',
                text: `Esto generará el reporte de cierre para el día ${fechaCierre}.`,
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
                            body: JSON.stringify({ fecha_cierre: fechaCierre })
                        })
                        const result = await response.json()
                        if (result.success) {
                            notifi(result.message, 'success')
                            // 1. Llama a la función para imprimir el resumen de cierre
                            fntImprimirCierre(result.dataCierre)
                            // 2. Realiza una nueva solicitud para obtener los datos detallados
                            const detailedResponse = await fetch(base_url + 'Estacion/getDetalleVentas', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/json'},
                                body: JSON.stringify({idUser: idUser ,fecha_detalle: fechaCierre })
                            })
                            const detailedResult = await detailedResponse.json()
                            if (detailedResult.success) {
                                // 3. Llama a la función para imprimir el reporte detallado con sus propios datos
                                fntImprimirDetallado(detailedResult.ticketData)
                            } else {
                                // Manejar error si no se pueden obtener los datos detallados
                                notifi( detailedResult.message, 'error')
                            }
                            // Usamos los datos devueltos para actualizar el resumen
                            updateDailySummary(result.resumen)
                            // Recargamos los datos para actualizar la lista de cierres pendientes
                            await loadInitialData()
                        } else {
                            notifi( result.message, 'error')
                        }
                    } catch (error) {
                        notifi('Error al cerrar el turno pendiente.', 'error')
                    }
                }
            })
        }
    })
    // Cargar datos al iniciar
    loadInitialData()
    updateTicketPreview()
})