{* HTML Blocks Prestashop Module
 * Copyright 2016, Prestaddons
 * Author: Prestaddons
 * Website: http://www.prestaddons.fr
 *}

<script type="text/javascript">

	{foreach from=$blocks item=block}
		editAreaLoader.init({
			id: "css_edit_{$block.id_block|escape:'htmlall':'UTF-8'}",	// id of the textarea to transform	
			start_highlight: true,
			font_size: "10",
			font_family: "verdana, monospace",
			allow_resize: "y",
			allow_toggle: false,
			language: "en",
			syntax: "css",	
			toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, reset_highlight, |, help",
			load_callback: "my_load",
			save_callback: "my_save",
			plugins: "charmap",
			charmap_default: "arrows"

		});
	{/foreach}

</script>

{if $blocks !== array()}
	<div class="bootstrap">
		<div id="modulecontent" class="clearfix">
			<div class="tabs-stacked col-lg-12">
				{if $alert ne ''}
					<div class="alert alert-success">{$alert|escape:'htmlall':'UTF-8'}</div>
				{/if}
			</div>
			<!-- Nav tabs -->
			<div class="col-lg-2">
				<div class="list-group">
					<ul class="nav nav-pills nav-stacked">
						{foreach from=$blocks item=block key=i}
							<li class="nav-item" data-id-block="{$block.id_block|escape:'htmlall':'UTF-8'}"><a href='#block_{$block.id_block|escape:'htmlall':'UTF-8'}' class="{if $id_block_active == $block.id_block}active{/if} list-group-item" data-toggle="tab" id="linkblock_{$block.id_block|escape:'htmlall':'UTF-8'}"><i class="icon-cogs"></i> {if $block.title ne ''}{$block.title|escape:'htmlall':'UTF-8'}{else}HTML block {$block.id_block|escape:'htmlall':'UTF-8'}{/if}</a></li>
						{/foreach}
					</ul>
				</div>
			</div>
			<!-- Tab panes -->

			<div class="tab-content col-lg-10">
				{foreach from=$blocks item=block key=i name=block_list}
					<div class="{if $id_block_active == $block.id_block}active{/if} tab-pane panel" id="block_{$block.id_block|escape:'htmlall':'UTF-8'}">
						<form name="formBlock" id="block" action="#" method="POST" role="form">	
							<div class="row form-group">
								<label class="control-label col-lg-12" for="{$block.id_block|escape:'htmlall':'UTF-8'}">
									<h3>{l s='Edit your CSS' mod='htmlblocks'}</h3>
								</label>
								{if $block.title != ''}<h2>{l s='Block:' mod='htmlblocks'} {$block.title|escape:'htmlall':'UTF-8'}</h2>{/if}
							</div>
							<div class="row form-group">
								<div class="alert alert-warning">{l s='Please, only use your own class and id' mod='htmlblocks'}</div>
								<div class="col-lg-12">
									<textarea id="css_edit_{$block.id_block|escape:'htmlall':'UTF-8'}" style="width: 100%; height:500px;" name="content">{$csss[$i]|@print_r:true|escape:'htmlall':'UTF-8'}</textarea>
								</div>
							</div>
							<div class="row"></div>
							<div class="panel-footer">
								<a href="{$url_back|escape:'htmlall':'UTF-8'}" class="btn btn-default"><i class="process-icon-back"></i> {l s='Back to list' mod='htmlblocks'}</a>
								<button type="submit" id="saveConf" class="btn btn-default pull-right" name="submitCSS"><i class="process-icon-save"></i> {l s='Save' mod='htmlblocks'}</button>
							</div>
							<input type="hidden" name="id" value="{$block.id_block|escape:'htmlall':'UTF-8'}" />
							<input type="hidden" name="id_block_active" value="{$block.id_block|escape:'htmlall':'UTF-8'}" />
							<input type="hidden" name="submit">
							<input type="hidden" name="submit_css" value="1">
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
