/* globals jQuery */

// The burst animation plugin
(
	function ( $ ) {

		var mousePos = {x: 0, y: 0};
		$( document ).mousemove( function ( e ) {
			mousePos = {
				x: e.pageX,
				y: e.pageY
			};
		} );

		$.fn.burstAnimation = function ( options ) {
			var settings = $.extend( {
				event: "click",
				container: "parent"
			}, options );

			return $( this ).each( function () {
				var $$ = $( this ),
					$p = settings.container === 'parent' ? $$.parent() : $$.closest( settings.container ),
					$o = $( '<div class="burst-animation-overlay"></div>' ),
					$c = $( '<div class="burst-circle"></div>' ).appendTo( $o );

				$$.on( settings.event, function () {
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
						}, 500, 'ease', function () {
							$o.detach();
						} );
				} );

			} );
		};

		$.fn.northSmoothScroll = function () {
			$( this ).click( function ( e ) {
				var $a = $( this );
				var $target = $( '[name=' + this.hash.slice( 1 ) + ']' ).length ? $( '[name=' + this.hash.slice( 1 ) + ']' ) : $( $a.get( 0 ).hash );

				if ( $target.length ) {

					var height = 0;
					if ( $( '#masthead' ).hasClass( 'sticky-menu' ) ) {
						height += $( '#masthead' ).outerHeight();
					}
					if ( $( 'body' ).hasClass( 'admin-bar' ) ) {
						height += $( '#wpadminbar' ).outerHeight();
					}

					$( 'html, body' ).animate( {
						scrollTop: $target.offset().top - height
					}, 1000 );

					return false;
				}
				// Scroll to the position of the item, minus the header size
			} );
		}

	}
)( jQuery );

jQuery( function ( $ ) {

	$( '.entry-meta a' ).hover(
		function () {
			$( this ).closest( 'li' ).addClass( 'hovering' );
		},
		function () {
			$( this ).closest( 'li' ).removeClass( 'hovering' );
		}
	);

    // Setup FitVids for entry content, panels and WooCommerce. Ignore Tableau.
    if ( typeof $.fn.fitVids !== 'undefined' ) {
        $( '.entry-content, .entry-content .panel, .woocommerce #main' ).fitVids( { ignore: '.tableauViz' } );
    }

	// This this is a touch device. We detect this through ontouchstart, msMaxTouchPoints and MaxTouchPoints.
	if( 'ontouchstart' in document.documentElement || window.navigator.msMaxTouchPoints || window.navigator.MaxTouchPoints ) {
		$('body').removeClass('no-touch');
	}
	if ( !$( 'body' ).hasClass( 'no-touch' ) ) {
		if ( /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream ) {
			$( 'body' ).css( 'cursor', 'pointer' );
		}
		$( '.main-navigation #primary-menu').find('.menu-item-has-children > a' ).each( function() {
			$( this ).click( function(e){
				var link = $(this);
				e.stopPropagation();
				link.parent().addClass( 'touch-drop' );

				if( link.hasClass( 'hover' ) ) {
					link.unbind( 'click' );
				} else {
					link.addClass( 'hover' );
					e.preventDefault();
				}

				$( '.main-navigation #primary-menu > .menu-item-has-children:not(.touch-drop) > a' ).click( function() {
					link.removeClass('hover').parent().removeClass('touch-drop');
				} );

				$( document ).click( function() {
					link.removeClass( 'hover' ).parent().removeClass( 'touch-drop' );
				} );

			} );
		} );
	}

	// Remove the no-js body class
	$( 'body.no-js' ).removeClass( 'no-js' );
	if ( $( 'body' ).hasClass( 'css3-animations' ) ) {
		// Display the burst animation
		$( '.search-field' ).burstAnimation( {
			event: "focus",
			container: ".search-form"
		} );

		var resetMenu = function () {
			$( '.main-navigation ul ul' ).each( function () {
				var $$ = $( this );
				var width = Math.max.apply( Math, $$.find( '> li > a' ).map( function () {
					return $( this ).width();
				} ).get() );
				$$.find( '> li > a' ).width( width );
			} );
		};
		resetMenu();
		$( window ).resize( resetMenu );

		var alignMenu = function () {
			$( '#primary-menu > li > ul.sub-menu' ).each( function () {
				var $$ = $( this );
				var left = - (
					$$.parents( 'li' ).width() - $$.width()
					) / 2;
				$$.css( 'left', - left );
			} );
		};
		alignMenu();

		// Add keyboard access to the menu
		$( '.menu-item' ).children( 'a' ).focus( function () {
			$( this ).parents( 'ul, li' ).addClass( 'focus' );
		} );
		// Click event fires after focus event
		$( '.menu-item' ).children( 'a' ).click( function () {
			$( this ).parents( 'ul, li' ).removeClass( 'focus' );
		} );
		$( '.menu-item' ).children( 'a' ).focusout( function () {
			$( this ).parents( 'ul, li' ).removeClass( 'focus' );
		} );

		// Burst animatin when the user clicks on a sub link
		$( '.main-navigation ul ul li a' ).burstAnimation( {
			event: "click",
			container: "parent"
		} );
	}

	// Handle displaying the mobile menu
	var $mobileMenu = false;
	$( '#mobile-menu-button' ).click( function ( e ) {
		e.preventDefault();
		var $$ = $( this );
		$$.toggleClass( 'to-close' );

		if ( $mobileMenu === false ) {
			$mobileMenu = $( '<div></div>' )
				.append( $( '.main-navigation ul' ).first().clone() )
				.attr( 'id', 'mobile-navigation' )
				.appendTo( '#masthead' ).hide();

			if ( $( '#header-search form' ).length ) {
				$mobileMenu.append( $( '#header-search form' ).clone() );
			}

			if ( $( '.main-navigation .shopping-cart' ).length ) {
				$mobileMenu.append( $( '.main-navigation .shopping-cart .shopping-cart-link' ).clone() );
			}

			$mobileMenu.find( '#primary-menu' ).show().css( 'opacity', 1 );

			$mobileMenu.find( '.menu-item-has-children > a' ).after( '<button class="dropdown-toggle" aria-expanded="false"><i class="north-icon-next"></i></button>' );
			$mobileMenu.find( '.dropdown-toggle' ).click( function( e ) {
				e.preventDefault();
				$( this ).toggleClass('toggle-open').next( '.children, .sub-menu' ).slideToggle('fast');
			} );
		}

		$mobileMenu.slideToggle( 'fast' );

		$('#mobile-navigation a').click(function(e){
			if($mobileMenu.is(':visible') ) {
				$mobileMenu.slideUp( 'fast' );
			}
			$$.removeClass( 'to-close' );
		});

		if ( siteoriginNorth.smoothScroll ) {
			$( '#mobile-navigation a[href*="#"]:not([href="#"])' ).smoothScroll();
		}

	} );

	// The scroll to top button
	var sttWindowScroll = function () {
		var top = window.pageYOffset || document.documentElement.scrollTop;

		if ( top > $( '#masthead' ).outerHeight() ) {
			if ( ! $( '#scroll-to-top' ).hasClass( 'show' ) ) {
				$( '#scroll-to-top' ).css( 'pointer-events', 'auto' ).addClass( 'show' );
			}
		}
		else {
			if ( $( '#scroll-to-top' ).hasClass( 'show' ) ) {
				$( '#scroll-to-top' ).css( 'pointer-events', 'none' ).removeClass( 'show' );
			}
		}
	};
	sttWindowScroll();
	$( window ).scroll( sttWindowScroll );
	$( '#scroll-to-top' ).click( function () {
		$( 'html,body' ).animate( {scrollTop: 0} );
	} );

	// Now lets do the sticky menu

	if ( $( '#masthead' ).hasClass( 'sticky-menu' ) && ! $( 'body' ).hasClass( 'is-mobile-device' ) ) {
		var $mhs = false,
			mhTop = false,
			pageTop = $( '#page' ).offset().top,
			$mh = $( '#masthead' );

		var smSetup = function () {
			pageTop = $( '#page' ).offset().top;

			if ( $mhs === false ) {
				$mhs = $( '<div class="masthead-sentinel"></div>' ).insertAfter( $mh );
			}
			if ( mhTop === false ) {
				mhTop = $mh.offset().top;
			}


			var top = window.pageYOffset || document.documentElement.scrollTop;
			$mh.css( {
				'position': $( 'body' ).hasClass( 'page-layout-menu-overlap' ) ? 'fixed' : 'relative',
				'top': 0,
				'left': 0,
				'width': null,
			} );

			var adminBarOffset = $( '#wpadminbar' ).css( 'position' ) === 'fixed' ? $( '#wpadminbar' ).outerHeight() : 0;

			if ( top + adminBarOffset > $mh.offset().top ) {

				$mhs.show().css( {
					'height': $mh.outerHeight(),
					'margin-bottom': $mh.css( 'margin-bottom' )
				} );
				$mh.css( {
					'position': 'fixed',
					'top': adminBarOffset,
					'left': 0 - self.pageXOffset + 'px',
					'width': '100%',
				} );
			}
			else {
				$mhs.hide();
			}

			// Don't let the height of the dropdown extend below the bottom of the screen.
			var adminBarHeight = $( '#wpadminbar' ).css( 'position' ) === 'fixed' ? $( '#wpadminbar' ).outerHeight() : 0;
			var mobileMenuHeight = $( window ).height() - $( '#masthead' ).innerHeight() - adminBarHeight;

			if ( $('#mobile-navigation').outerHeight() > mobileMenuHeight ) {
				$( '#mobile-navigation' ).css( {
					'max-height': mobileMenuHeight,
					'overflow-y': 'scroll',
					'-webkit-overflow-scrolling' : 'touch'
				} );
			} else {
				$('#mobile-navigation').css('max-height', mobileMenuHeight );
			}
		};
		smSetup();


		$( window ).resize( smSetup ).scroll( smSetup );

		var mhPadding = {
			top: parseInt( $mh.css( 'padding-top' ) ),
			bottom: parseInt( $mh.css( 'padding-bottom' ) )
		};

		if ( $mh.data( 'scale-logo' ) ) {
			var smResizeLogo = function () {
				var top = window.pageYOffset || document.documentElement.scrollTop;
				top -= pageTop;

				var $img = $mh.find( '.site-branding img' ),
					$branding = $mh.find( '.site-branding > *' );

				if ( top > 0 ) {
					var scale = 0.775 + (
						Math.max( 0, 48 - top ) / 48 * (
						1 - 0.775
						)
						);

					if ( $img.length ) {
						$img.css( {
							width: $img.attr( 'width' ) * scale,
							height: $img.attr( 'height' ) * scale
						} );
					}
					else {
						$branding.css( 'transform', 'scale(' + scale + ')' );
					}

					$mh.css( {
						'padding-top': mhPadding.top * scale,
						'padding-bottom': mhPadding.bottom * scale
					} ).addClass( 'floating' );
				}
				else {
					if ( $img.length ) {
						$img.css( {
							width: $img.attr( 'width' ),
							height: $img.attr( 'height' )
						} );
					}
					else {
						$branding.css( 'transform', 'scale(1)' );
					}

					$mh.css( {
						'padding-top': mhPadding.top,
						'padding-bottom': mhPadding.bottom
					} ).removeClass( 'floating' );
				}
			};
			smResizeLogo();
			$( window ).scroll( smResizeLogo );
		}
	}

	// Handle the header search
	var $hs = $( '#header-search' );
	$( '#masthead .north-search-icon' ).click( function () {
		$hs.fadeIn( 'fast' );
		$hs.find( 'form' ).css( 'margin-top', - $hs.find( 'form' ).outerHeight() / 2 );
		$hs.find( 'input[type="search"]' ).focus().select();
		$hs.find( '#close-search' ).addClass( 'animate-in' );
	} );
	$hs.find( '#close-search' ).click( function () {
		$hs.fadeOut( 350 );
		$( this ).removeClass( 'animate-in' );
	} );
	$( window ).scroll( function () {
		if ( $hs.is( ':visible' ) ) {
			$hs.find( 'form' ).css( 'margin-top', - $hs.find( 'form' ).outerHeight() / 2 );
		}
	} );

	// Handle smooth scrolling
	if ( siteoriginNorth.smoothScroll ) {
		$( '#site-navigation a[href*="#"]:not([href="#"])' ).smoothScroll();
	}

	// Add class to calendar elements that have links
	$( '#wp-calendar tbody td:has(a)' ).addClass( 'has-link' );

} );
