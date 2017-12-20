<!-- comeca aqui o editStatus.tpl -->
{lang_get 
  var='labels'
  s='edit_notes,build_is_closed,test_cases_cannot_be_executed,test_exec_notes,test_exec_result,
	th_testsuite,details,warning_delete_execution,title_test_case,th_test_case_id,keywords,
	version,has_no_assignment,assigned_to,execution_history,exec_notes,step_actions,
	execution_type_short_descr,expected_results,testcase_customfields,builds_notes,
  estimated_execution_duration,version,btn_save_and_exit,test_plan_notes,bug_copy_from_latest_exec,
	last_execution,exec_any_build,date_time_run,test_exec_by,build,exec_status,
	test_status_not_run,tc_not_tested_yet,last_execution,exec_current_build,
  bulk_tc_status_management,access_test_steps_exec,assign_exec_task_to_me,
	attachment_mgmt,bug_mgmt,delete,closed_build,alt_notes,alt_attachment_mgmt,
	img_title_bug_mgmt,img_title_delete_execution,test_exec_summary,title_t_r_on_build,
	execution_type_manual,execution_type_auto,run_mode,or_unassigned_test_cases,
	no_data_available,import_xml_results,btn_save_all_tests_results,execution_type,
	testcaseversion,btn_print,execute_and_save_results,warning,warning_nothing_will_be_saved,
	test_exec_steps,test_exec_expected_r,btn_save_tc_exec_results,only_test_cases_assigned_to,
	deleted_user,click_to_open,reqs,requirement,show_tcase_spec,edit_execution, 
	btn_save_exec_and_movetonext,step_number,btn_export,btn_export_testcases,bug_summary,bug_description,
  bug_link_tl_to_bts,bug_create_into_bts,execution_duration,execution_duration_short,
  issueType,issuePriority,artifactVersion,artifactComponent,
  add_issue_note,exec_not_run_result_note,remoteExecFeeback,create_issue_feedback'}
  {$args_labels = $labels}
  {$ResultsStatusCode=$tlCfg->results.status_code}
		<div class="resultBox">
		{*lang_get var="args_labels" s = "execution_duration"criei esse trecho achando que ia resolver mas não mudou nada*}
              {if $args_save_type == 'bulk'}
                {foreach key=verbose_status item=locale_status from=$tlCfg->results.status_label_for_exec_ui}
    						      <input type="radio" {$args_input_enable_mgmt} name="{$radio_id_prefix}[{$args_tcversion_id}]" 
    						      id="{$radio_id_prefix}_{$args_tcversion_id}_{$ResultsStatusCode.$verbose_status}" 
    							    value="{$ResultsStatusCode.$verbose_status}teste"
    											onclick="javascript:set_combo_group('execSetResults','status_','{$ResultsStatusCode.$verbose_status}');"
    							    {if $verbose_status eq $stat}{*$tlCfg->results.default_status*}
    							        checked="checked" 
    							    {/if} /> &nbsp;{lang_get s=$locale_status}<br />
    					  {/foreach}
{*              
				{else}
					{$args_labels.test_exec_result}&nbsp;
					<select name="statusSingle[{$tcversion_id}]" id="statusSingle_{$tcversion_id}">
					{html_options options=$gui->execStatusValues}
					</select>
				{/if}
*}
				{else}
                {$args_labels.test_exec_result}&nbsp;
					{foreach key=verbose_status item=locale_status from=$tlCfg->results.status_label_for_exec_ui}
						<br /><!--é esse que é usado para gerar os radios-->
						<input type="radio" {$args_input_enable_mgmt} name="executionStatus{*$radio_id_prefix}[{$args_tcversion_id}]*}" 
						id="{$radio_id_prefix}_{$args_tcversion_id}_{$ResultsStatusCode.$verbose_status}" 
						value="{$ResultsStatusCode.$verbose_status}"
									onclick="javascript:set_combo_group('execSetResults','status_','{$ResultsStatusCode.$verbose_status}');"
						{if $ResultsStatusCode.$verbose_status == $stat}{*$tlCfg->results.default_status*}
							checked="checked" 
					{/if} /> &nbsp;{lang_get s=$locale_status}
					{/foreach}
              {/if}
				  					  
    				</div>
<!-- termina aqui o editStatus.tpl -->