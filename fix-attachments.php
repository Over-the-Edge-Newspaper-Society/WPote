<?php
// Fix attachment records for the correct Rahim Somani images
require_once('./wp-load.php');

echo "Fixing attachment records...\n";

// Update attachment titles and descriptions
wp_update_post(array(
    'ID' => 5097,
    'post_title' => 'Rahim Somani and his family',
    'post_excerpt' => 'Rahim Somani and his family photo with indigenous artwork background',
    'post_content' => ''
));

wp_update_post(array(
    'ID' => 5098, 
    'post_title' => 'Rahim Somani and UNBC Leadership',
    'post_excerpt' => 'Rahim Somani with UNBC leadership team',
    'post_content' => ''
));

// Update attachment metadata to reflect JPEG format
update_post_meta(5097, '_wp_attachment_metadata', serialize(array(
    'width' => 1920,
    'height' => 1280,
    'file' => '2024/12/1.jpg',
    'sizes' => array(
        'large' => array(
            'file' => '1-1024x768.jpg',
            'width' => 1024,
            'height' => 768,
            'mime-type' => 'image/jpeg'
        )
    ),
    'image_meta' => array()
)));

update_post_meta(5098, '_wp_attachment_metadata', serialize(array(
    'width' => 1920,
    'height' => 1280,
    'file' => '2024/12/2.jpg', 
    'sizes' => array(
        'large' => array(
            'file' => '2-1024x684.jpg',
            'width' => 1024,
            'height' => 684,
            'mime-type' => 'image/jpeg'
        )
    ),
    'image_meta' => array()
)));

echo "Attachment records updated successfully!\n";

// Verify the updates
$att1 = get_post(5097);
$att2 = get_post(5098);

echo "ID 5097: " . $att1->post_title . "\n";
echo "ID 5098: " . $att2->post_title . "\n";
?>