<?php
/*
 * Add isotope scripts to use on portfolio
 */
if (!function_exists('isotope_scripts')) :
    function isotope_scripts()
    {
        if (is_post_type_archive('jetpack-portfolio')) {
            wp_enqueue_script(
                'isotope',
                get_stylesheet_directory_uri().'/assets/js/isotope.pkgd.min.js',
                array('jquery'),
                '',
                true
            );
            wp_enqueue_script(
                'portfolio',
                get_stylesheet_directory_uri().'/assets/js/portfolio.js',
                array('jquery'),
                '',
                true
            );
        }
    }
    add_action('wp_enqueue_scripts', 'isotope_scripts');
endif;

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
        'jetpack_portfolio_thumbnail_size' => __('Portfolio thumbnail')
    );
    $newsizes = array_merge($sizes, $addsizes);
    return $newsizes;
}
add_filter('image_size_names_choose', 'portfolio_thumbnail_size');

function jetpackme_no_related_posts($options)
{
    if (is_singular('jetpack-portfolio')) {
        $options['enabled'] = false;
    }
    return $options;
}
add_filter('jetpack_relatedposts_filter_options', 'jetpackme_no_related_posts');

function no_photon_on_portfolio()
{
    if (is_archive('jetpack-portfolio')) {
        add_filter('jetpack_photon_skip_image', '__return_true');
    }
}
add_action('wp', 'no_photon_on_portfolio');
