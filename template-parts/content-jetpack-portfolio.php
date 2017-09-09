<?php
/**
 * The template part for displaying content
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

// get Jetpack Portfolio taxonomy terms for portfolio filtering
$terms = get_the_terms($post->ID, 'jetpack-portfolio-type');

if ($terms && ! is_wp_error($terms)) :

    $filtering_links = array();

    foreach ($terms as $term) {
        $filtering_links[] = $term->slug;
    }

    $filtering = join(" ", $filtering_links);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class($filtering); ?>>
    <a href="<?php the_permalink(); ?>" rel="bookmark" class="image-link" tabindex="-1">
        <div class="mask">
            <div class="title">
                <h3><?php the_title(); ?></h3>
                <small><?php the_date('m/Y'); ?></small>
            </div>
        </div>
        <?php  if ('' != get_the_post_thumbnail()) : ?>
            <?php the_post_thumbnail('jetpack_portfolio_thumbnail_size'); //360x240 ?>
        <?php endif; ?>
    </a>
</article><!-- #post-## -->

<?php
endif;
