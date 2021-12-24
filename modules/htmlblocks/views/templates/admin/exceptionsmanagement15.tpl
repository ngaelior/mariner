{* HTML Blocks Prestashop Module
 * Copyright 2016, Prestaddons
 * Author: Prestaddons
 * Website: http://www.prestaddons.fr
 *}
 
{if $blocks !== array()}
{literal}
<script type="text/javascript">
	//<!--
	function change_onglet(name)
	{
			document.getElementById('onglet_'+anc_onglet).className = 'onglet_0 onglet';
			document.getElementById('onglet_'+name).className = 'onglet_1 onglet';
			document.getElementById('contenu_onglet_'+anc_onglet).style.display = 'none';
			document.getElementById('contenu_onglet_'+name).style.display = 'block';
			anc_onglet = name;
	}
	//-->
</script>
<style type="text/css">
	.onglet
	{
			display:block;
			margin-left:3px;
			margin-right:3px;
			padding:3px;
			border: 1px solid #ccc;
			cursor:pointer;
	}
	.onglets{
		width:20%;
		float:left;
		font-size: 1.2em;
		text-align: center;
	}
	.onglet_0
	{
			background:#F1F1F1;
			border: 1px solid #ccc;
			border-bottom:1px solid #ccc;
	}
	.onglet_1
	{
			background:#fafafa;
			border-bottom: 1px solid #ccc;
			padding-bottom:4px;
	}
	.contenu_onglets
	{
		float:right;
		width:80%;
	}
	.contenu_onglet
	{
		background-color: #F1F1F1;
		border: 1px solid #ccc;
		margin-top:-1px;
		padding:5px;
		display:none;
	}
	.titre
	{
			margin:0px;
			padding:0px;
	}
	.textarea
	{
		height:500px;
	}
</style>
{/literal}

	{if $alert ne ''}
		<div class="alert alert-success">{$alert|escape:'htmlall':'UTF-8'}</div>
	{/if}

	<div class="systeme_onglets">
        <div class="onglets">
			{foreach from=$blocks item=block key=i}
				<span class="onglet_0 onglet" id="onglet_{$block.id_block|escape:'htmlall':'UTF-8'}" onclick="javascript:change_onglet('{$block.id_block|escape:'htmlall':'UTF-8'}');">{if $block.title ne ''}{$block.title|escape:'htmlall':'UTF-8'}{else}HTML block {$block.id_block|escape:'htmlall':'UTF-8'}{/if}</span>
			{/foreach}
        </div>
        <div class="contenu_onglets">
           
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
					<div class="contenu_onglet" id="contenu_onglet_{$block.id_block|escape:'htmlall':'UTF-8'}">
						<h1 class="titre">{$block.title|escape:'htmlall':'UTF-8'}</h1>
						<h3>{l s='Edit your CSS' mod='htmlblocks'}</h3>
						<form name="formBlock" id="block" action="#" method="post" role="form">	
							
							<div class="form-group">
								<div style="margin-top:10px; height:230px;">
									<div style="float:left;">
										<h4 style="margin-top:5px;">{l s='Selected categories' mod='htmlblocks'}  </h4>
										<select multiple="multiple" name="items[]" id="items{$i|escape:'htmlall':'UTF-8'}" style="height: 160px; width:300px;">
										{$selected_pages[$i]|escape:'htmlall':'UTF-8'}
										<div class="col-xs-6 col-sm-5 col-lg-4" ><a href="#" id="removeItem{$i|escape:'htmlall':'UTF-8'}" class="btn btn-default"><button type="button"><i class="process-icon-back"></i> {l s='Remove' mod='htmlblocks'}</button></a></div>
									</div>
									<div style="float:left;">
										<h4 style="margin-top:5px;">{l s='Available categories' mod='htmlblocks'}</h4>
										<select multiple="multiple" id="availableItems{$i|escape:'htmlall':'UTF-8'}" style="height: 160px; width:300px;">
										{$choices|escape:'htmlall':'UTF-8'}
										<div class="col-xs-6 col-sm-5 col-lg-4"><a href="#" id="addItem{$i|escape:'htmlall':'UTF-8'}" class="btn btn-default"><button type="button"><i class="process-icon-back"></i> {l s='Add' mod='htmlblocks'}</button></a></div>
									</div>
								</div>
								<br/>
								<div class="row"></div>
							</div>
							<div class="row"></div>
							<div class="panel-footer">
								<a href="{$url_back|escape:'htmlall':'UTF-8'}" class="btn btn-default"><button type="button"><i class="process-icon-back"></i> {l s='Back to list' mod='htmlblocks'}</button></a>
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
	{literal}
	<script type="text/javascript">
		  //<!--
		  if({/literal}{$idblock|escape:'htmlall':'UTF-8'}{literal} === 1) {
			  var anc_onglet = '{/literal}{$blocks[0].id_block|escape:'htmlall':'UTF-8'}{literal}';
			  change_onglet(anc_onglet);
		  }
		  else {
			  var anc_onglet = '{/literal}{$idblock|escape:'htmlall':'UTF-8'}{literal}';
			  change_onglet(anc_onglet);
		  }
	  //-->
		  </script>
	{/literal}
{else}
	<div class="bootstrap panel">
		<div class="alert alert-warning">{l s='Please, add a block first' mod='htmlblocks'}</div>
		<a href="{$url_back|escape:'htmlall':'UTF-8'}" class="btn btn-default"><button type="button"><i class="process-icon-back"></i> {l s='Back to list' mod='htmlblocks'}</button></a>
	</div>	  
{/if}


