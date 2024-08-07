<?php
/**
 * SiteOrigin North functions and definitions.
 *
 * @license GPL 2.0
 */
define( 'SITEORIGIN_THEME_VERSION', 'dev' );
define( 'SITEORIGIN_THEME_JS_PREFIX', '' );
define( 'SITEORIGIN_THEME_CSS_PREFIX', '' );

// The settings manager.
include get_template_directory() . '/inc/settings/settings.php';

if ( ! function_exists( 'siteorigin_north_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function siteorigin_north_setup() {
		load_theme_textdomain( 'siteorigin-north', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for the custom logo.
		 */
		add_theme_support( 'custom-logo' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );

		set_post_thumbnail_size( 650, 650 );
		add_image_size( 'north-thumbnail-no-sidebar', 1040, 650, true );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary Menu', 'siteorigin-north' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		/*
		 * Enable support for Post Formats.
		 * See http://codex.wordpress.org/Post_Formats
		 */
		add_theme_support( 'post-formats', array(
			'aside',
			'image',
			'video',
			'quote',
			'link',
			'gallery',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'siteorigin_north_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Allowing use of shortcodes in taxonomy descriptions
		add_filter( 'term_description', 'shortcode_unautop' );
		add_filter( 'term_description', 'do_shortcode' );

		// Add support for WooCommerce.
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-slider' );

		if ( ! defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
			// Only include panels lite if the panels plugin doesn't exist.
			include get_template_directory() . '/inc/panels-lite/panels-lite.php';
		}

		add_theme_support( 'siteorigin-panels', array(
			'home-page'  => true,
			'responsive' => ! siteorigin_setting( 'responsive_disabled' ),
		) );

		// We'll use archive settings
		add_theme_support( 'siteorigin-template-settings' );
	}
} // siteorigin_north_setup
add_action( 'after_setup_theme', 'siteorigin_north_setup' );


if ( ! function_exists( 'siteorigin_north_premium_setup' ) ) {
	/**
	 * Add support for premium theme components
	 */
	function siteorigin_north_premium_setup() {
		// This theme supports the no attribution addon
		add_theme_support( 'siteorigin-premium-no-attribution', array(
			'filter'  => 'siteorigin_north_footer_credits',
			'enabled' => siteorigin_setting( 'branding_attribution' ),
			'siteorigin_setting' => 'branding_attribution',
		) );

		// This theme supports the no attribution addon
		add_theme_support( 'siteorigin-premium-ajax-comments', array(
			'enabled' => siteorigin_setting( 'blog_ajax_comments' ),
			'siteorigin_setting' => 'blog_ajax_comments',
		) );
	}
  
  // Logo Booster.
	add_theme_support( 'siteorigin-premium-logo-booster' );
}
add_action( 'after_setup_theme', 'siteorigin_north_premium_setup' );

if ( ! function_exists( 'siteorigin_north_content_width' ) ) {
	/**
	 * Set the content width in pixels, based on the theme's design and stylesheet.
	 *
	 * Priority 0 to make it available to lower priority callbacks.
	 *
	 * @global int $content_width
	 */
	function siteorigin_north_content_width() {
		global $content_width;
		$content_width = apply_filters( 'siteorigin_north_content_width', 650 );
	}
}
add_action( 'after_setup_theme', 'siteorigin_north_content_width', 0 );

if ( ! function_exists( 'siteorigin_north_post_class_filter' ) ) {
	/**
	 * Filter post classes as required.
	 *
	 * @see https://codex.wordpress.org/Function_Reference/post_class.
	 */
	function siteorigin_north_post_class_filter( $classes ) {
		$classes[] = 'post';

		// Resolves structured data issue in core. See https://core.trac.wordpress.org/ticket/28482.
		if ( is_page() ) {
			$class_key = array_search( 'hentry', $classes );

			if ( $class_key !== false ) {
				unset( $classes[ $class_key ] );
			}
		}

		$classes = array_unique( $classes );

		return $classes;
	}
}
add_filter( 'post_class', 'siteorigin_north_post_class_filter' );

if ( ! function_exists( 'siteorigin_north_disable_responsive' ) ) {
	/**
	 * Disable responsive layout.
	 */
	function siteorigin_north_disable_responsive() {
		if ( siteorigin_setting( 'responsive_disabled' ) == false ) {
			echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
		}
	}
}
add_action( 'wp_head', 'siteorigin_north_disable_responsive', 0 );

if ( ! function_exists( 'siteorigin_north_widgets_init' ) ) {
	/**
	 * Register widget area.
	 *
	 * @see http://codex.wordpress.org/Function_Reference/register_sidebar
	 */
	function siteorigin_north_widgets_init() {
		register_sidebar( array(
			'name'          => esc_html__( 'Main Sidebar', 'siteorigin-north' ),
			'id'            => 'main-sidebar',
			'description'   => esc_html__( 'Visible on posts and pages that use the default layout.', 'siteorigin-north' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Footer Widgets', 'siteorigin-north' ),
			'id'            => 'footer-sidebar',
			'description'   => esc_html__( 'A column will be automatically assigned to each widget inserted.', 'siteorigin-north' ),
			'before_widget' => '<div class="widget-wrapper"><aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside></div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Top Bar', 'siteorigin-north' ),
			'id'            => 'topbar-sidebar',
			'description'   => esc_html__( 'Replaces the top bar text.', 'siteorigin-north' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

		if ( function_exists( 'is_woocommerce' ) ) {
			register_sidebar( array(
				'name'          => esc_html__( 'Shop Sidebar', 'siteorigin-north' ),
				'id'            => 'sidebar-shop',
				'description'   => esc_html__( 'Displays on WooCommerce pages.', 'siteorigin-north' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			) );
		}
	}
}
add_action( 'widgets_init', 'siteorigin_north_widgets_init' );

if ( ! function_exists( 'siteorigin_north_scripts' ) ) {
	/**
	 * Enqueue scripts and styles.
	 */
	function siteorigin_north_scripts() {
		// Theme stylesheet.
		wp_enqueue_style(
			'siteorigin-north-style',
			get_template_directory_uri() . '/style' . SITEORIGIN_THEME_CSS_PREFIX . '.css',
			array(),
			SITEORIGIN_THEME_VERSION
		);

		// Theme icons.
		wp_enqueue_style(
			'siteorigin-north-icons', get_template_directory_uri() . '/css/north-icons' . SITEORIGIN_THEME_CSS_PREFIX . '.css',
			array(),
			SITEORIGIN_THEME_VERSION
		);

		// Flexslider
		wp_enqueue_style(
			'siteorigin-north-flexslider',
			get_template_directory_uri() . '/css/flexslider' . SITEORIGIN_THEME_CSS_PREFIX . '.css'
		);

		wp_enqueue_script(
			'jquery-flexslider',
			get_template_directory_uri() . '/js/jquery.flexslider' . SITEORIGIN_THEME_JS_PREFIX . '.js',
			array( 'jquery' ),
			'2.6.3',
			true
		);

		// jQuery Transit.
		wp_enqueue_script(
			'jquery-transit',
			get_template_directory_uri() . '/js/jquery.transit' . SITEORIGIN_THEME_JS_PREFIX . '.js',
			array( 'jquery' ),
			'0.9.12',
			true
		);

		// jQuery FitVids.
		if ( siteorigin_setting( 'responsive_fitvids' ) ) {
			wp_enqueue_script(
				'jquery-fitvids',
				get_template_directory_uri() . '/js/jquery.fitvids' . SITEORIGIN_THEME_JS_PREFIX . '.js',
				array( 'jquery' ),
				'1.1',
				true
			);
		}

		// Theme JavaScript.
		wp_enqueue_script(
			'siteorigin-north-script',
			get_template_directory_uri() . '/js/north' . SITEORIGIN_THEME_JS_PREFIX . '.js',
			array( 'jquery' ),
			SITEORIGIN_THEME_VERSION,
			true
		);

		// Skip link focus fix.
		wp_enqueue_script(
			'siteorigin-north-skip-link',
			get_template_directory_uri() . '/js/skip-link-focus-fix' . SITEORIGIN_THEME_JS_PREFIX . '.js',
			array(),
			SITEORIGIN_THEME_VERSION,
			true
		);

		// Add settings variables used by main theme JS.
		$logo_sticky_scale = apply_filters( 'siteorigin_north_logo_sticky_scale', 0.755 );
		$sticky_topbar = apply_filters( 'siteorigin_north_sticky_topbar', false );
		wp_localize_script(
			'siteorigin-north-script',
			'siteoriginNorth',
			array(
				'smoothScroll' => siteorigin_setting( 'navigation_smooth_scroll' ),
				'logoScale' => is_numeric( $logo_sticky_scale ) ? $logo_sticky_scale : 0.755,
				'collapse' => siteorigin_setting( 'responsive_menu_breakpoint' ),
				'fitvids' => siteorigin_setting( 'responsive_fitvids' ),
				'stickyTopbar' => $sticky_topbar,
			)
		);

		// Comment reply.
		if (
			is_singular() &&
			comments_open() &&
			get_option( 'thread_comments' )
		) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'siteorigin_north_scripts' );

/**
 * Enqueue Block Editor styles.
 */
function siteorigin_north_block_editor_styles() {
	wp_enqueue_style( 'siteorigin-north-block-editor-styles', get_template_directory_uri() . '/style-editor.css', SITEORIGIN_THEME_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'siteorigin_north_block_editor_styles' );

if ( ! function_exists( 'siteorigin_north_siteorigin_premium' ) ) {
	function siteorigin_north_siteorigin_premium( $themes ) {
		$themes[] = 'siteorigin-north';

		return $themes;
	}
}
add_filter( 'siteorigin_premium_themes', 'siteorigin_north_siteorigin_premium' );

if ( ! function_exists( 'siteorigin_north_filter_comment_form_default_fields' ) ) {
	/**
	 * Modify comments form - change placeholders.
	 */
	function siteorigin_north_filter_comment_form_default_fields( $fields ) {
		$placeholders = apply_filters(
			'siteorigin_north_comment_form_placeholders',
			array(
				'author' => esc_html__( 'Enter Your Name', 'siteorigin-north' ),
				'email'  => esc_html__( 'Enter Your Email', 'siteorigin-north' ),
				'url'    => esc_html__( 'Your Site URL', 'siteorigin-north' ),
			)
		);

		$default_author = array( '<label for="author"', '<input id="author" ' );
		$default_email = array( '<label for="email"', '<input id="email" ' );
		$default_url = array( '<label for="url"', '<input id="url" ' );

		$replace_author = array(
			'<label for="author" class="screen-reader-text"',
			'<input id="author" placeholder="' . esc_attr( $placeholders['author'] ) . '" ',
		);
		$replace_email = array(
			'<label for="email" class="screen-reader-text"',
			'<input id="email" placeholder="' . esc_attr( $placeholders['email'] ) . '" ',
		);
		$replace_url = array(
			'<label for="url" class="screen-reader-text"',
			'<input id="url" placeholder="' . esc_attr( $placeholders['url'] ) . '" ',
		);

		if ( isset( $fields['author'] ) ) {
			$fields['author'] = str_replace(
				$default_author,
				$replace_author,
				$fields['author']
			);
		}

		if ( isset( $fields['email'] ) ) {
			$fields['email'] = str_replace(
				$default_email,
				$replace_email,
				$fields['email']
			);
		}

		if ( isset( $fields['url'] ) ) {
			$fields['url'] = str_replace(
				$default_url,
				$replace_url,
				$fields['url']
			);
		}

		return $fields;
	}
}
add_filter( 'comment_form_default_fields', 'siteorigin_north_filter_comment_form_default_fields' );

if ( ! function_exists( 'siteorigin_north_filter_comment_form_defaults' ) ) {
	/**
	 * Modify comments form - make labels screen-reader-text
	 */
	function siteorigin_north_filter_comment_form_defaults( $defaults ) {
		$comment_placeholder = __( 'Enter your message', 'siteorigin-north' );
		$default_comment = array(
			'<label for="comment"',
			'<textarea id="comment" '
		);

		$replace_comment = array(
			'<label for="comment" class="screen-reader-text"',
			'<textarea id="comment" placeholder="' . esc_attr( $comment_placeholder ) . '" ',
		);

		if ( ! empty( $defaults['comment_field'] ) ) {
			$defaults['comment_field'] = str_replace(
				$default_comment,
				$replace_comment,
				$defaults['comment_field']
			);
			$defaults['comment_field'] = '<div class="clear"></div>' . $defaults['comment_field'];
		}

		return $defaults;
	}
}
add_filter( 'comment_form_defaults', 'siteorigin_north_filter_comment_form_defaults' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/*
 * Load Jetpack compatibility file.
 */
if ( class_exists( 'Jetpack' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load the theme settings file
 */
require get_template_directory() . '/inc/settings.php';

/**
 * Support for SiteOrigin Page Builder
 */
require get_template_directory() . '/inc/siteorigin-panels.php';

/*
 * Load support for WooCommerce.
 */
if ( function_exists( 'is_woocommerce' ) ) {
	require get_template_directory() . '/woocommerce/functions.php';
}

/* IMPORTANT NOTICE: Please don't edit this file; any changes made here will be lost during the theme update process.
If you need to add custom functions, use the Code Snippets plugin (https://wordpress.org/plugins/code-snippets/) or a child theme. */
