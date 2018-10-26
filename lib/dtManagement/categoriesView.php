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
require_once("categories.class.php");
testlinkInitPage($db,false,false,"checkRights");

$templateCfg = templateConfiguration();

$categories = new categories($db);
$gui = new StdClass();
$gui->categories = $categories->getDashboardByTestproject($_SESSION['testprojectID'],$_SESSION['testplanName']);
$gui->user_feedback = null;
//var_dump($_SESSION['testplanName']);
$smarty = new TLSmarty();
$smarty->assign('gui', $gui);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);

function checkRights(&$db,&$user)
{
	return true;
}

?>
