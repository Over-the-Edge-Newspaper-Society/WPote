/**
 * Search Sheet Functionality - Works for both mobile and desktop
 */
document.addEventListener('DOMContentLoaded', function() {
    const searchSheet = document.getElementById('search-sheet');
    const searchInput = document.querySelector('.search-input');
    const searchClear = document.querySelector('.search-clear');
    const searchForm = document.querySelector('.search-form');
    const searchResults = document.querySelector('.search-results');
    const searchEmpty = document.querySelector('.search-empty');
    const searchContent = document.querySelector('.search-results-content');
    const body = document.body;
    
    let searchTimeout;
    
    if (!searchSheet) return;
    
    // Function to open search sheet
    function openSearchSheet() {
        searchSheet.setAttribute('data-open', 'true');
        body.style.overflow = 'hidden';
        
        // Focus on input after animation
        setTimeout(() => {
            if (searchInput) {
                searchInput.focus();
            }
        }, 300);
        
        // Trap focus
        trapFocus();
    }
    
    // Function to close search sheet
    function closeSearchSheet() {
        searchSheet.setAttribute('data-open', 'false');
        body.style.overflow = '';
        
        // Clear search
        if (searchInput) {
            searchInput.value = '';
            updateClearButton();
            showEmptyState();
        }
    }
    
    // Function to toggle search sheet
    function toggleSearchSheet() {
        const isOpen = searchSheet.getAttribute('data-open') === 'true';
        if (isOpen) {
            closeSearchSheet();
        } else {
            openSearchSheet();
        }
    }
    
    // Trap focus within the sheet
    function trapFocus() {
        const focusableElements = searchSheet.querySelectorAll(
            'input, button, [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length === 0) return;
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        function handleTabKey(e) {
            if (e.key !== 'Tab') return;
            
            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        }
        
        // Remove existing listeners
        searchSheet.removeEventListener('keydown', handleTabKey);
        // Add new listener
        searchSheet.addEventListener('keydown', handleTabKey);
    }
    
    // Update clear button visibility
    function updateClearButton() {
        if (searchInput && searchClear) {
            if (searchInput.value.length > 0) {
                searchClear.classList.add('visible');
            } else {
                searchClear.classList.remove('visible');
            }
        }
    }
    
    // Show empty state
    function showEmptyState() {
        if (searchEmpty && searchContent) {
            searchEmpty.style.display = 'block';
            searchContent.style.display = 'none';
        }
    }
    
    // Show search results
    function showSearchResults() {
        if (searchEmpty && searchContent) {
            searchEmpty.style.display = 'none';
            searchContent.style.display = 'block';
        }
    }
    
    // Perform search via AJAX
    function performSearch(query) {
        if (!query || query.length < 2) {
            showEmptyState();
            return;
        }
        
        // Show loading state
        if (searchContent) {
            searchContent.innerHTML = '<div style="text-align: center; padding: 20px; color: var(--text-secondary);">Searching...</div>';
            showSearchResults();
        }
        
        // Make AJAX request
        const formData = new FormData();
        formData.append('action', 'ote_search');
        formData.append('query', query);
        formData.append('nonce', searchAjax.nonce);
        
        fetch(searchAjax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySearchResults(data.data);
            } else {
                displaySearchError();
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            displaySearchError();
        });
    }
    
    // Display search results
    function displaySearchResults(results) {
        if (!searchContent) return;
        
        let html = '';
        
        // Posts section
        if (results.posts && results.posts.length > 0) {
            html += '<div class="search-section"><h3 class="search-section__title">Posts</h3>';
            results.posts.forEach(post => {
                html += `
                    <a href="${post.url}" class="search-item">
                        <span class="search-item__icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="14" rx="2"/>
                                <path d="M7 8h10M7 12h10M7 16h6"/>
                            </svg>
                        </span>
                        <div class="search-item__content">
                            <h4 class="search-item__title">${post.title}</h4>
                            <p class="search-item__excerpt">${post.excerpt}</p>
                        </div>
                    </a>
                `;
            });
            html += '</div>';
        }
        
        // Pages section
        if (results.pages && results.pages.length > 0) {
            html += '<div class="search-section"><h3 class="search-section__title">Pages</h3>';
            results.pages.forEach(page => {
                html += `
                    <a href="${page.url}" class="search-item">
                        <span class="search-item__icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14,2 14,8 20,8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10,9 9,9 8,9"/>
                            </svg>
                        </span>
                        <div class="search-item__content">
                            <h4 class="search-item__title">${page.title}</h4>
                            <p class="search-item__excerpt">${page.excerpt}</p>
                        </div>
                    </a>
                `;
            });
            html += '</div>';
        }
        
        if (html === '') {
            html = '<div style="text-align: center; padding: 20px; color: var(--text-secondary);">No results found. Try different keywords.</div>';
        }
        
        searchContent.innerHTML = html;
        showSearchResults();
    }
    
    // Display search error
    function displaySearchError() {
        if (searchContent) {
            searchContent.innerHTML = '<div style="text-align: center; padding: 20px; color: var(--text-secondary);">Search temporarily unavailable. Please try again.</div>';
            showSearchResults();
        }
    }
    
    // Event listeners
    
    // Search button click handlers
    document.addEventListener('click', function(e) {
        // Open search sheet
        if (e.target.closest('.search-toggle') || e.target.closest('[data-search-open]')) {
            e.preventDefault();
            openSearchSheet();
            return;
        }
        
        // Close search sheet
        if (e.target.closest('[data-search-close]')) {
            e.preventDefault();
            closeSearchSheet();
            return;
        }
        
        // Close when clicking backdrop
        if (e.target === searchSheet) {
            closeSearchSheet();
            return;
        }
        
        // Clear search
        if (e.target.closest('.search-clear')) {
            e.preventDefault();
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
                updateClearButton();
                showEmptyState();
            }
            return;
        }
    });
    
    // Search input handlers
    if (searchInput) {
        // Input event for real-time search
        searchInput.addEventListener('input', function() {
            updateClearButton();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Set new timeout for search
            searchTimeout = setTimeout(() => {
                performSearch(this.value.trim());
            }, 300);
        });
        
        // Prevent form submission if using AJAX
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && this.value.trim().length < 2) {
                e.preventDefault();
            }
        });
    }
    
    // Form submission handler
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const query = searchInput ? searchInput.value.trim() : '';
            if (query.length < 2) {
                e.preventDefault();
                return;
            }
            // Allow normal form submission for full search page
        });
    }
    
    // Keyboard handlers
    document.addEventListener('keydown', function(e) {
        // Close on Escape
        if (e.key === 'Escape' && searchSheet.getAttribute('data-open') === 'true') {
            e.preventDefault();
            closeSearchSheet();
        }
        
        // Open search with Ctrl/Cmd + K
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openSearchSheet();
        }
    });
    
    // Initialize
    updateClearButton();
    showEmptyState();
});