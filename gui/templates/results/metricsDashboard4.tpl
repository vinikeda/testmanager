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
<script>
    function resetparams(radical,period,clear,width){
        document.getElementById('period'+radical).value = period;
        document.getElementById('width'+radical).value = width;
        statlist = clear.split('');console.log(statlist);
        
        {foreach key = "key" item="value" from=$gui->status_radicals}
            temp = document.getElementById("{$key}"+radical);console.log("{$value}");
            temp.checked = (statlist.includes('{$value}'));
        {/foreach}
        
        getnewparams(radical);
    }
    
    function getnewparams(radical){
        period = document.getElementById('period'+radical).value;
        width = document.getElementById('width'+radical).value;
        //clear = document.getElementById('stat'+radical).checked;
        stats = '';
        {foreach key = "key" item="value" from=$gui->status_radicals}
            temp = document.getElementById("{$key}"+radical);
            if(temp.checked)stats += temp.value;
        {/foreach}
            
        //console.log(stats);
        setparams(radical,period,stats,width);
    }
    
    function setparams(radical,period,clear,width){
        graph = document.getElementById('imgtime'+radical);
        graph.width = width;
        //div = document.getElementById('time'+radical);//console.log(div.style.width);//console.log(div.offsetWidth);
        //div.setAttribute("style","clear:both;resize:both;overflow: scroll;width:"+width+"px");
        params = graph.src.split('&');
        params[2] = params[2].replace(params[2].split('=')[1],clear);
        params[3] = params[3].replace(params[3].split('=')[1],period);
        params[4] = (params[4].split('=')[1] == '')?params[4] + 1500: params[4].replace(params[4].split('=')[1],width);
        str = params[0];
        for(var i = 1;i<params.length;i++){
            str+='&'+params[i];
        }
        graph.src = str;
    }
</script>
</body>

<style>
    div.x-panel-tbar,div.x-grid3-header{
        display:none;
    }
    div.x-grid3-body{
        margin-top: 26px;
    }
</style>

</html>
<!-- termina aqui o metricsDashboard2.tpl-->