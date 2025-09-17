document.addEventListener('DOMContentLoaded', function () {
    initLoginSystem()
})
function initLoginSystem() {
    setupLoginForm()
}
function setupLoginForm() {
    var formLogin = document.querySelector('#formLogin')
    if (!formLogin) return
    formLogin.onsubmit = function (e) {
        e.preventDefault()
        handleLogin(formLogin)
    }
}
function handleLogin1(form) {
    var strTxtUser = document.querySelector('#txtUser').value.trim()
    var strTxtPass = document.querySelector('#txtPass').value.trim()
    const btn = formLogin.querySelector('#btnActionForm')
    if (!strTxtUser || !strTxtPass) {
        showAlert('Por favor', 'Escriba su usuario y password', 'error')
        return false
    }
    var ajaxUrl = base_url + 'Login/loginUser'
    var formData = new FormData(form)
    ajaxRequest('POST', ajaxUrl, formData, function(response) {
        if (response.status) {
            window.location = base_url + 'home'
        } else if (response.code === 'session_exists') {
            Swal.fire({
                title: 'Sesión activa',
                text: response.msg + '. ¿Deseas cerrar la sesión anterior y continuar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar y continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Llama a un endpoint para forzar el cierre de la sesión anterior y reintenta login
                    var forceUrl = base_url + 'Login/forceLogout'
                    var data = new FormData()
                    data.append('userId', strTxtUser)
                    ajaxRequest('POST', forceUrl, data, function(forceResp) {
                        if (forceResp.status) {
                            // Reintenta login automáticamente
                            handleLogin(form)
                        } else {
                            showAlert('Error', forceResp.msg, 'error')
                        }
                    }, function() {
                        showAlert('Error', 'No se pudo cerrar la sesión anterior', 'error')
                    })
                }
            })
        } else {
            showAlert('Atencion', response.msg, 'error')
            document.querySelector('#txtPass').value = ""
        }
    }, function(error) {
        showAlert('Atencion', 'Error en el proceso', 'error')
    })
}

function handleLogin(form) {
    var strTxtUser = document.querySelector('#txtUser').value.trim()
    var strTxtPass = document.querySelector('#txtPass').value.trim()
    const btn = document.querySelector('#btnActionForm')
    const btnText = btn.querySelector('.btn-text')
    const spinner = btn.querySelector('.spinner-border')
    
    if (!strTxtUser || !strTxtPass) {
        showAlert('Por favor', 'Escriba su usuario y password', 'error')
        return false
    }
    
    // Deshabilitar botón y mostrar estado de carga
    btn.disabled = true
    btnText.classList.add('d-none')
    spinner.classList.remove('d-none')
    btn.classList.add('btn-loading')
    
    var ajaxUrl = base_url + 'Login/loginUser'
    var formData = new FormData(form)
    
    ajaxRequest('POST', ajaxUrl, formData, function(response) {
        if (response.status) {
            window.location = base_url + 'home'
        } else if (response.code === 'session_exists') {
            Swal.fire({
                title: 'Sesión activa',
                text: response.msg + '. ¿Deseas cerrar la sesión anterior y continuar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar y continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Llama a un endpoint para forzar el cierre de la sesión anterior y reintenta login
                    var forceUrl = base_url + 'Login/forceLogout'
                    var data = new FormData()
                    data.append('userId', strTxtUser)
                    ajaxRequest('POST', forceUrl, data, function(forceResp) {
                        if (forceResp.status) {
                            // Reintenta login automáticamente
                            handleLogin(form)
                        } else {
                            showAlert('Error', forceResp.msg, 'error')
                            // Rehabilitar botón
                            btn.disabled = false
                            btnText.classList.remove('d-none')
                            spinner.classList.add('d-none')
                            btn.classList.remove('btn-loading')
                        }
                    }, function() {
                        showAlert('Error', 'No se pudo cerrar la sesión anterior', 'error')
                        // Rehabilitar botón
                        btn.disabled = false
                        btnText.classList.remove('d-none')
                        spinner.classList.add('d-none')
                        btn.classList.remove('btn-loading')
                    })
                } else {
                    // Rehabilitar botón si el usuario cancela
                    btn.disabled = false
                    btnText.classList.remove('d-none')
                    spinner.classList.add('d-none')
                    btn.classList.remove('btn-loading')
                }
            })
        } else {
            showToast(response.message, 'error')
            document.querySelector('#txtPass').value = ""
            // Rehabilitar botón
            btn.disabled = false
            btnText.classList.remove('d-none')
            spinner.classList.add('d-none')
            btn.classList.remove('btn-loading')
        }
    }, function(error) {
        showAlert('Atencion', error , 'error')
        // Rehabilitar botón
        btn.disabled = false
        btnText.classList.remove('d-none')
        spinner.classList.add('d-none')
        btn.classList.remove('btn-loading')
    })
}
function ajaxRequest(method, url, data, successCallback, errorCallback) {
    var request = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP')
    request.open(method, url, true)
    request.onreadystatechange = function () {
        if (request.readyState === 4) {
            if (request.status === 200) {
                try {
                    var response = JSON.parse(request.responseText)
                    successCallback(response)
                } catch (e) {
                    if (errorCallback) errorCallback('Error parsing response')
                }
            } else {
                if (errorCallback) errorCallback('Request failed')
            }
        }
    }
    request.onerror = function () {
        if (errorCallback) errorCallback('Network error')
    }
    request.send(data)
}
function showAlert(title, text, icon) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: 'OK'
    })
}

function showToast(message, icon) {
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    })
    
    Toast.fire({
        icon: icon || 'info',
        title: message
    })
}