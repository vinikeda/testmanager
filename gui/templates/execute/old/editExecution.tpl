<!-- comeca aqui o editExecution.tpl -->
{* 
TestLink Open Source Project - http://testlink.sourceforge.net/
@filesource editExecution.tpl
@author   francisco.mancardi@gmail.com


*}
{include file="inc_head.tpl" openHead='yes' editorType=$gui->editorType}
{include file="inc_ext_js.tpl"}
<script>
	var observe;
	if (window.attachEvent) {
		observe = function (element, event, handler) {
			element.attachEvent('on'+event, handler);
		};
	}
	else {
		observe = function (element, event, handler) {
			element.addEventListener(event, handler, false);
		};
	}
	function init (text) {
		//var text = document.getElementById('text');
		function resize () {
			text.style.height = 'auto';
			text.style.height = text.scrollHeight+'px';
		}
		/* 0-timeout to get the already changed text */
		function delayedResize () {
			window.setTimeout(resize, 0);
		}
		observe(text, 'change',  resize);
		observe(text, 'cut',     delayedResize);
		observe(text, 'paste',   delayedResize);
		observe(text, 'drop',    delayedResize);
		observe(text, 'keydown', delayedResize);

		text.focus();
		text.select();
		resize();
	}
</script> 

</head>

<body onUnload="storeWindowSize('ExecEditPopup')">
<h1 class="title">{lang_get s='title_execution_notes'}</h1>
<div class="workBack">
  <form method="post">
    {* memory *}
    <input type="hidden" name="tplan_id" value="{$gui->tplan_id}">
    <input type="hidden" name="tproject_id" value="{$gui->tproject_id}">
    <input type="hidden" name="exec_id" value="{$gui->exec_id}">
    <input type="hidden" name="tcversion_id" value="{$gui->tcversion_id}">
	<table class="simple">
		{include file="execute/EditStepNotes.tpl"}
		
	</table>
	<div id="cfields_exec_time_tcversionid_{$tcversion_id}" class="custom_field_container" 
		 style="background-color:#dddddd;">
		 <!--começa div-->
			{$gui->cfields_exec}
			<!--acaba div-->
	</div>
    <div class="container-fluid" id ="sexta">
	<div class="row">
        {$gui->notes}
		<script>
		init(document.getElementById('notes'));
		document.getElementById('notes').classList.add('col-sm-10');
		</script>
		
	<div class="col-sm-2"{*width = "138"*} style="float:right">
		{include file="execute/editStatus.tpl"}
		<div class="groupBtn" style = "float:right;padding-right:12px">
		  <input type="hidden" name="doAction" value="doUpdate" />
		  <input type="submit" value="{lang_get s='btn_save'}" />
		  <input type="button" value="{lang_get s='btn_close'}" onclick="window.close()" />
		</div>
	</div>
	</div>
	</div>
  </form>
</div>


</body>
</html>    

<!-- termina aqui o editExecution.tpl -->