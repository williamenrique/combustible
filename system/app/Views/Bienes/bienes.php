<?= head($data) ?>
<div class="content-area">
    <!-- Page content -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Gestión de Inventario de Bienes</h3>
            <div class="flex gap-2">
                <button type="button" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md transition-colors flex items-center" onclick="openModalQR();">
                    <i class="fas fa-qrcode mr-2"></i> Generar QR
                </button>
                <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition-colors flex items-center" onclick="openModal();">
                    <i class="fas fa-plus-circle mr-2"></i> Nuevo Bien
                </button>
            </div>
        </div>

        <table id="tableBienes" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Descripción</th>
                    <th scope="col" class="px-6 py-3">Departamento</th>
                    <th scope="col" class="px-6 py-3">Grupo</th>
                    <th scope="col" class="px-6 py-3">Subgrupo</th>
                    <th scope="col" class="px-6 py-3">Sección</th>
                    <th scope="col" class="px-6 py-3">Estado del Bien</th>
                    <th scope="col" class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal para agregar/editar bien -->
<div id="modalFormBien" class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 flex items-center justify-center hidden z-50" onclick="if(event.target === this) closeModal();">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-3xl mx-4">
        <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white" id="titleModal">Nuevo Bien</h4>
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-white" onclick="closeModal()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
        <form id="formBien" name="formBien" class="p-6">
            <input type="hidden" id="id_bien" name="id_bien" value="">
            
            <div class="mb-4">
                <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="2" class="form-input"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="departamento" class="form-label">Departamento <span class="text-red-500">*</span></label>
                    <select id="departamento" name="departamento" class="form-select" required></select>
                </div>
                <div>
                    <label for="grupo" class="form-label">Grupo <span class="text-red-500">*</span></label>
                    <select id="grupo" name="grupo" class="form-select" required></select>
                </div>
                <div>
                    <label for="subgrupo" class="form-label">Subgrupo <span class="text-red-500">*</span></label>
                    <select id="subgrupo" name="subgrupo" class="form-select" required></select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="seccion" class="form-label">Sección <span class="text-red-500">*</span></label>
                    <select id="seccion" name="seccion" class="form-select" required></select>
                </div>
                <div>
                    <label for="status_bien" class="form-label">Estado del Bien <span class="text-red-500">*</span></label>
                    <select id="status_bien" name="status_bien" class="form-select" required>
                        <option value="">Seleccionar Estado</option>
                        <option value="EN USO">EN USO</option>
                        <option value="EXTRAVIADO">EXTRAVIADO</option>
                        <option value="EN REPARACION">EN REPARACION</option>
                        <option value="DAÑADO">DAÑADO</option>
                    </select>
                </div>
                <div>
                    <label for="fecha_adquisicion" class="form-label">Fecha de Adquisición</label>
                    <input type="date" id="fecha_adquisicion" name="fecha_adquisicion" class="form-input">
                </div>
            </div>

            <div class="pt-4 border-t dark:border-gray-700 flex justify-end gap-2">
                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-md transition-colors" onclick="closeModal()">Cerrar</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i><span id="btnActionText">Guardar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para generar QR -->
<div id="modalQR" class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 flex items-center justify-center hidden z-50" onclick="if(event.target === this) closeModalQR();">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Generar Código QR por Departamento</h4>
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-white" onclick="closeModalQR()">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <label for="listDeptoQR" class="form-label">Seleccionar Departamento</label>
                <select id="listDeptoQR" class="form-select">
                    <option value="">Seleccionar Departamento</option>
                    <?php if(isset($data['departamentos']) && is_array($data['departamentos'])) {
                        foreach($data['departamentos'] as $departamento) { ?>
                            <option value="<?= $departamento['depatamento_bien_id'] ?>"><?= $departamento['departamento_bien'] ?></option>
                    <?php } } ?>
                </select>
            </div>
            <div id="qrResult" class="text-center mt-4 hidden">
                <div id="qrcode" class="inline-block p-2 bg-white rounded-lg"></div>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Escanea este código para ver los bienes del departamento.</p>
            </div>
        </div>
        <div class="p-4 border-t dark:border-gray-700 flex justify-end gap-2">
            <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-md transition-colors" onclick="closeModalQR()">Cerrar</button>
            <button type="button" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md transition-colors" onclick="generarQR()">
                <i class="fas fa-qrcode mr-2"></i>Generar QR
            </button>
        </div>
    </div>
</div>

<?= footer($data)?>