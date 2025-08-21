/**
 * Category Filter Functionality for OTE Child Theme
 * Handles filtering of WordPress Query Loop blocks based on categories
 */

// Global function for filtering by category (called by shortcode buttons)
window.oteFilterByCategory = function(buttonElement, categorySlug) {
    if (!buttonElement) return;
    
    const filterContainer = buttonElement.closest('.ote-category-filters');
    const targetSelector = filterContainer.dataset.target || '.wp-block-query';
    const ajaxEnabled = filterContainer.dataset.ajax === 'true';
    
    // Update active states
    updateActiveStates(buttonElement);
    
    if (ajaxEnabled) {
        // AJAX filtering (requires server-side endpoint)
        performAjaxFilter(categorySlug, targetSelector, buttonElement);
    } else {
        // Client-side filtering (works with static content)
        performClientSideFilter(categorySlug, targetSelector, filterContainer);
    }
};

// Update active states of filter buttons
function updateActiveStates(activeButton) {
    const filterContainer = activeButton.closest('.ote-category-filters');
    const allButtons = filterContainer.querySelectorAll('.ote-filter-item');
    
    allButtons.forEach(button => {
        button.classList.remove('is-active');
        button.setAttribute('aria-selected', 'false');
    });
    
    activeButton.classList.add('is-active');
    activeButton.setAttribute('aria-selected', 'true');
}

// Client-side filtering for static content
function performClientSideFilter(categorySlug, targetSelector, filterContainer) {
    let queryBlocks;
    
    // If targeting the default selector and we have a filter container,
    // look for the closest parent query block first
    if (targetSelector === '.wp-block-query' && filterContainer) {
        const closestQuery = filterContainer.closest('.wp-block-query');
        queryBlocks = closestQuery ? [closestQuery] : document.querySelectorAll(targetSelector);
    } else {
        queryBlocks = document.querySelectorAll(targetSelector);
    }
    
    queryBlocks.forEach(queryBlock => {
        const posts = queryBlock.querySelectorAll('.wp-block-post');
        
        posts.forEach(post => {
            if (categorySlug === 'all') {
                // Show all posts
                post.style.display = '';
                post.classList.remove('ote-filtered-out');
            } else {
                // Check if post has the category
                const postCategories = getPostCategories(post);
                const hasCategory = postCategories.includes(categorySlug);
                
                if (hasCategory) {
                    post.style.display = '';
                    post.classList.remove('ote-filtered-out');
                } else {
                    post.style.display = 'none';
                    post.classList.add('ote-filtered-out');
                }
            }
        });
        
        // Update empty state if no posts are visible
        updateEmptyState(queryBlock);
    });
}

// Get categories from post element (various methods)
function getPostCategories(postElement) {
    const categories = [];
    
    // Method 1: Check for category classes on post element
    const classList = postElement.classList;
    classList.forEach(className => {
        if (className.startsWith('category-')) {
            categories.push(className.replace('category-', ''));
        }
    });
    
    // Method 2: Check for category links in post content
    const categoryLinks = postElement.querySelectorAll('.wp-block-post-terms a, .taxonomy-category a, .post-categories a');
    categoryLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.includes('/category/')) {
            const slug = href.split('/category/')[1].split('/')[0];
            if (slug && !categories.includes(slug)) {
                categories.push(slug);
            }
        }
    });
    
    // Method 3: Check data attributes
    const categoryData = postElement.dataset.categories;
    if (categoryData) {
        const dataCategories = categoryData.split(',').map(cat => cat.trim());
        dataCategories.forEach(cat => {
            if (!categories.includes(cat)) {
                categories.push(cat);
            }
        });
    }
    
    return categories;
}

// AJAX filtering (for dynamic content)
function performAjaxFilter(categorySlug, targetSelector, buttonElement) {
    const filterContainer = buttonElement.closest('.ote-category-filters');
    
    // Add loading state
    buttonElement.classList.add('loading');
    
    // Prepare AJAX request
    const formData = new FormData();
    formData.append('action', 'ote_filter_posts');
    formData.append('category', categorySlug);
    formData.append('nonce', window.ote_ajax?.nonce || '');
    
    fetch(window.ote_ajax?.ajax_url || '/wp-admin/admin-ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update content
            const queryBlocks = document.querySelectorAll(targetSelector);
            queryBlocks.forEach(queryBlock => {
                const postsContainer = queryBlock.querySelector('.wp-block-post-template, .wp-block-query-loop');
                if (postsContainer) {
                    postsContainer.innerHTML = data.data.html;
                }
            });
        } else {
            console.error('Filter error:', data.data);
        }
    })
    .catch(error => {
        console.error('AJAX error:', error);
    })
    .finally(() => {
        // Remove loading state
        buttonElement.classList.remove('loading');
    });
}

// Update empty state message
function updateEmptyState(queryBlock) {
    const posts = queryBlock.querySelectorAll('.wp-block-post:not(.ote-filtered-out)');
    const postsContainer = queryBlock.querySelector('.wp-block-post-template, .wp-block-query-loop');
    
    if (!postsContainer) return;
    
    // Remove existing empty state
    const existingEmpty = queryBlock.querySelector('.ote-empty-state');
    if (existingEmpty) {
        existingEmpty.remove();
    }
    
    if (posts.length === 0) {
        // Add empty state
        const emptyState = document.createElement('div');
        emptyState.className = 'ote-empty-state';
        emptyState.innerHTML = `
            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                <h3 style="margin-bottom: 8px;">No posts found</h3>
                <p>Try selecting a different category or check back later.</p>
            </div>
        `;
        postsContainer.appendChild(emptyState);
    }
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    // Hide category filter buttons that have no corresponding posts
    hideCategoryFiltersWithNoPosts();
    
    // Add keyboard support for filter buttons
    const filterButtons = document.querySelectorAll('.ote-filter-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                button.click();
            }
        });
    });
    
    // Initialize scroll gradient indicators
    initializeScrollGradients();
    
    // Dark mode theme observer
    observeThemeChanges();
    
    // Handle URL hash-based filtering
    const handleHashChange = () => {
        const hash = window.location.hash.substring(1);
        if (hash.startsWith('category-')) {
            const categorySlug = hash.replace('category-', '');
            const button = document.querySelector(`[data-category="${categorySlug}"]`);
            if (button) {
                oteFilterByCategory(button, categorySlug);
            }
        }
    };
    
    // Listen for hash changes
    window.addEventListener('hashchange', handleHashChange);
    
    // Handle initial hash
    if (window.location.hash) {
        handleHashChange();
    }
});

// Utility function to create a single chip/tab button
window.oteCreateFilterButton = function(options = {}) {
    const {
        text = 'Filter',
        category = '',
        style = 'chip',
        active = false,
        showCount = false,
        count = 0
    } = options;
    
    const button = document.createElement('button');
    button.className = `ote-${style} ote-filter-item ${active ? 'is-active' : ''}`;
    button.setAttribute('role', 'tab');
    button.setAttribute('aria-selected', active ? 'true' : 'false');
    button.setAttribute('data-category', category);
    button.onclick = () => oteFilterByCategory(button, category);
    
    let innerHTML = text;
    if (showCount) {
        innerHTML += `<span class="count">${count}</span>`;
    }
    button.innerHTML = innerHTML;
    
    return button;
};

// Dark mode theme observer
function observeThemeChanges() {
    // Watch for data-theme attribute changes on html/body
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && 
                (mutation.attributeName === 'data-theme' || 
                 mutation.attributeName === 'class')) {
                updateChipTheme();
            }
        });
    });
    
    // Observe html and body elements
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-theme', 'class']
    });
    
    observer.observe(document.body, {
        attributes: true,
        attributeFilter: ['class', 'data-theme']
    });
    
    // Watch for prefers-color-scheme changes
    if (window.matchMedia) {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addListener(updateChipTheme);
    }
    
    // Initial theme update
    updateChipTheme();
}

// Update chip theme based on current dark/light mode
function updateChipTheme() {
    const isDarkMode = 
        document.documentElement.getAttribute('data-theme') === 'dark' ||
        document.body.classList.contains('dark-theme') ||
        document.body.classList.contains('dark-mode') ||
        document.body.classList.contains('dark') ||
        (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches &&
         !document.documentElement.getAttribute('data-theme'));
    
    const chips = document.querySelectorAll('.ote-chip, .ote-tab, .ote-filter-item');
    chips.forEach(chip => {
        if (isDarkMode) {
            chip.classList.add('dark-mode-chip');
        } else {
            chip.classList.remove('dark-mode-chip');
        }
    });
}

// Initialize scroll gradient indicators for filter containers
function initializeScrollGradients() {
    const filterContainers = document.querySelectorAll('.ote-category-filters');
    
    filterContainers.forEach(filterContainer => {
        const chipsContainer = filterContainer.querySelector('.ote-chips');
        if (!chipsContainer) return;
        
        // Function to update gradient visibility based on scroll position
        const updateGradients = () => {
            const scrollLeft = chipsContainer.scrollLeft;
            const scrollWidth = chipsContainer.scrollWidth;
            const clientWidth = chipsContainer.clientWidth;
            
            // Check if content is scrollable
            const isScrollable = scrollWidth > clientWidth;
            
            if (isScrollable) {
                filterContainer.classList.add('has-scroll');
                
                // Check if at start
                if (scrollLeft <= 5) {
                    filterContainer.classList.add('at-start');
                } else {
                    filterContainer.classList.remove('at-start');
                }
                
                // Check if at end
                if (scrollLeft + clientWidth >= scrollWidth - 5) {
                    filterContainer.classList.add('at-end');
                } else {
                    filterContainer.classList.remove('at-end');
                }
            } else {
                filterContainer.classList.remove('has-scroll', 'at-start', 'at-end');
            }
        };
        
        // Update on scroll
        chipsContainer.addEventListener('scroll', updateGradients);
        
        // Update on resize
        window.addEventListener('resize', updateGradients);
        
        // Initial update
        updateGradients();
        
        // Re-check after fonts load (can affect text width)
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(updateGradients);
        }
    });
}

// Hide category filter buttons that have no corresponding posts in the current query
function hideCategoryFiltersWithNoPosts() {
    const filterContainers = document.querySelectorAll('.ote-category-filters');
    
    filterContainers.forEach(filterContainer => {
        const targetSelector = filterContainer.dataset.target || '.wp-block-query';
        
        // Find the closest query block if using default selector
        let queryBlocks;
        if (targetSelector === '.wp-block-query') {
            const closestQuery = filterContainer.closest('.wp-block-query');
            queryBlocks = closestQuery ? [closestQuery] : [];
        } else {
            queryBlocks = document.querySelectorAll(targetSelector);
        }
        
        if (queryBlocks.length === 0) return;
        
        // Get all categories that exist in the visible posts
        const existingCategories = new Set(['all']); // Always keep "All" button
        
        queryBlocks.forEach(queryBlock => {
            const posts = queryBlock.querySelectorAll('.wp-block-post');
            posts.forEach(post => {
                const postCategories = getPostCategories(post);
                postCategories.forEach(cat => existingCategories.add(cat));
            });
        });
        
        // Hide filter buttons that don't have corresponding posts
        const filterButtons = filterContainer.querySelectorAll('.ote-filter-item');
        filterButtons.forEach(button => {
            const categorySlug = button.dataset.category;
            if (!existingCategories.has(categorySlug)) {
                button.style.display = 'none';
            } else {
                button.style.display = '';
            }
        });
    });
}