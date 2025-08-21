<?php
/**
 * Inline Styles
 * 
 * Handles theme inline styles that cannot be easily externalized
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add inline styles for theme functionality
 */
function ote_child_inline_styles() {
    ?>
    <style id="ote-inline-styles">
        /* Ensure dark mode toggle works */
        html {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Typography - Headline styles for post titles */
        .wp-block-post-title,
        h1.wp-block-post-title {
            font-size: clamp(24px, 2.6vw, 32px);
            line-height: 1.2;
            letter-spacing: -0.01em;
            font-weight: 600;
        }
        
        /* Extra large headlines for main pages */
        .page-template-default h1.wp-block-post-title,
        .single h1.wp-block-post-title {
            font-size: clamp(32px, 4vw, 48px);
            line-height: 1.1;
            letter-spacing: -0.02em;
        }
        
        /* Medium headlines */
        h2.wp-block-heading {
            font-size: clamp(20px, 2.2vw, 28px);
            letter-spacing: -0.01em;
            font-weight: 600;
        }
        
        /* Small headlines */
        h3.wp-block-heading {
            font-size: 18px;
            font-weight: 600;
        }
        
        /* Dark theme variables */
        [data-theme="dark"] {
            --brand: #4a7c59;
            --brand-2: #86a58e;
            --accent-blue: #38bdf8;
            --bg: #0f172a;
            --bg-elev: #1e293b;
            --surface: #1e293b;
            --text: #e6eaf2;
            --text-sec: #94a3b8;
            --border: #334155;
            --muted: #1e293b;
        }
        
        [data-theme="dark"] body {
            background: var(--bg);
            color: var(--text);
        }
        
        /* WordPress block elements in dark mode */
        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6,
        [data-theme="dark"] .wp-block-heading,
        [data-theme="dark"] .wp-block-post-title {
            color: var(--text) !important;
        }
        
        [data-theme="dark"] .wp-block-post-title a,
        [data-theme="dark"] .wp-block-heading a,
        [data-theme="dark"] h1 a,
        [data-theme="dark"] h2 a,
        [data-theme="dark"] h3 a,
        [data-theme="dark"] h4 a,
        [data-theme="dark"] h5 a,
        [data-theme="dark"] h6 a {
            color: var(--text) !important;
            text-decoration: none;
        }
        
        [data-theme="dark"] .wp-block-post-title a:hover,
        [data-theme="dark"] .wp-block-heading a:hover,
        [data-theme="dark"] a:hover {
            opacity: 0.8;
        }
        
        [data-theme="dark"] p,
        [data-theme="dark"] .wp-block-paragraph,
        [data-theme="dark"] .wp-block-post-excerpt,
        [data-theme="dark"] .wp-block-post-content {
            color: var(--text) !important;
        }
        
        [data-theme="dark"] .wp-block-post-date,
        [data-theme="dark"] .wp-block-post-author-name,
        [data-theme="dark"] .wp-block-post-terms {
            color: var(--text-sec) !important;
        }
        
        /* Mobile Menu Sheet (Centered Modal) */
        .mobile-menu-sheet {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            max-width: 100%;
            max-height: 100%;
            overflow: hidden;
            background: color-mix(in oklab, var(--bg, #ffffff), transparent 35%);
            backdrop-filter: blur(4px);
            display: grid;
            place-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 240ms cubic-bezier(.2,.6,.2,1);
            z-index: 9999;
            box-sizing: border-box;
        }
        
        [data-theme="dark"] .mobile-menu-sheet {
            background: color-mix(in oklab, var(--bg, #0f172a), transparent 35%);
        }
        
        .mobile-menu-sheet[data-open="true"] {
            opacity: 1;
            pointer-events: auto;
        }
        
        .mobile-menu-sheet__panel {
            width: min(560px, 92vw);
            max-width: calc(100vw - 32px);
            max-height: min(80vh, calc(100vh - 32px));
            background: var(--surface, #ffffff);
            border: 1px solid var(--border, #e2e8f0);
            box-shadow: 0 16px 40px rgba(0,0,0,.16);
            border-radius: 16px;
            padding: 24px 20px 32px;
            display: grid;
            gap: 20px;
            transform: translateY(12px) scale(.98);
            transition: all 240ms cubic-bezier(.2,.6,.2,1);
            opacity: 0;
            box-sizing: border-box;
            margin: 16px;
            overflow-y: auto;
        }
        
        [data-theme="dark"] .mobile-menu-sheet__panel {
            background: var(--surface, #1e293b);
            border-color: var(--border, #334155);
            box-shadow: 0 16px 40px rgba(0,0,0,.3);
        }
        
        .mobile-menu-sheet[data-open="true"] .mobile-menu-sheet__panel {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
        
        .mobile-menu-sheet__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: var(--text);
        }
        
        .mobile-menu-sheet__nav {
            display: grid;
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .mm-link {
            display: block;
            padding: 12px 14px;
            border: 1px solid var(--border, #e2e8f0);
            border-radius: 12px;
            text-decoration: none;
            color: var(--text, #1e293b);
            font-weight: 500;
            transition: all 200ms ease;
        }
        
        .mm-link:hover {
            background: var(--muted, #f8fafc);
            border-color: var(--brand, #2d5f3f);
        }
        
        .mm-link.current-menu-item {
            background: var(--brand, #2d5f3f);
            color: white;
            border-color: var(--brand, #2d5f3f);
        }
        
        .mobile-menu-sheet__actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            padding: 20px 0;
        }
        
        .mobile-menu-theme-toggle {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--muted, #f8fafc);
            border: 1px solid var(--border, #e2e8f0);
            border-radius: 50px;
            padding: 8px 12px;
            color: var(--text, #1e293b);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 200ms ease;
        }
        
        [data-theme="dark"] .mobile-menu-theme-toggle {
            background: var(--muted, #1e293b);
            border-color: var(--border, #334155);
            color: var(--text, #e6eaf2);
        }
        
        .mobile-menu-theme-toggle:hover {
            background: var(--brand, #2d5f3f);
            color: white;
            border-color: var(--brand, #2d5f3f);
        }
        
        [data-theme="dark"] .mobile-menu-theme-toggle:hover {
            background: var(--brand, #4a7c59);
            color: white;
            border-color: var(--brand, #4a7c59);
        }
        
        /* Hide on desktop */
        @media (min-width: 900px) {
            .mobile-menu-sheet {
                display: none;
            }
        }
        
        /* Hide WordPress default mobile menu */
        .wp-block-navigation__responsive-container.is-menu-open {
            display: none;
        }
        
        /* Horizontal alignment */
        .ote-logo-wrapper.ote-logo--align-center {
            justify-content: center;
        }
        
        .ote-logo-wrapper.ote-logo--align-right {
            justify-content: flex-end;
        }
        
        .ote-logo-wrapper.ote-logo--align-left {
            justify-content: flex-start;
        }
        
        /* Vertical alignment */
        .ote-logo-wrapper.ote-logo--valign-top {
            align-items: flex-start;
        }
        
        .ote-logo-wrapper.ote-logo--valign-center {
            align-items: center;
        }
        
        .ote-logo-wrapper.ote-logo--valign-bottom {
            align-items: flex-end;
        }
        
        .ote-logo-link {
            display: inline-block;
            text-decoration: none;
            outline: none !important;
            border: none !important;
        }
        
        .ote-logo-link:focus,
        .ote-logo-link:focus-visible {
            outline: none !important;
            box-shadow: none !important;
        }
        
        .ote-logo,
        .ote-logo-wrapper svg {
            outline: none !important;
            border: none !important;
        }
        
        .ote-logo-link:hover {
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }
        
        .ote-logo-placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-family: var(--font-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui);
        }
        
        /* Responsive Logo Scaling */
        @media (max-width: 768px) {
            .ote-logo-text {
                max-width: 90vw !important;
                height: auto !important;
            }
        }
        
        @media (max-width: 480px) {
            .ote-logo-text {
                max-width: 85vw !important;
                height: auto !important;
            }
        }
        
        /* OTE Theme Toggle - Clean Implementation */
        .ote-theme-toggle {
            position: relative;
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            border-radius: var(--radius-s, 10px);
            cursor: pointer;
            overflow: hidden;
            outline: none;
            transition: background 160ms cubic-bezier(.2,.6,.2,1);
        }
        
        .ote-theme-toggle:hover {
            background: var(--muted, #f8fafc);
        }
        
        .ote-theme-toggle:focus,
        .ote-theme-toggle:focus-visible {
            outline: none;
            box-shadow: none;
        }
        
        .ote-theme-toggle .theme-toggle__icon {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: transform 240ms cubic-bezier(.2,.6,.2,1), opacity 240ms cubic-bezier(.2,.6,.2,1) !important;
        }
        
        /* Simple translateY animation */
        html[data-theme="light"] .ote-theme-toggle .theme-toggle__icon--moon {
            transform: translateY(10px) !important;
            opacity: 0 !important;
        }
        
        html[data-theme="light"] .ote-theme-toggle .theme-toggle__icon--sun {
            transform: translateY(0) !important;
            opacity: 1 !important;
        }
        
        html[data-theme="dark"] .ote-theme-toggle .theme-toggle__icon--sun {
            transform: translateY(-10px) !important;
            opacity: 0 !important;
        }
        
        html[data-theme="dark"] .ote-theme-toggle .theme-toggle__icon--moon {
            transform: translateY(0) !important;
            opacity: 1 !important;
        }
        
        html:not([data-theme]) .ote-theme-toggle .theme-toggle__icon--moon {
            transform: translateY(10px) !important;
            opacity: 0 !important;
        }
        
        html:not([data-theme]) .ote-theme-toggle .theme-toggle__icon--sun {
            transform: translateY(0) !important;
            opacity: 1 !important;
        }
        
        /* Hide navbar toggle on mobile (keep mobile menu toggle visible) */
        @media (max-width: 899px) {
            .wp-block-group.alignfull .ote-theme-toggle,
            .wp-block-group .ote-theme-toggle:not(#mobileThemeToggle) {
                display: none !important;
            }
        }
        
        /* Organization Search Bar */
        .ote-org-search-wrapper {
            margin-bottom: var(--space-6, 24px);
            position: relative;
        }
        
        .ote-org-search-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Search bar alignment options */
        .ote-org-search--align-left .ote-org-search-container {
            margin: 0;
        }
        
        .ote-org-search--align-right .ote-org-search-container {
            margin: 0 0 0 auto;
        }
        
        /* Inline style for header integration */
        .ote-org-search--inline {
            margin-bottom: 0;
            display: inline-block;
        }
        
        .ote-org-search--inline .ote-org-search-container {
            max-width: 300px;
        }
        
        .ote-org-search--inline .ote-org-search-field {
            border-radius: 999px;
            background: color-mix(in oklab, var(--muted, #f8fafc), transparent 10%);
        }
        
        .ote-org-search--inline .ote-org-search-input {
            padding: 10px 14px 10px 42px;
            font-size: 14px;
        }
        
        .ote-org-search--inline .ote-org-search-icon {
            width: 18px;
            height: 18px;
            left: 14px;
        }
        
        .ote-org-search--inline .ote-org-search-count {
            display: none;
        }
        
        /* Compact style */
        .ote-org-search--compact {
            margin-bottom: var(--space-4, 16px);
        }
        
        .ote-org-search--compact .ote-org-search-container {
            max-width: 400px;
        }
        
        .ote-org-search--compact .ote-org-search-input {
            padding: 10px 14px 10px 42px;
            font-size: 14px;
        }
        
        .ote-org-search-field {
            position: relative;
            display: flex;
            align-items: center;
            background: var(--surface, #ffffff);
            border: 1px solid var(--border, #e2e8f0);
            border-radius: var(--radius-m, 14px);
            padding: 0;
            overflow: hidden;
            transition: border-color 240ms cubic-bezier(.2,.6,.2,1), box-shadow 240ms cubic-bezier(.2,.6,.2,1);
        }
        
        .ote-org-search-field:focus-within {
            border-color: var(--brand, #2d5f3f);
            box-shadow: 0 0 0 3px color-mix(in oklab, var(--brand, #2d5f3f), transparent 85%);
        }
        
        .ote-org-search-icon {
            position: absolute;
            left: 16px;
            color: var(--text-sec, #64748b);
            pointer-events: none;
        }
        
        .ote-org-search-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            font-size: 16px;
            background: transparent;
            border: none;
            outline: none;
            color: var(--text, #1e293b);
        }
        
        .ote-org-search-input::placeholder {
            color: var(--text-sec, #64748b);
        }
        
        .ote-org-search-count {
            position: absolute;
            right: 16px;
            font-size: 13px;
            color: var(--text-sec, #64748b);
            white-space: nowrap;
            pointer-events: none;
        }
        
        /* No results message */
        .ote-no-results {
            text-align: center;
            padding: var(--space-10, 40px) var(--space-4, 16px);
            color: var(--text-sec, #64748b);
        }
        
        .ote-no-results p {
            margin: 0 0 var(--space-2, 8px);
            font-size: 18px;
            color: var(--text, #1e293b);
        }
        
        .ote-no-results-hint {
            font-size: 14px;
            color: var(--text-sec, #64748b) !important;
        }
        
        /* Mobile adjustments */
        @media (max-width: 639px) {
            .ote-org-search-wrapper {
                margin-bottom: var(--space-4, 16px);
            }
            
            .ote-org-search-input {
                padding: 12px 16px 12px 44px;
                font-size: 15px;
            }
            
            .ote-org-search-count {
                display: none;
            }
            
            .ote-org-search-icon {
                width: 18px;
                height: 18px;
                left: 14px;
            }
        }
        
        /* Dark mode support for search */
        [data-theme="dark"] .ote-org-search-field {
            background: var(--surface);
            border-color: var(--border);
        }
        
        [data-theme="dark"] .ote-org-search-input {
            color: var(--text);
        }
        
        /* Category Filters Styles */
        .ote-category-filters {
            margin-bottom: var(--space-6);
        }
        
        .ote-chips {
            display: flex;
            flex-wrap: nowrap;
            gap: var(--space-2);
            justify-content: flex-start;
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
            padding-bottom: 4px;
        }
        
        .ote-chips::-webkit-scrollbar {
            height: 6px;
        }
        
        .ote-chips::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .ote-chips::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }
        
        .ote-chips::-webkit-scrollbar-thumb:hover {
            background: var(--text-sec);
        }
        
        .ote-chip {
            padding: 8px 16px;
            background: var(--muted);
            border: 1px solid var(--border);
            border-radius: 50px;
            color: var(--text);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
            white-space: nowrap;
        }
        
        .ote-chip:hover,
        .ote-chip.is-active {
            background: var(--brand);
            color: white;
            border-color: var(--brand);
        }
        
        .ote-tabs {
            display: flex;
            gap: 4px;
            background: var(--muted);
            padding: 4px;
            border-radius: 12px;
            overflow-x: auto;
        }
        
        .ote-tab {
            padding: 8px 16px;
            background: transparent;
            border: none;
            border-radius: 8px;
            color: var(--text-sec);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        
        .ote-tab:hover,
        .ote-tab.is-active {
            background: var(--surface);
            color: var(--text);
            box-shadow: var(--elev-1);
        }
        
        .ote-filter-item .count {
            margin-left: 6px;
            font-size: 12px;
            opacity: 0.7;
        }
    </style>
    <?php
}
add_action('wp_head', 'ote_child_inline_styles');