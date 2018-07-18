{* 
TestLink Open Source Project - http://testlink.sourceforge.net/
@filesource buildView.tpl

Purpose: smarty template - Show existing builds

@internal revisions
@since 1.9.10
*}
{$cfg_section=$smarty.template|basename|replace:".tpl":""}
{config_load file="input_dimensions.conf" section=$cfg_section}

{* Configure Actions *}
{$managerURL=$gui->editFile}
{$editAction="$managerURL?do_action=edit&ID="}
{$deleteAction="$managerURL?do_action=do_delete&ID="}
{$createAction="$managerURL?do_action=create"}


{lang_get s='warning_delete_build' var="warning_msg"}
{lang_get s='delete' var="del_msgbox_title"}

{lang_get var="labels" 
          s='th_title,th_description,th_active,
             th_open,th_delete,alt_edit_build,alt_active_build,
             alt_open_build,alt_delete_build,no_builds,btn_build_create,
             builds_description,sort_table_by_column,th_id,release_date,
             inactive_click_to_change,active_click_to_change,click_to_set_open,click_to_set_closed'}

{include file="inc_head.tpl" openHead="yes" jsValidate="yes" enableTableSorting="yes"}
{include file="inc_del_onclick.tpl"}

<script type="text/javascript">
/* All this stuff is needed for logic contained in inc_del_onclick.tpl */
var del_action=fRoot+'{$deleteAction}';
</script>

</head>
<body {$body_onload} id="buildEdit">{*id mantido para evitar mudanças no css*}

<h1 class="title">{$gui->title}</h1>

<div class="workBack">
{include file="inc_update.tpl" result=$sqlResult item="sub-adquirente" user_feedback=$gui->user_feedback}

{* ------------------------------------------------------------------------------------------- *}
<div id="existing_items">
  {if $gui->list ne ""}
  <form method="post" id="buildView" name="buildView" action="{$managerURL}">
    <input type="hidden" name="do_action" id="do_action" value="">
    <input type="hidden" name="ID" id="ID" value="">


    {* table id MUST BE item_view to use show/hide API info *}
  	<table id="item_view" class="simple_tableruler sortable">
  		<tr>
  			<th>{$tlImages.toggle_api_info}{$tlImages.sort_hint}{$labels.th_title}</th>
  			<th class="{$noSortableColumnClass}">{$labels.th_delete}</th>
  		</tr>
  		{foreach item=item from=$gui->list}
        	<tr>
  				<td><span class="api_info" style='display:none'>{$tlCfg->api->id_format|replace:"%s":$item.{$gui->fieldID}}</span>
  				    <a href="{$editAction}{$item.{$gui->fieldID}}" title="{$labels.alt_edit_build}">{$item.{$gui->fieldListed}|escape}
  					     {if $gsmarty_gui->show_icon_edit}
  					         <img style="border:none" alt="{$labels.alt_edit_build}" title="{$labels.alt_edit_build}"
  					              src="{$tlImages.edit}"/>
  					     {/if}    
  					  </a>   
  				</td>
  				<td class="clickable_icon">
				       <img style="border:none;cursor: pointer;"  title="{$labels.alt_delete_build}" 
  				            alt="{$labels.alt_delete_build}" 
 					            onclick="delete_confirmation({$item.{$gui->fieldID}},'{$item.{$gui->fieldListed}|escape:'javascript'|escape}',
 					                                         '{$del_msgbox_title}','{$warning_msg}');"
  				            src="{$tlImages.delete}"/>
  				</td>
  			</tr>
  		{/foreach}
  	</table>
   </form> 
  {else}
  	<p>{$labels.no_builds}</p>
  {/if}
</div>
{* ------------------------------------------------------------------------------------------- *}

<div class="groupBtn">
  <form method="post" action="{$createAction}" id="create_build">
    <input type="submit" name="create_build" value="{$labels.btn_build_create}" />
  </form>
</div>

<!--p class ="white">{$labels.builds_description}</p-->
</div>

</body>
</html>
