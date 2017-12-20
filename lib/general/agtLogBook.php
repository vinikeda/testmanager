<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * @filesource	agtLogBook.php
 * @author      Martin Havlat
 * 
 * Page has two functions: navigation and select Test Plan
 *
 * This file is the first page that the user sees when they log in.
 * Most of the code in it is html but there is some logic that displays
 * based upon the login. 
 * There is also some javascript that handles the form information.
 *
 * @internal revisions
 * @since 1.9.10
 *
 **/

require_once('../../config.inc.php');
require_once('common.php');

if(function_exists('memory_get_usage') && function_exists('memory_get_peak_usage'))
{
  tlog("agtLogBook.php: Memory after common.php> Usage: ".memory_get_usage(), 'DEBUG');
}

testlinkInitPage($db,TRUE);

$smarty = new TLSmarty();
$tproject_mgr = new testproject($db);
$user = $_SESSION['currentUser'];

$testprojectID = isset($_SESSION['testprojectID']) ? intval($_SESSION['testprojectID']) : 0;
$testplanID = isset($_SESSION['testplanID']) ? intval($_SESSION['testplanID']) : 0;

$accessibleItems = $tproject_mgr->get_accessible_for_user($user->dbID,array('output' => 'map_name_with_inactive_mark'));
$tprojectQty = $tproject_mgr->getItemCount();
$userIsBlindFolded = (is_null($accessibleItems) || count($accessibleItems) == 0) && $tprojectQty > 0;
if($userIsBlindFolded)
{
  $testprojectID = $testplanID = 0;
  $_SESSION['testprojectTopMenu'] = '';
}

$tplan2check = null;
$currentUser = $_SESSION['currentUser'];
$userID = $currentUser->dbID;

$gui->testplanRole = null;
if ($testplanID && isset($currentUser->tplanRoles[$testplanID]))
{
	$role = $currentUser->tplanRoles[$testplanID];
	$gui->testplanRole = $tlCfg->gui->role_separator_open . $role->getDisplayName() . $tlCfg->gui->role_separator_close;
}

$rights2check = array('testplan_execute','testplan_create_build','testplan_metrics','testplan_planning',
                      'testplan_user_role_assignment','mgt_testplan_create','cfield_view', 'cfield_management',
                      'testplan_milestone_overview','exec_testcases_assigned_to_me',
                      'testplan_add_remove_platforms','testplan_update_linked_testcase_versions',
                      'testplan_set_urgent_testcases','testplan_show_testcases_newest_versions');

                         
$gui->docs = config_get('userDocOnDesktop') ? getUserDocumentation() : null;

$secCfg = config_get('config_check_warning_frequence');

$gui->opt_requirements = isset($_SESSION['testprojectOptions']->requirementsEnabled) ? 
                         $_SESSION['testprojectOptions']->requirementsEnabled : null; 

$smarty->assign('gui',$gui);
$smarty->display('agt_mainPage.tpl');

/**
 * Get User Documentation 
 * based on contribution by Eugenia Drosdezki
 */
function getUserDocumentation()
{
  $target_dir = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'docs';
  $documents = null;
    
  if ($handle = opendir($target_dir)) 
  {
    while (false !== ($file = readdir($handle))) 
    {
      clearstatcache();
      if (($file != ".") && ($file != "..")) 
      {
        if (is_file($target_dir . DIRECTORY_SEPARATOR . $file))
        {
          $documents[] = $file;
        }    
      }
    }
    closedir($handle);
  }
  return $documents;
}

/**
 *
 */
function getGrants($dbHandler,$user,$forceToNo=false)
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
                       'reqmgrsystem_management' => "reqmgrsystem_management",
                       'reqmgrsystem_view' => "reqmgrsystem_view",
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