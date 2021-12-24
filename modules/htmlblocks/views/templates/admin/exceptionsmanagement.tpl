{* HTML Blocks Prestashop Module
 * Copyright 2016, Prestaddons
 * Author: Prestaddons
 * Website: http://www.prestaddons.fr
 *}

{if $blocks !== array()}
	<div class="bootstrap">
		<div id="modulecontent" class="clearfix">
			<div class="tab-content col-xs-12">
				{if $alert ne ''}
					<div class="alert alert-success">{$alert|escape:'htmlall':'UTF-8'}</div>
				{/if}
			</div>
			<!-- Nav tabs -->
			<div class="col-xs-2">
				<div class="list-group">
					<ul class="nav nav-pills nav-stacked">
						{foreach from=$blocks item=block key=i}
							<li {if $tab == $block.id_block}class="active"{/if}><a href='#block{$block.id_block|escape:'htmlall':'UTF-8'}' class="list-group-item" data-toggle="tab" id="linkblock_{$block.id_block|escape:'htmlall':'UTF-8'}"><i class="icon-cogs"></i> {if $block.title ne ''}{$block.title|escape:'htmlall':'UTF-8'}{else}HTML block {$block.id_block|escape:'htmlall':'UTF-8'}{/if}</a></li>
						{/foreach}
					</ul>
				</div>
			</div>
			<!-- Tab panes -->

			<div class="tab-content col-xs-10">
				{foreach from=$blocks item=block key=i}
					{literal}
					<script>
					$(document).ready(function(){
						$("#items{/literal}{$i|escape:'htmlall':'UTF-8'}{literal}").closest('form').on('submit', function(e) {
							$("#items{/literal}{$i|escape:'htmlall':'UTF-8'}{literal} option").prop('selected', true);
						});
						$("#addItem{/literal}{$i|escape:'htmlall':'UTF-8'}{literal}").click(add);
						$("#availableItems{/literal}{$i|escape:'htmlall':'UTF-8'}{literal}").dblclick(add);
						$("#removeItem{/literal}{$i|escape:'htmlall':'UTF-8'}{literal}").click(remove);
						$("#items{/literal}{$i|escape:'htmlall':'UTF-8'}{literal}").dblclick(remove);
						function add()
						{
							$("#availableItems{/literal}{$i|escape:'htmlall':'UTF-8'}{literal} option:selected").each(function(i){
								var val = $(this).val();
								var text = $(this).text();
								text = text.replace(/(^\s*)|(\s*$)/gi,"");
								if (val === "PRODUCT")
								{
									val = prompt('{/literal}{l s="Indicate the ID number for the product" mod='blocktopmenu' js=1}{literal}');
									if (val === null || val === "" || isNaN(val))
										return;
									text = '{/literal}{l s="Product ID #" mod='blocktopmenu' js=1}{literal}'+val;
									val = "PRD"+val;
								}
								$("#items{/literal}{$i|escape:'htmlall':'UTF-8'}{literal}").append('<option value="'+val+'" selected="selected">'+text+'</option>');
							});
							serialize();
							return false;
						}
						function remove()
						{
							$("#items{/literal}{$i|escape:'htmlall':'UTF-8'}{literal} option:selected").each(function(i){
								$(this).remove();
							});
							serialize();
							return false;
						}
						function serialize()
						{
							var options = "";
							$("#items{/literal}{$i|escape:'htmlall':'UTF-8'}{literal} option").each(function(i){
								options += $(this).val()+",";
							});
							$("#itemsInput").val(options.substr(0, options.length - 1));
						}
					});
					</script>
					{/literal}
					<div class="tab-pane {if $tab eq $block.id_block}active{/if} panel" id="block{$block.id_block|escape:'htmlall':'UTF-8'}">
						<form name="formBlock" id="block" action="#" method="POST" role="form">
							<div class="row form-group">
								<label class="control-label col-lg-12" for="{$block.id_block|escape:'htmlall':'UTF-8'}">
									<h3>{l s='Manage exceptions' mod='htmlblocks'}</h3>
								</label>
								{if $block.title != ''}<h2>{l s='Block:' mod='htmlblocks'} {$block.title|escape:'htmlall':'UTF-8'}</h2>{/if}
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-5 col-lg-4">
									<h4 style="margin-top:5px; margin-right: 10px; text-align: right;">{l s='Selected categories' mod='htmlblocks'}  </h4>
									<select multiple="multiple" name="items[]" id="items{$i|escape:'htmlall':'UTF-8'}" style="height: 160px; float:right;">
									{$selected_pages[$i]|@print_r|rtrim:'1'|escape:'htmlall':'UTF-8'}
								</div>
								<div class="col-xs-6 col-sm-5 col-lg-4">
									<h4 style="margin-top:5px; margin-left:10px;">  {l s='Available categories' mod='htmlblocks'}</h4>
									<select multiple="multiple" id="availableItems{$i|escape:'htmlall':'UTF-8'}" style="height: 160px;">
									{$choices|@print_r|rtrim:'1'|escape:'htmlall':'UTF-8'}
								</div>
							</div>
							<br/>
							<div class="row">
								<div class="col-xs-6 col-sm-5 col-lg-4"><a href="#" id="removeItem{$i|escape:'htmlall':'UTF-8'}" class="btn btn-default" style="float:right;"><i class="icon-arrow-right"></i> {l s='Remove' mod='htmlblocks'}</a></div>
								<div class="col-xs-6 col-sm-5 col-lg-4"><a href="#" id="addItem{$i|escape:'htmlall':'UTF-8'}" class="btn btn-default"><i class="icon-arrow-left"></i> {l s='Add' mod='htmlblocks'}</a></div>
							</div>
		
							<div class="panel-footer">
								<a href="{$url_back|escape:'htmlall':'UTF-8'}" class="btn btn-default"><i class="process-icon-back"></i> {l s='Back to list' mod='htmlblocks'}</a>
								<button type="submit" id="saveConf" class="btn btn-default pull-right" name="submitExeptions"><i class="process-icon-save"></i> {l s='Save' mod='htmlblocks'}</button>
							</div>
							<input type="hidden" name="id" value="{$block.id_block|escape:'htmlall':'UTF-8'}">
							<input type="hidden" name="submit">
							<input type="hidden" name="submit_exceptions" value="1">
						</form>
						<div class="row"></div>
					</div>	
				{/foreach}
			</div>
		</div>
	</div>
{else}
	<div class="bootstrap panel">
		<div class="alert alert-warning">{l s='Please, add a block first' mod='htmlblocks'}</div>
		<a href="{$url_back|escape:'htmlall':'UTF-8'}" class="btn btn-default"><i class="process-icon-back"></i> {l s='Back to list' mod='htmlblocks'}</a>
	</div>
{/if}
