<?php
/**
 * Admin Functions
 * 
 * Handles admin notices and tools
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display Admin Notices with Shortcode Information
 */
function ote_child_admin_notices() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $screen = get_current_screen();
    if ($screen && in_array($screen->base, ['post', 'edit'])) {
        ?>
        <div class="notice notice-info is-dismissible">
            <p><strong>OTE Dark Mode Toggle:</strong> Use the shortcode <code>[dark_mode_toggle]</code> to add a dark mode toggle anywhere.</p>
            <p><strong>With options:</strong> <code>[dark_mode_toggle style="pill" size="large" show_label="true" align="center"]</code></p>
            <hr style="margin: 12px 0;">
            <p><strong>OTE Logo:</strong> Use the shortcode <code>[ote_logo]</code> to add the OTE logo.</p>
            <p><strong>Logo options:</strong> <code>[ote_logo type="text" size="200" link="true" align="center" valign="top" variant="white"]</code></p>
            <p><strong>Logo types:</strong> <code>symbol</code> (icon only), <code>text</code> (full logo without subtext), <code>full</code> (full logo with subtext)</p>
            <p><strong>Alignment:</strong> <code>align="left|center|right"</code> (horizontal), <code>valign="top|center|bottom"</code> (vertical)</p>
            <p><strong>Variant option:</strong> <code>variant="default"</code> or <code>variant="white"</code> (for text type only)</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'ote_child_admin_notices');

/**
 * Admin Tools for Block Cleanup
 */
function ote_child_admin_tools() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Check if we need to show cleanup notice
    if (isset($_GET['ote_cleanup']) && $_GET['ote_cleanup'] === 'blocks') {
        ?>
        <div class="notice notice-info">
            <p><strong>OTE Theme:</strong> To remove broken blocks from posts, you can:</p>
            <ol>
                <li>Edit the post/page in the block editor</li>
                <li>Click on the broken block</li>
                <li>Choose "Remove Block" or convert it to HTML</li>
                <li>Replace with the working "Dark Mode Toggle (Simple)" block</li>
            </ol>
            <p>Or use this quick fix: <a href="?ote_fix_blocks=1&_wpnonce=<?php echo wp_create_nonce('ote_fix_blocks'); ?>" class="button">Auto-fix broken dark mode blocks</a></p>
        </div>
        <?php
    }
    
    // Handle auto-fix
    if (isset($_GET['ote_fix_blocks']) && wp_verify_nonce($_GET['_wpnonce'], 'ote_fix_blocks')) {
        $posts = get_posts(array(
            'post_type' => array('post', 'page'),
            'post_status' => 'any',
            'numberposts' => -1,
            's' => 'ote/dark-mode-toggle'
        ));
        
        $fixed = 0;
        foreach ($posts as $post) {
            $content = $post->post_content;
            $original = $content;
            
            // Replace broken dark mode toggle blocks with simple ones
            $content = preg_replace(
                '/<!-- wp:ote\/dark-mode-toggle.*?-->(.*?)<!-- \/wp:ote\/dark-mode-toggle -->/s',
                '<!-- wp:ote/dark-mode-toggle-simple --><!-- /wp:ote/dark-mode-toggle-simple -->',
                $content
            );
            
            if ($content !== $original) {
                wp_update_post(array(
                    'ID' => $post->ID,
                    'post_content' => $content
                ));
                $fixed++;
            }
        }
        
        ?>
        <div class="notice notice-success">
            <p><strong>OTE Theme:</strong> Fixed <?php echo $fixed; ?> posts with broken dark mode blocks.</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'ote_child_admin_tools');