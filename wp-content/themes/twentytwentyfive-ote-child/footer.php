        </main><!-- #primary -->
    </div><!-- #content -->

    <footer id="colophon" class="site-footer footer" role="contentinfo">
        <div class="container footer__grid">
            <div class="footer__brand">
                <div class="brand">
                    <span class="brand__logo" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
                            <defs>
                                <linearGradient id="ote-g-footer" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#2d5f3f"/>
                                    <stop offset="100%" stop-color="#4a7c59"/>
                                </linearGradient>
                            </defs>
                            <rect x="4" y="4" width="40" height="40" rx="10" fill="url(#ote-g-footer)"/>
                            <path d="M12 24a12 12 0 1 0 24 0 12 12 0 1 0-24 0Z" fill="none" stroke="#fff" stroke-width="3" />
                            <path d="M18 24h12" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="brand__name"><?php bloginfo('name'); ?></span>
                </div>
                <p class="body-s text-secondary">
                    &copy; <span id="year"><?php echo date('Y'); ?></span> <?php bloginfo('name'); ?>
                    <?php if (get_bloginfo('description')) : ?>
                        - <?php bloginfo('description'); ?>
                    <?php endif; ?>
                    <?php esc_html_e('All rights reserved.', 'ote-child-theme'); ?>
                </p>
            </div>
            
            <nav class="footer__nav" aria-label="<?php esc_attr_e('Footer', 'ote-child-theme'); ?>">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'menu_class'     => 'footer-menu',
                    'container'      => false,
                    'items_wrap'     => '%3$s',
                    'fallback_cb'    => function() {
                        echo '<a href="#">' . esc_html__('News', 'ote-child-theme') . '</a>';
                        echo '<a href="#">' . esc_html__('Clubs', 'ote-child-theme') . '</a>';
                        echo '<a href="#">' . esc_html__('Events', 'ote-child-theme') . '</a>';
                        echo '<a href="#">' . esc_html__('Calendar', 'ote-child-theme') . '</a>';
                        echo '<a href="#">' . esc_html__('Settings', 'ote-child-theme') . '</a>';
                    }
                ));
                ?>
            </nav>
        </div>
    </footer><!-- #colophon -->
</div><!-- #page -->

<style>
/* Footer specific styles */
.footer {
    background: var(--surface);
    padding: var(--space-8) 0 var(--space-6) 0;
    border-top: 1px solid var(--border);
    margin-top: var(--space-12);
}

.footer__grid {
    display: grid;
    gap: var(--space-6);
    grid-template-columns: 1fr;
    align-items: start;
}

@media (min-width: 768px) {
    .footer__grid {
        grid-template-columns: 1fr auto;
        align-items: center;
    }
}

.footer__brand {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
}

.footer__nav {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-4);
}

.footer__nav a {
    color: var(--text-sec);
    text-decoration: none;
    font-size: 14px;
    transition: color var(--trans-fast);
}

.footer__nav a:hover {
    color: var(--brand);
}

/* Mobile nav styles */
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--surface);
    border-top: 1px solid var(--border);
    padding: var(--space-2) var(--space-4);
    display: none;
    justify-content: space-around;
    z-index: 40;
}

@media (max-width: 768px) {
    .bottom-nav {
        display: flex;
    }
    
    .app-bar__nav {
        display: none;
    }
}

.bottom-nav__link {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: var(--space-2);
    color: var(--text-sec);
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    transition: color var(--trans-fast);
    min-width: 60px;
}

.bottom-nav__link:hover,
.bottom-nav__link.current-menu-item {
    color: var(--brand);
}

.bottom-nav__link .icon {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Mobile menu styles */
.mobile-nav {
    position: fixed;
    top: 0;
    left: -100%;
    width: 280px;
    height: 100vh;
    background: var(--surface);
    border-right: 1px solid var(--border);
    padding: var(--space-6) var(--space-4);
    z-index: 60;
    transition: left var(--trans-med);
    display: flex;
    flex-direction: column;
    gap: var(--space-4);
}

.mobile-nav[data-open="true"] {
    left: 0;
}

.mobile-nav a {
    display: block;
    padding: var(--space-3) var(--space-4);
    color: var(--text);
    text-decoration: none;
    border-radius: var(--radius-s);
    transition: background var(--trans-fast);
}

.mobile-nav a:hover {
    background: var(--muted);
}

/* Mobile menu overlay */
.mobile-nav-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 50;
    display: none;
}

.mobile-nav-overlay.active {
    display: block;
}
</style>

<?php wp_footer(); ?>

</body>
</html>