/**
 * File jquery.theme.js.
 *
 * Handles the primary JavaScript functions for the theme.
 */

( function( $ ) {

	// Burst animation plugin.
	var mousePos = {x: 0, y: 0};
	$( document ).on( 'mousemove', function( e ) {
		mousePos = {
			x: e.pageX,
			y: e.pageY
		};
	} );

	$.fn.burstAnimation = function( options ) {
		var settings = $.extend( {
			event: "click",
			container: "parent"
		}, options );

		return $( this ).each( function() {
			var $$ = $( this ),
				$p = settings.container === 'parent' ? $$.parent() : $$.closest( settings.container ),
				$o = $( '<div class="burst-animation-overlay"></div>' ),
				$c = $( '<div class="burst-circle"></div>' ).appendTo( $o );

			$$.on( settings.event, function() {
				$o.appendTo( $p );
				$c
					.css( {
						top: mousePos.y - $p.offset().top,
						left: mousePos.x - $p.offset().left,
						opacity: 0.1,
						scale: 1
					} )
					.transition( {
						opacity: 0,
						scale: $p.width()
					}, 500, 'ease', function() {
						$o.detach();
					} );
			} );

		} );
	};

	// Check if an element is visible in the viewport.
	$.fn.northIsVisible = function() {
		var rect = this[0].getBoundingClientRect();
		return (
			rect.bottom >= 0 &&
			rect.right >= 0 &&
			rect.top <= ( window.innerHeight || document.documentElement.clientHeight ) &&
			rect.left <= ( window.innerWidth || document.documentElement.clientWidth )
		);
	};

	var headerHeight = function( $target, load ) {
		var height = 0;

		if ( $( '#masthead' ).hasClass( 'sticky-menu' ) && $( '#masthead' ).data( 'scale-logo' ) ) {
			if ( typeof $target != 'undefined' && $target.offset().top < 48 ) {
				height += $( '#masthead' ).outerHeight();
			} else if (
				$( '.site-branding' ).outerHeight() > $( '#site-navigation' ).outerHeight() ||
				(
					typeof $target != 'undefined' && load && $target.offset().top > 48
				)
			) {
				height += $( '#masthead' ).outerHeight() * siteoriginNorth.logoScale;
			} else {
				height += $( '#masthead' ).height() + ( $( '#masthead' ).innerHeight() - $( '#masthead' ).height() );
			}
		} else if ( $( '#masthead' ).hasClass( 'sticky-menu' ) ) {
			height += $( '#masthead' ).outerHeight();
		}

		if ( $( 'body' ).hasClass( 'admin-bar' ) ) {
			height += $( '#wpadminbar' ).outerHeight();
		}

		return height;
	};

	$.fn.northSmoothScroll = function() {
		$( this ).on( 'click', function( e ) {
			var $a = $( this );
			var $target = $( '[name=' + this.hash.slice( 1 ) + ']' ).length ? $( '[name=' + this.hash.slice( 1 ) + ']' ) : $( $a.get( 0 ).hash );
			if ( $target.length ) {
				$( 'html, body' ).stop().animate( {
					scrollTop: $target.offset().top - headerHeight( $target )
				}, 1000 );

				return false;
			}
			// Scroll to the position of the item, minus the header size.
		} );
	}

	$( '.entry-meta a' ).on( 'mouseenter', function() {
		$( this ).closest( 'li' ).addClass( 'hovering' );
	} );

	$( '.entry-meta a' ).on( 'mouseout', function() {
		$( this ).closest( 'li' ).removeClass( 'hovering' );
	} );

	// Setup FitVids for entry content, panels and WooCommerce. Ignore Tableau.
	if ( typeof $.fn.fitVids !== 'undefined' && siteoriginNorth.fitvids ) {
		$( '.entry-content, .entry-content .panel, .woocommerce #main' ).fitVids( { ignore: '.tableauViz' } );
	}

	// Detect if is a touch device. We detect this through ontouchstart, msMaxTouchPoints and MaxTouchPoints.
	if ( 'ontouchstart' in document.documentElement || window.navigator.msMaxTouchPoints || window.navigator.MaxTouchPoints ) {
		if ( /iPad|iPhone|iPod/.test( navigator.userAgent ) && ! window.MSStream ) {
			$( 'body' ).css( 'cursor', 'pointer' );
			$( 'body' ).addClass( 'ios' );
		}

		$( '.main-navigation #primary-menu' ).find( '.menu-item-has-children > a' ).each( function() {
			$( this ).on( 'click touchend', function( e ) {
				var link = $( this );
				e.stopPropagation();

				if ( e.type == 'click' ) {
					return;
				}

				if ( ! link.parent().hasClass( 'hover' ) ) {
					// Remove .hover from all other sub menus
					$( '.menu-item.hover' ).removeClass( 'hover' );
					link.parents('.menu-item').addClass( 'hover' );
					e.preventDefault();
				}

				// Remove .hover class when user clicks outside of sub menu
				$( document ).one( 'click', function() {
					link.parent().removeClass( 'hover' );
				} );

			} );
		} );
	}

	// Remove the no-js body class.
	$( 'body.no-js' ).removeClass( 'no-js' );
	if ( $( 'body' ).hasClass( 'css3-animations' ) ) {

		var alignMenu = function() {
			$( '#primary-menu > li > ul.sub-menu' ).each( function() {
				var $$ = $( this );
				var left = - (
					$$.parents( 'li' ).width() - $$.width()
					) / 2;
				$$.css( 'left', - left );
			} );
		};
		alignMenu();

		// Add keyboard access to the menu.
		$( '.menu-item' ).children( 'a' ).on( 'focusin', function() {
			$( this ).parents( 'ul, li' ).addClass( 'focus' );
		} );
		// Click event fires after focus event.
		$( '.menu-item' ).children( 'a' ).on( 'click', function() {
			$( this ).parents( 'ul, li' ).removeClass( 'focus' );
		} );
		$( '.menu-item' ).children( 'a' ).on( 'focusout', function() {
			$( this ).parents( 'ul, li' ).removeClass( 'focus' );
		} );

		// Burst animatin when the user clicks on a sub link.
		$( '.main-navigation ul ul li a' ).burstAnimation( {
			event: "click",
			container: "parent"
		} );
	}

	// Handle displaying the mobile menu.
	var $mobileMenu = false;
	$( '#mobile-menu-button' ).on( 'click', function( e ) {
		e.preventDefault();
		var $$ = $( this );
		$$.toggleClass( 'to-close' );

		if ( $mobileMenu === false ) {
			$mobileMenu = $( '<div></div>' )
				.append( $( '.main-navigation .menu ul, .main-navigation ul.menu' ).first().clone() )
				.attr( 'id', 'mobile-navigation' )
				.appendTo( '#masthead' ).hide();

			if ( $( '#header-search form' ).length ) {
				$mobileMenu.append( $( '#header-search form' ).clone() );
			}

			if ( $( '.main-navigation .shopping-cart' ).length ) {
				$mobileMenu.append( $( '.main-navigation .shopping-cart .shopping-cart-link' ).clone() );
			}

			$mobileMenu.find( '#primary-menu' ).show().css( 'opacity', 1 );

			$mobileMenu.find( '.menu-item-has-children > a' ).addClass( 'has-dropdown' );

			$mobileMenu.find( '.has-dropdown' ).after( '<button class="dropdown-toggle" aria-expanded="false"><i class="north-icon-next"></i></button>' );

			$mobileMenu.find( '.dropdown-toggle' ).on( 'click', function( e ) {
				e.preventDefault();
				$( this ).toggleClass( 'toggle-open' ).next( '.children, .sub-menu' ).slideToggle( 'fast' );
			} );

			$mobileMenu.find( '.has-dropdown' ).on( 'click', function( e ) {
				if ( typeof $( this ).attr( 'href' ) === "undefined" || $( this ).attr( 'href' ) == "#" ) {
					e.preventDefault();
					$( this ). siblings( '.dropdown-toggle' ).trigger( 'click' );
				}
			} );

			var mmOverflow = function() {
				if ( $( '#masthead' ).hasClass( 'sticky-menu' ) ) {
					// Don't let the height of the dropdown extend below the bottom of the screen.
					var adminBarHeight = $( '#wpadminbar' ).css( 'position' ) === 'fixed' ? $( '#wpadminbar' ).outerHeight() : 0;
					var topBarHeight = $( '#topbar' ).outerHeight();
					var mhHeight = $( '#masthead' ).innerHeight();
					if ( $( 'body' ).hasClass( 'no-topbar' ) || ( ! $( 'body' ).hasClass( 'no-topbar' ) &&  $( 'body' ).hasClass( 'topbar-out' ) ) ) {
						var mobileMenuHeight = $( window ).height() - mhHeight - adminBarHeight;
					} else if ( ! $( 'body' ).hasClass( 'no-topbar' ) &&  ! $( 'body' ).hasClass( 'topbar-out' ) ) {
						var mobileMenuHeight = $( window ).height() - mhHeight - adminBarHeight - topBarHeight;
					}

					$( '#mobile-navigation' ).css( 'max-height', mobileMenuHeight );
				}
			}
			mmOverflow();

			$( window ).on( 'resize', mmOverflow );
			$( '#mobile-navigation' ).on( 'scroll', mmOverflow );
		}

		$mobileMenu.slideToggle( 'fast' );

		$( '#mobile-navigation a' ).on( 'click', function( e ) {
			if ( ! $( this ).hasClass( 'has-dropdown' ) || ( typeof $( this ).attr( 'href' ) !== "undefined" && $( this ).attr( 'href' )  !== "#" ) ) {
				if ( $mobileMenu.is( ':visible' ) ) {
					$mobileMenu.slideUp( 'fast' );
				}
				$$.removeClass( 'to-close' );
			}
		} );

		if ( siteoriginNorth.smoothScroll ) {
			$( '#mobile-navigation a[href*="#"]:not([href="#"])' ).northSmoothScroll();
		}

	} );

	// The scroll to top button.
	var sttWindowScroll = function() {
		var top = window.pageYOffset || document.documentElement.scrollTop;
		var scrollOffset = $( '#masthead' ).length ? $( '#masthead' ).outerHeight() : $( window ).outerHeight() / 2;

		if ( top > scrollOffset ) {
			if ( ! $( '#scroll-to-top' ).hasClass( 'show' ) ) {
				$( '#scroll-to-top' ).css( 'pointer-events', 'auto' ).addClass( 'show' );
			}
		} else if ( $( '#scroll-to-top' ).hasClass( 'show' ) ) {
			$( '#scroll-to-top' ).css( 'pointer-events', 'none' ).removeClass( 'show' );
		}
	};

	sttWindowScroll();
	$( window ).on( 'scroll', sttWindowScroll );
	$( '#scroll-to-top' ).on( 'click', function() {
		$( 'html,body' ).stop().animate( { scrollTop: 0 } );
	} );

	// Handle the header search.
	var $hs = $( '#header-search' );
	$( '#masthead .north-search-icon' ).on( 'click', function() {
		$hs.fadeIn( 'fast' );
		$hs.find( 'form' ).css( 'margin-top', - $hs.find( 'form' ).outerHeight() / 2 );
		$hs.find( 'input[type="search"]' ).trigger( 'focus' ).trigger( 'select' );
		$hs.find( '#close-search' ).addClass( 'animate-in' );
	} );
	$hs.find( '#close-search' ).on( 'click', function() {
		$hs.fadeOut( 350 );
		$( this ).removeClass( 'animate-in' );
	} );
	$( window ).on( 'scroll', function() {
		if ( $hs.is( ':visible' ) ) {
			$hs.find( 'form' ).css( 'margin-top', - $hs.find( 'form' ).outerHeight() / 2 );
		}
	} );

	// Close the header search when clicking outside of the search field or open search button.
	$( '#header-search input[type=search]' ).on( 'focusout', function( e ) {
		$( '#close-search.animate-in' ).trigger( 'click' );
	} );

	// Close search with escape key.
	$( document ).on( 'keyup', function( e ) {
		if ( e.keyCode == 27 ) { // Escape key maps to keycode `27`.
			$( '#close-search.animate-in' ).trigger( 'click' );
		}
	} );

	// Add class to calendar elements that have links.
	$( '#wp-calendar tbody td:has(a)' ).addClass( 'has-link' );

	// Gallery format image slider.
	$( document ).ready( function() {
		if ( typeof $.fn.flexslider == 'function' ) {
			$( '.gallery-format-slider' ).flexslider( {
				animation: "slide",
			} );
		}
	} );

	// Detect potential page jump on load and prevent it.
	if ( location.pathname.replace( /^\//,'' ) == window.location.pathname.replace( /^\//,'' ) && location.hostname == window.location.hostname ) {
		setTimeout( function() {
			window.scrollTo( 0, 0 );
		}, 1 );
		var scrollOnLoad = true;
	}

	$( window ).on( 'load', function() {
		siteoriginNorth.logoScale = parseFloat( siteoriginNorth.logoScale );

		// Handle smooth scrolling.
		if ( siteoriginNorth.smoothScroll ) {
			$( '#site-navigation a[href*="#"]:not([href="#"])' ).add( 'a[href*="#"]:not([href="#"])' ).not( '.lsow-tab a[href*="#"]:not([href="#"]), .wc-tabs a[href*="#"]:not([href="#"]), .iw-so-tab-title a[href*="#"]:not([href="#"]), .comment-navigation a[href*="#"]' ).northSmoothScroll();
		}

		var $mh = $( '#masthead' ),
			mhPadding = {
				top: parseInt( $mh.css( 'padding-top' ) ),
				bottom: parseInt( $mh.css( 'padding-bottom' ) )
			};

		if ( $mh.data( 'scale-logo' ) ) {
			var $img = $mh.find( '.site-branding img' ),
				imgWidth = $img.width(),
				imgHeight = $img.height(),
				scaledWidth = imgWidth * siteoriginNorth.logoScale,
				scaledHeight = imgHeight * siteoriginNorth.logoScale;

			$( '.site-branding img' ).wrap( '<span class="custom-logo-wrapper"></span>' );

			var smResizeLogo = function() {
				var $branding = $mh.find( '.site-branding > *' ),
					top = window.pageYOffset || document.documentElement.scrollTop;

				// Check if the menu is meant to be sticky or not, and if it is apply padding/class
				if ( top > 0 ) {
					$mh.css( {
						'padding-top': mhPadding.top * siteoriginNorth.logoScale,
						'padding-bottom': mhPadding.bottom * siteoriginNorth.logoScale
					} );

				} else {
					$mh.css( {
						'padding-top': mhPadding.top,
						'padding-bottom': mhPadding.bottom
					} );
				}

				if ( $img.length ) {
					// Are we at the top of the page?
					if ( top > 0 ) {
						// Calulate scale amount based on distance from the top of the page.
						var logoScale = siteoriginNorth.logoScale + ( Math.max( 0, 48 - top ) / 48 * ( 1 - siteoriginNorth.logoScale ) );
						if ( $img.height() != scaledHeight || $img.width() != scaledWidth || logoScale != siteoriginNorth.logoScale ) {
							$( '.site-branding img' ).css( {
								width: logoScale * 100 + '%',
							} );
						}
					} else {
						// Ensure no scaling is present.
						$( '.site-branding img' ).css( {
							width: '',
						} );
					}

				} else if ( top > 0 ) {
					$branding.css( 'transform', 'scale(' + siteoriginNorth.logoScale + ')' );

				} else {
					$branding.css( 'transform', 'scale(1)' );
				}
			};
			smResizeLogo();
			$( window ).on( 'scroll resize', smResizeLogo );
		}

		// Now lets do the sticky menu.
		if ( $( '#masthead' ).hasClass( 'sticky-menu' ) ) {
			var $mh = $( '#masthead' ),
				$mhs = $( '<div class="masthead-sentinel"></div>' ).insertAfter( $mh ),
				$tb = $( '#topbar' ),
				$wpab = $( '#wpadminbar' );

			var whenToStickyMh = function() {
				var wpabMobile = $( window ).width() <= 600;
				var wpabHeight = $wpab.length && ! wpabMobile ? $wpab.outerHeight() : 0;
				var tbHeight = $tb.length && siteoriginNorth.stickyTopbar ? $tb.outerHeight() : 0;

				return wpabHeight + tbHeight;
			};

			// Sticky header shadow.
			var smShadow = function() {
				if ( $( window ).scrollTop() > whenToStickyMh ) {
					$( $mh ).addClass( 'floating' );
				} else {
					$( $mh ).removeClass( 'floating' );
				}
			};

			smShadow();
			$( window ).on( 'scroll', smShadow );

			var smSetup = function() {

				if ( $( 'body' ).hasClass( 'mobile-header-ns' ) && ( $( window ).width() < siteoriginNorth.collapse ) ) {
					return;
				}

				if ( ! $( 'body' ).hasClass( 'page-layout-menu-overlap' ) ) {
					$mhs.css( 'height', $mh.outerHeight() );
				}
				// Toggle .topbar-out with visibility of top-bar in the viewport.
				if ( ! $( 'body' ).hasClass( 'no-topbar' ) && ! $tb.northIsVisible() ) {
					$( 'body' ).addClass( 'topbar-out' );
				}
				if ( $tb.length && $( 'body' ).hasClass( 'topbar-out' ) && $tb.northIsVisible() ) {
					$( 'body' ).removeClass( 'topbar-out' );
				}

				if (
					$( 'body' ).hasClass( 'no-topbar' ) ||
					(
						! $( 'body' ).hasClass( 'no-topbar' ) &&
						$( 'body' ).hasClass( 'topbar-out' )
					)
				) {
					$mh.css( 'position', 'fixed' );
				} else if ( ! $( 'body' ).hasClass( 'no-topbar' ) &&  ! $( 'body' ).hasClass( 'topbar-out' ) ) {
					$mh.css( 'position', 'absolute' );
				}

				if ( $( 'body' ).hasClass( 'no-topbar' ) && ! $( window ).scrollTop() ) {
					$( 'body' ).addClass( 'topbar-out' );
				}

				if ( $( window ).width() < 601 && $( 'body' ).hasClass( 'admin-bar' ) ) {
					if ( ! $wpab.northIsVisible() ) {
						if ( $( 'body' ).hasClass( 'no-topbar' ) || ( ! $( 'body' ).hasClass( 'no-topbar' ) &&  $( 'body' ).hasClass( 'topbar-out' ) ) ) {
							$mh.addClass( 'mobile-sticky-menu' );
						}
					}
					if ( $wpab.northIsVisible() ) {
						$mh.removeClass( 'mobile-sticky-menu' );
					}
				}

				if ( $( window ).width() > 600 && $mh.hasClass( 'mobile-sticky-menu' ) ) {
					$mh.removeClass( 'mobile-sticky-menu' );
				}
			}

			if ( whenToStickyMh() === 0 || ! siteoriginNorth.stickyTopbar ) {
				smSetup();
				$( window ).on( 'resize scroll', smSetup );
			} else {
				var tbMhStickyPosition = function() {
					var wpabMobile = $( window ).width() <= 600;
					$tb.css( {
						'position': 'sticky',
						'top': $wpab.length && ! wpabMobile ? $wpab.outerHeight() : 0,
					} );

					$mh.css( {
						'position': 'sticky',
						'top': whenToStickyMh(),
					} );
				};

				tbMhStickyPosition();
				$( window ).on( 'resize', tbMhStickyPosition );
			}
		}

		// Adjust for sticky header when linking from external anchors.
		if ( typeof scrollOnLoad != "undefined" ) {
			var $target = $( window.location.hash );
			if ( $target.length ) {
				setTimeout( function() {
					$( 'html, body' ).stop().animate( {
						scrollTop: $target.offset().top - headerHeight( $target, true )
					},
					0,
					function() {
						if ( $( '#masthead' ).hasClass( 'sticky-menu' ) ) {
							// Avoid a situation where the logo can be incorrectly sized due to the page jump.
							smSetup();
						}
					} );
				}, 100 );
			}
		}
	} );

} )( jQuery );
