<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * Filename $RCSfile: buildView.php,v $
 *
 * @version $Revision: 1.14 $
 * @modified $Date: 2009/06/10 19:36:00 $ $Author: franciscom $
 *
 * rev:
 *      20090509 - franciscom - minor refactoring      
 *       
 *
*/

require('../../config.inc.php');
require_once("common.php");
require_once("issues.class.php");
testlinkInitPage($db,false,false,"checkRights");

$templateCfg = templateConfiguration();

$categories = new issues($db);
$gui = new StdClass();
$gui->issues = $categories->getIssues();
$gui->isQA = ($_SESSION['currentUser']->globalRole->dbID === '13'||$_SESSION['currentUser']->globalRole->dbID === '11'?1:0);
$gui->isADM= ($_SESSION['currentUser']->globalRole->dbID ===  '8'?1:0);
$gui->isSUP= ($_SESSION['currentUser']->globalRole->dbID === '12'?1:0);
$gui->isAnalist = ($_SESSION['currentUser']->user->globalRole->dbID === '9'?1:0 );
//var_dump($gui->issues);
$gui->user_feedback = null;

$smarty = new TLSmarty();
$smarty->assign('gui', $gui);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);

function checkRights(&$db,&$user)
{
	return $user->hasRight($db,'testplan_create_build');
}

?>
