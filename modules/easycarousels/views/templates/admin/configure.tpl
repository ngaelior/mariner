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

<div class="easycarousels horizontal-tabs clearfix{if !$is_17} ps-16{/if}">
	<a href="#carousels" class="nav-tab-name first active"><i class="icon-image"></i> {l s='Carousels' mod='easycarousels'}</a>
	<a href="#customcode" class="nav-tab-name"><i class="icon-code"></i> {l s='Custom CSS/JS' mod='easycarousels'}</a>
	<a href="#importer" class="nav-tab-name"><i class="icon-file-zip-o"></i> {l s='Import/export' mod='easycarousels'}</a>
	<a href="#overrides" class="nav-tab-name"><i class="icon-file-text-o"></i> {l s='Overrides' mod='easycarousels'}</a>
	<a href="#info" class="nav-tab-name"><i class="icon-info-circle"></i> {l s='Information' mod='easycarousels'}</a>
</div>
<div class="easycarousels">{$module_obj->renderPossibleWarnings()}{* can not be escaped *}</div>
<div class="easycarousels horizontal-tabs-content">
	<div id="carousels" class="panel active all-carousels clearfix">
	<form class="form-horizontal hook-form clearfix">
		<label class="control-label col-lg-1" for="hookSelector">
			{l s='Select hook' mod='easycarousels'}
		</label>
		<div class="col-lg-3">
			<select class="hookSelector">
				{foreach $hooks item=qty key=hk}
					<option value="{$hk|escape:'html':'UTF-8'}"> {$hk|escape:'html':'UTF-8'} ({$qty|intval}) </option>
				{/foreach}
			</select>
		</div>
		<div class="col-lg-6 hook-settings">
			<a href="#" class="btn btn-default callSettings" data-settings="display">
				<i class="icon-wrench"></i> {l s='Display settings' mod='easycarousels'}
			</a>
			<a href="#" class="btn btn-default callSettings" data-settings="exceptions">
				<i class="icon-ban"></i> {l s='Exceptions' mod='easycarousels'}
			</a>
			<a href="#" class="btn btn-default callSettings" data-settings="positions">
				<i class="icon-arrows-alt"></i> {l s='Module positions' mod='easycarousels'}
			</a>
		</div>
		<button type="button" class="addWrapper btn btn-default pull-right">
			<span class="custom-wrapper-icon"><i class="t-l"></i><i class="t-r"></i><i class="b-r"></i><i class="b-l"></i></span>
			{l s='New Wrapper' mod='easycarousels'}
		</button>
	</form>
	<div id="settings-content" style="display:none;">{* filled dinamically *}</div>
	{foreach $hooks item=qty key=hk}
	<div id="{$hk|escape:'html':'UTF-8'}" class="hook-content {if $hk == 'displayHome'}active{/if}">
		{if $hk|substr:0:19 == 'displayEasyCarousel'}
		<div class="alert alert-info">
			{l s='In order to display this hook, insert the following code to any tpl' mod='easycarousels'}:
			{ldelim}hook h='{$hk|escape:'html':'UTF-8'}'{rdelim}
		</div>
		{/if}
		{if $hk == 'displayFooterProduct'}
		<div class="alert alert-info">
			{l s='Here you can display product accessories or other products with same categories/features' mod='easycarousels'}
		</div>
		{/if}
		<div class="wrappers-container">
			{if !isset($carousels.$hk)}
				{$carousels.$hk = [0 => []]}
			{/if}
			{foreach $carousels.$hk as $id_wrapper => $wrapper_carousels}
				{$wrapper_fields = $ec->getWrapperFields($id_wrapper)}
				<div class="c-wrapper{if !$wrapper_carousels} empty{/if}" data-id="{$id_wrapper|intval}">
					<div class="w-actions">
						<div class="w-settings-form pull-left">
							<form>
								<input type="hidden" name="id_wrapper" value="{$id_wrapper|intval}">
								{foreach $wrapper_fields as $k => $field}
								{$name = $k}
								<div class="inline-block wrapper-form-group">
									<label>
										<span{if !empty($field.tooltip)} class="label-tooltip" data-toggle="tooltip" title="{$field.tooltip|escape:'html':'UTF-8'}"{/if}>
											{$field.display_name|escape:'html':'UTF-8'}
										</span>
									</label>
									<div class="inline-block">
										<input type="text" name="{$name|escape:'html':'UTF-8'}" value="{$field.value|escape:'html':'UTF-8'}" class="save-on-the-fly {if !empty($field.input_class)} {$field.input_class|escape:'html':'UTF-8'}{/if}">
									</div>
								</div>
								{/foreach}
							</form>
						</div>
						<a href="#" class="addCarousel pull-right">
							<i class="icon-plus"></i>
							<span class="btn-txt">{l s='New carousel' mod='easycarousels'}</span>
						</a>
					</div>
					<a href="#" class="deleteWrapper" title="{l s='Delete wrapper' mod='easycarousels'}"><i class="icon-trash"></i></a>
					<a href="#" class="dragger w-dragger">
						<i class="icon icon-arrows-v"></i>
					</a>
					<div class="settings-container" style="display:none;"></div>
					<div class="carousel-list">
						{foreach $wrapper_carousels as $carousel}
							{include file="./carousel-form.tpl"
								carousel=$carousel
								type_names=$type_names
								full=0
							}
						{/foreach}
					</div>
				</div>
			{/foreach}
		</div>
		<div class="btn-group bulk-actions dropup">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				{l s='Bulk actions' mod='easycarousels'} <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li><a href="#"	class="bulk-select"><i class="icon-check-sign"></i> {l s='Select all' mod='easycarousels'}</a></li>
				<li><a href="#" class="bulk-unselect"><i class="icon-check-empty"></i> {l s='Unselect all' mod='easycarousels'}</a></li>
				<li class="divider"></li>
				<li><a href="#" data-bulk-act="enable"><i class="icon-check on"></i> {l s='Enable' mod='easycarousels'}</a></li>
				<li><a href="#" data-bulk-act="disable"><i class="icon-times off"></i> {l s='Disable' mod='easycarousels'}</a></li>
				<li><a href="#" data-bulk-act="group_in_tabs"><i class="icon-plus-square"></i> {l s='Group in tabs' mod='easycarousels'}</a></li>
				<li><a href="#" data-bulk-act="ungroup"><i class="icon-minus-square"></i> {l s='Ungroup' mod='easycarousels'}</a></li>
				<li class="divider"></li>
				<li><a href="#" data-bulk-act="delete"><i class="icon-trash"></i> {l s='Delete' mod='easycarousels'}</a></li>
			</ul>
		</div>
	</div>
	{/foreach}
	</div>

	<div id="customcode" class="panel customcode clearfix">
		<script src="https://pagecdn.io/lib/ace/1.4.5/ace.js" integrity="sha256-5Xkhn3k/1rbXB+Q/DX/2RuAtaB4dRRyQvMs83prFjpM=" crossorigin="anonymous"></script>
		{foreach $custom_code as $type => $code}
			<div class="custom-code {$type|escape:'html':'UTF-8'}">
				{if $type == 'css'}
					<div class="editor-theme">
						<label class="inline-block">{l s='Editor theme' mod='easycarousels'}</label>
						<div class="inline-block">
							<select class="updateEditorTheme">
								<option value="monokai">{l s='Dark' mod='easycarousels'}</option>
								<option value="solarized_light">{l s='Light' mod='easycarousels'}</option>
							</select>
						</div>
					</div>
				{/if}
				<div class="custom-code-title">{l s='Custom [1]%s[/1]' mod='easycarousels' sprintf=$type tags=['<span class="uppercase b">']}</div>
				<div id="code{$type}" class="custom-code-content" data-type="{$type|escape:'html':'UTF-8'}">{$code}{* can not be escaped*}</div>
				<div class="custom-code-backup hidden {$type|escape:'html':'UTF-8'}">{$code}{* can not be escaped*}</div>
				<div class="custom-code-actions clearfix text-right">
					<button type="button" class="btn btn-default pull-left processCustomCode" data-type="{$type|escape:'html':'UTF-8'}" data-action="Save">
					<i class="icon-save"></i> {l s='Save' mod='easycarousels'}</button>
					<span class="grey-note for-{$type|escape:'html':'UTF-8'} hidden">
						{l s='Code was updated in editor. You can [1]Save it[/1] now, or [2]Undo[/2] last action' mod='easycarousels' tags=['<span class="saveCode">', '<span class="undoCodeAction">']}.
					</span>
					<div class="btn-group pull-right">
						<button type="button" class="btn btn-default toggleResetOptions"><i class="icon-undo"></i> Reset</button>
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="icon-caret-down"></i></button>
						<ul class="dropdown-menu">
							<li><a href="#" class="processCustomCode" data-type="{$type|escape:'html':'UTF-8'}" data-action="GetInitial">
							{l s='Reset to initial code, that was used when this page was loaded' mod='easycarousels'}</a></li>
							<li><a href="#" class="processCustomCode" data-type="{$type|escape:'html':'UTF-8'}" data-action="GetOriginal">
							{l s='Reset to original code, that was used when module was installed' mod='easycarousels'}</a></li>
						</ul>
					</div>
				</div>
			</div>
		{/foreach}
	</div>

	<div id="importer" class="panel importer clearfix">
		<div class="info-note alert-info">{include file=$howto_tpl_path}</div>
		<form method="post" class="export-form" action="" enctype="multipart/form-data">
			<input type="hidden" name="action" value="exportCarousels">
			<button type="submit" class="export btn btn-default">
				<i class="icon-download icon-rotate-180"></i>
				{l s='Export carousels' mod='easycarousels'}
			</button>
		</form>
		<button class="import btn btn-default">
			<i class="icon-download"></i>
			{l s='Import carousels' mod='easycarousels'}
		</button>
		<form action="" method="post" enctype="multipart/form-data" style="display:none;">
			<input type="file" name="carousels_data_file" style="display:none;">
		</form>
	</div>

	<div id="overrides" class="panel clearfix">
		<div class="info-note alert-info">
			{l s='In most cases overrides are processed automatically' mod='easycarousels'}.
			{l s='They are used to improve module functionality' mod='easycarousels'}.<br>
			<span class="b">{l s='NOTE: These are advanced settings' mod='easycarousels'}.</span>
			{l s='Do not change anything here, if you are not sure what are you doing.' mod='easycarousels'}
		</div>
		<div class="overrides-list">
			{foreach $overrides_data as $class_name => $override}
				<div class="override-item{if $override.installed === true} installed{else if $override.installed === false} not-installed{/if} clearfix">
					<span class="override-name b">{$override.path|escape:'html':'UTF-8'}</span>
					{if $override.installed === true || $override.installed === false}
						<span class="override-status alert-success">{l s='Installed' mod='easycarousels'}</span>
						<span class="override-status alert-danger">{l s='Not installed' mod='easycarousels'}</span>
					{else}
						<span class="override-status alert-warning">{l s='The following methods are already overriden: %s' mod='easycarousels' sprintf=[$override.installed]}</span>
						<span class="grey-note pull-right install-manually">{l s='Should be added manually' mod='easycarousels'}</span>
					{/if}
					<button class="btn btn-default install-override pull-right" data-override="{$override.path|escape:'html':'UTF-8'}">{l s='Install' mod='easycarousels'}</button>
					<button class="btn btn-default uninstall-override pull-right" data-override="{$override.path|escape:'html':'UTF-8'}">{l s='Uninstall' mod='easycarousels'}</button>
					<div class="grey-note">
						{$override.note|escape:'html':'UTF-8'}.
					</div>
				</div>
			{/foreach}
		</div>
	</div>

	<div id="info" class="panel clearfix">
		<div class="info-row">
			{l s='Current version:' mod='easycarousels'} <b>{$ec->version|escape:'html':'UTF-8'}</b>
		</div>
		<div class="info-row">
			<a href="{$info_links.changelog|escape:'html':'UTF-8'}" target="_blank">
				<i class="icon-code-fork"></i> {l s='Changelog' mod='easycarousels'}
			</a>
		</div>
		<div class="info-row">
			<a href="{$info_links.documentation|escape:'html':'UTF-8'}" target="_blank">
				<i class="icon-file-text"></i> {l s='Documentation' mod='easycarousels'}
			</a>
		</div>
		<div class="info-row">
			<a href="{$info_links.contact|escape:'html':'UTF-8'}" target="_blank">
				<i class="icon-envelope"></i> {l s='Contact us' mod='easycarousels'}
			</a>
		</div>
		<div class="info-row">
			<a href="{$info_links.modules|escape:'html':'UTF-8'}" target="_blank">
				<i class="icon-download"></i> {l s='Our modules' mod='easycarousels'}
			</a>
		</div>
	</div>
</div>
{* since 2.6.2 *}
