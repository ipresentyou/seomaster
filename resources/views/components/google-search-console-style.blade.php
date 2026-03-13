{{-- Google Search Console Style Guide --}}
{{-- 
Farbpalette:
- Primary: #1a73e8 (Google Blue)
- Primary Dark: #185abc
- Secondary: #5f6368 (Google Gray)
- Background: #ffffff
- Surface: #f8f9fa
- Error: #d93025
- Warning: #f9ab00
- Success: #1e8e3e
- Text Primary: #202124
- Text Secondary: #5f6368
- Text Disabled: #9aa0a6
- Border: #dadce0
- Hover: #f1f3f4

Typography:
- Font Family: Google Sans, Roboto, Arial, sans-serif
- Font Sizes: 14px (body), 16px (labels), 12px (captions)
- Font Weights: 400 (regular), 500 (medium), 700 (bold)

Components:
- Buttons: Rounded corners, subtle shadows, hover states
- Cards: White background, subtle borders, elevation shadows
- Forms: Material Design inspired inputs
- Navigation: Clean, minimal, with clear hierarchy
--}}

<style>
/* Google Search Console CSS Variables */
:root {
    /* Google Color Palette */
    --gsc-primary: #1a73e8;
    --gsc-primary-dark: #185abc;
    --gsc-primary-light: #d2e3fc;
    --gsc-secondary: #5f6368;
    --gsc-background: #ffffff;
    --gsc-surface: #f8f9fa;
    --gsc-surface-variant: #f1f3f4;
    --gsc-error: #d93025;
    --gsc-warning: #f9ab00;
    --gsc-success: #1e8e3e;
    --gsc-info: #1a73e8;
    
    /* Text Colors */
    --gsc-text-primary: #202124;
    --gsc-text-secondary: #5f6368;
    --gsc-text-disabled: #9aa0a6;
    --gsc-text-on-primary: #ffffff;
    
    /* Border & UI */
    --gsc-border: #dadce0;
    --gsc-border-hover: #c0c4cc;
    --gsc-divider: #e0e0e0;
    --gsc-hover: #f1f3f4;
    --gsc-active: #e8eaed;
    
    /* Shadows */
    --gsc-shadow-1: 0 1px 2px 0 rgba(60,64,67,0.3), 0 1px 3px 1px rgba(60,64,67,0.15);
    --gsc-shadow-2: 0 1px 2px 0 rgba(60,64,67,0.3), 0 2px 6px 2px rgba(60,64,67,0.15);
    --gsc-shadow-3: 0 4px 8px 3px rgba(60,64,67,0.15), 0 1px 3px 0 rgba(60,64,67,0.3);
    --gsc-shadow-4: 0 6px 10px 4px rgba(60,64,67,0.15), 0 2px 3px 0 rgba(60,64,67,0.3);
    
    /* Typography */
    --gsc-font-family: 'Google Sans', 'Roboto', Arial, sans-serif;
    --gsc-font-size-xs: 10px;
    --gsc-font-size-sm: 12px;
    --gsc-font-size-base: 14px;
    --gsc-font-size-lg: 16px;
    --gsc-font-size-xl: 18px;
    --gsc-font-size-2xl: 24px;
    --gsc-font-weight-normal: 400;
    --gsc-font-weight-medium: 500;
    --gsc-font-weight-bold: 700;
    
    /* Spacing */
    --gsc-spacing-xs: 4px;
    --gsc-spacing-sm: 8px;
    --gsc-spacing-md: 16px;
    --gsc-spacing-lg: 24px;
    --gsc-spacing-xl: 32px;
    
    /* Border Radius */
    --gsc-radius-sm: 4px;
    --gsc-radius-md: 8px;
    --gsc-radius-lg: 12px;
    --gsc-radius-xl: 16px;
}

/* Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@400;500;700&display=swap');

/* Base Styles */
.gsc-body {
    font-family: var(--gsc-font-family);
    font-size: var(--gsc-font-size-base);
    color: var(--gsc-text-primary);
    background-color: var(--gsc-background);
    margin: 0;
    padding: 0;
    line-height: 1.5;
}

/* Typography */
.gsc-text-xs { font-size: var(--gsc-font-size-xs); }
.gsc-text-sm { font-size: var(--gsc-font-size-sm); }
.gsc-text-base { font-size: var(--gsc-font-size-base); }
.gsc-text-lg { font-size: var(--gsc-font-size-lg); }
.gsc-text-xl { font-size: var(--gsc-font-size-xl); }
.gsc-text-2xl { font-size: var(--gsc-font-size-2xl); }

.gsc-font-normal { font-weight: var(--gsc-font-weight-normal); }
.gsc-font-medium { font-weight: var(--gsc-font-weight-medium); }
.gsc-font-bold { font-weight: var(--gsc-font-weight-bold); }

.gsc-text-primary { color: var(--gsc-text-primary); }
.gsc-text-secondary { color: var(--gsc-text-secondary); }
.gsc-text-disabled { color: var(--gsc-text-disabled); }

/* Buttons */
.gsc-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--gsc-spacing-sm);
    padding: var(--gsc-spacing-sm) var(--gsc-spacing-md);
    border: 1px solid transparent;
    border-radius: var(--gsc-radius-md);
    font-family: var(--gsc-font-family);
    font-size: var(--gsc-font-size-base);
    font-weight: var(--gsc-font-weight-medium);
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    min-height: 36px;
    outline: none;
    box-sizing: border-box;
}

.gsc-btn:hover {
    box-shadow: var(--gsc-shadow-1);
}

.gsc-btn:active {
    box-shadow: none;
    transform: translateY(1px);
}

.gsc-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    box-shadow: none;
}

/* Button Variants */
.gsc-btn-primary {
    background-color: var(--gsc-primary);
    color: var(--gsc-text-on-primary);
    border-color: var(--gsc-primary);
}

.gsc-btn-primary:hover {
    background-color: var(--gsc-primary-dark);
    border-color: var(--gsc-primary-dark);
}

.gsc-btn-secondary {
    background-color: var(--gsc-background);
    color: var(--gsc-primary);
    border-color: var(--gsc-border);
}

.gsc-btn-secondary:hover {
    background-color: var(--gsc-surface-variant);
    border-color: var(--gsc-border-hover);
}

.gsc-btn-outlined {
    background-color: transparent;
    color: var(--gsc-primary);
    border-color: var(--gsc-primary);
}

.gsc-btn-outlined:hover {
    background-color: var(--gsc-primary-light);
}

.gsc-btn-ghost {
    background-color: transparent;
    color: var(--gsc-text-secondary);
    border-color: transparent;
}

.gsc-btn-ghost:hover {
    background-color: var(--gsc-hover);
    color: var(--gsc-text-primary);
}

/* Button Sizes */
.gsc-btn-sm {
    padding: var(--gsc-spacing-xs) var(--gsc-spacing-sm);
    font-size: var(--gsc-font-size-sm);
    min-height: 28px;
}

.gsc-btn-lg {
    padding: var(--gsc-spacing-md) var(--gsc-spacing-lg);
    font-size: var(--gsc-font-size-lg);
    min-height: 44px;
}

/* Cards */
.gsc-card {
    background-color: var(--gsc-background);
    border: 1px solid var(--gsc-border);
    border-radius: var(--gsc-radius-lg);
    box-shadow: var(--gsc-shadow-1);
    overflow: hidden;
}

.gsc-card-header {
    padding: var(--gsc-spacing-md);
    border-bottom: 1px solid var(--gsc-divider);
    background-color: var(--gsc-surface);
}

.gsc-card-title {
    font-size: var(--gsc-font-size-lg);
    font-weight: var(--gsc-font-weight-medium);
    color: var(--gsc-text-primary);
    margin: 0;
}

.gsc-card-subtitle {
    font-size: var(--gsc-font-size-sm);
    color: var(--gsc-text-secondary);
    margin: var(--gsc-spacing-xs) 0 0 0;
}

.gsc-card-body {
    padding: var(--gsc-spacing-md);
}

.gsc-card-footer {
    padding: var(--gsc-spacing-md);
    border-top: 1px solid var(--gsc-divider);
    background-color: var(--gsc-surface);
}

/* Form Elements */
.gsc-form-group {
    margin-bottom: var(--gsc-spacing-md);
}

.gsc-label {
    display: block;
    font-size: var(--gsc-font-size-sm);
    font-weight: var(--gsc-font-weight-medium);
    color: var(--gsc-text-primary);
    margin-bottom: var(--gsc-spacing-xs);
}

.gsc-input,
.gsc-select,
.gsc-textarea {
    width: 100%;
    padding: var(--gsc-spacing-sm);
    border: 1px solid var(--gsc-border);
    border-radius: var(--gsc-radius-md);
    font-family: var(--gsc-font-family);
    font-size: var(--gsc-font-size-base);
    color: var(--gsc-text-primary);
    background-color: var(--gsc-background);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    outline: none;
    box-sizing: border-box;
}

.gsc-input:focus,
.gsc-select:focus,
.gsc-textarea:focus {
    border-color: var(--gsc-primary);
    box-shadow: 0 0 0 3px var(--gsc-primary-light);
}

.gsc-input::placeholder,
.gsc-textarea::placeholder {
    color: var(--gsc-text-disabled);
}

.gsc-input:disabled,
.gsc-select:disabled,
.gsc-textarea:disabled {
    background-color: var(--gsc-surface);
    color: var(--gsc-text-disabled);
    cursor: not-allowed;
}

/* Navigation */
.gsc-nav {
    background-color: var(--gsc-background);
    border-bottom: 1px solid var(--gsc-border);
    box-shadow: var(--gsc-shadow-1);
}

.gsc-nav-list {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
}

.gsc-nav-item {
    border-bottom: 3px solid transparent;
    transition: border-color 0.2s ease;
}

.gsc-nav-link {
    display: block;
    padding: var(--gsc-spacing-md) var(--gsc-spacing-lg);
    color: var(--gsc-text-secondary);
    text-decoration: none;
    font-weight: var(--gsc-font-weight-medium);
    transition: color 0.2s ease;
}

.gsc-nav-link:hover {
    color: var(--gsc-text-primary);
}

.gsc-nav-item.active {
    border-bottom-color: var(--gsc-primary);
}

.gsc-nav-item.active .gsc-nav-link {
    color: var(--gsc-primary);
}

/* Sidebar Navigation */
.gsc-sidebar {
    background-color: var(--gsc-background);
    border-right: 1px solid var(--gsc-border);
    width: 256px;
    height: 100vh;
    overflow-y: auto;
}

.gsc-sidebar-section {
    padding: var(--gsc-spacing-md);
}

.gsc-sidebar-title {
    font-size: var(--gsc-font-size-sm);
    font-weight: var(--gsc-font-weight-medium);
    color: var(--gsc-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: var(--gsc-spacing-sm);
}

.gsc-sidebar-item {
    display: flex;
    align-items: center;
    gap: var(--gsc-spacing-sm);
    padding: var(--gsc-spacing-sm);
    border-radius: var(--gsc-radius-md);
    color: var(--gsc-text-secondary);
    text-decoration: none;
    transition: all 0.2s ease;
    margin-bottom: 2px;
}

.gsc-sidebar-item:hover {
    background-color: var(--gsc-hover);
    color: var(--gsc-text-primary);
}

.gsc-sidebar-item.active {
    background-color: var(--gsc-primary-light);
    color: var(--gsc-primary);
    font-weight: var(--gsc-font-weight-medium);
}

/* Alerts/Notifications */
.gsc-alert {
    padding: var(--gsc-spacing-md);
    border-radius: var(--gsc-radius-md);
    border: 1px solid;
    margin-bottom: var(--gsc-spacing-md);
}

.gsc-alert-success {
    background-color: #e6f4ea;
    border-color: #c8e6c9;
    color: var(--gsc-success);
}

.gsc-alert-warning {
    background-color: #fef7e0;
    border-color: #fce8b2;
    color: var(--gsc-warning);
}

.gsc-alert-error {
    background-color: #fce8e6;
    border-color: #f6aea9;
    color: var(--gsc-error);
}

.gsc-alert-info {
    background-color: #e8f0fe;
    border-color: #c6dafc;
    color: var(--gsc-info);
}

/* Tables */
.gsc-table {
    width: 100%;
    border-collapse: collapse;
    background-color: var(--gsc-background);
}

.gsc-table th,
.gsc-table td {
    padding: var(--gsc-spacing-md);
    text-align: left;
    border-bottom: 1px solid var(--gsc-divider);
}

.gsc-table th {
    font-weight: var(--gsc-font-weight-medium);
    color: var(--gsc-text-secondary);
    background-color: var(--gsc-surface);
}

.gsc-table tr:hover {
    background-color: var(--gsc-hover);
}

/* Badges */
.gsc-badge {
    display: inline-flex;
    align-items: center;
    padding: var(--gsc-spacing-xs) var(--gsc-spacing-sm);
    border-radius: var(--gsc-radius-sm);
    font-size: var(--gsc-font-size-xs);
    font-weight: var(--gsc-font-weight-medium);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.gsc-badge-primary {
    background-color: var(--gsc-primary-light);
    color: var(--gsc-primary);
}

.gsc-badge-success {
    background-color: #e6f4ea;
    color: var(--gsc-success);
}

.gsc-badge-warning {
    background-color: #fef7e0;
    color: var(--gsc-warning);
}

.gsc-badge-error {
    background-color: #fce8e6;
    color: var(--gsc-error);
}

.gsc-badge-gray {
    background-color: var(--gsc-surface);
    color: var(--gsc-text-secondary);
}

/* Utility Classes */
.gsc-mb-0 { margin-bottom: 0; }
.gsc-mb-1 { margin-bottom: var(--gsc-spacing-xs); }
.gsc-mb-2 { margin-bottom: var(--gsc-spacing-sm); }
.gsc-mb-3 { margin-bottom: var(--gsc-spacing-md); }
.gsc-mb-4 { margin-bottom: var(--gsc-spacing-lg); }
.gsc-mb-5 { margin-bottom: var(--gsc-spacing-xl); }

.gsc-mt-0 { margin-top: 0; }
.gsc-mt-1 { margin-top: var(--gsc-spacing-xs); }
.gsc-mt-2 { margin-top: var(--gsc-spacing-sm); }
.gsc-mt-3 { margin-top: var(--gsc-spacing-md); }
.gsc-mt-4 { margin-top: var(--gsc-spacing-lg); }
.gsc-mt-5 { margin-top: var(--gsc-spacing-xl); }

.gsc-p-0 { padding: 0; }
.gsc-p-1 { padding: var(--gsc-spacing-xs); }
.gsc-p-2 { padding: var(--gsc-spacing-sm); }
.gsc-p-3 { padding: var(--gsc-spacing-md); }
.gsc-p-4 { padding: var(--gsc-spacing-lg); }
.gsc-p-5 { padding: var(--gsc-spacing-xl); }

.gsc-flex { display: flex; }
.gsc-inline-flex { display: inline-flex; }
.gsc-block { display: block; }
.gsc-inline-block { display: inline-block; }
.gsc-hidden { display: none; }

.gsc-items-center { align-items: center; }
.gsc-items-start { align-items: flex-start; }
.gsc-items-end { align-items: flex-end; }

.gsc-justify-center { justify-content: center; }
.gsc-justify-between { justify-content: space-between; }
.gsc-justify-start { justify-content: flex-start; }
.gsc-justify-end { justify-content: flex-end; }

.gsc-w-full { width: 100%; }
.gsc-h-full { height: 100%; }

.gsc-text-center { text-align: center; }
.gsc-text-left { text-align: left; }
.gsc-text-right { text-align: right; }
</style>
