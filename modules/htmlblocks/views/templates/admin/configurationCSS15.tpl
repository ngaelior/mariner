{* HTML Blocks Prestashop Module
 * Copyright 2015, Prestaddons
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
	function eventFire(el, etype){
		if (el.fireEvent) {
		  el.fireEvent('on' + etype);
		} else {
		  var evObj = document.createEvent('Events');
		  evObj.initEvent(etype, true, false);
		  el.dispatchEvent(evObj);
		}
	}
	
	{/literal}
	{foreach from=$blocks item=block key=i}
		{literal}
		editAreaLoader.init({
			id: "{/literal}{$block.id_block|escape:'htmlall':'UTF-8'}{literal}"	// id of the textarea to transform	
			,start_highlight: true	
			,font_size: "8"
			,font_family: "verdana, monospace"
			,allow_resize: "y"
			,allow_toggle: false
			,language: "en"
			,syntax: "css"	
			,toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, reset_highlight, |, help"
			,load_callback: "my_load"
			,save_callback: "my_save"
			,plugins: "charmap"
			,charmap_default: "arrows"
				
		});
		{/literal}
	{/foreach}
	{literal}
		function my_save(id, content){
			alert("Here is the content of the EditArea '"+ id +"' as received by the save callback function:\n"+content);
		}
		
		function my_load(id){
			editAreaLoader.setValue(id, "The content is loaded from the load_callback function into EditArea");
		}
		
		function test_setSelectionRange(id){
			editAreaLoader.setSelectionRange(id, 100, 150);
		}
		
		function test_getSelectionRange(id){
			var sel =editAreaLoader.getSelectionRange(id);
			alert("start: "+sel["start"]+"\nend: "+sel["end"]); 
		}
		
		function test_setSelectedText(id){
			text= "[REPLACED SELECTION]"; 
			editAreaLoader.setSelectedText(id, text);
		}
		
		function test_getSelectedText(id){
			alert(editAreaLoader.getSelectedText(id)); 
		}
		
		function open_file1()
		{
			var new_file= {id: "to\\ é # € to", text: "$authors= array();\n$news= array();", syntax: 'php', title: 'beautiful title'};
			editAreaLoader.openFile('example_2', new_file);
		}
		
		function open_file2()
		{
			var new_file= {id: "Filename", text: "<a href=\"toto\">\n\tbouh\n</a>\n<!-- it's a comment -->", syntax: 'html'};
			editAreaLoader.openFile('example_2', new_file);
		}
		
		function close_file1()
		{
			editAreaLoader.closeFile('example_2', "to\\ é # € to");
		}
		
		function toogle_editable(id)
		{
			editAreaLoader.execCommand(id, 'set_editable', !editAreaLoader.execCommand(id, 'is_editable'));
		}
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
					<div class="contenu_onglet" id="contenu_onglet_{$block.id_block|escape:'htmlall':'UTF-8'}">
						<h1 class="titre">{$block.title|escape:'htmlall':'UTF-8'}</h1>
						<form name="formBlock" id="block" action="#" method="post" role="form">	
							<div class="form-group">
								<label class="control-label col-lg-2" for="{$block.id_block|escape:'htmlall':'UTF-8'}">
									<h3>{l s='Edit your CSS' mod='htmlblocks'}</h3>
								</label>
								<div class="col-lg-10" style="height:500px;">
									<textarea id="{$block.id_block|escape:'htmlall':'UTF-8'}" style="width: 100%; height:90%;" name="content">{$csss[$i]|@print_r:true|escape:'htmlall':'UTF-8'} </textarea>
								</div>

								<div class="alert alert-warning">{l s='Please, only use your own class and id' mod='htmlblocks'}</div>
							</div>
							<div class="row"></div>
							<div class="panel-footer">
								<a href="{$url_back|escape:'htmlall':'UTF-8'}" class="btn btn-default"><button type="button"><i class="process-icon-back"></i> {l s='Back to list' mod='htmlblocks'}</button></a>
								<button type="submit" id="saveConf" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='htmlblocks'}</button>
							</div>
							<input type="hidden" name="id" value="{$block.id_block|escape:'htmlall':'UTF-8'}">
							<input type="hidden" name="submit">
							<input type="hidden" name="submit_conf" value="1">
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