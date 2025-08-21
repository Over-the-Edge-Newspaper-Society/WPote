/**
 * Club Cards Conditional Display
 * Handles showing/hiding club meta cards based on field content
 */

(function() {
    
    // Check if a field has content
    function hasFieldContent(fieldElement) {
        if (!fieldElement) {
            return false;
        }
        
        const content = fieldElement.querySelector('.organization-field-content');
        if (!content) {
            return false;
        }
        
        const text = (content.textContent || content.innerText || '').trim();
        const links = content.querySelector('a');
        const hasLink = links && links.href && links.href !== '' && links.href !== 'mailto:' && links.href !== '#';
        
        return text.length > 0 || hasLink;
    }
    
    // Check if a card content area has any meaningful content
    function hasCardContent(contentElement) {
        if (!contentElement) {
            return false;
        }
        
        // Check for organization field blocks
        const orgFields = contentElement.querySelectorAll('.wp-block-unbc-organization-field');
        if (orgFields.length === 0) {
            return false;
        }
        
        // Check if any organization field has content
        for (let field of orgFields) {
            if (hasFieldContent(field)) {
                return true;
            }
        }
        
        return false;
    }
    
    // Check membership card - look for the card with "Membership" heading
    function checkMembershipCard() {
        const cards = document.querySelectorAll('.club-meta-card');
        
        cards.forEach(function(card, index) {
            const heading = card.querySelector('h2');
            
            if (heading && heading.textContent.includes('Membership')) {
                const contentArea = card.querySelector('.club-meta-content');
                
                if (!hasCardContent(contentArea)) {
                    // Card stays hidden (already hidden by CSS)
                } else {
                    card.classList.add('has-content');
                }
            }
        });
    }
    
    // Check executive contact card - look for the card with "Executive" heading
    function checkExecutiveCard() {
        const cards = document.querySelectorAll('.club-meta-card');
        
        cards.forEach(function(card) {
            const heading = card.querySelector('h2');
            if (heading && heading.textContent.includes('Executive')) {
                const contentArea = card.querySelector('.club-meta-content');
                
                if (!hasCardContent(contentArea)) {
                    // Card stays hidden (already hidden by CSS)
                } else {
                    card.classList.add('has-content');
                }
            }
        });
    }
    
    // Check social media card and process social links
    function checkSocialCard() {
        const cards = document.querySelectorAll('.club-meta-card');
        
        cards.forEach(function(card) {
            const heading = card.querySelector('h2');
            if (heading && heading.textContent.includes('Connect')) {
                const socialLinks = card.querySelectorAll('.social-link');
                let hasAnySocial = false;
                
                socialLinks.forEach(function(socialLink, index) {
                    const field = socialLink.querySelector('.wp-block-unbc-organization-field');
                    const iconElement = socialLink.querySelector('p');
                    
                    if (hasFieldContent(field)) {
                        hasAnySocial = true;
                        processSocialLink(socialLink, field, iconElement, index);
                    } else {
                        // Hide individual social links that don't have content
                        socialLink.classList.add('hidden');
                    }
                });
                
                if (!hasAnySocial) {
                    // Card stays hidden (already hidden by CSS)
                } else {
                    card.classList.add('has-content');
                }
            }
        });
    }
    
    // Process individual social media link
    function processSocialLink(socialLink, field, iconElement, index) {
        if (!field) {
            socialLink.classList.add('hidden');
            return;
        }
        
        const content = field.querySelector('.organization-field-content');
        if (!content) {
            socialLink.classList.add('hidden');
            return;
        }
        
        const linkElement = content.querySelector('a');
        if (!linkElement || !linkElement.href || linkElement.href === '' || linkElement.href === 'mailto:' || linkElement.href === '#') {
            socialLink.classList.add('hidden');
            return;
        }
        
        // Determine platform name from common social media URLs
        let platformName = 'Link';
        const href = linkElement.href.toLowerCase();
        
        if (href.includes('instagram.com') || href.includes('alphapibeta')) {
            platformName = 'Instagram';
        } else if (href.includes('facebook.com')) {
            platformName = 'Facebook';
        } else if (href.includes('twitter.com') || href.includes('x.com')) {
            platformName = 'Twitter';
        } else if (href.includes('youtube.com')) {
            platformName = 'YouTube';
        } else if (href.includes('discord.com') || href.includes('discord.gg')) {
            platformName = 'Discord';
        } else if (href.includes('linktr.ee')) {
            platformName = 'Linktree';
        }
        
        
        // Create new social link button structure
        socialLink.innerHTML = '';
        socialLink.className = 'social-link-button';
        
        // Apply styles for icon-only button
        const styles = {
            display: 'inline-flex',
            alignItems: 'center',
            justifyContent: 'center',
            width: '48px',
            height: '48px',
            padding: '0',
            background: 'var(--brand, #2d5f3f)',
            color: 'white',
            textDecoration: 'none',
            borderRadius: '10px',
            transition: 'all 0.2s ease',
            cursor: 'pointer',
            border: 'none',
            margin: '4px'
        };
        
        Object.assign(socialLink.style, styles);
        
        // Add icon only (no text)
        if (iconElement) {
            const iconClone = iconElement.cloneNode(true);
            iconClone.style.width = '24px';
            iconClone.style.height = '24px';
            iconClone.style.display = 'flex';
            iconClone.style.alignItems = 'center';
            iconClone.style.justifyContent = 'center';
            iconClone.style.margin = '0';
            iconClone.style.padding = '0';
            socialLink.appendChild(iconClone);
        }
        
        // Add click handler
        socialLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.open(linkElement.href, '_blank');
        });
        
        // Add hover effect
        socialLink.addEventListener('mouseenter', function() {
            this.style.background = 'var(--brand-2, #4a7c59)';
            this.style.transform = 'translateY(-1px)';
        });
        
        socialLink.addEventListener('mouseleave', function() {
            this.style.background = 'var(--brand, #2d5f3f)';
            this.style.transform = 'translateY(0)';
        });
        
    }
    
    // Flag to prevent double processing
    let cardsProcessed = false;
    let retryCount = 0;
    const maxRetries = 50; // Max 5 seconds of retrying
    
    // Initialize conditional display
    function initializeCards() {
        // Check if cards exist, if not wait a bit
        const cards = document.querySelectorAll('.club-meta-card');
        if (cards.length === 0) {
            retryCount++;
            if (retryCount >= maxRetries) {
                return;
            }
            setTimeout(initializeCards, 100);
            return;
        }
        
        // Only skip if we've already successfully processed the cards
        if (cardsProcessed) {
            // But still check if cards are visible
            let anyVisible = false;
            cards.forEach(card => {
                if (window.getComputedStyle(card).display !== 'none') {
                    anyVisible = true;
                }
            });
            
            if (anyVisible) {
                return;
            } else {
                cardsProcessed = false;
            }
        }
        
        cardsProcessed = true;
        
        checkMembershipCard();
        checkExecutiveCard();
        checkSocialCard();
    }
    
    // Function to just process social links without hiding cards
    function processSocialLinksOnly() {
        const cards = document.querySelectorAll('.club-meta-card');
        
        cards.forEach(function(card) {
            const heading = card.querySelector('h2');
            if (heading && heading.textContent.includes('Connect')) {
                const socialLinks = card.querySelectorAll('.social-link');
                
                socialLinks.forEach(function(socialLink, index) {
                    const field = socialLink.querySelector('.wp-block-unbc-organization-field');
                    const iconElement = socialLink.querySelector('p');
                    
                    if (hasFieldContent(field)) {
                        processSocialLink(socialLink, field, iconElement, index);
                    }
                });
            }
        });
    }
    
    // Initialize immediately - runs as soon as script loads
    initializeCards();
    
    // Also initialize when DOM is ready (backup)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCards);
    }
    
    // Note: Removed MutationObserver to prevent double processing
})();