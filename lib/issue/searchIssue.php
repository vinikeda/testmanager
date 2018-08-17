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

$categoryID = $_REQUEST['category'];
$markersID =  $_REQUEST['markersID'];


if(isset($markersID)) {//com marcadores
    if($categoryID != 0) /*com categoria*/ $isslist =($issues->getIssuesByMarksAndCategories($categoryID, $markersID));
    else /*sem categoria*/ $isslist =($issues->getIssuesByMarks( $markersID));;
    
}else if($categoryID != 0) /*com categoria*/ $isslist =($issues->getIssuesByCategory($categoryID));
else /*sem marcador nem categoria*/ $isslist =($issues->getIssues());

    foreach($isslist as $chade=>$item){
        $isslist[$chade]['adjusted_text_description'] =str_replace("\r\n", "\\n",$isslist[$chade]['text_description']);
    }
    //$gui->issues = $isslist;
$iss = '';
echo json_encode($isslist);
foreach($isslist as $issue){
    $description = $issue[description];
    $id = $issue[id];
    $iss .= "<div id=\"issr$description\"><input id=\"issr2$description\" type=\"checkbox\" name=\"issue['$id']\" >$description</div><br id = \"issr$description\">";
}
//print_r($isslist);
//echo $iss;
/*
$smarty = new TLSmarty();
$smarty->assign('gui', $gui);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);
*/


function checkRights(&$db,&$user)
{
	return $user->hasRight($db,'testplan_create_build');
}
