<!-- começa aqui o mainPageLeft.tpl-->
{* 
 Testlink Open Source Project - http://testlink.sourceforge.net/ 
 @filesource  mainPageLeft.tpl
 Purpose: smarty template - main page / site map                 
                                                                 
 @internal revisions
 @since 1.9.15
*}
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
                          href_reqmgrsystem_management,href_req_monitor_overview'}

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

<ul class="nav nav-pills nav-stacked col-xs-2" style="padding-right:0px;">
	{if $display_left_block_2}
		<li role="presentation" class="active"><a>{$labels.system_config}</a></li>
		{if $gui->grants.cfield_management == "yes"}
			<li><a href="lib/cfields/cfieldsView.php">{$labels.href_cfields_management}</a></li>
		{/if}
		 
		{if $gui->grants.issuetracker_management || $gui->grants.issuetracker_view}
			<li><a href="lib/issuetrackers/issueTrackerView.php">{$labels.href_issuetracker_management}</a></li>
		{/if}
	{/if}
	{if $display_left_block_1}
		<li class="active"><a>{$labels.title_product_mgmt}</a></li>
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
	{/if}
	{if $display_left_block_4}
		<li role="presentation" class="active"><a>{$labels.title_test_spec}</a></li>
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
			<li><a href="lib/testcases/tcSearch.php?doAction=userInput&tproject_id={$gui->testprojectID}">{$labels.href_search_tc}</a></li>
		{/if*}    
  
		{if $gui->hasKeywords}  
			{if $gui->grants.keywords_view == "yes"}
				{if $gui->grants.keywords_edit == "yes"}
					<li><a href="{$gui->launcher}?feature=keywordsAssign">{$labels.href_keywords_assign}</a></li>
				{/if}
			{/if}
		{/if}
  
		{if $gui->grants.modify_tc eq "yes"}
			<li><a href="lib/results/tcCreatedPerUserOnTestProject.php?do_action=uinput&tproject_id={$gui->testprojectID}">{$labels.link_report_test_cases_created_per_user}</a></li>
		{/if}
	{/if}
	{*if $display_left_block_3}comentado pois não será utilizado nessa versão em questão
		<li role="presentation" class="active"><a>{$labels.title_requirements}</a></li>
		<!--div id="requirements_topics" -->
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
	{/if*}
	{if $display_left_block_top}
		<li role="presentation" class="active"><a>{$labels.title_plugins}</a></li>
		<!--script type="text/javascript">
		  function display_left_block_top()
		  {
			var pt = new Ext.Panel({
									title: '{$labels.title_plugins}',
									collapsible:false,
									collapsed: false,
									draggable: false,
									contentEl: 'plugin_left_top',
									baseCls: 'x-tl-panel',
									bodyStyle: "background:#ffffff;padding:3px;",
									renderTo: 'menu_left_block_top',
									width:'100%'
								   });
		  }
		</script-->
		{if isset($gui->plugins.EVENT_LEFTMENU_TOP)}
			<div id="plugin_left_top">
				{foreach from=$gui->plugins.EVENT_LEFTMENU_TOP item=menu_item}
					{$menu_item}
				{/foreach}
			</div>
		{/if}
	{/if}
	{if $display_left_block_bottom}
		<!--script type="text/javascript">apenas comentei pois como não tenho nenhum plugin pra testar, não sei se essa estruturá é vital para os plugins
			function display_left_block_bottom()
			{
				var pb = new Ext.Panel({
					title: '{$labels.title_plugins}',
					collapsible:false,
					collapsed: false,
					draggable: false,
					contentEl: 'plugin_left_bottom',
					baseCls: 'x-tl-panel',
					bodyStyle: "background:#ffffff;padding:3px;",
					renderTo: 'menu_left_block_bottom',
					width:'100%'
				});
			}
		</script-->
		<li role="presentation" class="active"><a>{$labels.title_plugins}</a></li>
		{if isset($gui->plugins.EVENT_LEFTMENU_BOTTOM)}
			<div id="plugin_left_bottom">
				{foreach from=$gui->plugins.EVENT_LEFTMENU_BOTTOM item=menu_item}
					{$menu_item}
					<br />
				{/foreach}
			</div>
		{/if}
	{/if}
</ul>
<!-- termina aqui o mainPageLeft.tpl-->