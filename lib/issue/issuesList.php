<?php
require('../../config.inc.php');
require_once("common.php");
require_once("issues.class.php");
testlinkInitPage($db,false,false,"checkRights");

$categories = new issues($db);
echo json_encode($categories->getActiveIssues());

function checkRights(&$db,&$user)
{
    return $user->hasRight($db,'testplan_create_build');
}