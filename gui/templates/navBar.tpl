<!-- começa aqui o navBar.tpl-->
{*
Testlink Open Source Project - http://testlink.sourceforge.net/

title bar + menu

@filesource navBar.tpl
@internal revisions
@since 1.9.7

*}
{lang_get var="labels"
          s="title_events,event_viewer,home,testproject,title_specification,title_execute,
             title_edit_personal_data,th_tcid,link_logout,title_admin,
             search_testcase,title_results,title_user_mgmt"}
{$cfg_section=$smarty.template|replace:".tpl":""}
{config_load file="input_dimensions.conf" section=$cfg_section}

{include file="inc_head.tpl" openHead="yes"}
</head>
<body style="min-width: 800px;">
<!--div style="float:left; height: 100%;padding:0.5%">
  </div-->
  
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand logo-navbar"  href="index.php" target="_parent">
			<img width = 240  class="image-responsive" alt="Company logo" title="logo" src="{$smarty.const.TL_THEME_IMG_DIR}{$tlCfg->logo_login}{*{$tlCfg->logo_navbar}*}" /></a>
		</div>	
		<div class="collapse navbar-collapse" id="main-nav" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav" >
			
			<!--inicio da parte gerada no initTopMenu no meio do common.php-->
			{$session.testprojectTopMenu}
			<!--fim da parte gerada no initTopMenu no meio do common.php-->
			</ul>
			
			{if $gui->TestProjects != ""}
			<form class="navbar-form navbar-right" style="/*display:inline*/" name="productForm" action="lib/general/navBar.php?viewer={$gui->viewer}" method="get">
				<div class="form-group">
					<label for="testproject" id="testproject-label">{$labels.testproject}</label>
					<select id="testproject" class="form-control" style="/*font-size: 80%;position:relative; top:-1px;*/" name="testproject" onchange="this.form.submit();">
						{foreach key=tproject_id item=tproject_name from=$gui->TestProjects}
						<option {*class="highlighted"*} value="{$tproject_id}" title="{$tproject_name|escape}"
						{if $tproject_id == $gui->tprojectID} selected="selected" {/if}>
						{$tproject_name|truncate:#TESTPROJECT_TRUNCATE_SIZE#|escape}</option>
						{/foreach}
					</select>
				</div>
			</form>
  
			{/if}
			{*if $gui->tprojectID}
			{if $gui->grants->view_testcase_spec == "yes"}
			<form class="navbar-form navbar-right" target="mainframe" name="searchTC" id="searchTC"
				action="lib/testcases/archiveData.php" method="get">
				<div class="form-group">
					<input class="form-control" style="/*font-size: 80%; position:relative; top:-1px;*/" type="text" size="{$gui->searchSize}"
						title="{$labels.search_testcase}" name="targetTestCase" value="{$gui->tcasePrefix}"/>
					{* useful to avoid a call to method to get test case prefix in called page }
					<input type="hidden" id="tcasePrefix" name="tcasePrefix" value="{$gui->tcasePrefix}" />
					{* Give a hint to archiveData, will make logic simpler to understand }
					<input class="form-control" type="hidden" id="caller" name="caller" value="navBar" />
					<!--img class="btn btn-default" src="{$tlImages.magnifier}"
						title="{$labels.search_testcase}" alt="{$labels.search_testcase}"
						onclick="document.getElementById('searchTC').submit()" class="clickable" 
						style="/*position:relative; top:2px;*/" /-->
					<span class="btn btn-default  glyphicon glyphicon-search" aria-hidden="true" title="{$labels.search_testcase}" alt="{$labels.search_testcase}"
						onclick="document.getElementById('searchTC').submit()" class="clickable"  > </span>
					<input class="form-control" type="hidden" name="edit" value="testcase"/>
					<input class="form-control" type="hidden" name="allow_edit" value="0"/>
				</div>
			</form>
			{/if}
			{/if*}
			<ul class="navbar-right nav navbar-nav">
			<li>
					<!--span class="bold"-->
					<!--div class="form-control"-->
					<a><!--uma gambeta para o nome do usuário pegar a formatação que eu quero.-->
						{$gui->whoami|escape}
					</a>
				</li>
				<li>
					<a href='lib/usermanagement/userInfo.php' target="mainframe" accesskey="i"
						tabindex="6"><span class="glyphicon glyphicon-cog" title="{$labels.title_edit_personal_data}" aria-hidden="true"></span><!--img src="{$tlImages.account}" title="{$labels.title_edit_personal_data}"--></a>
				</li>
				<li>
					<a href="logout.php?viewer={$gui->viewer}" target="_parent" accesskey="q">
						<!--img src="{$tlImages.logout}" title="{$labels.link_logout}"-->
						logout
					</a>
				</li>
					<!--/span-->
					<!--/div-->
			</ul>
				
			
				
		</div>
	</div>
</nav>



{if $gui->updateMainPage == 1}
  <script type="text/javascript">
  parent.mainframe.location = "{$basehref}lib/general/mainPage.php";
  </script>
{/if}

</body>
</html>
<!-- termina aqui o navBar.tpl-->