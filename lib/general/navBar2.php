<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 *
 * @filesource  navBar.php
 *
 * This file manages the navigation bar. 
 *
 * internal revisions
 *
**/

/*
esse arquivo foi feito para reescrever a forma que é gerado o navbar para que o mesmo saia do sistema de frames.ele foi baseado no navBar.php.
a idéia aqui é que ele faça as mesmas verificações, mas sem utilizar o frame. 
*/
//require_once('../../config.inc.php');
//require_once("common.php");
//require('../../config.inc.php');
require_once("common.php");
require_once("subadiq.class.php");
function initNavBar(){
	//$beforeproject = intval(isset($_SESSION['testprojectID']) ? $_SESSION['testprojectID'] : 0);falho
	testlinkInitPage($db,('initProject' == 'initProject'));
	
	$tproject_mgr = new testproject($db);
	$args = init_args();//echo $_SESSION['sub_adquirenteID'];
	
	$gui = new stdClass();
	$gui_cfg = config_get("gui");
	//print_r(config_get('guiTopMenu'));
	$gui->tprojectID = intval(isset($_SESSION['testprojectID']) ? $_SESSION['testprojectID'] : 0);
	//if(isset($args->sub_adquirenteID)) $_SESSION['sub_adquirenteID'] = intval($args->sub_adquirenteID);
	
	//$gui->subadiqID = $_SESSION['sub_adquirenteID'];
	$testprojectID = $gui->tprojectID;
	//$subadiqID = $gui->subadiqID;
	$gui->tcasePrefix = '';
	$gui->searchSize = 8;
	if($gui->tprojectID > 0)
	{
		$gui->tcasePrefix = $tproject_mgr->getTestCasePrefix($gui->tprojectID) . config_get('testcase_cfg')->glue_character;
		$gui->searchSize = tlStringLen($gui->tcasePrefix) + $gui_cfg->dynamic_quick_tcase_search_input_size;
	}
	$gui->TestProjects = $tproject_mgr->get_accessible_for_user($args->user->dbID,
                                                                    array('output' => 'map_name_with_inactive_mark',
                                                                              'field_set' => $tlCfg->gui->tprojects_combo_format,
                                                                              'order_by' => ' order by name'));



	$gui->TestProjectCount = sizeof($gui->TestProjects);
	$gui->TestPlanCount = 0; 

	$tprojectQty = $tproject_mgr->getItemCount();
	if($gui->TestProjectCount == 0 && $tprojectQty > 0)
	{
	  // User rights configurations does not allow access to ANY test project
	  $_SESSION['testprojectTopMenu'] = '';
	  $_SESSION['testprojectTopMenu2'] = '';
	  $gui->tprojectID = 0;
	}

	if($gui->tprojectID)
	{
		$testPlanSet = $args->user->getAccessibleTestPlans($db,$gui->tprojectID);//print_r( $args->user->getAccessibleTestPlans($db,$gui->tprojectID));
	  $gui->TestPlanCount = sizeof($testPlanSet);

		$tplanID = isset($_SESSION['testplanID']) ? intval($_SESSION['testplanID']) : null;
		//$gui->subadiqID = ($_SESSION['sub_adquirenteID'] > $gui->TestPlanCount)?0:$_SESSION['sub_adquirenteID'];
		$testplanID = $tplanID;
	  if( !is_null($tplanID) )
	  {
		// Need to set this info on session with first Test Plan from $testPlanSet
			// if this test plan is present on $testPlanSet
			//	  OK we will set it on $testPlanSet as selected one.
			// else 
			//    need to set test plan on session
			//
			$index=0;
			$testPlanFound=0;
			$loop2do=count($testPlanSet);
			for($idx=0; $idx < $loop2do; $idx++)
			{
			if( $testPlanSet[$idx]['id'] == $tplanID )
			{
			$testPlanFound = 1;
			  $index = $idx;
			  $break;
			}
		}
		if( $testPlanFound == 0 )
		{
				$tplanID = $testPlanSet[0]['id'];
				setSessionTestPlan($testPlanSet[0]);     	
		} 
		$testPlanSet[$index]['selected']=1;
	  }
	}	

	if ($gui->tprojectID && isset($args->user->tprojectRoles[$gui->tprojectID]))
	{
		// test project specific role applied
		$role = $args->user->tprojectRoles[$gui->tprojectID];
		$testprojectRole = $role->getDisplayName();
	}
	else
	{
		// general role applied
		$testprojectRole = $args->user->globalRole->getDisplayName();
	}	
	$gui->whoami = $args->user->getDisplayName() . ' ' . $tlCfg->gui->role_separator_open . 
					 $testprojectRole . $tlCfg->gui->role_separator_close;
	$gui->userName = $args->user->getDisplayName();
	$gui->userRole = $testprojectRole;
					   

	// only when the user has changed project using the combo the _GET has this key.
	// Use this clue to launch a refresh of other frames present on the screen
	// using the onload HTML body attribute
	$gui->updateMainPage = 0;
	if ($args->testproject)
	{
	  // set test project ID for the next session
		$gui->updateMainPage = is_null($args->caller);
		setcookie('TL_lastTestProjectForUserID_'. $args->user->dbID, $args->testproject, TL_COOKIE_KEEPTIME, '/');
	}

	$gui->grants = getGrants($db,$args->user);
	/*---------------------------------inicio do trecho que faz a fusão dos dados que são necessários do mainPageBar*/
	$gui->num_active_tplans = $tproject_mgr->getActiveTestPlansCount($testprojectID);
	$currentUser = $_SESSION['currentUser'];
	
	//$subadiq = $currentUser->getAccessibleSub_adquirentes($db,$testprojectID);
//var_dump($subadiq);
	/*if(!isset($_SESSION['sub_adquirenteID']) || $_SESSION['sub_adquirenteID'] == null){//uma gambiarra para selecionar o primeiro sub adquirente caso não exista um definido na sessão ou se o definido na sessão não está presente na lista.
		foreach($subadiq as $chave=>&$valor){
			$_SESSION['sub_adquirenteID'] = $chave;break;
		}
	}
	if(!isset($subadiq[$_SESSION['sub_adquirenteID']]) || $_SESSION['sub_adquirenteID'] == null){
		foreach($subadiq as $chave=>&$valor){
			$_SESSION['sub_adquirenteID'] = $chave;break;
		}		
	}*///var_dump($subadiq);
	//echo $_SESSION['sub_adquirenteID'];
	/*$gui->subadiqID = $_SESSION['sub_adquirenteID'];
	$_SESSION['sub_adquirenteID']= ($_SESSION['sub_adquirenteID']==null)?0:$_SESSION['sub_adquirenteID'];*/
	$arrPlans = $currentUser->getAccessibleTestPlans($db,$testprojectID);//var_dump($currentUser);
        //var_dump($currentUser->getAccessibleTestPlans($db,$testprojectID),$testprojectID);
	//$arrPlans = $currentUser->getAccessibleTestPlansFilteringBySubadiq($db,$testprojectID,$_SESSION['sub_adquirenteID']);//var_dump($currentUser);
	/*if($currentUser->getEffectiveRole($testprojectID)->dbID == 8){//se for admin pode ver os sub_adquirentes vazios
		$subadiq_mgr = new subadiq_mgr($db);//echo $testprojectID;
		$temp = $subadiq_mgr->get_empty_subadiqs($testprojectID);
		if($temp != null) foreach($temp as $key=>$value)$subadiq[$key]= $value;
	}*///echo ;
	if($testplanID > 0)
	{
		// if this test plan is present on $arrPlans
		//	  OK we will set it on $arrPlans as selected one.
		// else 
		//    need to set test plan on session
		//
		$index=0;
		$found=0;
		$loop2do=count($arrPlans);
		for($idx=0; $idx < $loop2do; $idx++)
		{
		if( $arrPlans[$idx]['id'] == $testplanID )
		{
			$found = 1;
			$index = $idx;
			$break;
		}
	  }
	  if( $found == 0 )
	  {
		// update test plan id
		$testplanID = $arrPlans[0]['id'];
		  setSessionTestPlan($arrPlans[0]);     	
	  } 
	  $arrPlans[$index]['selected']=1;
	}
	//echo $currentUser->getSub_adquirentesID($testplanID);//'tplID: '.$testplanID;//if($testplanID)$gui->subadiqID = $currentUser->getSub_adquirentesID($testplanID);
	//$gui->subadiq = $subadiq;
	$gui->arrPlans = $arrPlans;                   
	$gui->countPlans = count($gui->arrPlans);
	$gui->testplanRole = null;
	if ($testplanID && isset($currentUser->tplanRoles[$testplanID]))
	{
		$role = $currentUser->tplanRoles[$testplanID];
		$gui->testplanRole = $tlCfg->gui->role_separator_open . $role->getDisplayName() . $tlCfg->gui->role_separator_close;
	}
	$gui->testprojectID = $testprojectID;
	$gui->opt_requirements = isset($_SESSION['testprojectOptions']->requirementsEnabled) ? 
							 $_SESSION['testprojectOptions']->requirementsEnabled : null; 
	$gui->hasKeywords = false;
	if($gui->hasTestCases)
	{
	  $gui->hasKeywords = $tproject_mgr->hasKeywords($testprojectID);
	} 
	$rights2check = array('testplan_execute','testplan_create_build','testplan_metrics','testplan_planning',
						  'testplan_user_role_assignment','mgt_testplan_create','cfield_view', 'cfield_management',
						  'testplan_milestone_overview','exec_testcases_assigned_to_me',
						  'testplan_add_remove_platforms','testplan_update_linked_testcase_versions',
						  'testplan_set_urgent_testcases','testplan_show_testcases_newest_versions');

	foreach($rights2check as $key => $the_right)
	{
	  $gui->grants[$the_right] = $userIsBlindFolded ? 'no' : $currentUser->hasRight($db,$the_right,$testprojectID,$testplanID);
	}
							 
	$gui->grants['tproject_user_role_assignment'] = "no";
	if( $currentUser->hasRight($db,"testproject_user_role_assignment",$testprojectID,-1) == "yes" ||
		$currentUser->hasRight($db,"user_role_assignment",null,-1) == "yes" )
	{ 
		$gui->grants['tproject_user_role_assignment'] = "yes";
	}
	/*---------------------------------fim do trecho que faz a fusão dos dados que são necessários do mainPageBar*/
	//$smarty = new TLSmarty();
	//$smarty->assign('gui',$gui);//print_r($_SESSION[testprojectTopMenu2]);
	//$smarty->display('navBar.tpl');

	return $gui;
}

/**
 * 
 *//*
function getGrants(&$db,&$userObj)
{
  $grants = new stdClass();
  $grants->view_testcase_spec = $userObj->hasRight($db,"mgt_view_tc");
  return $grants;  
}*/
function getGrants($dbHandler,$user,$forceToNo=false)//a função getGrants foi substituída por uma função de mesmo nome mas do arquivo mainPage.php, pois ela complementa as liberações de permissão do mainNavBar.tpl
{
  // User has test project rights
  // This talks about Default/Global
  //
  // key: more or less verbose
  // value: string present on rights table
  $right2check = array('project_edit' => 'mgt_modify_product',
                       'reqs_view' => "mgt_view_req", 
                       'reqs_edit' => "mgt_modify_req",
                       'keywords_view' => "mgt_view_key",
                       'keywords_edit' => "mgt_modify_key",
                       'platform_management' => "platform_management",
                       'issuetracker_management' => "issuetracker_management",
                       'issuetracker_view' => "issuetracker_view",
                       // 'reqmgrsystem_management' => "reqmgrsystem_management",
                       // 'reqmgrsystem_view' => "reqmgrsystem_view",
                       'configuration' => "system_configuraton",
                       'usergroups' => "mgt_view_usergroups",
                       'view_tc' => "mgt_view_tc",
                       'view_testcase_spec' => "mgt_view_tc",
                       'project_inventory_view' => 'project_inventory_view',
                       'modify_tc' => 'mgt_modify_tc',
                       'exec_edit_notes' => 'exec_edit_notes', 'exec_delete' => 'exec_delete',
                       'testplan_unlink_executed_testcases' => 'testplan_unlink_executed_testcases',
                       'testproject_delete_executed_testcases' => 'testproject_delete_executed_testcases');
 if($forceToNo)
 {
    $grants = array_fill_keys(array_keys($right2check), 'no');
    return $grants;      
 }  
  
  
 $grants['project_edit'] = $user->hasRight($dbHandler,$right2check['project_edit']); 

  /** redirect admin to create testproject if not found */
  if ($grants['project_edit'] && !isset($_SESSION['testprojectID']))
  {
	  tLog('No project found: Assume a new installation and redirect to create it','WARNING'); 
	  redirect($_SESSION['basehref'] . 'lib/project/projectEdit.php?doAction=create');
	  exit();
  }
  
  foreach($right2check as $humankey => $right)
  {
    $grants[$humankey] = $user->hasRight($dbHandler,$right); 
  }


  $grants['project_inventory_view'] = ($_SESSION['testprojectOptions']->inventoryEnabled && 
                                      ($user->hasRight($dbHandler,"project_inventory_view") == 'yes')) ? 1 : 0;

  return $grants;  
}
function init_args()
{ 
	$iParams = array("testproject" => array(tlInputParameter::INT_N),"sub_adquirenteID" => array(tlInputParameter::INT_N),
                   "caller" => array(tlInputParameter::STRING_N,1,6));
	$args = new stdClass();
	$pParams = R_PARAMS($iParams,$args);

  $args->user = $_SESSION['currentUser'];
	return $args;
}
