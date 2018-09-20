<?php
require_once('../../config.inc.php');
require_once('../functions/common.php');
require_once('../functions/attachments.inc.php');
testlinkInitPage($db,false,true);
$smarty = new TLSmarty();
$smarty->assign('gui',$args);
$smarty->display('attachmentempty.tpl');
?>		