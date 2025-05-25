document.addEventListener('DOMContentLoaded', function() {
    // Inicializar animación de carga de página
    const pageTransition = document.createElement('div');
    pageTransition.className = 'page-transition';
    document.body.appendChild(pageTransition);
    
    setTimeout(() => {
        pageTransition.classList.add('loaded');
        setTimeout(() => {
            pageTransition.remove();
        }, 600);
    }, 300);
    
    // Añadir el patrón de fondo
    const bgPattern = document.createElement('div');
    bgPattern.className = 'bg-pattern';
    document.body.appendChild(bgPattern);
    
    // Crear efecto de estrellas en el hero
    if (document.querySelector('.hero')) {
        const starsContainer = document.createElement('div');
        starsContainer.className = 'stars';
        document.querySelector('.hero').appendChild(starsContainer);
        
        for (let i = 0; i < 150; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            
            const size = Math.random() * 2;
            const left = Math.random() * 100;
            const top = Math.random() * 100;
            const duration = 3 + Math.random() * 7;
            const delay = Math.random() * 5;
            const opacity = 0.1 + Math.random() * 0.7;
            
            star.style.width = `${size}px`;
            star.style.height = `${size}px`;
            star.style.left = `${left}%`;
            star.style.top = `${top}%`;
            star.style.setProperty('--duration', `${duration}s`);
            star.style.setProperty('--delay', `${delay}s`);
            star.style.setProperty('--opacity', opacity);
            
            starsContainer.appendChild(star);
        }
        
        // Crear el indicador de desplazamiento
        const scrollIndicator = document.createElement('div');
        scrollIndicator.className = 'scroll-indicator';
        scrollIndicator.innerHTML = '<div class="mouse"></div>';
        document.querySelector('.hero').appendChild(scrollIndicator);
    }
    
    // Detección de scroll para efectos en el header
    const header = document.querySelector('.header');
    const sectionElements = document.querySelectorAll('.fade-in');
    
    // Mejora para detección de scroll más eficiente
    let lastScrollTop = 0;
    function checkScroll() {
        // Optimización para solo disparar cuando es necesario
        const st = window.scrollY;
        if (Math.abs(lastScrollTop - st) <= 10) return;
        
        // Header effect con umbral dinámico
        const headerThreshold = window.innerHeight * 0.1;
        if (st > headerThreshold) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        lastScrollTop = st;
        
        // Fade-in con intersectionObserver para mejor rendimiento
        sectionElements.forEach(section => {
            const rect = section.getBoundingClientRect();
            const isVisible = rect.top <= window.innerHeight - 100;
            if (isVisible) {
                section.classList.add('visible');
            }
        });
    }
    
    // Inicializar al cargar
    checkScroll();
    window.addEventListener('scroll', checkScroll);
    
    // Añadir efecto quick view a productos
    document.querySelectorAll('.product-image').forEach(productImage => {
        const quickView = document.createElement('div');
        quickView.className = 'quick-view';
        
        const quickViewBtn = document.createElement('button');
        quickViewBtn.className = 'quick-view-btn';
        quickViewBtn.textContent = 'Vista Rápida';
        
        quickViewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Aquí iría la lógica para mostrar una vista rápida del producto
            alert('Vista rápida del producto');
        });
        
        quickView.appendChild(quickViewBtn);
        productImage.appendChild(quickView);
    });
    
    // Añadir footer divider
    const footerContent = document.querySelector('.footer-content');
    if (footerContent) {
        const copyright = footerContent.querySelector('p[style*="opacity"]');
        if (copyright) {
            const divider = document.createElement('div');
            divider.className = 'footer-divider';
            footerContent.insertBefore(divider, copyright);
            copyright.className = 'copyright';
            copyright.removeAttribute('style');
        }
    }
    
    // Animación suave al hacer clic en enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Efecto hover para logo
    const logo = document.querySelector('.logo');
    if (logo) {
        logo.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        logo.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }
    
    // Inicializar efecto de parallax para secciones
    const parallaxSections = document.querySelectorAll('.featured-products, .categories');
    window.addEventListener('scroll', function() {
        parallaxSections.forEach(section => {
            const scrollPos = window.scrollY;
            const sectionTop = section.offsetTop;
            const distance = scrollPos - sectionTop;
            
            if (Math.abs(distance) < window.innerHeight) {
                const parallaxSpeed = 0.15;
                const yPos = distance * parallaxSpeed;
                
                if (section.querySelector('::before')) {
                    section.style.setProperty('--parallax-offset', `${yPos}px`);
                }
            }
        });
    });
    
    // Mejora en efecto de hover en productos
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            const img = this.querySelector('.product-image img');
            if (img) img.style.transform = 'scale(1.08)';
        });
        
        card.addEventListener('mouseleave', function() {
            const img = this.querySelector('.product-image img');
            if (img) img.style.transform = 'scale(1)';
        });
    });
    
    // Añadir detector de página activa para navegación
    function markActiveLink() {
        const currentPage = window.location.pathname.split('/').pop() || 'index.php';
        document.querySelectorAll('.nav-links li').forEach(item => {
            const link = item.querySelector('a');
            if (link && link.getAttribute('href') === currentPage) {
                item.classList.add('active-link');
            }
        });
    }
    markActiveLink();
    
    // Mejora en animación de elementos al entrar en viewport
    const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px 0px -50px 0px'
    };

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });
    }
    
    console.log('Divine Supplements enhancements initialized');
});