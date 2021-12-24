{* HTML Blocks Prestashop Module
 * Copyright 2016, Prestaddons
 * Author: Prestaddons
 * Website: http://www.prestaddons.fr
 *}

<div class="html_blocks_wrapper clearfix">
{foreach $block_list as $block}
	{if isset($block.link) && $block.link != ''}
		<a href="{$block.link|escape:'htmlall':'UTF-8'}" title="{$block.link_title|escape:'htmlall':'UTF-8'}" {if $block.new_window}target="_blank"{/if}>
	{/if}
			<div id="html_block_{$block.id_block|escape:'htmlall':'UTF-8'}" class="html_block" >
				{if isset($block.title) && $block.title != ''}
					<h3>{$block.title|escape:'htmlall':'UTF-8'}</h3>
				{/if}
				{$block.content|@print_r|rtrim:'1'|escape:'htmlall':'UTF-8'}{* HTML CONTENT *}
			</div>
	{if isset($block.link) && $block.link != ''}
		</a>
	{/if}
{/foreach}
</div>