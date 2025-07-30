# Twenty Twenty-Five Newspaper

A modern newspaper-style child theme for WordPress built on Twenty Twenty-Five with shadcn/ui design principles.

## Features

- 🎨 **shadcn/ui inspired design system** - Modern, clean components with consistent styling
- 🌙 **Built-in dark mode** - Seamless light/dark theme switching with system preference detection
- 📰 **Newspaper-optimized layouts** - Card-based post displays perfect for news content
- 🧱 **Full Gutenberg compatibility** - All WordPress blocks work perfectly with custom styling
- 📱 **Responsive design** - Looks great on all devices
- ♿ **Accessibility focused** - Proper ARIA labels, keyboard navigation, and semantic HTML
- ⚡ **Performance optimized** - Minimal CSS and JavaScript footprint

## Installation

1. Ensure Twenty Twenty-Five parent theme is installed and activated
2. Upload this child theme to `/wp-content/themes/`
3. Activate "Twenty Twenty-Five Newspaper" from Appearance > Themes

## Customization

### CSS Variables

The theme uses CSS custom properties (variables) following shadcn/ui conventions:

```css
:root {
  --background: 0 0% 100%;
  --foreground: 222.2 84% 4.9%;
  --primary: 222.2 47.4% 11.2%;
  --secondary: 210 40% 96%;
  --muted: 210 40% 96%;
  --border: 214.3 31.8% 91.4%;
  --radius: 0.5rem;
}
```

### Dark Mode

Dark mode is automatically applied based on user's system preference, with a toggle button for manual control. The state is persisted in localStorage.

### Newspaper Features

- **Featured Articles**: Mark posts as featured in the post editor
- **Breaking News**: Special styling for urgent news items
- **Optimized Typography**: Newspaper-friendly fonts and spacing
- **Card Layouts**: Modern card-based post displays

## Development

### File Structure

```
twentytwentyfive-newspaper/
├── style.css              # Main stylesheet with theme header
├── functions.php          # Theme functionality and hooks
├── assets/
│   ├── js/
│   │   └── dark-mode.js   # Dark mode toggle functionality
│   └── css/
│       └── editor-style.css # Gutenberg editor styling
└── README.md
```

### Browser Support

- Chrome/Edge 88+
- Firefox 78+
- Safari 14+

## License

GPL v2 or later - same as WordPress

## Contributing

This theme follows WordPress coding standards and shadcn/ui design principles. Feel free to submit issues and enhancement requests.