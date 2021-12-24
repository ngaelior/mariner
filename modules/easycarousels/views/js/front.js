/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/

var ec = {
	resizeTimer: null,
	carousels: {},
	quickViewForced: false,
	prepareVisibleCarousels: function() {
		$('.c_container:visible').not('.rendered').each(function(){
			var $parent = $(this).closest('.easycarousel');
			ec.renderCarousel($parent.attr('id'));
			if ($parent.closest('.column').length){
				$parent.toggleClass('block', $parent.hasClass('carousel_block'));
			}
		});
		if ($('.accordion').find('.carousel_block').length) {
			try {accordion('disable'); accordion('enable');}catch(e){};
		}
		if (!is_17 && $('.easycarousels .quick-view').length && !ec.quickViewForced) {
			try {quick_view(); ec.quickViewForced = true;}catch(e){};
		}
	},
	loadDynamicCarousels: function() {
		$('.easycarousels.dynamic').each(function(){ // generated in easycarousels.php->displayNativeHook
			var $el = $(this);
			if ($el.data('ajaxpath')) {
				$.ajax({
					type: 'POST',
					url: $el.data('ajaxpath'),
					dataType : 'json',
					success: function(r) {
						$el.replaceWith(ec.utf8_decode(r.carousels_html));
					},
					error: function(r) {
						console.warn($(r.responseText).text() || r.responseText);
					}
				});
			}
		});
	},
	activateTabs: function() {
		$('.ec-tabs').not('.activated').each(function() {
			$(this).addClass('activated').find('.ec-tab-link').on('click', function(e){
				e.preventDefault();
				var $parent = $(this).parent(), txt = $(this).text(), id = $(this).attr('href').replace('#', '');
				if ($parent.hasClass('active') || !id) {
					return;
				}
				$parent.addClass('active').siblings().removeClass('active');
				$parent.closest('ul').addClass('closed').find('.responsive_tabs_selection').find('span').html(txt);
				$('#'+id).addClass('active').siblings('.ec-tab-pane').removeClass('active');
				ec.renderCarousel(id);
			});
			$(this).find('.responsive_tabs_selection').on('click', function(){
				var $parent = $(this).parent();
				$parent.toggleClass('closed');
				if (!$parent.hasClass('closed')) {
					ec.onClickOutSide($(this), function(){$parent.addClass('closed')});
					$('.ec-tabs').not($parent).addClass('closed');
				}
			});
		});
	},
	onClickOutSide: function($el, action) {
		$(document).off('click.outside').on('click.outside', function(e) {
			if (!$el.is(e.target) && $el.has(e.target).length === 0) {
				action();
				$(document).off('click.outside');
			}
		});
	},
	renderCarousel: function(id){
		var $container = $('#'+id).find('.c_container'),
			settings = $container.data('settings');

		if ($container.hasClass('rendered')) {
			var arrowsShown = $container.closest('.bx-wrapper').find('.bx-prev:visible').length ? true : false;
			$container.closest('.in_tabs').toggleClass('arrows-shown', arrowsShown);
			return;
		} else if (!$container.hasClass('carousel')) {
			ec.carousels[id] = $container;
			$container.closest('.in_tabs').removeClass('arrows-shown');
			ec.compactTabs($container.closest('.in_tabs'));
			if (settings.normalize_h == 1) {
				ec.normalizeHeights($container);
			}
		}

		$container.addClass('rendered');

		if ($container.hasClass('simple-grid')) {
			return;
		}

		var w = $(window).width(),
			itemsNum = ec.getItemsNum(w, settings),
			wrapperWidth = $container.closest('.c-wrapper').innerWidth(),
			slideWidth = parseInt(wrapperWidth / itemsNum);

		if (slideWidth < settings.min_width) {
			itemsNum = parseInt(wrapperWidth / settings.min_width);
			slideWidth = parseInt(wrapperWidth / itemsNum);
		}

		if ($container.hasClass('scroll-x')) {
			$container.find('.c_col').css('width', slideWidth+'px');
		} else {
			var params = ec.prepareCarouselParams($container, settings, itemsNum, slideWidth);
			if ($container.data('initial-classes')) {
				ec.carousels[id].reloadSlider(params);
			} else {
				if (settings.n == 1 && settings.n_hover == 1 && !isMobile) {
					$container.addClass('n-hover');
				}
				ec.carousels[id] = $container.data('initial-classes', $container.attr('class')).bxSlider(params);
			}
		}
	},
	getItemsNum: function(w, settings) {
		var itemsNum = 1;
		if (w > 1199) {
			itemsNum = settings.i;
		} else if (w > 991) {
			itemsNum = settings.i_1200;
		} else if (w > 767) {
			itemsNum = settings.i_992;
		} else if (w > 479) {
			itemsNum = settings.i_768;
		} else if (w < 480) {
			itemsNum = settings.i_480;
		}
		return itemsNum;
	},
	prepareCarouselParams: function($container, settings, itemsNum, slideWidth) {
		var loop = $container.find('.c_col').length <= itemsNum ? 0 : settings.l;
		return {
			pager : settings.p == 1 ? true : false,
			controls: settings.n == 1 ? true : false,
			auto: settings.a == 1 ? true : false,
			pause: parseInt(settings.ps),
			autoHover: settings.ah == 1 ? true : false,
			moveSlides: parseInt(settings.m),
			speed: parseInt(settings.s),
			maxSlides: itemsNum,
			minSlides: itemsNum,
			slideWidth: slideWidth,
			responsive: false,
			swipeThreshold: 50,
			useCSS: true,
			oneToOneTouch: false,
			infiniteLoop: loop == 1 ? true : false,
			onSliderLoad: function(){
				$container.attr('class', $container.data('initial-classes')+' items-num-'+itemsNum).closest('.bx-wrapper')
				.css({'max-width': '100%'}).find('.bx-viewport').css({'height': ''});
				if (settings.normalize_h == 1) {
					ec.normalizeHeights($container);
				}
				var arrowsShown = settings.n && $container.find('.c_col').length > itemsNum;
				$container.closest('.in_tabs').toggleClass('arrows-shown', arrowsShown);
				ec.compactTabs($container.closest('.in_tabs'));
			},
			onSlideAfter: function ($slideElement) {
				$slideElement.addClass('current').siblings('.current').removeClass('current');
			}
		};
	},
	reRenderOnWindowResize: function() {
		$(window).on('resize', function(){
			clearTimeout(ec.resizeTimer);
			ec.resizeTimer = setTimeout(function() {
				$('.c_container.rendered').removeClass('rendered');
				$('.in_tabs.compact').removeClass('compact');
				for (var id in ec.carousels) {
					if (ec.carousels[id].is(':visible')) {
						ec.renderCarousel(id);
					}
				}
			}, 200);
		});
		if (!is_17) {
			// if carousels are minimized in accordion after resize,
			// they should be regenerated after clicking on block title
			$('.column').on('click', 'h3.carousel_title.active', function(){
				ec.renderCarousel($(this).parent().attr('id'));
			});
		}
	},
	compactTabs: function($tabsContainer) {
		if ($tabsContainer.hasClass('compact_on') && !$tabsContainer.hasClass('compact')) {
			var $tabList = $tabsContainer.find('ul.ec-tabs'),
				$lastLi = $tabList.find('li.carousel_title').last(),
				$firstLi = $tabList.find('li.carousel_title').first();
			if ($lastLi.prev().hasClass('carousel_title') && $lastLi.offset().top != $firstLi.offset().top) {
				$tabList.closest('.in_tabs').addClass('compact');
			}
		}
	},
	normalizeHeights: function($container) {
		if ($container.hasClass('scroll-x') && typeof window.ec_loaded == 'undefined') {
			// before window is loaded, heights of multiline containers are not calculated properly
			$(window).on('load', function(){
				window.ec_loaded = 1;
				ec.normalizeHeights($container);
			});
			return;
		}
		var hMax = {'title': 0, 'reference': 0, 'category': 0, 'manufacturer': 0, 'description-short': 0, 'availability': 0};
		$.each(hMax, function(el, max){
			$container.find('.product-'+el).each(function(){
				var h = $(this).outerHeight();
				max = h > max ? h : max;
			});
			$container.find('.product-'+el).css('min-height', max+'px');
		});
	},
	utf8_decode: function(utfstr) {
		var res = '';
		for (var i = 0; i < utfstr.length;) {
			var c = utfstr.charCodeAt(i);
			if (c < 128){
				res += String.fromCharCode(c);
				i++;
			} else if((c > 191) && (c < 224)) {
				var c1 = utfstr.charCodeAt(i+1);
				res += String.fromCharCode(((c & 31) << 6) | (c1 & 63));
				i += 2;
			} else {
				var c1 = utfstr.charCodeAt(i+1);
				var c2 = utfstr.charCodeAt(i+2);
				res += String.fromCharCode(((c & 15) << 12) | ((c1 & 63) << 6) | (c2 & 63));
				i += 3;
			}
		}
		return res;
	},
};

$(document).ready(function(){
	ec.activateTabs();
	ec.prepareVisibleCarousels();
	ec.loadDynamicCarousels();
	ec.reRenderOnWindowResize();
	if (is_17 && $('body').attr('id') == 'cart') {
		prestashop.blockcart.showModal = function(){};
	} else if (!is_17 && (page_name == 'order' || page_name == 'order-opc')) {
		$(document).off('click', '.ajax_add_to_cart_button');
	}
});
/* since 2.6.2 */
