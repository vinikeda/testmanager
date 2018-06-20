{*
TestLink Open Source Project - http://testlink.sourceforge.net/
$Id: buildEdit.tpl,v 1.22 2010/11/06 11:42:47 amkhullar Exp $

Purpose: smarty template - Add new build and show existing

@internal revisions

*}
{assign var="managerURL" value="lib/macros/macrosEdit.php"}
{assign var="cancelAction" value="lib/macros/macrosView.php"}

{lang_get var="labels"
          s="warning,warning_empty_subadiq_name,enter_build,enter_build_notes,active,
             open,builds_description,cancel,release_date,closure_date,closed_on_date,
             copy_tester_assignments, assignment_source_build,show_event_history,name,docs,
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
{$count = 0}
</head>
<body onload="showOrHideElement('closure_date',{$gui->is_open})">
{assign var="cfg_section" value=$smarty.template|basename|replace:".tpl":""}
{config_load file="input_dimensions.conf" section=$cfg_section}

<h1 class="title">Macros</h1>

<div class="workBack">
{include file="inc_update.tpl" user_feedback=$gui->user_feedback 
         result=$sqlResult item="build"}

<div>
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
      <th style="background:none;">{$labels.name}</th>
      <td><input type="text" name="subadiq_name" id="subadiq_name" 
                 maxlength="{#subadiq_name_MAXLEN#}" 
                 value="{$gui->subadiq_name|escape}" size="{#subadiq_name_SIZE#}" required />
                {include file="error_icon.tpl" field="subadiq_name"}
      </td>
    </tr>
    <!--tr>
        <th style="background:none;">
            {$labels.docs}
        </th>
        <td>
            <div id="docs" >
                {foreach item=selected key=key from=$gui->selectedMarkers}
                    <select id="docSelector{$count}" class="chosen-select" name="cfSelected[]">
                        {html_options options = $gui->doc_type selected = $selected}
                    </select>
                    <div id="docs{$count}">
                        {$gui->inputs[$key]}
                    </div>
                    <script>
                        element = document.getElementById("docs{$count}").querySelector("#{$gui->ids[$key]}");
                        element.value = "{$gui->Values[$key]}";
                        element.setAttribute("name","cfValue[]");
                    </script>
    <input type="button" id="rmv{$count}" value="Remove" onclick="cleanField({$count})">
<br id="br{*$count++*}">
                {/foreach}
            </div>
            <button type="button" onclick = "addDocSelector()">Adicionar Documento</button>
        </td>
    </tr-->
            {foreach item=selected key=key from=$gui->selectedMarkers}
                <tr>
                    <th style="background:none;">
                        {$gui->doc_type[$selected]}
                    </th>
                    <td>
                        <div id="docs{$count}">
                        {$gui->inputs[$key]}
                        </div>
                        <input type="hidden" name="cfSelected[]" value="{$selected}">
                <script>
                    element = document.getElementById("docs{$count++}").querySelector("#{$gui->ids[$selected]}");{*element = document.getElementById("docs{$count}").querySelector("#{$gui->ids[$key]}");*}
                    element.value = "{$gui->Values[$key]}";
                    element.setAttribute("name","cfValue[]");
                </script>
                    </td>
                </tr>
            {/foreach}
   
   
   
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
<div id ="docList" style="display:none;">
    {html_options options = $gui->doc_type}
</div>
<script>
jQuery( document ).ready(function() {
jQuery(".chosen-select").chosen({ width: "300px", allow_single_deselect: true });
jQuery(".chosen-bulk-select").chosen({ width: "35%", allow_single_deselect: true });
});

{literal}
var docTypeList = jQuery('#docList').html();
function addDocSelector(){
    appendString = '<select id="docSelector'+docCount+'" target="docs'+docCount+'" name="cfSelected[]">'+docTypeList+'</select>\
    <div id="docs'+docCount+'" type="text" > </div>   <input type="button" id="rmv'+docCount+'" value="Remove" onclick="cleanField('+docCount+')">\
<br id="br'+docCount+'">';
    jQuery('#docs').append(appendString);
    docSelector = jQuery("#docSelector"+docCount);
    docSelector.chosen({ width: "300px", allow_single_deselect: true });
    docSelector.chosen().change(
            function(desc, selected){
                getField(selected.selected,this.getAttribute('target'));
            }
        );
    getField(docSelector.val(),docSelector.attr('target'));
    docCount++;
}

function cleanField(number){
    jQuery('#rmv'+number).remove();
    jQuery('#docs'+number).remove();
    jQuery('#docSelector'+number).chosen("destroy");
    jQuery('#docSelector'+number).remove();
    jQuery('#br'+number).remove();
}
function getField(id,target){
    jQuery.ajax({
        url:'lib/macros/getField.php?macro_id='+id, success: function(result){
            jsonObj = JSON.parse(result);
            temp = jQuery('#'+target);
            temp.empty();
            temp.append(jsonObj[0]['input']);
            document.getElementById(target).querySelector('#'+jsonObj[0]['label_id']).setAttribute('name','cfValue[]');
            
        }
    });
}

{/literal}
</script>
</body>

<script>
var docCount = {$count};
</script>
</html>
