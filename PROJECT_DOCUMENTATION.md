# Over the Edge WordPress Theme Development

## Project Overview

This project involved creating a custom WordPress child theme for the "Over the Edge" student newspaper website at UNBC (University of Northern British Columbia). The theme is built as a child theme of WordPress's Twenty Twenty-Five default theme and implements a modern design system specifically tailored for campus news and student organization content.

## Design Reference

The theme design and functionality are based on a complete design system provided in the **"New Implementation"** directory located at:
```
/Users/ahzs645/Downloads/New Implementation/
```

### Reference Files Used

The implementation directly references and adapts the following source files:

#### **HTML Structure Reference**
- **`example/index.html`** - Complete page layout and component structure
  - App bar with sticky navigation
  - Brand logo and navigation patterns
  - Hero sections with animated backgrounds
  - Card-based content layouts
  - Footer structure with brand and navigation

#### **CSS Design System Reference**
- **`example/styles.css`** - Complete design system implementation
  - CSS custom properties and color system
  - Typography scale (Inter + Source Serif 4)
  - Component styles (buttons, cards, navigation)
  - Light/dark theme variables
  - Animation and interaction patterns
  - Responsive breakpoints and mobile-first approach

#### **Card Grid System Reference**
- **`card_grid_styles.css`** - Query Loop and card styling system
  - WordPress block-specific card layouts
  - Animated hover effects with organic borders
  - Responsive grid systems (1-2-3-4 column layouts)
  - Compact mobile card arrangements
  - Integration with WordPress Query Loop blocks

#### **Dark Mode Toggle System Reference**
- **`dark_mode_toggle_block.json`** - Block configuration and attributes
- **`dark_mode_toggle_render.php`** - Server-side rendering template
- **`dark_mode_toggle_styles.css`** - Complete toggle component styles
- **`dark_mode_toggle_js.js`** - Block editor integration (React components)

#### **JavaScript Functionality Reference**
- **`theme_js.js`** - Complete theme JavaScript functionality
  - Dark mode theme switching and persistence
  - Mobile navigation and menu interactions
  - Search modal functionality
  - Card interaction behaviors
  - Animation initialization and intersection observers

#### **WordPress Integration Reference**
- **`wp_functions.php`** - WordPress-specific functionality
  - Block registration and custom block styles
  - Theme setup and support features
  - Color palette and editor configurations
  - AJAX handlers for theme switching

#### **Documentation Reference**
- **`wordpress_theme_readme.txt`** - Comprehensive usage documentation
  - Installation and setup instructions
  - Block usage examples and configuration
  - Custom block styles and variations
  - Performance and accessibility guidelines

## Implementation Approach

### 1. **Child Theme Architecture**
Instead of creating a standalone theme, we implemented a **child theme approach** using Twenty Twenty-Five as the parent. This provides:
- Automatic updates and security patches from WordPress core
- Compatibility with WordPress block editor features
- Future-proof foundation with modern block theme structure
- Inheritance of accessibility and performance optimizations

### 2. **Design System Translation**
The original design system was carefully translated from a generic web implementation to WordPress-specific functionality:

**Original Structure → WordPress Implementation:**
- Generic CSS classes → WordPress block styles and variations
- Static HTML components → Dynamic PHP templates and block patterns  
- Vanilla JavaScript → WordPress block editor integration
- Design tokens → CSS custom properties compatible with WordPress themes

### 3. **WordPress Block Integration**
The design system was specifically adapted to work with WordPress's block editor:

#### **Custom Block Styles Created:**
- **Query Loop Styles:**
  - `ote-card-grid` - Basic responsive grid layout
  - `ote-card-grid-animated` - With hover animations and organic borders
  - `ote-card-grid-compact` - Dense mobile-friendly layout

- **Group Block Styles:**
  - `ote-card` - Clean card design for individual posts
  - `ote-card-animated` - Interactive cards with hover effects
  - `ote-hero` - Hero section with gradient background
  - `ote-section` - Standard section spacing and layout

- **Button Styles:**
  - `ote-primary` - Brand-colored gradient button
  - `ote-outline` - Outlined button style  
  - `ote-ghost` - Transparent button style

#### **Custom WordPress Block Created:**
- **Dark Mode Toggle Block** (`ote/dark-mode-toggle`)
  - Multiple visual styles (button, icon-only, segmented, pill)
  - Size variations (small, medium, large)
  - System preference integration
  - Persistent theme storage via cookies and WordPress AJAX

### 4. **Plugin Integration Preparation**
The theme was designed to seamlessly integrate with planned campus management plugins:

- **OTE Article Manager** - Custom post types for news and articles
- **UNBC Campus Manager** - Events, clubs, and calendar management

The card system and block styles will automatically apply to content from these plugins without additional configuration.

## File Structure Created

```
twentytwentyfive-ote-child/
├── assets/
│   ├── css/
│   │   ├── design-system.css      # Complete design system (from styles.css)
│   │   ├── card-grid.css          # Query Loop styling (from card_grid_styles.css)
│   │   └── editor-style.css       # Gutenberg editor integration
│   └── js/
│       ├── theme.js               # Main theme JavaScript (from theme_js.js)
│       └── dark-mode-toggle.js    # Dark mode functionality
├── blocks/
│   ├── dark-mode-toggle/
│   │   ├── block.json             # Block definition (from dark_mode_toggle_block.json)
│   │   ├── render.php             # Server rendering (from dark_mode_toggle_render.php)
│   │   ├── index.js               # Editor component (adapted from dark_mode_toggle_js.js)
│   │   └── style.css              # Block styles (from dark_mode_toggle_styles.css)
│   └── dark-mode-toggle-simple.php # PHP fallback block
├── functions.php                  # Theme setup (adapted from wp_functions.php)
├── style.css                      # Theme info and main imports
├── header.php                     # Custom header (from index.html structure)
├── footer.php                     # Custom footer (from index.html structure)
├── index.php                      # Main template with card layout
└── README.md                      # WordPress-specific documentation
```

## Technical Implementation Details

### **Color System**
Implemented a complete light/dark theme system using CSS custom properties:

```css
/* Light Theme */
--brand: #2d5f3f;           /* UNBC/OTE green primary */
--brand-2: #4a7c59;         /* Secondary green */
--accent-blue: #0ea5e9;     /* Accent color */
--text: #1e293b;            /* Primary text */
--bg: #ffffff;              /* Background */

/* Dark Theme */  
--brand: #4a7c59;           /* Adjusted for dark backgrounds */
--brand-2: #86a58e;         /* Lighter secondary */
--text: #e6eaf2;            /* Light text */
--bg: #0f172a;              /* Dark background */
```

### **Typography Scale**
- **UI Font:** Inter (Google Fonts) - Clean, readable sans-serif
- **Headline Font:** Source Serif 4 (Google Fonts) - Editorial serif for headlines
- **Responsive sizing:** Using clamp() for fluid typography across devices

### **Animation System**
- **Organic Borders:** SVG-based hand-drawn style borders on card hover
- **Smooth Transitions:** Consistent timing functions and easing
- **Accessibility:** Respects `prefers-reduced-motion` user preference
- **Performance:** Hardware-accelerated transforms and opacity changes

### **Responsive Design**
- **Mobile-First:** All styles written mobile-first with progressive enhancement
- **Breakpoints:** 640px (tablet), 1000px (desktop) 
- **Touch-Friendly:** 44px minimum touch targets, bottom navigation on mobile
- **Fluid Layouts:** CSS Grid and Flexbox with responsive patterns

## Current Status

### ✅ **Completed Features**
- [x] Complete child theme structure and WordPress integration
- [x] Full design system implementation with light/dark themes
- [x] Custom dark mode toggle block with multiple styles
- [x] Animated card layouts for Query Loop blocks
- [x] Custom block styles and variations
- [x] Responsive navigation with mobile bottom nav
- [x] WordPress block editor integration
- [x] Accessibility features and keyboard navigation
- [x] Performance optimizations and reduced motion support

### ⏳ **Pending Integration**
- [ ] OTE Article Manager plugin integration
- [ ] UNBC Campus Manager plugin integration  
- [ ] Content migration and testing with real campus data
- [ ] SEO optimization and structured data implementation
- [ ] Performance testing and optimization

### 🔧 **Recent Fixes Applied**
- Fixed CSS import path issues causing 404 errors
- Resolved grey background in block editor
- Added PHP fallback for dark mode toggle block
- Improved block registration and editor asset loading
- Added debug utilities for troubleshooting

## Usage Instructions

### **For Content Editors:**
1. Use **Query Loop** blocks with "OTE Card Grid" styles for article listings
2. Add **Dark Mode Toggle** blocks in headers/footers for theme switching
3. Apply "OTE Card" styles to Group blocks for individual content cards
4. Use "OTE Hero" style for section headers and featured content

### **For Developers:**
1. All design system variables are defined in `assets/css/design-system.css`
2. Block styles can be extended by registering new variations in `functions.php`
3. The card animation system can be customized in `assets/css/card-grid.css`
4. JavaScript functionality is modular and can be extended in `assets/js/`

## References and Attribution

This implementation is a direct adaptation of the design system provided in the "New Implementation" sample files. All design decisions, color choices, typography, animations, and component behaviors are based on the reference implementation to ensure consistency with the original vision for the Over the Edge student newspaper website.

The WordPress integration maintains the visual and functional integrity of the original design while adapting it to work seamlessly with WordPress's block editor, theme system, and content management capabilities.