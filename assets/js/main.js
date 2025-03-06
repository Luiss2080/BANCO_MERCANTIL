/**
 * Banco Camba - Archivo JavaScript principal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Selector de idioma
    const languageSelector = document.getElementById('language');
    if (languageSelector) {
        // 02/03/2025: Corregido el manejador de eventos para el selector de idioma
        // para garantizar que el cambio de idioma funcione correctamente
        languageSelector.addEventListener('change', function() {
            window.location.href = `index.php?controller=usuario&action=cambiarIdioma&lang=${this.value}`;
        });
    }

    // Registrar Service Worker para PWA
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
            .then(function(registration) {
                console.log('Service Worker registrado con éxito:', registration.scope);
            })
            .catch(function(error) {
                console.log('Error al registrar el Service Worker:', error);
            });
    }

    // Confirmar eliminaciones
    const deleteButtons = document.querySelectorAll('.delete-button');
    if (deleteButtons) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                if (!confirm('¿Está seguro que desea eliminar este elemento?')) {
                    event.preventDefault();
                }
            });
        });
    }

    // Mostrar/ocultar contraseña
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    if (togglePasswordButtons) {
        togglePasswordButtons.forEach(button => {
            button.addEventListener('click', function() {
                const passwordInput = document.querySelector(this.getAttribute('toggle'));
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.classList.replace('icon-eye', 'icon-eye-off');
                } else {
                    passwordInput.type = 'password';
                    this.classList.replace('icon-eye-off', 'icon-eye');
                }
            });
        });
    }

    // Validación de formularios
    const forms = document.querySelectorAll('.needs-validation');
    if (forms) {
        forms.forEach(form => {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }

    // Datepickers
    const datepickers = document.querySelectorAll('.datepicker');
    if (datepickers.length > 0) {
        datepickers.forEach(input => {
            // Aquí iría la inicialización de datepicker si se está usando una librería
        });
    }

    // Cerrar alertas automáticamente
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    if (alerts.length > 0) {
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
    }
});

// Función para formatear moneda
function formatCurrency(amount, currency = 'BOB') {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

// Función para formatear fecha
function formatDate(dateString) {
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(dateString).toLocaleDateString('es-BO', options);
}

// Función para enviar transacciones pendientes cuando hay conexión
function syncTransactions() {
    if ('serviceWorker' in navigator && 'SyncManager' in window) {
        navigator.serviceWorker.ready
            .then(function(registration) {
                return registration.sync.register('sync-transactions');
            })
            .catch(function(err) {
                console.log('Error al registrar sync:', err);
            });
    }
}








