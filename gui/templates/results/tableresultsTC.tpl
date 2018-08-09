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

<script>
    function abab(){
        tst = jQuery(".x-grid-group-title");
        tst.on('click',function(){
            resizeframe();
    });
        //tst.trigger("click");
        resizeframe();
    }
    function resizeframe(){
        iFrameID = this.parent.document.getElementById("idIframe{$gui->tplan_id}");
        iFrameID.height = "";//console.log(iFrameID.contentWindow.document.body.scrollHeight);
        iFrameID.height = (iFrameID.contentWindow.document.body.scrollHeight + 50) + "px";
    }
</script>
<body >

<div class="workBack">
{*include file="inc_result_tproject_tplan.tpl" 
         arg_tproject_name=$gui->tproject_name arg_tplan_name=$gui->tplan_name arg_build_set=$gui->filterFeedback*}

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
                <script>
                    function resizeIframe(obj) {
                        obj.style.height = (obj.contentWindow.document.body.scrollHeight) + 'px';
                    }
                </script>
                <div class = "row">
                        <iframe id="execprint" class="col-md-12" frameborder="0" scrolling="horizontal" onload="resizeIframe(this)"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
</body> 
</html>