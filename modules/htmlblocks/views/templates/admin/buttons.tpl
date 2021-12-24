{* HTML Blocks Prestashop module
 * Copyright 2016, Prestaddons
 * Author: Prestaddons
 * Website: http://www.prestaddons.fr
 *}

{if $ps_version16}
	<div id="support-buttons-admin" class="panel">
		<h3><i class="icon-support"></i> {$module_name|escape:'html':'UTF-8'}</h3>
		<div class="row">
			<a class="btn btn-default" href="{$add_url|escape:'html':'UTF-8'}"><i class="process-icon-plus"></i>{l s='Add a html block' mod='htmlblocks'}</a>
			<a class="btn btn-default" href="{$list_url|escape:'html':'UTF-8'}"><i class="icon-list"></i>{l s='Blocks list' mod='htmlblocks'}</a>
			<a class="btn btn-default" href="{$css_url|escape:'html':'UTF-8'}"><i class="process-icon-edit"></i> {l s='Configure CSS' mod='htmlblocks'}</a>
			<a class="btn btn-default" href="{$exceptions_url|escape:'html':'UTF-8'}"><i class="process-icon-anchor"></i> {l s='Exceptions' mod='htmlblocks'}</a>
			<a class="btn btn-default" href="{$documentation_url|escape:'html':'UTF-8'}"><i class="icon-file-pdf-o"></i>{l s='Documentation' mod='htmlblocks'}</a>
			<a class="btn btn-default" href="{$support_url|escape:'html':'UTF-8'}" target="_blank"><i class="process-icon-help"></i>{l s='Support' mod='htmlblocks'}</a>
		</div>
	</div>
{/if}