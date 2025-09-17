<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?= $data['page_tag'] ?> </title>
    <link rel="shortcut icon" href="<?= IMG ?>gas.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= PLUGINS ?>css/sweetalert2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #4f46e5;
            --success: #10b981;
            --info: #0ea5e9;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark-bg: #111827;
            --dark-secondary: #1a202c;
            --dark-tertiary: #2d3748;
            --dark-text: #f3f4f6;
            --light-bg: #f9fafb;
            --light-secondary: #ffffff;
            --light-text: #111827;
            --light-border: #e5e7eb;
            --transition-speed: 0.3s;
            --neon-green: #39ff14;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: background-color var(--transition-speed) ease, color var(--transition-speed) ease, border-color var(--transition-speed) ease;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--light-text);
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        body.dark-mode {
            background-color: var(--dark-bg);
            color: var(--dark-text);
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
            background-color: var(--light-secondary);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .dark-mode .login-container {
            background-color: var(--dark-secondary);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        
        .login-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .login-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--light-text);
        }
        
        .dark-mode .form-label {
            color: var(--dark-text);
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid var(--light-border);
            border-radius: 8px;
            background-color: var(--light-secondary);
            color: var(--light-text);
            font-size: 15px;
        }
        
        .dark-mode .form-input {
            border: 1px solid var(--dark-border);
            background-color: var(--dark-secondary);
            color: var(--dark-text);
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        
        .dark-mode .form-input:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.4);
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        .dark-mode .forgot-link {
            color: var(--info);
        }
        
        .btn {
            width: 100%;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 16px;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--light-border);
            font-size: 14px;
        }
        
        .dark-mode .login-footer {
            border-top: 1px solid var(--dark-border);
        }
        
        .recovery-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 10px;
        }
        
        .recovery-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .dark-mode .recovery-link {
            color: var(--info);
        }
        
        .recovery-link:hover {
            text-decoration: underline;
        }
        
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 24px;
            background-color: #ccc;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0 3px;
            z-index: 100;
        }
        
        .theme-toggle.dark {
            background-color: var(--primary);
            justify-content: flex-end;
        }
        
        .theme-toggle.light {
            background-color: #ccc;
            justify-content: flex-start;
        }
        
        .theme-toggle-handle {
            width: 20px;
            height: 20px;
            background-color: white;
            border-radius: 50%;
            display: inline-block;
        }
        
        .theme-toggle i {
            font-size: 12px;
            color: #fff;
            position: absolute;
        }
        
        .theme-toggle .fa-sun {
            left: 6px;
        }
        
        .theme-toggle .fa-moon {
            right: 6px;
        }
        
        /* Modals */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .modal-content {
            background-color: var(--light-secondary);
            border-radius: 12px;
            padding: 30px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        
        .dark-mode .modal-content {
            background-color: var(--dark-secondary);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }
        
        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #6b7280;
        }
        
        .modal-header {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .modal-header h2 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .dark-mode .modal-header h2 {
            color: var(--info);
        }
        
        .modal-body {
            margin-bottom: 25px;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .btn-secondary {
            background-color: var(--light-border);
            color: var(--light-text);
        }
        
        .dark-mode .btn-secondary {
            background-color: var(--dark-border);
            color: var(--dark-text);
        }
        
        .btn-secondary:hover {
            background-color: #d1d5db;
        }
        
        .dark-mode .btn-secondary:hover {
            background-color: var(--dark-tertiary);
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                max-width: 100%;
            }
            
            .login-body {
                padding: 20px;
            }
            
            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .recovery-links {
                flex-direction: column;
                gap: 10px;
            }
        }

        .btn-loading {
            position: relative;
            color: transparent !important;
            pointer-events: none;
        }
        .btn-loading:after {
            content: 'Procesando...';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Login Container -->
    <div class="login-container">
        <div class="login-header">
            <h1>Bienvenido de nuevo</h1>
            <p>Ingresa a tu cuenta para acceder al sistema</p>
        </div>
        <div class="login-body">
            <form id="formLogin" name="formLogin">
                <div class="form-group">
                    <label class="form-label">Usuario</label>
                    <div class="input-with-icon">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-input" id="txtUser" name="txtUser"  placeholder="Ingresa tu usuario" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="input-with-icon">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-input" id="txtPass" name="txtPass" placeholder="Ingresa tu contraseña" required>
                    </div>
                </div>
                
                <div class="remember-forgot">
                    <label class="remember">
                        <input type="checkbox">
                        <span>Recordarme</span>
                    </label>
                    <a href="#" class="forgot-link" id="forgotPassword">¿Olvidaste tu contraseña?</a>
                </div>
                
                <!-- <button type="submit" class="btn btn-primary" id="btnActionForm">Iniciar Sesión</button> -->
                <button type="submit" class="btn btn-primary" id="btnActionForm">
                    <span class="btn-text">Iniciar Sesión</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </form>
            
            <div class="login-footer">
                <p>¿Necesitas ayuda con tu cuenta?</p>
                <div class="recovery-links">
                    <a href="#" class="recovery-link" id="recoverUsername">
                        <i class="fas fa-user-circle"></i> Recuperar usuario
                    </a>
                    <a href="#" class="recovery-link" id="recoverPassword">
                        <i class="fas fa-key"></i> Recuperar contraseña
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toggle Theme -->
    <div class="theme-toggle light" id="themeToggle">
        <i class="fas fa-sun"></i>
        <span class="theme-toggle-handle"></span>
        <i class="fas fa-moon"></i>
    </div>
    
    <!-- Modal Recuperar Usuario -->
    <div class="modal" id="usernameModal">
        <div class="modal-content">
            <button class="modal-close" data-dismiss="modal">&times;</button>
            <div class="modal-header">
                <h2>Recuperar Usuario</h2>
                <p>Ingresa tu correo electrónico para recuperar tu usuario</p>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-with-icon">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-input" placeholder="correo@ejemplo.com" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Enviar</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Recuperar Contraseña -->
    <div class="modal" id="passwordModal">
        <div class="modal-content">
            <button class="modal-close" data-dismiss="modal">&times;</button>
            <div class="modal-header">
                <h2>Recuperar Contraseña</h2>
                <p>Ingresa tu usuario o correo para recuperar tu contraseña</p>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Usuario o Correo Electrónico</label>
                    <div class="input-with-icon">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-input" placeholder="Usuario o correo@ejemplo.com" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Enviar</button>
            </div>
        </div>
    </div>


    <script src="<?= PLUGINS ?>js/sweetalert2@10.js"></script>
    <script src="<?= JS.$data['page_functions'] ?>"></script>
    <script>

        const base_url = "<?= base_url()?>";
        document.addEventListener('DOMContentLoaded', function() {
            // Obtenemos el mensaje de error de PHP
            const dbError = "<?= $data['db_error'] ?>";
            // Si existe un error, mostramos la alerta de SweetAlert
            if (dbError) {
                Swal.fire({
                    title: 'Error de Conexión',
                    text: dbError,
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
            }
            // El resto de tu lógica de JavaScript para el login
            initLoginSystem();
            // Toggle theme
            const themeToggle = document.getElementById('themeToggle');
            themeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                themeToggle.classList.toggle('light');
                themeToggle.classList.toggle('dark');
            });
            
            // Form submission
            const formLogin = document.getElementById('formLogin');

            
            // Modal functionality
            const recoverUsernameBtn = document.getElementById('recoverUsername');
            const recoverPasswordBtn = document.getElementById('recoverPassword');
            const forgotPasswordLink = document.getElementById('forgotPassword');
            const usernameModal = document.getElementById('usernameModal');
            const passwordModal = document.getElementById('passwordModal');
            const closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
            
            // Abrir modales
            recoverUsernameBtn.addEventListener('click', function() {
                usernameModal.style.display = 'flex';
            });
            
            recoverPasswordBtn.addEventListener('click', function() {
                passwordModal.style.display = 'flex';
            });
            
            forgotPasswordLink.addEventListener('click', function(e) {
                e.preventDefault();
                passwordModal.style.display = 'flex';
            });
            
            // Cerrar modales
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    usernameModal.style.display = 'none';
                    passwordModal.style.display = 'none';
                });
            });
            
            // Cerrar modal al hacer clic fuera del contenido
            window.addEventListener('click', function(e) {
                if (e.target === usernameModal) {
                    usernameModal.style.display = 'none';
                }
                if (e.target === passwordModal) {
                    passwordModal.style.display = 'none';
                }
            });
        });
    </script>
    <script src="<?= JS ?>notifications.js"></script>
</body>
</html>