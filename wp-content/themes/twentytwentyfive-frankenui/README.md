# Twenty Twenty-Five Franken UI Child Theme

A modern child theme of Twenty Twenty-Five that integrates Franken UI components for a beautiful student newspaper website.

## Features

- ✅ **Full Block Editor Support** - Complete Gutenberg/FSE compatibility
- 🎨 **Franken UI Integration** - Modern shadcn/ui-inspired components
- 🌓 **Dark/Light Mode** - Automatic theme switching with toggle
- 📱 **Responsive Design** - Mobile-first approach
- 🎯 **Newspaper-Focused** - Custom patterns and styles for news content
- ⚡ **Performance Optimized** - CDN delivery and efficient loading

## Installation

1. Ensure Twenty Twenty-Five theme is installed and available
2. Upload this child theme to `/wp-content/themes/`
3. Activate "Twenty Twenty-Five Franken UI" in WordPress admin
4. Navigate to Appearance → Editor to start building your site

## What's Included

### Core Files
- `style.css` - Main stylesheet with Franken UI variables and overrides
- `functions.php` - Theme setup, asset enqueuing, and functionality
- `theme.json` - Block editor configuration with Franken UI colors
- `README.md` - This documentation

### Assets
- `assets/js/theme.js` - Theme JavaScript including dark mode toggle
- `assets/css/editor-style.css` - Block editor styling

## Theme Features

### Block Editor Integration
- Custom color palette matching Franken UI
- Enhanced button styles (Primary, Secondary)
- Card-style group blocks
- Newspaper-specific block patterns
- Custom font loading (Inter)

### JavaScript Features
- Dark/light mode toggle with localStorage persistence
- Smooth scrolling for anchor links
- Card hover effects
- Reading progress bar for articles
- Auto-hiding navigation on mobile scroll
- UIkit component initialization

### Custom Block Styles
- **Card Group**: Apply card styling to group blocks
- **Primary Button**: Franken UI primary button style
- **Secondary Button**: Franken UI secondary button style

### Block Patterns
- **Featured Article**: Hero-style article layout
- More patterns can be added in `functions.php`

## Customization

### Colors
Edit the CSS variables in `style.css` to customize colors:

```css
:root {
  --primary: 0 0% 9%;
  --secondary: 0 0% 96%;
  --background: 0 0% 100%;
  /* etc... */
}
```

### Adding Custom Patterns
Register new block patterns in `functions.php`:

```php
register_block_pattern(
    'twentytwentyfive-frankenui/pattern-name',
    array(
        'title' => 'Pattern Title',
        'categories' => array('newspaper'),
        'content' => '<!-- wp:group -->...',
    )
);
```

### Dark Mode
The theme automatically detects user preference and provides a toggle button. Dark mode settings are stored in localStorage.

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Dependencies

- WordPress 6.4+
- Twenty Twenty-Five parent theme
- Franken UI (loaded via CDN)

## Support

This theme inherits all the block editing capabilities of Twenty Twenty-Five while adding modern Franken UI styling. You can use the full site editor to customize:

- Headers and footers
- Page templates
- Post templates
- Navigation menus
- Global styles

## License

GPL v2 or later, same as WordPress