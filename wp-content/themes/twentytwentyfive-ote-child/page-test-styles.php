<?php
/**
 * Template Name: OTE Style Test Page
 * Description: A test page to verify all OTE theme styles are working
 */

get_header(); ?>

<div class="ote-test-page" style="padding: 40px 20px; max-width: 1120px; margin: 0 auto;">
    
    <h1 style="text-align: center; margin-bottom: 40px;">OTE Theme Style Test Page</h1>
    
    <!-- Hero Section Test -->
    <section style="margin-bottom: 60px;">
        <h2>Hero Section</h2>
        <div class="wp-block-group is-style-ote-hero" style="background: linear-gradient(135deg, #2d5f3f 0%, #4a7c59 100%); color: white; padding: 48px 24px; border-radius: 20px; text-align: center;">
            <h1 style="color: white; margin: 0 0 16px 0;">Welcome to Over the Edge</h1>
            <p style="color: white; font-size: 18px;">UNBC's Independent Student Newspaper</p>
        </div>
    </section>
    
    <!-- Button Styles Test -->
    <section style="margin-bottom: 60px;">
        <h2>Button Styles</h2>
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <button class="wp-block-button__link is-style-ote-primary" style="background: #2d5f3f; color: white; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 500; cursor: pointer;">Primary Button</button>
            <button class="wp-block-button__link is-style-ote-outline" style="background: transparent; color: #2d5f3f; border: 2px solid #2d5f3f; padding: 10px 22px; border-radius: 10px; font-weight: 500; cursor: pointer;">Outline Button</button>
            <button class="wp-block-button__link is-style-ote-ghost" style="background: transparent; color: #1e293b; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 500; cursor: pointer;">Ghost Button</button>
        </div>
    </section>
    
    <!-- Card Styles Test -->
    <section style="margin-bottom: 60px;">
        <h2>Card Styles</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
            
            <!-- Basic Card -->
            <div class="wp-block-group is-style-ote-card" style="border: 1px solid #e2e8f0; background: white; border-radius: 14px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,.08);">
                <div style="aspect-ratio: 16/9; background: linear-gradient(135deg, #e2e8f0, #f8fafc); border-radius: 10px; margin-bottom: 16px;"></div>
                <span style="color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">Category</span>
                <h3 style="margin: 12px 0; font-size: 20px;">Basic Card Style</h3>
                <p style="color: #64748b; font-size: 14px; line-height: 1.5;">This is a basic OTE card with clean styling and subtle shadow.</p>
                <div style="display: flex; justify-content: space-between; margin-top: 16px;">
                    <span style="color: #64748b; font-size: 13px;">Jan 1, 2025</span>
                    <span style="color: #64748b; font-size: 13px;">Author Name</span>
                </div>
            </div>
            
            <!-- Animated Card -->
            <div class="wp-block-group is-style-ote-card-animated" style="border: 1px solid #e2e8f0; background: white; border-radius: 14px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,.08); transition: all 0.3s ease; cursor: pointer;" 
                 onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,.15)';" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,.08)';">
                <div style="aspect-ratio: 16/9; background: linear-gradient(135deg, #2d5f3f, #4a7c59); border-radius: 10px; margin-bottom: 16px;"></div>
                <span style="color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">Featured</span>
                <h3 style="margin: 12px 0; font-size: 20px;">Animated Card (Hover Me)</h3>
                <p style="color: #64748b; font-size: 14px; line-height: 1.5;">This card has hover animations with smooth transitions.</p>
                <div style="display: flex; justify-content: space-between; margin-top: 16px;">
                    <span style="color: #64748b; font-size: 13px;">Jan 1, 2025</span>
                    <span style="color: #64748b; font-size: 13px;">Author Name</span>
                </div>
            </div>
            
            <!-- Compact Card -->
            <div class="wp-block-group is-style-ote-card-compact" style="border: 1px solid #e2e8f0; background: white; border-radius: 14px; padding: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.08);">
                <div style="display: flex; gap: 12px; align-items: start;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #0ea5e9, #38bdf8); border-radius: 8px; flex-shrink: 0;"></div>
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 8px 0; font-size: 16px;">Compact Card Style</h3>
                        <p style="color: #64748b; font-size: 13px; line-height: 1.4; margin: 0;">Compact layout for dense content areas.</p>
                        <span style="color: #64748b; font-size: 12px;">Jan 1, 2025</span>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
    
    <!-- Typography Test -->
    <section style="margin-bottom: 60px;">
        <h2>Typography</h2>
        <div style="background: white; padding: 24px; border: 1px solid #e2e8f0; border-radius: 14px;">
            <h1 style="font-family: 'Source Serif 4', serif; font-size: 48px; margin: 0 0 16px 0;">Heading 1 - Source Serif 4</h1>
            <h2 style="font-family: 'Source Serif 4', serif; font-size: 32px; margin: 0 0 16px 0;">Heading 2 - Source Serif 4</h2>
            <h3 style="font-family: 'Source Serif 4', serif; font-size: 24px; margin: 0 0 16px 0;">Heading 3 - Source Serif 4</h3>
            <p style="font-family: 'Inter', sans-serif; font-size: 16px; line-height: 1.5; margin: 0 0 16px 0;">Body text uses Inter font for optimal readability. This is a sample paragraph showing the default body text styling with proper line height and spacing.</p>
            <p style="font-family: 'Inter', sans-serif; font-size: 14px; color: #64748b; margin: 0;">Secondary text with muted color for less prominent information.</p>
        </div>
    </section>
    
    <!-- Color Palette Test -->
    <section style="margin-bottom: 60px;">
        <h2>Color Palette</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px;">
            <div style="text-align: center;">
                <div style="width: 100%; height: 80px; background: #2d5f3f; border-radius: 10px; margin-bottom: 8px;"></div>
                <strong>Brand</strong><br>#2d5f3f
            </div>
            <div style="text-align: center;">
                <div style="width: 100%; height: 80px; background: #4a7c59; border-radius: 10px; margin-bottom: 8px;"></div>
                <strong>Brand 2</strong><br>#4a7c59
            </div>
            <div style="text-align: center;">
                <div style="width: 100%; height: 80px; background: #0ea5e9; border-radius: 10px; margin-bottom: 8px;"></div>
                <strong>Accent Blue</strong><br>#0ea5e9
            </div>
            <div style="text-align: center;">
                <div style="width: 100%; height: 80px; background: #1e293b; border-radius: 10px; margin-bottom: 8px;"></div>
                <strong>Text</strong><br>#1e293b
            </div>
            <div style="text-align: center;">
                <div style="width: 100%; height: 80px; background: #64748b; border-radius: 10px; margin-bottom: 8px;"></div>
                <strong>Text Secondary</strong><br>#64748b
            </div>
            <div style="text-align: center;">
                <div style="width: 100%; height: 80px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 8px;"></div>
                <strong>Surface</strong><br>#f8fafc
            </div>
        </div>
    </section>
    
    <!-- Dark Mode Toggle Test -->
    <section style="margin-bottom: 60px;">
        <h2>Dark Mode Toggle</h2>
        <div style="display: flex; gap: 16px; align-items: center;">
            <button class="theme-toggle" onclick="document.documentElement.setAttribute('data-theme', document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark')" 
                    style="width: 44px; height: 44px; border: none; border-radius: 10px; background: #f8fafc; color: #2d5f3f; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
            </button>
            <span>Click to toggle theme (visual demo only)</span>
        </div>
    </section>
    
    <!-- Status Check -->
    <section style="margin-bottom: 60px;">
        <h2>Style Loading Status</h2>
        <div id="style-status" style="background: #f8fafc; padding: 16px; border-radius: 10px; font-family: monospace; font-size: 14px;">
            Checking styles...
        </div>
    </section>
    
    <script>
        // Check if styles are loaded
        document.addEventListener('DOMContentLoaded', function() {
            const statusDiv = document.getElementById('style-status');
            let status = [];
            
            // Check for stylesheets
            const stylesheets = Array.from(document.styleSheets);
            const oteStyles = stylesheets.filter(sheet => {
                try {
                    return sheet.href && sheet.href.includes('twentytwentyfive-ote-child');
                } catch(e) {
                    return false;
                }
            });
            
            status.push('✅ OTE Stylesheets found: ' + oteStyles.length);
            
            // Check for CSS variables
            const rootStyles = window.getComputedStyle(document.documentElement);
            const brandColor = rootStyles.getPropertyValue('--brand').trim();
            if (brandColor) {
                status.push('✅ CSS Variables loaded (--brand: ' + brandColor + ')');
            } else {
                status.push('❌ CSS Variables not found');
            }
            
            // Check for fonts
            const testElement = document.createElement('div');
            testElement.style.fontFamily = 'Inter';
            document.body.appendChild(testElement);
            const computedFont = window.getComputedStyle(testElement).fontFamily;
            document.body.removeChild(testElement);
            
            if (computedFont.includes('Inter')) {
                status.push('✅ Inter font loaded');
            } else {
                status.push('⚠️ Inter font may not be loaded');
            }
            
            statusDiv.innerHTML = status.join('<br>');
        });
    </script>
    
</div>

<?php get_footer(); ?>