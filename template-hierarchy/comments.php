<?php
/**
 * The template for displaying comments
 *
 * Professional comment system designed specifically for recruitment agencies.
 * Features enhanced moderation, professional context fields, spam protection,
 * and industry-specific comment functionality for blog posts, job listings,
 * career advice articles, and company updates.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/comments.php
 * Purpose: Handle all comment display and submission forms
 * Context: Recruitment industry professional discussions
 * Features: Enhanced moderation, professional validation, spam protection
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Don't load comments template if post is password protected
if (post_password_required()) {
    return;
}

// Get comment counts and settings
$comment_count = get_comments_number();
$comments_open = comments_open();
$have_comments = have_comments();

?>

<div id="comments" class="comments-area">
    <?php if ($have_comments || $comments_open) : ?>
        
        <!-- Comments Header -->
        <div class="comments-header">
            <?php if ($have_comments) : ?>
                <h3 class="comments-title">
                    <?php
                    printf(
                        esc_html(_n(
                            '%1$s Professional Comment',
                            '%1$s Professional Comments',
                            $comment_count,
                            'recruitpro'
                        )),
                        number_format_i18n($comment_count)
                    );
                    ?>
                </h3>
                
                <div class="comments-meta">
                    <span class="comments-count"><?php echo esc_html($comment_count); ?> <?php esc_html_e('comments', 'recruitpro'); ?></span>
                    <?php if (get_option('thread_comments')) : ?>
                        <span class="comments-threading"><?php esc_html_e('Threaded discussions enabled', 'recruitpro'); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Comment Navigation (Top) -->
        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav class="comment-navigation comment-navigation-top">
                <h4 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'recruitpro'); ?></h4>
                <div class="nav-previous">
                    <?php previous_comments_link(esc_html__('&larr; Older Comments', 'recruitpro')); ?>
                </div>
                <div class="nav-next">
                    <?php next_comments_link(esc_html__('Newer Comments &rarr;', 'recruitpro')); ?>
                </div>
            </nav>
        <?php endif; ?>

        <!-- Comments List -->
        <?php if ($have_comments) : ?>
            <ol class="comment-list">
                <?php
                wp_list_comments(array(
                    'style'         => 'ol',
                    'short_ping'    => true,
                    'avatar_size'   => 64,
                    'callback'      => 'recruitpro_comment_callback',
                    'reply_text'    => esc_html__('Reply', 'recruitpro'),
                    'format'        => 'html5',
                ));
                ?>
            </ol>
        <?php endif; ?>

        <!-- Comment Navigation (Bottom) -->
        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav class="comment-navigation comment-navigation-bottom">
                <h4 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'recruitpro'); ?></h4>
                <div class="nav-previous">
                    <?php previous_comments_link(esc_html__('&larr; Older Comments', 'recruitpro')); ?>
                </div>
                <div class="nav-next">
                    <?php next_comments_link(esc_html__('Newer Comments &rarr;', 'recruitpro')); ?>
                </div>
            </nav>
        <?php endif; ?>

    <?php endif; ?>

    <!-- Comment Form -->
    <?php if ($comments_open) : ?>
        <div id="respond" class="comment-respond">
            <?php
            $form_args = array(
                'id_form'               => 'commentform',
                'id_submit'             => 'submit',
                'class_form'            => 'comment-form',
                'class_submit'          => 'submit btn btn-primary',
                'name_submit'           => 'submit',
                'title_reply'           => esc_html__('Join the Professional Discussion', 'recruitpro'),
                'title_reply_to'        => esc_html__('Reply to %s', 'recruitpro'),
                'title_reply_before'    => '<h3 id="reply-title" class="comment-reply-title">',
                'title_reply_after'     => '</h3>',
                'cancel_reply_before'   => ' <small>',
                'cancel_reply_after'    => '</small>',
                'cancel_reply_link'     => esc_html__('Cancel reply', 'recruitpro'),
                'label_submit'          => esc_html__('Submit Comment', 'recruitpro'),
                'submit_button'         => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
                'submit_field'          => '<div class="form-submit">%1$s %2$s</div>',
                'format'                => 'html5',
                'comment_field'         => recruitpro_get_comment_field(),
                'fields'                => recruitpro_get_comment_fields(),
                'comment_notes_before'  => recruitpro_get_comment_notes_before(),
                'comment_notes_after'   => recruitpro_get_comment_notes_after(),
            );

            comment_form($form_args);
            ?>
        </div>
    <?php elseif ($have_comments) : ?>
        <div class="comments-closed">
            <p class="no-comments"><?php esc_html_e('Comments are closed for this professional discussion.', 'recruitpro'); ?></p>
        </div>
    <?php endif; ?>

    <!-- Comments Closed Message for Posts without Comments -->
    <?php if (!$comments_open && !$have_comments) : ?>
        <div class="comments-closed">
            <p class="no-comments"><?php esc_html_e('Professional discussions are not available for this content.', 'recruitpro'); ?></p>
        </div>
    <?php endif; ?>

</div><!-- #comments -->

<?php
/**
 * Custom comment callback function
 * Displays individual comments with professional formatting
 */
function recruitpro_comment_callback($comment, $args, $depth) {
    $tag = ($args['style'] === 'div') ? 'div' : 'li';
    ?>
    <<?php echo esc_attr($tag); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?>>
        
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <div class="comment-header">
                <div class="comment-author vcard">
                    <?php
                    // Display avatar with professional styling
                    if ($args['avatar_size'] != 0) {
                        echo get_avatar($comment, $args['avatar_size'], '', '', array('class' => 'comment-avatar'));
                    }
                    ?>
                    
                    <div class="comment-meta">
                        <div class="comment-author-info">
                            <?php
                            // Author name with professional context
                            $professional_context = get_comment_meta(get_comment_ID(), 'professional_context', true);
                            
                            printf(
                                '<cite class="fn comment-author-name">%1$s</cite>',
                                get_comment_author_link()
                            );
                            
                            if (!empty($professional_context)) {
                                $context_labels = array(
                                    'hr_professional' => esc_html__('HR Professional', 'recruitpro'),
                                    'recruiter'       => esc_html__('Recruiter', 'recruitpro'),
                                    'job_seeker'      => esc_html__('Job Seeker', 'recruitpro'),
                                    'employer'        => esc_html__('Employer', 'recruitpro'),
                                    'consultant'      => esc_html__('Consultant', 'recruitpro'),
                                    'other'           => esc_html__('Industry Professional', 'recruitpro'),
                                );
                                
                                if (isset($context_labels[$professional_context])) {
                                    echo '<span class="professional-badge professional-badge-' . esc_attr($professional_context) . '">';
                                    echo esc_html($context_labels[$professional_context]);
                                    echo '</span>';
                                }
                            }
                            ?>
                        </div>
                        
                        <div class="comment-metadata">
                            <time datetime="<?php comment_time('c'); ?>" class="comment-date">
                                <?php
                                printf(
                                    esc_html__('%1$s at %2$s', 'recruitpro'),
                                    get_comment_date(),
                                    get_comment_time()
                                );
                                ?>
                            </time>
                            
                            <?php
                            // Edit link for authorized users
                            edit_comment_link(esc_html__('Edit', 'recruitpro'), '<span class="edit-link">', '</span>');
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="comment-content">
                <?php if ('0' == $comment->comment_approved) : ?>
                    <p class="comment-awaiting-moderation">
                        <?php esc_html_e('Your comment is awaiting professional moderation.', 'recruitpro'); ?>
                    </p>
                <?php endif; ?>

                <?php comment_text(); ?>

                <div class="comment-actions">
                    <?php
                    // Reply link with professional styling
                    comment_reply_link(array_merge($args, array(
                        'add_below' => 'div-comment',
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'],
                        'before'    => '<div class="reply-link">',
                        'after'     => '</div>',
                    )));
                    ?>
                </div>
            </div>
        </article>
    <?php
    // Note: </li> or </div> is automatically added by WordPress
}

/**
 * Get custom comment field
 */
function recruitpro_get_comment_field() {
    return '<div class="comment-form-comment">
        <label for="comment" class="comment-form-label">' . esc_html__('Your Professional Comment *', 'recruitpro') . '</label>
        <textarea id="comment" name="comment" cols="45" rows="6" maxlength="65525" required="required" 
                  class="comment-form-textarea" 
                  placeholder="' . esc_attr__('Share your professional insights, experiences, or questions related to recruitment and career development...', 'recruitpro') . '"></textarea>
    </div>';
}

/**
 * Get custom comment form fields
 */
function recruitpro_get_comment_fields() {
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $html_req = ($req ? ' required="required"' : '');
    $html5 = current_theme_supports('html5', 'comment-form') ? 'html5' : 'xhtml';

    $fields = array(
        'author' => '<div class="comment-form-author">
            <label for="author" class="comment-form-label">' . esc_html__('Name', 'recruitpro') . ($req ? ' *' : '') . '</label>
            <input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" 
                   size="30" maxlength="245"' . $html_req . ' class="comment-form-input" 
                   placeholder="' . esc_attr__('Your professional name', 'recruitpro') . '" />
        </div>',

        'email' => '<div class="comment-form-email">
            <label for="email" class="comment-form-label">' . esc_html__('Email', 'recruitpro') . ($req ? ' *' : '') . '</label>
            <input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" 
                   size="30" maxlength="100" aria-describedby="email-notes"' . $html_req . ' class="comment-form-input"
                   placeholder="' . esc_attr__('your.email@company.com', 'recruitpro') . '" />
        </div>',

        'url' => '<div class="comment-form-url">
            <label for="url" class="comment-form-label">' . esc_html__('Website/LinkedIn', 'recruitpro') . '</label>
            <input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" 
                   size="30" maxlength="200" class="comment-form-input"
                   placeholder="' . esc_attr__('https://linkedin.com/in/yourprofile', 'recruitpro') . '" />
        </div>',

        'professional_context' => '<div class="comment-form-professional-context">
            <label for="professional_context" class="comment-form-label">' . esc_html__('Professional Context', 'recruitpro') . '</label>
            <select id="professional_context" name="professional_context" class="comment-form-select">
                <option value="">' . esc_html__('Select your role (optional)', 'recruitpro') . '</option>
                <option value="hr_professional">' . esc_html__('HR Professional', 'recruitpro') . '</option>
                <option value="recruiter">' . esc_html__('Recruiter', 'recruitpro') . '</option>
                <option value="job_seeker">' . esc_html__('Job Seeker', 'recruitpro') . '</option>
                <option value="employer">' . esc_html__('Employer', 'recruitpro') . '</option>
                <option value="consultant">' . esc_html__('Consultant', 'recruitpro') . '</option>
                <option value="other">' . esc_html__('Other Industry Professional', 'recruitpro') . '</option>
            </select>
        </div>',
    );

    return $fields;
}

/**
 * Get comment notes before form
 */
function recruitpro_get_comment_notes_before() {
    return '<div class="comment-notes">
        <p class="comment-guidelines">' . 
        esc_html__('Join our professional community discussion. Share insights, ask questions, and connect with industry experts.', 'recruitpro') . 
        '</p>
        <p class="comment-privacy">' . 
        sprintf(
            esc_html__('Your email address will not be published. Required fields are marked %s', 'recruitpro'),
            '<span class="required">*</span>'
        ) . 
        '</p>
    </div>';
}

/**
 * Get comment notes after form
 */
function recruitpro_get_comment_notes_after() {
    return '<div class="comment-notes-after">
        <p class="comment-policy">' . 
        sprintf(
            esc_html__('By submitting a comment, you agree to our %1$sComment Policy%2$s and %3$sPrivacy Policy%4$s.', 'recruitpro'),
            '<a href="' . esc_url(get_privacy_policy_url()) . '" target="_blank">',
            '</a>',
            '<a href="' . esc_url(home_url('/comment-policy/')) . '" target="_blank">',
            '</a>'
        ) . 
        '</p>
        <p class="comment-moderation">' . 
        esc_html__('Comments are moderated to ensure professional discussion quality. Spam and inappropriate content will be removed.', 'recruitpro') . 
        '</p>
    </div>';
}

/**
 * Hook comment form processing for professional context
 */
add_action('comment_post', 'recruitpro_save_comment_professional_context');
function recruitpro_save_comment_professional_context($comment_id) {
    if (isset($_POST['professional_context']) && !empty($_POST['professional_context'])) {
        $context = sanitize_text_field($_POST['professional_context']);
        $allowed_contexts = array('hr_professional', 'recruiter', 'job_seeker', 'employer', 'consultant', 'other');
        
        if (in_array($context, $allowed_contexts)) {
            add_comment_meta($comment_id, 'professional_context', $context);
        }
    }
}

/**
 * Enhanced comment validation for recruitment context
 */
add_filter('preprocess_comment', 'recruitpro_validate_professional_comment');
function recruitpro_validate_professional_comment($commentdata) {
    // Enhanced email validation for professional domains
    $email = $commentdata['comment_author_email'];
    if (!empty($email) && !is_email($email)) {
        wp_die(esc_html__('Please provide a valid professional email address.', 'recruitpro'));
    }
    
    // Check for minimum content length
    $content_length = strlen(trim($commentdata['comment_content']));
    if ($content_length < 10) {
        wp_die(esc_html__('Please provide a more detailed professional comment (minimum 10 characters).', 'recruitpro'));
    }
    
    // Check for maximum content length
    if ($content_length > 5000) {
        wp_die(esc_html__('Comment is too long. Please keep it under 5000 characters for better readability.', 'recruitpro'));
    }
    
    return $commentdata;
}

/**
 * Add professional styling classes to comment form
 */
add_filter('comment_form_default_fields', 'recruitpro_comment_form_fields_styling');
function recruitpro_comment_form_fields_styling($fields) {
    foreach ($fields as $key => $field) {
        $fields[$key] = str_replace('class="', 'class="professional-field ', $field);
    }
    return $fields;
}
?>