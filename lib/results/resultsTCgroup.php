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
//var_dump($_SESSION['testplanID']);
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
$tproject_mgr = new testproject($db);
 $tproject_info = $tproject_mgr->get_by_id($args->tproject_id);
$gui->tproject_name = $tproject_info['name'];//var_dump($gui);
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