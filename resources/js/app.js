import './bootstrap';

// Theme toggle
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.createElement('button');
    themeToggle.className = 'theme-toggle';
    themeToggle.innerHTML = '<i class="bi bi-moon-fill"></i>';
    themeToggle.setAttribute('aria-label', 'Toggle dark mode');
    document.body.appendChild(themeToggle);

    const currentTheme = localStorage.getItem('theme') || 'light';
    document.body.setAttribute('data-theme', currentTheme);

    themeToggle.addEventListener('click', function() {
        const currentTheme = document.body.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.body.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        themeToggle.innerHTML = newTheme === 'dark' ? '<i class="bi bi-sun-fill"></i>' : '<i class="bi bi-moon-fill"></i>';
    });

    // Accessibility: Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            document.body.classList.add('keyboard-navigation');
        }
    });

    document.addEventListener('mousedown', function() {
        document.body.classList.remove('keyboard-navigation');
    });
});

// Font size controls
function increaseFontSize() {
    const body = document.body;
    const currentSize = parseFloat(getComputedStyle(body).fontSize);
    body.style.fontSize = (currentSize + 2) + 'px';
}

function decreaseFontSize() {
    const body = document.body;
    const currentSize = parseFloat(getComputedStyle(body).fontSize);
    body.style.fontSize = (currentSize - 2) + 'px';
}

function resetFontSize() {
    document.body.style.fontSize = '';
}

// High contrast toggle
function toggleHighContrast() {
    document.body.classList.toggle('high-contrast');
}

