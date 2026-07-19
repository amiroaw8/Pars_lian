// resources/js/app.js
import './bootstrap';
import Swal from 'sweetalert2';
window.Swal = Swal;

// فعال‌سازی انیمیشن‌ها
document.addEventListener('DOMContentLoaded', function() {
    // اضافه کردن کلاس‌های انیمیشن به عناصر
    const elements = document.querySelectorAll('[data-animate]');
    elements.forEach(element => {
        const animation = element.getAttribute('data-animate');
        element.classList.add(`animate-${animation}`);
    });
    
    // فعال‌سازی منوی موبایل
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('show');
            navToggle.classList.toggle('active');
        });
    }
});