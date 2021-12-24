{**
*
* @author    Amazzing <mail@amazzing.ru>
* @copyright 2007-2019 Amazzing
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
**}

{$c_settings = $carousel.settings.carousel}
{$tpl_settings = $carousel.settings.tpl}

<div id="{$carousel.identifier|escape:'html':'UTF-8'}" class="easycarousel {$tpl_settings.custom_class|escape:'html':'UTF-8'}{if $in_tabs} ec-tab-pane{else} carousel_block{/if}{if !empty($custom_class)} {$custom_class|escape:'html':'UTF-8'}{/if}{if empty($carousel.name) && $c_settings.n} nav_without_name{/if}">
	{if !$in_tabs && !empty($carousel.name)}
		<h3 class="title_block carousel_title">
			{if $tpl_settings.view_all == 2 && !empty($carousel.view_all_link)}<a href="{$carousel.view_all_link|escape:'html':'UTF-8'}">{/if}
			{$carousel.name|escape:'html':'UTF-8'}
			{if $tpl_settings.view_all == 2 && !empty($carousel.view_all_link)}</a>{/if}
		</h3>
	{/if}
	{if !empty($carousel.description)}<div class="carousel-description">{$carousel.description nofilter}{* can not be escaped *}</div>{/if}
	<div class="block_content{if $c_settings.type == 2} scroll-x-wrapper{/if}">
		<div class="c_container{if empty($c_settings.type)} simple-grid xl-{$c_settings.i|intval} l-{$c_settings.i_1200|intval} m-{$c_settings.i_992|intval} s-{$c_settings.i_768|intval} xs-{$c_settings.i_480|intval} clearfix{else if $c_settings.type == 1} carousel{else if $c_settings.type == 2} scroll-x{/if}" data-settings="{$c_settings|json_encode|escape:'html':'UTF-8'}">
			{$total = $carousel.items|count}
			{foreach array_values($carousel.items) as $k => $column_items}
				{* div from previous iteration is closed here in order to remove spaces among items *}
				{if $k}</div>{/if}<div class="c_col">
					{foreach $column_items as $i}
						<div class="c_item">
							{if $carousel.item_type == 'product'}{$product = $i}{else}{$item = $i}{/if}
							{include file=$carousel.item_tpl type=$carousel.type settings=$tpl_settings}
						</div>
					{/foreach}
				{if $k + 1 == $total}</div>{/if}{* only last div is closed here *}
			{/foreach}
		</div>
	</div>
	{if  $tpl_settings.view_all == 1 && !empty($carousel.view_all_link)}
		<div class="text-center">
			<a href="{$carousel.view_all_link|escape:'html':'UTF-8'}" class="view_all">{l s='View all' mod='easycarousels'}</a>
		</div>
	{/if}
</div>
{* since 2.6.0 *}
