<!-- começa aqui o mainPageRight.tpl-->
{*
 Testlink Open Source Project - http://testlink.sourceforge.net/
 @filesource mainPageRight.tpl
 main page right side
 @internal revisions
 
*}
{lang_get var="labels"
          s="current_test_plan,ok,testplan_role,msg_no_rights_for_tp,
             title_test_execution,href_execute_test,href_rep_and_metrics,
             href_update_tplan,href_newest_tcversions,title_plugins,
             href_my_testcase_assignments,href_platform_assign,
             href_tc_exec_assignment,href_plan_assign_urgency,
             href_upd_mod_tc,title_test_plan_mgmt,title_test_case_suite,
             href_plan_management,href_assign_user_roles,
             href_build_new,href_plan_mstones,href_plan_define_priority,
             href_metrics_dashboard,href_add_remove_test_cases"}


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
<ul class="nav nav-pills nav-stacked col-xs-2" style="float:right;">
	<li role="presentation" class="active"><a>{$labels.title_test_plan_mgmt}{include file="inc_help.tpl" helptopic="hlp_testPlan" show_help_icon=true 
							inc_help_alt="$text_hint" inc_help_title="$text_hint"  
							inc_help_style="float: right;vertical-align: top;"}</a>
	</li>
	{if $gui->num_active_tplans > 0}
		<li>
			<div style="padding:10px 0px 0px 15px;">
				{lang_get s='help' var='common_prefix'}
				{lang_get s='test_plan' var="xx_alt"}
				{$text_hint="$common_prefix: $xx_alt"}
				
				<form style="margin-left:0px;" class="form-horizontal" name="testplanForm" action="lib/general/mainPage.php">
					{if $gui->countPlans > 0}
						<div style="margin-left:0px; margin-bottom:0px;" class="form-group">
							{$labels.current_test_plan}:<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<select class="form-control chosen-select" id="select" name="testplan" onchange="this.form.submit();">
								{section name=tPlan loop=$gui->arrPlans}
									<option value="{$gui->arrPlans[tPlan].id}"
										{if $gui->arrPlans[tPlan].selected} selected="selected" {/if}
										title="{$gui->arrPlans[tPlan].name|escape}">{$gui->arrPlans[tPlan].name|escape}</option>
								{/section}
							</select>
						</div>
						{if $gui->countPlans == 1}
							<input type="button" onclick="this.form.submit();" value="{$labels.ok}"/>
						{/if}
						{if $gui->testplanRole neq null}
							<br />{$labels.testplan_role} {$gui->testplanRole|escape}
						{/if}
					{else}
						{if $gui->num_active_tplans > 0}{$labels.msg_no_rights_for_tp}{/if}
					{/if}
				</form>
			</div>
		</li>
	{/if}
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
	{if $display_right_block_2}
		<li role="presentation" class="active"><a>{$labels.title_test_execution}</a></li>
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
	{/if}
	{if $display_right_block_3}
		<li role="presentation" class="active"><a>{$labels.title_test_case_suite}</a></li>
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
	{/if}
	{if $display_right_block_top}
		<!--script type="text/javascript">
			function display_right_block_top() 
			{
			  var pt = new Ext.Panel({
									  title: '{$labels.title_plugins}',
									  collapsible: false,
									  collapsed: false,
									  draggable: false,
									  contentEl: 'plugin_right_top',
									  baseCls: 'x-tl-panel',
									  bodyStyle: "background:#ffffff;padding:3px;",
									  renderTo: 'menu_right_block_top',
									  width: '100%'
									 });
			}
		</script-->
		<li role="presentation" class="active"><a>{$labels.title_plugins}</a></li>
		{if isset($gui->plugins.EVENT_RIGHTMENU_TOP)}
			<div id="plugin_right_top">
				{foreach from=$gui->plugins.EVENT_RIGHTMENU_TOP item=menu_item}
					{$menu_item}
					<br/>
				{/foreach}
			</div>
		{/if}
	{/if}
	{if $display_right_block_bottom}
		<!--script type="text/javascript">
		function display_right_block_bottom() 
		{
		  var pb = new Ext.Panel({
								  title: '{$labels.title_plugins}',
								  collapsible: false,
								  collapsed: false,
								  draggable: false,
								  contentEl: 'plugin_right_bottom',
								  baseCls: 'x-tl-panel',
								  bodyStyle: "background:#ffffff;padding:3px;",
								  renderTo: 'menu_right_block_bottom',
								  width: '100%'
								 });
		}
		</script-->
		<li role="presentation" class="active"><a>{$labels.title_plugins}</a></li>
		{if isset($gui->plugins.EVENT_RIGHTMENU_BOTTOM)}
			<div id="plugin_right_bottom">
				{foreach from=$gui->plugins.EVENT_RIGHTMENU_BOTTOM item=menu_item}
					{$menu_item}
					<br/>
				{/foreach}
			</div>
		{/if}  
	{/if}
</ul>
<script>
jQuery( document ).ready(function() {
jQuery(".chosen-select").chosen({ width: "80%" });
});
</script>
<!-- termina aqui o mainPageRight.tpl-->