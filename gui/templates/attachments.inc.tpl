{*
Testlink Open Source Project - http://testlink.sourceforge.net/
@filesource attachments.inc. tpl

@internal revisions
@since 1.9.10

Generic attachment management

Input:
  $attach_attachmentsInfos
  $attach_id
  $attach_tableName
  $attach_show_upload_btn
  $attach_downloadOnly
  $attach_tableClassName
  $attach_inheritStyle
  $attach_tableStyles

  

Smarty global variables:
$gsmarty_attachments
*}

{lang_get var='labels'
          s='title_upload_attachment,enter_attachment_title,btn_upload_file,warning,attachment_title,
             display_inline,local_file,attachment_upload_ok,title_choose_local_file,btn_cancel,
             max_size_file_upload,display_inline_string'}

{lang_get s='warning_delete_attachment' var="warning_msg"}
{lang_get s='delete' var="del_msgbox_title"}
<!--script src="//code.jquery.com/jquery-1.11.1.min.js" </script-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"> </script>
<script type="text/javascript">
jQuery(document).ready(function(){

     jQuery("a#link_attach.bold").each(function() {
         if(jQuery(this).text().length > 45) {
           jQuery(this).text(jQuery(this).text().substr(0,45)+"...");
         }
     });
});	
function checkFileSize()
{
  if (typeof FileReader !== "undefined") {
    var bytes = document.getElementById('uploadedFile').files[0].size;
    if( bytes > {$gui->import_limit} )
    {
      var msg = "{$labels.max_size_file_upload}: {$gui->import_limit} Bytes < " + bytes + ' Bytes';
      alert(msg);
      return false;
    }   
  }
  return true;
}  


var warning_delete_attachment = "{lang_get s='warning_delete_attachment'}";
{if isset($attach_loadOnCancelURL)}
  var attachment_reloadOnCancelURL = '{$attach_loadOnCancelURL}';
{/if} 

function attach(id) {
	document.getElementById('attachment_form').elements.namedItem("id").value = id;
	document.getElementById('attachment_form').submit();
}	

</script>


{if $gsmarty_attachments->enabled eq FALSE}
	<div class="messages">{lang_get s='attachment_feature_disabled'}<p>
		{$gsmarty_attachments->disabled_msg}
	</div>
{/if}
{include file="inc_del_onclick.tpl"}
{if $gsmarty_attachments->enabled && ($attach_attachmentInfos != "" || $attach_show_upload_btn)}
	<table class="{$attach_tableClassName}" {if $attach_inheritStyle == 0} style="{$attach_tableStyles}" {/if}>	
		{if $attach_show_title}
		<tr>
			<td class="bold">{lang_get s="attached_files"}{$tlCfg->gui_title_separator_1}</td>
		</tr>
		{/if}		
		<table style="width:100%;" >
			<tr>
				{$log = 0}
				{$rec = 0}
				{$oth = 0}
				{$car = 0}
				{$col = 0}
				{$titles = array()}
				{$max_titles = array()}
				{$max = 0}
				{$min = 1}
				{foreach from=$attach_attachmentInfos item=cont}
					{if $cont.title}
						{$cur_string = $cont.title}
						{if !($cur_string|in_array:$max_titles)}
							{if $cur_string == "Log"}
								{$log = 1}
							{elseif $cur_string == "Receipt"}
								{$rec = 1}
							{elseif $cur_string == "Cardspy"}
								{$car = 1}
							{elseif $cur_string == "Others"}
								{$oth = 1}
							{/if}
							{if $max_string != $cur_string}
								{$min = 1}
								{$max_string=$cur_string}
								{$max_titles[$cur_string] = $cur_string}							
							{/if}		
							{else}
								{assign var="min" value=$min+1}
						{/if}
						{if $min > $max}
							{$max = $min}
						{/if}						
					{/if}
				{/foreach}
				{foreach from=$attach_attachmentInfos item=array}
					{if $array.title eq ""}
						{if $gsmarty_attachments->action_on_display_empty_title == 'show_icon'}
							{$my_link=$gsmarty_attachments->access_icon}
						{else}
							{$my_link=$gsmarty_attachments->access_string}
					{/if}
					{else}
						{$my_link=$array.title|escape}
					{/if}
						{if !("Log"|in_array:$titles) && ($log == 0)}
							{$titles["Log"] = "Log"}
							{$rowcur = 0}
							<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Log</th>				
									</tr>
									{while $rowcur < $max}
										<tr>
											<td style="vertical-align:middle;"> - </td>
										</tr>
										{assign var="rowcur" value=$rowcur+1}
									{/while}
								</table>
							</td>
						{/if}
						{if ("Log"|in_array:$titles) && !("Receipt"|in_array:$titles) && ($rec == 0)}
							{$titles["Receipt"] = "Receipt"}
							{$rowcur = 0}
							<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Receipt</th>				
									</tr>
									{while $rowcur < $max}
										<tr>
											<td style="vertical-align:middle;"> - </td>
										</tr>
										{assign var="rowcur" value=$rowcur+1}
									{/while}
								</table>
							</td>
						{/if}
						{if ("Log"|in_array:$titles) && ("Receipt"|in_array:$titles) && !("Cardspy"|in_array:$titles) && ($car == 0)}
							{$titles["Cardspy"] = "Cardspy"}
							{$rowcur = 0}
							<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Cardspy</th>				
									</tr>
									{while $rowcur < $max}
										<tr>
											<td style="vertical-align:middle;"> - </td>
										</tr>
										{assign var="rowcur" value=$rowcur+1}
									{/while}
								</table>
							</td>
						{/if}
						{if ("Log"|in_array:$titles) && ("Receipt"|in_array:$titles) && ("Cardspy"|in_array:$titles) && !("Others"|in_array:$titles) && ($oth == 0)}
							{$titles["Others"] = "Others"}
							{$rowcur = 0}
							<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Others</th>				
									</tr>
									{while $rowcur < $max}
										<tr>
											<td style="vertical-align:middle;"> - </td>
										</tr>
										{assign var="rowcur" value=$rowcur+1}
									{/while}
								</table>
							</td>
						{/if}
						{if !($my_link|in_array:$titles)}
							{$rowcur = 0}
							{if $last != $my_link}
								{$last=$my_link}
								
								{$titles[$my_link] = $my_link}
								<td width="25%">
									<table style="width:100%; font-size: 13px;">
										<tr>
											<th>{$last}</th>				
										</tr>										
										{foreach from=$attach_attachmentInfos item=info key=chave}		
											{if $last == $info.title}
											{assign var="rowcur" value=$rowcur+1}
											<tr>
												<td style="vertical-align:middle;">
														<a id="link_attach" href="javascript:attach({$info.id});" class="bold" target="_blank" data-toggle="tooltip" title="{$info.file_name|escape} - {localize_date d=$info.date_added|escape}">
														{$info.file_name|escape} - {localize_date d=$info.date_added|escape} </a> 
														{if $info.is_image} 
															<img src="{$tlImages.eye}" style="border:none" title="{$labels.display_inline}" 
															 onclick="c4i = document.getElementById('inline_img_container_{$info.id}');
															 c4i.innerHTML=toogleImageURL('inline_img_container_{$info.id}',{$info.id});"/>
														{/if}
														{if $info.is_image}
															<span><img src="{$tlImages.ghost_item}" title="{$labels.display_inline_string}" style="border:none" onclick="showHideByClass('span','ghost_' + {$info.id});"></span>
															<span class='ghost_{$info.id}' style='display:none'>{$info.inlineString}</span>
														{/if}

														{if !$attach_downloadOnly}
															<a href="javascript:delete_confirmation({$info.id},'{$info.file_name|escape:'javascript'|escape}',
																						  '{$del_msgbox_title|escape:'javascript'|escape}','{$warning_msg|escape:'javascript'|escape}',jsCallDeleteFile);">
															<img style="border:none;" alt="{lang_get s='alt_delete_attachment'}"
																				 title="{lang_get s='alt_delete_attachment'}"
																				 src="{$tlImages.delete}" /></a>
														{/if}
												
												</td>
											</tr>
											<tr>
												<td id="inline_img_container_{$info.id}" style="vertical-align:middle;">
												</td>
											</tr>  {* to display images inline on user request *}
									
											{/if}
										{/foreach}							
										{while $rowcur < $max}
													<tr>
														<td style="vertical-align:middle;"> &nbsp </td>
													</tr>
													{assign var="rowcur" value=$rowcur+1}
										{/while}
									</table>
								</td>
							{/if}
						{/if}
				{/foreach}
				{if !("Log"|in_array:$titles) && ($log == 0)}
							{$titles["Log"] = "Log"}
							{$rowcur = 0}
							<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Log</th>				
									</tr>
									{while $rowcur < $max}
										<tr>
											<td style="vertical-align:middle;"> - </td>
										</tr>
										{assign var="rowcur" value=$rowcur+1}
									{/while}
								</table>
							</td>
						{/if}
						{if ("Log"|in_array:$titles) && !("Receipt"|in_array:$titles) && ($rec == 0)}
							{$titles["Receipt"] = "Receipt"}
							{$rowcur = 0}
							<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Receipt</th>				
									</tr>
									{while $rowcur < $max}
										<tr>
											<td style="vertical-align:middle;"> - </td>
										</tr>
										{assign var="rowcur" value=$rowcur+1}
									{/while}
								</table>
							</td>
						{/if}
						{if ("Log"|in_array:$titles) && ("Receipt"|in_array:$titles) && !("Cardspy"|in_array:$titles) && ($car == 0)}
							{$titles["Cardspy"] = "Cardspy"}
							{$rowcur = 0}
							<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Cardspy</th>				
									</tr>
									{while $rowcur < $max}
										<tr>
											<td style="vertical-align:middle;"> - </td>
										</tr>
										{assign var="rowcur" value=$rowcur+1}
									{/while}
								</table>
							</td>
						{/if}
						{if ("Log"|in_array:$titles) && ("Receipt"|in_array:$titles) && ("Cardspy"|in_array:$titles) && !("Others"|in_array:$titles) && ($oth == 0)}
							{$titles["Others"] = "Others"}
							{$rowcur = 0}
							<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Others</th>				
									</tr>
									{while $rowcur < $max}
										<tr>
											<td style="vertical-align:middle;"> - </td>
										</tr>
										{assign var="rowcur" value=$rowcur+1}
									{/while}
								</table>
							</td>
						{/if}
			</tr>	
	</table>

	{if $attach_show_upload_btn && !$attach_downloadOnly}
		<div  class="importArchives">
			<form action="{$gui->fileUploadURL}" method="post" enctype="multipart/form-data" id="aForm" onsubmit="javascript:return checkFileSize();">
				<label for="uploadedFile" class="labelHolder">{$labels.local_file} </label>
				<img class="clickable" src="{$tlImages.activity}" title="{$labels.max_size_file_upload}: {$gui->import_limit} Bytes)">
				<input type="hidden" name="MAX_FILE_SIZE" value="{$gui->import_limit}" /> {* restrict file size *}
				<input type="file" name="uploadedFile" id="uploadedFile" size="{#UPLOAD_FILENAME_SIZE#}" />
				&nbsp;&nbsp;&nbsp;&nbsp;
				<span class="labelHolder">{$labels.attachment_title}:</span>
				<input type="text" id="fileTitle" name="fileTitle" maxlength="{#ATTACHMENT_TITLE_MAXLEN#}" size="{#ATTACHMENT_TITLE_SIZE#}" />
				<input type="submit" value="{$labels.btn_upload_file}"/>
			</form>
			{if $gui->fileUploadMsg != ''}
				<p class="bold" style="color:red">{$gui->fileUploadMsg}</p>
			{/if}
		</div>
	{/if}
{/if}
