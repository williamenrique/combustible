<?= head($data) ?>
<div class="content-area">
    <!-- Contenedor Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Columna Departamentos -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Gestión de Departamentos</h3>
                <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition-colors flex items-center" onclick="openModal();">
                    <i class="fas fa-plus-circle mr-2"></i> Nuevo
                </button>
            </div>
            <table id="tableDepartamentos" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Nombre</th>
                        <th scope="col" class="px-6 py-3">Estado</th>
                        <th scope="col" class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Columna Roles -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Gestión de Roles</h3>
                <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition-colors flex items-center" onclick="openRolModal();">
                    <i class="fas fa-plus-circle mr-2"></i> Nuevo
                </button>
            </div>
            <table id="tableRoles" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Nombre</th>
                        <th scope="col" class="px-6 py-3">Estado</th>
                        <th scope="col" class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Departamentos -->
<div id="modalFormDepto" class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 flex items-center justify-center hidden z-50" onclick="if(event.target === this) closeDeptoModal();">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4">
        <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white" id="titleModal">Nuevo Departamento</h4>
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-white" onclick="closeDeptoModal()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
        <form id="formDepto" name="formDepto" class="p-6">
            <input type="hidden" id="idDepartamento" name="idDepartamento" value="">
            <div class="mb-4">
                <label for="txtNombre" class="form-label">Nombre <span class="text-red-500">*</span></label>
                <input type="text" id="txtNombreDepto" name="txtNombre" class="form-input" required>
            </div>
            <div class="mb-4">
                <label for="txtDescripcion" class="form-label">Descripción</label>
                <textarea id="txtDescripcionDepto" name="txtDescripcion" rows="3" class="form-input"></textarea>
            </div>
            <div class="mb-4">
                <label for="listStatus" class="form-label">Estado <span class="text-red-500">*</span></label>
                <select id="listStatusDepto" name="listStatus" class="form-select" required>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
            <div class="pt-4 border-t dark:border-gray-700 flex justify-end gap-2">
                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-md transition-colors" onclick="closeDeptoModal()">Cerrar</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i><span id="btnActionTextDepto">Guardar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Roles -->
<div id="modalFormRol" class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 flex items-center justify-center hidden z-50" onclick="if(event.target === this) closeRolModal();">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4">
        <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white" id="titleModalRol">Nuevo Rol</h4>
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-white" onclick="closeRolModal()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
        <form id="formRol" name="formRol" class="p-6">
            <input type="hidden" id="idRol" name="idRol" value="">
            <div class="mb-4">
                <label for="txtNombreRol" class="form-label">Nombre <span class="text-red-500">*</span></label>
                <input type="text" id="txtNombreRol" name="txtNombre" class="form-input" required>
            </div>
            <div class="mb-4">
                <label for="txtDescripcionRol" class="form-label">Descripción</label>
                <textarea id="txtDescripcionRol" name="txtDescripcion" rows="3" class="form-input"></textarea>
            </div>
            <div class="mb-4">
                <label for="listStatusRol" class="form-label">Estado <span class="text-red-500">*</span></label>
                <select id="listStatusRol" name="listStatus" class="form-select" required>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
            <div class="pt-4 border-t dark:border-gray-700 flex justify-end gap-2">
                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-md transition-colors" onclick="closeRolModal()">Cerrar</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i><span id="btnActionTextRol">Guardar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= footer($data)?>