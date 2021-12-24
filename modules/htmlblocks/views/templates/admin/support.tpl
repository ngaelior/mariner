{* HTML Blocks Prestashop Module
 * Copyright 2015, Prestaddons
 * Author: Prestaddons
 * Website: http://www.prestaddons.fr
 *}
 
<div id="fieldset_0" class="panel">
	
	{if $ps_version16}
	<div class="panel-heading">
		<i class="icon-question-sign">&nbsp;</i>{l s='Support' mod='htmlblocks'}
	</div>
	<div class="form-wrapper">
	{else}
	<div class="toolbar-placeholder">
		<div class="toolbarBox">
			<ul class="cc_button">
				<li>
					<a class="toolbar_btn" href="{$back_link|escape:'htmlall':'UTF-8'}" title="{l s='Back' mod='htmlblocks'}">
						<span class="process-icon-back"></span>
						<div class="locked">{l s='Back' mod='htmlblocks'}</div>
					</a>
				</li>
			</ul>
			<div class="pageTitle">
				<h3>
					<span style="font-weight: normal;" id="current_obj">
						<span class="breadcrumb item-0">{$display_name|escape:'htmlall':'UTF-8'}</span>
					</span>
				</h3>
			</div>
		</div>
	</div>
	<div class="leadin"></div>
	<fieldset>
		<legend><img src="{$path|escape:'htmlall':'UTF-8'}views/img/help_16x16.png" alt="{l s='Support' mod='htmlblocks'}" />{l s='Support' mod='htmlblocks'}</legend>
	{/if}
		<div class="form-group" style="font-size:14px;">
			<div style="width:70%;margin:0 auto;">
				<img src="{$path|escape:'htmlall':'UTF-8'}logo.png" alt="{$display_name|escape:'htmlall':'UTF-8'} {$version|escape:'htmlall':'UTF-8'}" />
				<strong>{$display_name|escape:'htmlall':'UTF-8'} {$version|escape:'htmlall':'UTF-8'}</strong>
				<img style="float:right;" width="200" height="48" src="{$path|escape:'htmlall':'UTF-8'}views/img/prestaddons.png" alt="" />
			</div>
			<br /><br />
			<div style="width:70%;margin:0 auto;border:1px solid #E6E6E6;padding:10px;">
				<span style="font-size: 1.1em;"><img src="{$path|escape:'htmlall':'UTF-8'}views/img/pdf_16x16.png" alt="{l s='Documentation' mod='htmlblocks'}" />&nbsp;{l s='Documentation' mod='htmlblocks'}</span>
				<br />
				<div style="text-align:center;">
					<a href="{$path|escape:'htmlall':'UTF-8'}docs/readme_{$iso|escape:'htmlall':'UTF-8'}.pdf" title="{l s='Download the' mod='htmlblocks'} {$display_name|escape:'htmlall':'UTF-8'} {l s='documentation' mod='htmlblocks'}">
						<img src="{$path|escape:'htmlall':'UTF-8'}views/img/pdf_16x16.png" alt="{l s='Documentation' mod='htmlblocks'}" />
						{l s='Download the' mod='htmlblocks'} {$display_name|escape:'htmlall':'UTF-8'} {l s='documentation' mod='htmlblocks'}
					</a>
				</div>
				<br />
			</div>
			<br />
			<div style="width:70%;margin:0 auto;border:1px solid #E6E6E6;padding:10px;">
				<span style="font-size: 1.1em;"><img src="{$path|escape:'htmlall':'UTF-8'}views/img/copyright_16x16.png" alt="{l s='Copyright' mod='htmlblocks'}" />&nbsp;{l s='Copyright' mod='htmlblocks'}</span>
				<br /><br />
				<div style="text-align:justify;">
					{l s='Copyright_Text' mod='htmlblocks'}
				</div>
				<br />
			</div>
			<br />
		</div>
	{if !$ps_version16}
	</fieldset>
	{else}
	</div>
	<div class="panel-footer">
		<a class="btn btn-default" href="{$back_link|escape:'htmlall':'UTF-8'}"><i class="process-icon-back"></i>{l s='Back' mod='htmlblocks'}</a>
	</div>
	{/if}
</div>