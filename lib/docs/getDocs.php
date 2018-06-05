<?php
require_once("docs.class.php");
require_once('../../config.inc.php');
require_once('common.php');

$doc_typeID = $_REQUEST['doc_type'];
testlinkInitPage($db,false,false,"checkRights");

$docs = new docs($db);
$docsArray = $docs->getDocsPerType($doc_typeID);
//print_r($docsArray);
echo json_encode($docsArray);

function checkRights(&$db,&$user)
{
    return $user->hasRight($db,'testplan_create_build');
}