<!-- comeca aqui o metricsDashboard2.tpl-->
{*
Testlink Open Source Project - http://testlink.sourceforge.net/ 
@filesource  metricsDashboard2.tpl
@internal revisions
@since 1.9.10                
*}
{lang_get var="labels"
          s="generated_by_TestLink_on,testproject,test_plan,platform,show_only_active,
             info_metrics_dashboard,test_plan_progress,project_progress, info_metrics_dashboard_progress"}

{include file="inc_head.tpl" openHead='yes'}
{include file="inc_ext_js.tpl" bResetEXTCss=1}
{foreach from=$gui->tableSet key=idx item=matrix name="initializer"}
  {if $smarty.foreach.initializer.first}
    {$matrix->renderCommonGlobals()}
    {if $matrix instanceof tlExtTable}
        {include file="inc_ext_table.tpl"}
    {/if}
  {/if}
  {$matrix->renderHeadSection()}
{/foreach}

{$tplan_metric=$gui->tplan_metrics}
<script type="text/javascript">
/*console.log(parent.document.getElementById("manageHelper"));*/
var circle = parent.document.getElementById("circle");
var loader = parent.document.getElementById("loader");
circle.hidden=true;
loader.hidden=true;
/*Ext.onReady(function() {ldelim}
	{foreach key="key" item="value" from=$gui->project_metrics}
    new Ext.ProgressBar({ldelim}
        text:'&nbsp;&nbsp;{lang_get s=$value.label_key}: {$value.value}% [{$tplan_metric.total.$key}/{$tplan_metric.total.active}]',
        width:'400',
        cls:'left-align',
        renderTo:'{$key}',
        value:'{$value.value/100}'
    {rdelim});
    {/foreach}
{rdelim});*/
</script>
<link rel="stylesheet" href="vendor/Argo/ArgoCustomizations.css">
<link rel="stylesheet" href="vendor/Argo/metricsDashboard2.css">
</head>

<body style = "background-color:transparent">
<div>
{if $gui->warning_msg == ''}
	{foreach from=$gui->tableSet key=idx item=matrix}
		{$tableID="table_$idx"}
   		{$matrix->renderBodySection($tableID)}
	{/foreach}
{else}
	<div class="user_feedback">
    {$gui->warning_msg}
    </div>
{/if}
{foreach key="key" item="value" from=$gui->graphList}
    {$value} 
{/foreach}
</div> 
</body>
</html>
<!-- comeca aqui o metricsDashboard2.tpl-->