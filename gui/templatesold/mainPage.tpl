<!-- começa aqui o mainPage.tpl-->
{* 
 Testlink Open Source Project - http://testlink.sourceforge.net/ 
 @filesource  mainPage.tpl 
 Purpose: smarty template - main page / site map                 
*}
{$cfg_section=$smarty.template|replace:".tpl":""}
{config_load file="input_dimensions.conf" section=$cfg_section}
{include file="inc_head.tpl" popup="yes" openHead="yes"}
{include file="inc_ext_js.tpl"}
<script language="JavaScript" src="{$basehref}gui/niftycube/niftycube.js" type="text/javascript"></script>
<script type="text/javascript">
window.onload=function() 
{

  /* with typeof display_left_block_1 I'm checking is function exists */
  if(typeof display_left_block_top != 'undefined') 
  {
    display_left_block_top();
  }

  if(typeof display_left_block_1 != 'undefined') 
  {
    display_left_block_1();
  }

  if(typeof display_left_block_2 != 'undefined') 
  {
    display_left_block_2();
  }

  if(typeof display_left_block_3 != 'undefined') 
  {
    display_left_block_3();
  }

  if(typeof display_left_block_4 != 'undefined') 
  {
    display_left_block_4();
  }

  if(typeof display_left_block_bottom != 'undefined') 
  {
    display_left_block_bottom();
  }

  if(typeof display_left_block_5 != 'undefined')
  {
    display_left_block_5();
  }

  if( typeof display_right_block_1 != 'undefined')
  {
    display_right_block_1();
  }

  if( typeof display_right_block_2 != 'undefined')
  {
    display_right_block_2();
  }

  if( typeof display_right_block_3 != 'undefined')
  {
    display_right_block_3();
  }

  if( typeof display_right_block_top != 'undefined')
  {
    display_right_block_top();
  }

  if( typeof display_right_block_bottom != 'undefined')
  {
    display_right_block_bottom();
  }
}
</script>
<style>
body{
	background:transparent;
}
</style>
</head>

<body>
{if $gui->securityNotes}
  {include file="inc_msg_from_array.tpl" array_of_msg=$gui->securityNotes arg_css_class="warning"}
{/if}

{* ----- Right Column ------------- *}
{*include file="mainPageRight.tpl"*}
{*include file="mainPageBar.tpl"*}
{lang_get var="labels" s="log_level_AUDIT, btn_close"}
<div id ="metricas" class="col-xs-12" style="height: 100%">

<!--img src="{$smarty.const.TL_THEME_IMG_DIR}/loader.gif" id="loader" style="display : none;position:absolute;"-->
</div>
<input type = "button" value = "{$labels.log_level_AUDIT}"id = "vizualizar" onclick="showMetrics()"  style="top: 90%;position: absolute;left: 50%;margin-right: -50%;transform: translate(-50%, -50%);" ></input>
<script>
var showMetrics = function(){
	/*var gif = document.getElementById("loader");
	gif.style.display = 'inline';*/
	var frame = document.getElementById("metricas");
	frame.innerHTML = '<div><img id="circle" src = "{$smarty.const.TL_THEME_IMG_DIR}/circle.png" style ="position:absolute;top:50%;left: 50%;margin-right: -50%;transform: translate(-50%, -50%);"><img id="loader" src = "{$smarty.const.TL_THEME_IMG_DIR}/loader.gif" style ="position:absolute;top:50%;left: 50%;margin-right: -50%;transform: translate(-50%, -50%);"><iframe id="manageHelper" src="lib/results/metricsDashboard2.php" frameborder="0" scrolling="auto" allowtransparency="true" style="position:absolute;width:calc(100% - 30px);height: 100%;"></iframe></div>';
	//frame.style.cssText="height:100%;";
	var button = document.getElementById("vizualizar");
	//button.hidden = true;
	var close = function(){		
	button.value="{$labels.btn_close}";
	button.style.cssText="top: 10px;position: absolute;left: 95%;margin-right: -50%;transform: translate(-50%, 0%);";
	}
	close();
	var reclick = function(){
		frame.hidden=true;
		button.value = '{$labels.log_level_AUDIT}';
		button.style.cssText="top: 90%;position: absolute;left: 50%;margin-right: -50%;transform: translate(-50%, -50%);";
		button.onclick = function(){			
	        close();
			frame.hidden=false;
			button.onclick =reclick;
		};	
	};
	button.onclick = reclick;
	//frame.style.border = "1px solid black";
}
</script>

{* ----- Left Column -------------- *}
{*include file="mainPageLeft.tpl"*}
</body>
</html>
<!-- termina aqui o mainPage.tpl-->