function initializeAlerts() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        // Si el alert tiene la clase 'alert-danger', lo eliminamos después de 5 segundos
        if (alert.classList.contains('alert-danger')) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        }
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeAlerts);

// Reinicializar después de cada actualización de la página
document.addEventListener('turbo:load', initializeAlerts);
document.addEventListener('turbo:render', initializeAlerts); 