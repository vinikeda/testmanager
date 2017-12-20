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
<!--
	<script type="text/javascript">

	</script>
-->
</head>

<body style="background-image: url('http://200.204.163.104:8082/testlink/gui/themes/default/images/argowall.png'); background-image: no-repeat; background-position: 50% 40%;" >
{if $gui->securityNotes}
  {include file="inc_msg_from_array.tpl" array_of_msg=$gui->securityNotes arg_css_class="warning"}
{/if}
	<div align="center" style="width: 16%; position: absolute; top: 30%; left: 40%; background-color: #EEE;">
		<p> <b> AGTESTLINK - SISTEMA DE INVENTÁRIO </b> </p>
	</div>
	<div style="height: 20%; width: 16%; position: absolute; top: 35%; left: 40%; background-color: #EEE;">
		<form action="">
		  <input type="radio" name="agt_log_operation_opt" value="agt_log_register"> CADASTRO <br>
		  <input type="radio" name="agt_log_operation_opt" value="agt_log_withdraw"> RETIRADA <br>
		  <input type="radio" name="agt_log_operation_opt" value="agt_log_return"> DEVOLUÇÃO <br>
		  <input type="radio" name="agt_log_operation_opt" value="agt_log_search"> CONSULTA <br>
		  <input type="radio" name="agt_log_operation_opt" value="agt_log_remove"> REMOÇÃO <br>
		  <br>
		  <br>
		  <br>
		  <br>
			<div align="right">
				<input type="submit" align="right" value="SELECIONAR">
			</div>
		</form>
	</div>
</body>
</html>