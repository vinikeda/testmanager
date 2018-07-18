<?php

require_once(__DIR__."/../../functions/genericView.php");

$list = new genericCRUD($db,'subadquirentes');
//var_dump($_SESSION);
//$list->
$gui->list = $list->getPerFieldValue('testproject_id',$_SESSION['testprojectID']);
displayDefault($gui,"sub_adquirente","lib/dtStruct/sub/subEdit.php");