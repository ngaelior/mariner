{**
*
* @author    Amazzing <mail@amazzing.ru>
* @copyright 2007-2019 Amazzing
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
* NOTE: this file is extendable. You can override only selected blocks in your template.
* Path for extending: 'modules/easycarousels/views/templates/hook/item.tpl'
*
**}

{block name='item'}
<div class="item-container">
	{block name='item_image'}
	{if $item.img_src}
		<div class="item-image">
			<a href="{$item.url|escape:'html':'UTF-8'}">
				<img class="item-image" src="{$item.img_src|escape:'html':'UTF-8'}" alt="{$item.name|escape:'html':'UTF-8'}">
			</a>
		</div>
	{/if}
	{/block}

	{block name='item_title'}
	{if !empty($settings.title)}
		<div class="item-title{if !empty($settings.title_one_line)} nowrap{/if}">
			<a href="{$item.url|escape:'html':'UTF-8'}">{$item.name|truncate:$settings.title:'...'}</a>
		</div>
	{/if}
	{/block}

	{block name='item_matches'}
	{if isset($item.matches)}
		<div class="item-matches">{l s='%d items' sprintf=[$item.matches] mod='easycarousels'}</div>
	{/if}
	{/block}

	{block name='item_description'}
	{if !empty($settings.description)}
		<div class="item-desc">
			{$item.description|strip_tags:'UTF-8'|truncate:$settings.description:'...'|escape:'html':'UTF-8'}
		</div>
	{/if}
	{/block}

</div>
{/block}
{* since 2.6.0 *}
