<?php
/**
 * Template part for displaying single gallery format posts.
 *
 * @license GPL 2.0
 */
$gallery = get_post_gallery( get_the_ID(), false );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>

	<?php if ( $gallery != '' ) { ?>
		<div class="flexslider gallery-format-slider">
			<ul class="slides gallery-format-slides">
				<?php foreach ( $gallery['src'] as $image ) { ?>
					<li class="gallery-format-slide">
						<img src="<?php echo $image; ?>">
					</li>
				<?php } ?>
			<ul>
		</div>
	<?php } elseif ( is_singular() && has_post_thumbnail() && siteorigin_setting( 'blog_featured_single' ) ) { ?>
		<div class="entry-thumbnail">
			<?php siteorigin_north_entry_thumbnail(); ?>
		</div>
	<?php } elseif ( has_post_thumbnail() && siteorigin_setting( 'blog_featured_archive' ) ) { ?>
		<div class="entry-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<div class="thumbnail-hover">
					<span class="screen-reader-text"><?php esc_html_e( 'Open post', 'siteorigin-north' ); ?></span>
					<span class="north-icon-add"></span>
				</div>
				<?php siteorigin_north_entry_thumbnail(); ?>
			</a>
		</div>
	<?php } ?>

	<?php if ( is_singular() ) { ?>
		<?php if ( siteorigin_page_setting( 'page_title' ) ) { ?>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		<?php } ?>

		<?php siteorigin_north_breadcrumbs(); ?>

		<?php if ( siteorigin_page_setting( 'page_title' ) ) { ?>
				<div class="entry-meta">
					<?php siteorigin_north_post_meta(); ?>
				</div><!-- .entry-meta -->
			</header><!-- .entry-header -->
		<?php } ?>
	<?php } else { ?>
		<header class="entry-header">
			<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

			<?php if ( 'post' == get_post_type() ) { ?>
				<ul class="entry-meta">
					<?php siteorigin_north_post_meta(); ?>
				</ul><!-- .entry-meta -->
			<?php } ?>
		</header><!-- .entry-header -->
	<?php } ?>

	<div class="entry-content">
		<?php
		if ( is_singular() ) {
			// Display the content without gallery.
			$content = siteorigin_north_strip_gallery( get_the_content() );
			echo  str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $content ) );
		} else {
			siteorigin_north_post_content();
		}

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'siteorigin-north' ),
			'after'  => '</div>',
		) );
		?>
	</div><!-- .entry-content -->

	<?php if ( is_singular() ) { ?>
		<footer class="entry-footer">
			<?php siteorigin_north_entry_footer(); ?>
		</footer><!-- .entry-footer -->
	<?php } ?>
</article><!-- #post-## -->
