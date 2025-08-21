<?php
/**
 * Template Name: Shortcode Test
 * Description: Test page for dark mode toggle shortcode
 */

get_header(); ?>

<div style="padding: 40px 20px; max-width: 800px; margin: 0 auto;">
    
    <h1>Dark Mode Toggle Shortcode Test</h1>
    
    <div style="margin: 40px 0; padding: 20px; background: var(--surface, #f8fafc); border: 1px solid var(--border, #e2e8f0); border-radius: 10px;">
        <h2>Basic Toggle</h2>
        <p>Default shortcode: <code>[dark_mode_toggle]</code></p>
        <?php echo do_shortcode('[dark_mode_toggle]'); ?>
    </div>
    
    <div style="margin: 40px 0; padding: 20px; background: var(--surface, #f8fafc); border: 1px solid var(--border, #e2e8f0); border-radius: 10px;">
        <h2>With Label</h2>
        <p>Shortcode: <code>[dark_mode_toggle show_label="true"]</code></p>
        <?php echo do_shortcode('[dark_mode_toggle show_label="true"]'); ?>
    </div>
    
    <div style="margin: 40px 0; padding: 20px; background: var(--surface, #f8fafc); border: 1px solid var(--border, #e2e8f0); border-radius: 10px;">
        <h2>Custom ID</h2>
        <p>Shortcode: <code>[dark_mode_toggle id="headerToggle"]</code></p>
        <?php echo do_shortcode('[dark_mode_toggle id="headerToggle"]'); ?>
    </div>
    
    <div style="margin: 40px 0; padding: 20px; background: var(--surface, #f8fafc); border: 1px solid var(--border, #e2e8f0); border-radius: 10px;">
        <h2>How to Use</h2>
        <h3>In Posts/Pages:</h3>
        <p>Simply add the shortcode in your content:</p>
        <pre style="background: white; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">[dark_mode_toggle]</pre>
        
        <h3>In Templates:</h3>
        <p>Use PHP to output the shortcode:</p>
        <pre style="background: white; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">&lt;?php echo do_shortcode('[dark_mode_toggle]'); ?&gt;</pre>
        
        <h3>In Widgets:</h3>
        <p>Add the shortcode directly in text widgets or use the new shortcode block.</p>
        
        <h3>Available Options:</h3>
        <ul>
            <li><strong>id:</strong> Button ID (default: themeToggle)</li>
            <li><strong>show_label:</strong> true, false (default: false)</li>
        </ul>
    </div>
    
    <div style="margin: 40px 0; padding: 20px; background: var(--surface, #f8fafc); border: 1px solid var(--border, #e2e8f0); border-radius: 10px;">
        <h2>Theme Status</h2>
        <div id="theme-status">Loading...</div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeStatus = document.getElementById('theme-status');
            const html = document.documentElement;
            
            function updateStatus() {
                const currentTheme = html.getAttribute('data-theme') || 'not set';
                const currentMode = html.dataset.themeMode || 'not set';
                
                themeStatus.innerHTML = `
                    <strong>Current Theme:</strong> ${currentTheme}<br>
                    <strong>Current Mode:</strong> ${currentMode}<br>
                    <strong>System Preference:</strong> ${window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'}
                `;
            }
            
            updateStatus();
            
            // Listen for theme changes
            const observer = new MutationObserver(updateStatus);
            observer.observe(html, { 
                attributes: true, 
                attributeFilter: ['data-theme', 'data-theme-mode'] 
            });
        });
    </script>
    
    <style>
        /* Simple test page styles - all complex toggle styling is now in inline-styles.php */
        .theme-toggle__label {
            margin-left: 8px;
            font-size: 14px;
            font-weight: 500;
        }
        
        /* Dark theme styles */
        [data-theme="dark"] {
            --bg: #0f172a;
            --surface: #1e293b;
            --text: #e6eaf2;
            --border: #334155;
            --brand: #4a7c59;
        }
        
        [data-theme="dark"] body {
            background: var(--bg);
            color: var(--text);
        }
    </style>
    
</div>

<?php get_footer(); ?>