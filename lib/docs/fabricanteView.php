<?php
require('../../config.inc.php');
require_once("common.php");
require_once("fabricante.class.php");
testlinkInitPage($db,false,false,"checkRights");

$templateCfg = templateConfiguration();

$categories = new fabricantes($db);
$gui = new StdClass();
$gui->categories = $categories->getCategories();
$gui->user_feedback = null;

$smarty = new TLSmarty();
$smarty->assign('gui', $gui);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);

function checkRights(&$db,&$user)
{
	return $user->hasRight($db,'testplan_create_build');
}

?>
