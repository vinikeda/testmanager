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
             title_edit_personal_data,th_tcid,title_test_plan_mgmt,link_logout,title_admin,
             search_testcase,title_results,title_user_mgmt"}
{$cfg_section=$smarty.template|replace:".tpl":""}
{config_load file="input_dimensions.conf" section=$cfg_section}

<nav id ="mainnavbar" class="navbar navbar-default" style ="margin-bottom:0px;">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand logo-navbar"  href="index.php" target="_parent">
				<img style="height:100%;"class="image-responsive" alt="Company logo" title="logo" src="{$smarty.const.TL_THEME_IMG_DIR}{$tlCfg->logo_login}{*{$tlCfg->logo_navbar}*}" />
			</a>
		</div>	
		<div class="collapse navbar-collapse" id="main-nav" >
		
			{include file="mainNavBar.tpl"}
		
		</div>
	</div>	
</nav>
				
				
				
				
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
						
						<span class="btn btn-default  glyphicon glyphicon-search" aria-hidden="true" title="{$labels.search_testcase}" alt="{$labels.search_testcase}"
							onclick="document.getElementById('searchTC').submit()" class="clickable"  > </span>
						<input class="form-control" type="hidden" name="edit" value="testcase"/>
						<input class="form-control" type="hidden" name="allow_edit" value="0"/>
					</div>
				</form>
				{/if}
				{/if*}
				
				
				
{if $gui->updateMainPage == 1}
  <script type="text/javascript">
  document.getElementById('mainframe').location = "{$basehref}lib/general/mainPage.php";
  </script>
{/if}

<!-- termina aqui o navBar.tpl-->