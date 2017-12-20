<!-- começa aqui o mainPageLeft.tpl-->
{* 
 Testlink Open Source Project - http://testlink.sourceforge.net/ 
 @filesource  mainPageLeft.tpl
 Purpose: smarty template - main page / site map                 
                                                                 
 @internal revisions
 @since 1.9.15
*}
{*
esse arquivo foi criado com a fusão do mainPageLeft.tpl com o mainPageRight.tpl. portanto ele possui praticamente o todo dos dois. 
como este arquivo foi montado em cima do mainPageLeft, ele possui mais partes dele. 
Os trechos do ManiPageRight que foram colocados possuem comentarios de comeco e fim para facilitar sua identificação. 
durante a maior parte deste arquivo, o mainPageRight será referido como MPR para facilitar.
*}

{* houve uma fusão entre os lang_get do MPR e do left. foram retiradas as redundâncias para que isso fosse possível.*}
{lang_get var='labels' s='title_product_mgmt,href_tproject_management,href_admin_modules,
                          href_assign_user_roles,href_cfields_management,system_config,
                          href_cfields_tproject_assign,href_keywords_manage,
                          title_user_mgmt,href_user_management,
                          href_roles_management,title_requirements,
                          href_req_spec,href_req_assign,link_report_test_cases_created_per_user,
                          title_test_spec,href_edit_tc,href_browse_tc,href_search_tc,
                          href_search_req, href_search_req_spec,href_inventory,
                          href_platform_management, href_inventory_management,
                          href_print_tc,href_keywords_assign, href_req_overview,
                          href_print_req,title_plugins,title_documentation,href_issuetracker_management,
                          href_reqmgrsystem_management,href_req_monitor_overview,
						  current_test_plan,ok,testplan_role,msg_no_rights_for_tp,
						  title_test_execution,href_execute_test,href_rep_and_metrics,
						  href_update_tplan,href_newest_tcversions,
						  href_my_testcase_assignments,href_platform_assign,
						  href_tc_exec_assignment,href_plan_assign_urgency,
						  href_upd_mod_tc,title_test_plan_mgmt,title_test_case_suite,
						  href_plan_management,
						  href_build_new,href_plan_mstones,href_plan_define_priority,
						  href_metrics_dashboard,href_add_remove_test_cases'}

{$menuLayout=$tlCfg->gui->layoutMainPageLeft}
{* DO NOT GET CONFUSED this are SMARTY variables NOT JS *}
{$display_left_block_1=false}
{$display_left_block_2=false}
{$display_left_block_3=false}
{$display_left_block_4=false}
{$display_left_block_5=$tlCfg->userDocOnDesktop}
{$display_left_block_top = false}
{$display_left_block_bottom = false}

{if $gui->testprojectID && 
	(
		$gui->grants.project_edit == "yes" || 
		$gui->grants.tproject_user_role_assignment == "yes" ||
		$gui->grants.cfield_management == "yes" || 
		$gui->grants.platform_management == "yes" || 
		$gui->grants.keywords_view == "yes"
	)}
	{$display_left_block_1=true}
{/if}

{if $gui->testprojectID && ($gui->grants.cfield_management == "yes" || $gui->grants.issuetracker_management || $gui->grants.issuetracker_view)}
	{$display_left_block_2=true}
{/if}

{if $gui->testprojectID && $gui->opt_requirements == TRUE && ($gui->grants.reqs_view == "yes" || $gui->grants.reqs_edit == "yes")}
	{$display_left_block_3=true}
{/if}

{if $gui->testprojectID && $gui->grants.view_tc == "yes"}
	{$display_left_block_4=true}
{/if}

{if isset($gui->plugins.EVENT_LEFTMENU_TOP) &&  $gui->plugins.EVENT_LEFTMENU_TOP}
	{$display_left_block_top=true}
{/if}

{if isset($gui->plugins.EVENT_LEFTMENU_BOTTOM) &&  $gui->plugins.EVENT_LEFTMENU_BOTTOM}
	{$display_left_block_bottom=true}
{/if}

{*começo  cabeçalho do MPR*}

{$menuLayout=$tlCfg->gui->layoutMainPageRight}
{$display_right_block_1=false}
{$display_right_block_2=false}
{$display_right_block_3=false}
{$display_left_block_top = false}
{$display_left_block_bottom = false}

{if $gui->grants.testplan_planning == "yes" || $gui->grants.mgt_testplan_create == "yes" ||
	  $gui->grants.testplan_user_role_assignment == "yes" or $gui->grants.testplan_create_build == "yes"}
   {$display_right_block_1=true}
{/if}

{if $gui->countPlans > 0 && ($gui->grants.testplan_execute == "yes" || $gui->grants.testplan_metrics == "yes")}
   {$display_right_block_2=true}
{/if}

{if $gui->countPlans > 0 && $gui->grants.testplan_planning == "yes"}
   {$display_right_block_3=true}
{/if}

{$display_right_block_top=false}
{$display_right_block_bottom=false}

{if isset($gui->plugins.EVENT_RIGHTMENU_TOP) &&  $gui->plugins.EVENT_RIGHTMENU_TOP}
  {$display_right_block_top=true}
{/if}
{if isset($gui->plugins.EVENT_RIGHTMENU_BOTTOM) &&  $gui->plugins.EVENT_RIGHTMENU_BOTTOM}
  {$display_right_block_bottom=true}
{/if}
{*fim do cabeçalho do MPR*}	
<style>
.dropdown{
display:block !important;
}

.chosen-container-single .chosen-single {
	display: inline-block;
    width: auto;
    vertical-align: middle;
	height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
	box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
	font-family: inherit;
	text-transform: none;
	margin: 0;
    font: inherit;
	background: #f8f8f8;
	box-sizing: border-box;
	align-items: center;
    white-space: pre;
    -webkit-rtl-ordering: logical;
	cursor: default;
	text-rendering: auto;
	letter-spacing: normal;
    word-spacing: normal;
	text-indent: 0px;
    text-shadow: none;
	text-align: start;
	-webkit-writing-mode: horizontal-tb;
	-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
	box-sizing: border-box;
	font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-size: 14px;
    line-height: 1.42857143;
}
</style>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<!--div class="collapse navbar-collapse" id="MPnavbar"-->
			<ul class="nav nav-pills">
				{if $display_left_block_2}
					
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
							{$labels.system_config}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							{if $gui->grants.cfield_management == "yes"}
								<li><a href="lib/cfields/cfieldsView.php">{$labels.href_cfields_management}</a></li>
							{/if}
							 
							{if $gui->grants.issuetracker_management || $gui->grants.issuetracker_view}
								<li><a href="lib/issuetrackers/issueTrackerView.php">{$labels.href_issuetracker_management}</a></li>
							{/if}
						</ul>
					</li>
				{/if}
				{if $display_left_block_1}
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
							{$labels.title_product_mgmt}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							{if $gui->grants.project_edit == "yes"}
								<li><a href="lib/project/projectView.php">{$labels.href_tproject_management}</a></li>
							{/if}

							{if $gui->grants.tproject_user_role_assignment == "yes"}
								<li><a href="lib/usermanagement/usersAssign.php?featureType=testproject&amp;featureID={$gui->testprojectID}">{$labels.href_assign_user_roles}</a></li>
							{/if}

							{if $gui->grants.cfield_management == "yes"}
								<li><a href="lib/cfields/cfieldsTprojectAssign.php">{$labels.href_cfields_tproject_assign}</a></li>
							{/if}
						
							{if $gui->grants.keywords_view == "yes"}
								<li><a href="lib/keywords/keywordsView.php?tproject_id={$gui->testprojectID}">{$labels.href_keywords_manage}</a></li>
							{/if}
						
							{if $gui->grants.platform_management == "yes"}
								<li><a href="lib/platforms/platformsView.php">{$labels.href_platform_management}</a></li>
							{/if}

							{if $gui->grants.project_inventory_view}
								<li><a href="lib/inventory/inventoryView.php">{$labels.href_inventory}</a></li>
							{/if}
						</ul>
					</li>
				{/if}
				{if $display_left_block_4}
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
							{$labels.title_test_spec}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<li>
								<a href="{$gui->launcher}?feature=editTc">
									{if $gui->grants.modify_tc eq "yes"}
										{lang_get s='href_edit_tc'}
									{else}
										{lang_get s='href_browse_tc'}
									{/if}
								</a>
							</li>
							{*if $gui->hasTestCases}comentado pois é redundante, esse seria o search testCase
								<li>
									<a href="lib/testcases/tcSearch.php?doAction=userInput&tproject_id={$gui->testprojectID}">
										{$labels.href_search_tc}
									</a>
								</li>
							{/if*}    
					  
							{if $gui->hasKeywords}  
								{if $gui->grants.keywords_view == "yes"}
									{if $gui->grants.keywords_edit == "yes"}
										<li>
											<a href="{$gui->launcher}?feature=keywordsAssign">
												{$labels.href_keywords_assign}
											</a>
										</li>
									{/if}
								{/if}
							{/if}
					  
							{if $gui->grants.modify_tc eq "yes"}
								<li>
									<a href="lib/results/tcCreatedPerUserOnTestProject.php?do_action=uinput&tproject_id={$gui->testprojectID}">
										{$labels.link_report_test_cases_created_per_user}
									</a>
								</li>
							{/if}
						</ul>
					</li>
				{/if}
				{*if $display_left_block_3}comentado pois não será utilizado nessa versão em questão
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
							{$labels.title_requirements}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							{if $gui->grants.reqs_view == "yes"}
								<li><a href="{$gui->launcher}?feature=reqSpecMgmt">{$labels.href_req_spec}</a></li>
								<li><a href="lib/requirements/reqOverview.php">{$labels.href_req_overview}</a></li>
								<li><a href="{$gui->launcher}?feature=searchReq">{$labels.href_search_req}</a></li>
								<li><a href="{$gui->launcher}?feature=searchReqSpec">{$labels.href_search_req_spec}</a></li>
							{/if}
							{if $gui->grants.reqs_edit == "yes"}
								<li><a href="lib/general/frmWorkArea.php?feature=assignReqs">{$labels.href_req_assign}</a></li>
								<li><a href="lib/requirements/reqMonitorOverview.php?tproject_id={$gui->testprojectID}">{$labels.href_req_monitor_overview}</a></li>
								<li><a href="{$gui->launcher}?feature=printReqSpec">{$labels.href_print_req}</a></li>
							{/if}
						</ul>
					</li>
				{/if*}
				{if $display_left_block_top}
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
							{$labels.title_plugins}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							{if isset($gui->plugins.EVENT_LEFTMENU_TOP)}
								<div id="plugin_left_top">
									{foreach from=$gui->plugins.EVENT_LEFTMENU_TOP item=menu_item}
										{$menu_item}
									{/foreach}
								</div>
							{/if}
						</ul>
					</li>
				{/if}
				{if $display_left_block_bottom}
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
							{$labels.title_plugins}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							{if isset($gui->plugins.EVENT_LEFTMENU_BOTTOM)}
								<div id="plugin_left_bottom">
									{foreach from=$gui->plugins.EVENT_LEFTMENU_BOTTOM item=menu_item}
										{$menu_item}
										<br />
									{/foreach}
								</div>
							{/if}
						</ul>
					</li>
				{/if}
				{if $gui->num_active_tplans > 0}
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
							{$labels.title_test_plan_mgmt}{*include file="inc_help.tpl" helptopic="hlp_testPlan" show_help_icon=true  help padrão do destlink. desabilitado por solicitação do caio
							inc_help_alt="$text_hint" inc_help_title="$text_hint"  
							inc_help_style="float: right;vertical-align: top;"*}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							{if $display_right_block_1}
								{if $gui->grants.mgt_testplan_create == "yes"}
									<li><a href="lib/plan/planView.php">{$labels.href_plan_management}</a></li>
								{/if}
								{if $gui->grants.testplan_create_build == "yes" and $gui->countPlans > 0}
									<li><a href="lib/plan/buildView.php?tplan_id={$gui->testplanID}">{$labels.href_build_new}</a></li>
								{/if}
									
								{if $gui->grants.testplan_milestone_overview == "yes" and $gui->countPlans > 0}
									<li><a href="lib/plan/planMilestonesView.php">{$labels.href_plan_mstones}</a></li>
								{/if}
							{/if}
						</ul>
					</li>
				{/if}
				{if $display_right_block_2}
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
							{$labels.title_test_execution}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							{if $gui->grants.testplan_execute == "yes"}
								<li><a href="{$gui->launcher}?feature=executeTest">{$labels.href_execute_test}</a></li>

								{if $gui->grants.exec_testcases_assigned_to_me == "yes"}
									<li><a href="{$gui->url.testcase_assignments}">{$labels.href_my_testcase_assignments}</a></li>
								{/if} 
							{/if} 

							{if $gui->grants.testplan_metrics == "yes"}
								<li><a href="{$gui->launcher}?feature=showMetrics">{$labels.href_rep_and_metrics}</a></li>
								<li><a href="{$gui->url.metrics_dashboard}">{$labels.href_metrics_dashboard}</a></li>
							{/if}
						</ul>
					</li>
				{/if}
				{if $display_right_block_3}
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
							{$labels.title_test_case_suite}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							{if $gui->grants.testplan_add_remove_platforms == "yes"}
								<!--li><a href="lib/platforms/platformsAssign.php?tplan_id={$gui->testplanID}">{$labels.href_platform_assign}</a></li-->
							{/if}
							
							<li><a href="{$gui->launcher}?feature=planAddTC">{$labels.href_add_remove_test_cases}</a><li>

							<li><a href="{$gui->launcher}?feature=tc_exec_assignment">{$labels.href_tc_exec_assignment}</a></li>
								
							{if $session['testprojectOptions']->testPriorityEnabled && 
								$gui->grants.testplan_set_urgent_testcases == "yes"}
								<li><a href="{$gui->launcher}?feature=test_urgency">{$labels.href_plan_assign_urgency}</a></li>
							{/if}

							{if $gui->grants.testplan_update_linked_testcase_versions == "yes"}
								<li><a href="{$gui->launcher}?feature=planUpdateTC">{$labels.href_update_tplan}</a></li>
							{/if} 

							{if $gui->grants.testplan_show_testcases_newest_versions == "yes"}
								<li><a href="{$gui->launcher}?feature=newest_tcversions">{$labels.href_newest_tcversions}</a></li>
							{/if}
						</ul>
					</li>
				{/if}
				{if $display_right_block_top}
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
							{$labels.title_plugins}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							{if isset($gui->plugins.EVENT_RIGHTMENU_TOP)}
								<div id="plugin_right_top">
									{foreach from=$gui->plugins.EVENT_RIGHTMENU_TOP item=menu_item}
										{$menu_item}
										<br/>
									{/foreach}
								</div>
							{/if}
						</ul>
					</li>
				{/if}
				{if $display_right_block_bottom}
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
							{$labels.title_plugins}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							{if isset($gui->plugins.EVENT_RIGHTMENU_BOTTOM)}
								<div id="plugin_right_bottom">
									{foreach from=$gui->plugins.EVENT_RIGHTMENU_BOTTOM item=menu_item}
										{$menu_item}
										<br/>
									{/foreach}
								</div>
							{/if}
						</ul>
					</li>
				{/if}
				{if $gui->num_active_tplans > 0}
					{lang_get s='help' var='common_prefix'}
					{lang_get s='test_plan' var="xx_alt"}
					{$text_hint="$common_prefix: $xx_alt"}
					<form style="/*margin-left:0px;*/" class="navbar-form navbar-right" name="testplanForm" action="lib/general/mainPage.php">
						{if $gui->countPlans > 0}
							<div style="/*margin-left:0px; margin-bottom:0px;*/" class="form-group">
								{$labels.title_test_plan_mgmt}{*$labels.current_test_plan*}:
								<select class="form-control chosen-select" id="select" name="testplan" onchange="this.form.submit();">
									{section name=tPlan loop=$gui->arrPlans}
										<option value="{$gui->arrPlans[tPlan].id}"
											{if $gui->arrPlans[tPlan].selected} selected="selected" {/if}
											title="{$gui->arrPlans[tPlan].name|escape}">{$gui->arrPlans[tPlan].name|escape}
										</option>
									{/section}
								</select>
							</div>
							{if $gui->countPlans == 1}
								<input type="button" onclick="this.form.submit();" value="{$labels.ok}"/>
							{/if}
							{if $gui->testplanRole neq null}
								{$labels.testplan_role} {$gui->testplanRole|escape}
							{/if}
						{else}
							{if $gui->num_active_tplans > 0}{$labels.msg_no_rights_for_tp}{/if}
						{/if}
					</form>
				{/if}
			</ul>
			
		<!--/div-->
	</div>
</nav>
<script>
jQuery( document ).ready(function() {
jQuery(".chosen-select").chosen({ width: "auto" });
});
</script>
<!-- termina aqui o mainPageLeft.tpl-->