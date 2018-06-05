{*
TestLink Open Source Project - http://testlink.sourceforge.net/
$Id: buildEdit.tpl,v 1.22 2010/11/06 11:42:47 amkhullar Exp $

Purpose: smarty template - Add new build and show existing

@internal revisions

*}
{assign var="managerURL" value="lib/docs/docsEdit.php"}
{assign var="cancelAction" value="lib/docs/docsView.php"}

{lang_get var="labels"
          s="warning,warning_empty_doc_name,enter_build,enter_build_notes,active,
             open,builds_description,cancel,release_date,closure_date,
             copy_tester_assignments,show_event_history,
             show_calender,clear_date"}

{include file="inc_head.tpl" openHead="yes" jsValidate="yes" editorType=$gui->editorType}
{include file="inc_ext_js.tpl" bResetEXTCss=1}
{include file="inc_del_onclick.tpl"}

{literal}
    <script type="text/javascript">
    {/literal}
    var alert_box_title = "{$labels.warning|escape:'javascript'}";
    var warning_empty_doc_name = "{$labels.warning_empty_doc_name|escape:'javascript'}";
    {literal}
    function validateForm(f)
    {
        if (isWhitespace(f.doc_name.value))
        {
            alert_message(alert_box_title, warning_empty_doc_name);
            selectField(f, 'doc_name');
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

    <h1 class="title">Documentos</h1>

    <div class="workBack">
        {include file="inc_update.tpl" user_feedback=$gui->user_feedback 
         result=$sqlResult item="build"}

        <div> 
            <h2>{$gui->operation_descr|escape}
                {if $gui->mgt_view_events eq "yes" && $gui->build_id > 0}
                    <img style="margin-left:5px;" class="clickable" 
                         src="{$smarty.const.TL_THEME_IMG_DIR}/question.gif" onclick="showEventHistoryFor('{$gui->build_id}', 'builds')" 
                         alt="{$labels.show_event_history}" title="{$labels.show_event_history}"/>
                {/if}
            </h2>
            <form method="post" id="create_build" name="create_build" 
                  action="{$managerURL}" onSubmit="javascript:return validateForm(this);">
                <table class="common" >
                    <tr>
                        <th style="background:none;">Nome</th>
                        <td><input type="text" name="doc_name" id="doc_name"  
                                   value="{$gui->doc_name|escape}" required />
                            {include file="error_icon.tpl" field="doc_name"}
                        </td>
                    </tr>

                    <tr>
                        <th style="background:none;">Tipo de documento</th>
                        <td>
                            <select name="doc_type" class = "chosen-bulk-select" id="bulk_tester_div">
                                {html_options options=$gui->docs_types selected=$gui->SelectedDocs_types}
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th style="background:none;">Validade</th>
                        <td>
                            {* BUGID 3716, BUGID 3930 *}
                            <input type="text" class="date" 
                                   name="release_date" id="release_date" 
                                   value="{$gui->validity}" />
                            <img title="{$labels.show_calender}" src="{$smarty.const.TL_THEME_IMG_DIR}/calendar.gif"
                                 onclick="showCal('release_date-cal', 'release_date', '{$gsmarty_datepicker_format}');" >
                            <img title="{$labels.clear_date}" src="{$smarty.const.TL_THEME_IMG_DIR}/trash.png"
                                        onclick="javascript:var x = document.getElementById('release_date');
                                                 x.value = '';" >
                            <div id="release_date-cal" style="position:absolute;width:240px;left:300px;z-index:1;"></div>
                        </td>
                    </tr>
                    
                    <tr><th style="background:none;">{$labels.active}</th>
                        <td>
                            <input type="checkbox"  name="is_active" id="is_active"  
                                   {if $gui->is_active eq 1} checked {/if} />
                        </td>
                    </tr>
                </table>
                <div class="groupBtn">  

                    {* BUGID 628: Name edit Invalid action parameter/other behaviours if Enter pressed. *}
                    <input type="hidden" name="do_action" value="{$gui->buttonCfg->name}" />
                    <input type="hidden" name="docID" value="{$gui->doc_id}" />

                    <input type="submit" name="{$gui->buttonCfg->name}" value="{$gui->buttonCfg->value|escape}"
                           onclick="do_action.value = '{$gui->buttonCfg->name}'"/>
                    <input type="button" name="go_back" value="{$labels.cancel}" 
                           onclick="javascript: location.href = fRoot + '{$cancelAction}';"/>

                </div>
            </form>
        </div>
    </div>
<script>
    jQuery(document).ready(function () {
        jQuery(".chosen-select").chosen({ width: "85%", allow_single_deselect: true });
        jQuery(".chosen-bulk-select").chosen({ width: "35%", allow_single_deselect: true });

    });
</script>
</body>
</html>
