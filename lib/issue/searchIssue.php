<?php
require_once('../../config.inc.php');
require_once('common.php');
require_once('exec.inc.php');
require_once("attachments.inc.php");
require_once("specview.php");
require_once("web_editor.php");
require_once("issues.class.php");
require_once("categories.class.php");
require_once("markers.class.php");
testlinkInitPage($db,false,false,"checkRights");
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$templateCfg = templateConfiguration();
$categories  = new categories($db);
$markers = new markers($db);
$issues = new issues($db);

$categoriesList = $categories->getCategoriesForSelect();
$gui->Categories = $categoriesList;
if(isset($_SESSION['selectedCategoryID'])){
    $gui->issues = $issues->getIssuesByCategory($_SESSION['selectedCategoryID']);
    if(isset($_SESSION['selectedMarkersID'])){
        $gui->issues= $issues->getIssuesByMarksAndCategories($_SESSION['selectedCategoryID'], $_SESSION['selectedMarkersID']);
        
    }
}



$smarty = new TLSmarty();
$smarty->assign('gui', $gui);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);



function checkRights(&$db,&$user)
{
	return $user->hasRight($db,'testplan_create_build');
}
