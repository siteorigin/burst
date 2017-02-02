<?php
/**
 * Template part for displaying single video format posts.
 *
 * @package siteorigin-north
 * @license GPL 2.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>

	<?php if ( siteorigin_north_get_video() ) : ?>
		<div class="entry-video">
			<?php echo siteorigin_north_get_video(); ?>
		</div>
	<?php elseif ( has_post_thumbnail() && siteorigin_setting( 'blog_featured_single' ) ) : ?>
		<div class="entry-thumbnail">
			<?php siteorigin_north_entry_thumbnail() ?>
		</div>
	<?php endif; ?>

	<?php if( siteorigin_page_setting( 'page_title' ) ) : ?>
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	<?php endif; ?>

	<?php siteorigin_north_breadcrumbs(); ?>

	<?php if( siteorigin_page_setting( 'page_title' ) ) : ?>
			<div class="entry-meta">
				<?php siteorigin_north_post_meta(); ?>
			</div><!-- .entry-meta -->
		</header><!-- .entry-header -->
	<?php endif; ?>

	<div class="entry-content">
		<?php echo apply_filters( 'the_content', siteorigin_north_filter_video( get_the_content() ) ); // Display the content without first video ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'siteorigin-north' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php siteorigin_north_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
