<?php
/**
 * Title: OTE Card Grid
 * Slug: ote/card-grid
 * Categories: posts
 * Keywords: card, grid, posts, query
 * Description: A responsive card grid layout for displaying posts with OTE styling
 */
?>

<!-- wp:query {"queryId":1,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"is-style-ote-card-grid-animated"} -->
<div class="wp-block-query is-style-ote-card-grid-animated">
    <!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
        <!-- wp:group {"className":"is-style-ote-card-animated","layout":{"type":"constrained"}} -->
        <div class="wp-block-group is-style-ote-card-animated">
            <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9"} /-->
            
            <!-- wp:group {"className":"card__content","layout":{"type":"constrained"}} -->
            <div class="wp-block-group card__content">
                <!-- wp:post-terms {"term":"category","fontSize":"small","style":{"color":{"text":"var:preset|color|text-secondary"}}} /-->
                
                <!-- wp:post-title {"isLink":true,"fontSize":"large"} /-->
                
                <!-- wp:post-excerpt {"moreText":"Read more →","excerptLength":20} /-->
                
                <!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between"},"style":{"spacing":{"margin":{"top":"auto"}}}} -->
                <div class="wp-block-group" style="margin-top:auto">
                    <!-- wp:post-date {"fontSize":"small","style":{"color":{"text":"var:preset|color|text-secondary"}}} /-->
                    
                    <!-- wp:post-author-name {"fontSize":"small","style":{"color":{"text":"var:preset|color|text-secondary"}}} /-->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
    <!-- /wp:post-template -->
    
    <!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->
        <!-- wp:query-pagination-previous /-->
        <!-- wp:query-pagination-numbers /-->
        <!-- wp:query-pagination-next /-->
    <!-- /wp:query-pagination -->
    
    <!-- wp:query-no-results -->
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">No posts found. Try adjusting your search or filters.</p>
        <!-- /wp:paragraph -->
    <!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->