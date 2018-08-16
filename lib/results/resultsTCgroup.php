<?php

require('../../config.inc.php');
require_once('common.php');


$templateCfg = templateConfiguration();

$smarty = new TLSmarty;


testlinkInitPage($db,false,false,"checkRights"); 

$metricsMgr = new tlTestPlanMetrics($db);

$args->user = $_SESSION['currentUser'];
$args->tplan_id = $_SESSION['testplanID'];
$args->tproject_id = $_SESSION['testprojectID'];
if(isset($_GET['sub'])){
    $args->sub = $_GET['sub'];
}else{
    $args->sub =  $args->user->getSub_adquirentesID($db,$args->tplan_id);
}
$args->subs =  $args->user->getAccessibleSub_adquirentes($db,$args->tproject_id);
if($args->sub == '0'){
    $args->tplanIDS = $args->user->getAccessibleTestPlans($db,$args->tproject_id,null,array('output' =>'combo', 'active' => 1));//$args->user->getAccessibleTestplansBySubaquirer($db,$args->tproject_id,$args->sub);
}else{
    $args->tplanIDS = $args->user->getAccessibleTestplansBySubaquirer($db,$args->tproject_id,$args->sub);
}
    
//$args->tplanIDS[0] = 'todos';
$gui = $args;
$cfg['results'] = config_get('results');
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
$gui->matrixCfg  = config_get('resultMatrixReport');$cont =0;
foreach($args->tplanIDS as $ids=>$names){
    $gui->buildInfoSet = $tplanMgr->get_builds($ids, testplan::ACTIVE_BUILDS,null,
                                                array('orderBy' => $gui->matrixCfg->buildOrderByClause));
$buildSet/*$args->builds->idSet*/ = array_keys($gui->buildInfoSet);
$args->builds->latest->id = end($buildSet);
    if(!is_null($buildSet))$execStatus = $metricsMgr->getExecStatusMatrix($ids,$buildSet,$opt);//print_r($execStatus['metrics']);
    $latestExecution = $execStatus['latestExec'];
    foreach($execStatus['metrics'] as $tsuiteID=>$tsuite){
        foreach($tsuite as $tcaseID=>$tcase){
            foreach($tcase as $platformID=>$platform){
                $top = current(array_keys($execStatus['metrics'][$tsuiteID][$tcaseID][$platformID]));
                $rows[$cols['tsuite']] = $platform[$top]['suiteName'];
                $external_id = $args->tcPrefix . $platform[$top]['external_id'];
                $rows[$cols['link']] .= "{$external_id}:{$platform[$top]['name']}";//htmlspecialchars("{$external_id}:{$rf[$top]['name']}",ENT_QUOTES);
                $rows[$cols['priority']] = $platform[$top]['priority_level'];
                foreach($platform as $buildID=>$build){
                    $r4build['text'] = "";
                    $execID = $build['executions_id'];//var_dump($labels);
                    $r4build['text'] = $labels[$build['status']];
                    if($build['status'] != 'n')
                        $r4build['text'] .= "    <a  onclick=\"jQuery('#Nissues').modal('show');document.getElementById('execprint').src = 'http://localhost/testlink/lib/execute/execPrint.php?id=$execID'\" ><img title=\"{$labels['execution']}\" src=\"{$gui->img->exec}\" /></a>";
                    $r4build['value'] = $build['status'];
                    $r4build['cssClass'] = $gui->map_status_css[$build['status']];
                    $buildExecStatus[] = $r4build;
                    if($args->builds->latest->id == $buildID) $execOnLastBuild = $r4build; 
                    //var_dump($latestExecution);
                    if(
                        ($latestExecution[$platformID][$tcaseID]['status'] == 
                        $cfg['results']['status_code']['not_run']) ||
                        ( ($latestExecution[$platformID][$tcaseID]['build_id'] == $buildID) &&                             
                        ($latestExecution[$platformID][$tcaseID]['id'] == $build['executions_id']) )
                    )                  
                    {
                      $lexec = $r4build;
                    }
                    
                }
                
                if ($gui->matrixCfg->buildColumns['showStatusLastExecuted'])
                {
                    $buildExecStatus[] = $execOnLastBuild;
                }//var_dump($gui->matrixCfg->buildColumns['latestBuildOnLeft']);
                if ($gui->matrixCfg->buildColumns['latestBuildOnLeft']) 
                {
                  $buildExecStatus = array_reverse($buildExecStatus);
                }

                $rows = array_merge($rows, $buildExecStatus);
                $rows[] = $lexec;
                $gui->matrix[] = $rows;var_dump($gui->matrix);
                unset($r4build);
                unset($rows);
                unset($buildExecStatus);
                if($cont >10) break;
                $cont++;
            }if($cont >10) break;
        }if($cont >10) break;
    }if($cont >10) break;
}
//$execStatus = $metricsMgr->getExecStatusMatrix($args->tplan_id,$buildSet,$opt);




$smarty->assign('gui',$gui);
$smarty->display($templateCfg->template_dir .$templateCfg->default_template);
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
