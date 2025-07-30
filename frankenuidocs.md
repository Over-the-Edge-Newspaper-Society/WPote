Installation
Learn how to install and integrate Franken UI in your projects.

Franken UI is an HTML-first UI component library built on UIkit 3 and extended with LitElement, inspired by shadcn/ui.

CDN
Vite
Installation via CDN
Perfect for beginners, the simplest installation can be done using CDN.

<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0-next.16/dist/css/core.min.css"
/>
Optionally, you can include a pre-built (~91.45 kB gzipped) Tailwind CSS utility classes to complement with Franken UI. These utility classes are pre-extracted from Tailwind CSS. If you find this too large, you can always switch to a proper build process.

<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0-next.16/dist/css/utilities.min.css"
/>
The following utility classes are available:

Layout
Display
Visibility
Overflow
Position
Top / Right / Bottom / Left
Flexbox and Grid
Flex Basis
Flex Direction
Flex Wrap
Flex
Flex Grow
Flex Shrink
Order
Grid Template Columns
Grid Column Start / End
Grid Template Rows
Grid Row Start / End
Grid Auto Flow
Grid Auto Columns
Grid Auto Rows
Gap
Justify Content
Justify Items
Justify Self
Align Content
Align Items
Align Self
Place Content
Place Items
Place Self
Spacing
Padding
Margin
Space Between
Sizing
Width
Min-Width
Max-Width
Height
Min-Height
Max-Height
Size
Typography
Font Size
Font Style
Font Weight
Letter Spacing
Line Height
Text Align
Text Transform
Borders
Border Radius
Border Width
Border Style
Once you're done, you may now proceed adding JavaScript.


JavaScript
Table of contents
Installation via CDN
Installation via NPM
UIkit and reactive JavaScript frameworks
Component usage
Component configuration
Instance
Precedence
Globally
Programmatic use
UIkit initialization
Once you have installed Franken UI, we can now include the JavaScript to control the behavior of our components.

Installation via CDN
You can include the JavaScript files on your page by adding them to the <head> section.

<script
  src="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0-next.16/dist/js/core.iife.js"
  type="module"
></script>
<script
  src="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0-next.16/dist/js/icon.iife.js"
  type="module"
></script>
Installation via NPM
You can import the JavaScript from franken-ui, which you installed earlier, into your app.js file.

import "franken-ui/js/core.iife";
import "franken-ui/js/icon.iife";
UIkit and reactive JavaScript frameworks
UIkit is listening for DOM manipulations and will automatically initialize, connect and disconnect components as they are inserted or removed from the DOM. That way it can easily be used with JavaScript frameworks like Vue.js and React.

Component usage
You can use UIkit components by adding uk-* or data-uk-* attributes to your HTML elements without writing a single line of JavaScript. This is UIkit’s best practice of using its components and should always be considered first.

<div uk-sticky="offset: 50;"></div>

<div data-uk-sticky="offset: 50;"></div>
Note React will work with data-uk-* prefixes only.

You can also initialize components via JavaScript and apply them to elements in your document.

var sticky = UIkit.sticky("#sticky", {
  offset: 50,
});
You can retrieve an already initialized component by passing a selector or an element as a first Argument to the component function.

var sticky = UIkit.sticky("#sticky");
Omitting the second parameter will not re-initialize the component but serve as a getter function.

Component configuration
Each component comes with a set of configuration options that let you customize their behavior. You can set the options on a per-instance level or globally.

Instance
Options can be set as shown in the following examples.

With the key: value; format:

<div uk-sticky="start: 100; offset: 50;"></div>
In valid JSON format:

<div uk-sticky='{"start": 100, "offset": 50}'></div>
As single attributes:

<div uk-sticky start="100" offset="50"></div>
Or as single attributes prefixed with data-:

<div uk-sticky data-start="100" data-offset="50"></div>
You can also pass options to the component constructor programmatically.

// Passing an options object.
UIkit.sticky(".sticky", {
  offset: 50,
  top: 100,
});

// If the component supports Primary options.
UIkit.drop("#drop", "top-left");
Precedence
Options passed via the component attribute will have the highest precedence, followed by single attributes and then JavaScript.

<div uk-sticky="offset: 50;" offset="100"></div>

<!-- The offset will be 50 -->
Globally
Component options can be changed globally by extending a component. It will affect newly created instances only.

UIkit.mixin(
  {
    data: {
      offset: 50,
      top: 100,
    },
  },
  "sticky",
);
Omitting the second parameter, will apply the custom behavior to every UIkit instance created afterwards.

Programmatic use
Programmatically, components may be initialized with the element, options arguments format in JavaScript. The element argument may be any Node, selector or jQuery object. You’ll receive the initialized component as a return value. Functional Components (e.g. Notification) should omit the element parameter.

// Passing a selector and an options object.
var sticky = UIkit.sticky(".sticky", {
  offset: 50,
  top: 100,
});

// Functional components should omit the 'element' argument.
var notifications = UIkit.notification("MyMessage", "destructive");
Note The options names must be in their camel-cased representation, e.g. show-on-up becomes showOnUp.

After initialization, you can get your component by calling the same initialization function, omitting the options parameter.

// Sticky is now the prior initialised components
var sticky = UIkit.sticky(".sticky");
Note Using UIkit[componentName](selector) with CSS selectors will always return the first occurrence only! If you need to access all instances do query the elements first. Then apply the getter to each element separately - UIkit[componentName](element).

Initializing your components programmatically gives you the possibility to invoke their functions directly.

UIkit.offcanvas("#offcanvas").toggle();
Any component functions and variables prefixed with an underscore are considered as part of the internal API, which may change at any given time.

Each component triggers DOM events that you can react to. For example when a Modal is shown or a Scrollspy element becomes visible.

UIkit.util.on("#offcanvas", "show", function () {
  // do something
});
The component’s documentation page lists its events.

Note Components often trigger events with the same name (e.g. ‘show’). Usually events bubble through the DOM. Check the event target, to ensure the event was triggered by the desired component.

Sometimes, components like Grid or Tab are hidden in the markup. This may happen when used in combination with the Switcher, Modal or Dropdown. Once they become visible, they need to adjust or fix their height and other dimensions.

UIkit offers several ways of updating a component. Omitting the type parameter will trigger an update event.

// Calls the update hook on components registered on the element itself, its parents and children.
UIkit.update((element = document.body), (type = "update"));

// Updates the component itself.
component.$emit((type = "update"));
If you need to make sure a component is properly destroyed, for example upon removal from the DOM, you can call its $destroy function.

// Destroys the component. For example unbind its event listeners.
component.$destroy();

// Also destroys the component, but also removes the element from the DOM.
component.$destroy(true);
UIkit initialization
You might need to execute code after UIkit is loaded, but before it initializes its components on the page.

This hook allows you to register custom components or component mixins.

You can hook into this step in the lifecycle by listening for the uikit:init event UIkit triggers on the document.

document.addEventListener("uikit:init", () => {
  // do something
});

Theming
Table of contents
List of variables
Adding new colors
Custom palette
Setting the default palette
Adding to theme switcher
Franken UI, just like shadcn/ui use a simple background and foreground convention for colors. The background variable is used for the background color of the component and the foreground variable is used for the text color.

The background suffix is omitted when the variable is used for the background color of the component.

Given the following CSS variables:

--primary: 0 0% 9%;
--primary-foreground: 0 0% 98%;
The background color of the following component will be hsl(var(--primary)) and the foreground color will be hsl(var(--primary-foreground)).

<div class="bg-primary text-primary-foreground">Hello</div>
CSS variables must be defined without color space function. See the Tailwind CSS documentation for more information.

List of variables
Here’s the list of variables available for customization:

1. For default backgrounds

--background: 0 0% 100%;
--foreground: 0 0% 4%;
2. For muted backgrounds

--muted: 0 0% 96%;
--muted-foreground: 0 0% 45%;
3. Background color for cards

--card: 0 0% 100%;
--card-foreground: 0 0% 4%;
4. Background color for popovers

--popover: 0 0% 100%;
--popover-foreground: 0 0% 4%;
5. For border color

--border: 0 0% 90%;
6. Border color for inputs

--input: 0 0% 90%;
7. For primary colors

--primary: 0 0% 9%;
--primary-foreground: 0 0% 98%;
8. For secondary colors

--secondary: 0 0% 96%;
--secondary-foreground: 0 0% 9%;
9. For accents such as hover effects

--accent: 0 0% 96%;
--accent-foreground: 0 0% 9%;
10. For destructive actions

--destructive: 357 100% 45%;
--destructive-foreground: 0 0% 100%;
11. For focus ring

--ring: 0 0% 63%;
Adding new colors
To add new colors, simply add them to your main CSS file.

:root {
  --warning: 38 92% 50%;
  --warning-foreground: 48 96% 89%;
}

.dark {
  --warning: 48 96% 89%;
  --warning-foreground: 38 92% 50%;
}

@theme {
  --color-warning: hsl(var(--warning));
  --color-warning-foreground: hsl(var(--warning-foreground));
}
You can now use the warning utility class in your components.

<div class="bg-warning text-warning-foreground"></div>
Custom palette
To create your own palette, follow these steps:

1. Start by going to https://ui.shadcn.com/colors. Set the output format to HSL and pick your desired color. Use that color to assign values to the --primary, --primary-foreground, and --ring tokens. These tokens represent your main theme color, its contrasting foreground color, and the ring color for focus states.

2. Use the snippet below as your starting point and replace the * with your theme name (e.g. indigo, cyan, fuchsia, etc.). You only need to update the values for --primary, --primary-foreground, and --ring for both light and dark modes, but you’re free to customize everything else if needed.

'.uk-theme-*': {
  '--background': '0 0% 100%',
  '--foreground': '0 0% 4%',
  '--card': '0 0% 100%',
  '--card-foreground': '0 0% 4%',
  '--popover': '0 0% 100%',
  '--popover-foreground': '0 0% 4%',
  '--primary': '0 0% 9%',
  '--primary-foreground': '0 0% 98%',
  '--secondary': '0 0% 96%',
  '--secondary-foreground': '0 0% 9%',
  '--muted': '0 0% 96%',
  '--muted-foreground': '0 0% 45%',
  '--accent': '0 0% 96%',
  '--accent-foreground': '0 0% 9%',
  '--destructive': '357 100% 45%',
  '--destructive-foreground': '0 0% 100%',
  '--border': '0 0% 90%',
  '--input': '0 0% 90%',
  '--ring': '0 0% 63%'
},
'.dark.uk-theme-*': {
  '--background': '0 0% 4%',
  '--foreground': '0 0% 98%',
  '--card': '0 0% 9%',
  '--card-foreground': '0 0% 98%',
  '--popover': '0 0% 15%',
  '--popover-foreground': '0 0% 98%',
  '--primary': '0 0% 90%',
  '--primary-foreground': '0 0% 9%',
  '--secondary': '0 0% 15%',
  '--secondary-foreground': '0 0% 98%',
  '--muted': '0 0% 15%',
  '--muted-foreground': '0 0% 63%',
  '--accent': '0 0% 25%',
  '--accent-foreground': '0 0% 98%',
  '--destructive': '357 100% 45%',
  '--destructive-foreground': '0 0% 100%',
  '--border': '0 0% 100%',
  '--input': '0 0% 100%',
  '--ring': '0 0% 45%'
},
Note If you’re using the legacy color generators from version 2.0, make sure to include the following additional keys in dark mode for compatibility:

.dark.uk-theme-* {
  '--destructive-alpha': '1',
  '--border-alpha': '1',
  '--input-alpha': '1'
}
3. Finally, configure your vite.config.js to add the custom palette. You will do this inside the customPalette option.

import franken from "franken-ui/plugin-vite";
import tailwindcss from "@tailwindcss/vite";
import { defineConfig } from "vite";

export default defineConfig({
  plugins: [
    franken({
      customPalette: {
        ".uk-theme-emerald": {
          "--background": "0 0% 100%",
          "--foreground": "0 0% 4%",
          "--card": "0 0% 100%",
          "--card-foreground": "0 0% 4%",
          "--popover": "0 0% 100%",
          "--popover-foreground": "0 0% 4%",
          "--primary": "161.4 93.5% 30.4%",
          "--primary-foreground": "151.8 81% 95.9%",
          "--secondary": "0 0% 96%",
          "--secondary-foreground": "0 0% 9%",
          "--muted": "0 0% 96%",
          "--muted-foreground": "0 0% 45%",
          "--accent": "0 0% 96%",
          "--accent-foreground": "0 0% 9%",
          "--destructive": "357 100% 45%",
          "--destructive-foreground": "0 0% 100%",
          "--border": "0 0% 90%",
          "--input": "0 0% 90%",
          "--ring": "158.1 64.4% 51.6%",
        },
        ".dark.uk-theme-emerald": {
          "--background": "0 0% 4%",
          "--foreground": "0 0% 98%",
          "--card": "0 0% 9%",
          "--card-foreground": "0 0% 98%",
          "--popover": "0 0% 15%",
          "--popover-foreground": "0 0% 98%",
          "--primary": "160.1 84.1% 39.4%",
          "--primary-foreground": "151.8 81% 95.9%",
          "--secondary": "0 0% 15%",
          "--secondary-foreground": "0 0% 98%",
          "--muted": "0 0% 15%",
          "--muted-foreground": "0 0% 63%",
          "--accent": "0 0% 25%",
          "--accent-foreground": "0 0% 98%",
          "--destructive": "357 100% 45%",
          "--destructive-foreground": "0 0% 100%",
          "--border": "0 0% 100%",
          "--input": "0 0% 100%",
          "--ring": "143.8 61.2% 20.2%",
        },
      },
    }),
    tailwindcss(),
  ],
});
Setting the default palette
To set your newly added palette as the default, simply update the script in your <head> tag to reference the new theme name, like so:

<script>
  const htmlElement = document.documentElement;

  const __FRANKEN__ = JSON.parse(
    localStorage.getItem("__FRANKEN__") || "{}",
  );

  if (
    __FRANKEN__.mode === "dark" ||
    (!__FRANKEN__.mode &&
      window.matchMedia("(prefers-color-scheme: dark)").matches)
  ) {
    htmlElement.classList.add("dark");
  } else {
    htmlElement.classList.remove("dark");
  }

  htmlElement.classList.add(__FRANKEN__.theme || "uk-theme-emerald");
  htmlElement.classList.add(__FRANKEN__.radii || "uk-radii-md");
  htmlElement.classList.add(__FRANKEN__.shadows || "uk-shadows-sm");
  htmlElement.classList.add(__FRANKEN__.font || "uk-font-sm");
  htmlElement.classList.add(__FRANKEN__.chart || "uk-chart-default");
</script>
Adding to theme switcher
To register your newly added palette with the Theme Switcher, please refer to the corresponding documentation or guide for step-by-step instructions on how to integrate it.

Accessibility
Table of contents
Interactive components
Internationalization
Instance
Globally
Complete Translation
UIkit is a fully accessible front-end framework. All its interactive components are accessible out of the box. Still, the accessibility largely depends on the author’s markup. In each component documentation we provide best practice examples on how to use UIkit to comply with the WCAG 2.1 standards.

Interactive components
UIkit’s interactive JavaScript components, for example, slideshow, lightbox, dropdown, among others, are accessible to keyboard users. We implement the common keyboard navigation convention in which the tab and shift+tab keys move focus from one component to another while other keys like arrow keys move focus inside of components that include multiple focusable elements. Learn more about keyboard interaction in the documentation of each component.

By using relevant WAI-ARIA roles, states and properties, the JavaScript components are readable and operable using assistive technologies like screen readers. They automatically set the required HTML attributes in the markup. Learn more about accessibility in the documentation of each component.

Since our JavaScript components are designed to be generic, it’s not always possible to determine the precise WAI-ARIA roles and properties that need to be set by a component. Please refer to the ARIA Authoring Practices Guide (APG) for further reading.

If you find the documentation or the components lacking accessibility, please open an issue or pull request for the documentation or UIkit on GitHub.

Internationalization
UIkit supports language internationalization (i18n) of its components. The default texts for the aria-label attributes can be translated. The translation keys for each component can be found in the corresponding documentation.

There are several ways to modify the default texts. You can pass an object to the i18n option of the component.

Instance
The i18n option can be set as shown in the following examples.

As a valid JSON format:

<div uk-marker='{"i18n": {"label": "Open"}}'></div>
As a single attribute:

<div uk-marker i18n="label: Open;"></div>
As a single attribute prefixed with data-:

<div uk-marker data-i18n="label: Open;"></div>
Or pass the i18n option to the component programmatically:

UIkit.marker(".marker", {
  i18n: { label: "Open" },
});
Globally
The default texts for a component can be changed globally by extending the component.

UIkit.mixin(
  {
    i18n: { label: "Open" },
  },
  "marker",
);
Complete Translation
Here is an example of all available component translation strings applied.

const i18n = {
  close: { label: "Close" },
  totop: { label: "Back to top" },
  marker: { label: "Open" },
  navbarToggleIcon: { label: "Open menu" },
  paginationPrevious: { label: "Next page" },
  paginationNext: { label: "Previous page" },
  slider: {
    next: "Next slide",
    previous: "Previous slide",
    slideX: "Slide %s",
    slideLabel: "%s of %s",
  },
  slideshow: {
    next: "Next slide",
    previous: "Previous slide",
    slideX: "Slide %s",
    slideLabel: "%s of %s",
  },
  lightboxPanel: {
    next: "Next slide",
    previous: "Previous slide",
    slideLabel: "%s of %s",
    close: "Close",
  },
};

for (const component in i18n) {
  UIkit.mixin({ i18n: i18n[component] }, component);
}