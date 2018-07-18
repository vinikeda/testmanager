<?php

require(__DIR__.'/../../config.inc.php');
require_once("common.php");
require_once("genericCRUD.php");
//teste();

testlinkInitPage($db,false,false,"checkRights");



$gui = new StdClass();
/*$tableObj = new genericCRUD($db);

$gui->list = $tableObj->getCategories();
$gui->user_feedback = null;

$smarty = new TLSmarty();
$smarty->assign('gui', $gui);
//echo $templateCfg->template_dir .' - '. $templateCfg->default_template;

$smarty->display($templateCfg->default_template);*/


/**
 * function Display
 * 
 * parameters:
 * 
 *      vars: an array of values to be assigned on smarty, its keys will be their assigned names
 *  */
function display($vars){
    $templateCfg = templateConfiguration();
    $smarty = new TLSmarty();
    foreach($vars as $key=>$var){
        $smarty->assign($key, $var);
    }
    $smarty->assign('gui', $gui);
    //echo $templateCfg->template_dir .' - '. $templateCfg->default_template;

    $smarty->display($templateCfg->template_dir .$templateCfg->default_template);
}

/**
 * 
 */
function displayDefault($gui,$title,$editFile,$fieldListed = 'name',$fieldID = 'id'){
    $templateCfg = templateConfiguration();
    $smarty = new TLSmarty();
    $gui->title = $title;
    $gui->editFile = $editFile;
    $gui->fieldListed = $fieldListed;
    $gui->fieldID = $fieldID;
    $smarty->assign('gui', $gui);
    //echo $templateCfg->template_dir .' - '. $templateCfg->default_template;

    $smarty->display('genericView.tpl');
}

function checkRights(&$db,&$user)
{
    return $user->hasRight($db,'testplan_create_build');
}