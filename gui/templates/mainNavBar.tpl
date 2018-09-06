<!-- começa aqui o mainNavBar.tpl-->
{* 
 Testlink Open Source Project - http://testlink.sourceforge.net/ 
 @filesource  mainPageLeft.tpl
 Purpose: smarty template - main page / site map                 
                                                                 
 @internal revisions
 @since 1.9.15
*}
{*

esse arquivo foi criado a partir de altrações no mainPageBar.tpl para que ele seja gerado a partir do NavBar.tpl ao invés de ser gerado pelo MainPage.tpl.
essas alterações fazem com que aqui não se tenha mais a necessidade de gerar uma nav inteira, só parte de seus componentes.os itens foram reorganizados e
se fundirão com aqueles que eram gerados pelo initTopMenu() no common.php. a ordem também foi alterada.

segue abaixo a documentação do que era o MainPageBar.tpl

esse arquivo foi criado com a fusão do mainPageLeft.tpl com o mainPageRight.tpl. portanto ele possui praticamente o todo dos dois. 
como este arquivo foi montado em cima do mainPageLeft, ele possui mais partes dele. 
A maior parte dos trechos do ManiPageRight que foram colocados possuem comentarios de comeco e fim para facilitar sua identificação. 
durante a maior parte deste arquivo, o mainPageRight será referido como MPR para facilitar.
*}

{* houve uma fusão entre os lang_get do MPR e do left. foram retiradas as redundâncias para que isso fosse possível.*}
{lang_get var='labels' s='title_product_mgmt,href_tproject_management,href_admin_modules,
                          href_assign_user_roles,href_cfields_management,system_config,
                          href_cfields_tproject_assign,href_keywords_manage,
                          testproject,title_user_mgmt,href_user_management,
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
                          href_plan_management,href_sub_aquire_management,
                          href_build_new,href_plan_mstones,href_plan_define_priority,
                          href_manage_issues,href_manage_issues_categories,href_manage_issues_markers,
                          href_complete_monitoring_report,href_quick_monitoring_report,href_history_report,macros_mgmt,
                          href_metrics_dashboard,th_user,title_results,href_add_remove_test_cases,title_edit_personal_data,th_user_rights'}

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

{*
COMEÇA PELOS DELECTS DE PLANO DE TESTE, ADQUIRENTE, E PROJETO DE TESTE
*}
<div style="float:right;margin-top:-10px">
	<style>
		.spaced{
			padding-top:7px;
			padding-bottom:2px;
		}
	</style>
	<div style="list-style-type:none;color: #777;text-align:right;float:left;">
		<li class = "spaced"><label for="testproject" id="testproject-label" >{$labels.testproject}:</label></li>
		<li class = "spaced"><label id="testplan-label" for="select_chosen">{$labels.title_test_plan_mgmt}{*$labels.current_test_plan*}:</label></li>
	</div>
	<div style="float:left;" class="container-fluid">					
		{if $gui->TestProjects != ""}
			<form class="form-inline" name="productForm" action="index.php?viewer={$gui->viewer}" method="post">
				<select id="testproject" class="form-control" name="testproject" onchange="this.form.submit();" style="width:100%;">
					{foreach key=tproject_id item=tproject_name from=$gui->TestProjects}
						<option {*class="highlighted"*} value="{$tproject_id}" title="{$tproject_name|escape}"
							{if $tproject_id == $gui->tprojectID} selected="selected" {/if}>
							{$tproject_name|truncate:#TESTPROJECT_TRUNCATE_SIZE#|escape}
						</option>
					{/foreach}
				</select>
			</form>
		{/if}
		
		{if $gui->num_active_tplans > 0}		
			{lang_get s='help' var='common_prefix'}
			{lang_get s='test_plan' var="xx_alt"}
			{$text_hint="$common_prefix: $xx_alt"}
			<form class="form-inline" name="testplanForm" id="testplanForm" action="index.php?" method="get">
				{if $gui->countPlans > 0}
					<select class="chosen-select form-control" id="select" name="testplan" onchange="this.form.submit();">
						{section name=tPlan loop=$gui->arrPlans}
							<option value="{$gui->arrPlans[tPlan].id}"
								{if $gui->arrPlans[tPlan].selected} selected="selected" {/if}
								title="{$gui->arrPlans[tPlan].name|escape}">{$gui->arrPlans[tPlan].name|escape}
							</option>
						{/section}
					</select>
					{if $gui->countPlans == 1}
						<input type="button" onclick="this.form.submit();" value="{$labels.ok}"/>
					{/if}
				{else}
					{if $gui->num_active_tplans > 0}{$labels.msg_no_rights_for_tp}{/if}
				{/if}
			</form>
	
			<script>
				jQuery( document ).ready(function() {
					jQuery(".chosen-select").chosen({ width: '100%'});
				});
			</script>
			<style>
				.chosen-single{
					width:/*calc(100% - 18px)*/100% !important;
				}
			</style>
		{/if}
	</div>
</div>


<ul class="nav navbar-nav" >
	
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
			{$labels.th_user}<span class="caret">
		</a>
		<ul class="dropdown-menu">
			<li>
				<a>{*$gui->whoami|escape*}{$gui->userName}</a>
			</li>
			<li>
				<a>{$gui->userRole}</a>
			</li>
			<li role="separator" class="divider"></li>
                        {if $display_right_block_2}
                            {if $gui->grants.testplan_execute == "yes"}
                                {if $gui->grants.exec_testcases_assigned_to_me == "yes"}
                                    <li><a href="{$gui->url.testcase_assignments}" target="mainframe">{$labels.href_my_testcase_assignments}</a></li>
                                {/if}
                            {/if}
                        {/if}
			<li>
				<a href='lib/usermanagement/userInfo.php' target="mainframe" accesskey="i" tabindex="6">
					{$labels.title_edit_personal_data}
					<!--img src="{$tlImages.account}" title="{$labels.title_edit_personal_data}"-->
				</a>
			</li>
			<li>
				<a href="logout.php?viewer={$gui->viewer}" target="_parent" accesskey="q" >
					Logout
				</a>
			</li>
		</ul>
	</li>
</ul>
	
{if $display_left_block_2 || $display_left_block_1}
	<ul class="nav navbar-nav" >
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
				{$labels.system_config}
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
                            {if $display_left_block_2}
				{if $gui->grants.cfield_management == "yes"}
					<li><a href="lib/cfields/cfieldsView.php" target="mainframe">{$labels.href_cfields_management}</a></li>
				{/if}
                            {/if}
                                {if $display_left_block_1}
                                    {if $gui->grants.cfield_management == "yes"}
                                            <li><a href="lib/cfields/cfieldsTprojectAssign.php" target="mainframe">{$labels.href_cfields_tproject_assign}</a></li>
                                    {/if}

                                    {if $gui->grants.keywords_view == "yes"}
                                            <li><a href="lib/keywords/keywordsView.php?tproject_id={$gui->testprojectID}" target="mainframe">{$labels.href_keywords_manage}</a></li>
                                    {/if}

                                    {if $gui->grants.platform_management == "yes"}
                                            <li><a href="lib/platforms/platformsView.php" target="mainframe">{$labels.href_platform_management}</a></li>
                                    {/if}
                                {/if}
                            {if $display_left_block_2}
				{if $gui->grants.issuetracker_management || $gui->grants.issuetracker_view}
					<li><a href="lib/issuetrackers/issueTrackerView.php" target="mainframe">{$labels.href_issuetracker_management}</a></li>
				{/if}
					{if isset($session.testprojectTopMenu2.title_admin)}{$session.testprojectTopMenu2.title_admin}{/if}
					{if isset($session.testprojectTopMenu2.title_events)}{$session.testprojectTopMenu2.title_events}{/if}
                            {/if}
			</ul>
		</li>
	</ul>
{/if}
{if $gui->role == 8 ||$gui->role == 11 || $gui->role == 13 || $gui->grants.tproject_user_role_assignment == "yes" || ($gui->grants.testplan_milestone_overview == "yes" and $gui->countPlans > 0)}
    <ul class="nav navbar-nav" >
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
                Quality Assurance
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                {if $gui->role == 8 ||$gui->role == 11 || $gui->role == 13}
                    <li><a href="lib/macros/macrosView.php" target="mainframe">{$labels.macros_mgmt}</a></li>
                {/if}
                {if $gui->grants.tproject_user_role_assignment == "yes"}
                    <li><a href="lib/usermanagement/usersAssign.php?featureType=testproject&amp;featureID={$gui->testprojectID}" target="mainframe">{$labels.href_assign_user_roles}</a></li>
                {/if}
                {if $gui->grants.testplan_milestone_overview == "yes" and $gui->countPlans > 0}
					<li><a href="lib/plan/planMilestonesView.php" target="mainframe">{$labels.href_plan_mstones}</a></li>
		{/if}
            </ul>
        </li>
    </ul>
{/if}
{if $gui->role == 8 ||$gui->role == 11 || $gui->role == 13 || $gui->role == 9|| $gui->role == 14}
    <ul class="nav navbar-nav" >
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
                Issue Manager
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="lib/issue/issuesView.php" target="mainframe">
                        {$labels.href_manage_issues}
                    </a>
                </li>
                <li>
                    <a href="lib/issue/CategoriesView.php" target="mainframe">
                        {$labels.href_manage_issues_categories}
                    </a>
                </li>
                <li>
                    <a href="lib/issue/MarkersView.php" target="mainframe">
                        {$labels.href_manage_issues_markers}
                    </a>
                </li>
            </ul>
        </li>
    </ul>
{/if}
{if $display_left_block_4 || $gui->grants.testplan_update_linked_testcase_versions == "yes" || $gui->grants.testplan_show_testcases_newest_versions == "yes"}
	<ul class="nav navbar-nav" >
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
				{$labels.title_test_spec}
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
                            {if $display_left_block_4}
				<li>
					<a href="{$gui->launcher}?feature=editTc" target="mainframe">
						{if $gui->grants.modify_tc eq "yes"}
							{lang_get s='href_edit_tc'}
						{else}
							{lang_get s='href_browse_tc'}
						{/if}
					</a>
				</li>
				{*if $gui->hasTestCases}comentado pois é redundante, esse seria o search testCase
				<li>
					<a href="lib/testcases/tcSearch.php?doAction=userInput&tproject_id={$gui->testprojectID}" target="mainframe">
						{$labels.href_search_tc}
					</a>
				</li>
				{/if*}    
				{if $gui->hasKeywords}  
                                    {if $gui->grants.keywords_view == "yes"}
                                        {if $gui->grants.keywords_edit == "yes"}
                                            <li>
                                                <a href="{$gui->launcher}?feature=keywordsAssign" target="mainframe">
                                                    {$labels.href_keywords_assign}
                                                </a>
                                            </li>
                                        {/if}
                                    {/if}
				{/if}
					  
				{if $gui->grants.modify_tc eq "yes"}
					<li>
						<a href="lib/results/tcCreatedPerUserOnTestProject.php?do_action=uinput&tproject_id={$gui->testprojectID}" target="mainframe">
							{$labels.link_report_test_cases_created_per_user}
						</a>
					</li>
				{/if}
                                {/if}
                                {if $gui->grants.testplan_update_linked_testcase_versions == "yes"}
					<li><a href="{$gui->launcher}?feature=planUpdateTC" target="mainframe">{$labels.href_update_tplan}</a></li>
				{/if}
                                {if $gui->grants.testplan_show_testcases_newest_versions == "yes"}
					<li><a href="{$gui->launcher}?feature=newest_tcversions" target="mainframe">{$labels.href_newest_tcversions}</a></li>
				{/if}
			</ul>
		</li>
	</ul>
{/if}
{if $display_left_block_1}
	<ul class="nav navbar-nav" >
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
				{$labels.title_product_mgmt}
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				{if $gui->grants.project_edit == "yes"}
					<li><a href="lib/project/projectView.php" target="mainframe">{$labels.href_tproject_management}</a></li>
					<li><a href="lib/subadiq/subadiqView.php" target="mainframe">{$labels.href_sub_aquire_management}</a></li>
				{/if}

				{*if $gui->grants.tproject_user_role_assignment == "yes"}
					<li><a href="lib/usermanagement/usersAssign.php?featureType=testproject&amp;featureID={$gui->testprojectID}" target="mainframe">{$labels.href_assign_user_roles}</a></li>
				{/if}

				{if $gui->grants.cfield_management == "yes"}
					<li><a href="lib/cfields/cfieldsTprojectAssign.php" target="mainframe">{$labels.href_cfields_tproject_assign}</a></li>
				{/if}
			
				{if $gui->grants.keywords_view == "yes"}
					<li><a href="lib/keywords/keywordsView.php?tproject_id={$gui->testprojectID}" target="mainframe">{$labels.href_keywords_manage}</a></li>
				{/if}
			
				{if $gui->grants.platform_management == "yes"}
					<li><a href="lib/platforms/platformsView.php" target="mainframe">{$labels.href_platform_management}</a></li>
				{/if}

				{if $gui->grants.project_inventory_view}
					<li><a href="lib/inventory/inventoryView.php" target="mainframe">{$labels.href_inventory}</a></li>
				{/if*}
			</ul>
		</li>
	</ul>
{/if}
{*if $display_left_block_3}comentado pois não será utilizado nessa versão em questão
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
		{$labels.title_requirements}
		<span class="caret"></span>
	</a>
	<ul class="dropdown-menu">
		{if $gui->grants.reqs_view == "yes"}
			<li><a href="{$gui->launcher}?feature=reqSpecMgmt" target="mainframe">{$labels.href_req_spec}</a></li>
			<li><a href="lib/requirements/reqOverview.php" target="mainframe">{$labels.href_req_overview}</a></li>
			<li><a href="{$gui->launcher}?feature=searchReq" target="mainframe">{$labels.href_search_req}</a></li>
			<li><a href="{$gui->launcher}?feature=searchReqSpec" target="mainframe">{$labels.href_search_req_spec}</a></li>
		{/if}
		{if $gui->grants.reqs_edit == "yes"}
			<li><a href="lib/general/frmWorkArea.php?feature=assignReqs" target="mainframe">{$labels.href_req_assign}</a></li>
			<li><a href="lib/requirements/reqMonitorOverview.php?tproject_id={$gui->testprojectID}" target="mainframe">{$labels.href_req_monitor_overview}</a></li>
			<li><a href="{$gui->launcher}?feature=printReqSpec" target="mainframe">{$labels.href_print_req}</a></li>
		{/if}
	</ul>
</li>
{/if*}
{if $display_left_block_top}
	<ul class="nav navbar-nav" >
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
	</ul>
{/if}
{if $display_left_block_bottom}
	<ul class="nav navbar-nav" >
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
	</ul>
{/if}
				
{if $gui->num_active_tplans > 0 and ($gui->grants.mgt_testplan_create == "yes" or $display_right_block_3)}
<ul class="nav navbar-nav" >
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
					<li><a href="lib/plan/planView.php" target="mainframe">{$labels.href_plan_management}</a></li>
				{/if}
				
					
				{*if $gui->grants.testplan_milestone_overview == "yes" and $gui->countPlans > 0}
					<li><a href="lib/plan/planMilestonesView.php" target="mainframe">{$labels.href_plan_mstones}</a></li>
				{/if*}
			{/if}
			
			{if $display_right_block_3}
				<!--li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
						{$labels.title_test_case_suite}
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu"-->
				{if $gui->grants.testplan_add_remove_platforms == "yes"}
					<!--li><a href="lib/platforms/platformsAssign.php?tplan_id={$gui->testplanID}">{$labels.href_platform_assign}</a></li-->
				{/if}
				
				<li><a href="{$gui->launcher}?feature=planAddTC" target="mainframe">{$labels.href_add_remove_test_cases}</a></li>

				<li><a href="{$gui->launcher}?feature=tc_exec_assignment" target="mainframe">{$labels.href_tc_exec_assignment}</a></li>
					
				{if $session['testprojectOptions']->testPriorityEnabled && 
					$gui->grants.testplan_set_urgent_testcases == "yes"}
					<li><a href="{$gui->launcher}?feature=test_urgency" target="mainframe">{$labels.href_plan_assign_urgency}</a></li>
				{/if}

				{*if $gui->grants.testplan_update_linked_testcase_versions == "yes"}
					<li><a href="{$gui->launcher}?feature=planUpdateTC" target="mainframe">{$labels.href_update_tplan}</a></li>
				{/if} 

				{if $gui->grants.testplan_show_testcases_newest_versions == "yes"}
					<li><a href="{$gui->launcher}?feature=newest_tcversions" target="mainframe">{$labels.href_newest_tcversions}</a></li>
				{/if*}
					<!--/ul>
				</li-->
			{/if}
		</ul>
	</li>
</ul>
{/if}
	
{if $display_right_block_2}
	<ul class="nav navbar-nav" >
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
				{$labels.title_test_execution}
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				{if $gui->num_active_tplans > 0}
					{if $gui->grants.testplan_create_build == "yes" and $gui->countPlans > 0}
						<li><a href="lib/plan/buildView.php?tplan_id={$gui->testplanID}" target="mainframe">{$labels.href_build_new}</a></li>
					{/if}
				{/if}
				{if $gui->grants.testplan_execute == "yes"}
					<li><a href="{$gui->launcher}?feature=executeTest" target="mainframe">{$labels.href_execute_test}</a></li>

					{*if $gui->grants.exec_testcases_assigned_to_me == "yes"}
						<li><a href="{$gui->url.testcase_assignments}" target="mainframe">{$labels.href_my_testcase_assignments}</a></li>
					{/if*} 
				{/if}
			</ul>
		</li>
	</ul>
{/if}
{if $display_right_block_top}
	<ul class="nav navbar-nav" >
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
	</ul>
{/if}
{if $display_right_block_bottom}
	<ul class="nav navbar-nav" >
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
	</ul>
{/if}
{if $display_right_block_2}
	{if isset($session.testprojectTopMenu2.title_results)}
		<ul class="nav navbar-nav" >
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
					{$labels.title_results}
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
	
					{if $gui->grants.testplan_metrics == "yes"}
						<li><a href="{$gui->launcher}?feature=showMetrics" target="mainframe" method="post">{$labels.href_rep_and_metrics}</a></li>
                                                <!-- CODIGO ORIGINAL 
						<li><a href="{$gui->url.metrics_dashboard}" target="mainframe" method="post">{$labels.href_metrics_dashboard}</a></li>
						-->
						<li><a href="lib/results/resultsTCgroup.php?sub=0" target="mainframe" method="post">{$labels.href_complete_monitoring_report}</a></li>
						{*<li><a href="lib/results/metricsDashboard5.php" target="mainframe" method="post">{$labels.href_complete_monitoring_report}</a></li>*}
						<li><a href="lib/results/metricsDashboard3.php" target="mainframe" method="post">{$labels.href_quick_monitoring_report}</a></li>
						<li><a href="lib/results/metricsDashboard-3.php" target="mainframe" method="post">{$labels.href_history_report}</a></li>
					{/if}
				</ul>
			</li>
		</ul>
	{/if}
{/if}
<ul class="nav navbar-nav">
    <li>
        <a href="lib/dtManagement/categoriesView.php" target="mainframe">
            Data Management
        </a>
    </li>
</ul>
<!-- termina aqui o mainNavBar.tpl-->