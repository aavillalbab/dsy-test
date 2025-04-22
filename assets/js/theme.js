// Función para actualizar el ícono del tema
function updateThemeIcon(theme) {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.innerHTML = theme === 'light' 
            ? '<i class="bi bi-moon-fill"></i>' 
            : '<i class="bi bi-sun-fill"></i>';
    }
}

// Función para cambiar el tema
function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    html.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
}

// Función para inicializar el tema
function initializeTheme() {
    const html = document.documentElement;
    const savedTheme = localStorage.getItem('theme') || 'light';
    
    html.setAttribute('data-bs-theme', savedTheme);
    updateThemeIcon(savedTheme);
    
    // Remover cualquier event listener existente
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        const newToggle = themeToggle.cloneNode(true);
        themeToggle.parentNode.replaceChild(newToggle, themeToggle);
        newToggle.addEventListener('click', toggleTheme);
    }
}

// Inicializar cuando el DOM esté listo PLfL584ETjnZXx3$
document.addEventListener('DOMContentLoaded', initializeTheme);

// Reinicializar después de cada actualización de la página
document.addEventListener('turbo:load', initializeTheme);
document.addEventListener('turbo:render', initializeTheme);
