<?php
/**
 * The template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			the_content();

			$terms_list = get_the_term_list( '', 'jetpack-portfolio-tag', '', _x( ', ', 'Used between list items, there is a space after the comma.', 'twentysixteen' ) );
			if ( $terms_list ) {
				printf( '<span class="tecnologias"><span class="screen-reader-text">%1$s </span><strong>Tecnologias:</strong> %2$s</span>',
					_x( 'Tecnologias', 'Used before tag names.', 'twentysixteen' ),
					strip_tags( $terms_list )
				);
			}
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php
			edit_post_link(
				sprintf(
					/* translators: %s: Name of current post */
					__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'twentysixteen' ),
					get_the_title()
				),
				'<span class="edit-link">',
				'</span>'
			);
		?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
