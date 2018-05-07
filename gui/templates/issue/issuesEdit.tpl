{*
TestLink Open Source Project - http://testlink.sourceforge.net/
$Id: buildEdit.tpl,v 1.22 2010/11/06 11:42:47 amkhullar Exp $

Purpose: smarty template - Add new build and show existing

@internal revisions

*}
{assign var="managerURL" value="lib/issue/issuesEdit.php"}
{assign var="cancelAction" value="lib/issue/issuesView.php"}

{lang_get var="labels"
          s="warning,warning_empty_subadiq_name,enter_build,enter_build_notes,active,
             open,builds_description,cancel,release_date,closure_date,closed_on_date,
             copy_tester_assignments, assignment_source_build,show_event_history,
             show_calender,clear_date"}

{include file="inc_head.tpl" openHead="yes" jsValidate="yes" editorType=$gui->editorType}
{include file="inc_ext_js.tpl" bResetEXTCss=1}
{include file="inc_del_onclick.tpl"}

{literal}
<script type="text/javascript">
{/literal}
var alert_box_title = "{$labels.warning|escape:'javascript'}";
var warning_empty_subadiq_name = "{$labels.warning_empty_subadiq_name|escape:'javascript'}";
{literal}
function validateForm(f)
{
  if (isWhitespace(f.subadiq_name.value)) 
  {
      alert_message(alert_box_title,warning_empty_subadiq_name);
      selectField(f, 'subadiq_name');
      return false;
  }
  return true;
}
</script>
{/literal}
</head>


<body onload="showOrHideElement('closure_date',{$gui->is_open})">
{assign var="cfg_section" value=$smarty.template|basename|replace:".tpl":""}
{config_load file="input_dimensions.conf" section=$cfg_section}

<h1 class="title">Inconsistencias</h1>

<div class="workBack">
{include file="inc_update.tpl" user_feedback=$gui->user_feedback 
         result=$sqlResult item="build"}

<div> 
  <h2>{$gui->operation_descr|escape}
    {if $gui->mgt_view_events eq "yes" && $gui->build_id > 0}
        <img style="margin-left:5px;" class="clickable" 
             src="{$smarty.const.TL_THEME_IMG_DIR}/question.gif" onclick="showEventHistoryFor('{$gui->build_id}','builds')" 
             alt="{$labels.show_event_history}" title="{$labels.show_event_history}"/>
    {/if}
  </h2>
  <form method="post" id="create_build" name="create_build" 
        action="{$managerURL}" onSubmit="javascript:return validateForm(this);">
  <table class="common" >
    <tr>
      <th style="background:none;">Nome</th>
      <td><input type="text" name="subadiq_name" id="subadiq_name" 
                 maxlength="{#subadiq_name_MAXLEN#}" 
                 value="{$gui->subadiq_name|escape}" size="{#subadiq_name_SIZE#}" required />
                {include file="error_icon.tpl" field="subadiq_name"}
      </td>
    </tr>
   
    <tr style="/*display:none*/">
      <th style="background:none;">Categoria</th>
      <td>
        <select name="category" class = "chosen-bulk-select" id="bulk_tester_div">
                {html_options options=$gui->Categories selected=$gui->SelectedCategory}
        </select>
      </td>
    </tr>
    
   <tr style="/*display:none*/">
	  <th style="background:none;">Marcadores</th>
      <td>
        <select name="markersID[]" class = "chosen-bulk-select" multiple = multiple id="bulk_tester_div">
                {html_options options=$gui->testers selected=$gui->selectedMarkers}
        </select>
      </td>
    </tr>
   <tr style="/*display:none*/">
	  <th style="background:none;">Disponível a</th>
      <td>
        <select name="projectsID[]" class = "chosen-bulk-select" multiple = multiple id="bulk_tester_div">
                {html_options options=$gui->projects selected=$gui->selectedProjects}
        </select>
      </td>
    </tr>
   <tr>
	  <th style="background:none;">Descrição</th>
      <td>
          <textarea name="descText">{$gui->descText}</textarea>
      </td>
    </tr>
   
   
  </table>
  <div class="groupBtn">  

    {* BUGID 628: Name edit Invalid action parameter/other behaviours if Enter pressed. *}
    <input type="hidden" name="do_action" value="{$gui->buttonCfg->name}" />
    <input type="hidden" name="markerID" value="{$gui->subadiq_id}" />
    
    <input type="submit" name="{$gui->buttonCfg->name}" value="{$gui->buttonCfg->value|escape}"
           onclick="do_action.value='{$gui->buttonCfg->name}'"/>
    <input type="button" name="go_back" value="{$labels.cancel}" 
           onclick="javascript: location.href=fRoot+'{$cancelAction}';"/>

  </div>
  </form>
</div>
</div>
</form>
<script>
jQuery( document ).ready(function() {
jQuery(".chosen-select").chosen({ width: "85%", allow_single_deselect: true });
jQuery(".chosen-bulk-select").chosen({ width: "35%", allow_single_deselect: true });

});
</script>
</body>
</html>
