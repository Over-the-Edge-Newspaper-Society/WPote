<?php
/**
 * Template Name: OTE Logo Test Page
 * Test page for OTE Logo shortcode functionality
 */

get_header(); ?>

<div style="max-width: 1200px; margin: 40px auto; padding: 20px;">
    <h1>OTE Logo Shortcode Tests</h1>
    
    <!-- Dark Mode Toggle -->
    <div style="margin-bottom: 40px;">
        <h2>Dark Mode Toggle</h2>
        <?php echo do_shortcode('[dark_mode_toggle style="button" show_label="true"]'); ?>
    </div>
    
    <!-- Logo Symbol -->
    <div style="margin-bottom: 40px;">
        <h2>Logo Symbol (Default)</h2>
        <?php echo do_shortcode('[ote_logo type="symbol" size="100"]'); ?>
    </div>
    
    <!-- Logo Symbol Centered -->
    <div style="margin-bottom: 40px;">
        <h2>Logo Symbol Centered with Link</h2>
        <?php echo do_shortcode('[ote_logo type="symbol" size="120" align="center" link="true"]'); ?>
    </div>
    
    <!-- Logo Text -->
    <div style="margin-bottom: 40px;">
        <h2>Logo Text</h2>
        <?php echo do_shortcode('[ote_logo type="text" size="300"]'); ?>
    </div>
    
    <!-- Logo Text Centered -->
    <div style="margin-bottom: 40px;">
        <h2>Logo Text Centered</h2>
        <?php echo do_shortcode('[ote_logo type="text" size="400" align="center" link="true"]'); ?>
    </div>
    
    <!-- Full Logo -->
    <div style="margin-bottom: 40px;">
        <h2>Full Logo (Symbol + Text)</h2>
        <?php echo do_shortcode('[ote_logo type="full" size="400"]'); ?>
    </div>
    
    <!-- Full Logo Centered -->
    <div style="margin-bottom: 40px;">
        <h2>Full Logo Centered with Link</h2>
        <?php echo do_shortcode('[ote_logo type="full" size="500" align="center" link="true"]'); ?>
    </div>
    
    <!-- Different Sizes -->
    <div style="margin-bottom: 40px;">
        <h2>Different Sizes</h2>
        <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            <?php echo do_shortcode('[ote_logo type="symbol" size="60"]'); ?>
            <?php echo do_shortcode('[ote_logo type="symbol" size="80"]'); ?>
            <?php echo do_shortcode('[ote_logo type="symbol" size="100"]'); ?>
            <?php echo do_shortcode('[ote_logo type="symbol" size="120"]'); ?>
        </div>
    </div>
    
    <!-- Test Dark Mode Text -->
    <div style="margin-bottom: 40px;">
        <h2>Logo with Dark Mode Text Test</h2>
        <p style="background: var(--surface, #f8fafc); padding: 20px; border-radius: 10px; margin: 20px 0;">
            Switch to dark mode using the toggle above to see how the text portion of the logo adapts.
            The "UNBC STUDENT NEWSPAPER" text should change color from dark to light.
        </p>
        <?php echo do_shortcode('[ote_logo type="text" size="350" align="center"]'); ?>
    </div>
    
    <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e2e8f0;">
        <h2>Usage Examples</h2>
        <pre style="background: #f8fafc; padding: 15px; border-radius: 8px; overflow-x: auto;">
<!-- Basic symbol -->
[ote_logo]

<!-- Text logo with custom size -->
[ote_logo type="text" size="300"]

<!-- Full logo with link and center alignment -->
[ote_logo type="full" size="400" align="center" link="true"]

<!-- Custom classes -->
[ote_logo type="symbol" size="100" class="my-custom-class"]
        </pre>
    </div>
</div>

<?php get_footer(); ?>