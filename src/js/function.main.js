// Validación de solo números
function soloNumeros(e) {
    const key = e.keyCode || e.which
    const tecla = String.fromCharCode(key).toLowerCase()
    const letras = "0123456789"
    const especiales = [8, 37, 39, 46] // Backspace, Left, Right, Delete

    const tecla_especial = especiales.includes(key)

    if (letras.indexOf(tecla) === -1 && !tecla_especial) {
        e.preventDefault()
        return false
    }
    return true
}

// Validación de solo letras y caracteres especiales en español
function soloLetras(e) {
    const key = e.keyCode || e.which
    const tecla = String.fromCharCode(key).toLowerCase()
    const letras = " áéíóúabcdefghijklmnñopqrstuvwxyz.,:¿?¡!"
    const especiales = [8, 9, 13, 32, 37, 39, 46] // Backspace, Tab, Enter, Space, Left, Right, Delete

    const tecla_especial = especiales.includes(key)

    // Permitir teclas de control
    if (key >= 16 && key <= 20) return true // Shift, Ctrl, Alt
    if (key >= 33 && key <= 40) return true // PageUp, PageDown, End, Home, Arrow keys

    if (letras.indexOf(tecla) === -1 && !tecla_especial) {
        e.preventDefault()
        return false
    }
    return true
}

// Validación de email
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return regex.test(email)
}

// Validación de teléfono
function validarTelefono(telefono) {
    const regex = /^[\+]?[0-9\s\-\(\)]{7,15}$/
    return regex.test(telefono)
}

// Notificación Toast mejorada
function notifi(data, icon = 'info', position = 'top-end', timer = 3000) {
    const Toast = Swal.mixin({
        toast: true,
        position: position,
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    
    Toast.fire({
        icon: icon,
        title: data,
        background: getComputedStyle(document.documentElement).getPropertyValue('--light-secondary'),
        color: getComputedStyle(document.documentElement).getPropertyValue('--light-text')
    })
}

// Alert personalizable
function showAlert(title, text, icon = 'info', confirmButtonText = 'OK') {
    return Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: confirmButtonText,
        background: getComputedStyle(document.documentElement).getPropertyValue('--light-secondary'),
        color: getComputedStyle(document.documentElement).getPropertyValue('--light-text'),
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary')
    })
}

// Confirmación con promesa
function showConfirm(title, text, icon = 'question', confirmText = 'Sí', cancelText = 'No') {
    return Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--success'),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--danger'),
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        background: getComputedStyle(document.documentElement).getPropertyValue('--light-secondary'),
        color: getComputedStyle(document.documentElement).getPropertyValue('--light-text')
    })
}

// Toast simplificado (alias de notifi)
function showToast(message, icon = 'info') {
    notifi(message, icon)
}

// Cargar contenido dinámico
async function loadContent(url, containerId, options = {}) {
    try {
        const { method = 'GET', data = null, headers = {} } = options
        const container = document.getElementById(containerId)
        
        if (!container) {
            console.error('Contenedor no encontrado:', containerId)
            return
        }

        // Mostrar loader
        container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>'

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...headers
            },
            body: data ? JSON.stringify(data) : null
        })

        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`)
        }

        const content = await response.text()
        container.innerHTML = content

        // Ejecutar scripts dentro del contenido cargado
        container.querySelectorAll('script').forEach(script => {
            const newScript = document.createElement('script')
            newScript.text = script.text
            document.head.appendChild(newScript).remove()
        })

    } catch (error) {
        console.error('Error loading content:', error)
        notifi('Error al cargar el contenido', 'error')
    }
}

// Formatear número con separadores
function formatNumber(number, decimals = 0) {
    return new Intl.NumberFormat('es-ES', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number)
}

// Formatear fecha
function formatDate(date, format = 'long') {
    const options = {
        short: { day: '2-digit', month: '2-digit', year: 'numeric' },
        medium: { day: '2-digit', month: 'short', year: 'numeric' },
        long: { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }
    }
    
    return new Date(date).toLocaleDateString('es-ES', options[format] || options.long)
}

// Toggle sidebar mejorado
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar')
    const mainContent = document.getElementById('mainContent')
    const toggleBtn = document.getElementById('toggleSidebar')
    
    if (!sidebar || !mainContent || !toggleBtn) return
    
    sidebar.classList.toggle('collapsed')
    mainContent.classList.toggle('expanded')
    
    // Cambiar icono
    const icon = toggleBtn.querySelector('i')
    if (icon) {
        if (sidebar.classList.contains('collapsed')) {
            icon.classList.replace('fa-chevron-left', 'fa-chevron-right')
        } else {
            icon.classList.replace('fa-chevron-right', 'fa-chevron-left')
        }
    }
    
    // Guardar preferencia
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'))
}

// Toggle theme mejorado
function toggleTheme() {
    const body = document.body
    const themeToggle = document.getElementById('themeToggle')
    
    body.classList.toggle('dark-mode')
    
    if (themeToggle) {
        themeToggle.classList.toggle('light')
        themeToggle.classList.toggle('dark')
    }
    
    // Guardar preferencia
    localStorage.setItem('darkMode', body.classList.contains('dark-mode'))
}

// Cerrar menús dropdown al hacer clic fuera
function setupDropdowns() {
    document.addEventListener('click', (e) => {
        document.querySelectorAll('.dropdown.show').forEach(dropdown => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('show')
            }
        })
    })
}

// Inicialización de la aplicación
function initApp() {
    // Restaurar preferencias
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        toggleSidebar()
    }
    
    if (localStorage.getItem('darkMode') === 'true') {
        toggleTheme()
    }
    
    // Configurar eventos
    setupEventListeners()
    setupDropdowns()
}

// Configurar event listeners
function setupEventListeners() {
    // Toggle sidebar
    const toggleSidebarBtn = document.getElementById('toggleSidebar')
    if (toggleSidebarBtn) {
        toggleSidebarBtn.addEventListener('click', toggleSidebar)
    }
    
    // Toggle mobile sidebar
    const mobileToggleBtn = document.getElementById('mobileToggle')
    if (mobileToggleBtn) {
        mobileToggleBtn.addEventListener('click', () => {
            document.getElementById('sidebar')?.classList.toggle('open')
        })
    }
    
    // Toggle theme
    const themeToggle = document.getElementById('themeToggle')
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme)
    }
    
    // User menu dropdown
    const userMenuBtn = document.getElementById('userMenuBtn')
    const userMenuDropdown = document.getElementById('userMenuDropdown')
    
    if (userMenuBtn && userMenuDropdown) {
        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation()
            userMenuDropdown.classList.toggle('show')
        })
    }
    
    // Submenús
    document.querySelectorAll('.has-submenu').forEach(item => {
        const menuLink = item.querySelector('.menu-link')
        if (menuLink) {
            menuLink.addEventListener('click', (e) => {
                e.preventDefault()
                item.classList.toggle('open')
            })
        }
    })
    
    // Actualizar fecha actual
    updateCurrentDate()
}

// Actualizar fecha actual
function updateCurrentDate() {
    const currentDateElement = document.getElementById('current-date')
    if (currentDateElement) {
        const today = new Date()
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        }
        currentDateElement.textContent = today.toLocaleDateString('es-ES', options)
    }
}

// Debounce para optimizar eventos
function debounce(func, wait) {
    let timeout
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout)
            func(...args)
        }
        clearTimeout(timeout)
        timeout = setTimeout(later, wait)
    }
}

// Throttle para eventos frecuentes
function throttle(func, limit) {
    let inThrottle
    return function() {
        const args = arguments
        const context = this
        if (!inThrottle) {
            func.apply(context, args)
            inThrottle = true
            setTimeout(() => inThrottle = false, limit)
        }
    }
}

// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar la aplicación
    initApp()
    
    // Configurar event listeners para elementos dinámicos
    document.addEventListener('click', function(e) {
        // Cerrar dropdowns al hacer clic fuera
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show')
            })
        }
        
        // Cerrar sidebar en móviles al hacer clic en un enlace
        if (window.innerWidth <= 992) {
            if (e.target.closest('.menu-link') || e.target.closest('.submenu-link')) {
                document.getElementById('sidebar')?.classList.remove('open')
            }
        }
    })
    
    // Manejar resize con debounce
    window.addEventListener('resize', debounce(function() {
        if (window.innerWidth > 992) {
            document.getElementById('sidebar')?.classList.remove('open')
        }
    }, 250))
})

// Exportar funciones para uso global (si es necesario)
window.App = {
    soloNumeros,
    soloLetras,
    validarEmail,
    validarTelefono,
    notifi,
    showAlert,
    showConfirm,
    showToast,
    loadContent,
    formatNumber,
    formatDate,
    toggleSidebar,
    toggleTheme,
    initApp
}

/** adaptacion del tema oscuro a todos los slect */
// Función para actualizar todos los selects al cambiar tema
function updateSelectThemes() {
    document.querySelectorAll('.theme-select').forEach(select => {
        // Forzar repintado para aplicar estilos CSS
        select.style.display = 'none'
        select.offsetHeight // Trigger reflow
        select.style.display = ''
    })
}

// Escuchar cambios de tema
document.addEventListener('DOMContentLoaded', function() {
    // Observar cambios en la clase dark-mode del body
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                setTimeout(updateSelectThemes, 50)
            }
        })
    })
    
    observer.observe(document.body, { 
        attributes: true,
        attributeFilter: ['class']
    })
    
    // También actualizar cuando se cambie el tema manualmente
    const themeToggle = document.getElementById('themeToggle')
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            setTimeout(updateSelectThemes, 100)
        })
    }
    
    // Aplicar la clase theme-select a todos los selects al cargar
    document.querySelectorAll('select').forEach(select => {
        if (!select.classList.contains('theme-select')) {
            select.classList.add('theme-select')
        }
    })
})