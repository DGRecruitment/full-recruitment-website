<?php
/**
 * RecruitPro Theme Breadcrumbs
 *
 * SEO-friendly breadcrumb navigation with structured data support
 * Supports recruitment-specific content types and multilingual sites
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main breadcrumb function
 */
function recruitpro_breadcrumbs($args = array()) {
    // Don't show on homepage unless specifically requested
    if (is_front_page() && !isset($args['show_on_front'])) {
        return;
    }
    
    // Default arguments
    $defaults = array(
        'container'         => 'nav',
        'container_class'   => 'recruitpro-breadcrumbs',
        'container_id'      => '',
        'list_class'        => 'breadcrumb-list',
        'item_class'        => 'breadcrumb-item',
        'active_class'      => 'breadcrumb-active',
        'separator'         => '<span class="breadcrumb-separator" aria-hidden="true">/</span>',
        'home_text'         => esc_html__('Home', 'recruitpro'),
        'show_current'      => true,
        'show_home'         => true,
        'schema'            => true,
        'show_on_front'     => false,
        'max_length'        => 50,
    );
    
    $args = wp_parse_args($args, $defaults);
    
    // Get breadcrumb items
    $breadcrumbs = recruitpro_get_breadcrumb_items($args);
    
    if (empty($breadcrumbs)) {
        return;
    }
    
    // Build breadcrumb HTML
    $html = recruitpro_build_breadcrumb_html($breadcrumbs, $args);
    
    // Apply filters
    $html = apply_filters('recruitpro_breadcrumbs_html', $html, $breadcrumbs, $args);
    
    echo $html;
}

/**
 * Get breadcrumb items based on current page
 */
function recruitpro_get_breadcrumb_items($args) {
    global $post, $wp_query;
    
    $breadcrumbs = array();
    
    // Add home link
    if ($args['show_home']) {
        $breadcrumbs[] = array(
            'title' => $args['home_text'],
            'url'   => home_url('/'),
            'type'  => 'home'
        );
    }
    
    // Handle different page types
    if (is_home() && !is_front_page()) {
        // Blog page
        $blog_page_id = get_option('page_for_posts');
        if ($blog_page_id) {
            $breadcrumbs[] = array(
                'title' => get_the_title($blog_page_id),
                'url'   => get_permalink($blog_page_id),
                'type'  => 'blog'
            );
        }
    } elseif (is_category()) {
        // Category archive
        $breadcrumbs = array_merge($breadcrumbs, recruitpro_get_category_breadcrumbs());
    } elseif (is_tag()) {
        // Tag archive
        $breadcrumbs = array_merge($breadcrumbs, recruitpro_get_tag_breadcrumbs());
    } elseif (is_date()) {
        // Date archive
        $breadcrumbs = array_merge($breadcrumbs, recruitpro_get_date_breadcrumbs());
    } elseif (is_author()) {
        // Author archive
        $breadcrumbs = array_merge($breadcrumbs, recruitpro_get_author_breadcrumbs());
    } elseif (is_search()) {
        // Search results
        $breadcrumbs[] = array(
            'title' => sprintf(esc_html__('Search results for: %s', 'recruitpro'), get_search_query()),
            'url'   => '',
            'type'  => 'search'
        );
    } elseif (is_404()) {
        // 404 page
        $breadcrumbs[] = array(
            'title' => esc_html__('Page Not Found', 'recruitpro'),
            'url'   => '',
            'type'  => '404'
        );
    } elseif (is_singular()) {
        // Single post/page
        $breadcrumbs = array_merge($breadcrumbs, recruitpro_get_singular_breadcrumbs($post));
    } elseif (is_post_type_archive()) {
        // Post type archive
        $breadcrumbs = array_merge($breadcrumbs, recruitpro_get_post_type_archive_breadcrumbs());
    } elseif (is_tax()) {
        // Custom taxonomy
        $breadcrumbs = array_merge($breadcrumbs, recruitpro_get_taxonomy_breadcrumbs());
    }
    
    // Apply filters
    $breadcrumbs = apply_filters('recruitpro_breadcrumb_items', $breadcrumbs, $args);
    
    return $breadcrumbs;
}

/**
 * Get category breadcrumbs
 */
function recruitpro_get_category_breadcrumbs() {
    $breadcrumbs = array();
    $category = get_queried_object();
    
    // Add blog page if it exists
    $blog_page_id = get_option('page_for_posts');
    if ($blog_page_id && !is_front_page()) {
        $breadcrumbs[] = array(
            'title' => get_the_title($blog_page_id),
            'url'   => get_permalink($blog_page_id),
            'type'  => 'blog'
        );
    }
    
    // Add parent categories
    if ($category->parent) {
        $parent_categories = array();
        $parent = $category->parent;
        
        while ($parent) {
            $parent_cat = get_category($parent);
            $parent_categories[] = array(
                'title' => $parent_cat->name,
                'url'   => get_category_link($parent_cat->term_id),
                'type'  => 'category'
            );
            $parent = $parent_cat->parent;
        }
        
        $breadcrumbs = array_merge($breadcrumbs, array_reverse($parent_categories));
    }
    
    // Add current category
    $breadcrumbs[] = array(
        'title' => $category->name,
        'url'   => get_category_link($category->term_id),
        'type'  => 'category'
    );
    
    return $breadcrumbs;
}

/**
 * Get tag breadcrumbs
 */
function recruitpro_get_tag_breadcrumbs() {
    $breadcrumbs = array();
    $tag = get_queried_object();
    
    // Add blog page if it exists
    $blog_page_id = get_option('page_for_posts');
    if ($blog_page_id && !is_front_page()) {
        $breadcrumbs[] = array(
            'title' => get_the_title($blog_page_id),
            'url'   => get_permalink($blog_page_id),
            'type'  => 'blog'
        );
    }
    
    $breadcrumbs[] = array(
        'title' => sprintf(esc_html__('Tag: %s', 'recruitpro'), $tag->name),
        'url'   => get_tag_link($tag->term_id),
        'type'  => 'tag'
    );
    
    return $breadcrumbs;
}

/**
 * Get date archive breadcrumbs
 */
function recruitpro_get_date_breadcrumbs() {
    $breadcrumbs = array();
    
    // Add blog page if it exists
    $blog_page_id = get_option('page_for_posts');
    if ($blog_page_id && !is_front_page()) {
        $breadcrumbs[] = array(
            'title' => get_the_title($blog_page_id),
            'url'   => get_permalink($blog_page_id),
            'type'  => 'blog'
        );
    }
    
    $year = get_query_var('year');
    $month = get_query_var('monthnum');
    $day = get_query_var('day');
    
    if ($year) {
        $breadcrumbs[] = array(
            'title' => $year,
            'url'   => get_year_link($year),
            'type'  => 'year'
        );
    }
    
    if ($month) {
        $breadcrumbs[] = array(
            'title' => date_i18n('F', mktime(0, 0, 0, $month, 1)),
            'url'   => get_month_link($year, $month),
            'type'  => 'month'
        );
    }
    
    if ($day) {
        $breadcrumbs[] = array(
            'title' => $day,
            'url'   => get_day_link($year, $month, $day),
            'type'  => 'day'
        );
    }
    
    return $breadcrumbs;
}

/**
 * Get author breadcrumbs
 */
function recruitpro_get_author_breadcrumbs() {
    $breadcrumbs = array();
    $author = get_queried_object();
    
    // Add blog page if it exists
    $blog_page_id = get_option('page_for_posts');
    if ($blog_page_id && !is_front_page()) {
        $breadcrumbs[] = array(
            'title' => get_the_title($blog_page_id),
            'url'   => get_permalink($blog_page_id),
            'type'  => 'blog'
        );
    }
    
    $breadcrumbs[] = array(
        'title' => sprintf(esc_html__('Author: %s', 'recruitpro'), $author->display_name),
        'url'   => get_author_posts_url($author->ID),
        'type'  => 'author'
    );
    
    return $breadcrumbs;
}

/**
 * Get singular post/page breadcrumbs
 */
function recruitpro_get_singular_breadcrumbs($post) {
    $breadcrumbs = array();
    $post_type = get_post_type($post);
    
    if ($post_type === 'post') {
        // Regular blog post
        $blog_page_id = get_option('page_for_posts');
        if ($blog_page_id && !is_front_page()) {
            $breadcrumbs[] = array(
                'title' => get_the_title($blog_page_id),
                'url'   => get_permalink($blog_page_id),
                'type'  => 'blog'
            );
        }
        
        // Add categories
        $categories = get_the_category($post->ID);
        if (!empty($categories)) {
            $main_category = $categories[0];
            
            // Add parent categories
            if ($main_category->parent) {
                $parent_categories = array();
                $parent = $main_category->parent;
                
                while ($parent) {
                    $parent_cat = get_category($parent);
                    $parent_categories[] = array(
                        'title' => $parent_cat->name,
                        'url'   => get_category_link($parent_cat->term_id),
                        'type'  => 'category'
                    );
                    $parent = $parent_cat->parent;
                }
                
                $breadcrumbs = array_merge($breadcrumbs, array_reverse($parent_categories));
            }
            
            $breadcrumbs[] = array(
                'title' => $main_category->name,
                'url'   => get_category_link($main_category->term_id),
                'type'  => 'category'
            );
        }
    } elseif ($post_type === 'page') {
        // Page hierarchy
        $ancestors = get_post_ancestors($post);
        if (!empty($ancestors)) {
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor_id) {
                $breadcrumbs[] = array(
                    'title' => get_the_title($ancestor_id),
                    'url'   => get_permalink($ancestor_id),
                    'type'  => 'page'
                );
            }
        }
    } else {
        // Custom post type
        $breadcrumbs = array_merge($breadcrumbs, recruitpro_get_custom_post_type_breadcrumbs($post));
    }
    
    // Add current post/page
    $breadcrumbs[] = array(
        'title' => get_the_title($post),
        'url'   => get_permalink($post),
        'type'  => $post_type
    );
    
    return $breadcrumbs;
}

/**
 * Get custom post type breadcrumbs
 */
function recruitpro_get_custom_post_type_breadcrumbs($post) {
    $breadcrumbs = array();
    $post_type = get_post_type($post);
    $post_type_obj = get_post_type_object($post_type);
    
    // Add post type archive link if it has one
    if ($post_type_obj && $post_type_obj->has_archive) {
        $archive_title = $post_type_obj->labels->name;
        
        // Handle job post type specifically (when jobs plugin is active)
        if ($post_type === 'job' && function_exists('recruitpro_jobs_get_archive_title')) {
            $archive_title = recruitpro_jobs_get_archive_title();
        }
        
        $breadcrumbs[] = array(
            'title' => $archive_title,
            'url'   => get_post_type_archive_link($post_type),
            'type'  => 'post_type_archive'
        );
    }
    
    // Add custom taxonomy terms if applicable
    if ($post_type === 'job') {
        // Job categories and locations (when jobs plugin is active)
        $breadcrumbs = array_merge($breadcrumbs, recruitpro_get_job_taxonomy_breadcrumbs($post));
    }
    
    // Allow plugins to add custom post type breadcrumbs
    $breadcrumbs = apply_filters('recruitpro_custom_post_type_breadcrumbs', $breadcrumbs, $post, $post_type);
    
    return $breadcrumbs;
}

/**
 * Get job taxonomy breadcrumbs (for jobs plugin integration)
 */
function recruitpro_get_job_taxonomy_breadcrumbs($post) {
    $breadcrumbs = array();
    
    // Only proceed if jobs plugin is active
    if (!function_exists('recruitpro_jobs_get_taxonomies')) {
        return $breadcrumbs;
    }
    
    // Get primary job category
    $job_categories = wp_get_post_terms($post->ID, 'job_category');
    if (!empty($job_categories) && !is_wp_error($job_categories)) {
        $primary_category = $job_categories[0];
        
        // Add parent categories
        if ($primary_category->parent) {
            $parent_categories = array();
            $parent = $primary_category->parent;
            
            while ($parent) {
                $parent_term = get_term($parent, 'job_category');
                if ($parent_term && !is_wp_error($parent_term)) {
                    $parent_categories[] = array(
                        'title' => $parent_term->name,
                        'url'   => get_term_link($parent_term),
                        'type'  => 'job_category'
                    );
                    $parent = $parent_term->parent;
                }
            }
            
            $breadcrumbs = array_merge($breadcrumbs, array_reverse($parent_categories));
        }
        
        $breadcrumbs[] = array(
            'title' => $primary_category->name,
            'url'   => get_term_link($primary_category),
            'type'  => 'job_category'
        );
    }
    
    return $breadcrumbs;
}

/**
 * Get post type archive breadcrumbs
 */
function recruitpro_get_post_type_archive_breadcrumbs() {
    $breadcrumbs = array();
    $post_type = get_query_var('post_type');
    $post_type_obj = get_post_type_object($post_type);
    
    if ($post_type_obj) {
        $breadcrumbs[] = array(
            'title' => $post_type_obj->labels->name,
            'url'   => get_post_type_archive_link($post_type),
            'type'  => 'post_type_archive'
        );
    }
    
    return $breadcrumbs;
}

/**
 * Get taxonomy archive breadcrumbs
 */
function recruitpro_get_taxonomy_breadcrumbs() {
    $breadcrumbs = array();
    $term = get_queried_object();
    $taxonomy = $term->taxonomy;
    
    // Add post type archive if applicable
    $taxonomy_obj = get_taxonomy($taxonomy);
    if ($taxonomy_obj && !empty($taxonomy_obj->object_type)) {
        $post_type = $taxonomy_obj->object_type[0];
        $post_type_obj = get_post_type_object($post_type);
        
        if ($post_type_obj && $post_type_obj->has_archive) {
            $breadcrumbs[] = array(
                'title' => $post_type_obj->labels->name,
                'url'   => get_post_type_archive_link($post_type),
                'type'  => 'post_type_archive'
            );
        }
    }
    
    // Add parent terms
    if ($term->parent) {
        $parent_terms = array();
        $parent = $term->parent;
        
        while ($parent) {
            $parent_term = get_term($parent, $taxonomy);
            if ($parent_term && !is_wp_error($parent_term)) {
                $parent_terms[] = array(
                    'title' => $parent_term->name,
                    'url'   => get_term_link($parent_term),
                    'type'  => $taxonomy
                );
                $parent = $parent_term->parent;
            } else {
                break;
            }
        }
        
        $breadcrumbs = array_merge($breadcrumbs, array_reverse($parent_terms));
    }
    
    // Add current term
    $breadcrumbs[] = array(
        'title' => $term->name,
        'url'   => get_term_link($term),
        'type'  => $taxonomy
    );
    
    return $breadcrumbs;
}

/**
 * Build breadcrumb HTML
 */
function recruitpro_build_breadcrumb_html($breadcrumbs, $args) {
    if (empty($breadcrumbs)) {
        return '';
    }
    
    $html = '';
    $container_classes = array($args['container_class']);
    
    // Add microdata/schema support
    if ($args['schema']) {
        $container_classes[] = 'breadcrumb-schema';
    }
    
    // Container opening tag
    $container_attrs = array();
    if (!empty($args['container_id'])) {
        $container_attrs[] = 'id="' . esc_attr($args['container_id']) . '"';
    }
    if (!empty($container_classes)) {
        $container_attrs[] = 'class="' . esc_attr(implode(' ', $container_classes)) . '"';
    }
    if ($args['schema']) {
        $container_attrs[] = 'aria-label="' . esc_attr__('Breadcrumb navigation', 'recruitpro') . '"';
    }
    
    $html .= '<' . $args['container'];
    if (!empty($container_attrs)) {
        $html .= ' ' . implode(' ', $container_attrs);
    }
    $html .= '>';
    
    // Schema markup
    if ($args['schema']) {
        $html .= '<script type="application/ld+json">';
        $html .= recruitpro_get_breadcrumb_schema($breadcrumbs);
        $html .= '</script>';
    }
    
    // Breadcrumb list
    $html .= '<ol class="' . esc_attr($args['list_class']) . '">';
    
    $total_items = count($breadcrumbs);
    foreach ($breadcrumbs as $index => $breadcrumb) {
        $is_last = ($index === $total_items - 1);
        $is_current = $is_last && $args['show_current'];
        
        $item_classes = array($args['item_class']);
        if ($is_current) {
            $item_classes[] = $args['active_class'];
        }
        
        $html .= '<li class="' . esc_attr(implode(' ', $item_classes)) . '">';
        
        // Truncate title if too long
        $title = $breadcrumb['title'];
        if (strlen($title) > $args['max_length']) {
            $title = substr($title, 0, $args['max_length']) . '...';
        }
        
        if (!empty($breadcrumb['url']) && !$is_current) {
            $html .= '<a href="' . esc_url($breadcrumb['url']) . '">';
            $html .= esc_html($title);
            $html .= '</a>';
        } else {
            $html .= '<span>' . esc_html($title) . '</span>';
        }
        
        // Add separator (except for last item)
        if (!$is_last) {
            $html .= ' ' . $args['separator'] . ' ';
        }
        
        $html .= '</li>';
    }
    
    $html .= '</ol>';
    $html .= '</' . $args['container'] . '>';
    
    return $html;
}

/**
 * Generate JSON-LD schema markup for breadcrumbs
 */
function recruitpro_get_breadcrumb_schema($breadcrumbs) {
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array()
    );
    
    foreach ($breadcrumbs as $index => $breadcrumb) {
        $item = array(
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $breadcrumb['title']
        );
        
        if (!empty($breadcrumb['url'])) {
            $item['item'] = $breadcrumb['url'];
        }
        
        $schema['itemListElement'][] = $item;
    }
    
    return wp_json_encode($schema, JSON_UNESCAPED_SLASHES);
}

/**
 * Shortcode for breadcrumbs
 */
function recruitpro_breadcrumbs_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show_home'    => true,
        'show_current' => true,
        'separator'    => '/',
        'home_text'    => esc_html__('Home', 'recruitpro'),
    ), $atts, 'recruitpro_breadcrumbs');
    
    // Convert string values to boolean
    $atts['show_home'] = filter_var($atts['show_home'], FILTER_VALIDATE_BOOLEAN);
    $atts['show_current'] = filter_var($atts['show_current'], FILTER_VALIDATE_BOOLEAN);
    
    ob_start();
    recruitpro_breadcrumbs($atts);
    return ob_get_clean();
}
add_shortcode('recruitpro_breadcrumbs', 'recruitpro_breadcrumbs_shortcode');

/**
 * Widget for breadcrumbs
 */
class RecruitPro_Breadcrumbs_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'recruitpro_breadcrumbs_widget',
            esc_html__('RecruitPro Breadcrumbs', 'recruitpro'),
            array(
                'description' => esc_html__('Display breadcrumb navigation', 'recruitpro'),
                'classname' => 'recruitpro-breadcrumbs-widget'
            )
        );
    }
    
    public function widget($args, $instance) {
        // Don't show on homepage
        if (is_front_page()) {
            return;
        }
        
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $breadcrumb_args = array(
            'show_home' => !empty($instance['show_home']),
            'show_current' => !empty($instance['show_current']),
            'separator' => !empty($instance['separator']) ? $instance['separator'] : '/',
            'home_text' => !empty($instance['home_text']) ? $instance['home_text'] : esc_html__('Home', 'recruitpro'),
        );
        
        recruitpro_breadcrumbs($breadcrumb_args);
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $show_home = !empty($instance['show_home']) ? $instance['show_home'] : true;
        $show_current = !empty($instance['show_current']) ? $instance['show_current'] : true;
        $separator = !empty($instance['separator']) ? $instance['separator'] : '/';
        $home_text = !empty($instance['home_text']) ? $instance['home_text'] : esc_html__('Home', 'recruitpro');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_home); ?> id="<?php echo esc_attr($this->get_field_id('show_home')); ?>" name="<?php echo esc_attr($this->get_field_name('show_home')); ?>" value="1">
            <label for="<?php echo esc_attr($this->get_field_id('show_home')); ?>"><?php esc_html_e('Show home link', 'recruitpro'); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_current); ?> id="<?php echo esc_attr($this->get_field_id('show_current')); ?>" name="<?php echo esc_attr($this->get_field_name('show_current')); ?>" value="1">
            <label for="<?php echo esc_attr($this->get_field_id('show_current')); ?>"><?php esc_html_e('Show current page', 'recruitpro'); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('separator')); ?>"><?php esc_html_e('Separator:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('separator')); ?>" name="<?php echo esc_attr($this->get_field_name('separator')); ?>" type="text" value="<?php echo esc_attr($separator); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('home_text')); ?>"><?php esc_html_e('Home text:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('home_text')); ?>" name="<?php echo esc_attr($this->get_field_name('home_text')); ?>" type="text" value="<?php echo esc_attr($home_text); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['show_home'] = !empty($new_instance['show_home']);
        $instance['show_current'] = !empty($new_instance['show_current']);
        $instance['separator'] = !empty($new_instance['separator']) ? sanitize_text_field($new_instance['separator']) : '/';
        $instance['home_text'] = !empty($new_instance['home_text']) ? sanitize_text_field($new_instance['home_text']) : esc_html__('Home', 'recruitpro');
        
        return $instance;
    }
}

/**
 * Register breadcrumbs widget
 */
function recruitpro_register_breadcrumbs_widget() {
    register_widget('RecruitPro_Breadcrumbs_Widget');
}
add_action('widgets_init', 'recruitpro_register_breadcrumbs_widget');

/**
 * Add breadcrumbs CSS
 */
function recruitpro_breadcrumbs_styles() {
    ?>
    <style>
    .recruitpro-breadcrumbs {
        margin-bottom: 20px;
        font-size: 14px;
    }
    .recruitpro-breadcrumbs .breadcrumb-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
    }
    .recruitpro-breadcrumbs .breadcrumb-item {
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
    }
    .recruitpro-breadcrumbs .breadcrumb-separator {
        margin: 0 8px;
        color: #666;
    }
    .recruitpro-breadcrumbs a {
        color: #0073aa;
        text-decoration: none;
    }
    .recruitpro-breadcrumbs a:hover {
        text-decoration: underline;
    }
    .recruitpro-breadcrumbs .breadcrumb-active span {
        color: #333;
        font-weight: 500;
    }
    @media (max-width: 768px) {
        .recruitpro-breadcrumbs {
            font-size: 12px;
        }
        .recruitpro-breadcrumbs .breadcrumb-separator {
            margin: 0 4px;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'recruitpro_breadcrumbs_styles');

/**
 * Template tag for easy use in themes
 */
function recruitpro_the_breadcrumbs($args = array()) {
    recruitpro_breadcrumbs($args);
}

/**
 * Get breadcrumbs without displaying them
 */
function recruitpro_get_breadcrumbs($args = array()) {
    ob_start();
    recruitpro_breadcrumbs($args);
    return ob_get_clean();
}