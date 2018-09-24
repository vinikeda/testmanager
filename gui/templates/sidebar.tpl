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

<style>
    #sidebar {
        min-width: 250px;
        max-width: 250px;
        position:fixed;
        transition: all 0.3s;
        background-color: #f8f8f8;
    }
    #sidebar.active {
        margin-left: -250px;
    }
    a.sidebarElem{
        width:100%;
    }
    /*#sidebar ul li a{
        padding: 10px;
        font-size: 1.1em;
        display: block;
        list-style: none;
    }
    a[aria-expanded="false"]::before,
    a[aria-expanded="true"]::before {
      content: '\e259';
      display: block;
      position: absolute;
      right: 20px;
      font-family: 'Glyphicons Halflings';
      font-size: 0.6em;
    }
    .remove-style{
        list-style: none;
        -webkit-padding-start: 10px;
    }

    a[aria-expanded="true"]::before {
      content: '\e260';
    }*/
</style>
<script>
    jQuery(document).ready(function() {
        jQuery("#sidebarCollapse").on("click", function() {
            jQuery("#sidebar").toggleClass("active");
            jQuery(this).toggleClass("active");
        });
    });
</script>
<nav id="sidebar">
    <!--ul>
        <a class="navbar-brand logo-navbar"  href="index.php" target="_parent">
            <img style="height:100%;"class="image-responsive" alt="Company logo" title="logo" src="{$smarty.const.TL_THEME_IMG_DIR}{$tlCfg->logo_login}{*{$tlCfg->logo_navbar}*}" />
        </a>
    </ul-->{*
    <ul class="nav navbar-nav" >
	
	<li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
                    {$labels.th_user}<span class="caret">
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a>{$gui->userName}</a>
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
{/if*}
    
    <ul class="nav flex-column">
        <li class="nav-item">
            <div class="dropright">
                <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                    {$labels.th_user}
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item">{$gui->userName}</a>
                    <a class="dropdown-item">{$gui->userRole}</a>
                    <div class="dropdown-divider"></div>
                    {if $display_right_block_2}
                        {if $gui->grants.testplan_execute == "yes"}
                            {if $gui->grants.exec_testcases_assigned_to_me == "yes"}
                                <a href="{$gui->url.testcase_assignments}" target="mainframe" class="dropdown-item">{$labels.href_my_testcase_assignments}</a>
                            {/if}
                        {/if}
                    {/if}
                    <a href='lib/usermanagement/userInfo.php' target="mainframe" accesskey="i" tabindex="6" class="dropdown-item">
                        {$labels.title_edit_personal_data}
                    </a>
                    <a href="logout.php?viewer={$gui->viewer}" target="_parent" accesskey="q" class="dropdown-item">
                        Logout
                    </a>
                </div>
            </div>
            <!--a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
                    {$labels.th_user}<span class="caret">
            </a-->
        </li>
        {if $display_left_block_2 || $display_left_block_1}
            <li class="nav-item">
                <div class="dropright">
                    <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                        {$labels.system_config}
			<span class="caret"></span>
                    </a>
                        <div class="dropdown-menu">
                            {if $display_left_block_2}
				{if $gui->grants.cfield_management == "yes"}
                                    <a href="lib/cfields/cfieldsView.php" target="mainframe" class="dropdown-item">{$labels.href_cfields_management}</a>
				{/if}
                            {/if}
                                {if $display_left_block_1}
                                    {if $gui->grants.cfield_management == "yes"}
                                        <a href="lib/cfields/cfieldsTprojectAssign.php" target="mainframe" class="dropdown-item">{$labels.href_cfields_tproject_assign}</a>
                                    {/if}

                                    {if $gui->grants.keywords_view == "yes"}
                                        <a href="lib/keywords/keywordsView.php?tproject_id={$gui->testprojectID}" target="mainframe" class="dropdown-item">{$labels.href_keywords_manage}</a>
                                    {/if}

                                    {if $gui->grants.platform_management == "yes"}
                                        <a href="lib/platforms/platformsView.php" target="mainframe" class="dropdown-item">{$labels.href_platform_management}</a>
                                    {/if}
                                {/if}
                            {if $display_left_block_2}
				{if $gui->grants.issuetracker_management || $gui->grants.issuetracker_view}
                                    <a href="lib/issuetrackers/issueTrackerView.php" target="mainframe" class="dropdown-item">{$labels.href_issuetracker_management}</a>
				{/if}
                                {if isset($session.testprojectTopMenu3.title_admin)}{$session.testprojectTopMenu3.title_admin}{/if}
                                {if isset($session.testprojectTopMenu3.title_events)}{$session.testprojectTopMenu3.title_events}{/if}
                            {/if}
                        </div>
                </div>
            </li>
        {/if}
        {if $gui->role == 8 ||$gui->role == 11 || $gui->role == 13 || $gui->grants.tproject_user_role_assignment == "yes" || ($gui->grants.testplan_milestone_overview == "yes" and $gui->countPlans > 0)}
            <li class="nav-item">
                <div class="dropright">
                    <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                        Quality Assurance
			<span class="caret"></span>
                    </a>
                    <div class="dropdown-menu">
                        {if $gui->role == 8 ||$gui->role == 11 || $gui->role == 13}
                            <a href="lib/macros/macrosView.php" target="mainframe" class="dropdown-item">{$labels.macros_mgmt}</a>
                        {/if}
                        {if $gui->grants.tproject_user_role_assignment == "yes"}
                            <a href="lib/usermanagement/usersAssign.php?featureType=testproject&amp;featureID={$gui->testprojectID}" target="mainframe" class="dropdown-item">{$labels.href_assign_user_roles}</a>
                        {/if}
                        {if $gui->grants.testplan_milestone_overview == "yes" and $gui->countPlans > 0}
                            <a href="lib/plan/planMilestonesView.php" target="mainframe" class="dropdown-item">{$labels.href_plan_mstones}</a>
                        {/if}
                    </div>
                </div>
            </li>
        {/if}
        {if $gui->role == 8 ||$gui->role == 11 || $gui->role == 13 || $gui->role == 9|| $gui->role == 14}
            <li class="nav-item">
                <div class="dropright">
                    <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                        {$labels.href_issue_manager}
			<span class="caret"></span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="lib/issue/issuesView.php" target="mainframe" class="dropdown-item">
                            {$labels.href_manage_issues}
                        </a>
                        <a href="lib/issue/CategoriesView.php" target="mainframe" class="dropdown-item">
                            {$labels.href_manage_issues_categories}
                        </a>
                        <a href="lib/issue/MarkersView.php" target="mainframe" class="dropdown-item">
                            {$labels.href_manage_issues_markers}
                        </a>
                    </div>
                </div>
            </li>
        {/if}
        {if $display_left_block_4 || $gui->grants.testplan_update_linked_testcase_versions == "yes" || $gui->grants.testplan_show_testcases_newest_versions == "yes"}
            <li class="nav-item">
                <div class="dropright">
                    <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                        {$labels.title_test_spec}
			<span class="caret"></span>
                    </a>
                    <div class="dropdown-menu">
                        {if $display_left_block_4}
                            <a href="{$gui->launcher}?feature=editTc" target="mainframe" class="dropdown-item">
                                {if $gui->grants.modify_tc eq "yes"}
                                    {lang_get s='href_edit_tc'}
                                {else}
                                    {lang_get s='href_browse_tc'}
                                {/if}
                            </a>  
                            {if $gui->hasKeywords}  
                                {if $gui->grants.keywords_view == "yes"}
                                    {if $gui->grants.keywords_edit == "yes"}
                                        <a href="{$gui->launcher}?feature=keywordsAssign" target="mainframe" class="dropdown-item">
                                            {$labels.href_keywords_assign}
                                        </a>
                                    {/if}
                                {/if}
                            {/if}
					  
                            {if $gui->grants.modify_tc eq "yes"}
                                <a href="lib/results/tcCreatedPerUserOnTestProject.php?do_action=uinput&tproject_id={$gui->testprojectID}" target="mainframe" class="dropdown-item">
                                    {$labels.link_report_test_cases_created_per_user}
                                </a>
                            {/if}
                        {/if}
                        {if $gui->grants.testplan_update_linked_testcase_versions == "yes"}
                            <a href="{$gui->launcher}?feature=planUpdateTC" target="mainframe" class="dropdown-item">{$labels.href_update_tplan}</a>
                        {/if}
                        {if $gui->grants.testplan_show_testcases_newest_versions == "yes"}
                            <a href="{$gui->launcher}?feature=newest_tcversions" target="mainframe" class="dropdown-item">{$labels.href_newest_tcversions}</a>
                        {/if}
                    </div>
                </div>
            </li>
        {/if}
        {if $display_left_block_1}
            <li class="nav-item">
                <div class="dropright">
                    <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                        {$labels.title_product_mgmt}
			<span class="caret"></span>
                    </a>
                    <div class="dropdown-menu">
                        {if $gui->grants.project_edit == "yes"}
                            <a href="lib/project/projectView.php" target="mainframe" class="dropdown-item">{$labels.href_tproject_management}</a>
                            <a href="lib/subadiq/subadiqView.php" target="mainframe" class="dropdown-item">{$labels.href_sub_aquire_management}</a>
                        {/if}
                    </div>
                </div>
            </li>
        {/if}
        
        {if $display_left_block_top}
            <li class="nav-item">
                <div class="dropright">
                    <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                        {$labels.title_plugins}
			<span class="caret"></span>
                    </a>
                    <div class="dropdown-menu">
                        {if isset($gui->plugins.EVENT_LEFTMENU_TOP)}
                            <div id="plugin_left_top">
                                {foreach from=$gui->plugins.EVENT_LEFTMENU_TOP item=menu_item}
                                    {$menu_item}
                                {/foreach}
                            </div>
                        {/if}
                    </div>
                </div>
            </li>
        {/if}
        {if $display_left_block_bottom}
            <li class="nav-item">
                <div class="dropright">
                    <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                        {$labels.title_plugins}
                        <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu">
                        {if isset($gui->plugins.EVENT_LEFTMENU_BOTTOM)}
                            <div id="plugin_left_bottom">
                                {foreach from=$gui->plugins.EVENT_LEFTMENU_BOTTOM item=menu_item}
                                    {$menu_item}
                                    <br />
                                {/foreach}
                            </div>
                        {/if}
                    </div>
                </div>
            </li>
        {/if}
        {if $gui->num_active_tplans > 0 and ($gui->grants.mgt_testplan_create == "yes" or $display_right_block_3)}
            <li class="nav-item">
                <div class="dropright">
                    <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                        {$labels.title_test_plan_mgmt}
                        <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu">
                        {if $display_right_block_1}
                            {if $gui->grants.mgt_testplan_create == "yes"}
                                <a href="lib/plan/planView.php" target="mainframe" class="dropdown-item">{$labels.href_plan_management}</a>
                            {/if}
			{/if}
			
			{if $display_right_block_3}
			
                            <a href="{$gui->launcher}?feature=planAddTC" target="mainframe" class="dropdown-item">{$labels.href_add_remove_test_cases}</a>

                            <a href="{$gui->launcher}?feature=tc_exec_assignment" target="mainframe" class="dropdown-item">{$labels.href_tc_exec_assignment}</a>

                            {if $session['testprojectOptions']->testPriorityEnabled && 
                                    $gui->grants.testplan_set_urgent_testcases == "yes"}
                                <a href="{$gui->launcher}?feature=test_urgency" target="mainframe" class="dropdown-item">{$labels.href_plan_assign_urgency}</a>
                            {/if}

			{/if}
                    </div>
                </div>
            </li>
        {/if}
            
        {if $display_right_block_2}
            <li class="nav-item">
                <div class="dropright">
                    <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                        {$labels.title_test_execution}
                        <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu">
                        {if $gui->num_active_tplans > 0}
                            {if $gui->grants.testplan_create_build == "yes" and $gui->countPlans > 0}
                                <a href="lib/plan/buildView.php?tplan_id={$gui->testplanID}" target="mainframe" class="dropdown-item">{$labels.href_build_new}</a>
                            {/if}
                        {/if}
                        {if $gui->grants.testplan_execute == "yes"}
                            <a href="{$gui->launcher}?feature=executeTest" target="mainframe" class="dropdown-item">{$labels.href_execute_test}</a>
                        {/if}
                    </div>
                </div>
            </li>
        {/if}
        
        {if $display_right_block_top}
            <li class="nav-item">
                <div class="dropright">
                    <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                        {$labels.title_plugins}
                        <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu">
                        {if isset($gui->plugins.EVENT_RIGHTMENU_TOP)}
                            <div id="plugin_right_top">
                                {foreach from=$gui->plugins.EVENT_RIGHTMENU_TOP item=menu_item}
                                    {$menu_item}
                                    <br/>
                                {/foreach}
                            </div>
                        {/if}
                    </div>
                </div>
            </li>
        {/if}
        
        {if $display_right_block_bottom}
            <li class="nav-item">
                <div class="dropright">
                    <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                        {$labels.title_plugins}
                        <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu">
                        {if isset($gui->plugins.EVENT_RIGHTMENU_BOTTOM)}
                            <div id="plugin_right_bottom">
                                {foreach from=$gui->plugins.EVENT_RIGHTMENU_BOTTOM item=menu_item}
                                    {$menu_item}
                                    <br/>
                                {/foreach}
                            </div>
                        {/if}
                    </div>
                </div>
            </li>
        {/if}
        {if $display_right_block_2}
            {if isset($session.testprojectTopMenu2.title_results)}
                <li class="nav-item">
                    <div class="dropright">
                        <a class ="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            {$labels.title_results}
                            <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu">
                            {if $gui->grants.testplan_metrics == "yes"}
                                <a href="{$gui->launcher}?feature=showMetrics" target="mainframe" method="post" class="dropdown-item">{$labels.href_rep_and_metrics}</a>
                                <a href="lib/results/resultsTCgroup.php?sub=0&active=1" target="mainframe" method="post" class="dropdown-item">{$labels.href_complete_monitoring_report}</a>
                                <a href="lib/results/metricsDashboard3.php" target="mainframe" method="post" class="dropdown-item">{$labels.href_quick_monitoring_report}</a>
                                <a href="lib/results/resultsTCgroup.php?sub=0&active=0" target="mainframe" method="post" class="dropdown-item">{$labels.href_history_report}</a>
                            {/if}
                        </div>
                    </div>
                </li>
            {/if}
        {/if}
        <li class="nav-item">
            <a href="lib/dtManagement/categoriesView.php" target="mainframe" class="nav-link">
                Data Management
            </a>
        </li>
    </ul>
</nav>