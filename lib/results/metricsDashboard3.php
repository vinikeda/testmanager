<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * @filesource  metricsDashboard.php
 * @package     TestLink
 * @copyright   2007-2013, TestLink community 
 * @author      franciscom
 *
 * @internal revisions
 * @since 1.9.9
 *
 **/
require('../../config.inc.php');
require_once('common.php');
require_once('exttable.class.php');
require_once('../functions/tlTestPlanMetrics.class.php');
$templateCfg = templateConfiguration();

//testlinkInitPage($db,false,false,"checkRights");
list($args,$gui) = initEnv($db);
$result_cfg = config_get('results');
$show_all_status_details = config_get('metrics_dashboard')->show_test_plan_status;
$round_precision = config_get('dashboard_precision');

$labels = init_labels(array('overall_progress' => null, 'blank' => null, 'progress' => null,
                            'href_metrics_dashboard' => null, 'progress_absolute' => null,
                            'no_testplans_available' => null, 'not_aplicable' => null,
                            'platform' => null, 'th_active_tc' => null, 'in_percent' => null));

						
list($gui->tplan_metrics,$gui->show_platforms, $platforms) = getMetrics($db,$_SESSION['currentUser'],$args,$result_cfg, $labels);
  
  /*$smarty = new TLSmarty;
  $smarty->assign('gui', $gui);
  $smarty->display($templateCfg->template_dir . $templateCfg->default_template);*/

// new dBug($gui->tplan_metrics);
if(count($gui->tplan_metrics) > 0) 
{
  $statusSetForDisplay = $result_cfg['status_label_for_exec_ui']; 
  $gui->warning_msg = '';
  //$columns = getColumnsDefinition($gui->show_platforms, $statusSetForDisplay, $labels, $platforms);//essa é a forma padrão de criar as colunas da tabela do painel. eu desativei pois assim eu poderia forçar a visualização como se houvessem plataformas.
  $testplannames = array();//variável criada por mim, por algum motivo isso não deu erro, mas tbm não funciona então deixei assim
 
 $columns = getColumnsDefinition(true, $statusSetForDisplay, $labels, $testplannames);//crido por mim para colocar os baselines como plataformas.
  $matrixData = array();
  if(isset($gui->tplan_metrics['testplans']))
  {
    foreach ($gui->tplan_metrics['testplans'] as $tplanid => $tplan_metrics)
    {
		
      foreach($tplan_metrics['platforms'] as $key => $platform_metric) 
      {
        //new dBug($platform_metric);
        
        $rowData = array();
        $tplan_string = strip_tags($platform_metric['tplan_name']);
        $rowData[] = $tplan_string;// aqui ele tá passando o cabeçalho o "nome do plano de teste" e a porcentagem completa do plano de teste
        /*if ($gui->show_platforms) 
        {*/
          $rowData[] = /*strip_tags(*/$platform_metric['platform_name']/*)*/;//foi necessário comentar o strip tags pois ele não me permitia obter o link que eu gerava.
        //}
        foreach ($statusSetForDisplay as $status_verbose => $status_label)
        {
          if( isset($platform_metric[$status_verbose]) )
          {
            $rowData[] = $platform_metric[$status_verbose];
            $rowData[] = getPercentage($platform_metric[$status_verbose], $platform_metric['total'],
                                         $round_precision);
          }
          else
          {
            $rowData[] = 0;
            $rowData[] = 0;
          }
        }

        
        $matrixData[] = $rowData;
      }
    }//fim do foreach
  }
  //new dBug($matrixData);
  $table = new tlExtTable($columns, $matrixData, 'tl_table_metrics_dashboard2');
  // if platforms are to be shown -> group by test plan
  // if no platforms are to be shown -> no grouping
  if($gui->show_platforms) 
  {	
    $table->setGroupByColumnName($labels['blank']);
  }

  $table->setSortByColumnName($labels['progress']);
  $table->sortDirection = 'DESC';

  $table->showToolbar = true;
  $table->toolbarExpandCollapseGroupsButton = false;
  $table->toolbarShowAllColumnsButton = false;
  $table->toolbarResetFiltersButton = false;
  $table->title = $labels['href_metrics_dashboard'];
  $table->showGroupItemsCount = false;
  $gui->tableSet = array($table);
  
  // get overall progress, collect test project metrics
  $gui->project_metrics = collectTestProjectMetrics($gui->tplan_metrics,
                            array('statusSetForDisplay' => $statusSetForDisplay,
                                'round_precision' => $round_precision));
								//trecho colocado por mim

}


$smarty = new TLSmarty;
$smarty->assign('gui', $gui);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);


/**
 *  only active builds has to be used
 *
 *  @internal revisions
 *
 *  
 */
function getMetrics(&$db,$userObj,$args, $result_cfg, $labels)
{
    //$user_id = $args->currentUserID;
    $tproject_id = $args->tproject_id;
    //$linked_tcversions = array();
    $metrics = array();
    $tplan_mgr = new testplan($db);
    //$show_platforms = false;
    $platforms = array();
    $buildmetrics = array();//criado para coletar as metricas por build. o plano é substiruir o que está no metrics antes do return, colocando as builds no lugar das platforms
    // get all tesplans accessibles  for user, for $tproject_id
    $options = array('output' => 'map');
    $options['active'] = $args->show_only_active ? ACTIVE : TP_ALL_STATUS;
    //foreach ($userObj->getAccessibleTestPlans($db,$tproject_id,null,$options) as $campo)print_r(array_keys($campo));
    //pode ser utilizado em versões posteriores para controlar a vizualização apenas para os planos de teste que o user tem acesso. usando a key do vetor
    $test_plans/*$tmp */= $userObj->getAccessibleTestPlans($db,$tproject_id,null,$options);

    $metricsMgr = new tlTestPlanMetrics($db);
    //$show_platforms = false;
    
    $list = $metricsMgr->getExecutionsByOrganizedBuilds($args->tproject_id);
    $result_cfg['status_code'];
    //print_r($list);
    foreach($list as $sub_name=>$sub){
        foreach($sub as $solucao_name=>$solucao){
            foreach ($solucao as $req_name=>$req){
                $execs['tplan_name'] = $sub_name;
                $execs['platform_name'] = $req['solucao'].' - '.$req['roteiro'].' - '.$req['ciclo'];
                foreach($result_cfg['status_code'] as $key=>$stat){
                    $execs[$key] = $req['status'][$stat];
                }
                $execs['total'] = $metricsMgr->getTotalexec($req['tplan_id']);
                $metrics['testplans'][$req['tplan_id']]['platforms'][]= $execs;
            }
        }
    }
    //print_r($execs);
   /* $sublist = $metricsMgr->getSubList($args->tproject_id);
    //var_dump($sublist);
    foreach ($sublist as $sub) {
        $buildlist = $metricsMgr->getExplainedBuildsBySub($args->tproject_id, $sub['name']);
        if ($buildlist != null) {
            //var_dump($sub,$buildlist);
            $buildvalid = array();
            $groupSolucao = array();
            foreach ($buildlist as $build) {
                $groupSolucao[$build['solucao']][] = $build;
                //$buildvalid[$build['solucao']] .= '&buildvalid[]='.$build['id'];
            }
            //var_dump($buildvalid,$groupSolucao);
        }
    }
  
  
  $metrics = array('testplans' => null, 'total' => null);
  $mm = &$metrics['testplans'];
  $metrics['total'] = array('active' => 0,'total' => 0, 'executed' => 0);
  foreach($result_cfg['status_label_for_exec_ui'] as $status_code => &$dummy)
  {
    $metrics['total'][$status_code] = 0; 
  } 
  
  $codeStatusVerbose = array_flip($result_cfg['status_code']);
  foreach($test_plans as $key => &$dummy)
  {
    // We need to know if test plan has builds, if not we can not call any method 
    // that try to get exec info, because you can only execute if you have builds.
    //
    // 20130909 - added active filter
    $buildSet = $tplan_mgr->get_builds($key,testplan::ACTIVE_BUILDS);
    if( is_null($buildSet) )
    {
      continue;
    }

    //colocando as builds como plataformas para que elas apareçam como devem
	$show_platforms = true;
	$buildmetrics[$key] = $metricsMgr->getExecStatusPerBuild($key);//executa a função que faz a busca no banco e faz a organização de  toda a métrica de uma vez.
	if(count($buildmetrics[$key])){
            $metrics['testplans'][$key]['platforms'] = array();
            $metrics['testplans'][$key]['platforms'] = $metrics['testplans'][$key]['platforms']+$buildmetrics[$key];//array_merge($metrics['testplans'][$key]['platforms'],$buildmetrics[$key]);
            $metrics['testplans'][$key]['overall']['total'] = count($buildmetrics[$key]);

            foreach($buildmetrics[$key] as $chaves=>$build){
                    $metrics['testplans'][$key]['overall']['failed'] =+ $build['failed'];
                    $metrics['testplans'][$key]['overall']['passed'] =+ $build['passed'];
                    $metrics['testplans'][$key]['overall']['executed'] =+ $build['executed'];
                    $metrics['testplans'][$key]['overall']['blocked'] =+ $build['failed'];
                    $metrics['testplans'][$key]['overall']['active'] =+ $build['active'];
                    $metrics['testplans'][$key]['overall']['not_run'] =+ $build['not_run'];//echo "   ".$key." -".$build['satanas'];
            }
        }
  }*///fim do foreach dos testplans
    //print_r($metrics);
  return array($metrics, $show_platforms, $platformsUnique);
}

/**
 * 
 *
 */
function getPercentage($denominator, $numerator, $round_precision)
{
  $percentage = ($numerator > 0) ? (round(($denominator / $numerator) * 100,$round_precision)) : 0;

  return $percentage;
}

/**
 * get Columns definition for table to display
 *
 */
function getColumnsDefinition($showPlatforms, $statusLbl, $labels, $platforms)
{
  $colDef = array();
  
  $colDef[] = array('title_key' => 'blank', 'width' => 60, 'type' => 'text', 'sortType' => 'asText',
                    'filter' => 'string');

  if ($showPlatforms)
  {
    $colDef[] = array('title_key' => 'platform', 'width' => 60, 'sortType' => 'asText',
                      'filter' => 'list', 'filterOptions' => $platforms);
  }

  //$colDef[] = array('title_key' => 'link_charts', 'width' => 40, 'type' => 'text', 'filter' => 'string');
  //$colDef[] = array('title_key' => 'th_active_tc', 'width' => 40, 'sortType' => 'asInt', 'filter' => 'numeric');
  //$colDef[] = array('title_key' => 'progress', 'width' => 40, 'sortType' => 'asFloat', 'filter' => 'numeric');
  // create 2 columns for each defined status
  foreach($statusLbl as $lbl)
  {
    $colDef[] = array('title_key' => $lbl, 'width' => 40, 'hidden' => true, 'type' => 'int',
                      'sortType' => 'asInt', 'filter' => 'numeric');
    
    $colDef[] = array('title' => lang_get($lbl) . " " . $labels['in_percent'], 'width' => 40,
                      'col_id' => 'id_'. $lbl .'_percent', 'type' => 'float', 'sortType' => 'asFloat',
                      'filter' => 'numeric');
  }
  
  //print_r($colDef);

  return $colDef;
}

function initEnv(&$dbHandler)
{
  $args = new stdClass();
  $gui = new stdClass();

  $iParams = array("apikey" => array(tlInputParameter::STRING_N,32,64),
                   "tproject_id" => array(tlInputParameter::INT_N), 
                   "tplan_id" => array(tlInputParameter::INT_N),
                   "show_only_active" => array(tlInputParameter::CB_BOOL),
                   "show_only_active_hidden" => array(tlInputParameter::CB_BOOL));

  R_PARAMS($iParams,$args);
  //echo is_null($args->apikey);//vai dar igual a 1
  if( !is_null($args->apikey) )
  {
  
    $args->show_only_active = true;
    $cerbero = new stdClass();
    $cerbero->args = new stdClass();
    $cerbero->args->tproject_id = $args->tproject_id;
    $cerbero->args->tplan_id = $args->tplan_id;
    $cerbero->args->getAccessAttr = true;
    $cerbero->method = 'checkRights';
    $cerbero->redirect_target = "../../login.php?note=logout";
    if(strlen($args->apikey) == 32)
    {
      setUpEnvForRemoteAccess($dbHandler,$args->apikey,$cerbero);
    }
    else//executa por aqui
    {
      setUpEnvForAnonymousAccess($dbHandler,$args->apikey,$cerbero);
    }  

  }
  else
  {
    testlinkInitPage($dbHandler,false,false,"checkRights");  
    $args->tproject_id = isset($_SESSION['testprojectID']) ? intval($_SESSION['testprojectID']) : 0;
  }
  
  if($args->tproject_id <= 0)
  {
    $msg = __FILE__ . '::' . __FUNCTION__ . " :: Invalid Test Project ID ({$args->tproject_id})";
    throw new Exception($msg);
  }
  $mgr = new tree($dbHandler);
  $dummy = $mgr->get_node_hierarchy_info($args->tproject_id);
  $args->tproject_name = $dummy['name'];

  $args->user = $_SESSION['currentUser'];
  $args->currentUserID = $args->user->dbID;
  
  // I'm sorry for MAGIC
  $args->direct_link_ok = true;
  if( strlen(trim($args->user->userApiKey)) == 32)
  {
    $args->direct_link = $_SESSION['basehref'] . "lnl.php?type=metricsdashboard&" .
                         "apikey={$args->user->userApiKey}&tproject_id={$args->tproject_id}";
  }
  else
  {
    $args->direct_link_ok = false;
    $args->direct_link = lang_get('can_not_create_direct_link');
  }  

  if ($args->show_only_active) 
  {
    $selection = true;
  } 
  else if ($args->show_only_active_hidden) 
  {
    $selection = false;
  } 
  else if (isset($_SESSION['show_only_active'])) 
  {
    $selection = $_SESSION['show_only_active'];
  } 
  else 
  {
    $selection = true;
  }
  $args->show_only_active = $_SESSION['show_only_active'] = $selection;
  

  $gui->tproject_name = $args->tproject_name;
  $gui->show_only_active = $args->show_only_active;
  $gui->direct_link = $args->direct_link;
  $gui->direct_link_ok = $args->direct_link_ok;
  $gui->warning_msg = lang_get('no_testplans_available');

  return array($args,$gui);
}


/**
 *
 */
function collectTestProjectMetrics($tplanMetrics,$cfg)
{
  $mm = array();
  $mm['executed']['value'] = getPercentage($tplanMetrics['total']['executed'], 
                                           $tplanMetrics['total']['active'], $cfg['round_precision']);
  $mm['executed']['label_key'] = 'progress_absolute';

  foreach ($cfg['statusSetForDisplay'] as $status_verbose => $label_key)
  {
    $mm[$status_verbose]['value'] = getPercentage($tplanMetrics['total'][$status_verbose], 
                                                    $tplanMetrics['total']['active'], $cfg['round_precision']);
    $mm[$status_verbose]['label_key'] = $label_key;
  }
  return $mm;
}

/**
 *
 */
function checkRights(&$db,&$user,$context = null)
{
  if(is_null($context))
  {
    $context = new stdClass();
    $context->tproject_id = $context->tplan_id = null;
    $context->getAccessAttr = false; 
  }
  $checkOrMode = array('testplan_metrics','testplan_execute');
  foreach($checkOrMode as $right)
  {
    if( $user->hasRight($db,$right,$context->tproject_id,$context->tplan_id,$context->getAccessAttr) )
    {
      return true;  
    }
  }  
  return false;
}
?>