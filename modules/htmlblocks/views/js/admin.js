/* HTML Blocks Prestashop module
 * Copyright 2016, Prestaddons
 * Author: Prestaddons
 * Website: http://www.prestaddons.fr
 */

$(document).ready(function(){
	
	var date = new Date();
	var hours = date.getHours();
	if (hours < 10)
		hours = "0" + hours;
	var mins = date.getMinutes();
	if (mins < 10)
		mins = "0" + mins;
	var secs = date.getSeconds();
	if (secs < 10)
		secs = "0" + secs;
	$('.datepicker').datepicker({
		prevText: '',
		nextText: '',
		dateFormat: 'yy-mm-dd ' + hours + ':' + mins + ':' + secs
	});
	$('.ui-datepicker').hide();
	
	$('#modulecontent .nav li').click(function(e) {
		$('#modulecontent .nav li a').removeClass('active');
		$(this).children('a').addClass('active');

		var id_block = $(this).attr('data-id-block');
		$('input[name=id_block_active').val(id_block);

		editAreaLoader.init({
			id: "css_edit_"+id_block,	// id of the textarea to transform	
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
	});
});


/*function my_save(id, content){
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
}*/