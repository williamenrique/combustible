function showDatabaseError(message) {
    // Separar mensaje amigable de detalles técnicos (si existen)
    let userMessage = message;
    let technicalDetails = '';
    
    if (message.includes('. Detalles técnicos:')) {
        const parts = message.split('. Detalles técnicos:');
        userMessage = parts[0];
        technicalDetails = parts[1];
    }
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error del Sistema',
            html: `<div style="text-align: left;">
                   <p style="margin-bottom: 15px; font-size: 16px;">${userMessage}</p>
                   ${technicalDetails ? 
                   `<details style="margin-top: 10px;">
                    <summary style="cursor: pointer; color: #666; font-size: 14px;">
                    Detalles técnicos
                    </summary>
                    <code style="background: #f8f9fa; padding: 10px; border-radius: 5px; display: block; margin-top: 5px; font-size: 12px;">
                    ${escapeHtml(technicalDetails)}
                    </code>
                    </details>` : ''}
                   </div>`,
            confirmButtonText: 'Aceptar',
            width: '600px'
        });
    } else {
        alert('Error: ' + userMessage);
    }
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Verificar errores al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Verificar parámetro URL
    const urlParams = new URLSearchParams(window.location.search);
    const urlError = urlParams.get('error');
    
    if (urlError) {
        showDatabaseError(decodeURIComponent(urlError));
        
        // Limpiar parámetro URL sin recargar
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
    
    // Verificar cookie (solo si no hay error en URL)
    if (!urlError) {
        const cookieError = getCookie('db_error');
        if (cookieError) {
            showDatabaseError(cookieError);
            // Eliminar cookie
            document.cookie = 'db_error=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        }
    }
});

// Función para leer cookies
function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for(let i=0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}