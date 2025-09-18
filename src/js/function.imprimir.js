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