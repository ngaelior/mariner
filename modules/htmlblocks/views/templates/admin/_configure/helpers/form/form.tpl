{* Booking Rent Prestashop module
 * Copyright 2015, Prestaddons
 * Author: Prestaddons
 * Website: http://www.prestaddons.fr
 *}

{extends file="helpers/form/form.tpl"}



{block name="legend"}
	<h3>
		{if isset($field.image)}<img src="{$field.image|escape:'html':'UTF-8'}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
		{if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
		{$field.title|escape:'html':'UTF-8'}
		<span class="panel-heading-action">
		{foreach from=$toolbar_btn item=btn key=k}
			{if $k != 'modules-list' && $k != 'back'}
				<a id="desc-{$table|escape:'html':'UTF-8'}-{if isset($btn.imgclass)}{$btn.imgclass|escape:'html':'UTF-8'}{else}{$k|escape:'html':'UTF-8'}{/if}" class="list-toolbar-btn" {if isset($btn.href)}href="{$btn.href|escape:'html':'UTF-8'}"{/if} {if isset($btn.target) && $btn.target}target="_blank"{/if} {if isset($btn.js) && $btn.js}onclick="{$btn.js|escape:'html':'UTF-8'}" {/if}>
					<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s=$btn.desc mod='htmlblocks'}" data-html="true">
						<i class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass|escape:'html':'UTF-8'}{else}{$k|escape:'html':'UTF-8'}{/if} {if isset($btn.class)}{$btn.class|escape:'html':'UTF-8'}{/if}" ></i>
					</span>
				</a>
			{/if}
		{/foreach}
		</span>
	</h3>
{/block}
{block name="input"}
	
	{if $input.type == 'button'}
		
		<a href="{$input.href|escape:'html':'UTF-8'}" class="btn btn-default" style="float: left; margin-right: 10px;"><i class="process-icon-edit"></i> {$input.name|escape:'html':'UTF-8'}</a>
		<a href="{$input.hrefA|escape:'html':'UTF-8'}" class="btn btn-default" style="float: left;"><i class="process-icon-anchor"></i> {$input.nameA|escape:'html':'UTF-8'}</a>
	
	{elseif $input.type == "button15"}
		
		<a href="{$input.href|escape:'html':'UTF-8'}" class="btn btn-default" style="float: left; margin-right: 10px; margin-bottom: 20px;"><button type="button">{$input.name|escape:'html':'UTF-8'}</button></a>
		<a href="{$input.hrefA|escape:'html':'UTF-8'}" class="btn btn-default" style="float: left;"><button type="button">{$input.nameA|escape:'html':'UTF-8'}</button></a>

	{else}
		
		{$smarty.block.parent}
		
	{/if}
	
{/block}