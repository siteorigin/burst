<?php

if ( ! function_exists( 'siteorigin_north_woocommerce_change_hooks' ) ) {
	/**
	 * Adjust hooks to accomodate design.
	 */
	function siteorigin_north_woocommerce_change_hooks() {
		// Move the price higher.
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 4 );

		// Change the result count priority.
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		add_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 35 );

		// Use a custom upsell function to change number of items.
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		add_action( 'woocommerce_after_single_product_summary', 'siteorigin_north_woocommerce_output_upsells', 15 );

		// Remove actions in the cart.
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );

		if ( class_exists( 'SiteOrigin_Premium_Plugin_WooCommerce_Templates' ) ) {
			$so_wc_templates = get_option( 'so-wc-templates' );

			if (
				! empty( $so_wc_templates['cart'] ) &&
				! empty( $so_wc_templates['cart']['active'] )
			) {
				$prevent_override = true;
			}
		}

		if ( empty( $prevent_override ) ) {
			remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
		}

		// Product archive buttons.
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

		// Quick view action hooks.
		add_action( 'siteorigin_north_woocommerce_quick_view_images', 'siteorigin_north_woocommerce_quick_view_image', 5 );
		add_action( 'siteorigin_north_woocommerce_quick_view_title', 'woocommerce_template_single_price', 5 );
		add_action( 'siteorigin_north_woocommerce_quick_view_title', 'woocommerce_template_single_title', 5 );
		add_action( 'siteorigin_north_woocommerce_quick_view_content', 'woocommerce_template_loop_rating', 15 );
		add_action( 'siteorigin_north_woocommerce_quick_view_content', 'woocommerce_template_single_excerpt', 15 );
		add_action( 'siteorigin_north_woocommerce_quick_view_content', 'woocommerce_template_single_add_to_cart', 20 );

		// Remove the demo store notice.
		remove_action( 'wp_footer', 'woocommerce_demo_store' );
	}
}
add_action( 'after_setup_theme', 'siteorigin_north_woocommerce_change_hooks' );

// Make sure cart widget is displayed on cart & checkout pages.
function siteorigin_north_woocommerce_widget_cart_is_hidden( $is_cart ) {
	if ( ( is_cart() || is_checkout() ) && siteorigin_setting( 'woocommerce_display_checkout_cart' ) ) {
		return;
	} else {
		return $is_cart;
	}
}
add_filter( 'woocommerce_widget_cart_is_hidden', 'siteorigin_north_woocommerce_widget_cart_is_hidden', 10, 1 );

if ( ! function_exists( 'siteorigin_north_woocommerce_quick_view_image' ) ) {
	/**
	 * Displays image in the product quick view.
	 */
	function siteorigin_north_woocommerce_quick_view_image() {
		echo woocommerce_get_product_thumbnail( 'shop_single' );
	}
}

if ( ! function_exists( 'siteorigin_north_woocommerce_add_to_cart_text' ) ) {
	/**
	 * Displays add to cart text.
	 */
	function siteorigin_north_woocommerce_add_to_cart_text( $text ) {
		return $text;
	}
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'siteorigin_north_woocommerce_add_to_cart_text' );

if ( ! function_exists( 'siteorigin_north_woocommerce_enqueue_styles' ) ) {
	/**
	 * Enqueue WooCommerce styles.
	 */
	function siteorigin_north_woocommerce_enqueue_styles( $styles ) {
		$styles['northern-woocommerce'] = array(
			'src' => get_template_directory_uri() . '/woocommerce' . SITEORIGIN_THEME_CSS_PREFIX . '.css',
			'deps' => 'woocommerce-layout',
			'version' => SITEORIGIN_THEME_VERSION,
			'media' => 'all',
		);

		if ( is_rtl() ) {
			$styles['northern-woocommerce-rtl'] = array(
				'src' => get_template_directory_uri() . '/woocommerce-rtl' . SITEORIGIN_THEME_CSS_PREFIX . '.css',
				'deps' => 'northern-woocommerce',
				'version' => SITEORIGIN_THEME_VERSION,
				'media' => 'all',
			);
			$styles['northern-woocommerce-smallscreen-rtl'] = array(
				'src' => get_template_directory_uri() . '/woocommerce-smallscreen-rtl' . SITEORIGIN_THEME_CSS_PREFIX . '.css',
				'deps' => 'northern-woocommerce',
				'version' => SITEORIGIN_THEME_VERSION,
				'media' => 'only screen and (max-width: ' . apply_filters( 'woocommerce_style_smallscreen_breakpoint', $breakpoint = '768px' ) . ')',
			);
		}

		if ( siteorigin_setting( 'responsive_disabled' ) ) {
			unset( $styles['woocommerce-smallscreen'] );
			unset( $styles['northern-woocommerce-smallscreen-rtl'] );
		}

		return $styles;
	}
}
add_filter( 'woocommerce_enqueue_styles', 'siteorigin_north_woocommerce_enqueue_styles' );

if ( ! function_exists( 'siteorigin_north_woocommerce_enqueue_scripts' ) ) {
	/**
	 * Enqueue WooCommerce scripts.
	 */
	function siteorigin_north_woocommerce_enqueue_scripts() {
		if ( !function_exists( 'is_woocommerce' ) ) {
			return;
		}

		if ( is_woocommerce() || wc_post_content_has_shortcode( 'products' ) ) {
			wp_enqueue_script( 'siteorigin-north-woocommerce', get_template_directory_uri() . '/js/woocommerce.js', array( 'jquery' ), SITEORIGIN_THEME_VERSION );
			wp_localize_script( 'siteorigin-north-woocommerce', 'so_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}
	}
}
add_filter( 'wp_enqueue_scripts', 'siteorigin_north_woocommerce_enqueue_scripts' );

if ( ! function_exists( 'siteorigin_north_woocommerce_loop_shop_columns' ) ) {
	/**
	 * Define the number of columns in the loop.
	 */
	function siteorigin_north_woocommerce_loop_shop_columns() {
		return 3;
	}
}
add_filter( 'loop_shop_columns', 'siteorigin_north_woocommerce_loop_shop_columns' );

/**
 * Filter the archive page title.
 */
function siteorigin_north_woocommerce_archive_title() {
	if ( siteorigin_page_setting( 'page_title' ) ) {
		return true;
	}
}
add_filter( 'woocommerce_show_page_title', 'siteorigin_north_woocommerce_archive_title' );

if ( ! function_exists( 'siteorigin_north_woocommerce_related_product_args' ) ) {
	/**
	 * Define the number of columns/posts_per_page for related products.
	 */
	function siteorigin_north_woocommerce_related_product_args( $args ) {
		$args['columns'] = 3;
		$args['posts_per_page'] = 3;

		return $args;
	}
}
add_filter( 'woocommerce_output_related_products_args', 'siteorigin_north_woocommerce_related_product_args' );

if ( ! function_exists( 'siteorigin_north_woocommerce_output_upsells' ) ) {
	function siteorigin_north_woocommerce_output_upsells() {
		woocommerce_upsell_display( -1, 3 );
	}
}

if ( ! function_exists( 'siteorigin_north_woocommerce_template_single_undertitle_meta' ) ) {
	function siteorigin_north_woocommerce_template_single_undertitle_meta() {
		if ( ( class_exists( 'SiteOrigin_Premium' ) ) && ( class_exists( 'SiteOrigin_Premium_Plugin_WooCommerce_Templates' ) ) ) {
			return;
		}
		wc_get_template( 'single-product/meta-undertitle.php' );
	}
}
add_action( 'woocommerce_single_product_summary', 'siteorigin_north_woocommerce_template_single_undertitle_meta', 7 );

if ( ! function_exists( 'siteorigin_north_woocommerce_update_cart_count' ) ) {
	/**
	 * Update cart count with the masthead cart icon.
	 */
	function siteorigin_north_woocommerce_update_cart_count( $fragments ) {
		ob_start();
		?>
	<span class="shopping-cart-count"><?php echo WC()->cart->cart_contents_count; ?></span>
	<?php

		$fragments['span.shopping-cart-count'] = ob_get_clean();

		return $fragments;
	}
}

global $woocommerce;

if ( version_compare( $woocommerce->version, '3', '<' ) ) {
	add_filter( 'add_to_cart_fragments', 'siteorigin_north_woocommerce_update_cart_count' );
} else {
	add_filter( 'woocommerce_add_to_cart_fragments', 'siteorigin_north_woocommerce_update_cart_count' );
}

if ( ! function_exists( 'siteorigin_north_woocommerce_quick_view_button' ) ) {
	/**
	 * Add the quick view button in the products in loop.
	 */
	function siteorigin_north_woocommerce_quick_view_button() {
		global $product;
		echo '<a href="#" id="product-id-' . $product->get_id() . '" class="button product-quick-view-button" data-product-id="' . $product->get_id() . '">' . __( 'Quick View', 'siteorigin-north' ) . '</a>';
	}
}

if ( ! function_exists( 'siteorigin_north_woocommerce_archive_buttons' ) ) {
	/**
	 * Archive product buttons.
	 */
	function siteorigin_north_woocommerce_archive_buttons() { ?>
	<div>
		<?php
				if ( siteorigin_setting( 'woocommerce_display_quick_view' ) ) {
					siteorigin_north_woocommerce_quick_view_button();
				}

				woocommerce_template_loop_add_to_cart();
		?>
	</div>
<?php }
	}
add_action( 'woocommerce_after_shop_loop_item', 'siteorigin_north_woocommerce_archive_buttons' );

if ( ! function_exists( 'siteorigin_north_woocommerce_quick_view ' ) ) {
	/**
	 * Setup quick view modal in the footer.
	 */
	function siteorigin_north_woocommerce_quick_view() { ?>
	<!-- WooCommerce Quick View -->
	<div id="quick-view-container">
		<div id="product-quick-view" class="quick-view"></div>
	</div>
<?php }
	}
add_action( 'wp_footer', 'siteorigin_north_woocommerce_quick_view', 100 );

if ( ! function_exists( 'so_product_quick_view_ajax' ) ) {
	/**
	 * Add quick view modal content.
	 */
	function so_product_quick_view_ajax() {
		if ( ! isset( $_REQUEST['product_id'] ) ) {
			die();
		}

		$product_id = intval( $_REQUEST['product_id'] );

		// set the main wp query for the product
		wp( 'p=' . $product_id . '&post_type=product' );

		ob_start();

		// load content template
		wc_get_template( 'quick-view.php' );

		echo ob_get_clean();

		die();
	}
}
add_action( 'wp_ajax_so_product_quick_view', 'so_product_quick_view_ajax' );
add_action( 'wp_ajax_nopriv_so_product_quick_view', 'so_product_quick_view_ajax' );

/*
* Enabling breadcrumbs in product pages and archives.
*/
add_action( 'woocommerce_single_product_summary', 'siteorigin_north_breadcrumbs', 6, 0 );
add_action( 'woocommerce_before_shop_loop', 'siteorigin_north_breadcrumbs', 6, 0 );

if ( ! function_exists( 'siteorigin_north_paypal_icon' ) ) {
	/*
	* Return a standardised PayPal PNG icon.
	*/
	function siteorigin_north_paypal_icon() {
		return get_template_directory_uri() . '/woocommerce/images/paypal-icon.png';
	}
	add_filter( 'woocommerce_paypal_icon', 'siteorigin_north_paypal_icon' );
}

if ( ! function_exists( 'siteorigin_north_wc_columns' ) ) {
	// Change number of products per row
	function siteorigin_north_wc_columns() {
		return siteorigin_setting( 'woocommerce_archive_columns' );
	}
}
add_filter( 'loop_shop_columns', 'siteorigin_north_wc_columns' );

/**
 * Move the demo store banner to the top bar if enabled.
 */
function siteorigin_north_wc_demo_store() {
	if ( ! is_store_notice_showing() ) {
		return;
	}

	$notice = get_option( 'woocommerce_demo_store_notice' );

	if ( empty( $notice ) ) {
		$notice = esc_html__( 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'siteorigin-north' );
	}

	echo '<p class="woocommerce-store-notice demo_store">' . wp_kses_post( $notice ) . ' <a href="#" class="woocommerce-store-notice__dismiss-link">' . esc_html__( 'Dismiss', 'siteorigin-north' ) . '</a></p>';
}

if ( ! function_exists( 'siteorigin_north_wc_cart_contents' ) ) {
	function siteorigin_north_wc_cart_contents() {
		?>
		<tr>
			<td colspan="6" class="actions">
				<?php woocommerce_cart_totals(); ?>
			</td>
		</tr>
		<tr>
			<td colspan="6" class="actions wc-buttons">
				<table cellspacing="0">
					<tbody>
		<?php
	}
}
add_action( 'woocommerce_cart_contents', 'siteorigin_north_wc_cart_contents' );

if ( ! function_exists( 'siteorigin_north_wc_cart_contents_after' ) ) {
	function siteorigin_north_wc_cart_contents_after() {
		?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
	}
}
add_action( 'woocommerce_after_cart_contents', 'siteorigin_north_wc_cart_contents_after' );

if ( ! function_exists( 'siteorigin_north_wc_cart_actions' ) ) {
	function siteorigin_north_wc_cart_actions() {
		?>
		<div class="cart-buttons">
			<a class="button-continue-shopping button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<?php esc_html_e( 'Continue Shopping', 'siteorigin-north' ); ?>
			</a>
			
			<a class="checkout-button button" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
				<span class="north-icon-cart" aria-hidden="true"></span> <?php esc_html_e( 'Checkout', 'siteorigin-north' ); ?>
			</a>
		</div>
		<?php
	}
}
add_action( 'woocommerce_cart_actions', 'siteorigin_north_wc_cart_actions' );

if ( ! function_exists( 'siteorigin_north_wc_cart_wrapper_open' ) ) {
	function siteorigin_north_wc_cart_wrapper_open() {
		echo '<div class="cart-wrapper">';
	}
}
add_action( 'woocommerce_before_cart_table', 'siteorigin_north_wc_cart_wrapper_open' );

if ( ! function_exists( 'siteorigin_north_wc_cart_wrapper_close' ) ) {
	function siteorigin_north_wc_cart_wrapper_close() {
		echo '</div>';
	}
}
add_action( 'woocommerce_after_cart_table', 'siteorigin_north_wc_cart_wrapper_close' );
