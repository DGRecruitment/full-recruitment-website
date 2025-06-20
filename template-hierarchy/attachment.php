<?php
/**
 * The template for displaying attachment pages
 *
 * Professional attachment template for recruitment agencies handling uploaded files
 * including resumes, portfolios, certificates, company documents, and media files.
 * Features comprehensive file display, metadata, download functionality, and 
 * navigation designed specifically for recruitment and HR document management.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/attachment.php
 * Purpose: Display uploaded attachments with professional presentation
 * Dependencies: WordPress core, theme functions
 * Features: File preview, metadata display, download options, navigation
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get attachment and post data
$attachment_id = get_the_ID();
$attachment = get_post($attachment_id);
$attachment_url = wp_get_attachment_url($attachment_id);
$attachment_meta = wp_get_attachment_metadata($attachment_id);
$parent_post = get_post($attachment->post_parent);

// Get file information
$file_path = get_attached_file($attachment_id);
$file_size = $file_path ? size_format(filesize($file_path)) : '';
$file_type = get_post_mime_type($attachment_id);
$file_extension = pathinfo($attachment_url, PATHINFO_EXTENSION);

// Get customizer settings
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_attachment_sidebar_position', 'right');
$show_navigation = get_theme_mod('recruitpro_attachment_show_navigation', true);
$show_metadata = get_theme_mod('recruitpro_attachment_show_metadata', true);
$allow_downloads = get_theme_mod('recruitpro_attachment_allow_downloads', true);

// Determine attachment type category
$is_image = wp_attachment_is_image($attachment_id);
$is_pdf = ($file_type === 'application/pdf');
$is_document = in_array($file_type, array(
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'text/plain',
    'text/csv'
));
$is_archive = in_array($file_type, array('application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'));
$is_audio = strpos($file_type, 'audio/') === 0;
$is_video = strpos($file_type, 'video/') === 0;

// Get attachment navigation
$attachments = array();
if ($show_navigation && $parent_post) {
    $attachments = get_children(array(
        'post_parent' => $parent_post->ID,
        'post_status' => 'inherit',
        'post_type' => 'attachment',
        'orderby' => 'menu_order',
        'order' => 'ASC'
    ));
}

$current_position = 0;
$total_attachments = count($attachments);
if ($attachments) {
    $attachment_ids = array_keys($attachments);
    $current_position = array_search($attachment_id, $attachment_ids) + 1;
}

// Schema.org markup
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'MediaObject',
    'name' => get_the_title(),
    'description' => get_the_content() ? wp_strip_all_tags(get_the_content()) : get_the_title(),
    'url' => $attachment_url,
    'contentUrl' => $attachment_url,
    'encodingFormat' => $file_type,
    'contentSize' => $file_size,
    'uploadDate' => get_the_date('c'),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    )
);

// Add specific schema for images
if ($is_image && $attachment_meta) {
    $schema_data['@type'] = 'ImageObject';
    $schema_data['width'] = $attachment_meta['width'] ?? null;
    $schema_data['height'] = $attachment_meta['height'] ?? null;
    $schema_data['thumbnail'] = wp_get_attachment_image_url($attachment_id, 'thumbnail');
}

// File type icons mapping
$file_icons = array(
    'pdf' => 'fas fa-file-pdf',
    'doc' => 'fas fa-file-word',
    'docx' => 'fas fa-file-word',
    'xls' => 'fas fa-file-excel',
    'xlsx' => 'fas fa-file-excel',
    'ppt' => 'fas fa-file-powerpoint',
    'pptx' => 'fas fa-file-powerpoint',
    'txt' => 'fas fa-file-alt',
    'csv' => 'fas fa-file-csv',
    'zip' => 'fas fa-file-archive',
    'rar' => 'fas fa-file-archive',
    '7z' => 'fas fa-file-archive',
    'mp3' => 'fas fa-file-audio',
    'wav' => 'fas fa-file-audio',
    'mp4' => 'fas fa-file-video',
    'avi' => 'fas fa-file-video',
    'mov' => 'fas fa-file-video'
);

$file_icon = $file_icons[strtolower($file_extension)] ?? 'fas fa-file';
?>

<div id="primary" class="content-area attachment-page">
    <main id="main" class="site-main">
        
        <?php if ($show_breadcrumbs) : ?>
            <div class="breadcrumbs-wrapper">
                <div class="container">
                    <?php recruitpro_breadcrumbs(); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="container">
            <div class="attachment-wrapper">
                
                <!-- Attachment Header -->
                <header class="attachment-header">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="attachment-title-wrapper">
                                <div class="file-type-badge">
                                    <i class="<?php echo esc_attr($file_icon); ?>"></i>
                                    <span><?php echo esc_html(strtoupper($file_extension)); ?></span>
                                </div>
                                
                                <h1 class="attachment-title"><?php the_title(); ?></h1>
                                
                                <?php if (get_the_content()) : ?>
                                    <div class="attachment-description">
                                        <?php the_content(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="attachment-meta-header">
                                    <span class="upload-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php printf(esc_html__('Uploaded on %s', 'recruitpro'), get_the_date()); ?>
                                    </span>
                                    
                                    <?php if ($file_size) : ?>
                                        <span class="file-size">
                                            <i class="fas fa-hdd"></i>
                                            <?php echo esc_html($file_size); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($parent_post) : ?>
                                        <span class="parent-post">
                                            <i class="fas fa-link"></i>
                                            <?php esc_html_e('Attached to:', 'recruitpro'); ?>
                                            <a href="<?php echo esc_url(get_permalink($parent_post->ID)); ?>">
                                                <?php echo esc_html($parent_post->post_title); ?>
                                            </a>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($total_attachments > 1) : ?>
                                        <span class="attachment-position">
                                            <i class="fas fa-images"></i>
                                            <?php printf(esc_html__('File %d of %d', 'recruitpro'), $current_position, $total_attachments); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="attachment-actions">
                                <?php if ($allow_downloads) : ?>
                                    <a href="<?php echo esc_url($attachment_url); ?>" 
                                       class="btn btn-primary btn-lg download-btn" 
                                       download
                                       title="<?php esc_attr_e('Download file', 'recruitpro'); ?>">
                                        <i class="fas fa-download"></i>
                                        <?php esc_html_e('Download', 'recruitpro'); ?>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="<?php echo esc_url($attachment_url); ?>" 
                                   class="btn btn-secondary btn-lg view-btn" 
                                   target="_blank"
                                   title="<?php esc_attr_e('View file in new tab', 'recruitpro'); ?>">
                                    <i class="fas fa-external-link-alt"></i>
                                    <?php esc_html_e('View', 'recruitpro'); ?>
                                </a>
                                
                                <button class="btn btn-outline-primary btn-lg share-btn" 
                                        title="<?php esc_attr_e('Share file', 'recruitpro'); ?>">
                                    <i class="fas fa-share-alt"></i>
                                    <?php esc_html_e('Share', 'recruitpro'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="attachment-content">
                    <div class="row">
                        
                        <!-- Main Content -->
                        <div class="<?php echo ($sidebar_position === 'left') ? 'col-lg-9 order-2' : (($sidebar_position === 'right') ? 'col-lg-9' : 'col-12'); ?>">
                            
                            <!-- File Preview Section -->
                            <section class="file-preview-section">
                                <div class="file-preview-wrapper">
                                    
                                    <?php if ($is_image) : ?>
                                        <!-- Image Preview -->
                                        <div class="image-preview">
                                            <figure class="attachment-image">
                                                <?php echo wp_get_attachment_image($attachment_id, 'full', false, array(
                                                    'class' => 'img-fluid',
                                                    'alt' => get_the_title()
                                                )); ?>
                                                <figcaption class="image-caption">
                                                    <?php echo esc_html(get_the_title()); ?>
                                                    <?php if ($attachment_meta) : ?>
                                                        <span class="image-dimensions">
                                                            (<?php echo esc_html($attachment_meta['width']); ?> × <?php echo esc_html($attachment_meta['height']); ?> <?php esc_html_e('pixels', 'recruitpro'); ?>)
                                                        </span>
                                                    <?php endif; ?>
                                                </figcaption>
                                            </figure>
                                        </div>
                                        
                                    <?php elseif ($is_pdf) : ?>
                                        <!-- PDF Preview -->
                                        <div class="pdf-preview">
                                            <div class="pdf-embed-wrapper">
                                                <iframe src="<?php echo esc_url($attachment_url); ?>#toolbar=1&navpanes=1&scrollbar=1" 
                                                        class="pdf-embed"
                                                        title="<?php echo esc_attr(get_the_title()); ?>">
                                                    <p><?php esc_html_e('Your browser does not support PDFs.', 'recruitpro'); ?> 
                                                       <a href="<?php echo esc_url($attachment_url); ?>" target="_blank">
                                                           <?php esc_html_e('Download the PDF', 'recruitpro'); ?>
                                                       </a>
                                                    </p>
                                                </iframe>
                                            </div>
                                            <div class="pdf-controls">
                                                <button class="btn btn-sm btn-secondary" onclick="document.querySelector('.pdf-embed').contentWindow.print();">
                                                    <i class="fas fa-print"></i>
                                                    <?php esc_html_e('Print', 'recruitpro'); ?>
                                                </button>
                                                <button class="btn btn-sm btn-secondary fullscreen-btn">
                                                    <i class="fas fa-expand"></i>
                                                    <?php esc_html_e('Fullscreen', 'recruitpro'); ?>
                                                </button>
                                            </div>
                                        </div>
                                        
                                    <?php elseif ($is_audio) : ?>
                                        <!-- Audio Preview -->
                                        <div class="audio-preview">
                                            <div class="audio-player-wrapper">
                                                <audio controls preload="metadata" class="audio-player">
                                                    <source src="<?php echo esc_url($attachment_url); ?>" type="<?php echo esc_attr($file_type); ?>">
                                                    <?php esc_html_e('Your browser does not support the audio element.', 'recruitpro'); ?>
                                                </audio>
                                            </div>
                                        </div>
                                        
                                    <?php elseif ($is_video) : ?>
                                        <!-- Video Preview -->
                                        <div class="video-preview">
                                            <div class="video-player-wrapper">
                                                <video controls preload="metadata" class="video-player">
                                                    <source src="<?php echo esc_url($attachment_url); ?>" type="<?php echo esc_attr($file_type); ?>">
                                                    <?php esc_html_e('Your browser does not support the video element.', 'recruitpro'); ?>
                                                </video>
                                            </div>
                                        </div>
                                        
                                    <?php else : ?>
                                        <!-- Generic File Preview -->
                                        <div class="file-preview-generic">
                                            <div class="file-icon-large">
                                                <i class="<?php echo esc_attr($file_icon); ?>"></i>
                                            </div>
                                            <h3 class="file-name"><?php echo esc_html(basename($attachment_url)); ?></h3>
                                            <p class="file-info">
                                                <?php printf(esc_html__('%s file', 'recruitpro'), esc_html(strtoupper($file_extension))); ?>
                                                <?php if ($file_size) : ?>
                                                    • <?php echo esc_html($file_size); ?>
                                                <?php endif; ?>
                                            </p>
                                            
                                            <?php if ($is_document) : ?>
                                                <div class="document-notice">
                                                    <i class="fas fa-info-circle"></i>
                                                    <p><?php esc_html_e('This document can be downloaded and opened with compatible software.', 'recruitpro'); ?></p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                </div>
                            </section>

                            <!-- File Metadata -->
                            <?php if ($show_metadata) : ?>
                                <section class="file-metadata-section">
                                    <h2 class="section-title"><?php esc_html_e('File Information', 'recruitpro'); ?></h2>
                                    <div class="metadata-grid">
                                        <div class="metadata-item">
                                            <span class="metadata-label"><?php esc_html_e('File Name:', 'recruitpro'); ?></span>
                                            <span class="metadata-value"><?php echo esc_html(basename($attachment_url)); ?></span>
                                        </div>
                                        
                                        <div class="metadata-item">
                                            <span class="metadata-label"><?php esc_html_e('File Type:', 'recruitpro'); ?></span>
                                            <span class="metadata-value"><?php echo esc_html($file_type); ?></span>
                                        </div>
                                        
                                        <?php if ($file_size) : ?>
                                            <div class="metadata-item">
                                                <span class="metadata-label"><?php esc_html_e('File Size:', 'recruitpro'); ?></span>
                                                <span class="metadata-value"><?php echo esc_html($file_size); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="metadata-item">
                                            <span class="metadata-label"><?php esc_html_e('Upload Date:', 'recruitpro'); ?></span>
                                            <span class="metadata-value"><?php echo esc_html(get_the_date('F j, Y g:i A')); ?></span>
                                        </div>
                                        
                                        <?php if ($is_image && $attachment_meta) : ?>
                                            <div class="metadata-item">
                                                <span class="metadata-label"><?php esc_html_e('Dimensions:', 'recruitpro'); ?></span>
                                                <span class="metadata-value"><?php echo esc_html($attachment_meta['width']); ?> × <?php echo esc_html($attachment_meta['height']); ?> <?php esc_html_e('pixels', 'recruitpro'); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php
                                        // Get author
                                        $author_id = get_the_author_meta('ID');
                                        if ($author_id) :
                                        ?>
                                            <div class="metadata-item">
                                                <span class="metadata-label"><?php esc_html_e('Uploaded by:', 'recruitpro'); ?></span>
                                                <span class="metadata-value">
                                                    <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>">
                                                        <?php echo esc_html(get_the_author()); ?>
                                                    </a>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($attachment->post_excerpt) : ?>
                                            <div class="metadata-item full-width">
                                                <span class="metadata-label"><?php esc_html_e('Caption:', 'recruitpro'); ?></span>
                                                <span class="metadata-value"><?php echo wp_kses_post($attachment->post_excerpt); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php
                                        // Get alt text for images
                                        if ($is_image) {
                                            $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
                                            if ($alt_text) :
                                            ?>
                                                <div class="metadata-item full-width">
                                                    <span class="metadata-label"><?php esc_html_e('Alt Text:', 'recruitpro'); ?></span>
                                                    <span class="metadata-value"><?php echo esc_html($alt_text); ?></span>
                                                </div>
                                            <?php endif;
                                        }
                                        ?>
                                    </div>
                                </section>
                            <?php endif; ?>

                            <!-- Attachment Navigation -->
                            <?php if ($show_navigation && $total_attachments > 1) : ?>
                                <section class="attachment-navigation-section">
                                    <h2 class="section-title"><?php esc_html_e('Other Attachments', 'recruitpro'); ?></h2>
                                    <div class="attachment-navigation">
                                        <?php
                                        $prev_attachment = null;
                                        $next_attachment = null;
                                        $attachment_ids = array_keys($attachments);
                                        $current_index = array_search($attachment_id, $attachment_ids);
                                        
                                        if ($current_index > 0) {
                                            $prev_attachment = $attachments[$attachment_ids[$current_index - 1]];
                                        }
                                        if ($current_index < count($attachment_ids) - 1) {
                                            $next_attachment = $attachments[$attachment_ids[$current_index + 1]];
                                        }
                                        ?>
                                        
                                        <div class="nav-links">
                                            <?php if ($prev_attachment) : ?>
                                                <div class="nav-previous">
                                                    <a href="<?php echo esc_url(get_attachment_link($prev_attachment->ID)); ?>" class="nav-link">
                                                        <i class="fas fa-chevron-left"></i>
                                                        <div class="nav-content">
                                                            <span class="nav-label"><?php esc_html_e('Previous', 'recruitpro'); ?></span>
                                                            <span class="nav-title"><?php echo esc_html($prev_attachment->post_title); ?></span>
                                                        </div>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($next_attachment) : ?>
                                                <div class="nav-next">
                                                    <a href="<?php echo esc_url(get_attachment_link($next_attachment->ID)); ?>" class="nav-link">
                                                        <div class="nav-content">
                                                            <span class="nav-label"><?php esc_html_e('Next', 'recruitpro'); ?></span>
                                                            <span class="nav-title"><?php echo esc_html($next_attachment->post_title); ?></span>
                                                        </div>
                                                        <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Thumbnail Gallery -->
                                        <div class="attachment-thumbnails">
                                            <?php foreach ($attachments as $att_id => $att) : ?>
                                                <div class="thumbnail-item <?php echo ($att_id == $attachment_id) ? 'current' : ''; ?>">
                                                    <a href="<?php echo esc_url(get_attachment_link($att_id)); ?>">
                                                        <?php if (wp_attachment_is_image($att_id)) : ?>
                                                            <?php echo wp_get_attachment_image($att_id, 'thumbnail', false, array('alt' => $att->post_title)); ?>
                                                        <?php else : ?>
                                                            <div class="file-thumbnail">
                                                                <?php 
                                                                $thumb_extension = pathinfo(wp_get_attachment_url($att_id), PATHINFO_EXTENSION);
                                                                $thumb_icon = $file_icons[strtolower($thumb_extension)] ?? 'fas fa-file';
                                                                ?>
                                                                <i class="<?php echo esc_attr($thumb_icon); ?>"></i>
                                                                <span class="file-ext"><?php echo esc_html(strtoupper($thumb_extension)); ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <span class="thumbnail-title"><?php echo esc_html($att->post_title); ?></span>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </section>
                            <?php endif; ?>

                        </div>

                        <!-- Sidebar -->
                        <?php if ($sidebar_position !== 'none') : ?>
                            <aside class="<?php echo ($sidebar_position === 'left') ? 'col-lg-3 order-1' : 'col-lg-3'; ?> sidebar attachment-sidebar">
                                
                                <!-- File Actions Widget -->
                                <div class="widget file-actions-widget">
                                    <h3 class="widget-title"><?php esc_html_e('File Actions', 'recruitpro'); ?></h3>
                                    <div class="widget-content">
                                        <div class="action-buttons">
                                            <?php if ($allow_downloads) : ?>
                                                <a href="<?php echo esc_url($attachment_url); ?>" 
                                                   class="btn btn-primary btn-block" 
                                                   download>
                                                    <i class="fas fa-download"></i>
                                                    <?php esc_html_e('Download File', 'recruitpro'); ?>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="<?php echo esc_url($attachment_url); ?>" 
                                               class="btn btn-secondary btn-block" 
                                               target="_blank">
                                                <i class="fas fa-external-link-alt"></i>
                                                <?php esc_html_e('Open in New Tab', 'recruitpro'); ?>
                                            </a>
                                            
                                            <button class="btn btn-outline-primary btn-block copy-link-btn">
                                                <i class="fas fa-copy"></i>
                                                <?php esc_html_e('Copy Link', 'recruitpro'); ?>
                                            </button>
                                        </div>
                                        
                                        <div class="quick-info">
                                            <div class="info-item">
                                                <span class="info-label"><?php esc_html_e('File Type:', 'recruitpro'); ?></span>
                                                <span class="info-value"><?php echo esc_html(strtoupper($file_extension)); ?></span>
                                            </div>
                                            
                                            <?php if ($file_size) : ?>
                                                <div class="info-item">
                                                    <span class="info-label"><?php esc_html_e('Size:', 'recruitpro'); ?></span>
                                                    <span class="info-value"><?php echo esc_html($file_size); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="info-item">
                                                <span class="info-label"><?php esc_html_e('Uploaded:', 'recruitpro'); ?></span>
                                                <span class="info-value"><?php echo esc_html(get_the_date('M j, Y')); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Parent Post Widget -->
                                <?php if ($parent_post) : ?>
                                    <div class="widget parent-post-widget">
                                        <h3 class="widget-title"><?php esc_html_e('Attached To', 'recruitpro'); ?></h3>
                                        <div class="widget-content">
                                            <div class="parent-post-item">
                                                <?php if (has_post_thumbnail($parent_post->ID)) : ?>
                                                    <div class="parent-post-thumbnail">
                                                        <a href="<?php echo esc_url(get_permalink($parent_post->ID)); ?>">
                                                            <?php echo get_the_post_thumbnail($parent_post->ID, 'thumbnail'); ?>
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="parent-post-content">
                                                    <h4 class="parent-post-title">
                                                        <a href="<?php echo esc_url(get_permalink($parent_post->ID)); ?>">
                                                            <?php echo esc_html($parent_post->post_title); ?>
                                                        </a>
                                                    </h4>
                                                    <div class="parent-post-meta">
                                                        <span class="post-type">
                                                            <?php echo esc_html(get_post_type_object($parent_post->post_type)->labels->singular_name); ?>
                                                        </span>
                                                        <span class="post-date">
                                                            <?php echo esc_html(get_the_date('', $parent_post->ID)); ?>
                                                        </span>
                                                    </div>
                                                    <div class="parent-post-excerpt">
                                                        <?php echo wp_trim_words(get_the_excerpt($parent_post->ID), 15, '...'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Recent Attachments Widget -->
                                <div class="widget recent-attachments-widget">
                                    <h3 class="widget-title"><?php esc_html_e('Recent Files', 'recruitpro'); ?></h3>
                                    <div class="widget-content">
                                        <?php
                                        $recent_attachments = get_posts(array(
                                            'post_type' => 'attachment',
                                            'post_status' => 'inherit',
                                            'numberposts' => 5,
                                            'orderby' => 'date',
                                            'order' => 'DESC',
                                            'exclude' => array($attachment_id)
                                        ));
                                        
                                        if ($recent_attachments) :
                                        ?>
                                            <ul class="recent-attachments-list">
                                                <?php foreach ($recent_attachments as $recent_att) : ?>
                                                    <li class="recent-attachment-item">
                                                        <a href="<?php echo esc_url(get_attachment_link($recent_att->ID)); ?>">
                                                            <div class="attachment-icon">
                                                                <?php if (wp_attachment_is_image($recent_att->ID)) : ?>
                                                                    <?php echo wp_get_attachment_image($recent_att->ID, 'thumbnail'); ?>
                                                                <?php else : ?>
                                                                    <?php 
                                                                    $recent_ext = pathinfo(wp_get_attachment_url($recent_att->ID), PATHINFO_EXTENSION);
                                                                    $recent_icon = $file_icons[strtolower($recent_ext)] ?? 'fas fa-file';
                                                                    ?>
                                                                    <i class="<?php echo esc_attr($recent_icon); ?>"></i>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="attachment-info">
                                                                <span class="attachment-title"><?php echo esc_html($recent_att->post_title); ?></span>
                                                                <span class="attachment-date"><?php echo esc_html(get_the_date('M j', $recent_att->ID)); ?></span>
                                                            </div>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if (is_active_sidebar('attachment-sidebar')) : ?>
                                    <?php dynamic_sidebar('attachment-sidebar'); ?>
                                <?php endif; ?>
                                
                            </aside>
                        <?php endif; ?>
                        
                    </div>
                </div>
                
            </div>
        </div>
        
    </main>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel"><?php esc_html_e('Share File', 'recruitpro'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php esc_attr_e('Close', 'recruitpro'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="share-options">
                    <div class="share-link">
                        <label for="share-url"><?php esc_html_e('File URL:', 'recruitpro'); ?></label>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="share-url" 
                                   value="<?php echo esc_url(get_attachment_link($attachment_id)); ?>" 
                                   readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary copy-url-btn" type="button">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-share">
                        <h6><?php esc_html_e('Share on Social Media:', 'recruitpro'); ?></h6>
                        <div class="social-buttons">
                            <a href="#" class="social-btn linkedin-btn" data-platform="linkedin">
                                <i class="fab fa-linkedin"></i>
                                LinkedIn
                            </a>
                            <a href="#" class="social-btn twitter-btn" data-platform="twitter">
                                <i class="fab fa-twitter"></i>
                                Twitter
                            </a>
                            <a href="#" class="social-btn facebook-btn" data-platform="facebook">
                                <i class="fab fa-facebook"></i>
                                Facebook
                            </a>
                            <a href="mailto:?subject=<?php echo urlencode(get_the_title()); ?>&body=<?php echo urlencode(get_attachment_link($attachment_id)); ?>" 
                               class="social-btn email-btn">
                                <i class="fas fa-envelope"></i>
                                Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<style>
/* Attachment Page Specific Styles */
.attachment-page {
    background: #f8f9fa;
    min-height: 80vh;
}

.attachment-wrapper {
    padding: 2rem 0;
}

/* Attachment Header */
.attachment-header {
    background: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.file-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.attachment-title {
    font-size: 2.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.attachment-description {
    font-size: 1.1rem;
    color: #6c757d;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.attachment-meta-header {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    align-items: center;
    font-size: 0.95rem;
    color: #495057;
}

.attachment-meta-header span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.attachment-meta-header a {
    color: var(--primary-color);
    text-decoration: none;
    transition: color 0.3s ease;
}

.attachment-meta-header a:hover {
    color: var(--secondary-color);
}

.attachment-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
}

.attachment-actions .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

/* File Preview */
.file-preview-section {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
}

.file-preview-wrapper {
    position: relative;
}

/* Image Preview */
.image-preview {
    text-align: center;
    padding: 2rem;
}

.attachment-image {
    margin: 0;
    display: inline-block;
}

.attachment-image img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.image-caption {
    margin-top: 1rem;
    font-size: 0.95rem;
    color: #6c757d;
}

.image-dimensions {
    font-size: 0.85rem;
    opacity: 0.8;
}

/* PDF Preview */
.pdf-preview {
    position: relative;
}

.pdf-embed-wrapper {
    position: relative;
    height: 600px;
    background: #f8f9fa;
}

.pdf-embed {
    width: 100%;
    height: 100%;
    border: none;
    background: white;
}

.pdf-controls {
    padding: 1rem;
    background: #f8f9fa;
    border-top: 1px solid #e1e5e9;
    display: flex;
    gap: 1rem;
}

/* Audio/Video Preview */
.audio-preview,
.video-preview {
    padding: 2rem;
    text-align: center;
}

.audio-player,
.video-player {
    width: 100%;
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* Generic File Preview */
.file-preview-generic {
    padding: 4rem 2rem;
    text-align: center;
}

.file-icon-large {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    color: white;
    font-size: 3rem;
}

.file-name {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
    word-break: break-word;
}

.file-info {
    font-size: 1.1rem;
    color: #6c757d;
    margin-bottom: 2rem;
}

.document-notice {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 8px;
    padding: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    text-align: left;
    max-width: 500px;
    margin: 0 auto;
}

.document-notice i {
    color: #1976d2;
    font-size: 1.2rem;
    flex-shrink: 0;
    margin-top: 0.2rem;
}

.document-notice p {
    margin: 0;
    color: #1565c0;
}

/* File Metadata */
.file-metadata-section {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

.metadata-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.metadata-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.metadata-item.full-width {
    grid-column: 1 / -1;
}

.metadata-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.metadata-value {
    color: #2c3e50;
    font-size: 1rem;
    word-break: break-word;
}

.metadata-value a {
    color: var(--primary-color);
    text-decoration: none;
    transition: color 0.3s ease;
}

.metadata-value a:hover {
    color: var(--secondary-color);
}

/* Attachment Navigation */
.attachment-navigation-section {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.nav-links {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.nav-previous,
.nav-next {
    flex: 1;
    max-width: 45%;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    color: #495057;
    text-decoration: none;
    transition: all 0.3s ease;
}

.nav-link:hover {
    background: var(--primary-color);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.nav-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.nav-label {
    font-size: 0.8rem;
    text-transform: uppercase;
    opacity: 0.8;
}

.nav-title {
    font-weight: 600;
}

.attachment-thumbnails {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
}

.thumbnail-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.thumbnail-item:hover {
    transform: translateY(-3px);
}

.thumbnail-item.current {
    border: 3px solid var(--primary-color);
}

.thumbnail-item a {
    display: block;
    text-decoration: none;
    color: inherit;
}

.thumbnail-item img {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
}

.file-thumbnail {
    width: 100%;
    height: 80px;
    background: #f8f9fa;
    border: 1px solid #e1e5e9;
    border-radius: 4px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.file-thumbnail i {
    font-size: 1.5rem;
    color: var(--primary-color);
}

.file-ext {
    font-size: 0.7rem;
    font-weight: 600;
    color: #6c757d;
}

.thumbnail-title {
    display: block;
    padding: 0.5rem;
    font-size: 0.8rem;
    text-align: center;
    word-break: break-word;
    line-height: 1.3;
}

/* Sidebar */
.attachment-sidebar .widget {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.attachment-sidebar .widget-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.quick-info {
    border-top: 1px solid #f1f3f4;
    padding-top: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 0.9rem;
    color: #6c757d;
}

.info-value {
    font-weight: 600;
    color: #2c3e50;
}

.parent-post-item {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.parent-post-thumbnail {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    border-radius: 6px;
    overflow: hidden;
}

.parent-post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.parent-post-content {
    flex: 1;
}

.parent-post-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    line-height: 1.3;
}

.parent-post-title a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.parent-post-title a:hover {
    color: var(--primary-color);
}

.parent-post-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
    font-size: 0.8rem;
    color: #6c757d;
}

.parent-post-excerpt {
    font-size: 0.9rem;
    color: #6c757d;
    line-height: 1.4;
}

.recent-attachments-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.recent-attachment-item {
    border-bottom: 1px solid #f1f3f4;
    padding: 0.75rem 0;
}

.recent-attachment-item:last-child {
    border-bottom: none;
}

.recent-attachment-item a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.recent-attachment-item a:hover {
    color: var(--primary-color);
}

.attachment-icon {
    width: 40px;
    height: 40px;
    border-radius: 4px;
    overflow: hidden;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.attachment-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.attachment-icon i {
    font-size: 1.2rem;
    color: var(--primary-color);
}

.attachment-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.attachment-title {
    font-size: 0.9rem;
    font-weight: 600;
    line-height: 1.3;
}

.attachment-date {
    font-size: 0.8rem;
    color: #6c757d;
}

/* Share Modal */
.share-options {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.share-link label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #495057;
}

.social-share h6 {
    margin-bottom: 1rem;
    color: #495057;
}

.social-buttons {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.social-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.linkedin-btn {
    background: #0077b5;
    color: white;
}

.twitter-btn {
    background: #1da1f2;
    color: white;
}

.facebook-btn {
    background: #1877f2;
    color: white;
}

.email-btn {
    background: #6c757d;
    color: white;
}

.social-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    color: white;
    text-decoration: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .attachment-title {
        font-size: 1.8rem;
    }
    
    .attachment-meta-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .attachment-actions {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .attachment-actions .btn {
        flex: 1;
        min-width: calc(50% - 0.25rem);
    }
    
    .metadata-grid {
        grid-template-columns: 1fr;
    }
    
    .nav-links {
        flex-direction: column;
        gap: 1rem;
    }
    
    .nav-previous,
    .nav-next {
        max-width: 100%;
    }
    
    .attachment-thumbnails {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
    
    .parent-post-item {
        flex-direction: column;
        text-align: center;
    }
    
    .social-buttons {
        grid-template-columns: 1fr;
    }
    
    .pdf-embed-wrapper {
        height: 400px;
    }
}

@media (max-width: 576px) {
    .attachment-wrapper {
        padding: 1rem 0;
    }
    
    .attachment-header {
        padding: 2rem 0;
    }
    
    .attachment-title {
        font-size: 1.5rem;
    }
    
    .file-preview-generic {
        padding: 2rem 1rem;
    }
    
    .file-icon-large {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }
    
    .attachment-actions .btn {
        min-width: 100%;
    }
    
    .pdf-embed-wrapper {
        height: 300px;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    .thumbnail-item,
    .nav-link,
    .social-btn {
        transition: none;
    }
    
    .thumbnail-item:hover,
    .nav-link:hover,
    .social-btn:hover {
        transform: none;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .file-preview-section,
    .file-metadata-section,
    .attachment-navigation-section,
    .attachment-sidebar .widget {
        border: 2px solid #000;
    }
    
    .nav-link {
        border: 2px solid #000;
    }
}

/* Print styles */
@media print {
    .attachment-actions,
    .pdf-controls,
    .attachment-navigation-section,
    .attachment-sidebar {
        display: none;
    }
    
    .file-preview-section {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .attachment-header {
        background: white;
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Copy link functionality
    const copyLinkBtns = document.querySelectorAll('.copy-link-btn');
    copyLinkBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = '<?php echo esc_js($attachment_url); ?>';
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    showCopyNotification('Link copied to clipboard!');
                }).catch(() => {
                    fallbackCopyText(url);
                });
            } else {
                fallbackCopyText(url);
            }
        });
    });
    
    // Copy URL from modal
    const copyUrlBtn = document.querySelector('.copy-url-btn');
    if (copyUrlBtn) {
        copyUrlBtn.addEventListener('click', function() {
            const input = document.getElementById('share-url');
            input.select();
            document.execCommand('copy');
            showCopyNotification('URL copied to clipboard!');
        });
    }
    
    // Fallback copy function
    function fallbackCopyText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showCopyNotification('Link copied to clipboard!');
        } catch (err) {
            console.error('Failed to copy text: ', err);
            showCopyNotification('Failed to copy link', 'error');
        }
        
        document.body.removeChild(textArea);
    }
    
    // Show copy notification
    function showCopyNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `copy-notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 6px;
            z-index: 9999;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateY(0)';
            notification.style.opacity = '1';
        }, 100);
        
        setTimeout(() => {
            notification.style.transform = 'translateY(-20px)';
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    // Share modal functionality
    const shareBtn = document.querySelector('.share-btn');
    const shareModal = document.getElementById('shareModal');
    
    if (shareBtn && shareModal) {
        shareBtn.addEventListener('click', function(e) {
            e.preventDefault();
            $(shareModal).modal('show');
        });
    }
    
    // Social media sharing
    const socialBtns = document.querySelectorAll('.social-btn[data-platform]');
    socialBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const platform = this.dataset.platform;
            const url = encodeURIComponent('<?php echo esc_js(get_attachment_link($attachment_id)); ?>');
            const title = encodeURIComponent('<?php echo esc_js(get_the_title()); ?>');
            
            let shareUrl = '';
            
            switch(platform) {
                case 'linkedin':
                    shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                    break;
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                    break;
            }
            
            if (shareUrl) {
                window.open(shareUrl, 'share', 'width=600,height=400,scrollbars=yes,resizable=yes');
            }
        });
    });
    
    // Fullscreen PDF viewer
    const fullscreenBtn = document.querySelector('.fullscreen-btn');
    if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', function() {
            const pdfEmbed = document.querySelector('.pdf-embed');
            if (pdfEmbed.requestFullscreen) {
                pdfEmbed.requestFullscreen();
            } else if (pdfEmbed.webkitRequestFullscreen) {
                pdfEmbed.webkitRequestFullscreen();
            } else if (pdfEmbed.msRequestFullscreen) {
                pdfEmbed.msRequestFullscreen();
            }
        });
    }
    
    // Image zoom functionality for images
    const attachmentImage = document.querySelector('.attachment-image img');
    if (attachmentImage) {
        attachmentImage.addEventListener('click', function() {
            const modal = document.createElement('div');
            modal.className = 'image-zoom-modal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.9);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                cursor: zoom-out;
            `;
            
            const zoomedImage = this.cloneNode();
            zoomedImage.style.cssText = `
                max-width: 90%;
                max-height: 90%;
                object-fit: contain;
                box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            `;
            
            modal.appendChild(zoomedImage);
            document.body.appendChild(modal);
            
            modal.addEventListener('click', function() {
                document.body.removeChild(modal);
            });
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (document.body.contains(modal)) {
                        document.body.removeChild(modal);
                    }
                }
            });
        });
        
        attachmentImage.style.cursor = 'zoom-in';
    }
    
    // Track file interactions
    if (typeof gtag !== 'undefined') {
        // Track download clicks
        const downloadBtns = document.querySelectorAll('.download-btn');
        downloadBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                gtag('event', 'file_download', {
                    'file_name': '<?php echo esc_js(basename($attachment_url)); ?>',
                    'file_type': '<?php echo esc_js($file_extension); ?>',
                    'file_size': '<?php echo esc_js($file_size); ?>'
                });
            });
        });
        
        // Track view clicks
        const viewBtns = document.querySelectorAll('.view-btn');
        viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                gtag('event', 'file_view', {
                    'file_name': '<?php echo esc_js(basename($attachment_url)); ?>',
                    'file_type': '<?php echo esc_js($file_extension); ?>'
                });
            });
        });
        
        // Track share events
        if (shareBtn) {
            shareBtn.addEventListener('click', function() {
                gtag('event', 'file_share_modal_open', {
                    'file_name': '<?php echo esc_js(basename($attachment_url)); ?>'
                });
            });
        }
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        // Navigate between attachments with arrow keys
        if (e.key === 'ArrowLeft') {
            const prevLink = document.querySelector('.nav-previous a');
            if (prevLink) {
                window.location.href = prevLink.href;
            }
        } else if (e.key === 'ArrowRight') {
            const nextLink = document.querySelector('.nav-next a');
            if (nextLink) {
                window.location.href = nextLink.href;
            }
        }
        
        // Download with 'D' key
        if (e.key === 'd' || e.key === 'D') {
            const downloadBtn = document.querySelector('.download-btn');
            if (downloadBtn && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                downloadBtn.click();
            }
        }
    });
    
    // Lazy loading for thumbnails
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                }
            });
        });
        
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
});
</script>

<?php get_footer(); ?>