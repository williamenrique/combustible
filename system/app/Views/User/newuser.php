<?= head($data)?>
<style>
    /* Estilos para DataTable compacta y elegante */
    :root {
        --table-header-bg: #2c3e50;
        --table-header-color: #ffffff;
        --table-row-even: #f8f9fa;
        --table-row-hover: #e9ecef;
        --table-border: #dee2e6;
        --text-color: #212529;
        --bg-color: #ffffff;
    }

    [data-theme="dark"] {
        --table-header-bg: #343a40;
        --table-header-color: #f8f9fa;
        --table-row-even: #2d3035;
        --table-row-hover: #3a3f45;
        --table-border: #495057;
        --text-color: #f8f9fa;
        --bg-color: #212529;
    }

    #usuariosTable {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        margin: 0;
        color: var(--text-color);
        background-color: var(--bg-color);
    }

    #usuariosTable th {
        background-color: var(--table-header-bg);
        color: var(--table-header-color);
        padding: 10px 15px;
        text-align: left;
        font-weight: 600;
        border: none;
    }

    #usuariosTable td {
        padding: 8px 15px;
        border-bottom: 1px solid var(--table-border);
        vertical-align: middle;
    }

    #usuariosTable tr:nth-child(even) {
        background-color: var(--table-row-even);
    }

    #usuariosTable tr:hover {
        background-color: var(--table-row-hover);
    }

    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-success {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    /* Modal styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-container {
        background-color: var(--bg-color);
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        transform: translateY(-20px);
        transition: transform 0.3s ease;
    }

    .modal-overlay.active .modal-container {
        transform: translateY(0);
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--table-border);
        display: flex;
        justify-content: between;
        align-items: center;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-color);
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-color);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--table-border);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        #usuariosTable {
            font-size: 14px;
        }
        
        #usuariosTable th,
        #usuariosTable td {
            padding: 6px 10px;
        }
        
        .modal-container {
            width: 95%;
            margin: 1rem;
        }
    }
    
</style>
<div class="content-area">
    
    <!-- <div class="profile-card">
        <form id="userCreateForm">
            <input type="hidden" id="userCreateId" name="userCreateId" value="0">
            
            <div class="profile-form">
                <div class="form-group">
                    <label for="txtIdPersonal" class="form-label">Identificación *</label>
                    <input type="number" id="txtIdPersonal" name="txtIdPersonal" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="txtNombre" class="form-label">Nombres *</label>
                    <input type="text" id="txtNombre" name="txtNombre" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="txtApellido" class="form-label">Apellidos *</label>
                    <input type="text" id="txtApellido" name="txtApellido" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="txtTelefono" class="form-label">Teléfono</label>
                    <input type="tel" id="txtTelefono" name="txtTelefono" class="form-input">
                </div>
                <div class="form-group">
                    <label for="txtDireccion" class="form-label">Direccion</label>
                    <input type="tel" id="txtDireccion" name="txtDireccion" class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="txtEmail" class="form-label">Email *</label>
                    <input type="email" id="txtEmail" name="txtEmail" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="listRolId" class="form-label">Rol *</label>
                    <select id="listRolId" name="listRolId" class="form-input" required>
                        <option value="">Seleccionar rol</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="listDep" class="form-label">Departamento</label>
                    <select id="listDep" name="listDep" class="form-input">
                        <option value="">Seleccionar departamento</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" id="cancelCreateBtn" class="btn btn-secondary">Cancelar</button>
                <button type="submit" id="saveCreateBtn" class="btn btn-primary">Crear Usuario</button>
            </div>
        </form>
    </div> -->

    <div class="w-full max-w-3xl bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Formulario -->
        <form id="userCreateForm" class="p-5">
            <input type="hidden" id="userCreateId" name="userCreateId" value="0">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="txtIdPersonal" class="block text-sm font-medium text-gray-700 mb-1">Identificación *</label>
                    <div class="relative">
                        <input type="number" id="txtIdPersonal" name="txtIdPersonal" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required onkeypress="return soloNumeros(event);">
                        <span class="absolute right-3 top-2 text-gray-400">
                            <i class="fas fa-id-card"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="txtTelefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <div class="relative">
                        <input type="tel" id="txtTelefono" name="txtTelefono" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" onkeypress="return soloNumeros(event);">
                        <span class="absolute right-3 top-2 text-gray-400">
                            <i class="fas fa-phone"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="txtNombre" class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                    <div class="relative">
                        <input type="text" id="txtNombre" name="txtNombre" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                        <span class="absolute right-3 top-2 text-gray-400" onkeypress="return soloLetras(event);">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                </div>

                 <div class="form-group">
                    <label for="txtApellido" class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                    <div class="relative">
                        <input type="text" id="txtApellido" name="txtApellido" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                        <span class="absolute right-3 top-2 text-gray-400" onkeypress="return soloLetras(event);">
                            <i class="fas fa-users"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="txtDireccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <div class="relative">
                        <input type="text" id="txtDireccion" name="txtDireccion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <span class="absolute right-3 top-2 text-gray-400">
                            <i class="fas fa-home"></i>
                        </span>
                    </div>
                </div>
                
                
                <div class="form-group">
                    <label for="txtEmail" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <div class="relative">
                        <input type="email" id="txtEmail" name="txtEmail" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                        <span class="absolute right-3 top-2 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="listRolId" class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
                    <div class="relative">
                        <select id="listRolId" name="listRolId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition appearance-none" required>
                            <option value="">Seleccionar rol</option>
                            <option value="1">Administrador</option>
                            <option value="2">Usuario</option>
                            <option value="3">Invitado</option>
                        </select>
                        <span class="absolute right-3 top-2 text-gray-400 pointer-events-none">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="listDep" class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                    <div class="relative">
                        <select id="listDep" name="listDep" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition appearance-none">
                            <option value="">Seleccionar departamento</option>
                            <option value="1">Ventas</option>
                            <option value="2">TI</option>
                            <option value="3">Recursos Humanos</option>
                        </select>
                        <span class="absolute right-3 top-2 text-gray-400 pointer-events-none">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" id="cancelCreateBtn" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Cancelar
                </button>
                <button type="submit" id="saveCreateBtn" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center">
                    <i class="fas fa-save mr-2"></i> Crear Usuario
                </button>
            </div>
        </form>
    </div>
 
    <!-- Sección de tabla de usuarios -->
    <div class="mt-8 bg-white rounded-xl shadow-lg overflow-hidden">
        
        <div class="p-5">
            <table id="usuariosTable" class="display compact" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Identificación</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Departamento</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

</div>
<?= footer($data)?>