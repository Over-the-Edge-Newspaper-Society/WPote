/**
 * Twenty Twenty-Five Franken UI Theme JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme toggle functionality
    const themeToggle = document.getElementById('theme-toggle');
    const lightIcon = document.querySelector('.theme-icon-light');
    const darkIcon = document.querySelector('.theme-icon-dark');
    const htmlElement = document.documentElement;
    
    if (themeToggle && lightIcon && darkIcon) {
        function updateToggleIcon() {
            if (htmlElement.classList.contains('dark')) {
                lightIcon.style.display = 'none';
                darkIcon.style.display = 'inline';
            } else {
                lightIcon.style.display = 'inline';
                darkIcon.style.display = 'none';
            }
        }
        
        themeToggle.addEventListener('click', function() {
            const isDark = htmlElement.classList.contains('dark');
            const __FRANKEN__ = JSON.parse(localStorage.getItem("__FRANKEN__") || "{}");
            
            if (isDark) {
                htmlElement.classList.remove('dark');
                __FRANKEN__.mode = 'light';
            } else {
                htmlElement.classList.add('dark');
                __FRANKEN__.mode = 'dark';
            }
            
            localStorage.setItem("__FRANKEN__", JSON.stringify(__FRANKEN__));
            updateToggleIcon();
        });
        
        // Initial icon update
        updateToggleIcon();
    }
    
    // Initialize UIkit components if available
    if (typeof UIkit !== 'undefined') {
        // Auto-initialize any UIkit components on the page
        UIkit.use(UIkit.icons);
        
        // Initialize lightbox for galleries
        const galleries = document.querySelectorAll('[uk-lightbox]');
        galleries.forEach(gallery => {
            UIkit.lightbox(gallery);
        });
        
        // Initialize any other UIkit components as needed
        const dropdowns = document.querySelectorAll('[uk-dropdown]');
        dropdowns.forEach(dropdown => {
            UIkit.dropdown(dropdown);
        });
        
        const offcanvas = document.querySelectorAll('[uk-offcanvas]');
        offcanvas.forEach(canvas => {
            UIkit.offcanvas(canvas);
        });
    }
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Make entire card clickable including image area
    const postCards = document.querySelectorAll('.wp-block-post');
    postCards.forEach(card => {
        // Find the title link in the card
        const titleLink = card.querySelector('.wp-block-post-title a');
        
        if (titleLink && titleLink.href) {
            // Make the card clickable
            card.style.cursor = 'pointer';
            
            // Add click handler to the entire card
            card.addEventListener('click', function(e) {
                // Prevent default for all clicks within the card
                e.preventDefault();
                
                // Check if clicking on a different link (author, categories)
                const clickedElement = e.target;
                const clickedLink = clickedElement.closest('a');
                
                // If clicking on author or category links, let them work
                if (clickedLink && !clickedLink.closest('.wp-block-post-title') && !clickedLink.closest('.wp-block-post-featured-image')) {
                    window.location.href = clickedLink.href;
                } else {
                    // Otherwise, go to the main post link
                    window.location.href = titleLink.href;
                }
            });
            
            // Visual feedback on mousedown
            card.addEventListener('mousedown', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            card.addEventListener('mouseup', function() {
                this.style.transform = '';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        }
    });
    
    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                }
            });
        });
        
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    // Reading progress bar for single posts/pages
    const progressBar = document.createElement('div');
    progressBar.className = 'reading-progress';
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: hsl(var(--primary));
        z-index: 9999;
        transition: width 0.1s ease;
    `;
    
    if (document.body.classList.contains('single') || document.body.classList.contains('page')) {
        document.body.appendChild(progressBar);
        
        window.addEventListener('scroll', function() {
            const article = document.querySelector('article') || document.querySelector('main');
            if (article) {
                const articleHeight = article.offsetHeight;
                const articleTop = article.offsetTop;
                const scrollTop = window.pageYOffset;
                const windowHeight = window.innerHeight;
                
                const scrolled = (scrollTop - articleTop) / (articleHeight - windowHeight);
                const progress = Math.max(0, Math.min(100, scrolled * 100));
                
                progressBar.style.width = progress + '%';
            }
        });
    }
    
    // Auto-hide navigation on scroll (mobile)
    if (window.innerWidth <= 768) {
        let lastScrollTop = 0;
        const nav = document.querySelector('.wp-block-navigation');
        
        if (nav) {
            window.addEventListener('scroll', function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                if (scrollTop > lastScrollTop && scrollTop > 100) {
                    // Scrolling down
                    nav.style.transform = 'translateY(-100%)';
                } else {
                    // Scrolling up
                    nav.style.transform = 'translateY(0)';
                }
                
                lastScrollTop = scrollTop;
            });
        }
    }
});