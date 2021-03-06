{* 
TestLink Open Source Project - http://testlink.sourceforge.net/

show Test Results and Metrics
@filesource	resultsTC.tpl

@internal revisions
@since 1.9.6 
20130203 - franciscom - TICKET 0005516
*}

{lang_get var="labels"
          s="title,date,printed_by,title_test_suite_name,platform,builds,
             title_test_case_title,version,generated_by_TestLink_on, priority,
             info_resultsTC_report,elapsed_seconds,export_as_spreadsheet"}

{include file="inc_head.tpl" openHead="yes"}
{foreach from=$gui->tableSet key=idx item=matrix name="initializer"}
  {assign var=tableID value=$matrix->tableID}
  {if $smarty.foreach.initializer.first}
    {$matrix->renderCommonGlobals()}
    {if $matrix instanceof tlExtTable}
        {include file="inc_ext_js.tpl" bResetEXTCss=1}
        {include file="inc_ext_table.tpl"}
    {/if}
  {/if}
  {$matrix->renderHeadSection()}
{/foreach}

</head>
<body>

{if $gui->printDate == ''}
{* +++++++++++++++++++++++++++ *}
{* Form to launch Excel Export *}
<form name="resultsTC" id="resultsTC" METHOD="POST"
      action="lib/results/resultsTC.php?format=3&do_action=result&tplan_id={$gui->tplan_id}&tproject_id={$gui->tproject_id}&buildListForExcel={$gui->buildListForExcel}">
<h1 class="title">{$gui->title|escape}
  {if $gui->apikey != ''}
  <input type="hidden" name="apikey" id="apikey" value="{$gui->apikey}">
  {/if}
  <!--input type="image" name="exportSpreadSheet" id="exportSpreadSheet" 
         src="{$tlImages.export_excel}" title="{$labels.export_as_spreadsheet}"-->
</form>
</h1>

{else}{* print data to excel *}
<table style="font-size: larger;font-weight: bold;">
	<tr><td>{$labels.title}</td><td>{$gui->title|escape}</td><tr>
	<tr><td>{$labels.date}</td><td>{$gui->printDate|escape}</td><tr>
	<tr><td>{$labels.printed_by}</td><td>{$user|escape}</td><tr>
</table>
{/if}

<div class="workBack">
{*include file="inc_result_tproject_tplan.tpl" 
         arg_tproject_name=$gui->tproject_name arg_tplan_name=$gui->tplan_name arg_build_set=$gui->filterFeedback*}

    <table>
	<tr>
            <td>{lang_get s="testproject"}</td><td>{$smarty.const.TITLE_SEP}</td>
            <td>
                    <span style="color:black; font-weight:bold; text-decoration: underline;">{$gui->tproject_name|escape}</span>
            </td>
	</tr>
        {if $gui->tplan_name != ''}
        <tr>
            <td>Sub-Adquirente</td><td>{$smarty.const.TITLE_SEP}</td>
            <td>
                <span style="color:black; font-weight:bold; text-decoration:underline;">
                    <form method="get">
                        <input type="hidden" value="0" name="format">
                    <select name="sub" id="selectSubs" onchange="this.form.action = this.selectedIndex == 0?'lib/results/resultsTCgroup.php?sub=0':'lib/results/resultsTC.php';this.form.submit()">
                        <option value = "0" select >Todos</option>
                        {html_options options=$gui->subs selected=$gui->sub}
                    </select>
                    </form> 
                </span>
            </td>
        </tr>
	<tr>
            <td>{lang_get s="testplan"}</td><td>{$smarty.const.TITLE_SEP}</td>
            <td> 
                <span style="color:black; font-weight:bold; text-decoration:underline;">
                    <form method="get">
                        <input type="hidden" value="0" name="format">
                        <select name="tplan_id" id="selectTestplan" onchange="this.form.action = this.selectedIndex == 0?'lib/results/resultsTCgroup.php':'lib/results/resultsTC.php';this.form.submit()"  >
                            <option value = "0" >Todos</option>
                            {html_options options=$gui->tplans selected=$gui->tplan_id}
                        </select>
                    </form> 

                </span>
            </td>
	</tr>
        <tr>
            <td>
                <br>
            </td>
        </tr>
        {/if}
    </table>
{foreach from=$gui->tableSet key=idx item=matrix}
  {$tableID="table_$idx"}
  {if $idx != 0}
  <h2>{$labels.platform}: {$gui->platforms[$idx]|escape}</h2>
  {/if}
  {$matrix->renderBodySection()}
{/foreach}


{if isset($gui->message)}
    <br><h2><b>{$gui->message}</b></h2> 
{/if}
<br />
<p class="italic">{$labels.info_resultsTC_report}</p>
<br />

{*{$labels.generated_by_TestLink_on} {$smarty.now|date_format:$gsmarty_timestamp_format}
<p>{$labels.elapsed_seconds} {$gui->elapsed_time}</p>*}
</div>
<div class="modal fade " id="Nissues">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Relatório de Execução</h4>
            </div>
            <div class="modal-body" >
                <div class = "row">
                        <iframe id="execprint" class="col-md-12" frameborder="0" scrolling="horizontal" onload="resizeIframe(this)"></iframe>
                </div>
                <script>
                    function resizeIframe(obj) {
                        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
                    }
                </script>
            </div>
        </div>
    </div>
</div>
</body>
</html>