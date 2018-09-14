<?php

require('../../config.inc.php');
require_once('common.php');
require_once('displayMgr.php');
require_once('exttable.class.php');
ini_set('memory_limit', '-1');
/*set_time_limit(600);/**/

$templateCfg = templateConfiguration();

$smarty = new TLSmarty;


testlinkInitPage($db,false,false,"checkRights"); 
$metricsMgr = new tlTestPlanMetrics($db);
$columns = array();
$args->user = $_SESSION['currentUser'];
$args->tplan_id = $_SESSION['testplanID'];
$args->tproject_id = $_SESSION['testprojectID'];
$args->active = $_REQUEST['active'];
if(isset($_GET['sub'])){
    $args->sub = $_GET['sub'];
}else{
    $args->sub =  $args->user->getSub_adquirentesID($db,$args->tplan_id);
}
$args->subs =  $args->user->getAccessibleSub_adquirentes($db,$args->tproject_id,$args->active);
if($args->sub == '0'){
    //$args->tplanIDS = $args->user->getAccessibleTestPlans($db,$args->tproject_id,null,array('output' =>'combo', 'active' => 1));//$args->user->getAccessibleTestplansBySubaquirer($db,$args->tproject_id,$args->sub);
    $args->tplanIDS = $args->user->getAccessibleTestplansWithActiveBuilds($db,$args->tproject_id,$args->active);
}else{
    $args->tplanIDS = $args->user->getAccessibleTestplansBySubaquirer($db,$args->tproject_id,$args->sub,$args->active);
}
    
//$args->tplanIDS[0] = 'todos';
$gui = $args;
$imgSet = $smarty->getImages();
$gui->img = new stdClass();
$gui->img->exec = $imgSet['steps'];//$imgSet['exec_icon'];
$gui->img->edit = $imgSet['edit_icon'];
$gui->img->history = $imgSet['history_small'];
$cfg['results'] = config_get('results');
$lbl = init_labels(array('title_test_suite_name' => null,'platform' => null,'priority' => null,
                           'result_on_last_build' => null, 'title_test_case_title' => null));
$l18n = init_labels(array('design' => null, 'execution' => null, 'history' => 'execution_history',
                            'test_result_matrix_filters' => null, 'too_much_data' => null,'too_much_builds' => null,
                            'result_on_last_build' => null, 'versionTag' => 'tcversion_indicator') );
    $l18n['not_run']=lang_get($cfg['results']['status_label']['not_run']);
    foreach($cfg['results']['code_status'] as $code => $verbose)
    {
        if( isset($cfg['results']['status_label'][$verbose])){
            $l18n[$code] = lang_get($cfg['results']['status_label'][$verbose]);
            $gui->map_status_css[$code] = $cfg['results']['code_status'][$code] . '_text';
        }
    }
$labels= $l18n;

$tproject_mgr = new testproject($db);
$tproject_info = $tproject_mgr->get_by_id($args->tproject_id);
$gui->tproject_name = $tproject_info['name'];
$args->tcPrefix = $tproject_info['prefix'] . config_get('testcase_cfg')->glue_character;

$tcols = array('tsuite','link','priority');
$cols = array_flip($tcols);

$tplanMgr = new tlTestPlanMetrics($db);
$gui->matrixCfg  = config_get('resultMatrixReport');
foreach($args->tplanIDS as $ids=>$names){
    $gui->buildInfoSet = $tplanMgr->get_builds($ids, $args->active,null,
                                                array('orderBy' => $gui->matrixCfg->buildOrderByClause));
    if($gui->buildInfoSet != null){
    $buildSet/*$args->builds->idSet*/ = array_keys($gui->buildInfoSet);
    $args->builds->latest->id = end($buildSet);
    $opt['inactiveBuilds'] = $args->active == 1?true:false;
    if(!is_null($buildSet))$execStatus = $metricsMgr->getExecStatusMatrix($ids,$buildSet,$opt);//print_r($execStatus['metrics']);
    $latestExecution = $execStatus['latestExec'];
    $gui->matrix = array();//print_r($execStatus['metrics']);
    foreach($execStatus['metrics'] as $tsuiteID=>$tsuite){
        foreach($tsuite as $tcaseID=>$tcase){
            $top = current(array_keys($execStatus['metrics'][$tsuiteID][$tcaseID][0]));
            $rows[$cols['tsuite']] = $tcase[0][$top]['suiteName'];
            $external_id = $args->tcPrefix . $tcase[0][$top]['external_id'];
            $rows[$cols['link']] .= "{$external_id}:{$tcase[0][$top]['name']}";//htmlspecialchars("{$external_id}:{$rf[$top]['name']}",ENT_QUOTES);
            //$rows[$cols['priority']] = $tcase[0][$top]['priority_level'];
            foreach($tcase[0] as $buildID=>$build){
                $r4build['text'] = "";
                $execID = $build['executions_id'];
                $r4build['text'] = $labels[$build['status']];
                if($build['status'] != 'n')
                    $r4build['text'] .= "    <a  onclick=\"jQuery('#Nissues').modal('show');document.getElementById('execprint').src = 'lib/execute/execPrint.php?id=$execID'\" ><img title=\"{$labels['execution']}\" src=\"{$gui->img->exec}\" /></a>";
                $r4build['value'] = $build['status'];
                $r4build['cssClass'] = $gui->map_status_css[$build['status']];
                $buildExecStatus[] = $r4build;
                if($args->builds->latest->id == $buildID) $execOnLastBuild = $r4build;
                if(
                    ($latestExecution[0][$tcaseID]['status'] == 
                    $cfg['results']['status_code']['not_run']) ||
                    ( ($latestExecution[0][$tcaseID]['build_id'] == $buildID) &&                             
                    ($latestExecution[0][$tcaseID]['id'] == $build['executions_id']) )
                )                  
                {
                  $lexec = $r4build;
                }

            }

            if ($gui->matrixCfg->buildColumns['showStatusLastExecuted'])
            {
                $buildExecStatus[] = $execOnLastBuild;
            }
            if ($gui->matrixCfg->buildColumns['latestBuildOnLeft']) 
            {
              $buildExecStatus = array_reverse($buildExecStatus);
            }

            $rows = array_merge($rows, $buildExecStatus);
            $rows[] = $lexec;
            $gui->matrix[] = $rows;
            unset($r4build);
            unset($rows);
            unset($buildExecStatus);
        }
    }
    $columns[$ids] = array(array('title_key' => 'title_test_suite_name', 'width' => 100),
                                       array('title_key' => 'title_test_case_title', 'width' => 150));
    $group_name = $lbl['title_test_suite_name'];
    $gui->options->testPriorityEnabled = $tproject_info['opt']->testPriorityEnabled;
    if($gui->options->testPriorityEnabled) 
    {
      $columns[$ids][] = array('title_key' => 'priority', 'type' => 'priority', 'width' => 40, 'hidden' => true);
    }
    $guiObj->filterFeedback = null;

    /**
     * parei na adaptação desse cara: foreach($buildIDSet as $iix)
     */
    $buildIDSet = array_keys($tcase[0]);
    $buildSet = array();
    foreach($buildIDSet as $iix)
    {
        $buildSet[] = $gui->buildInfoSet[$iix];
        if($gui->filterApplied)
        {
            $gui->filterFeedback[] = $gui->buildInfoSet[$iix]['name'];
        }
    }
    foreach($buildSet as $build) 
    {
        $columns[$ids][] = array('title' => $build['name'], 'type' => 'status', 'width' => 100);
    }
    $columns[$ids][] = array('title_key' => 'last_execution', 'type' => 'status', 'width' => 100, 'hidden' => true);//if($ids == 159552)var_dump($ids,$gui->matrix);//var_dump($columns[$ids]);
    $matrix = new tlExtTable($columns[$ids], $gui->matrix, "tl_table_results_tc$ids");
    //if platforms feature is enabled group by platform otherwise group by test suite
    $matrix->setGroupByColumnName($group_name);
    $matrix->sortDirection = 'DESC';

    if($gui->options->testPriorityEnabled) 
    {
      // Developer Note:
      // To understand 'filter' => 'Priority' => see exttable.class.php => buildColumns()
      $matrix->addCustomBehaviour('priority', array('render' => 'priorityRenderer', 'filter' => 'Priority'));
      $matrix->setSortByColumnName($lbl['priority']);
    } 
    else 
    {
      //$matrix->setSortByColumnName($lbl['title_test_case_title']);
    }

    // define table toolbar
    $matrix->showToolbar = true;
    $matrix->toolbarExpandCollapseGroupsButton = true;
    $matrix->toolbarShowAllColumnsButton = true;
    unset($columns[$ids]);
    $gui->tableSet[$ids] = $matrix;
    }
}
//$execStatus = $metricsMgr->getExecStatusMatrix($args->tplan_id,$buildSet,$opt);

$timerOff = microtime(true);
$gui->elapsed_time = round($timerOff - $timerOn,2);
$smarty->assign('gui',$gui);//echo $templateCfg->template_dir . $tpl, $smarty, $args->format, $mailCfg;

/*foreach ($gui->tableSet as $set){
    echo($set->renderHeadSection().'
             ');
}/**/
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);


/*$smarty->assign('gui',$gui);
$smarty->display($templateCfg->template_dir .$templateCfg->default_template);*/
function checkRights(&$db,&$user,$context = null)
{
  if(is_null($context))
  {
    $context = new stdClass();
    $context->tproject_id = $context->tplan_id = null;
    $context->getAccessAttr = false; 
  }

  $check = $user->hasRight($db,'testplan_metrics',$context->tproject_id,$context->tplan_id,$context->getAccessAttr);
  return $check;
}
