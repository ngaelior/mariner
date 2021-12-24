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

<div class="panel clearfix">
	<form action="" method="post" class="form-horizontal">
	<h3>
		{l s='Display settings for %s' mod='easycarousels' sprintf=$hook_name}
		<i class="icon-times hide-settings" title="{l s='Hide' mod='easycarousels'}"></i>
	</h3>
	<div class="form-group col-lg-12">
		<label class="control-label col-lg-2" for="custom_class">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Custom class that will be applied to container holding all carousels in this hook' mod='easycarousels'}">{l s='Container class' mod='easycarousels'}</span>
		</label>
		<div class="col-lg-2">
			<input id="custom_class" type="text" name="settings[custom_class]" value="{$settings.custom_class|escape:'html':'UTF-8'}">
		</div>
	</div>
	<div class="form-group col-lg-12">
		<label class="control-label col-lg-2">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='If tab names overlap container, they will be dynamically transformed to a compact dropdown list' mod='easycarousels'}">{l s='Compact tabs' mod='easycarousels'}</span>
		</label>
		<div class="col-lg-2">
			<span class="switch prestashop-switch">
				<input type="radio" id="compact_tabs" name="settings[compact_tabs]" value="1" {if $settings.compact_tabs} checked{/if} >
				<label for="compact_tabs">{l s='Yes' mod='easycarousels'}</label>
				<input type="radio" id="compact_tabs_0" name="settings[compact_tabs]" value="0" {if !$settings.compact_tabs}checked{/if} >
				<label for="compact_tabs_0">{l s='No' mod='easycarousels'}</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>
	<div class="form-group col-lg-12">
		<label class="control-label col-lg-2">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Load carousels dynamically after all other site contents have been loaded' mod='easycarousels'}">{l s='Dynamic load' mod='easycarousels'}</span>
		</label>
		<div class="col-lg-2">
			<span class="switch prestashop-switch">
				{* instant_load is used for retro-compatibility with prev versions *}
				<input type="radio" id="instant_load_0" name="settings[instant_load]" value="0" {if empty($settings.instant_load)} checked{/if} >
				<label for="instant_load_0">{l s='Yes' mod='easycarousels'}</label>
				<input type="radio" id="instant_load" name="settings[instant_load]" value="1" {if !empty($settings.instant_load)}checked{/if} >
				<label for="instant_load">{l s='No' mod='easycarousels'}</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>
	<div class="p-footer clearfix">
		<input type="hidden" name="hook_name" value="{$hook_name|escape:'html':'UTF-8'}">
		<input type="hidden" name="settings_type" value="{$settings_type|escape:'html':'UTF-8'}">
		<button class="saveHookSettings btn btn-default">
			<i class="process-icon-save"></i>
			{l s='Save' mod='easycarousels'}
		</button>
	</div>
	</form>
</div>
