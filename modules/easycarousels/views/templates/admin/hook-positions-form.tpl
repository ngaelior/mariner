{*
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2019 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel clearfix module_list">
	<form action="" method="post" class="position-settings">
	<h3>
		{l s='Module positions for %s' mod='easycarousels' sprintf=$hook_name}
		<i class="icon-times hide-settings" title="{l s='Hide' mod='easycarousels'}"></i>
	</h3>
	<div class="alert alert-info">
		{l s='Drag modules in the list below to change order' mod='easycarousels'}
	</div>
	<ul class="list-unstyled{if $settings|count > 1} sortable{/if}">
		{foreach $settings as $id_module => $module}
			<li id="mod_{$id_module|intval}" class="module_list_item draggable{if (!$module.enabled)} disabled{/if}">
				<div class="module_col_position dragHandle infoblock">
					<span class="positions">{$module@iteration|intval}</span>
				</div>
				<div class="module_col_icon infoblock">
					{if $module.logo_src}
						<img src="{$module.logo_src|escape:'html':'UTF-8'}" alt="{$module.displayName|stripslashes|escape:'html':'UTF-8'}" />
					{/if}
				</div>
				<div class="module_col_infos infoblock">
					<span class="module_name">{$module.display_name|escape:'html':'UTF-8'}</span>
					<div class="module_description">{$module.description|escape:'html':'UTF-8'}</div>
				</div>
				<div class="module_col_actions">

					<div class="actions-enabled btn-group pull-right">
						<a class="btn btn-default" href="#">{l s='Actions' mod='easycarousels'}</a>
						<a class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="#" data-act="unhook"><i class="icon-minus-sign-alt"></i> {l s='Unhook' mod='easycarousels'}</a></li>
							{if !isset($module.current)}
							<li><a href="#" data-act="disable"><i class="icon-power-off off"></i> {l s='Disable' mod='easycarousels'}</a></li>
							<li><a href="#" data-act="uninstall"><i class="icon-times"></i> {l s='Uninstall' mod='easycarousels'}</a></li>
							{/if}
						</ul>
					</div>
					<div class="actions-disabled">
						<a class="btn btn-default" href="#" data-act="enable"><i class="icon-power-off on"></i> {l s='Enable' mod='easycarousels'}</a>
					</div>

				</div>
			</li>
		{/foreach}
	</ul>
	</form>
</div>
<script type="text/javascript">
	$('.position-settings .sortable').sortable({
		placeholder: 'new-position-placeholder',
		 start: function(e, ui) {
			var $item = ui.item,
				css = {
					'height': $item.innerHeight(),
					// 'width': $item.innerWidth(),
					// 'display': 'inline-block',
				};
			$('.new-position-placeholder').css(css);
			$item.startIndex = $item.index();
        },
		update: function(e, ui) {

			var $item = ui.item,
				id_module = $item.attr('id').replace('mod_', ''),
				new_position = $item.index() + 1,
				way = ($item.startIndex < $item.index())? 1 : 0,
				hook_name = $('.hookSelector').val(),
				params = 'action=SaveHookSettings&settings_type=position';
			params += '&hook_name='+hook_name+'&id_module='+id_module+'&new_position='+new_position+'&way='+way;
			$.ajax({
				type: 'POST',
				url: ajax_action_path+'&'+params,
				dataType : 'json',
				success: function(r) {
					$('.position-settings .dragHandle .positions').each(function(i){
						$(this).html(i + 1);
					});
					if(r.saved) {
						$.growl.notice({ title: '', message: savedTxt});
					} else {
						$.growl.error({ title: '', message: failedTxt});
					}
				},
				error: function(r) {
					console.warn(r.responseText);
				}
			});
		}
	});
	$('.module_col_actions a').on('click', function(e){
		e.preventDefault();
		if (!$(this).data('act')) {
			return;
		}
		var id_module = $(this).closest('.module_list_item').attr('id').replace('mod_', ''),
			act = $(this).data('act'),
			hook_name = $('.hookSelector').val();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=ProcessModule&act='+act+'&hook_name='+hook_name+'&id_module='+id_module,
			dataType : 'json',
			success: function(r)
			{
				if(r.saved){
					$.growl.notice({ title: '', message: savedTxt});
					if (act == 'uninstall' || act == 'unhook'){
						$('#mod_'+id_module).fadeOut('fast', function(){
							$(this).remove();
							$('.position-settings .dragHandle .positions').each(function(i){
								$(this).html(i + 1);
							});
						});
					}
					else
						$('#mod_'+id_module).toggleClass('disabled');
				}
				else
					$.growl.error({ title: '', message: failedTxt});

			},
			error: function(r)
			{
				console.warn(r.responseText);
			}
		});
	});
</script>
