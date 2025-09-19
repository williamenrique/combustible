let tableDepartamentos;
let tableRoles;

document.addEventListener('DOMContentLoaded', function() {
    tableDepartamentos = $('#tableDepartamentos').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url + "src/plugins/js/es_es.json"
        },
        "ajax": {
            "url": base_url + "User/getDepartamentos", // Apunta a UserController
            "dataSrc": ""
        },
        "columns": [
            { "data": "departamento_id" },
            { "data": "departamento_nombre" },
            { "data": "departamento_status" },
            { "data": "acciones" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "asc"]]
    });

    // NUEVO DEPARTAMENTO
    const formDepto = document.querySelector("#formDepto");
    formDepto.onsubmit = async function(e) {
        e.preventDefault();
        
        const nombre = document.querySelector('#txtNombreDepto').value;
        if (nombre.trim() === '') {
            notifi("El nombre es obligatorio.", "warning");
            return;
        }

        try {
            const formData = new FormData(formDepto);
            const url = base_url + 'User/setDepartamento'; // Apunta a UserController
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                closeDeptoModal();
                formDepto.reset();
                notifi(data.message, "success");
                tableDepartamentos.ajax.reload();
            } else {
                notifi(data.message, "error");
            }
        } catch (error) {
            console.error('Error:', error);
            notifi("Ocurrió un error en la operación.", "error");
        }
    };

    // --- INICIALIZACIÓN DE ROLES ---
    tableRoles = $('#tableRoles').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": { "url": base_url + "src/plugins/js/es_es.json" },
        "ajax": {
            "url": base_url + "User/getRolesForTable",
            "dataSrc": ""
        },
        "columns": [
            { "data": "rol_id" },
            { "data": "rol_nombre" },
            { "data": "rol_status" },
            { "data": "acciones" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "asc"]]
    });

    const formRol = document.querySelector("#formRol");
    formRol.onsubmit = async function(e) {
        e.preventDefault();
        const nombre = document.querySelector('#txtNombreRol').value;
        if (nombre.trim() === '') {
            notifi("El nombre del rol es obligatorio.", "warning");
            return;
        }
        try {
            const formData = new FormData(formRol);
            const url = base_url + 'User/setRol';
            const response = await fetch(url, { method: 'POST', body: formData });
            const data = await response.json();
            if (data.success) {
                closeRolModal();
                formRol.reset();
                notifi(data.message, "success");
                tableRoles.ajax.reload();
            } else {
                notifi(data.message, "error");
            }
        } catch (error) {
            console.error('Error:', error);
            notifi("Ocurrió un error en la operación.", "error");
        }
    };
});

// --- FUNCIONES PARA DEPARTAMENTOS ---
function openModal() {
    document.querySelector('#idDepartamento').value = "";
    document.querySelector('#titleModal').innerHTML = "Nuevo Departamento";
    document.querySelector('#btnActionTextDepto').innerHTML = "Guardar";
    document.querySelector("#formDepto").reset();
    document.querySelector('#modalFormDepto').classList.remove('hidden');
}

function closeDeptoModal() {
    document.querySelector('#modalFormDepto').classList.add('hidden');
}

async function fntEditDepto(iddepto) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Departamento";
    document.querySelector('#btnActionTextDepto').innerHTML = "Actualizar";

    try {
        const url = `${base_url}User/getDepartamento/${iddepto}`; // Apunta a UserController
        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            const depto = result.data;
            document.querySelector("#idDepartamento").value = depto.departamento_id;
            document.querySelector("#txtNombreDepto").value = depto.departamento_nombre;
            document.querySelector("#txtDescripcionDepto").value = depto.departamento_descripcion;
            document.querySelector("#listStatusDepto").value = depto.departamento_status;
            
            document.querySelector('#modalFormDepto').classList.remove('hidden');
        } else {
            notifi(result.message, "error");
        }
    } catch (error) {
        console.error('Error:', error);
        notifi("Ocurrió un error al obtener los datos.", "error");
    }
}

function fntDelDepto(iddepto) {
    Swal.fire({
        title: 'Eliminar Departamento',
        text: "¿Realmente quieres eliminar este departamento?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No, cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const formData = new FormData();
                formData.append('idDepartamento', iddepto);

                const url = base_url + 'User/delDepartamento'; // Apunta a UserController
                const response = await fetch(url, { method: 'POST', body: formData });
                const data = await response.json();

                if (data.success) {
                    notifi(data.message, "success");
                    tableDepartamentos.ajax.reload();
                } else {
                    notifi(data.message, "error");
                }
            } catch (error) {
                console.error('Error:', error);
                notifi("Ocurrió un error en la operación.", "error");
            }
        }
    });
}

// --- FUNCIONES PARA ROLES ---
function openRolModal() {
    document.querySelector('#idRol').value = "";
    document.querySelector('#titleModalRol').innerHTML = "Nuevo Rol";
    document.querySelector('#btnActionTextRol').innerHTML = "Guardar";
    document.querySelector("#formRol").reset();
    document.querySelector('#modalFormRol').classList.remove('hidden');
}

function closeRolModal() {
    document.querySelector('#modalFormRol').classList.add('hidden');
}

async function fntEditRol(idrol) {
    document.querySelector('#titleModalRol').innerHTML = "Actualizar Rol";
    document.querySelector('#btnActionTextRol').innerHTML = "Actualizar";
    try {
        const url = `${base_url}User/getRol/${idrol}`;
        const response = await fetch(url);
        const result = await response.json();
        if (result.success) {
            const rol = result.data;
            document.querySelector("#idRol").value = rol.rol_id;
            document.querySelector("#txtNombreRol").value = rol.rol_nombre;
            document.querySelector("#txtDescripcionRol").value = rol.rol_descripcion;
            document.querySelector("#listStatusRol").value = rol.rol_status;
            document.querySelector('#modalFormRol').classList.remove('hidden');
        } else {
            notifi(result.message, "error");
        }
    } catch (error) {
        console.error('Error:', error);
        notifi("Ocurrió un error al obtener los datos del rol.", "error");
    }
}

function fntDelRol(idrol) {
    Swal.fire({
        title: 'Eliminar Rol',
        text: "¿Realmente quieres eliminar este rol?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No, cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const formData = new FormData();
                formData.append('idRol', idrol);
                const url = base_url + 'User/delRol';
                const response = await fetch(url, { method: 'POST', body: formData });
                const data = await response.json();
                if (data.success) {
                    notifi(data.message, "success");
                    tableRoles.ajax.reload();
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