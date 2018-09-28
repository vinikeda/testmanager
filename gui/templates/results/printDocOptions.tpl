{* 
TestLink Open Source Project - http://testlink.sourceforge.net/ 
@filesource printDocOptions.tpl
@internal revisions
@since 1.9.14
*}
{lang_get var="labels"
          s='doc_opt_title,doc_opt_guide,tr_td_show_as,check_uncheck_all_options,build,builds,onlywithuser,download,alt_attachment'}

{include file="inc_head.tpl" openHead="yes"}
{include file="inc_ext_js.tpl" bResetEXTCss=1}
{include file="inc_jsCheckboxes.tpl"}

<script type="text/javascript">
	  function downloadAttachments(id) {
	document.getElementById('formZip').elements.namedItem("id").value = id;
//	w = window.open('', 'FileDownload','width=510,height=300,resizeable,scrollbars'); 
//	document.getElementById('formZip').target = 'FileDownload'; 
//	w.focus(); 
	document.getElementById('formZip').submit();
}
</script>	
{if $gui->ajaxTree->loadFromChildren}
  <script type="text/javascript">
  /* space after { and before } to signal to smarty that is JS => do not process */
  treeCfg = { tree_div_id:'tree_div',root_name:"",root_id:0,root_href:"",
              loader:"", enableDD:false, dragDropBackEndUrl:'',children:"" };
  </script>
  <script type="text/javascript">
  treeCfg.root_name = '{$gui->ajaxTree->root_node->name|escape:'javascript'}';
  treeCfg.root_id = {$gui->ajaxTree->root_node->id};
  treeCfg.root_href = '{$gui->ajaxTree->root_node->href}';
  treeCfg.children = {$gui->ajaxTree->children}
  treeCfg.cookiePrefix = '{$gui->ajaxTree->cookiePrefix}';
  </script>
  <script type="text/javascript" src='gui/javascript/execTree.js'></script>

{else}
  <script type="text/javascript">

  treeCfg = { tree_div_id:'tree_div',root_name:"",root_id:0,root_href:"",
               loader:"", enableDD:false, dragDropBackEndUrl:'' };
  </script>
  <script type="text/javascript">
  treeCfg.loader = '{$gui->ajaxTree->loader}';
  treeCfg.root_name = '{$gui->ajaxTree->root_node->name|escape:'javascript'}';
  treeCfg.root_id = {$gui->ajaxTree->root_node->id};
  treeCfg.root_href = '{$gui->ajaxTree->root_node->href}';
  treeCfg.enableDD = '{$gui->ajaxTree->dragDrop->enabled}';
  treeCfg.dragDropBackEndUrl = '{$gui->ajaxTree->dragDrop->BackEndUrl}';
  treeCfg.cookiePrefix = '{$gui->ajaxTree->cookiePrefix}';
  </script>
  <script type="text/javascript" src='gui/javascript/treebyloader.js'></script>
{/if} 

{if $gui->buildInfoSet != ''}
<script>
jQuery( document ).ready(function() {
jQuery(".chosen-select").chosen({ width: "100%" });
});
</script>
{/if}

</head>

<body>
<form method="POST" action="lib/attachments/attachmentzip.php" enctype="multipart/form-data" id="formZip" onsubmit="return false">
	<input type="hidden" name="id"/>
</form> 	
<h1 class="title">{$gui->mainTitle} 
                  {if $gui->showHelpIcon}{include file="inc_help.tpl" helptopic="hlp_generateDocOptions" show_help_icon=true}{/if}
                </h1>

{* Control if options are visible *}
<div style="margin: 10px; {if !$gui->showOptions}display:none;{/if}" >

<form method="GET" id="printDocOptions" name="printDocOptions"
      action="lib/results/printDocument.php?type={$gui->doc_type}">

  <input type="hidden" name="docTestPlanId" value="{$docTestPlanId}" />
  <input type="hidden" name="toggle_memory" id="toggle_memory"  value="0" />
	 


  {if $gui->buildInfoSet != ''}
   <table>
    <tr>
     <td><label for="build"> {$labels.build}</label></td>
     <td style="width:100px"> 
      <select class="chosen-select" name="build_id" id="build_id" 
              data-placeholder="{$labels.builds}">
        {foreach key=build_id item=buildObj from=$gui->buildInfoSet}
          <option value="{$build_id}">{$buildObj.name|escape}</option>
        {/foreach}
      </select>
     </td>
     <td style="width:20px">&nbsp;</td>
     <td><label for="with_user_assignment">{$labels.onlywithuser}</label></td>
     <td><input type="checkbox" name="with_user_assignment" 
                id="with_user_assignment"></td>
	 <td style="width:20px">&nbsp;</td>
	 <td> <input type="button" style="border:none" id="attachments"
                       title="{$labels.alt_attachment}" value="{$labels.download}" alt="{$labels.alt_attachment}" 
                       onClick = "javascript:downloadAttachments(jQuery('#build_id').chosen().val());return false;"/></td>
    </tr>
   </table>
  {/if}

  
  <table class="smallGrey" id="optionsContainer" name="optionsContainer">
    {section name=number loop=$gui->outputOptions}
    <tr style="margin: 10px; {if !$gui->showOptionsCheckBoxes}display:none;{/if}">
      <td>{$gui->outputOptions[number].description}</td>
      <td>
        <input type="checkbox" name="{$gui->outputOptions[number].value}" id="cb{$gui->outputOptions[number].value}"
        {if $gui->outputOptions[number].checked == 'y'}checked="checked"{/if}/>
      </td>
    </tr>
    {/section}

    <tr style="margin: 10px;{if !$gui->showOptionsCheckBoxes}display:none;{/if}">
     <td><input type="button" id="toogleOptions" name="toogleOptions"
                onclick='cs_all_checkbox_in_div("optionsContainer","cb","toggle_memory");'
                value="{$labels.check_uncheck_all_options}" /> </td>
    </tr>

    
    <tr>
    {if $docType == 'testspec' || $docType == 'reqspec'}
      <td>{$labels.tr_td_show_as}</td>
      <td>
        <select id="format" name="format">
          {html_options options=$gui->outputFormat selected=$selFormat}
        </select>
      </td>
    {else}
      <td><input type="hidden" id="format" name="format" value="{$selFormat}" /></td>
    {/if}
    </tr>
  </table>
  <br> 
  <p>{$labels.doc_opt_guide}<br /></p>

</form>
</div>

<div id="tree_div" style="overflow:auto; height:100%;border:1px solid #c3daf9;"></div>
<div id ="loading" hidden>
	<!--img id="circle" src = "{$smarty.const.TL_THEME_IMG_DIR}/circle.png" -->
	<img id="loader" src = "{$smarty.const.TL_THEME_IMG_DIR}/loader.gif" style ="position:absolute;top:50%;left: 50%;margin-right: -50%;transform: translate(-50%, -50%);">
</div>
<script>
	//criando função de leitura de cookies, ela vai ser usada para verificar se o loading deve permanecer na tela.
	function getCookie(name) {
		var cookies = document.cookie;
		var prefix = name + "=";
		var begin = cookies.indexOf("; " + prefix);
	 
		if (begin == -1) {
	 
			begin = cookies.indexOf(prefix);
			 
			if (begin != 0) {
				return null;
			}
	 
		} else {
			begin += 2;
		}
	 
		var end = cookies.indexOf(";", begin);
		 
		if (end == -1) {
			end = cookies.length;                        
		}
	 
		return cookies.substring(begin + prefix.length, end);
	}

	window.onload = function (){ console.log("selFormat = "+{$selFormat}+'    -     Url = '+fRoot+menuUrl+"?level=testsuite&id="+3+args+"&"+tree_getPrintPreferences());
		jQuery(document).on('click', '.x-tree-node-anchor > span', function () {
			var loading = document.getElementById("loading");
			loading.hidden = false;
			document.cookie = "terminouDown=1";//criando cookie para identificar se o download já terminou(vai ser alterado pelo server)
			console.log(getCookie('terminouDown'));
			setInterval(
				function(){ console.log(getCookie('terminouDown'));
				var loading = document.getElementById("loading");
				loading.hidden = true;},1000);
		});
	};
</script>
</body>
</html>