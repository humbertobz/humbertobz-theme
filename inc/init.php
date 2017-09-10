<?php
/*
 * Add style of parent and this child theme
 */
function humbertobz_enqueue_styles()
{
    $parentStyle = 'twentysixteen-style';

    wp_enqueue_style($parentStyle, get_template_directory_uri() . '/style.css');
    wp_enqueue_style(
        'humbertobz-style',
        get_stylesheet_directory_uri() . '/assets/css/style.css',
        array( $parentStyle ),
        wp_get_theme()->get('Version')
    );
    wp_enqueue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'humbertobz_enqueue_styles');

/*
 * Add post thumbnail on Feeds
 */
function rss_post_thumbnail($content)
{
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $content = '<div>'.get_the_post_thumbnail($post->ID, 'large').
        '</div>'.get_the_content();
    }

    return $content;
}
add_filter('the_excerpt_rss', 'rss_post_thumbnail');
add_filter('the_content_feed', 'rss_post_thumbnail');

/**
 * On lists of posts change the full content of the post for a custom excerpt.
 */
function custom_excerpt($content)
{
    $count = 300;

    if ((is_home() || is_search() || is_archive())) :
        $content = strip_tags($content);
        $content = strip_shortcodes($content);
        $content = substr($content, 0, $count);
        $content = substr($content, 0, strripos($content, ' '));
        $content = $content.' &hellip; <p><a href="'.esc_url(get_permalink()).'">'.sprintf(__('Continue reading %s', 'twentysixteen'), '<span class="meta-nav">&rarr;</span>'.the_title('<span class="screen-reader-text">', '</span>', false)).'</a></p>';
    endif;

    return $content;
}
add_filter('the_content', 'custom_excerpt');

/*
 * Add adsense with page level ads on the head of site
 */
function adsbygoogle_wp_head()
{
    if (!is_front_page() && !is_home() && 'page' != get_post_type() && 'jetpack-portfolio' != get_post_type()) {
    ?>
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-1402361469085356",
            enable_page_level_ads: true
        });
        </script>
        <?php
    }
}
add_action('wp_head', 'adsbygoogle_wp_head');
