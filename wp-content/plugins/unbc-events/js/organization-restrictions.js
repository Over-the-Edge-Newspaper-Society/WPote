jQuery(document).ready(function($) {
    // Check if user is organization manager
    var userIsOrgManager = $('body').hasClass('organization-manager-restrictions');
    
    // If we can't detect via body class, check if there are restricted field indicators
    var hasRestrictedFields = $('.description em').length > 0;
    
    if (hasRestrictedFields) {
        // Disable title field and show notice
        var titleField = $('#title');
        if (titleField.length) {
            titleField.prop('readonly', true);
            titleField.css({
                'background-color': '#f0f0f0',
                'color': '#666'
            });
            titleField.after('<p class="description"><em>Organization name can only be changed by administrators.</em></p>');
        }
        
        // Disable slug/permalink editing
        var permalinkField = $('#post_name');
        if (permalinkField.length) {
            permalinkField.prop('readonly', true);
            permalinkField.css({
                'background-color': '#f0f0f0',
                'color': '#666'
            });
        }
        
        // Hide permalink edit button
        $('.edit-permalink').hide();
        
        // Disable publish status changes (keep it as current status)
        var statusSelect = $('#post_status');
        if (statusSelect.length) {
            statusSelect.prop('disabled', true);
            statusSelect.css({
                'background-color': '#f0f0f0',
                'color': '#666'
            });
        }
        
        // Hide visibility options for organization managers
        $('.misc-pub-visibility').find('a').hide();
        
        // Add general notice at top of page
        $('#post-body-content').prepend(
            '<div class="notice notice-info">' +
            '<p><strong>Organization Manager:</strong> You can edit most content fields, contact information, and social media links. ' +
            'Administrative fields like organization name, dates, and department status can only be changed by administrators.</p>' +
            '</div>'
        );
    }
});