/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/

var ajax_action_path = window.location.href.split('#')[0]+'&ajax=1',
	blockAjax = false;

$(document).ready(function(){

	$('.nav-tab-name').on('click', function(e){
		e.preventDefault();
		$(this).addClass('active').siblings().removeClass('active');
		$($(this).attr('href')).addClass('active').siblings().removeClass('active');
	});

	activateSortable();

	$(document).on('change', '.hookSelector', function(){
		var hook_name = $(this).val();
		$('.hook-content#'+hook_name).addClass('active').siblings().removeClass('active');
		$('.callSettings.active').click();
		scrollUpAllCarousels();
	});

	$('.hookSelector').change();

	$(document).on('click', '.callSettings', function(e){
		e.preventDefault();
		scrollUpAllCarousels();
		$el = $(this);
		if ($el.hasClass('active')){
			$('#settings-content').slideUp(function(){
				$(this).html('');
				$('.callSettings').removeClass('active');
			});
			return;
		}
		$('#settings-content').hide().html('');
		$('.callSettings').removeClass('active');
		var settings_type = $(this).data('settings');
		var hook_name = $(this).closest('form').find('.hookSelector').val();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=CallSettingsForm&settings_type='+settings_type+'&hook_name='+hook_name,
			dataType : 'json',
			success: function(r){
				console.dir(r);
				if ('form_html' in r){
					$('#settings-content').html(utf8_decode(r.form_html)).slideDown().tooltip({selector: '.label-tooltip'});
					$el.addClass('active');
				}
			},
			error: function(r){
				$('#settings-content').hide().html('');
				console.warn(r.responseText);
			}
		});
	})

	$(document).on('submit', '.w-settings-form', function(e){
		e.preventDefault();
	}).on('focusin', '.save-on-the-fly', function(e) {
		$(this).data('initial-value', $(this).val());
	}).on('focusout keyup', '.save-on-the-fly', function(e) {
		var $el = $(this),
			$form = $el.closest('form'),
			formData = $form.serialize(),
			$wrapper = $form.closest('.c-wrapper');
		if ((e.type == 'keyup' && e.which != 13) || $el.val() == $el.data('initial-value')) {
			return;
		}
		$('.thrown-errors').remove();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=SaveWrapperSettings',
			data: {
				form_data: formData,
				ids_in_wrapper: getOrderedIds($wrapper),
			},
			dataType : 'json',
			success: function(r) {
				console.dir(r);
				if ('errors' in r) {
					var errorHTML = '<div class="wrapper-settings-error">'+utf8_decode(r.errors)+'</div>';
					$form.before(errorHTML);
				} else if ('saved' in r) {
					$.growl.notice({ title: '', message: savedTxt});
					$el.parent().addClass('just-saved');
					setTimeout(function(){
						$el.parent().removeClass('just-saved');
					}, 1000);
				}
				if (r.id_wrapper_new) {
					updateWrapperId($wrapper, r.id_wrapper_new);
				}
			},
			error: function(r) {
				console.warn(r.responseText);
			}
		});
	});

	$(document).on('click', '.chk-action', function(e){
		e.preventDefault();
		var $checkboxes = $(this).closest('#settings_content').find('input[type="checkbox"]');
		if ($(this).hasClass('checkall')){
			$checkboxes.each(function(){
				$(this).prop('checked', true);
			});
		}
		else if ($(this).hasClass('uncheckall')){
			$checkboxes.each(function(){
				$(this).prop('checked', false);
			});
		}
		else if ($(this).hasClass('invert')){
			$checkboxes.each(function(){
				$(this).prop('checked', !$(this).prop('checked'));
			});
		}
	});

	$(document).on('change', 'select.exc', function() {
		var value = $(this).val(),
			hasIds = (value != 0) && ($(this).hasClass('customer') || value.lastIndexOf('_all') == -1);
		$(this).closest('.exceptions-block').toggleClass('has-ids', hasIds);
	}).on('click', '.device-type', function(e){
		e.preventDefault();
		var device = $(this).data('type');
		$(this).closest('.form-horizontal').attr('class', 'form-horizontal '+device).data('device', device).attr('data-device', device);
		toggleExternalTplFields();
	}).on('click', '.lock-device-settings', function(e){
		e.preventDefault();
		$(this).closest('.device-input-wrapper').toggleClass('locked unlocked');
	});

	$(document).on('click', '.saveHookSettings', function(e){
		e.preventDefault();
		var hook_name = $(this).attr('data-hook');
		var data = $(this).closest('form').serialize();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=SaveHookSettings&'+data,
			dataType : 'json',
			success: function(r) {
				if (r.saved){
					$('#settings-content').slideUp(function(){
						$('.callSettings').removeClass('active');
						$(this).html('');
						$.growl.notice({ title: '', message: savedTxt});
					});
				}
			},
			error: function(r) {
				console.warn(r.responseText);
			}
		});
	});

	$(document).on('click', '.hide-settings', function(){
		$('.callSettings.active').click();
	});

	$(document).on('click', '.chk-action', function(e){
		e.preventDefault();
		var $checkboxes = $(this).closest('#settings-content').find('input[type="checkbox"]');
		if ($(this).hasClass('checkall')){
			$checkboxes.each(function(){
				$(this).prop('checked', true);
			});
		}
		else if ($(this).hasClass('uncheckall')){
			$checkboxes.each(function(){
				$(this).prop('checked', false);
			});
		}
		else if ($(this).hasClass('invert')){
			$checkboxes.each(function(){
				$(this).prop('checked', !$(this).prop('checked'));
			});
		}
	});

	$(document).on('click', '.bulk-select, .bulk-unselect', function(e){
		e.preventDefault();
		var checked = $(this).hasClass('bulk-select');
		$('.c-item:visible .carousel-box').prop('checked', checked);
	});

	$(document).on('click', '[data-bulk-act]', function(e){
		e.preventDefault();
		$('.bulk-actions-error').remove();
		var ids = [];
		$('.carousel-box:checked').each(function(){
			ids.push($(this).val());
		});
		var act = $(this).data('bulk-act');
		if (act == 'delete' && ids.length && !confirm(areYouSureTxt))
			return;
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=BulkAction',
			dataType : 'json',
			data: {
				act : act,
				ids : ids
			},
			success: function(r)
			{
				if ('errors' in r){
					var err = '<div class="bulk-actions-error" style="display:none;">'+utf8_decode(r.errors)+'</div>';
					$('.bulk-actions').removeClass('open').before(err);
					$('.bulk-actions-error').slideDown();
				}
				else if (r.success){
					$.growl.notice({ title: '', message: r.reponseText});
					blockAjax = true;
					switch (act){
						case 'enable':
							for (var i in ids)
								$('.c-item[data-id="'+ids[i]+'"] .activateCarousel').addClass('action-enabled').removeClass('action-disabled').find('input').prop('checked', true);
						break;
						case 'disable':
							for (var i in ids)
								$('.c-item[data-id="'+ids[i]+'"] .activateCarousel').removeClass('action-enabled').addClass('action-disabled').find('input').prop('checked', false);
						break;
						case 'group_in_tabs':
						case 'ungroup':
							var checked = act == 'group_in_tabs';
							for (var i in ids)
								$('.c-item[data-id="'+ids[i]+'"] [name="in_tabs"]').prop('checked', checked);
						break;
						case 'delete':
							removeCarouselRows(ids);
						break;
					}
					blockAjax = false;
				}

			},
			error: function(r)
			{
				console.warn(r.responseText);
			}
		});
	});

	$(document).on('click', '.addCarousel', function(e){
		e.preventDefault();
		scrollUpAllCarousels();
		// make sure there is only one carousel with id=0
		$('.c-item[data-id="0"]').remove();
		var $cWrapper = $(this).closest('.c-wrapper'),
			$cList = $cWrapper.find('.carousel-list'),
			hook_name = $(this).closest('.hook-content').attr('id'),
			id_wrapper = $cWrapper.attr('data-id');
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=CallCarouselForm&id_carousel=0&hook_name='+hook_name+'&id_wrapper='+id_wrapper,
			dataType : 'json',
			success: function(r) {
				$newItem = $(utf8_decode(r));
				$newItem.prependTo($cList).addClass('open').find('.carousel-details').slideDown().find('#carousel_type').change();
				prepareVisibleTextareas($newItem.find('.carousel-details'));
				$newItem.tooltip({selector: '.label-tooltip'});
				var carousels_num = $('#'+hook_name).find('.c-item:visible').length;
				$('.hookSelector').find('option[value="'+hook_name+'"]').text(hook_name+' ('+carousels_num+')');
			},
			error: function(r) {
				console.warn(r.responseText);
			}
		});
	});

	$(document).on('click', '.addWrapper', function(e){
		// at least one wrapper is always availabe in .hook-content
		$wOrig = $('.hook-content:visible').find('.c-wrapper').first();
		$wOrig.clone().insertBefore($wOrig).addClass('empty').attr('data-id', 0)
		.find('.carousel-list').removeClass('ui-sortable')
		.find('.c-item').remove();
		activateSortable();
	}).on('click', '.deleteWrapper', function(e){
		// button is available only in empty wrappers
		$wrapper = $(this).closest('.c-wrapper');
		if (!$wrapper.siblings().length || $wrapper.find('.c-wrapper').length) {
			alert('This wrapper can not be removed');
		} else {
			$wrapper.remove();
		}

	});

	$(document).on('click', '.activateCarousel', function(){
		if ($(this).closest('.c-item').attr('data-id') == 0)
			$(this).toggleClass('action-enabled action-disabled');
	});

	$(document).on('change', '.toggleable_param', function(e){
		var $parent = $(this).closest('.c-item');
		var id_carousel = $parent.attr('data-id');
		if (blockAjax || id_carousel == 0)
			return;
		var param_name = $(this).attr('name');
		var param_value = $(this).prop('checked') ? 1 : 0;

		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=ToggleParam&param_name='+param_name+'&param_value='+param_value+'&id_carousel='+id_carousel,
			dataType : 'json',
			success: function(r){
				console.dir(r);
				if(r.success){
					$.growl.notice({ title: '', message: savedTxt});
					if (param_name == 'active')
						$parent.find('.activateCarousel').toggleClass('action-enabled action-disabled');
				}
				else
					$.growl.error({ title: '', message: failedTxt});

			},
			error: function(r){
				console.warn(r.responseText);
			}
		});
	});

	$(document).on('click', '.editCarousel', function(e){
		e.preventDefault();
		scrollUpAllCarousels();
		var $item = $(this).closest('.c-item'),
			id = $item.attr('data-id'),
			id_wrapper = $item.closest('.c-wrapper').attr('data-id');
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=CallCarouselForm&id_carousel='+id+'&id_wrapper='+id_wrapper,
			dataType : 'json',
			success: function(r)
			{
				// console.dir(r);
				var newItemHTML = $(utf8_decode(r)).html();
				$item.html(newItemHTML).addClass('open').find('.carousel-details').slideDown().
				find('#carousel_type, select[name="settings[carousel][type]"]').change();
				prepareVisibleTextareas($item.find('.carousel-details'));
				$item.tooltip({selector: '.label-tooltip'});
			},
			error: function(r)
			{
				console.warn(r.responseText);
			}
		});
	});

	$(document).on('change', 'select[name="settings[carousel][type]"]', function(){
		$(this).closest('.carousel-settings').toggleClass('no-scripts', $(this).val() != 1);
	});

	$(document).on('click', '.scrollUp', function(e){
		e.preventDefault();
		scrollUpAllCarousels();
	});

	$(document).on('click', '.deleteCarousel', function(){
		if (!confirm(areYouSureTxt))
			return;
		var $item = $(this).closest('.c-item');
		id = $item.attr('data-id');
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=DeleteCarousel&id_carousel='+id,
			dataType : 'json',
			success: function(r)
			{
				if (r.deleted)
					removeCarouselRows(id);
			},
			error: function(r)
			{
				console.warn(r.responseText);
			}
		});
	});

	function getItemType(carouselType){
		var otherTypes = {manufacturers:'m', suppliers:'s', categories:'c', subcategories:'c'};
		return (carouselType in otherTypes) ? otherTypes[carouselType] : 'p';
	}

	$(document).on('change', '#carousel_type', function(){

		var carouselType = $(this).val(),
			itemType = getItemType(carouselType),
			isProductType = itemType == 'p';

		$('.select_image_type').each(function(){
			var $options = $(this).find('.img-type-option'),
				$savedImageOption = $options.filter('.saved'),
				imgType = $savedImageOption.val();
			$options.each(function(){
				var available = !$(this).hasClass('not-for-'+itemType);
				$(this).toggleClass('hidden', !available);
			});
			if ($savedImageOption.hasClass('hidden')) {
				imgType = $savedImageOption.siblings().not('.hidden').eq(2).val();
			}
			$(this).val(imgType).change();
		});

		$('.current-p-option').removeClass('current-p-option').removeClass('hidden');
		$('.p-option').toggleClass('hidden', !isProductType);
		$('.special-settings, .special_option').addClass('hidden');
		$('.special_option.'+carouselType).removeClass('hidden').closest('.special-settings').removeClass('hidden');
		$('.not-for-some'+(!isProductType ? ':not(.p-option)' : '')).removeClass('hidden').filter('.not-for-'+carouselType).addClass('hidden');

		if (isProductType) {
			$('.tpl-settings').find('.form-group').not('.hidden').addClass('current-p-option');
			toggleExternalTplFields();
		} else {
			hideEmptyColumns();
		}

		// update name field if it is not saved yet
		if ($('.name-not-saved').length) {
			$('.name-not-saved').val($.trim($(this).find('option:selected').text()));
		}

	}).on('change', '.select_external_tpl', function() {
		toggleExternalTplFields();
	});

	function hideEmptyColumns() {
		$('.f-col').each(function(){
			$(this).removeClass('hidden').toggleClass('hidden', !$(this).find('.form-group:visible').length);
		});
	}

	function toggleExternalTplFields() {
		var deviceType = $('.c-item.open').find('.form-horizontal').data('device'),
			desktopCustom = $('.select_external_tpl').first().val() == 1,
			custom = desktopCustom;
		if (deviceType != 'desktop') {
			var $externalTplWrapper = $('.form-group.external-tpl').find('.device-input-wrapper.'+deviceType),
				$externalPathWrapper = $('.form-group.external-tpl-path').find('.device-input-wrapper.'+deviceType);
			if (!$externalTplWrapper.hasClass('locked')) {
				custom = $externalTplWrapper.find('.select_external_tpl').val() == 1;
				if (custom && !desktopCustom && $externalPathWrapper.hasClass('locked')) {
					$externalPathWrapper.find('.lock-device-settings').click();  // make sure custom path is editable
				}
			}
		}
		$('.current-p-option').not('.external-tpl, .custom-class').toggleClass('hidden', custom)
		.filter('.external-tpl-path').toggleClass('hidden', !custom);
		hideEmptyColumns();
	}

	$(document).on('click', '#saveCarousel', function(e){
		e.preventDefault();
		var $item = $(this).closest('.c-item'),
			id = $item.attr('data-id'),
			$wrapper = $item.closest('.c-wrapper'),
			$hookContent = $item.closest('.hook-content');
		$item.find('textarea.mce-activated').each(function(){
			var html_content = tinyMCE.get($(this).attr('id')).getContent();
			$(this).val(html_content);
		});

		// don't submit locked values
		$item.find('.device-input-wrapper.locked').find('[name]').each(function(){
			var name = $(this).attr('name');
			$(this).data('real-name', name).attr('name', 'nosubmit');
		})

		$item.find('.ajax_errors').hide().html('');
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=saveCarousel',
			data: {
				id_carousel: id,
				carousel_data: $item.find('form').serialize(),
				hook_name: $hookContent.attr('id'),
				ids_in_hook: getOrderedIds($hookContent),
				ids_in_wrapper: getOrderedIds($wrapper),
			},
			dataType : 'json',
			success: function(r) {
				$item.find('[name="nosubmit"]').each(function(){
					$(this).attr('name', $(this).data('real-name'));
				});
				if ('errors' in r) {
					$item.find('.ajax_errors').show().append(utf8_decode(r.errors));
					$('html, body').animate({
						scrollTop: $item.offset().top - 130
					}, 500);
					return;
				} else {
					$.growl.notice({ title: '', message: r.responseText});
					$item.find('form').slideUp(function(){
						$item.replaceWith(utf8_decode(r.updated_form_header));
					});
					markEmptyWrappers();
					if (r.id_wrapper_new) {
						updateWrapperId($wrapper, r.id_wrapper_new);
					}
				}
			},
			error: function(r) {
				$item.find('[name="nosubmit"]').each(function(){
					$(this).attr('name', $(this).data('real-name'));
				});
				$item.find('.ajax_errors').show().append('<div class="alert alert-danger">'+r.responseText+'</div>');
			}
		});
	});

	$(document).on('click', '.lang_switcher', function(){
		var id_lang = $(this).attr('data-id-lang');
		$('.multilang').addClass('hidden');
		$('.multilang.lang_'+id_lang).removeClass('hidden');
		prepareVisibleTextareas($(this).closest('.carousel-details'));
	}).on('focus', 'textarea.mce', function(){
		if (!$(this).hasClass('mce-activated')) {
			activateMCE($(this));
		}
	});

	function prepareVisibleTextareas($container) {
		$container.find('textarea.mce:visible').not('.mce-activated').each(function(){
			$el = $(this);
			if ($el.val()) {
				// add timeout for smooth sliding
				setTimeout(function(){
					activateMCE($el);
				}, 500);
			}
		});
	}

	function activateMCE($el) {
		// if there is # in URL, page scrolls to top after activating MCE
		if (window.location.href.indexOf('#') >= 0) {
			window.history.pushState(null, null, window.location.href.split('#')[0]);
		}
		tinySetup({
			selector: '#'+$el.attr('id'),
			setup: function(editor) {
				editor.on('LoadContent', function(e) {
					$el.addClass('mce-activated');
					editor.focus();
				});
			},
			content_css: mce_content_css,
		});
	}

	$(document).on('click', '.importer .import', function(){
		$('input[name="carousels_data_file"]').click();
	}).on('change', 'input[name="carousels_data_file"]', function(){
		if (!this.files || this.files[0].type != 'text/plain')
			return;
		$('.importer .import i').toggleClass('icon-download icon-refresh icon-spin');
		var data = new FormData();
		data.append($(this).attr('name'), $(this).prop('files')[0]);
		$('.thrown-errors').remove();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=ImportCarousels',
			dataType : 'json',
			processData: false,
			contentType: false,
			data: data,
			success: function(r)
			{
				if ('errors' in r) {
					var errorsHTML = '<div class="thrown-errors">'+utf8_decode(r.errors)+'</div>';
					$('.importer').closest('.panel').before(errorsHTML);
				} else if ('upd_html' in r){
					$upd = $('<div>'+utf8_decode(r.upd_html)+'</div>');
					$('.all-carousels').replaceWith($upd.find('.all-carousels'));
					$('.all-carousels').find('.hookSelector').change();
					$('.all-carousels').before($upd.find('.module_confirmation'));
					$('.panel.customcode').replaceWith($upd.find('.panel.customcode'));
					$('.nav-tab-name[href="#carousels"]').click();
					customCode.activate();
					activateSortable();
				}
				$('.importer .import i').toggleClass('icon-download icon-refresh icon-spin');
			},
			error: function(r)
			{
				console.warn(r.responseText);
				$('.importer .import i').toggleClass('icon-download icon-refresh icon-spin');
			}
		});
	});

	$('.install-override, .uninstall-override').on('click', function(){
		var $parent = $(this).closest('.override-item'),
			override_action = $(this).hasClass('install-override') ? 'addOverride' : 'removeOverride',
			override = $(this).data('override');
		$parent.find('.thrown-errors').remove();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=ProcessOverride&override_action='+override_action+'&override='+override,
			dataType : 'json',
			success: function(r) {
				if ('errors' in r) {
					$parent.prepend(utf8_decode(r.errors));
				} else if (r.processed) {
					$.growl.notice({ title: '', message: savedTxt});
					$parent.toggleClass('installed not-installed');
				} else {
					$.growl.error({ title: '', message: errorTxt});
				}
				console.dir(r);
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	});

	$(document).on('click', '.close-parent', function(e){
		e.preventDefault();
		$(this).closest('.parent').remove();
	});

	// ajax progress
	$('body').append('<div id="re-progress"><div class="progress-inner"></div></div>');
	$(document).ajaxStart(function(){
		$('#re-progress .progress-inner').width(0).fadeIn('fast').animate({'width':'70%'},500);
	})
	.ajaxSuccess(function(){
		$('#re-progress .progress-inner').animate({'width':'100%'},500,function(){
			$(this).fadeOut('fast');
		});
	});

	customCode.activate();
});

/* custom codes */
var customCode = {
	editors: {},
	activate: function() {
		$('.custom-code-content').each(function(){
			var type = $(this).data('type');
			customCode.editors[type] = ace.edit('code'+type);
			customCode.editors[type].session.setMode('ace/mode/'+(type == 'js' ? 'javascript' : type));
			customCode.editors[type].setOptions({
				showPrintMargin: false,
				// useWorker: false,
			});
			customCode.editors[type].commands.addCommand({
				name: 'saveCode',
				bindKey: {win: 'Ctrl-s', mac: 'Command-s'},
				exec: function(editor) {
					$('.processCustomCode[data-type="'+type+'"][data-action="Save"]').click();
				}
			});
		});
		customCode.bindActions();
	},
	bindActions: function() {
		$('.updateEditorTheme').on('change', function() {
			var theme = $(this).val();
			$.each(customCode.editors, function(i, e){
				e.setTheme('ace/theme/'+theme);
			});
		}).change();
		$('.processCustomCode').on('click', function(e){
			e.preventDefault();
			var action = $(this).data('action'),
				type = $(this).data('type'),
				editor = (type in customCode.editors) ? customCode.editors[type] : false;
			if (!editor) {
				return;
			}
			if (action == 'GetInitial') {
				editor.setValue($('.custom-code-backup.'+type).text());
				customCode.toggleResetNotification(type, true);
				return;
			}
			$.ajax({
				type: 'POST',
				url: ajax_action_path+'&action='+action+'CustomCode',
				dataType : 'json',
				data: {type: type, code: editor.getValue()},
				success: function(r){
					if('successText' in r) {
						$.growl.notice({ title: '', message: r.successText});
						customCode.toggleResetNotification(type, false);
					} else if ('original_code' in r) {
						editor.setValue(utf8_decode(r.original_code));
						customCode.toggleResetNotification(type, true);
					}
				},
				error: function(r) {
					$.growl.error({ title: '', message: 'Error'});
					console.warn(r.responseText);
				}
			});
		});
		$('.toggleResetOptions').on('click', function(e) {
			e.stopPropagation();
			$(this).next().click();
		});
		$('.saveCode').on('click', function() {
			$(this).closest('.custom-code').find('.processCustomCode[data-action="Save"]').click();
		});
		$('.undoCodeAction').on('click', function() {
			var type = $(this).closest('.custom-code').find('.custom-code-content').data('type');
			if (type in customCode.editors) {
				customCode.editors[type].undo();
				customCode.toggleResetNotification(type, false);
			}
		});
	},
	toggleResetNotification: function(type, visible) {
		$('.grey-note.for-'+type).toggleClass('hidden', !visible);
	}
}

function scrollUpAllCarousels(){
	$('.c-item').each(function(){
		var $el = $(this);
		$el.removeClass('open').find('.carousel-details').slideUp(function(){
			$(this).html('');
		});
	});
}

function removeCarouselRows(ids){
	if (!$.isArray(ids)) {
		ids = [ids];
	}
	var lastId = ids[ids.length - 1];
	for (var i in ids){
		$('.c-item[data-id="'+ids[i]+'"]').fadeOut(function(){
			var updateHookCount = $(this).attr('data-id') == lastId;
			$(this).remove();
			if (updateHookCount) {
				var hook_name = $('.hookSelector').val(),
					carousels_num = $('#'+hook_name).find('.c-item:visible').length;
				$('.hookSelector').find('option[value="'+hook_name+'"]').text(hook_name+' ('+carousels_num+')');
				markEmptyWrappers();
			}
		});
	}
}

function markEmptyWrappers() {
	$('.c-wrapper').each(function() {
		$(this).toggleClass('empty', !$(this).find('.c-item').length);
	});
}

function updateWrapperId($wrapper, new_id) {
	$wrapper.attr('data-id', new_id);
}

function activateSortable(){
	$('.carousel-list, .wrappers-container').each(function(){
		if ($(this).hasClass('ui-sortable')) {
			return;
		}
		var isCarouselList = $(this).hasClass('carousel-list') ? 1 : 0,
			params = {
				placeholder: 'new-position-placeholder',
				connectWith: isCarouselList ? '.carousel-list' : '',
				handle: '.dragger',
				start: function(e, ui) {
					var $item = ui.item,
						css = {
							'height': $item.innerHeight(),
							// 'width': $item.innerWidth(),
							// 'display': 'inline-block',
						};
					$('.new-position-placeholder').css(css);
				},
				update: function(event, ui) {
					var $item = ui.item,
						$parent = $item.parent();
					// update may be called twice if elements are moved among wrappers
					// the following condition makes sure positions are updated only once
					if (this === $parent[0]) {
						$.ajax({
							type: 'POST',
							url: ajax_action_path+'&action=UpdatePositionsInHook',
							dataType : 'json',
							data: {
								ordered_ids: getOrderedIds($item.closest('.hook-content')),
								moved_element_is_carousel: isCarouselList,
								moved_element_wrapper_id: $parent.closest('.c-wrapper').attr('data-id'),
								moved_element_id: $item.attr('data-id'),
							},
							success: function(r){
								if('successText' in r) {
									$.growl.notice({ title: '', message: r.successText});
								}
								if (isCarouselList && r.id_wrapper_new) {
									updateWrapperId($parent.closest('.c-wrapper'), r.id_wrapper_new);
								}
								markEmptyWrappers();
							},
							error: function(r) {
								$.growl.error({ title: '', message: 'Error'});
								console.warn(r.responseText);
							}
						});
					}
				}
			};
		$(this).sortable(params);
	});
}

function getOrderedIds($container) {
	var ordered_ids = [];
	$container.find('.c-item').each(function(){
		ordered_ids.push($(this).attr('data-id'));
	});
	return ordered_ids;
}

function utf8_decode(utfstr) {
	var res = '';
	for (var i = 0; i < utfstr.length;) {
		var c = utfstr.charCodeAt(i);
		if (c < 128) {
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
}
/* since 2.6.2 */
