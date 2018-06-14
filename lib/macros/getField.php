<?php
require_once("macros.class.php");
require_once('../../config.inc.php');
require_once('common.php');

$ID = $_REQUEST['macro_id'];
testlinkInitPage($db,false,false,"checkRights");

$docs = new macros($db);
$docsArray = $docs->getBlankField($ID);
echo json_encode($docsArray);

function checkRights(&$db,&$user)
{
    return $user->hasRight($db,'testplan_create_build');
}