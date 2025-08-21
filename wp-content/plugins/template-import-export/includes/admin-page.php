<div class="wrap">
    <h1><?php _e('Template Import/Export', 'template-import-export'); ?></h1>
    
    <div class="tie-admin-container">
        <!-- Export Section -->
        <div class="tie-section">
            <h2><?php _e('Export Templates', 'template-import-export'); ?></h2>
            <p><?php _e('Select which templates and posts to export for transfer to another site.', 'template-import-export'); ?></p>
            
            <form id="tie-export-form">
                <div class="tie-options">
                    <h3><?php _e('What to export:', 'template-import-export'); ?></h3>
                    <label>
                        <input type="checkbox" id="export_default" name="export_default" value="1" checked>
                        <?php _e('Default template settings', 'template-import-export'); ?>
                    </label>
                    <br>
                    <label>
                        <input type="checkbox" id="export_block_templates" name="export_block_templates" value="1" checked>
                        <?php _e('Block templates from theme', 'template-import-export'); ?>
                    </label>
                </div>
                
                <div class="tie-post-types">
                    <h3><?php _e('Post types to include:', 'template-import-export'); ?></h3>
                    <?php
                    $post_types = get_post_types(array('public' => true), 'objects');
                    foreach ($post_types as $post_type) {
                        if ($post_type->name === 'attachment') continue;
                        ?>
                        <label>
                            <input type="checkbox" name="post_types[]" value="<?php echo esc_attr($post_type->name); ?>" 
                                   <?php echo $post_type->name === 'organization' ? 'checked' : ''; ?>>
                            <?php echo esc_html($post_type->label); ?>
                        </label>
                        <br>
                        <?php
                    }
                    ?>
                </div>
                
                <div class="tie-templates">
                    <h3><?php _e('Templates to export:', 'template-import-export'); ?></h3>
                    <label class="tie-select-all">
                        <input type="checkbox" id="select-all-templates">
                        <strong><?php _e('Select All Templates', 'template-import-export'); ?></strong>
                    </label>
                    <div id="template-list" class="tie-template-list">
                        <p class="tie-loading"><?php _e('Loading templates...', 'template-import-export'); ?></p>
                    </div>
                </div>
                
                <p class="submit">
                    <button type="submit" class="button button-primary" id="export-button">
                        <?php _e('Export Selected Templates', 'template-import-export'); ?>
                    </button>
                </p>
            </form>
        </div>
        
        <!-- Import Section -->
        <div class="tie-section">
            <h2><?php _e('Import Templates', 'template-import-export'); ?></h2>
            <p><?php _e('Import template settings from an exported file. This will update template assignments for selected posts.', 'template-import-export'); ?></p>
            
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('tie_import', 'tie_import_nonce'); ?>
                <input type="hidden" name="tie_import" value="1">
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="import_file"><?php _e('Import File', 'template-import-export'); ?></label>
                        </th>
                        <td>
                            <input type="file" name="import_file" id="import_file" accept=".json" required>
                            <p class="description"><?php _e('Select a JSON file exported from another site.', 'template-import-export'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Import Options', 'template-import-export'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="overwrite_existing" value="1" checked>
                                <?php _e('Overwrite existing template assignments', 'template-import-export'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php _e('Import Templates', 'template-import-export'); ?>
                    </button>
                </p>
            </form>
        </div>
    </div>
</div>