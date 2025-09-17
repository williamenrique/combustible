<?= head($data)?>
    <!-- Content Area -->
    <div class="content-area">
        <!-- Profile Page -->
        <div id="profilePage" class="page-content">
            <div class="profile-container">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-image-container">
                            <input type="file" id="profileImageInput" accept="image/*" style="display: none;">
                            <input type="hidden" name="user" id="user" value="<?= $_SESSION['userData']['usuario_id']?>">
                            <img src="<?= BASE_URL().'/'.$_SESSION['userData']['usuario_imagen']?>" alt="Usuario" class="profile-image-large" id="profileImageLarge">
                            <div class="profile-image-change" id="changeImageBtn">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                        <div class="profile-info">
                            <h2><?= $_SESSION['userData']['usuario_nombres'].' '.$_SESSION['userData']['usuario_apellidos']?></h2>
                            <p class="profile-status">En línea</p>
                            <div class="image-action-buttons" style="margin-top: 10px; display: none;" id="imageActions">
                                <button type="button" class="btn btn-sm btn-danger" id="removeImageBtn">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" id="saveImageBtn">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" id="cancelImageBtn">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>

                    <form class="profile-form compact-form" id="userDataForm" data-user-id="<?= $_SESSION['userData']['usuario_id'] ?? '' ?>">
                        <div class="form-row">
                            <div class="form-group form-group-sm">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-input input-sm" id="usuario_nombres" name="usuario_nombres" value="<?= $_SESSION['userData']['usuario_nombres']?>">
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="form-label">Apellido</label>
                                <input type="text" class="form-input input-sm" id="usuario_apellidos" name="usuario_apellidos" value="<?= $_SESSION['userData']['usuario_apellidos'] ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group form-group-sm">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-input input-sm" id="usuario_email" name="usuario_email" value="<?= $_SESSION['userData']['usuario_email'] ?>">
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-input input-sm" id="usuario_telefono" name="usuario_telefono" value="<?= $_SESSION['userData']['usuario_telefono'] ?>">
                            </div>
                        </div>

                        <div class="form-group form-group-sm full-width">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-input input-sm" id="usuario_direccion" name="usuario_direccion" value="<?= $_SESSION['userData']['usuario_direccion'] ?? '' ?>">
                        </div>

                        <div class="form-section">
                            <h4 class="form-section-title">Cambiar Contraseña</h4>
                            <div class="form-row">
                                <div class="form-group form-group-sm">
                                    <label class="form-label">Contraseña Actual</label>
                                    <input type="password" class="form-input input-sm" id="currentPassword" placeholder="Contraseña actual">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group form-group-sm">
                                    <label class="form-label">Nueva Contraseña</label>
                                    <input type="password" class="form-input input-sm" id="newPassword" placeholder="Nueva contraseña">
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="form-label">Confirmar Contraseña</label>
                                    <input type="password" class="form-input input-sm" id="confirmPassword" placeholder="Confirmar contraseña">
                                </div>
                            </div>
                            <div class="form-actions-sm">
                                <button type="button" class="btn btn-sm btn-secondary" id="cancelPasswordBtn">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" id="savePasswordBtn">
                                    <i class="fas fa-key"></i> Cambiar Password
                                </button>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-sm btn-secondary" id="cancelBtn">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-sm btn-primary" id="saveBtn">
                                <i class="fas fa-save"></i> Guardar Datos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
    </div>

<style>
    /* Estilos para la interfaz compacta */
    .compact-form .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 15px;
    }

    .compact-form .form-group-sm {
        margin-bottom: 15px;
    }

    .compact-form .form-group-sm .form-label {
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 5px;
        color: var(--light-text);
    }

    .dark-mode .compact-form .form-group-sm .form-label {
        color: var(--dark-text);
    }

    .compact-form .input-sm {
        padding: 8px 12px;
        font-size: 13px;
        height: 36px;
        border-radius: 6px;
    }

    .compact-form .form-section {
        margin: 20px 0;
        padding: 15px;
        background-color: rgba(0, 0, 0, 0.02);
        border-radius: 8px;
        border-left: 3px solid var(--primary);
    }

    .dark-mode .compact-form .form-section {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .compact-form .form-section-title {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--primary);
    }

    .dark-mode .compact-form .form-section-title {
        color: var(--info);
    }

    /* Botones pequeños */
    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 5px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-sm i {
        font-size: 11px;
    }

    .form-actions-sm {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 15px;
    }

    .image-action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* Profile image adjustments */
    .profile-image-container {
        position: relative;
        margin-right: 20px;
    }

    .profile-image-large {
        width: 100px;
        height: 100px;
        border: 3px solid var(--primary);
    }

    .profile-image-change {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }

    .profile-info h2 {
        font-size: 18px;
        margin-bottom: 5px;
    }

    .profile-status {
        font-size: 12px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .compact-form .form-row {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        
        .profile-header {
            flex-direction: column;
            text-align: center;
        }
        
        .profile-image-container {
            margin-right: 0;
            margin-bottom: 15px;
        }
        
        .image-action-buttons {
            justify-content: center;
        }
        
        .form-actions,
        .form-actions-sm {
            flex-direction: column;
        }
        
        .btn-sm {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .compact-form .input-sm {
            font-size: 12px;
            height: 32px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 11px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar/ocultar botones de imagen
        const changeImageBtn = document.getElementById('changeImageBtn');
        const imageActions = document.getElementById('imageActions');
        const cancelImageBtn = document.getElementById('cancelImageBtn');
        
        if (changeImageBtn && imageActions) {
            changeImageBtn.addEventListener('click', function() {
                imageActions.style.display = 'flex';
            });
        }
        
        if (cancelImageBtn && imageActions) {
            cancelImageBtn.addEventListener('click', function() {
                imageActions.style.display = 'none';
            });
        }
        
        // Manejo del formulario de contraseña
        const cancelPasswordBtn = document.getElementById('cancelPasswordBtn');
        const passwordInputs = document.querySelectorAll('#currentPassword, #newPassword, #confirmPassword');
        
        if (cancelPasswordBtn) {
            cancelPasswordBtn.addEventListener('click', function() {
                passwordInputs.forEach(input => input.value = '');
            });
        }
    });
</script>
<?= footer($data)?>