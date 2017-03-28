<?php
/**
 * The template for displaying archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

    <div id="primary" class="content-area full-width">
        <main id="main" class="site-main" role="main">

            <header class="entry-header">
                <h1 class="entry-title">Portfólio</h1>
            </header>

            <!-- <div class="portfolio-filter">
                <ul>
                    <li id="filter--all" class="filter active" data-filter="*"><?php _e( 'Todos', 'humbertobz' ) ?></li>
                    <?php
                        // list terms in a given taxonomy
                        $taxonomy = 'jetpack-portfolio-type';
                        $tax_terms = get_terms( $taxonomy );

                        foreach ( $tax_terms as $tax_term ) :
                            echo '<li class="filter" data-filter=".'. $tax_term->slug.'">' . $tax_term->name .'</li> ';
                        endforeach;
                    ?>
                </ul>
            </div> -->
            <div class="portfolio">

                <?php
                global $query_string;
                query_posts( $query_string . '&posts_per_page=-1' );

                if ( have_posts() ) :

                    // Start the Loop.
                    while ( have_posts() ) : the_post();

                        /*
                         * Include the Post-Format-specific template for the content.
                         * If you want to override this in a child theme, then include a file
                         * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                         */
                        get_template_part( 'template-parts/content', 'jetpack-portfolio' );

                    // End the loop.
                    endwhile;

                // If no content, include the "No posts found" template.
                else :
                    get_template_part( 'template-parts/content', 'none' );

                endif;
                ?>

            </div>
        </main><!-- .site-main -->
    </div><!-- .content-area -->

<?php get_footer(); ?>
