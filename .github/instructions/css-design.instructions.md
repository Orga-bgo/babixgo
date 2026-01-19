---
applyTo: "**/*.css"
---

# CSS & Design System Instructions

When working with CSS files in this repository, follow these mandatory design system guidelines:

## Single Source of Truth

**Global CSS Hierarchy:**
1. `/shared/assets/css/main.css` - Base styles for all sections (FIRST)
2. `/babixgo.de/assets/css/style.css` - Main site styles (3,852 lines)
3. Section-specific CSS files:
   - `/babixgo.de/assets/css/files.css` - Files section
   - `/babixgo.de/assets/css/user.css` - User section
   - `/babixgo.de/assets/css/admin.css` - Admin section

## MANDATORY Rules

### 1. NO Inline Styles
```html
<!-- ❌ WRONG - Violates CSP -->
<div style="color: red; padding: 20px;">

<!-- ✅ CORRECT - Use classes -->
<div class="error-message">
```

### 2. ALWAYS Use Design Tokens
```css
/* ✅ CORRECT - Use CSS variables */
.button {
    color: var(--md-primary);
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
}

/* ❌ WRONG - Hardcoded values */
.button {
    color: #6366f1;
    padding: 24px;
    border-radius: 8px;
}
```

### 3. Add New Global Styles to main.css
- If a style applies to multiple sections → add to `/shared/assets/css/main.css`
- If a style is section-specific → add to appropriate section CSS file

## Material Design 3 Dark Theme Tokens

### Typography
```css
--font-size-h1: 2rem;          /* 32px */
--font-size-h2: 1.5rem;        /* 24px */
--font-size-h3: 1.2rem;        /* 19.2px */
--font-size-body: 1rem;        /* 16px */
--font-size-small: 0.9rem;     /* 14.4px */

--font-body: 'Inter', sans-serif;        /* 400, 500, 600 */
--font-heading: 'Montserrat', sans-serif; /* 700 */
```

### Colors (Material Design 3 Dark)
```css
/* Primary Colors */
--md-primary: #6366f1;
--md-primary-hover: #5558e3;
--md-secondary: #8b5cf6;

/* Surface Colors */
--md-surface: #1e1e1e;
--md-surface-container-low: #1a1a1a;
--md-surface-container: #242424;
--md-surface-container-high: #2e2e2e;

/* Text Colors */
--text: #ffffff;
--text-secondary: #e0e0e0;
--muted: #a0a0a0;

/* State Colors */
--success: #10b981;
--warning: #f59e0b;
--error: #ef4444;
--info: #3b82f6;
```

### Spacing System
```css
--spacing-xs: 0.5rem;   /* 8px */
--spacing-sm: 1rem;     /* 16px */
--spacing-md: 1.5rem;   /* 24px */
--spacing-lg: 2rem;     /* 32px */
--spacing-xl: 3rem;     /* 48px */
```

### Border Radius
```css
--radius-sm: 4px;
--radius-md: 8px;
--radius-lg: 12px;
--radius-full: 9999px;
```

### Shadows
```css
--shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
--shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
--shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.2);
```

## Component Patterns

### Buttons
```css
.btn {
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    font-size: var(--font-size-body);
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-primary {
    background: var(--md-primary);
    color: var(--text);
}

.btn-primary:hover {
    background: var(--md-primary-hover);
}
```

### Cards
```css
.card {
    background: var(--md-surface-container);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-md);
}

.content-card {
    background: var(--md-surface-container-high);
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
}
```

### Forms
```css
.form-group {
    margin-bottom: var(--spacing-md);
}

.form-input {
    width: 100%;
    padding: var(--spacing-sm);
    background: var(--md-surface-container);
    color: var(--text);
    border: 1px solid var(--md-surface-container-high);
    border-radius: var(--radius-md);
}

.form-input:focus {
    outline: none;
    border-color: var(--md-primary);
}
```

## Heading Styles

### H1 - Hero Section
```css
h1, .welcome-title {
    font-family: var(--font-heading);
    font-size: var(--font-size-h1);
    color: var(--text);
    margin-bottom: var(--spacing-lg);
}
```

### H2 - Section Headers (with icon)
```css
.section-header h2 {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    font-family: var(--font-heading);
    font-size: var(--font-size-h2);
}

.section-header .icon {
    width: 32px;
    height: 32px;
}
```

### H3 - Content Headers (gradient underline)
```css
h3 {
    font-family: var(--font-heading);
    font-size: var(--font-size-h3);
    position: relative;
    padding-bottom: var(--spacing-sm);
}

h3::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, var(--md-primary), var(--md-secondary));
}
```

## Responsive Design

### Mobile-First Approach
```css
/* Mobile styles (default) */
.container {
    padding: var(--spacing-sm);
}

/* Tablet and up */
@media (min-width: 768px) {
    .container {
        padding: var(--spacing-md);
    }
}

/* Desktop and up */
@media (min-width: 1024px) {
    .container {
        padding: var(--spacing-lg);
        max-width: 1200px;
        margin: 0 auto;
    }
}
```

### Common Breakpoints
```css
/* Mobile: default (no media query needed) */
/* Tablet: 768px and up */
/* Desktop: 1024px and up */
/* Large Desktop: 1280px and up */
```

## Layout Patterns

### Grid Layout
```css
.grid {
    display: grid;
    gap: var(--spacing-md);
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}
```

### Flexbox Layout
```css
.flex-container {
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.flex-center {
    display: flex;
    align-items: center;
    justify-content: center;
}
```

## Animations & Transitions

### Standard Transitions
```css
.interactive-element {
    transition: all 0.2s ease;
}

/* Hover states */
.interactive-element:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}
```

### Loading Spinner
```css
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.spinner {
    animation: spin 1s linear infinite;
}
```

## Icon Usage

### Material Symbols Icons
```html
<!-- Use Material Symbols Outlined -->
<span class="material-symbols-outlined">home</span>
```

```css
.material-symbols-outlined {
    font-size: 24px;
    color: var(--md-primary);
}
```

## Utility Classes

### Text Utilities
```css
.text-center { text-align: center; }
.text-muted { color: var(--muted); }
.text-small { font-size: var(--font-size-small); }
```

### Spacing Utilities
```css
.mt-sm { margin-top: var(--spacing-sm); }
.mb-md { margin-bottom: var(--spacing-md); }
.p-lg { padding: var(--spacing-lg); }
```

### Display Utilities
```css
.hidden { display: none; }
.block { display: block; }
.flex { display: flex; }
.grid { display: grid; }
```

## Performance Best Practices

1. **Minimize specificity:**
   ```css
   /* ✅ GOOD */
   .card { }
   
   /* ❌ BAD */
   div.container .wrapper .card { }
   ```

2. **Group related selectors:**
   ```css
   /* ✅ GOOD */
   .btn,
   .link,
   .action {
       cursor: pointer;
       user-select: none;
   }
   ```

3. **Use CSS custom properties for dynamic values:**
   ```css
   /* ✅ GOOD */
   .theme-element {
       color: var(--theme-color, var(--md-primary));
   }
   ```

## Testing Checklist

Before committing CSS changes:
- [ ] NO inline styles added to HTML
- [ ] ALL colors use design tokens (var(--md-*))
- [ ] ALL spacing uses design tokens (var(--spacing-*))
- [ ] Responsive design tested (mobile, tablet, desktop)
- [ ] Browser DevTools console shows NO errors
- [ ] Styles work in dark mode (our default theme)
- [ ] No duplicate CSS (check if it already exists)
- [ ] CSS validates (no syntax errors)

## Where to Add Styles

**Global changes** → `/shared/assets/css/main.css`
- Base typography
- Global utility classes
- Shared component styles

**Main site** → `/babixgo.de/assets/css/style.css`
- Homepage styles
- Service pages styles
- Content pages styles

**Section-specific** → Appropriate section CSS file
- Files section → `/babixgo.de/assets/css/files.css`
- User section → `/babixgo.de/assets/css/user.css`
- Admin section → `/babixgo.de/assets/css/admin.css`
