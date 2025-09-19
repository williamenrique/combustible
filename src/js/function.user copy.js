$(document).ready(function() {
    let selectedFile = null
    let usuariosTable = null
    let originalData = {}
    // ===== FUNCIONES DE IMAGEN DE PERFIL =====
    $('#changeImageBtn, #profileImageLarge').click(function() {
        $('#profileImageInput').click()
    })
    $('#profileImageInput').change(function() {
        const file = this.files[0]
        if (file) {
            selectedFile = file
            const reader = new FileReader()
            reader.onload = function(e) {
                $('#profileImageLarge').attr('src', e.target.result)
                $('#saveImageBtn').show()
            }
            reader.readAsDataURL(file)
        }
    })
    $('#saveImageBtn').click(async function() {
        if (!selectedFile) {
            notifi('No hay imagen seleccionada', 'error')
            return
        }
        try {
            $(this).prop('disabled', true).text('Guardando...')
            const result = await saveImgUser(selectedFile)    
            $('#userImageSidebar img').attr('src', $('#profileImageLarge').attr('src'))
            $('.user-menu-btn img').attr('src', $('#profileImageLarge').attr('src'))
            $('.user-image img').attr('src', $('#profileImageLarge').attr('src'))
            $('#removeImageBtn').show()
            $('#saveImageBtn').hide()
            
            notifi('Imagen de perfil guardada correctamente', 'success')
        } catch (error) {
            console.error('Error al guardar imagen:', error)
            notifi('Error al guardar la imagen', 'error')
        } finally {
            $(this).prop('disabled', false).text('Guardar Imagen')
        }
    })
    $('#removeImageBtn').click(function() {
        const defaultImage = "data:image/svg+xmlbase64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4MCIgaGVpZ2h0PSI4MCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM2NjYiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cGF0aCBkPSJNMjAgMjF2LTJhNCA0IDAgMCAwLTQgNEg4YTQgNCAwIDAgMC00IDR2MiIvPjxjaXJjbGUgcyBjeD0iMTIiIGN5PSI3IiByPSI0Ii8+PC9zdmc+"
        $('#profileImageLarge').attr('src', defaultImage)
        $('#userImageSidebar img').attr('src', defaultImage)
        $('.user-menu-btn img').attr('src', defaultImage)
        $('#profileImageInput').val('')
        $('#removeImageBtn').hide()
        $('#saveImageBtn').hide()
    })
    // ===== FUNCIONES DE CONTRASEÑA =====
    $('#cancelPasswordBtn').click(function() {
        $('#currentPassword, #newPassword, #confirmPassword').val('')
        notifi('Cambios cancelados', 'info')
    })
    $('#savePasswordBtn').click(async function() {
        const currentPassword = $('#currentPassword').val().trim()
        const newPassword = $('#newPassword').val().trim()
        const confirmPassword = $('#confirmPassword').val().trim()
        
        if (!currentPassword || !newPassword || !confirmPassword) {
            notifi('Todos los campos son obligatorios', 'error')
            return
        }
        if (newPassword !== confirmPassword) {
            notifi('Las contraseñas nuevas no coinciden', 'error')
            return
        }
        if (newPassword.length < 1) {
            notifi('La nueva contraseña debe tener al menos 6 caracteres', 'error')
            return
        }
        if (currentPassword === newPassword) {
            notifi('La nueva contraseña debe ser diferente a la actual', 'error')
            return
        }
        
        try {
            $(this).prop('disabled', true).text('Guardando...')
            const result = await changePassword(currentPassword, newPassword)
            $('#currentPassword, #newPassword, #confirmPassword').val('')
            notifi('Contraseña cambiada exitosamente', 'success')
        } catch (error) {
            console.error('Error al cambiar contraseña:', error)
            notifi(error.message || 'Error al cambiar la contraseña', 'error')
        } finally {
            $(this).prop('disabled', false).text('Guardar Cambios')
        }
    })
    $('#currentPassword, #newPassword, #confirmPassword').keypress(function(e) {
        if (e.which === 13) {
            $('#savePasswordBtn').click()
        }
    })
    // ===== FUNCIONES DE DATOS DE USUARIO =====
    function saveOriginalData() {
        originalData = {
            usuario_nombres: $('#usuario_nombres').val(),
            usuario_apellidos: $('#usuario_apellidos').val(),
            usuario_email: $('#usuario_email').val(),
            usuario_telefono: $('#usuario_telefono').val(),
            usuario_direccion: $('#usuario_direccion').val()
        }
    }
    saveOriginalData()
    $('#cancelBtn').click(function() {
        $('#usuario_nombres').val(originalData.usuario_nombres)
        $('#usuario_apellidos').val(originalData.usuario_apellidos)
        $('#usuario_email').val(originalData.usuario_email)
        $('#usuario_telefono').val(originalData.usuario_telefono)
        $('#usuario_direccion').val(originalData.usuario_direccion)
        notifi('Cambios cancelados', 'info')
    })
    $('#userDataForm').submit(async function(e) {
        e.preventDefault()
        if (!validateForm()) return
        try {
            $('#saveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...')   
            const formData = {
                usuario_nombres: $('#usuario_nombres').val().trim(),
                usuario_apellidos: $('#usuario_apellidos').val().trim(),
                usuario_email: $('#usuario_email').val().trim(),
                usuario_telefono: $('#usuario_telefono').val().trim(),
                usuario_direccion: $('#usuario_direccion').val().trim(),
                id_usuario: $('#userDataForm').data('user-id') || 0
            }
            
            const result = await updateUserData(formData)
            saveOriginalData()
            
            if (result.userData) {
                notifi('Datos actualizados correctamente', 'success')
            }
        } catch (error) {
            console.error('Error al guardar datos:', error)
            notifi(error.message || 'Error al guardar los datos', 'error')
        } finally {
            $('#saveBtn').prop('disabled', false).html('Guardar Cambios')
        }
    })
    // ===== FUNCIONES DE CREACIÓN DE USUARIO =====
    loadSelectOptions()

    $('#userCreateForm').submit(async function(e) {
        e.preventDefault()
        if (!validateCreateForm()) return
        try {
            $('#saveCreateBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando...')
            const formData = {
                txtIdPersonal: $('#txtIdPersonal').val().trim(),
                txtNombre: $('#txtNombre').val().trim(),
                txtApellido: $('#txtApellido').val().trim(),
                txtTelefono: $('#txtTelefono').val().trim(),
                txtDireccion: $('#txtDireccion').val().trim(),
                txtEmail: $('#txtEmail').val().trim(),
                listRolId: $('#listRolId').val(),
                listDep: $('#listDep').val() || 0
            }
            const result = await createUser(formData)
            if (result.success) {
                $('#userCreateForm')[0].reset()
                notifi('Usuario creado correctamente', 'success')
            } else {
                notifi(result.message, 'error')
            }
        } catch (error) {
            console.error('Error al crear usuario:', error)
            notifi(error.message || 'Error al crear el usuario', 'error')
        } finally {
            $('#saveCreateBtn').prop('disabled', false).html('Crear Usuario')
        }
    })
    $('#cancelCreateBtn').click(function() {
        $('#userCreateForm')[0].reset()
        notifi('Formulario cancelado', 'info')
    })
    // ===== TABLA DE USUARIOS =====
    if ($('#usuariosTable').length) {
        initUsuariosTable()
    }
    window.updateUsuariosTable = function() {
        if (usuariosTable) {
            reloadUsuariosTable()
        }
    }
})
// ===== FUNCIONES GLOBALES =====
async function saveImgUser(imageFile) {
    try {
        const formData = new FormData()
        formData.append('imagen', imageFile)
        formData.append('id_usuario', $('#user').val())
        formData.append('fecha', new Date().toISOString())
        const response = await fetch(base_url + "User/subirImagen/", {
            method: 'POST',
            body: formData
        })
        if (!response.ok) throw new Error(`Error del servidor: ${response.status}`)
        
        const objData = await response.json()
        if (!objData || !objData.success) throw new Error(objData.message || 'Error al guardar la imagen')
        return objData
    } catch (error) {
        notifi('Error: ' + error.message, 'error')
        throw error
    }
}
async function changePassword(currentPassword, newPassword) {
    try {
        const response = await fetch(base_url + "User/cambiarPassword/", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                currentPassword: currentPassword,
                newPassword: newPassword,
                id_usuario: $('#user').val()
            })
        })
        if (!response.ok) throw new Error(`Error del servidor: ${response.status}`)
        
        const objData = await response.json()
        if (!objData.success) throw new Error(objData.message || 'Error al cambiar la contraseña')
        
        return objData
    } catch (error) {
        throw error
    }
}
function validateForm() {
    const nombres = $('#usuario_nombres').val().trim()
    const apellidos = $('#usuario_apellidos').val().trim()
    const email = $('#usuario_email').val().trim()
    const telefono = $('#usuario_telefono').val().trim()
    if (!nombres) {
        notifi('El nombre es obligatorio', 'error')
        $('#usuario_nombres').focus()
        return false
    }
    if (!apellidos) {
        notifi('El apellido es obligatorio', 'error')
        $('#usuario_apellidos').focus()
        return false
    }
    if (!email) {
        notifi('El correo electrónico es obligatorio', 'error')
        $('#usuario_email').focus()
        return false
    }
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(email)) {
        notifi('Por favor ingresa un correo electrónico válido', 'error')
        $('#usuario_email').focus()
        return false
    }
    if (telefono && !/^[\d\s\-\+\(\)]{10,15}$/.test(telefono)) {
        notifi('Por favor ingresa un número de teléfono válido', 'error')
        $('#usuario_telefono').focus()
        return false
    }
    return true
}
async function updateUserData(formData) {
    try {
        const response = await fetch(base_url + "User/actualizarDatos/", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        
        if (!response.ok) throw new Error(`Error del servidor: ${response.status}`)
        
        const objData = await response.json()
        if (!objData.success) throw new Error(objData.message || 'Error al actualizar los datos')
        
        return objData
    } catch (error) {
        throw error
    }
}
async function loadSelectOptions() {
    try {
        const rolesResponse = await fetch(base_url + "User/getRoles/")
        const rolesData = await rolesResponse.json()
        if (rolesData.success) {
            const rolSelect = $('#listRolId')
            rolSelect.empty().append('<option value="">Seleccionar rol</option>')
            rolesData.roles.forEach(rol => {
                rolSelect.append(`<option value="${rol.id}">${rol.nombre}</option>`)
            })
        }
        const depsResponse = await fetch(base_url + "User/getDepartments/")
        const depsData = await depsResponse.json()
        if (depsData.success) {
            const depSelect = $('#listDep')
            depSelect.empty().append('<option value="">Seleccionar departamento</option>')
            depsData.departments.forEach(dep => {
                depSelect.append(`<option value="${dep.id}">${dep.nombre}</option>`)
            })
        }
    } catch (error) {
        console.error('Error al cargar opciones:', error)
        notifi('Error al cargar opciones', 'error')
    }
}
function validateCreateForm() {
    const identificacion = $('#txtIdPersonal').val().trim()
    const nombre = $('#txtNombre').val().trim()
    const apellido = $('#txtApellido').val().trim()
    const email = $('#txtEmail').val().trim()
    const rol = $('#listRolId').val()
    if (!identificacion) {
        notifi('La identificación es obligatoria', 'error')
        $('#txtIdPersonal').focus()
        return false
    }
    if (!nombre) {
        notifi('El nombre es obligatorio', 'error')
        $('#txtNombre').focus()
        return false
    }
    if (!apellido) {
        notifi('El apellido es obligatorio', 'error')
        $('#txtApellido').focus()
        return false
    }
    if (!email) {
        notifi('El correo electrónico es obligatorio', 'error')
        $('#txtEmail').focus()
        return false
    }
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(email)) {
        notifi('Por favor ingresa un correo electrónico válido', 'error')
        $('#txtEmail').focus()
        return false
    }
    if (!rol) {
        notifi('Debe seleccionar un rol', 'error')
        $('#listRolId').focus()
        return false
    }
    return true
}
async function createUser(formData) {
    try {
        const response = await fetch(base_url + "User/setUser/", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        if (!response.ok) throw new Error(`Error del servidor: ${response.status}`)
        const objData = await response.json()
        return objData
    } catch (error) {
        throw error
    }
}
function initUsuariosTable() {
    usuariosTable = $('#usuariosTable').DataTable({
        ajax: {
            url: base_url + "User/getUsuarios/",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            { data: "usuario_id" },
            { data: "usuario_ci" },
            { 
                data: null,
                render: function(data) {
                    return `${data.usuario_nombres} ${data.usuario_apellidos}`
                }
            },
            { data: "usuario_email" },
            { data: "usuario_telefono" },
            { data: "rol_nombre" },
            { data: "departamento_nombre" },
            {
                data: "usuario_status",
                render: function(data) {
                    return data == 1 
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>'
                }
            },
            {
                data: null,
                render: function(data) {
                    return `
                        <div class="flex space-x-2">
                            <button class="btn-edit" data-id="${data.usuario_id}">
                                <i class="fas fa-edit text-blue-500"></i>
                            </button>
                            <button class="btn-status" data-id="${data.usuario_id}" data-status="${data.usuario_status}">
                                ${data.usuario_status == 1 
                                    ? '<i class="fas fa-ban text-red-500"></i>' 
                                    : '<i class="fas fa-check text-green-500"></i>'}
                            </button>
                        </div>
                    `
                },
                orderable: false
            }
        ],
        language: { url: base_url + 'src/plugins/js/es_es.json' },
        responsive: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[0, "desc"]],
        dom: '<"flex justify-between items-center mb-4"<"text-xl font-bold">f>rt<"flex justify-between items-center mt-4"lip>',
        drawCallback: function() {
            $('.btn-edit').off('click').on('click', function() {
                const userId = $(this).data('id')
                editUser(userId)
            })
            $('.btn-status').off('click').on('click', function() {
                const userId = $(this).data('id')
                const status = $(this).data('status')
                toggleStatus(userId, status)
            })
        }
    })
}
function reloadUsuariosTable() {
    if (usuariosTable) {
        usuariosTable.ajax.reload(null, false)
    }
}
async function editUser(userId) {
    try {
        const response = await fetch(base_url + "User/getUsuario/" + userId)
        const result = await response.json()
        
        if (result.success) {
            showEditModal(result.usuario)
        } else {
            notifi('Error al cargar datos del usuario', 'error')
        }
    } catch (error) {
        console.error('Error:', error)
        notifi('Error al cargar datos del usuario', 'error')
    }
}
function showEditModal(userData) {
    const modalHtml = `
        <div id="editUserModal" class="modal-overlay">
            <div class="modal-container">
                <div class="modal-header">
                    <h3 class="modal-title">Editar Usuario</h3>
                    <button class="modal-close">&times</button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" id="editUsuarioId" value="${userData.usuario_id}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="editUsuarioCi">Identificación</label>
                                <input type="number" id="editUsuarioCi" value="${userData.usuario_ci}" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="editUsuarioNombres">Nombres</label>
                                <input type="text" id="editUsuarioNombres" value="${userData.usuario_nombres}" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="editUsuarioApellidos">Apellidos</label>
                                <input type="text" id="editUsuarioApellidos" value="${userData.usuario_apellidos}" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="editUsuarioEmail">Email</label>
                                <input type="email" id="editUsuarioEmail" value="${userData.usuario_email}" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="editUsuarioTelefono">Teléfono</label>
                                <input type="tel" id="editUsuarioTelefono" value="${userData.usuario_telefono}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="editUsuarioDireccion">Dirección</label>
                                <input type="text" id="editUsuarioDireccion" value="${userData.usuario_direccion}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="editUsuarioRolId">Rol</label>
                                <select id="editUsuarioRolId" class="form-input" required>
                                    <option value="">Seleccionar rol</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editUsuarioDepartamentoId">Departamento</label>
                                <select id="editUsuarioDepartamentoId" class="form-input">
                                    <option value="">Seleccionar departamento</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editUsuarioStatus">Estado</label>
                                <select id="editUsuarioStatus" class="form-input" required>
                                    <option value="1" ${userData.usuario_status == 1 ? 'selected' : ''}>Activo</option>
                                    <option value="0" ${userData.usuario_status == 0 ? 'selected' : ''}>Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelEditBtn">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveEditBtn">Guardar Cambios</button>
                </div>
            </div>
        </div>
    `
    $('body').append(modalHtml)
    loadEditSelectOptions(userData)
    setupEditModalEvents()
    $('#editUserModal').addClass('active')
}
async function loadEditSelectOptions(userData) {
    try {
        const rolesResponse = await fetch(base_url + "User/getRoles")
        const rolesData = await rolesResponse.json()
        if (rolesData.success) {
            const rolSelect = $('#editUsuarioRolId')
            rolSelect.empty().append('<option value="">Seleccionar rol</option>')
            rolesData.roles.forEach(rol => {
                rolSelect.append(`<option value="${rol.id}" ${userData.usuario_rol_id == rol.id ? 'selected' : ''}>${rol.nombre}</option>`)
            })
        }
        const depsResponse = await fetch(base_url + "User/getDepartments")
        const depsData = await depsResponse.json()
        if (depsData.success) {
            const depSelect = $('#editUsuarioDepartamentoId')
            depSelect.empty().append('<option value="">Seleccionar departamento</option>')
            depsData.departments.forEach(dep => {
                depSelect.append(`<option value="${dep.id}" ${userData.usuario_departamento_id == dep.id ? 'selected' : ''}>${dep.nombre}</option>`)
            })
        }
    } catch (error) {
        console.error('Error al cargar opciones:', error)
    }
}
function setupEditModalEvents() {
    $('.modal-close, #cancelEditBtn').on('click', closeEditModal)
    $('#editUserModal').on('click', function(e) {
        if (e.target.id === 'editUserModal') closeEditModal()
    })
    $('#saveEditBtn').on('click', saveUserChanges)
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#editUserModal').hasClass('active')) closeEditModal()
    })
}
function closeEditModal() {
    $('#editUserModal').removeClass('active')
    setTimeout(() => $('#editUserModal').remove(), 300)
}
async function saveUserChanges() {
    try {
        const formData = {
            usuario_id: $('#editUsuarioId').val(),
            usuario_ci: $('#editUsuarioCi').val(),
            usuario_nombres: $('#editUsuarioNombres').val(),
            usuario_apellidos: $('#editUsuarioApellidos').val(),
            usuario_email: $('#editUsuarioEmail').val(),
            usuario_telefono: $('#editUsuarioTelefono').val(),
            usuario_direccion: $('#editUsuarioDireccion').val(),
            usuario_rol_id: $('#editUsuarioRolId').val(),
            usuario_departamento_id: $('#editUsuarioDepartamentoId').val(),
            usuario_status: $('#editUsuarioStatus').val()
        }
        if (!formData.usuario_nombres || !formData.usuario_apellidos || !formData.usuario_email || !formData.usuario_rol_id) {
            notifi('Todos los campos obligatorios deben estar completos', 'error')
            return
        }
        $('#saveEditBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...')
        const response = await fetch(base_url + "User/updateUsuario/", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        const result = await response.json()
        if (result.success) {
            notifi('Usuario actualizado correctamente', 'success')
            closeEditModal()
            reloadUsuariosTable()
        } else {
            notifi(result.message || 'Error al actualizar usuario', 'error')
        }
    } catch (error) {
        console.error('Error:', error)
        notifi('Error al actualizar usuario', 'error')
    } finally {
        $('#saveEditBtn').prop('disabled', false).html('Guardar Cambios')
    }
}
async function toggleStatus(userId, currentStatus) {
    try {
        const newStatus = currentStatus == 1 ? 0 : 1
        const confirmMessage = newStatus == 1 
            ? '¿Está seguro de activar este usuario?' 
            : '¿Está seguro de desactivar este usuario?'
        
        if (!confirm(confirmMessage)) return
        const response = await fetch(base_url + "User/updateStatus/", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                usuario_id: userId,
                usuario_status: newStatus
            })
        })
        const result = await response.json()
        if (result.success) {
            notifi('Estado actualizado correctamente', 'success')
            reloadUsuariosTable()
        } else {
            notifi(result.message || 'Error al cambiar estado', 'error')
        }
    } catch (error) {
        console.error('Error:', error)
        notifi('Error al cambiar estado', 'error')
    }
}