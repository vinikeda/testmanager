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
                          href_plan_management,href_sub_aquire_management,href_issue_manager,
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
.chosen-container-active.chosen-with-drop >a {
    background:none;
    background-image:none !important;
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
	/*background: #f8f8f8;*/
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
<div style="float:right;margin-top:0px">
	<style>
		.spaced{
			padding-top:3px;
			padding-right:5px;
		}
	</style>
	<div style="list-style-type:none;color: #777;text-align:right;float:left;">
		<li class = "spaced"><label for="testproject" id="testproject-label" >{$labels.testproject}:</label></li>
		<li class = "spaced"><label id="testplan-label" for="select_chosen">{$labels.title_test_plan_mgmt}{*$labels.current_test_plan*}:</label></li>
	</div>
	<div class="container-fluid">					
		{if $gui->TestProjects != ""}
			<form class="form-inline" name="productForm" action="index.php?viewer={$gui->viewer}" method="post">
				<select id="testproject" class="chosen-select form-control" name="testproject" onchange="this.form.submit();" style="width:100%;">
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
					jQuery(".chosen-select").chosen({ width:'400px' });
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



<!-- termina aqui o mainNavBar.tpl-->