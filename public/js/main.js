(function ($) {
	"use strict";

/*=============================================
	=    		 Preloader			      =
=============================================*/
function preloader() {
	$('#preloader').delay(1).fadeOut();
};
$(window).on('load', function () {
	preloader();
	wowAnimation();
});



/*=============================================
	=    		 Mobile Menu			      =
=============================================*/
//SubMenu Dropdown Toggle
if ($('.menu-area li.menu-item-has-children ul').length) {
	$('.menu-area .navigation li.menu-item-has-children').append('<div class="dropdown-btn"><span class="fas fa-angle-down"></span></div>');

}

//Mobile Nav Hide Show
if ($('.mobile-menu').length) {

	var mobileMenuContent = $('.menu-area .main-menu').html();
	$('.mobile-menu .menu-box .menu-outer').append(mobileMenuContent);

	//Dropdown Button
	$('.mobile-menu li.menu-item-has-children .dropdown-btn').on('click', function () {
		$(this).toggleClass('open');
		$(this).prev('ul').slideToggle(500);
	});
	//Menu Toggle Btn
	$('.mobile-nav-toggler').on('click', function () {
		$('body').addClass('mobile-menu-visible');
	});

	//Menu Toggle Btn
	$('.menu-backdrop, .mobile-menu .close-btn').on('click', function () {
		$('body').removeClass('mobile-menu-visible');
	});
}


/*=============================================
	=     Menu sticky & Scroll to top      =
=============================================*/
$(window).on('scroll', function () {
	var scroll = $(window).scrollTop();
	if (scroll < 245) {
		$("#sticky-header").removeClass("sticky-menu");
		$('.scroll-to-target').removeClass('open');

	} else {
		$("#sticky-header").addClass("sticky-menu");
		$('.scroll-to-target').addClass('open');
	}
});


/*=============================================
	=    		 Scroll Up  	         =
=============================================*/
if ($('.scroll-to-target').length) {
  $(".scroll-to-target").on('click', function () {
    var target = $(this).attr('data-target');
    // animate
    $('html, body').animate({
      scrollTop: $(target).offset().top
    }, 1000);

  });
}


/*=============================================
	=            Custom Scroll            =
=============================================*/
$(window).on("load", function () {
	if ($(".scroll").length) {
		$(".scroll").mCustomScrollbar({
			mouseWheelPixels: 50,
			scrollInertia: 0,
		});
	}
});
$('.activity-table-responsive').mCustomScrollbar({
	axis: "x",
	scrollbarPosition: "outside",
	theme: "custom-bar2",
	advanced: {
		autoExpandHorizontalScroll: true
	}
});

/*=============================================
=     Offcanvas Menu      =
=============================================*/
$(".menu-trigger").on("click", function () {
	$(".offcanvas-wrapper,.offcanvas-overly").addClass("active");
	return false;
});
$(".menu-close,.offcanvas-overly").on("click", function () {
	$(".offcanvas-wrapper,.offcanvas-overly").removeClass("active");
});


/*=============================================
=           Features - Active                 =
=============================================*/
$('.filter-toggle').on('click', function () {
	$('.filter-category-wrap').slideToggle(300);
	return false;
});


/*=============================================
=           Full Screen - Active         =
=============================================*/
function toggleFullscreen(elem) {
	elem = elem || document.documentElement;
	if (!document.fullscreenElement && !document.mozFullScreenElement &&
		!document.webkitFullscreenElement && !document.msFullscreenElement) {
		if (elem.requestFullscreen) {
			elem.requestFullscreen();
		} else if (elem.msRequestFullscreen) {
			elem.msRequestFullscreen();
		} else if (elem.mozRequestFullScreen) {
			elem.mozRequestFullScreen();
		} else if (elem.webkitRequestFullscreen) {
			elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
		}
	} else {
		if (document.exitFullscreen) {
			document.exitFullscreen();
		} else if (document.msExitFullscreen) {
			document.msExitFullscreen();
		} else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		} else if (document.webkitExitFullscreen) {
			document.webkitExitFullscreen();
		}
	}
}
// document.getElementById('btnFullscreen').addEventListener('click', function () {
// 	toggleFullscreen();
// });


/*=============================================
	=    		Collection Active		   =
=============================================*/
$('.top-collection-active').slick({
	dots: false,
	infinite: true,
	speed: 1000,
	autoplay: false,
	arrows: true,
	prevArrow: '<button type="button" class="slick-prev"><i class="fi-sr-arrow-left"></i></button>',
	nextArrow: '<button type="button" class="slick-next"><i class="fi-sr-arrow-right"></i></button>',
	appendArrows: ".featured-nav",
	slidesToShow: 6,
	slidesToScroll: 1,
	responsive: [
		{
			breakpoint: 1200,
			settings: {
				slidesToShow: 3,
				slidesToScroll: 1,
				infinite: true,
			}
		},
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1
			}
		},
		{
			breakpoint: 767,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
				arrows: false,
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
			}
		},
	]
});


/*=============================================
	=    		Testimonial Active		      =
=============================================*/
$('.testimonial-active').slick({
	dots: true,
	infinite: true,
	speed: 1000,
	autoplay: false,
	arrows: false,
	slidesToShow: 1,
	slidesToScroll: 1,
	responsive: [
		{
			breakpoint: 1200,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				infinite: true,
			}
		},
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1
			}
		},
		{
			breakpoint: 767,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
			}
		},
	]
});


/*=============================================
	=    		Creator Active		      =
=============================================*/
$('.creator-active').slick({
	dots: true,
	infinite: true,
	speed: 1000,
	autoplay: false,
	arrows: false,
	slidesToShow: 6,
	slidesToScroll: 2,
	responsive: [
		{
			breakpoint: 1500,
			settings: {
				slidesToShow: 5,
				slidesToScroll: 2,
				infinite: true,
			}
		},
		{
			breakpoint: 1200,
			settings: {
				slidesToShow: 4,
				slidesToScroll: 3
			}
		},
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 3,
				slidesToScroll: 2
			}
		},
		{
			breakpoint: 767,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 2,
				arrows: false,
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
			}
		},
	]
});

/*=============================================
	=           sidebar Active         =
=============================================*/
// $('.sidebar-active').slick({
// 	dots: true,
// 	infinite: true,
// 	speed: 1000,
// 	autoplay: false,
// 	arrows: false,
// 	fade: true,
// 	slidesToShow: 1,
// 	slidesToScroll: 1,
// 	responsive: [
// 		{
// 			breakpoint: 1500,
// 			settings: {
// 				slidesToShow: 1,
// 				infinite: true,
// 			}
// 		},
// 	]
// });


/*=============================================
	=    	  Countdown Active  	         =
=============================================*/
$('[data-countdown]').each(function () {
	var $this = $(this), finalDate = $(this).data('countdown');
	$this.countdown(finalDate, function (event) {
		$this.html(event.strftime('<div class="time-count day"><span>%D</span><span>Day</span></div><div class="time-count hour"><span>%H</span><span>Hours</span></div><div class="time-count min"><span>%M</span><span>Minute</span></div><div class="time-count sec"><span>%S</span><span>Second</span></div>'));
	});
});


/*=============================================
	=    		Magnific Popup		      =
=============================================*/
$('.popup-image').magnificPopup({
	type: 'image',
	gallery: {
		enabled: true
	}
});

/* magnificPopup video view */
$('.popup-video').magnificPopup({
	type: 'iframe'
});


/*=============================================
	=    		Isotope	Active  	      =
=============================================*/
$('.features-img-wrap').imagesLoaded(function () {
	// init Isotope
	var $grid = $('.features-img-wrap').isotope({
		itemSelector: '.grid-item',
		percentPosition: true,
		masonry: {
			columnWidth: 1,
		}
	});
	// filter items on button click
	$('.portfolio-menu').on('click', 'button', function () {
		var filterValue = $(this).attr('data-filter');
		$grid.isotope({ filter: filterValue });
	});

});


/*=============================================
	=    		 Wow Active  	         =
=============================================*/
function wowAnimation() {
	var wow = new WOW({
		boxClass: 'wow',
		animateClass: 'animated',
		offset: 0,
		mobile: false,
		live: true
	});
	wow.init();
}


})(jQuery);

$(document).ready(function() {
    let currentRequest = null;
    let currentPage = 1;
    let isLoading = false;
    let lastQuery = '';

    // Fungsi untuk menormalisasi string
    function normalizeString(str) {
        // Extract year if present
        let year = null;
        const yearMatch = str.match(/\((\d{4})\)/);
        if (yearMatch) {
            year = yearMatch[1];
        }
        
        // Remove year from string for normalization
        const strWithoutYear = str.replace(/\s*\(\d{4}\)\s*/, '');
        
        const normalized = strWithoutYear
            .toLowerCase()
            .replace(/-/g, '') // Hapus tanda hubung
            .replace(/\s+/g, '') // Hapus spasi
            .replace(/[:']/g, '') // Hapus tanda petik dan titik dua
            .replace(/&/g, 'and'); // Ganti & dengan and
        
        return {
            text: normalized,
            year: year
        };
    }

    $('#searchInput').on('input', function() {
        let query = $(this).val().trim();
        
        // Reset pencarian jika query berbeda
        if (query !== lastQuery) {
            currentPage = 1;
            $('#searchResultsContent').empty();
        }

        lastQuery = query;

        if (query === '') {
            $('#searchResults').hide();
            return;
        }

        // Minimal 2 karakter untuk memulai pencarian
        if (query.length > 1) {
            if (currentRequest != null) {
                currentRequest.abort();
            }

            $('#searchResults').show();
            $('#searchLoading').show();

            currentRequest = $.ajax({
                url: searchRoute,
                type: 'GET',
                data: { 
                    q: query,
                    page: currentPage
                },
                success: function(data) {
                    $('#searchLoading').hide();
                    
                    if (data.trim() === '') {
                        if (currentPage === 1) {
                            $('#searchResultsContent').html('<div class="search-item p-2 text-center text-light">No results found.</div>');
                            $('#searchResults').show();
                        }
                    } else {
                        if (currentPage === 1) {
                            $('#searchResultsContent').html(data);
                        } else {
                            $('#searchResultsContent').append(data);
                        }
                        $('#searchResultsContent').show();
                        
                        // Periksa hasil yang relevan dengan normalisasi
                        let results = $('#searchResultsContent .search-item');
                        let hasRelevantResults = false;
                        let normalized = normalizeString(query);
                        
                        results.each(function() {
                            let titleWithYear = $(this).find('h6').text();
                            let itemNormalized = normalizeString(titleWithYear);
                            
                            if (normalized.year) {
                                // Jika ada tahun, cocokkan keduanya
                                if (itemNormalized.text.includes(normalized.text) && 
                                    itemNormalized.year === normalized.year) {
                                    hasRelevantResults = true;
                                    return false; // break the loop
                                }
                            } else {
                                // Jika tidak ada tahun, cukup cocokkan judul
                                if (itemNormalized.text.includes(normalized.text)) {
                                    hasRelevantResults = true;
                                    return false; // break the loop
                                }
                            }
                        });

                        if (hasRelevantResults) {
                            currentPage++;
                            checkForMoreData(query);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    if (status !== 'abort') {
                        $('#searchResults').hide();
                        $('#searchLoading').hide();
                    }
                }
            });
        } else {
            $('#searchResults').hide();
        }
    });

    function checkForMoreData(query) {
        if (!isLoading) {
            isLoading = true;
            let normalizedQuery = normalizeString(query);
            
            $.ajax({
                url: searchRoute,
                type: 'GET',
                data: { 
                    q: query,
                    page: currentPage
                },
                success: function(data) {
                    if (data.trim() !== '') {
                        let tempDiv = $('<div>').html(data);
                        let relevantResults = tempDiv.find('.search-item').filter(function() {
                            let title = normalizeString($(this).find('h6').text());
                            return title.includes(normalizedQuery);
                        });

                        if (relevantResults.length > 0) {
                            $('#searchResultsContent').append(relevantResults);
                            currentPage++;
                            isLoading = false;
                            checkForMoreData(query);
                        } else {
                            $('#searchLoading').hide();
                            isLoading = false;
                        }
                    } else {
                        $('#searchLoading').hide();
                        isLoading = false;
                    }
                },
                error: function() {
                    $('#searchLoading').hide();
                    isLoading = false;
                }
            });
        }
    }

    $('#searchInput').on('keyup', function(e) {
        if (e.key === 'Backspace' || e.key === 'Delete') {
            if ($(this).val().trim() === '') {
                $('#searchResults').hide();
                currentPage = 1;
            }
        }
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#searchForm').length) {
            $('#searchResults').hide();
        }
    });
});