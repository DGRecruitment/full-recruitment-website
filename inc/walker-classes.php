<?php
/**
 * RecruitPro Theme Walker Classes
 *
 * Custom walker classes for enhanced navigation, mobile menus, breadcrumbs,
 * and hierarchical content display. Specifically designed for recruitment
 * websites with professional styling, accessibility features, and optimal
 * user experience for both candidates and employers.
 *
 * @package RecruitPro
 * @subpackage Theme/Walkers
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/walker-classes.php
 * Purpose: Custom walker classes for navigation and hierarchical content
 * Dependencies: WordPress core, Walker classes
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =================================================================
 * MAIN NAVIGATION WALKER
 * =================================================================
 * 
 * Enhanced navigation walker for professional recruitment websites
 * with accessibility features, dropdown support, and custom styling.
 */

if (!class_exists('RecruitPro_Walker_Nav_Menu')) :

class RecruitPro_Walker_Nav_Menu extends Walker_Nav_Menu {

    /**
     * Starts the list before the elements are added
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param int $depth Depth of menu item
     * @param stdClass $args An object of wp_nav_menu() arguments
     */
    public function start_lvl(&$output, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);
        
        // Determine submenu classes based on depth
        $classes = array('sub-menu');
        
        if ($depth === 0) {
            $classes[] = 'dropdown-menu';
            $classes[] = 'level-1';
        } else {
            $classes[] = 'level-' . ($depth + 1);
        }
        
        // Allow filtering of submenu classes
        $classes = apply_filters('recruitpro_submenu_classes', $classes, $depth, $args);
        
        $class_names = join(' ', $classes);
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        // Add accessibility attributes
        $accessibility_attrs = '';
        if (get_theme_mod('recruitpro_menu_accessibility', true)) {
            $accessibility_attrs = ' role="menu" aria-hidden="true"';
        }
        
        $output .= "{$n}{$indent}<ul{$class_names}{$accessibility_attrs}>{$n}";
    }

    /**
     * Ends the list after the elements are added
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param int $depth Depth of menu item
     * @param stdClass $args An object of wp_nav_menu() arguments
     */
    public function end_lvl(&$output, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);
        $output .= "{$indent}</ul>{$n}";
    }

    /**
     * Starts the element output
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param WP_Post $item Menu item data object
     * @param int $depth Depth of menu item
     * @param stdClass $args An object of wp_nav_menu() arguments
     * @param int $id Current item ID
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ($depth) ? str_repeat($t, $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // Add professional classes for recruitment websites
        if ($depth === 0) {
            $classes[] = 'nav-item';
            $classes[] = 'top-level-item';
        } else {
            $classes[] = 'dropdown-item';
            $classes[] = 'sub-item';
        }

        // Add parent item class
        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'has-dropdown';
            $classes[] = 'expandable';
        }

        // Add current item classes for better styling
        if (in_array('current-menu-item', $classes) || in_array('current-menu-parent', $classes)) {
            $classes[] = 'active';
        }

        // Add CTA styling for specific menu items
        if (in_array('menu-item-cta', $classes) || in_array('cta-button', $classes)) {
            $classes[] = 'nav-cta';
            $classes[] = 'button-style';
        }

        // Filter classes for customization
        $classes = apply_filters('nav_menu_css_class', array_filter($classes), $item, $args);
        $classes = apply_filters('recruitpro_nav_menu_css_class', $classes, $item, $args, $depth);

        $class_names = join(' ', $classes);
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        // Add data attributes for enhanced functionality
        $data_attrs = '';
        $data_attrs .= ' data-menu-item="' . esc_attr($item->ID) . '"';
        $data_attrs .= ' data-depth="' . esc_attr($depth) . '"';
        
        if (in_array('menu-item-has-children', $classes)) {
            $data_attrs .= ' data-has-children="true"';
        }

        $output .= $indent . '<li' . $id . $class_names . $data_attrs . '>';

        // Build link attributes
        $attributes = !empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= !empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= !empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= !empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';

        // Add accessibility attributes
        if (get_theme_mod('recruitpro_menu_accessibility', true)) {
            $attributes .= ' role="menuitem"';
            
            if (in_array('menu-item-has-children', $classes)) {
                $attributes .= ' aria-haspopup="true"';
                $attributes .= ' aria-expanded="false"';
                
                // Add unique ID for aria-controls
                $submenu_id = 'submenu-' . $item->ID;
                $attributes .= ' aria-controls="' . esc_attr($submenu_id) . '"';
            }
            
            if (in_array('current-menu-item', $classes)) {
                $attributes .= ' aria-current="page"';
            }
        }

        // Add click tracking for analytics
        if (get_theme_mod('recruitpro_menu_analytics', false)) {
            $attributes .= ' data-analytics-category="navigation"';
            $attributes .= ' data-analytics-action="click"';
            $attributes .= ' data-analytics-label="' . esc_attr(strip_tags($item->title)) . '"';
        }

        // Build link content
        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes . ' class="nav-link">';
        
        // Add icon if specified
        $icon = get_post_meta($item->ID, '_menu_item_icon', true);
        if (!empty($icon)) {
            $item_output .= '<i class="menu-icon ' . esc_attr($icon) . '" aria-hidden="true"></i>';
        }
        
        $item_output .= isset($args->link_before) ? $args->link_before : '';
        $item_output .= '<span class="menu-text">' . apply_filters('the_title', $item->title, $item->ID) . '</span>';
        $item_output .= isset($args->link_after) ? $args->link_after : '';
        
        // Add dropdown indicator for parent items
        if (in_array('menu-item-has-children', $classes)) {
            $dropdown_icon = get_theme_mod('recruitpro_dropdown_icon', 'chevron-down');
            $item_output .= '<span class="dropdown-indicator" aria-hidden="true">';
            $item_output .= '<i class="icon-' . esc_attr($dropdown_icon) . '"></i>';
            $item_output .= '</span>';
        }
        
        $item_output .= '</a>';
        $item_output .= isset($args->after) ? $args->after : '';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * Ends the element output
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param WP_Post $item Page data object (not used)
     * @param int $depth Depth of page (not used)
     * @param stdClass $args An object of wp_nav_menu() arguments
     */
    public function end_el(&$output, $item, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $output .= "</li>{$n}";
    }
}

endif; // RecruitPro_Walker_Nav_Menu

/**
 * =================================================================
 * MOBILE NAVIGATION WALKER
 * =================================================================
 * 
 * Mobile-optimized navigation walker with touch-friendly features,
 * collapsible submenus, and enhanced accessibility for mobile devices.
 */

if (!class_exists('RecruitPro_Mobile_Walker_Nav_Menu')) :

class RecruitPro_Mobile_Walker_Nav_Menu extends Walker_Nav_Menu {

    /**
     * Starts the list before the elements are added
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param int $depth Depth of menu item
     * @param stdClass $args An object of wp_nav_menu() arguments
     */
    public function start_lvl(&$output, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);
        
        $classes = array('mobile-sub-menu', 'collapsible-menu');
        $classes[] = 'depth-' . $depth;
        
        $class_names = join(' ', apply_filters('recruitpro_mobile_submenu_classes', $classes, $depth, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        // Add mobile-specific attributes
        $attributes = ' role="group" aria-hidden="true" data-mobile-submenu="true"';
        
        $output .= "{$n}{$indent}<ul{$class_names}{$attributes}>{$n}";
    }

    /**
     * Ends the list after the elements are added
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param int $depth Depth of menu item
     * @param stdClass $args An object of wp_nav_menu() arguments
     */
    public function end_lvl(&$output, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);
        $output .= "{$indent}</ul>{$n}";
    }

    /**
     * Starts the element output
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param WP_Post $item Menu item data object
     * @param int $depth Depth of menu item
     * @param stdClass $args An object of wp_nav_menu() arguments
     * @param int $id Current item ID
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ($depth) ? str_repeat($t, $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        $classes[] = 'mobile-menu-item';
        $classes[] = 'touch-friendly';

        // Add depth-specific classes
        if ($depth === 0) {
            $classes[] = 'mobile-top-level';
            $classes[] = 'primary-item';
        } else {
            $classes[] = 'mobile-sub-item';
            $classes[] = 'secondary-item';
        }

        // Enhanced mobile classes
        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'mobile-expandable';
            $classes[] = 'has-mobile-submenu';
        }

        if (in_array('current-menu-item', $classes) || in_array('current-menu-parent', $classes)) {
            $classes[] = 'mobile-active';
        }

        $classes = apply_filters('nav_menu_css_class', array_filter($classes), $item, $args);
        $classes = apply_filters('recruitpro_mobile_nav_menu_css_class', $classes, $item, $args, $depth);

        $class_names = join(' ', $classes);
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'mobile-menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        // Mobile-specific data attributes
        $data_attrs = '';
        $data_attrs .= ' data-mobile-item="' . esc_attr($item->ID) . '"';
        $data_attrs .= ' data-depth="' . esc_attr($depth) . '"';
        $data_attrs .= ' data-touch-target="true"';
        
        if (in_array('menu-item-has-children', $classes)) {
            $data_attrs .= ' data-expandable="true"';
        }

        $output .= $indent . '<li' . $id . $class_names . $data_attrs . '>';

        // Build mobile-optimized link attributes
        $attributes = !empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= !empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= !empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= !empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';

        // Mobile accessibility attributes
        $attributes .= ' role="menuitem"';
        $attributes .= ' tabindex="0"'; // Ensure keyboard accessibility
        
        if (in_array('menu-item-has-children', $classes)) {
            $attributes .= ' aria-haspopup="true"';
            $attributes .= ' aria-expanded="false"';
            $submenu_id = 'mobile-submenu-' . $item->ID;
            $attributes .= ' aria-controls="' . esc_attr($submenu_id) . '"';
        }
        
        if (in_array('current-menu-item', $classes)) {
            $attributes .= ' aria-current="page"';
        }

        // Mobile touch attributes
        $attributes .= ' data-touch-feedback="true"';

        // Build mobile link content
        $item_output = isset($args->before) ? $args->before : '';
        
        // For items with children, create a wrapper for the link and toggle
        if (in_array('menu-item-has-children', $classes)) {
            $item_output .= '<div class="mobile-menu-item-wrapper">';
            
            // Main link
            $item_output .= '<a' . $attributes . ' class="mobile-nav-link">';
            $item_output .= isset($args->link_before) ? $args->link_before : '';
            $item_output .= '<span class="mobile-menu-text">' . apply_filters('the_title', $item->title, $item->ID) . '</span>';
            $item_output .= isset($args->link_after) ? $args->link_after : '';
            $item_output .= '</a>';
            
            // Toggle button for submenu
            $item_output .= '<button class="mobile-submenu-toggle" ';
            $item_output .= 'aria-expanded="false" ';
            $item_output .= 'aria-controls="' . esc_attr($submenu_id) . '" ';
            $item_output .= 'data-toggle-submenu="' . esc_attr($item->ID) . '">';
            $item_output .= '<span class="sr-only">' . sprintf(__('Toggle %s submenu', 'recruitpro'), $item->title) . '</span>';
            $item_output .= '<i class="mobile-toggle-icon" aria-hidden="true"></i>';
            $item_output .= '</button>';
            
            $item_output .= '</div>';
        } else {
            // Regular link for items without children
            $item_output .= '<a' . $attributes . ' class="mobile-nav-link">';
            $item_output .= isset($args->link_before) ? $args->link_before : '';
            $item_output .= '<span class="mobile-menu-text">' . apply_filters('the_title', $item->title, $item->ID) . '</span>';
            $item_output .= isset($args->link_after) ? $args->link_after : '';
            $item_output .= '</a>';
        }
        
        $item_output .= isset($args->after) ? $args->after : '';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * Ends the element output
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param WP_Post $item Page data object (not used)
     * @param int $depth Depth of page (not used)
     * @param stdClass $args An object of wp_nav_menu() arguments
     */
    public function end_el(&$output, $item, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $output .= "</li>{$n}";
    }
}

endif; // RecruitPro_Mobile_Walker_Nav_Menu

/**
 * =================================================================
 * MEGA MENU WALKER
 * =================================================================
 * 
 * Advanced mega menu walker for complex navigation structures
 * with multi-column layouts and enhanced content areas.
 */

if (!class_exists('RecruitPro_Mega_Menu_Walker')) :

class RecruitPro_Mega_Menu_Walker extends Walker_Nav_Menu {

    /**
     * Track mega menu level
     *
     * @since 1.0.0
     * @var bool
     */
    private $is_mega_menu = false;

    /**
     * Starts the list before the elements are added
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param int $depth Depth of menu item
     * @param stdClass $args An object of wp_nav_menu() arguments
     */
    public function start_lvl(&$output, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);
        
        $classes = array('sub-menu');
        
        if ($depth === 0) {
            $classes[] = 'mega-menu';
            $classes[] = 'mega-dropdown';
            $this->is_mega_menu = true;
        } elseif ($this->is_mega_menu) {
            $classes[] = 'mega-menu-column';
        } else {
            $classes[] = 'standard-dropdown';
        }
        
        $class_names = join(' ', apply_filters('recruitpro_mega_menu_classes', $classes, $depth, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        // Mega menu specific attributes
        $attributes = '';
        if ($depth === 0 && $this->is_mega_menu) {
            $attributes .= ' role="region" aria-label="' . __('Mega menu content', 'recruitpro') . '"';
            $attributes .= ' data-mega-menu="true"';
        }
        
        $output .= "{$n}{$indent}<ul{$class_names}{$attributes}>";
        
        // Add mega menu container if this is the first level
        if ($depth === 0 && $this->is_mega_menu) {
            $output .= "{$n}{$indent}<div class=\"mega-menu-container\">";
            $output .= "{$n}{$indent}<div class=\"mega-menu-content\">";
        }
    }

    /**
     * Ends the list after the elements are added
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param int $depth Depth of menu item
     * @param stdClass $args An object of wp_nav_menu() arguments
     */
    public function end_lvl(&$output, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);
        
        // Close mega menu container
        if ($depth === 0 && $this->is_mega_menu) {
            $output .= "{$indent}</div>"; // .mega-menu-content
            $output .= "{$indent}</div>"; // .mega-menu-container
            $this->is_mega_menu = false;
        }
        
        $output .= "{$indent}</ul>{$n}";
    }

    /**
     * Starts the element output for mega menu
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param WP_Post $item Menu item data object
     * @param int $depth Depth of menu item
     * @param stdClass $args An object of wp_nav_menu() arguments
     * @param int $id Current item ID
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ($depth) ? str_repeat($t, $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // Mega menu specific classes
        if ($depth === 0) {
            $classes[] = 'mega-menu-item';
            $classes[] = 'top-level-mega';
            
            // Check if this item should be a mega menu
            $enable_mega = get_post_meta($item->ID, '_menu_item_mega_menu', true);
            if ($enable_mega === 'yes' || in_array('mega-menu', $classes)) {
                $classes[] = 'has-mega-menu';
            }
        } elseif ($this->is_mega_menu && $depth === 1) {
            $classes[] = 'mega-menu-section';
            $classes[] = 'mega-column';
            
            // Column width class
            $column_width = get_post_meta($item->ID, '_menu_item_mega_column_width', true);
            if ($column_width) {
                $classes[] = 'mega-column-' . sanitize_html_class($column_width);
            } else {
                $classes[] = 'mega-column-auto';
            }
        }

        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'has-dropdown';
        }

        $classes = apply_filters('nav_menu_css_class', array_filter($classes), $item, $args);
        $class_names = join(' ', $classes);
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'mega-menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        // Build link attributes
        $attributes = !empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= !empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= !empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= !empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';

        // Mega menu accessibility
        if (in_array('has-mega-menu', $classes)) {
            $attributes .= ' aria-haspopup="true"';
            $attributes .= ' aria-expanded="false"';
            $attributes .= ' data-mega-menu-trigger="true"';
        }

        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes . ' class="mega-nav-link">';
        $item_output .= isset($args->link_before) ? $args->link_before : '';
        
        // Add mega menu title styling for section headers
        if ($this->is_mega_menu && $depth === 1) {
            $item_output .= '<span class="mega-menu-title">' . apply_filters('the_title', $item->title, $item->ID) . '</span>';
        } else {
            $item_output .= apply_filters('the_title', $item->title, $item->ID);
        }
        
        $item_output .= isset($args->link_after) ? $args->link_after : '';
        $item_output .= '</a>';
        $item_output .= isset($args->after) ? $args->after : '';

        // Add mega menu content area
        if (in_array('has-mega-menu', $classes) && $depth === 0) {
            $mega_content = get_post_meta($item->ID, '_menu_item_mega_content', true);
            if (!empty($mega_content)) {
                $item_output .= '<div class="mega-menu-custom-content">';
                $item_output .= wp_kses_post($mega_content);
                $item_output .= '</div>';
            }
        }

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * Ends the element output
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param WP_Post $item Page data object (not used)
     * @param int $depth Depth of page (not used)
     * @param stdClass $args An object of wp_nav_menu() arguments
     */
    public function end_el(&$output, $item, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $output .= "</li>{$n}";
    }
}

endif; // RecruitPro_Mega_Menu_Walker

/**
 * =================================================================
 * BREADCRUMB WALKER
 * =================================================================
 * 
 * Professional breadcrumb navigation walker for recruitment websites
 * with Schema.org markup and accessibility features.
 */

if (!class_exists('RecruitPro_Breadcrumb_Walker')) :

class RecruitPro_Breadcrumb_Walker {

    /**
     * Generate breadcrumb navigation
     *
     * @since 1.0.0
     * @param array $args Breadcrumb arguments
     * @return string Breadcrumb HTML
     */
    public static function generate_breadcrumbs($args = array()) {
        global $post, $wp_query;

        // Default arguments
        $defaults = array(
            'separator' => '/',
            'home_text' => __('Home', 'recruitpro'),
            'show_current' => true,
            'show_home' => true,
            'schema_markup' => true,
            'container_class' => 'breadcrumb-navigation',
            'list_class' => 'breadcrumbs',
            'item_class' => 'breadcrumb-item',
            'link_class' => 'breadcrumb-link',
            'separator_class' => 'breadcrumb-separator',
            'current_class' => 'breadcrumb-current',
        );

        $args = wp_parse_args($args, $defaults);
        
        // Don't show breadcrumbs on homepage unless specifically requested
        if (is_front_page() && !isset($args['force_show'])) {
            return '';
        }

        $breadcrumbs = array();
        $output = '';

        // Add home link
        if ($args['show_home']) {
            $breadcrumbs[] = array(
                'title' => $args['home_text'],
                'url' => home_url('/'),
                'current' => false
            );
        }

        // Generate breadcrumb items based on current page
        if (is_category()) {
            self::add_category_breadcrumbs($breadcrumbs);
        } elseif (is_tag()) {
            self::add_tag_breadcrumbs($breadcrumbs);
        } elseif (is_author()) {
            self::add_author_breadcrumbs($breadcrumbs);
        } elseif (is_date()) {
            self::add_date_breadcrumbs($breadcrumbs);
        } elseif (is_search()) {
            self::add_search_breadcrumbs($breadcrumbs);
        } elseif (is_single()) {
            self::add_single_breadcrumbs($breadcrumbs, $post);
        } elseif (is_page()) {
            self::add_page_breadcrumbs($breadcrumbs, $post);
        } elseif (is_post_type_archive()) {
            self::add_post_type_archive_breadcrumbs($breadcrumbs);
        } elseif (is_tax()) {
            self::add_taxonomy_breadcrumbs($breadcrumbs);
        } elseif (is_404()) {
            $breadcrumbs[] = array(
                'title' => __('404 - Page Not Found', 'recruitpro'),
                'url' => '',
                'current' => true
            );
        }

        // Build HTML output
        if (!empty($breadcrumbs)) {
            $output .= '<nav class="' . esc_attr($args['container_class']) . '" role="navigation" aria-label="' . esc_attr__('Breadcrumb navigation', 'recruitpro') . '">';
            
            if ($args['schema_markup']) {
                $output .= '<ol class="' . esc_attr($args['list_class']) . '" itemscope itemtype="https://schema.org/BreadcrumbList">';
            } else {
                $output .= '<ol class="' . esc_attr($args['list_class']) . '">';
            }

            $total_items = count($breadcrumbs);
            $position = 1;

            foreach ($breadcrumbs as $crumb) {
                $is_current = $crumb['current'] || ($position === $total_items && $args['show_current']);
                
                $item_classes = array($args['item_class']);
                if ($is_current) {
                    $item_classes[] = $args['current_class'];
                }

                $output .= '<li class="' . esc_attr(implode(' ', $item_classes)) . '"';
                
                if ($args['schema_markup']) {
                    $output .= ' itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"';
                }
                
                $output .= '>';

                if (!empty($crumb['url']) && !$is_current) {
                    $output .= '<a href="' . esc_url($crumb['url']) . '" class="' . esc_attr($args['link_class']) . '"';
                    
                    if ($args['schema_markup']) {
                        $output .= ' itemprop="item"';
                    }
                    
                    $output .= '>';
                    $output .= '<span';
                    
                    if ($args['schema_markup']) {
                        $output .= ' itemprop="name"';
                    }
                    
                    $output .= '>' . esc_html($crumb['title']) . '</span>';
                    $output .= '</a>';
                } else {
                    $output .= '<span';
                    
                    if ($args['schema_markup']) {
                        $output .= ' itemprop="name"';
                    }
                    
                    if ($is_current) {
                        $output .= ' aria-current="page"';
                    }
                    
                    $output .= '>' . esc_html($crumb['title']) . '</span>';
                }

                if ($args['schema_markup']) {
                    $output .= '<meta itemprop="position" content="' . esc_attr($position) . '" />';
                }

                // Add separator (except for last item)
                if ($position < $total_items) {
                    $output .= '<span class="' . esc_attr($args['separator_class']) . '" aria-hidden="true">' . esc_html($args['separator']) . '</span>';
                }

                $output .= '</li>';
                $position++;
            }

            $output .= '</ol>';
            $output .= '</nav>';
        }

        return $output;
    }

    /**
     * Add category breadcrumbs
     *
     * @since 1.0.0
     * @param array $breadcrumbs Breadcrumb array
     * @return void
     */
    private static function add_category_breadcrumbs(&$breadcrumbs) {
        $category = get_queried_object();
        
        if ($category->parent) {
            $parent_cats = get_category_parents($category->parent, true, '|||');
            $parent_cats = explode('|||', $parent_cats);
            
            foreach ($parent_cats as $parent_cat) {
                if (!empty($parent_cat)) {
                    preg_match('/<a href="([^"]+)">([^<]+)<\/a>/', $parent_cat, $matches);
                    if (isset($matches[1]) && isset($matches[2])) {
                        $breadcrumbs[] = array(
                            'title' => $matches[2],
                            'url' => $matches[1],
                            'current' => false
                        );
                    }
                }
            }
        }

        $breadcrumbs[] = array(
            'title' => $category->name,
            'url' => '',
            'current' => true
        );
    }

    /**
     * Add tag breadcrumbs
     *
     * @since 1.0.0
     * @param array $breadcrumbs Breadcrumb array
     * @return void
     */
    private static function add_tag_breadcrumbs(&$breadcrumbs) {
        $tag = get_queried_object();
        
        $breadcrumbs[] = array(
            'title' => sprintf(__('Tag: %s', 'recruitpro'), $tag->name),
            'url' => '',
            'current' => true
        );
    }

    /**
     * Add author breadcrumbs
     *
     * @since 1.0.0
     * @param array $breadcrumbs Breadcrumb array
     * @return void
     */
    private static function add_author_breadcrumbs(&$breadcrumbs) {
        $author = get_queried_object();
        
        $breadcrumbs[] = array(
            'title' => sprintf(__('Author: %s', 'recruitpro'), $author->display_name),
            'url' => '',
            'current' => true
        );
    }

    /**
     * Add date breadcrumbs
     *
     * @since 1.0.0
     * @param array $breadcrumbs Breadcrumb array
     * @return void
     */
    private static function add_date_breadcrumbs(&$breadcrumbs) {
        if (is_year()) {
            $breadcrumbs[] = array(
                'title' => get_the_date('Y'),
                'url' => '',
                'current' => true
            );
        } elseif (is_month()) {
            $breadcrumbs[] = array(
                'title' => get_the_date('Y'),
                'url' => get_year_link(get_the_date('Y')),
                'current' => false
            );
            $breadcrumbs[] = array(
                'title' => get_the_date('F'),
                'url' => '',
                'current' => true
            );
        } elseif (is_day()) {
            $breadcrumbs[] = array(
                'title' => get_the_date('Y'),
                'url' => get_year_link(get_the_date('Y')),
                'current' => false
            );
            $breadcrumbs[] = array(
                'title' => get_the_date('F'),
                'url' => get_month_link(get_the_date('Y'), get_the_date('m')),
                'current' => false
            );
            $breadcrumbs[] = array(
                'title' => get_the_date('d'),
                'url' => '',
                'current' => true
            );
        }
    }

    /**
     * Add search breadcrumbs
     *
     * @since 1.0.0
     * @param array $breadcrumbs Breadcrumb array
     * @return void
     */
    private static function add_search_breadcrumbs(&$breadcrumbs) {
        $search_query = get_search_query();
        
        $breadcrumbs[] = array(
            'title' => sprintf(__('Search Results for: %s', 'recruitpro'), $search_query),
            'url' => '',
            'current' => true
        );
    }

    /**
     * Add single post breadcrumbs
     *
     * @since 1.0.0
     * @param array $breadcrumbs Breadcrumb array
     * @param WP_Post $post Post object
     * @return void
     */
    private static function add_single_breadcrumbs(&$breadcrumbs, $post) {
        // Add post type archive if not default post
        if ($post->post_type !== 'post') {
            $post_type_object = get_post_type_object($post->post_type);
            if ($post_type_object && $post_type_object->has_archive) {
                $breadcrumbs[] = array(
                    'title' => $post_type_object->labels->name,
                    'url' => get_post_type_archive_link($post->post_type),
                    'current' => false
                );
            }
        }

        // Add categories for posts
        if ($post->post_type === 'post') {
            $categories = get_the_category($post->ID);
            if (!empty($categories)) {
                $category = $categories[0];
                if ($category->parent) {
                    $parent_cats = get_category_parents($category->parent, true, '|||');
                    $parent_cats = explode('|||', $parent_cats);
                    
                    foreach ($parent_cats as $parent_cat) {
                        if (!empty($parent_cat)) {
                            preg_match('/<a href="([^"]+)">([^<]+)<\/a>/', $parent_cat, $matches);
                            if (isset($matches[1]) && isset($matches[2])) {
                                $breadcrumbs[] = array(
                                    'title' => $matches[2],
                                    'url' => $matches[1],
                                    'current' => false
                                );
                            }
                        }
                    }
                }
                
                $breadcrumbs[] = array(
                    'title' => $category->name,
                    'url' => get_category_link($category->term_id),
                    'current' => false
                );
            }
        }

        // Add taxonomies for custom post types
        if ($post->post_type !== 'post' && $post->post_type !== 'page') {
            $taxonomies = get_object_taxonomies($post->post_type, 'objects');
            foreach ($taxonomies as $taxonomy) {
                if ($taxonomy->public && $taxonomy->show_ui) {
                    $terms = get_the_terms($post->ID, $taxonomy->name);
                    if (!empty($terms) && !is_wp_error($terms)) {
                        $term = $terms[0];
                        $breadcrumbs[] = array(
                            'title' => $term->name,
                            'url' => get_term_link($term),
                            'current' => false
                        );
                        break; // Only show first taxonomy
                    }
                }
            }
        }

        // Add current post
        $breadcrumbs[] = array(
            'title' => get_the_title($post->ID),
            'url' => '',
            'current' => true
        );
    }

    /**
     * Add page breadcrumbs
     *
     * @since 1.0.0
     * @param array $breadcrumbs Breadcrumb array
     * @param WP_Post $post Post object
     * @return void
     */
    private static function add_page_breadcrumbs(&$breadcrumbs, $post) {
        // Add parent pages
        if ($post->post_parent) {
            $parent_pages = array();
            $parent_id = $post->post_parent;
            
            while ($parent_id) {
                $parent_page = get_post($parent_id);
                $parent_pages[] = array(
                    'title' => get_the_title($parent_page->ID),
                    'url' => get_permalink($parent_page->ID),
                    'current' => false
                );
                $parent_id = $parent_page->post_parent;
            }
            
            $breadcrumbs = array_merge($breadcrumbs, array_reverse($parent_pages));
        }

        // Add current page
        $breadcrumbs[] = array(
            'title' => get_the_title($post->ID),
            'url' => '',
            'current' => true
        );
    }

    /**
     * Add post type archive breadcrumbs
     *
     * @since 1.0.0
     * @param array $breadcrumbs Breadcrumb array
     * @return void
     */
    private static function add_post_type_archive_breadcrumbs(&$breadcrumbs) {
        $post_type = get_query_var('post_type');
        $post_type_object = get_post_type_object($post_type);
        
        if ($post_type_object) {
            $breadcrumbs[] = array(
                'title' => $post_type_object->labels->name,
                'url' => '',
                'current' => true
            );
        }
    }

    /**
     * Add taxonomy breadcrumbs
     *
     * @since 1.0.0
     * @param array $breadcrumbs Breadcrumb array
     * @return void
     */
    private static function add_taxonomy_breadcrumbs(&$breadcrumbs) {
        $term = get_queried_object();
        $taxonomy = get_taxonomy($term->taxonomy);
        
        // Add post type archive if applicable
        if ($taxonomy && !empty($taxonomy->object_type)) {
            $post_type = $taxonomy->object_type[0];
            $post_type_object = get_post_type_object($post_type);
            
            if ($post_type_object && $post_type_object->has_archive) {
                $breadcrumbs[] = array(
                    'title' => $post_type_object->labels->name,
                    'url' => get_post_type_archive_link($post_type),
                    'current' => false
                );
            }
        }

        // Add parent terms
        if ($term->parent) {
            $parent_terms = array();
            $parent_id = $term->parent;
            
            while ($parent_id) {
                $parent_term = get_term($parent_id, $term->taxonomy);
                $parent_terms[] = array(
                    'title' => $parent_term->name,
                    'url' => get_term_link($parent_term),
                    'current' => false
                );
                $parent_id = $parent_term->parent;
            }
            
            $breadcrumbs = array_merge($breadcrumbs, array_reverse($parent_terms));
        }

        // Add current term
        $breadcrumbs[] = array(
            'title' => $term->name,
            'url' => '',
            'current' => true
        );
    }
}

endif; // RecruitPro_Breadcrumb_Walker

/**
 * =================================================================
 * COMMENT WALKER
 * =================================================================
 * 
 * Professional comment display walker for recruitment blog posts
 * with enhanced threading and professional styling.
 */

if (!class_exists('RecruitPro_Walker_Comment')) :

class RecruitPro_Walker_Comment extends Walker_Comment {

    /**
     * Start the element output
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param WP_Comment $comment Comment data object
     * @param int $depth Depth of comment
     * @param array $args Uses 'style' argument for type of HTML list
     * @param int $id Current comment ID
     */
    public function start_el(&$output, $comment, $depth = 0, $args = array(), $id = 0) {
        $depth++;
        $GLOBALS['comment_depth'] = $depth;
        $GLOBALS['comment'] = $comment;

        if (!empty($args['callback'])) {
            ob_start();
            call_user_func($args['callback'], $comment, $args, $depth);
            $output .= ob_get_clean();
            return;
        }

        if (($comment->comment_type == 'pingback' || $comment->comment_type == 'trackback') && $args['short_ping']) {
            ob_start();
            $this->ping($comment, $depth, $args);
            $output .= ob_get_clean();
        } elseif ($args['style'] === 'div') {
            ob_start();
            $this->html5_comment($comment, $depth, $args);
            $output .= ob_get_clean();
        } else {
            ob_start();
            $this->html5_comment($comment, $depth, $args);
            $output .= ob_get_clean();
        }
    }

    /**
     * HTML5 comment template
     *
     * @since 1.0.0
     * @param WP_Comment $comment Comment object
     * @param int $depth Depth of comment
     * @param array $args Comment arguments
     */
    protected function html5_comment($comment, $depth, $args) {
        $tag = ('div' === $args['style']) ? 'div' : 'li';
        
        $commenter = wp_get_current_commenter();
        if ($commenter['comment_author_email']) {
            $moderation_note = __('Your comment is awaiting moderation.', 'recruitpro');
        } else {
            $moderation_note = __('Your comment is awaiting moderation. This is a preview, your comment will be visible after it has been approved.', 'recruitpro');
        }
        ?>
        
        <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent' : '', $comment); ?>>
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-body" itemscope itemtype="http://schema.org/Comment">
                
                <header class="comment-meta">
                    <div class="comment-author vcard" itemprop="author" itemscope itemtype="http://schema.org/Person">
                        <?php
                        $comment_author_url = get_comment_author_url($comment);
                        $comment_author = get_comment_author($comment);
                        $avatar = get_avatar($comment, $args['avatar_size']);
                        
                        if (0 != $args['avatar_size']) {
                            if (empty($comment_author_url)) {
                                echo '<div class="comment-avatar">' . $avatar . '</div>';
                            } else {
                                echo '<div class="comment-avatar"><a href="' . esc_url($comment_author_url) . '" rel="external nofollow" class="url">' . $avatar . '</a></div>';
                            }
                        }
                        ?>
                        
                        <div class="comment-metadata">
                            <div class="comment-author-name">
                                <?php
                                if (empty($comment_author_url)) {
                                    echo '<span class="fn" itemprop="name">' . esc_html($comment_author) . '</span>';
                                } else {
                                    echo '<a href="' . esc_url($comment_author_url) . '" rel="external nofollow" class="url fn" itemprop="name">' . esc_html($comment_author) . '</a>';
                                }
                                ?>
                            </div>
                            
                            <div class="comment-date">
                                <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>" class="comment-time-link">
                                    <time datetime="<?php comment_time('c'); ?>" itemprop="datePublished">
                                        <?php
                                        /* translators: 1: Comment date, 2: Comment time. */
                                        printf(__('%1$s at %2$s', 'recruitpro'), get_comment_date('', $comment), get_comment_time());
                                        ?>
                                    </time>
                                </a>
                            </div>
                            
                            <?php
                            // Show if comment is by post author
                            if (get_comment()->user_id === get_the_author_meta('ID')) {
                                echo '<span class="comment-author-badge">' . __('Author', 'recruitpro') . '</span>';
                            }
                            ?>
                        </div>
                    </div>
                </header>

                <?php if ('0' == $comment->comment_approved) : ?>
                    <p class="comment-awaiting-moderation" role="alert"><?php echo $moderation_note; ?></p>
                <?php endif; ?>

                <div class="comment-content" itemprop="text">
                    <?php comment_text(); ?>
                </div>

                <footer class="comment-footer">
                    <div class="comment-actions">
                        <?php
                        // Edit link
                        edit_comment_link(__('Edit', 'recruitpro'), '<span class="edit-link">', '</span>');
                        ?>
                        
                        <?php
                        // Reply link
                        comment_reply_link(
                            array_merge(
                                $args,
                                array(
                                    'add_below' => 'div-comment',
                                    'depth' => $depth,
                                    'max_depth' => $args['max_depth'],
                                    'before' => '<span class="reply-link">',
                                    'after' => '</span>',
                                )
                            )
                        );
                        ?>
                    </div>
                </footer>
            </article>
        <?php
    }

    /**
     * Display pingbacks and trackbacks
     *
     * @since 1.0.0
     * @param WP_Comment $comment Comment object
     * @param int $depth Depth of comment
     * @param array $args Comment arguments
     */
    protected function ping($comment, $depth, $args) {
        $tag = ('div' == $args['style']) ? 'div' : 'li';
        ?>
        <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class('ping', $comment); ?>>
            <div class="comment-body">
                <div class="comment-content">
                    <?php _e('Pingback:', 'recruitpro'); ?> <?php comment_author_link($comment); ?> <?php edit_comment_link(__('Edit', 'recruitpro'), '<span class="edit-link">', '</span>'); ?>
                </div>
            </div>
        <?php
    }

    /**
     * End the element output
     *
     * @since 1.0.0
     * @param string $output Used to append additional content
     * @param WP_Comment $comment Comment data object
     * @param int $depth Depth of comment
     * @param array $args Comment arguments
     */
    public function end_el(&$output, $comment, $depth = 0, $args = array()) {
        if (!empty($args['end-callback'])) {
            ob_start();
            call_user_func($args['end-callback'], $comment, $args, $depth);
            $output .= ob_get_clean();
            return;
        }
        
        if ('div' == $args['style']) {
            $output .= "</div><!-- #comment-## -->\n";
        } else {
            $output .= "</li><!-- #comment-## -->\n";
        }
    }
}

endif; // RecruitPro_Walker_Comment

/**
 * =================================================================
 * HELPER FUNCTIONS FOR WALKER CLASSES
 * =================================================================
 */

/**
 * Get the appropriate walker class for menu location
 *
 * @since 1.0.0
 * @param string $location Menu location
 * @return string Walker class name
 */
function recruitpro_get_menu_walker($location = '') {
    switch ($location) {
        case 'mobile':
            return 'RecruitPro_Mobile_Walker_Nav_Menu';
        case 'mega':
            return 'RecruitPro_Mega_Menu_Walker';
        default:
            return 'RecruitPro_Walker_Nav_Menu';
    }
}

/**
 * Display breadcrumb navigation
 *
 * @since 1.0.0
 * @param array $args Breadcrumb arguments
 * @return void
 */
function recruitpro_breadcrumbs($args = array()) {
    if (!get_theme_mod('recruitpro_breadcrumbs_enabled', true)) {
        return;
    }

    echo RecruitPro_Breadcrumb_Walker::generate_breadcrumbs($args);
}

/**
 * Enhanced comment form with professional styling
 *
 * @since 1.0.0
 * @param array $args Comment form arguments
 * @return void
 */
function recruitpro_comment_form($args = array()) {
    $defaults = array(
        'comment_field' => '<p class="comment-form-comment"><label for="comment" class="screen-reader-text">' . __('Comment', 'recruitpro') . '</label><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required" placeholder="' . esc_attr__('Share your thoughts...', 'recruitpro') . '"></textarea></p>',
        'class_submit' => 'submit button button-primary',
        'submit_button' => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
        'title_reply' => __('Leave a Comment', 'recruitpro'),
        'title_reply_to' => __('Leave a Reply to %s', 'recruitpro'),
        'label_submit' => __('Post Comment', 'recruitpro'),
        'format' => 'html5',
    );

    $args = wp_parse_args($args, $defaults);
    comment_form($args);
}

/**
 * Check if current menu item should be a mega menu
 *
 * @since 1.0.0
 * @param int $item_id Menu item ID
 * @return bool True if mega menu
 */
function recruitpro_is_mega_menu_item($item_id) {
    $enable_mega = get_post_meta($item_id, '_menu_item_mega_menu', true);
    return ($enable_mega === 'yes');
}

/**
 * Add custom fields to menu items
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_add_menu_item_custom_fields() {
    add_action('wp_nav_menu_item_custom_fields', 'recruitpro_menu_item_custom_fields', 10, 2);
}

/**
 * Display custom fields for menu items
 *
 * @since 1.0.0
 * @param int $item_id Menu item ID
 * @param object $item Menu item object
 * @return void
 */
function recruitpro_menu_item_custom_fields($item_id, $item) {
    $mega_menu_enabled = get_post_meta($item_id, '_menu_item_mega_menu', true);
    $menu_icon = get_post_meta($item_id, '_menu_item_icon', true);
    $mega_content = get_post_meta($item_id, '_menu_item_mega_content', true);
    ?>
    
    <p class="field-mega-menu description description-wide">
        <label for="edit-menu-item-mega-menu-<?php echo esc_attr($item_id); ?>">
            <input type="checkbox" id="edit-menu-item-mega-menu-<?php echo esc_attr($item_id); ?>" name="menu-item-mega-menu[<?php echo esc_attr($item_id); ?>]" value="yes" <?php checked($mega_menu_enabled, 'yes'); ?> />
            <?php esc_html_e('Enable Mega Menu', 'recruitpro'); ?>
        </label>
    </p>
    
    <p class="field-menu-icon description description-wide">
        <label for="edit-menu-item-icon-<?php echo esc_attr($item_id); ?>">
            <?php esc_html_e('Menu Icon Class', 'recruitpro'); ?><br />
            <input type="text" id="edit-menu-item-icon-<?php echo esc_attr($item_id); ?>" class="widefat" name="menu-item-icon[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($menu_icon); ?>" placeholder="fas fa-briefcase" />
            <span class="description"><?php esc_html_e('Font Awesome icon class (e.g., fas fa-briefcase)', 'recruitpro'); ?></span>
        </label>
    </p>
    
    <?php if ($mega_menu_enabled === 'yes') : ?>
    <p class="field-mega-content description description-wide">
        <label for="edit-menu-item-mega-content-<?php echo esc_attr($item_id); ?>">
            <?php esc_html_e('Mega Menu Content', 'recruitpro'); ?><br />
            <textarea id="edit-menu-item-mega-content-<?php echo esc_attr($item_id); ?>" class="widefat" name="menu-item-mega-content[<?php echo esc_attr($item_id); ?>]" rows="5"><?php echo esc_textarea($mega_content); ?></textarea>
            <span class="description"><?php esc_html_e('HTML content for mega menu area', 'recruitpro'); ?></span>
        </label>
    </p>
    <?php endif; ?>
    
    <?php
}

/**
 * Save custom menu item fields
 *
 * @since 1.0.0
 * @param int $menu_id Menu ID
 * @param int $menu_item_db_id Menu item ID
 * @return void
 */
function recruitpro_save_menu_item_custom_fields($menu_id, $menu_item_db_id) {
    if (isset($_POST['menu-item-mega-menu'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_menu_item_mega_menu', sanitize_text_field($_POST['menu-item-mega-menu'][$menu_item_db_id]));
    } else {
        delete_post_meta($menu_item_db_id, '_menu_item_mega_menu');
    }
    
    if (isset($_POST['menu-item-icon'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_menu_item_icon', sanitize_text_field($_POST['menu-item-icon'][$menu_item_db_id]));
    }
    
    if (isset($_POST['menu-item-mega-content'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_menu_item_mega_content', wp_kses_post($_POST['menu-item-mega-content'][$menu_item_db_id]));
    }
}

// Initialize custom menu fields
add_action('wp_nav_menu_item_custom_fields', 'recruitpro_menu_item_custom_fields', 10, 2);
add_action('wp_update_nav_menu_item', 'recruitpro_save_menu_item_custom_fields', 10, 2);

// End of walker-classes.php