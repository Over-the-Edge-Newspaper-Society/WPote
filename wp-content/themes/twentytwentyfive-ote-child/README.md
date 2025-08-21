# Over the Edge - Twenty Twenty-Five Child Theme

A modern WordPress child theme based on Twenty Twenty-Five, designed specifically for the "Over the Edge" student newspaper. Features a comprehensive design system, dark mode toggle functionality, and animated card layouts perfect for news and campus content.

## Features

- **Child Theme of Twenty Twenty-Five**: Built on WordPress's latest default theme
- **Over the Edge Design System**: Complete design system with green branding and modern typography
- **Dark Mode Toggle Block**: Custom WordPress block with multiple styles (button, pill, segmented)
- **Animated Card Layouts**: Query Loop blocks with hover animations and organic borders
- **Responsive Design**: Mobile-first approach with touch-friendly navigation
- **Block Style Variations**: Custom block styles for hero sections, cards, and buttons
- **Plugin Integration Ready**: Designed to work with OTE Article Manager and UNBC Campus Manager plugins

## Installation

1. **Prerequisites**
   - WordPress 6.4 or higher
   - Twenty Twenty-Five parent theme installed and activated
   - PHP 7.4 or higher

2. **Upload Theme**
   - Upload the theme folder to `/wp-content/themes/`
   - Or upload as ZIP file through WordPress admin

3. **Activate Theme**
   - Go to Appearance > Themes
   - Activate "Over the Edge - Twenty Twenty-Five Child"

## Theme Structure

```
twentytwentyfive-ote-child/
├── assets/
│   ├── css/
│   │   ├── design-system.css     # Core design system
│   │   ├── card-grid.css         # Query Loop card styles
│   │   └── editor-style.css      # Gutenberg editor styles
│   └── js/
│       ├── theme.js              # Main theme JavaScript
│       └── dark-mode-toggle.js   # Dark mode functionality
├── blocks/
│   └── dark-mode-toggle/
│       ├── block.json            # Block configuration
│       ├── render.php            # Server-side rendering
│       ├── index.js              # Editor component
│       └── style.css             # Block styles
├── functions.php                 # Theme functions
├── style.css                     # Theme info and imports
├── header.php                    # Custom header template
├── footer.php                    # Custom footer template
├── index.php                     # Main template with card layout
└── README.md                     # This file
```

## Using the Dark Mode Toggle Block

The theme includes a custom "Dark Mode Toggle" block with multiple style options:

### Block Styles

1. **Button Style** - Standard button with icon and optional label
2. **Icon Only** - Compact icon-only button
3. **Segmented Control** - Radio-style toggle with Light/Dark/System options
4. **Pill Toggle** - Sliding switch design

### Block Attributes

- **Style**: Choose button, icon-only, segmented, or pill
- **Size**: Small, medium, or large
- **Show Labels**: Display text labels with icons
- **Include System Option**: Add system preference option (segmented style)
- **Alignment**: Left, center, or right alignment

### Usage in Editor

1. Add the "Dark Mode Toggle" block from the "Over the Edge Blocks" category
2. Configure options in the block sidebar
3. The block will automatically handle theme switching and state persistence

## Using Query Loop Card Styles

The theme provides custom block styles for WordPress Query Loop blocks:

### Query Loop Styles

- **OTE Card Grid** - Basic responsive grid layout
- **OTE Card Grid with Animation** - Adds hover animations
- **OTE Card Grid Compact** - Denser layout for mobile

### Group/Post Styles

- **OTE Card** - Clean card design
- **OTE Card with Hover Animation** - Interactive cards with animations
- **OTE Section** - Standard section spacing

### Example Usage

1. Add a Query Loop block
2. Select "OTE Card Grid" or "OTE Card Grid with Animation" style in block sidebar
3. Style individual post items with "OTE Card" or "OTE Card with Hover Animation"
4. Customize inner blocks (featured image, title, excerpt, etc.)

## Button Styles

The theme includes custom button styles:

- **OTE Primary** - Brand-colored button with gradient
- **OTE Outline** - Outlined button style
- **OTE Ghost** - Transparent button style

## Color Palette

The theme uses a carefully crafted color system:

### Light Mode
- **Brand Green**: `#2d5f3f`
- **Brand Green 2**: `#4a7c59`
- **Accent Blue**: `#0ea5e9`
- **Text**: `#1e293b`
- **Text Secondary**: `#64748b`
- **Background**: `#ffffff`
- **Surface**: `#f8fafc`

### Dark Mode
- **Brand Green**: `#4a7c59`
- **Brand Green 2**: `#86a58e`
- **Accent Blue**: `#6FA3FF`
- **Text**: `#e6eaf2`
- **Text Secondary**: `#b9c0cf`
- **Background**: `#0f172a`
- **Surface**: `#1e293b`

## Typography

- **UI Font**: Inter (Google Fonts)
- **Serif Font**: Source Serif 4 (Google Fonts)
- **Headlines**: Use serif font family
- **Body Text**: Use UI font family

## Responsive Breakpoints

- **Mobile**: < 640px
- **Tablet**: 640px - 1000px
- **Desktop**: > 1000px

## Accessibility

The theme follows WordPress accessibility guidelines:

- Proper ARIA labels and roles
- Keyboard navigation support
- Focus management
- Screen reader compatibility
- Respects `prefers-reduced-motion` for animations

## Plugin Integration

The theme is designed to work seamlessly with:

- **OTE Article Manager**: Custom post types and fields
- **UNBC Campus Manager**: Event and club management
- **Block Editor**: Full Gutenberg compatibility
- **Custom Post Types**: Flexible content management

## Customization

### CSS Custom Properties

The theme uses CSS custom properties (variables) for easy customization:

```css
:root {
  --brand: #2d5f3f;
  --brand-2: #4a7c59;
  --font-ui: "Inter", sans-serif;
  --font-serif: "Source Serif 4", serif;
  /* ... and many more */
}
```

### Adding Custom Block Styles

Register new block styles in `functions.php`:

```php
register_block_style('core/group', array(
    'name'  => 'custom-style',
    'label' => __('Custom Style', 'ote-child-theme'),
));
```

### JavaScript Hooks

The theme dispatches custom events:

- `themechange`: Fired when theme is toggled
- Theme functions available at `window.oteTheme`
- Utilities available at `window.oteUtils`

## Performance

- Optimized CSS with logical grouping
- Lazy loading for animations
- Efficient JavaScript with event delegation
- Minimal dependencies

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Progressive enhancement for older browsers
- Graceful degradation for JavaScript features

## Development

### Local Development

1. Set up local WordPress environment
2. Install parent theme (Twenty Twenty-Five)
3. Clone/download child theme to `wp-content/themes/`
4. Activate child theme

### File Watching

For development, consider using a build process to watch CSS/JS files for changes.

### Testing

Test the theme with:
- Different content types (posts, pages, custom post types)
- Various devices and screen sizes
- Light and dark mode switching
- Keyboard navigation
- Screen readers

## Support

For support with this theme:

1. Check WordPress documentation for block editor features
2. Review theme files for customization examples
3. Test with default WordPress content first
4. Ensure parent theme is properly installed

## License

This theme inherits the GPL v2+ license from WordPress and the parent theme.

## Changelog

### Version 1.0.0
- Initial release
- Child theme of Twenty Twenty-Five
- Over the Edge design system implementation
- Dark mode toggle block
- Animated card layouts
- Responsive design system
- Custom block styles
- Plugin integration ready