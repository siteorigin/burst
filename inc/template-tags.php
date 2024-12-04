<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 */
if ( ! function_exists( 'siteorigin_north_display_logo' ) ) {
	/**
	 * Display the logo or site title.
	 */
	function siteorigin_north_display_logo() {
		$logo = siteorigin_setting( 'branding_logo' );
		do_action( 'siteorigin_north_logo_before' );

		if ( ! empty( $logo ) ) {
			$attrs = apply_filters( 'siteorigin_north_logo_attributes', array( 'class' => 'custom-logo' ) );
			$logo = apply_filters( 'siteorigin_north_logo_url', $logo );

			?><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<span class="screen-reader-text"><?php esc_html_e( 'Home', 'siteorigin-north' ); ?></span><?php
				echo wp_get_attachment_image( $logo, 'full', false, $attrs );
			?></a><?php

			do_action( 'siteorigin_north_logo_after' );
		} elseif ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
			?><?php the_custom_logo(); ?><?php
		} else {
			if ( is_front_page() ) { ?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<?php } else { ?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
			}
		}

		if ( ( $logo || ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) ) && siteorigin_setting( 'branding_site_title' ) ) {
			do_action( 'siteorigin_north_logo_title_before' );
			if ( is_front_page() ) { ?>
				<h1 class="logo-site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<?php } else { ?>
				<p class="logo-site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
			}
			do_action( 'siteorigin_north_logo_title_after' );
		}
	}
}

if ( ! function_exists( 'siteorigin_north_display_retina_logo' ) ) {
	/**
	 * Display a retina ready logo.
	 */
	function siteorigin_north_display_retina_logo( $attr ) {
		if ( current_filter() == 'wp_get_attachment_image_attributes' ) {
			if ( ! isset( $attr['class'] ) || $attr['class'] != 'custom-logo' ) {
				return $attr;
			}
		}
		$retina = siteorigin_setting( 'branding_retina_logo' );

		if ( ! empty( $retina ) ) {
			$logo = siteorigin_setting( 'branding_logo' );
			// Check if the user set a branding logo and if they didn't, default to the site identity logo.
			if ( empty( $logo ) ) {
				$logo = get_theme_mod( 'custom_logo' );
			}

			$srcset = array();
			$logo_src = wp_get_attachment_image_src( $logo, 'full' );
			$retina_src = wp_get_attachment_image_src( $retina, 'full' );

			if ( ! empty( $logo_src ) ) {
				$srcset[] = $logo_src[0] . ' 1x';
				$srcset[] = $retina_src[0] . ' 2x';
			}

			if ( ! empty( $srcset ) ) {
				$attr['srcset'] = implode( ',', $srcset );
			}
		}

		return $attr;
	}
}
add_filter( 'wp_get_attachment_image_attributes', 'siteorigin_north_display_retina_logo' );

if ( ! function_exists( 'siteorigin_north_the_post_navigation' ) ) {
	/**
	 * Display navigation to next/previous post when applicable.
	 */
	function siteorigin_north_the_post_navigation() {
		// Don't print empty markup if there's nowhere to navigate.
		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}
		?>
	<nav class="navigation post-navigation">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'siteorigin-north' ); ?></h2>
		<div class="nav-links">
			<?php
				if ( is_rtl() ) {
					previous_post_link( '<div class="nav-previous"><span class="north-icon-next"></span>%link</div>', '%title' );
					next_post_link( '<div class="nav-next">%link<span class="north-icon-previous"></span></div>', '%title' );
				} else {
					previous_post_link( '<div class="nav-previous"><span class="north-icon-previous"></span>%link</div>', '%title' );
					next_post_link( '<div class="nav-next">%link<span class="north-icon-next"></span></div>', '%title' );
				}
		?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
	}
}

if ( ! function_exists( 'siteorigin_north_post_meta' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function siteorigin_north_post_meta() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}
		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$byline = sprintf(
			esc_html_x( 'by %s', 'post author', 'siteorigin-north' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		$comments = sprintf(
			_nx( 'One Comment', '%1$s Comments', get_comments_number(), 'comments title', 'siteorigin-north' ),
			number_format_i18n( get_comments_number() )
		);

		if ( siteorigin_setting( 'blog_display_date' ) ) {
			?>
		<li class="posted-on">
			<span class="meta-icon north-icon-calendar" aria-hidden="true"></span>
			<a href="<?php the_permalink(); ?>">
				<?php echo $time_string; ?>
			</a>
		</li>
		<?php
		}

		if ( siteorigin_setting( 'blog_display_author' ) ) {
			?>
		<li class="posted-by">
			<span class="meta-icon north-icon-user" aria-hidden="true"></span>
			<?php echo $byline; ?>
		</li>
		<?php
		}

		if ( get_comments_number() > 0 && siteorigin_setting( 'blog_display_comment_count' ) ) {
			?>
		<li class="post-comments">
			<span class="meta-icon north-icon-comments" aria-hidden="true"></span>
			<a href="<?php the_permalink(); ?>#comments">
				<?php echo $comments; ?>
			</a>
		</li>
		<?php
		}
	}
}

if ( ! function_exists( 'siteorigin_north_entry_footer' ) ) {
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function siteorigin_north_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' == get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'siteorigin-north' ) );

			if ( $categories_list && siteorigin_north_categorized_blog() ) {
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'siteorigin-north' ) . '</span>', $categories_list ); // WPCS: XSS OK.
			}

			/* translators: used between list items, there is a space after the comma */
			the_tags( '<div class="tags-list">', '', '</div>' );

			if ( siteorigin_setting( 'blog_display_author_box' ) ) { ?>
			<div class="author-box">
				<div class="author-avatar">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?>
					<?php do_action( 'siteorigin_north_after_entry_author_avatar' ); ?>
				</div>
				<div class="author-description">
					<h2 class="author-title">
						<?php echo  ( !is_rtl() ) ? get_the_author() : ''; ?>
						<small class="author-info">
							<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
								<?php _e( 'View posts by ', 'siteorigin-north' );
				echo get_the_author(); ?>
							</a>
						</small>
						<?php echo  ( is_rtl() ) ? get_the_author() : ''; ?>
					</h2>
					<?php echo wp_kses_post( get_the_author_meta( 'description' ), null ); ?>
				</div>
				<div class="clear"></div>
			</div>
		<?php }
			}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link( esc_html__( 'Leave a comment', 'siteorigin-north' ), esc_html__( '1 Comment', 'siteorigin-north' ), esc_html__( '% Comments', 'siteorigin-north' ) );
			echo '</span>';
		}
	}
}

if ( ! function_exists( 'siteorigin_north_posts_pagination' ) ) {
	/**
	 * Display pagination.
	 */
	function siteorigin_north_posts_pagination() {
		global $wp_query;

		if ( $wp_query->max_num_pages <= 1 ) {
			return;
		}

		if ( is_rtl() ) {
			$args = array(
				'next_text' => '<span class="north-icon-double-previous"></span>',
				'prev_text' => '<span class="north-icon-double-next"></span>',
				'end_size'  => 3,
				'mid_size'  => 3,
			);
		} else {
			$args = array(
				'next_text' => '<span class="north-icon-double-next"></span>',
				'prev_text' => '<span class="north-icon-double-previous"></span>',
				'end_size'  => 3,
				'mid_size'  => 3,
			);
		}

		if ( is_search() ) {
			// Add the arguments neccessary for search.
			global $wp_query;
			$big = 999999999; // Need an unlikely integer.
			$args = wp_parse_args( array(
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'current' => max( 1, get_query_var( 'paged' ) ),
				'total' => $wp_query->max_num_pages,
			), $args );
		}

		?><div class="post-pagination">
	<h2 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'siteorigin-north' ); ?></h2><?php
		echo paginate_links( $args );
		?></div><?php
	}
}

if ( ! function_exists( 'siteorigin_north_categorized_blog' ) ) {
	/**
	 * Returns true if a blog has more than 1 category.
	 *
	 * @return bool
	 */
	function siteorigin_north_categorized_blog() {
		if ( false === ( $all_the_cool_cats = get_transient( 'siteorigin_north_categories' ) ) ) {
			// Create an array of all the categories that are attached to posts.
			$all_the_cool_cats = get_categories( array(
				'fields'     => 'ids',
				'hide_empty' => 1,
				// We only need to know if there is more than one category.
				'number'     => 2,
			) );

			// Count the number of categories that are attached to the posts.
			$all_the_cool_cats = count( $all_the_cool_cats );

			set_transient( 'siteorigin_north_categories', $all_the_cool_cats );
		}

		if ( $all_the_cool_cats > 1 ) {
			// This blog has more than 1 category so siteorigin_north_categorized_blog should return true.
			return true;
		} else {
			// This blog has only 1 category so siteorigin_north_categorized_blog should return false.
			return false;
		}
	}
}

if ( ! function_exists( 'siteorigin_north_comment' ) ) {
	/**
	 * Returns the comment template.
	 */
	function siteorigin_north_comment( $comment, $args, $depth ) {
		?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<?php $type = get_comment_type( $comment->comment_ID ); ?>
		<?php if ( $type == 'comment' ) { ?>
			<div class="avatar-container">
				<?php echo get_avatar( get_comment_author_email(), 80 ); ?>
			</div>
		<?php } ?>

		<div class="comment-container">
			<?php if ( $depth <= $args['max_depth'] ) { ?>
				<?php comment_reply_link( array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ); ?>
			<?php } ?>

			<div class="info">
				<?php if ( is_rtl() ) { ?>
					<span class="date"><?php comment_date(); ?></span>
					-
					<span class="author"><?php comment_author_link(); ?></span>
				<?php } else { ?>
					<span class="author"><?php comment_author_link(); ?></span>
					-
					<span class="date"><?php comment_date(); ?></span>
				<?php } ?>
			</div>

			<div class="comment-content content">
				<?php if ( ! $comment->comment_approved ) { ?>
					<p class="comment-awaiting-moderation">
						<?php esc_html_e( 'Your comment is awaiting moderation.', 'siteorigin-north' ); ?>
					</p>
				<?php } ?>
				<?php comment_text(); ?>
			</div>
		</div>

		<div class="clear"></div>
	<?php
	}
}

if ( ! function_exists( 'siteorigin_north_footer_text' ) ) {
	/**
	 * Displays the footer text.
	 */
	function siteorigin_north_footer_text() {
		$text = siteorigin_setting( 'footer_text' );

		if ( empty( $text ) ) {
			return;
		}
		$text = str_replace(
			array( '{sitename}', '{year}' ),
			array( get_bloginfo( 'sitename' ), date( 'Y' ) ),
			'<span>' . $text . '</span>'
		);
		echo wp_kses_post( $text );
	}
}

if ( ! function_exists( 'siteorigin_north_category_transient_flusher' ) ) {
	/**
	 * Flush out the transients used in siteorigin_north_categorized_blog.
	 */
	function siteorigin_north_category_transient_flusher() {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		delete_transient( 'siteorigin_north_categories' );
	}
}
add_action( 'edit_category', 'siteorigin_north_category_transient_flusher' );
add_action( 'save_post', 'siteorigin_north_category_transient_flusher' );

if ( ! function_exists( 'siteorigin_north_custom_icon' ) ) {
	/**
	 * Display a custom icons from the settings
	 */
	function siteorigin_north_custom_icon( $icon, $class ) {
		$id = siteorigin_setting( $icon );
		$url = wp_get_attachment_url( $id );
		$filetype = wp_check_filetype( $url );
		$extension = $filetype['ext'];

		switch( $extension ) {
			case 'svg':
				$attrs['class'] = "style-svg $class";
				break;

			default:
				$attrs['class'] = "$class";
		}

		echo wp_get_attachment_image( $id, 'full', false, $attrs );
	}
}

if ( ! function_exists( 'siteorigin_north_display_icon' ) ) {
	/**
	 * Displays SVG icons.
	 */
	function siteorigin_north_display_icon( $type ) {
		switch ( $type ) {
			case 'menu':
				if ( siteorigin_setting( 'icons_menu' ) && wp_attachment_is_image( siteorigin_setting( 'icons_menu' ) ) ) { ?>
				<?php siteorigin_north_custom_icon( 'icons_menu', 'svg-icon-menu' ); ?>
			<?php } else { ?>
				<div class="icon-menu">
					<span></span>
					<span></span>
					<span></span>
				</div>
			<?php }
			break;

			case 'close':
				if ( siteorigin_setting( 'icons_close_search' ) && wp_attachment_is_image( siteorigin_setting( 'icons_close_search' ) ) ) { ?>
				<?php siteorigin_north_custom_icon( 'icons_close_search', 'svg-icon-close' ); ?>
			<?php } else { ?>
				<svg version="1.1" class="svg-icon-close" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="12px" y="12px"
					viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
					<path class="circle" d="M22.1,7.7c-0.6-1.4-1.4-2.5-2.3-3.5c-1-1-2.2-1.8-3.5-2.3C14.9,1.3,13.5,1,12,1S9.1,1.3,7.7,1.9
					C6.4,2.5,5.2,3.2,4.2,4.2c-1,1-1.8,2.2-2.3,3.5C1.3,9.1,1,10.5,1,12c0,1.5,0.3,2.9,0.9,4.3c0.6,1.4,1.4,2.5,2.3,3.5
					c1,1,2.2,1.8,3.5,2.3C9.1,22.7,10.5,23,12,23s2.9-0.3,4.3-0.9c1.4-0.6,2.5-1.4,3.5-2.3c1-1,1.8-2.2,2.3-3.5
					c0.6-1.4,0.9-2.8,0.9-4.3C23,10.5,22.7,9.1,22.1,7.7z M20.3,15.5c-0.5,1.1-1.1,2.1-1.9,2.9s-1.8,1.4-2.9,1.9
					C14.4,20.8,13.2,21,12,21s-2.4-0.2-3.5-0.7c-1.1-0.5-2.1-1.1-2.9-1.9s-1.4-1.8-1.9-2.9C3.2,14.4,3,13.2,3,12
					c0-1.2,0.2-2.4,0.7-3.5c0.5-1.1,1.1-2.1,1.9-2.9s1.8-1.4,2.9-1.9C9.6,3.2,10.8,3,12,3s2.4,0.2,3.5,0.7c1.1,0.5,2.1,1.1,2.9,1.9
					s1.4,1.8,1.9,2.9C20.8,9.6,21,10.8,21,12C21,13.2,20.8,14.4,20.3,15.5z"/>
					<path class="cross" d="M14.8,8.2c0.3,0,0.5,0.1,0.7,0.3c0.2,0.2,0.3,0.4,0.3,0.7s-0.1,0.5-0.3,0.7L13.4,12l2.1,2.1
					c0.2,0.2,0.3,0.4,0.3,0.7c0,0.3-0.1,0.5-0.3,0.7s-0.4,0.3-0.7,0.3c-0.3,0-0.5-0.1-0.7-0.3L12,13.4l-2.1,2.1
					c-0.2,0.2-0.4,0.3-0.7,0.3c-0.3,0-0.5-0.1-0.7-0.3s-0.3-0.4-0.3-0.7c0-0.3,0.1-0.5,0.3-0.7l2.1-2.1L8.5,9.9
					C8.3,9.7,8.2,9.4,8.2,9.2c0-0.3,0.1-0.5,0.3-0.7s0.4-0.3,0.7-0.3s0.5,0.1,0.7,0.3l2.1,2.1l2.1-2.1C14.3,8.3,14.6,8.2,14.8,8.2z"/>
				</svg>
			<?php }
			break;

			case 'up-arrow':
				if ( siteorigin_setting( 'icons_scroll_to_top' ) && wp_attachment_is_image( siteorigin_setting( 'icons_scroll_to_top' ) ) ) { ?>
				<?php siteorigin_north_custom_icon( 'icons_scroll_to_top', 'svg-icon-to-top' ); ?>
			<?php } else { ?>
				<svg version="1.1" class="svg-icon-to-top" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
					<path class="st0" d="M12,2c0.3,0,0.5,0.1,0.7,0.3l7,7C19.9,9.5,20,9.7,20,10c0,0.3-0.1,0.5-0.3,0.7S19.3,11,19,11
						c-0.3,0-0.5-0.1-0.7-0.3L13,5.4V21c0,0.3-0.1,0.5-0.3,0.7S12.3,22,12,22s-0.5-0.1-0.7-0.3S11,21.3,11,21V5.4l-5.3,5.3
						C5.5,10.9,5.3,11,5,11c-0.3,0-0.5-0.1-0.7-0.3C4.1,10.5,4,10.3,4,10c0-0.3,0.1-0.5,0.3-0.7l7-7C11.5,2.1,11.7,2,12,2z"/>
				</svg>
			<?php }
			break;

			case 'search':
				if ( siteorigin_setting( 'icons_search' ) && wp_attachment_is_image( siteorigin_setting( 'icons_search' ) ) ) { ?>
				<?php siteorigin_north_custom_icon( 'icons_search', 'svg-icon-search' ); ?>
			<?php } else { ?>
				<svg version="1.1" class="svg-icon-search" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="32" viewBox="0 0 32 32">
					<path d="M20.943 4.619c-4.5-4.5-11.822-4.5-16.321 0-4.498 4.5-4.498 11.822 0 16.319 4.007 4.006 10.247 4.435 14.743 1.308 0.095 0.447 0.312 0.875 0.659 1.222l6.553 6.55c0.953 0.955 2.496 0.955 3.447 0 0.953-0.951 0.953-2.495 0-3.447l-6.553-6.551c-0.347-0.349-0.774-0.565-1.222-0.658 3.13-4.495 2.7-10.734-1.307-14.743zM18.874 18.871c-3.359 3.357-8.825 3.357-12.183 0-3.357-3.359-3.357-8.825 0-12.184 3.358-3.359 8.825-3.359 12.183 0s3.359 8.825 0 12.184z"></path>
				</svg>
			<?php }
			break;
		}
	}
}

if ( ! function_exists( 'siteorigin_north_breadcrumbs' ) ) {
	/**
	 * Display's breadcrumbs supported by Breadcrumb NavXT, Rank Math, and Yoast SEO.
	 */
	function siteorigin_north_breadcrumbs() {
		siteorigin_settings_breadcrumbs();
	}
}

if ( ! function_exists( 'siteorigin_north_entry_thumbnail' ) ) {
	/**
	 * Display the post/page thumbnail.
	 */
	function siteorigin_north_entry_thumbnail() {
		if ( in_array( siteorigin_page_setting( 'layout', 'default' ), array( 'default', 'full-width-sidebar' ), true ) && is_active_sidebar( 'main-sidebar' ) ) {
			$thumb_size = 'post-thumbnail';
		} else {
			$thumb_size = 'north-thumbnail-no-sidebar';
		}
		the_post_thumbnail( $thumb_size );
	}
}

if ( ! function_exists( 'siteorigin_north_get_video' ) ) {
	/**
	 * Get the video from the current post.
	 */
	function siteorigin_north_get_video() {
		$first_url = '';
		$first_video = '';

		$i = 0;

		preg_match_all( '|^\s*https?://[^\s"]+\s*$|im', get_the_content(), $urls );

		foreach ( $urls[0] as $url ) {
			$i++;

			if ( 1 === $i ) {
				$first_url = trim( $url );
			}

			$oembed = wp_oembed_get( esc_url( $url ) );

			if ( ! $oembed ) {
				continue;
			}

			$first_video = $oembed;

			break;
		}

		return ( '' !== $first_video ) ? $first_video : false;
	}
}

if ( ! function_exists( 'siteorigin_north_filter_video' ) ) {
	/**
	 * Removes the video from the post content.
	 */
	function siteorigin_north_filter_video( $content ) {
		if ( siteorigin_north_get_video() ) {
			preg_match_all( '|^\s*https?://[^\s"]+\s*$|im', $content, $urls );

			if ( ! empty( $urls[0] ) ) {
				$content = str_replace( $urls[0], '', $content );
			}

			return $content;
		} else {
			return $content;
		}
	}
}

if ( ! function_exists( 'siteorigin_north_strip_gallery' ) ) {
	/**
	 * Remove gallery from post content.
	 */
	function siteorigin_north_strip_gallery( $content ) {
		preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );

		if ( ! empty( $matches ) ) {
			foreach ( $matches as $shortcode ) {
				if ( 'gallery' === $shortcode[2] ) {
					$pos = strpos( $content, $shortcode[0] );

					if ( false !== $pos ) {
						return substr_replace( $content, '', $pos, strlen( $shortcode[0] ) );
					}
				}
			}
		}

		return $content;
	}
}

if ( ! function_exists( 'siteorigin_north_get_image' ) ) {
	/**
	 * Get the first image from the post content.
	 */
	function siteorigin_north_get_image() {
		$first_image = '';

		$output = preg_match_all( '/<img[^>]+\>/i', get_the_content(), $images );

		if ( empty( $images[0] ) ) {
			return false;
		}

		$first_image = $images[0][0];

		return ( '' !== $first_image ) ? $first_image : false;
	}
}

if ( ! function_exists( 'siteorigin_north_strip_image' ) ) {
	/**
	 * Removes the first image from the post content
	 */
	function siteorigin_north_strip_image( $content ) {
		return preg_replace( '/<img[^>]+\>/i', '', $content, 1 );
	}
}

if ( ! function_exists( 'siteorigin_north_archive_post_content' ) ) {
	/**
	 * Outputs the post content or excerpt based on theme settings, and whether
	 * the post is singular.
	 */
	function siteorigin_north_post_content() {
		$read_more_text = ! empty( siteorigin_setting( 'blog_read_more_text' ) ) ? siteorigin_setting( 'blog_read_more_text' ) : __( 'Read More', 'siteorigin-north' );

		if (
			is_singular() ||
			siteorigin_setting( 'blog_post_content' ) == 'content'
		) {
			the_content( $read_more_text );
			return;
		}

		the_excerpt();

		if ( siteorigin_setting( 'blog_excerpt_post_link' ) ) { ?>
			<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="more-link">
				<?php echo esc_html( $read_more_text ); ?>
			</a>
			<?php
		}
	}
}
