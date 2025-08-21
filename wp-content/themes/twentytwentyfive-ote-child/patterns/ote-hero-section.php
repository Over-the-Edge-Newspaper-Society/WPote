<?php
/**
 * Title: OTE Hero Section
 * Slug: ote/hero-section
 * Categories: banner, header
 * Keywords: hero, banner, header, welcome
 * Description: A hero section with gradient background and centered content
 */
?>

<!-- wp:group {"className":"is-style-ote-hero","align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-style-ote-hero">
    <!-- wp:heading {"textAlign":"center","level":1,"fontSize":"xx-large","style":{"color":{"text":"#ffffff"}}} -->
    <h1 class="wp-block-heading has-text-align-center has-xx-large-font-size" style="color:#ffffff">Welcome to Over the Edge</h1>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph {"align":"center","fontSize":"large","style":{"color":{"text":"#ffffff"},"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|50"}}}} -->
    <p class="has-text-align-center has-large-font-size" style="color:#ffffff;margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--50)">UNBC's Independent Student Newspaper</p>
    <!-- /wp:paragraph -->
    
    <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
    <div class="wp-block-buttons">
        <!-- wp:button {"className":"is-style-ote-primary","style":{"color":{"background":"#ffffff","text":"var:preset|color|brand"}}} -->
        <div class="wp-block-button is-style-ote-primary">
            <a class="wp-block-button__link has-text-color has-background wp-element-button" style="color:var(--wp--preset--color--brand);background-color:#ffffff">Latest Stories</a>
        </div>
        <!-- /wp:button -->
        
        <!-- wp:button {"className":"is-style-ote-outline","style":{"color":{"text":"#ffffff"},"border":{"color":"#ffffff","width":"2px"}}} -->
        <div class="wp-block-button is-style-ote-outline">
            <a class="wp-block-button__link has-text-color wp-element-button" style="color:#ffffff;border-color:#ffffff;border-width:2px">About Us</a>
        </div>
        <!-- /wp:button -->
    </div>
    <!-- /wp:buttons -->
</div>
<!-- /wp:group -->