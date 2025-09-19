/**
 * Archivo: function.bienes.js
 * Descripción: Contiene toda la lógica de JavaScript para la gestión de bienes (activos),
 *              incluyendo la inicialización de la tabla, operaciones CRUD (Crear, Leer,
 *              Actualizar, Eliminar) a través de peticiones asíncronas (fetch),
 *              y el manejo de modales con SweetAlert2.
 * Autor: [Tu Nombre]
 * Fecha: [Fecha Actual]
 */

// Variable global para la instancia de la DataTable
let tableBienes;

/**
 * Se ejecuta cuando el contenido del DOM ha sido completamente cargado.
 * Es el punto de entrada principal para la inicialización de la página.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Selección de elementos del DOM para un acceso más eficiente
    const formBien = document.querySelector("#formBien");

    // Inicializa la DataTable con configuraciones específicas
    tableBienes = $('#tableBienes').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url + "src/plugins/js/es_es.json"
        },
        "ajax": {
            "url": base_url + "Bienes/getBienes",
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_bien" },
            { "data": "descripcion_bien" },
            { "data": "departamento_bien" },
            { "data": "grupo" },
            { "data": "subgrupo" },
            { "data": "seccion" },
            { "data": "status_bien" },
            { "data": "acciones" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // Carga los datos iniciales para los selects del formulario
    loadFormSelects();

    // Evento de envío del formulario para crear o actualizar un bien
    formBien.onsubmit = async function(e) {
        e.preventDefault();
        
        // Validación simple de campos (se puede expandir)
        const descripcion = document.querySelector('#descripcion').value;
        if (descripcion.trim() === '') {
            notifi("La descripción es obligatoria.", "warning");
            return;
        }

        try {
            const formData = new FormData(formBien);
            const url = base_url + 'Bienes/setBien';
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                closeModal();
                formBien.reset();
                notifi(data.message, "success");
                tableBienes.ajax.reload(); // Recarga la DataTable
            } else {
                notifi(data.message, "error");
            }
        } catch (error) {
            console.error('Error:', error);
            notifi("Ocurrió un error en la operación.", "error");
        }
    };
});

/**
 * Carga de forma asíncrona los datos para los menús desplegables (selects) del formulario.
 * Esto evita tener que cargar los datos con PHP en la vista, haciendo la carga inicial más rápida.
 */
async function loadFormSelects() {
    try {
        const url = base_url + 'Bienes/getInitialData';
        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            const { departamentos, grupos, subgrupos, secciones } = result.data;
            
            // Funciones auxiliares para poblar los selects
            const populateSelect = (selectId, data, valueField, textField) => {
                const select = document.querySelector(`#${selectId}`);
                select.innerHTML = `<option value="">Seleccionar</option>`;
                data.forEach(item => {
                    select.innerHTML += `<option value="${item[valueField]}">${item[textField]}</option>`;
                });
            };

            populateSelect('departamento', departamentos, 'depatamento_bien_id', 'departamento_bien');
            populateSelect('grupo', grupos, 'id_grupo', 'grupo');
            populateSelect('subgrupo', subgrupos, 'subgrupo_id', 'subgrupo');
            populateSelect('seccion', secciones, 'seccion_id', 'seccion');
            populateSelect('listDeptoQR', departamentos, 'depatamento_bien_id', 'departamento_bien'); // Añadido para el modal de QR
        }
    } catch (error) {
        console.error("Error al cargar datos para los selects:", error);
    }
}

/**
 * Abre el modal para agregar un nuevo bien.
 * Limpia el formulario y ajusta los textos del modal.
 */
function openModal() {
    document.querySelector('#id_bien').value = "";
    document.querySelector('#titleModal').innerHTML = "Nuevo Bien";
    document.querySelector('#btnActionText').innerHTML = "Guardar";
    document.querySelector("#formBien").reset();
    document.querySelector('#modalFormBien').classList.remove('hidden');
}

/**
 * Cierra el modal de formulario de bienes.
 */
function closeModal() {
    document.querySelector('#modalFormBien').classList.add('hidden');
}

/**
 * Obtiene los datos de un bien específico para editarlo.
 * @param {number} id_bien - El ID del bien a editar.
 */
async function fntEditBien(id_bien) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Bien";
    document.querySelector('#btnActionText').innerHTML = "Actualizar";

    try {
        const url = `${base_url}Bienes/getBien/${id_bien}`;
        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            const bien = result.data;
            // Llenar el formulario con los datos del bien
            document.querySelector("#id_bien").value = bien.id_bien;
            document.querySelector("#descripcion").value = bien.descripcion_bien;
            document.querySelector("#departamento").value = bien.bien_depatamento_id;
            document.querySelector("#grupo").value = bien.grupo_id;
            document.querySelector("#subgrupo").value = bien.subgrupo_id;
            document.querySelector("#seccion").value = bien.seccion_id;
            document.querySelector("#status_bien").value = bien.status_bien;
            
            // La fecha ya viene formateada desde el controlador
            document.querySelector("#fecha_adquisicion").value = bien.fecha_adquisicion;
            
            document.querySelector('#modalFormBien').classList.remove('hidden');
        } else {
            notifi(result.message, "error");
        }
    } catch (error) {
        console.error('Error:', error);
        notifi("Ocurrió un error al obtener los datos del bien.", "error");
    }
}

/**
 * Elimina un bien después de una confirmación.
 * @param {number} id_bien - El ID del bien a eliminar.
 */
function fntDelBien(id_bien) {
    Swal.fire({
        title: 'Eliminar Bien',
        text: "¿Realmente quieres eliminar este bien?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No, cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const formData = new FormData();
                formData.append('id_bien', id_bien);

                const url = base_url + 'Bienes/delBien';
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    notifi(data.message, "success");
                    tableBienes.ajax.reload();
                } else {
                    notifi(data.message, "error");
                }
            } catch (error) {
                console.error('Error:', error);
                notifi("Ocurrió un error en la operación de eliminación.", "error");
            }
        }
    });
}

/**
 * Funciones para el modal de QR (a implementar si es necesario).
 * Estas funciones se dejan como plantilla para la futura implementación de la
 * generación de códigos QR.
 */
function openModalQR() {
    // Lógica para abrir el modal de QR
    document.querySelector('#modalQR').classList.remove('hidden');
}

function closeModalQR() {
    const qrResultDiv = document.querySelector('#qrResult');
    const qrcodeDiv = document.querySelector('#qrcode');
    const deptoSelect = document.querySelector('#listDeptoQR');

    // Limpiar el contenido del QR y los botones de acción
    qrcodeDiv.innerHTML = '';
    const existingLinks = qrResultDiv.querySelector('.qr-actions');
    if (existingLinks) {
        existingLinks.remove();
    }

    // Ocultar el resultado y resetear el select
    qrResultDiv.classList.add('hidden');
    deptoSelect.value = '';

    // Ocultar el modal
    document.querySelector('#modalQR').classList.add('hidden');
}

function generarQR() {
    const deptoSelect = document.querySelector('#listDeptoQR');
    const selectedDeptoId = deptoSelect.value;
    const qrResultDiv = document.querySelector('#qrResult');
    const qrcodeDiv = document.querySelector('#qrcode');

    // 1. Validar que se haya seleccionado un departamento
    if (!selectedDeptoId) {
        notifi("Por favor, selecciona un departamento.", "warning");
        qrResultDiv.classList.add('hidden'); // Ocultar si no hay selección
        return;
    }

    // 2. Construir la URL que se codificará en el QR
    const urlToEncode = `${base_url}data/bienes_qr.php?departamento_id=${selectedDeptoId}`;

    // 3. Limpiar cualquier QR y enlace anterior
    qrcodeDiv.innerHTML = '';
    const existingLinks = qrResultDiv.querySelector('.qr-actions');
    if (existingLinks) {
        existingLinks.remove();
    }

    // 4. Generar el nuevo código QR usando la librería qrcode.js
    try {
        new QRCode(qrcodeDiv, {
            text: urlToEncode,
            width: 220,
            height: 220,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // 5. Crear y mostrar botones de acción (Descargar y Ver)
        setTimeout(() => {
            const qrImage = qrcodeDiv.querySelector('img');
            const departamentoNombre = deptoSelect.options[deptoSelect.selectedIndex].text;
            if (qrImage) {
                const buttonContainer = document.createElement('div');
                buttonContainer.className = 'mt-3 flex justify-center gap-2 qr-actions'; // Clase para fácil selección
                buttonContainer.innerHTML = `
                    <a href="${qrImage.src}" download="QR_${departamentoNombre.replace(/\s+/g, '_')}.png" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md transition-colors flex items-center text-sm">
                        <i class="fas fa-download mr-2"></i> Descargar
                    </a>
                    <a href="${urlToEncode}" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition-colors flex items-center text-sm">
                        <i class="fas fa-external-link-alt mr-2"></i> Ver Tabla
                    </a>
                `;
                qrResultDiv.appendChild(buttonContainer);
            }
        }, 100); // Pequeño timeout para asegurar que la imagen del QR se haya renderizado

        // 6. Mostrar el resultado
        qrResultDiv.classList.remove('hidden');
        notifi("Código QR generado correctamente.", "success");
    } catch (error) {
        console.error("Error al generar el QR:", error);
        notifi("No se pudo generar el código QR. Asegúrate de que la librería qrcode.js esté cargada.", "error");
        qrResultDiv.classList.add('hidden');
    }
}