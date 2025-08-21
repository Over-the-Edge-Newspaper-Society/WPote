jQuery(document).ready(function($) {
    // Load template list
    loadTemplates();
    
    // Select all templates checkbox
    $('#select-all-templates').on('change', function() {
        $('.template-checkbox').prop('checked', $(this).is(':checked'));
        updateSelectAllState();
    });
    
    // Individual template checkbox change
    $(document).on('change', '.template-checkbox', function() {
        updateSelectAllState();
    });
    
    // Export form submission
    $('#tie-export-form').on('submit', function(e) {
        e.preventDefault();
        
        var selectedTemplates = [];
        $('.template-checkbox:checked').each(function() {
            selectedTemplates.push($(this).val());
        });
        
        if (selectedTemplates.length === 0 && !$('#export_block_templates').is(':checked')) {
            alert('Please select at least one template to export or enable block templates export.');
            return;
        }
        
        var postTypes = [];
        $('input[name="post_types[]"]:checked').each(function() {
            postTypes.push($(this).val());
        });
        
        $('#export-button').prop('disabled', true).text('Exporting...');
        
        $.ajax({
            url: tie_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'tie_export_templates',
                nonce: tie_ajax.nonce,
                selected_templates: selectedTemplates,
                export_default: $('#export_default').is(':checked') ? 1 : 0,
                export_block_templates: $('#export_block_templates').is(':checked') ? 1 : 0,
                post_types: postTypes
            },
            success: function(response) {
                if (response.success) {
                    downloadJSON(response.data.content, 'templates-export-' + getCurrentDateTime() + '.json');
                    
                    var message = 'Export completed! ';
                    if (response.data.post_count > 0) {
                        message += response.data.post_count + ' post assignments exported. ';
                    }
                    if (response.data.template_count > 0) {
                        message += response.data.template_count + ' block templates exported.';
                    }
                    alert(message);
                } else {
                    alert('Export failed: ' + response.data);
                }
                $('#export-button').prop('disabled', false).text('Export Selected Templates');
            },
            error: function() {
                alert('Export failed. Please try again.');
                $('#export-button').prop('disabled', false).text('Export Selected Templates');
            }
        });
    });
    
    function loadTemplates() {
        $.ajax({
            url: tie_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'tie_get_template_list',
                nonce: tie_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    var html = '';
                    $.each(response.data, function(key, template) {
                        html += '<div class="tie-template-item">';
                        html += '<label>';
                        html += '<input type="checkbox" class="template-checkbox" value="' + key + '">';
                        html += ' <strong>' + template.name + '</strong>';
                        
                        if (template.usage_count > 0) {
                            html += ' <span class="tie-usage">(' + template.usage_count + ' ' + (template.usage_count === 1 ? 'post' : 'posts') + ')</span>';
                            
                            // Show first 3 posts using this template
                            if (template.used_by.length > 0) {
                                var preview = template.used_by.slice(0, 3).map(function(post) {
                                    return post.title;
                                }).join(', ');
                                
                                if (template.used_by.length > 3) {
                                    preview += ' and ' + (template.used_by.length - 3) + ' more';
                                }
                                
                                html += '<br><small class="tie-used-by">Used by: ' + preview + '</small>';
                            }
                        } else {
                            html += ' <span class="tie-not-used">(Not used)</span>';
                        }
                        
                        html += '</label>';
                        html += '</div>';
                    });
                    
                    $('#template-list').html(html);
                } else {
                    $('#template-list').html('<p class="error">Failed to load templates.</p>');
                }
            },
            error: function() {
                $('#template-list').html('<p class="error">Failed to load templates.</p>');
            }
        });
    }
    
    function updateSelectAllState() {
        var total = $('.template-checkbox').length;
        var checked = $('.template-checkbox:checked').length;
        
        if (total === 0) return;
        
        if (checked === 0) {
            $('#select-all-templates').prop('checked', false).prop('indeterminate', false);
        } else if (checked === total) {
            $('#select-all-templates').prop('checked', true).prop('indeterminate', false);
        } else {
            $('#select-all-templates').prop('checked', false).prop('indeterminate', true);
        }
    }
    
    function downloadJSON(data, filename) {
        var json = JSON.stringify(data, null, 2);
        var blob = new Blob([json], {type: 'application/json'});
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
    
    function getCurrentDateTime() {
        var now = new Date();
        var year = now.getFullYear();
        var month = String(now.getMonth() + 1).padStart(2, '0');
        var day = String(now.getDate()).padStart(2, '0');
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        var seconds = String(now.getSeconds()).padStart(2, '0');
        
        return year + '-' + month + '-' + day + 'T' + hours + '-' + minutes + '-' + seconds;
    }
});