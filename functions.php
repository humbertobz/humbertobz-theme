<?php
/*
 * Add style of parent and this child theme
 */
function humbertobz_enqueue_styles()
{
    $parent_style = 'twentysixteen-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'humbertobz-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'humbertobz_enqueue_styles' );

/*
 * Add isotope scripts to use on portfolio
 */
if (!function_exists('isotope_scripts')) :
    function isotope_scripts()
    {
        if (is_post_type_archive('jetpack-portfolio')) {
            wp_enqueue_script('isotope', get_stylesheet_directory_uri().'/assets/js/isotope.pkgd.min.js', array('jquery'), '', true);
            wp_enqueue_script('portfolio', get_stylesheet_directory_uri().'/assets/js/portfolio.js', array('jquery'), '', true);
        }
    }
    add_action('wp_enqueue_scripts', 'isotope_scripts');
endif;

/*  Add post thumbnail on Feeds
 *  ---------------------------------------- */
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

/*
 * Minify HTML, Javascript and CSS
 */
class WP_HTML_Compression
{
    // Settings
    protected $compress_css = true;
    protected $compress_js = true;
    protected $info_comment = true;
    protected $remove_comments = true;

    // Variables
    protected $html;

    public function __construct($html)
    {
        if (!empty($html)) {
            $this->parseHTML($html);
        }
    }

    public function __toString()
    {
        return $this->html;
    }

    protected function bottomComment($raw, $compressed)
    {
        $raw = strlen($raw);
        $compressed = strlen($compressed);

        $savings = ($raw - $compressed) / $raw * 100;

        $savings = round($savings, 2);

        return '<!--HTML compressed, size saved '.$savings.'%. From '.$raw.' bytes, now '.$compressed.' bytes-->';
    }

    protected function minifyHTML($html)
    {
        $pattern = '/<(?<script>script).*?<\/script\s*>|<(?<style>style).*?<\/style\s*>|<!(?<comment>--).*?-->|<(?<tag>[\/\w.:-]*)(?:".*?"|\'.*?\'|[^\'">]+)*>|(?<text>((<[^!\/\w.:-])?[^<]*)+)|/si';
        preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);
        $overriding = false;
        $raw_tag = false;
        // Variable reused for output
        $html = '';
        foreach ($matches as $token) {
            $tag = (isset($token['tag'])) ? strtolower($token['tag']) : null;

            $content = $token[0];

            if (is_null($tag)) {
                if (!empty($token['script'])) {
                    $strip = $this->compress_js;
                } elseif (!empty($token['style'])) {
                    $strip = $this->compress_css;
                } elseif ($content == '<!--wp-html-compression no compression-->') {
                    $overriding = !$overriding;

                    // Don't print the comment
                    continue;
                } elseif ($this->remove_comments) {
                    if (!$overriding && $raw_tag != 'textarea') {
                        // Remove any HTML comments, except MSIE conditional comments
                        $content = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $content);
                    }
                }
            } else {
                if ($tag == 'pre' || $tag == 'textarea') {
                    $raw_tag = $tag;
                } elseif ($tag == '/pre' || $tag == '/textarea') {
                    $raw_tag = false;
                } else {
                    if ($raw_tag || $overriding) {
                        $strip = false;
                    } else {
                        $strip = true;

                        // Remove any empty attributes, except:
                        // action, alt, content, src
                        $content = preg_replace('/(\s+)(\w++(?<!\baction|\balt|\bcontent|\bsrc)="")/', '$1', $content);

                        // Remove any space before the end of self-closing XHTML tags
                        // JavaScript excluded
                        $content = str_replace(' />', '/>', $content);
                    }
                }
            }

            if ($strip) {
                $content = $this->removeWhiteSpace($content);
            }

            $html .= $content;
        }

        return $html;
    }

    public function parseHTML($html)
    {
        $this->html = $this->minifyHTML($html);

        if ($this->info_comment) {
            $this->html .= "\n".$this->bottomComment($html, $this->html);
        }
    }

    protected function removeWhiteSpace($str)
    {
        $str = str_replace("\t", ' ', $str);
        $str = str_replace("\n",  ' ', $str);
        $str = str_replace("\r",  '', $str);

        while (stristr($str, '  ')) {
            $str = str_replace('  ', ' ', $str);
        }

        return $str;
    }
}

function wp_html_compression_finish($html)
{
    return new WP_HTML_Compression($html);
}

function wp_html_compression_start()
{
    ob_start('wp_html_compression_finish');
}
add_action('get_header', 'wp_html_compression_start');

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
 * Use different thumbnail size for Jetpack portfolio featured image
 */
 function remove_then_add_image_sizesp()
 {
     remove_image_size('jetpack_portfolio_thumbnail_size');
     add_image_size('jetpack_portfolio_thumbnail_size', 360, 240, array( 'center', 'top' ));
 }
add_action('init', 'remove_then_add_image_sizesp');

function new_custom_portfolio_image_size()
{
    return 'jetpack_portfolio_thumbnail_size';
}
add_filter('jetpack_portfolio_thumbnail_size', 'new_custom_portfolio_image_size');

function portfolio_thumbnail_size($sizes)
{
    $addsizes = array(
        "jetpack_portfolio_thumbnail_size" => __( "Portfolio thumbnail")
    );
    $newsizes = array_merge($sizes, $addsizes);
    return $newsizes;
}
add_filter('image_size_names_choose', 'portfolio_thumbnail_size');

function adsbygoogle_wp_head()
{
    if ( !is_front_page() && !is_home() && 'page' != get_post_type() && 'jetpack-portfolio' != get_post_type() ) {
        ?>
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-xxxxxxxxxxxxxxxx",
            enable_page_level_ads: true
        });
        </script>
        <?php
    }
}
add_action('wp_head', 'adsbygoogle_wp_head');

function jetpackme_no_related_posts( $options )
{
    if (is_singular('jetpack-portfolio')) {
        $options['enabled'] = false;
    }
    return $options;
}
add_filter( 'jetpack_relatedposts_filter_options', 'jetpackme_no_related_posts' );
