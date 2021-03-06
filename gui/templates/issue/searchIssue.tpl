{$cfg_section=$smarty.template|basename|replace:".tpl":""}
{config_load file="input_dimensions.conf" section=$cfg_section}

{* Configure Actions *}
{$managerURL="lib/issue/searchIssue.php"}
{$editAction="$managerURL?do_action=edit&amp;markerID="}
{$deleteAction="$managerURL?do_action=do_delete&markerID="}
{$createAction="$managerURL?do_action=create"}


{lang_get s='warning_delete_build' var="warning_msg"}
{lang_get s='delete' var="del_msgbox_title"}

{lang_get var="labels" 
          s='title_build_2,test_plan,th_title,th_description,th_active,
             th_open,th_delete,alt_edit_build,alt_active_build,
             alt_open_build,alt_delete_build,no_builds,btn_build_create,
             builds_description,sort_table_by_column,th_id,release_date,
             inactive_click_to_change,active_click_to_change,click_to_set_open,click_to_set_closed'}

{include file="inc_head.tpl" openHead="yes" jsValidate="yes" enableTableSorting="yes"}

</head>

<body>
    <h1 class="title">Inconsistencia</h1>

    <form method="post" id="create_build" name="create_build" 
          action="{$managerURL}" onSubmit="javascript:return validateForm(this);">
        
        <table>
            <tr style="display:none">
                <td>
                    Categoria
                </td>
                <td>
                    <select name="category" class = "chosen-select" id="bulk_tester_div">
                        {html_options options=$gui->Categories selected=$gui->SelectedCategory}
                    </select>
                </td>
            </tr>
            <tr style="display:none">
                <td>
                    Marcadores
                </td>
                <td>
                    <select name="markersID[]" class = "chosen-bulk-select" multiple = multiple id="bulk_tester_div">
                        {html_options options=$gui->markers selected=$gui->selectedMarkers}
                    </select>
                </td>
            </tr>
            <script>
                jQuery( document ).ready(function() {
                jQuery(".chosen-select").chosen({ width: "85%", allow_single_deselect: true });
                jQuery(".chosen-bulk-select").chosen({ width: "100%", allow_single_deselect: true });
                });
            </script>
            <tr>
                <td>
                    Buscar Erros
                </td>
                <td>
                    <input id="chkfilter">
                </td>
            </tr>
            <tr>
                <td>
                    Erros
                </td>
                <td>
                    <div style="overflow-y: scroll;height:180px">
                        {foreach key=chave item=issue from=$gui->issues}
                            <div id="issr{$issue.description}"><input id="issr{$issue.description}" type="checkbox" name="issue[{$issue.id}]">{$issue.description}</div><br id = "issr{$issue.description}">
                        {/foreach}
                    </div>
                    <style>
                        [id^="issr"]{
                            display: inline-block;
                        }

                    </style>
                </td>
            </tr>
            <script>
                jQuery('#chkfilter').on('keyup', function() {
                    var query = this.value;

                    jQuery('[id^="issr"]').each(function(i, elem) {

                          if (elem.id.indexOf(query) != -1) {
                              elem.style.display = 'inline-block';console.log(elem);
                          }else{
                              elem.style.display = 'none';
                          }
                    });
                });
            </script>
        </table>
        
        <!--table class="common" >    
            <tr>
                <th style="background:none;">Categoria</th>
                <td>
                    <select name="category" class = "chosen-bulk-select" id="bulk_tester_div">
                        {html_options options=$gui->Categories selected=$gui->SelectedCategory}
                    </select>
                </td>
            </tr>

            <tr>
                <th style="background:none;">Marcadores</th>
                <td>
                    <select name="markersID[]" class = "chosen-bulk-select" multiple = multiple id="bulk_tester_div">
                        {html_options options=$gui->testers selected=$gui->selectedMarkers}
                    </select>
                </td>
            </tr>
            </tr>




        </table>
        <div class="groupBtn">  

            {* BUGID 628: Name edit Invalid action parameter/other behaviours if Enter pressed. *}
            <input type="hidden" name="do_action" value="{$gui->buttonCfg->name}" />
            <input type="hidden" name="markerID" value="{$gui->subadiq_id}" />

            <input type="submit" name="{$gui->buttonCfg->name}" value="{$gui->buttonCfg->value|escape}"
                   onclick="do_action.value = '{$gui->buttonCfg->name}'"/>
            <input type="button" name="go_back" value="{$labels.cancel}" 
                   onclick="javascript: location.href = fRoot + '{$cancelAction}';"/>

        </div-->
    </form>

</body>