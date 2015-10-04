{* $Id$ *}
{* Used by smarty_function_filegal_uploader() when $prefs.file_galleries_use_jquery_upload is enabled *}
{* The fileinput-button span is used to style the file input field as button *}
<div class="btn btn-success fileinput-button margin-bottom-sm">
	{icon name='plus'}
	<span>Add files...</span>
	{* The file input field used as target for the file upload widget *}
	<input id="fileupload" type="file" name="files[]" multiple>
	{* auto-upload user pref *}
</div>
<label>
	<input type="checkbox" name="autoupload"{if $prefs.filegals_autoupload eq 'y'} checked="checked"{/if}>
	{tr}Automatic upload{/tr}
</label>{* The container for the uploaded files *}
<div id="files" class="files"></div>
