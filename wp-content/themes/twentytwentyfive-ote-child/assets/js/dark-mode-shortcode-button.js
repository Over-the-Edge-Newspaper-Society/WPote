/**
 * TinyMCE Dark Mode Toggle Button Plugin
 * Placeholder file to prevent 404 errors
 */
(function() {
    tinymce.PluginManager.add('ote_dark_mode_toggle', function(editor, url) {
        // Simple dark mode toggle button for TinyMCE
        editor.addButton('ote_dark_mode_toggle', {
            text: '🌓',
            title: 'Toggle Dark Mode',
            onclick: function() {
                // Insert dark mode shortcode
                editor.insertContent('[dark_mode_toggle]');
            }
        });
    });
})();