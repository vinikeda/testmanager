<?php
require_once('../../config.inc.php');
require_once('users.inc.php');
testlinkInitPage($db,false,false,"checkRights");


//$tprojectMgr = new testproject($db);


$userID = $_GET['usr'];
$roleID = $_GET['role'];
$projectID = $_GET['project'];
$type = $_GET['type'];
//$userID = $_GET['usr'];

switch($type){
    case 'project':
        $sql = "insert into `user_testproject_roles` SELECT '$userID' as `user_id` ,id 'testproject_id',$roleID as 'role_id' FROM (select * from `user_testproject_roles` where user_id = $userID)b right join testprojects on (id = b.testproject_id) where isnull(role_id)";
        $db->exec_query($sql);
    break;
    case 'plan':
        $sql = "insert into `user_testplan_roles` SELECT '$userID' as `user_id` ,id 'testplan_id',$roleID as 'role_id' FROM (select * from `user_testplan_roles` where user_id = $userID)b right join testplans on (id = b.testplan_id) where isnull(role_id) and testplans.testproject_id = $projectID";
        $db->exec_query($sql);
    break;
    
}
echo  "<script> window.open('lib/usermanagement/usersAssign.php?featureType=testproject','_self);</script>";
/*push it to the limit scarface*/






















function checkRights(&$db,&$user)
{
  return $user->hasRight($db,'testplan_create_build');
}