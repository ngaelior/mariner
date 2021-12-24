{*
* 2007-2019 Amazzing
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright 2007-2019 Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*}

{if $file_warnings}
<div class="alert-warning parent">
	{l s='[1]NOTE:[/1] Some files, that you customized, have been updated in new module version' mod='easycarousels' tags=['<span class="b">']}.
	{l s='Don\'t forget to update them in your theme too' mod='easycarousels'}
	<a href="{$info_links.documentation|escape:'html':'UTF-8'}#page=3" title="{l s='More info' mod='easycarousels'}" target="_blank" class="icon-question-circle"></a>
	<ul>
	{foreach $file_warnings as $file => $identifier}
		<li>
			{$file|escape:'html':'UTF-8'}
			<span class="warning-advice">
				{l s='When ready, add this code to last line' mod='easycarousels'}:
				<span class="code">{$identifier|escape:'html':'UTF-8'}</span>
			</span>
		</li>
	{/foreach}
	</ul>
	<a href="#" class="close-parent">&times;</a>
</div>
{/if}
{* since 2.6.2 *}
