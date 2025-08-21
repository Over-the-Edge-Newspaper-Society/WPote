<?php
/**
 * Fix all corrupted attachment IDs in post content
 * The import process concatenated old IDs with new ones, creating corrupted references
 */

require_once('./wp-load.php');

echo "=== FIXING ALL CORRUPTED ATTACHMENT IDS ===\n\n";

global $wpdb;

// Get all posts with corrupted attachment IDs (8+ digits starting with 444844)
$posts_with_corrupted_ids = $wpdb->get_results("
    SELECT ID, post_title, post_content 
    FROM wp_posts 
    WHERE post_content LIKE '%444844%' 
    AND post_type = 'post'
");

echo "Found " . count($posts_with_corrupted_ids) . " posts with corrupted attachment IDs\n\n";

$total_fixes = 0;

foreach ($posts_with_corrupted_ids as $post) {
    echo "Processing: {$post->post_title} (ID: {$post->ID})\n";
    
    $updated_content = $post->post_content;
    $post_fixes = 0;
    
    // Pattern to find corrupted IDs like 4448448565, 4448448566, etc.
    // These should be 4565, 4566, etc.
    preg_match_all('/444844(\d{4,5})/', $updated_content, $matches);
    
    if (!empty($matches[0])) {
        foreach ($matches[0] as $index => $corrupted_id) {
            $correct_id = $matches[1][$index]; // Extract the last 4-5 digits
            
            echo "  - Fixing: {$corrupted_id} → {$correct_id}\n";
            
            // Replace all instances of the corrupted ID
            $patterns = array(
                '/wp-image-' . preg_quote($corrupted_id, '/') . '/',
                '/data-id=["\']' . preg_quote($corrupted_id, '/') . '["\']/',
                '/"id":' . preg_quote($corrupted_id, '/') . '/',
                '/\\"id\\":' . preg_quote($corrupted_id, '/') . '/',
                '/attachment_' . preg_quote($corrupted_id, '/') . '/',
            );
            
            $replacements = array(
                'wp-image-' . $correct_id,
                'data-id="' . $correct_id . '"',
                '"id":' . $correct_id,
                '\\"id\\":' . $correct_id,
                'attachment_' . $correct_id,
            );
            
            foreach ($patterns as $pattern_index => $pattern) {
                $before_count = substr_count($updated_content, $corrupted_id);
                $updated_content = preg_replace($pattern, $replacements[$pattern_index], $updated_content);
                $after_count = substr_count($updated_content, $corrupted_id);
                $fixes_made = $before_count - $after_count;
                $post_fixes += $fixes_made;
            }
        }
        
        // Update the post content if changes were made
        if ($updated_content !== $post->post_content) {
            $result = wp_update_post(array(
                'ID' => $post->ID,
                'post_content' => $updated_content
            ));
            
            if ($result && !is_wp_error($result)) {
                echo "  ✓ Updated post content ({$post_fixes} fixes)\n";
                $total_fixes += $post_fixes;
            } else {
                echo "  ✗ Failed to update post\n";
            }
        }
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total posts processed: " . count($posts_with_corrupted_ids) . "\n";
echo "Total ID fixes applied: {$total_fixes}\n";
echo "Attachment ID corruption fix complete!\n";
?>