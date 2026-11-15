const Notifications = {
    show: function(title, message, type = 'info') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const id = 'toast-' + Date.now();
        const icon = type === 'success' ? 'check-circle-fill' : 
                     type === 'error' ? 'exclamation-triangle-fill' : 
                     type === 'warning' ? 'exclamation-circle-fill' : 'info-circle-fill';
        
        const color = type === 'success' ? 'text-success' : 
                      type === 'error' ? 'text-danger' : 
                      type === 'warning' ? 'text-warning' : 'text-info';

        const html = `
            <div id="${id}" class="toast ${type}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bi bi-${icon} ${color} me-2"></i>
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
        const toastEl = document.getElementById(id);
        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();

        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    }
};

// Manejador global para el procesamiento de correos
document.addEventListener('DOMContentLoaded', function() {
    const btnProcesar = document.getElementById('btn-procesar-correos');
    if (btnProcesar) {
        btnProcesar.addEventListener('click', function(e) {
            e.preventDefault();
            
            fetch(window.APP_URL + '/correo/procesar')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Notifications.show('Sincronización', data.message, 'success');
                        // Opcional: recargar si estamos en el listado de tickets
                        if(window.location.href.includes('ticket/index')) {
                            setTimeout(() => window.location.reload(), 2000);
                        }
                    } else {
                        Notifications.show('Error', 'No se pudieron procesar los correos.', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Notifications.show('Error de Conexión', 'Hubo un problema al conectar con el servidor.', 'error');
                });
        });
    }
});
