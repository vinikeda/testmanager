<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * @filesource	editExecution.php
 *
 * Edit an execution notes and custom fields
 * @since 1.9.14
 * 
**/
require_once('../../config.inc.php');
require_once('common.php');
require_once('exec.inc.php');
require_once("web_editor.php");
require_once("../issue/issues.class.php");
$editorCfg = getWebEditorCfg('edit_execution');
require_once(require_web_editor($editorCfg['type']));
testlinkInitPage($db,false,false,"checkRights");
$templateCfg = templateConfiguration();
$tcase_mgr = new testcase($db);
$args = init_args();
$owebeditor = web_editor('notes',$args->basehref,$editorCfg);
switch ($args->doAction)
{
  case 'edit':
	break;
        
  case 'doUpdate':
    doUpdate($db,$args,$tcase_mgr,$_REQUEST);
  break;  
}
$map = get_execution($db,$args->exec_id);
$owebeditor->Value = $map[0]['notes'];
$stat = $map[0]['status'];//criei para passar o status para o editStatus.tpl usar
// order on script is critic 
$gui = initializeGui($args,$tcase_mgr);
$cols = intval(isset($editorCfg['cols']) ? $editorCfg['cols'] : 60);
$rows = intval(isset($editorCfg['rows']) ? $editorCfg['rows'] : 10); 
$gui->notes = $owebeditor->CreateHTML(8,84);//$owebeditor->CreateHTML($rows,$cols);
$gui->editorType = $editorCfg['type'];
$gui->execStatusValues = createResultsMenu();//carregando a relação das siglas dos status com os status
$gui->execStatusValues[$cfgObj->tc_status['not_run']] = '';
$issue = new issues($db);
$temp =  ($issue->getAssignedIssue($args->exec_id));$a;
foreach($temp as $chave=>$valor)$a[$valor['id_issue']] = 1;
$gui->selectedIssues =$a;
//inicio do trecho que irá adicionar os steps para edição
$steps = get_execution_steps($db,$args->exec_id);//nessa linha é carregado em forma de matriz o SELECT que traz as notas e os status dos steps 
//$resultsCfg = config_get('results');// não lembro, depois procurarei o que isto faz
//fim do trecho
  setupIssues($gui,$db);
$smarty = new TLSmarty();
$smarty->assign('gui',$gui);
$smarty->assign('stat',$stat);
$smarty->assign('steps',$steps);//também inserido por mim, passará uma variável para o tpl o que será necessário
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);


/**
 *
 */
function doUpdate(&$db,&$args,&$tcaseMgr,&$request)
{
    $issue = new issues($db);
    $issue->reassignIssue($args->exec_id, $args->issues);
	updateExecutionNotes($db,$args->exec_id,$args->notes);
    updateExecutionNotesSteps($db,$_REQUEST);//função criada para realizar os updates nas notas dos steps e nos status deles
	updateStatus($db,$_REQUEST['executionStatus'],$args->exec_id);
 	$cfield_mgr = new cfield_mgr($db);
	$cfield_mgr->execution_values_to_db($request,$args->tcversion_id,$args->exec_id,$args->tplan_id);
	echo "<script>window.close();</script>";
}

/**
 *
 */
function init_args()
{
  // Take care of proper escaping when magic_quotes_gpc is enabled
  $_REQUEST=strings_stripSlashes($_REQUEST);

	$iParams = array("exec_id" => array(tlInputParameter::INT_N),
					 "doAction" => array(tlInputParameter::STRING_N,0,100),
					 "notes" => array(tlInputParameter::STRING_N),
					 "tcversion_id" => array(tlInputParameter::INT_N),
					 "tplan_id" => array(tlInputParameter::INT_N),
					 "tproject_id" => array(tlInputParameter::INT_N));

	$args = new stdClass();
  R_PARAMS($iParams,$args);
  $args->basehref = $_SESSION['basehref'];
  $args->issues =$_REQUEST['issue'];
  $args->user = $_SESSION['currentUser'];

  return $args; 
}

/**
 *
 */
function initializeGui(&$argsObj,&$tcaseMgr)
{
  $guiObj = new stdClass();
  $guiObj->exec_id = $argsObj->exec_id;
  $guiObj->tcversion_id = $argsObj->tcversion_id;
  $guiObj->tplan_id = $argsObj->tplan_id;
  $guiObj->tproject_id = $argsObj->tproject_id;
  $guiObj->edit_enabled = $argsObj->user->hasRight($db,"exec_edit_notes") == 'yes' ? 1 : 0;
  $guiObj->cfields_exec = $tcaseMgr->html_table_of_custom_field_inputs($argsObj->tcversion_id,null,'execution','_cf',
                                                                       $argsObj->exec_id,$argsObj->tplan_id,$argsObj->tproject_id);
  return $guiObj;
}

/**
 * Checks the user rights for viewing the page
 * 
 * @param $db resource the database connection handle
 * @param $user tlUser the object of the current user
 *
 * @return boolean return true if the page can be viewed, false if not
 */
function checkRights(&$db,&$user)
{
	return $user->hasRight($db,"testplan_execute") && $user->hasRight($db,"exec_edit_notes");
}