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
/*var circle = parent.document.getElementById("circle");
var loader = parent.document.getElementById("loader");
circle.hidden=true;
loader.hidden=true;*/
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


<script type="text/javascript" src="vendor/Argo/VanBase/jquery.js"></script>
 <script type="text/javascript" src="vendor/Argo/VanBase/i18n.js"></script>
 <script type="text/javascript" src="vendor/Argo/VanBase/VanBase.js"></script>
 <script type="text/javascript" src="vendor/Argo/VanBase/VanCharts.js"></script>
    {literal}
    <script type="text/javascript">
        var execute = 0;
        function draw(elemen){
            elem = $(elemen);
            elem.empty();
            tplan = elem.attr("tplan");
            build = elem.attr("build");
            doAjax(tplan,build,elem);
            //console.log(document.getElementsByClassName("piechart"));
            //var pies = document.getElementsByClassName("piechart");
           // console.log (pies);
            /*execute = 1;
            pies = $(".piechart");console.log(execute);
            pies.each(function(elem){
                elem = this;
                elem.empty();console.log(elem);
                tplan = elem.getAttribute("tplan");
                build = elem.getAttribute("build");
                doAjax(tplan,build,elem);
            });
       /* for(i = 0;i<pies.length;i++){
            console.log(pies[i]);
        }
           /* var text =$("#code").val(); 
                var options=eval("("+text+")")
            var dom = $("#chartContainer");
                dom.empty();
            var charts = new VanCharts(options, dom);*/
        
    }
        
        function doAjax(testplan,build,dom){
            jQuery.ajax({
                url:"lib/results/overallPieChartPerBuildV2.php?tplan_id="+testplan+"&build="+build, success: function(result){
                    piechart = new VanCharts(eval('('+result+')'), dom);
                }
            });
        }
    </script>
    {/literal}
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
    function resetparams(radical,period,clear,width,cumulative,timetype){
        document.getElementById('period'+radical).value = period;
        document.getElementById('width'+radical).value = width;
        document.getElementById('cumulative'+radical).checked = cumulative;
        statlist = clear.split('');
        
        {*foreach key = "key" item="value" from=$gui->status_radicals}
            temp = document.getElementById("{$key}"+radical);//console.log("{$value}");
            temp.checked = (statlist.includes('{$value}'));
        {/foreach*}
        tmtpList = document.getElementsByName('timetype'+radical);
        for(var i = 0;i<tmtpList.length;i++){
            tmtpList[i].checked = (tmtpList[i].value == timetype);
        }
        getnewparams(radical);
    }
    
    function getnewparams(radical){
        period = document.getElementById('period'+radical).value;
        width = document.getElementById('width'+radical).value;
        //clear = document.getElementById('stat'+radical).checked;
        stats = '';
        {*foreach key = "key" item="value" from=$gui->status_radicals}
            temp = document.getElementById("{$key}"+radical);
            if(temp.checked)stats += temp.value;
        {/foreach*}
         cumulative = document.getElementById('cumulative'+radical).checked;
       timetype = document.querySelector('input[name="timetype'+radical+'"]:checked').value;
        setparams(radical,period,stats,width,cumulative,timetype);
    }
    
    function setparams(radical,period,clear,width,cumulative,timetype){
        graph = document.getElementById('imgtime'+radical);
        //graph.width = width;
        //div = document.getElementById('time'+radical);//console.log(div.style.width);//console.log(div.offsetWidth);
        //div.setAttribute("style","clear:both;resize:both;overflow: scroll;width:"+width+"px");
        params = graph.src.split('&');clear = "pfwbsotnx";
        params[2] = params[2].replace(params[2].split('=')[1],clear);
        params[3] = params[3].replace(params[3].split('=')[1],period);
        params[4] = (params[4].split('=')[1] == '')?params[4] + 1500: params[4].replace(params[4].split('=')[1],width);
        params[5] = params[5].replace(params[5].split('=')[1],cumulative);
        params[6] = params[6].replace(params[6].split('=')[1],timetype);
        str = params[0];
        for(var i = 1;i<params.length;i++){
            str+='&'+params[i];
        }
        graph.src = str;
    }
</script>
</body>

<style>
    .piechart>div{
    position:relative !important;
    }
    div.x-panel-tbar,div.x-grid3-header{
        display:none;
    }
    div.x-grid3-body{
        margin-top: 26px;
    }
    div.resultBox{
        margin:0px;
    
    }
</style>
</html>
<!-- termina aqui o metricsDashboard2.tpl-->