<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * @filesource attachmentupload.php,v $
 *
 * Upload dialog for attachments
 * Multiple file upload via HTML5 attribute
**/

require_once('../../config.inc.php');
require_once('../functions/common.php');
require_once('../functions/attachments.inc.php');
testlinkInitPage($db,false,false,"checkRights");
  
$args = init_args();
$gui = new stdClass();
$gui->uploaded = false;
$gui->msg = null;
$gui->tableName = $args->tableName;
$gui->import_limit = TL_REPOSITORY_MAXFILESIZE;
$gui->id = $args->id;


if ($args->bPostBack)	
{
	$id = $_SESSION['s_upload_id'];
	$gui->tableName = $_SESSION['s_upload_tableName'];
	$attachmentRepository = tlAttachmentRepository::create($db);
	if( isset($_FILES['uploadedFile']['name']['log']) && !is_null($_FILES['uploadedFile']['name']['log'])) 
       {
        // May be we have enabled MULTIPLE on file upload
        if( is_array($_FILES['uploadedFile']['name']['log'])) 
            {
                $curly = count($_FILES['uploadedFile']['name']['log']);
                for($moe=0; $moe < $curly; $moe++)
                {
                  $fSize = isset($_FILES['uploadedFile']['size']['log'][$moe]) ? 
                           $_FILES['uploadedFile']['size']['log'][$moe] : 0;

                  $fTmpName = isset($_FILES['uploadedFile']['tmp_name']['log'][$moe]) ? 
                              $_FILES['uploadedFile']['tmp_name']['log'][$moe] : '';

                  if ($fSize && $fTmpName != "")
                  {
                    $fk2loop = array_keys($_FILES['uploadedFile']);
                    foreach($fk2loop as $tk)
                    {
                      $fInfo[$tk] = $_FILES['uploadedFile'][$tk]['log'][$moe];
                    }  
                     $gui->uploaded = $attachmentRepository->insertAttachment($id,$gui->tableName,'Log',$fInfo);
                  }
                }  
            } 
        else
            {
                $fSize = isset($_FILES['uploadedFile']['size']['log']) ? $_FILES['uploadedFile']['size']['log'] : 0;
                $fTmpName = isset($_FILES['uploadedFile']['tmp_name']['log']) ? 
                            $_FILES['uploadedFile']['tmp_name']['log'] : '';

                if ($fSize && $fTmpName != "")
                {
                  $fk2loop = array_keys($_FILES['uploadedFile']);
                  foreach($fk2loop as $tk)
                  {
                    $fInfo[$tk] = $_FILES['uploadedFile']['log'][$tk];
                  }  
                   $gui->uploaded = $attachmentRepository->insertAttachment($id,$gui->tableName,'Logs',$fInfo);
                }
            } 
              
        }
	if( isset($_FILES['uploadedFile']['name']['receipt']) && !is_null($_FILES['uploadedFile']['name']['receipt'])) 
			{
              // May be we have enabled MULTIPLE on file upload
              if( is_array($_FILES['uploadedFile']['name']['receipt'])) 
              {
                $curly = count($_FILES['uploadedFile']['name']['receipt']);
                for($moe=0; $moe < $curly; $moe++)
                {
                  $fSize = isset($_FILES['uploadedFile']['size']['receipt'][$moe]) ? 
                           $_FILES['uploadedFile']['size']['receipt'][$moe] : 0;

                  $fTmpName = isset($_FILES['uploadedFile']['tmp_name']['receipt'][$moe]) ? 
                              $_FILES['uploadedFile']['tmp_name']['receipt'][$moe] : '';

                  if ($fSize && $fTmpName != "")
                  {
                    $fk2loop = array_keys($_FILES['uploadedFile']);
                    foreach($fk2loop as $tk)
                    {
                      $fInfo[$tk] = $_FILES['uploadedFile'][$tk]['receipt'][$moe];
                    }  
                    $gui->uploaded = $attachmentRepository->insertAttachment($id,$gui->tableName,'Receipt',$fInfo);
                  }
                }  
              } 
        else
            {
                $fSize = isset($_FILES['uploadedFile']['size']['receipt']) ? $_FILES['uploadedFile']['size']['receipt'] : 0;
                $fTmpName = isset($_FILES['uploadedFile']['tmp_name']['receipt']) ? 
                            $_FILES['uploadedFile']['tmp_name']['receipt'] : '';

                if ($fSize && $fTmpName != "")
                {
                  $fk2loop = array_keys($_FILES['uploadedFile']);
                  foreach($fk2loop as $tk)
                  {
                    $fInfo[$tk] = $_FILES['uploadedFile']['receipt'][$tk];
                  }  
				 
				  $gui->uploaded = $attachmentRepository->insertAttachment($id,$gui->tableName,'Receipt',$fInfo);
                }
            } 
              
        }
		if( isset($_FILES['uploadedFile']['name']['cardspy']) && !is_null($_FILES['uploadedFile']['name']['cardspy'])) 
			{
              // May be we have enabled MULTIPLE on file upload
              if( is_array($_FILES['uploadedFile']['name']['cardspy'])) 
              {
                $curly = count($_FILES['uploadedFile']['name']['cardspy']);
                for($moe=0; $moe < $curly; $moe++)
                {
                  $fSize = isset($_FILES['uploadedFile']['size']['cardspy'][$moe]) ? 
                           $_FILES['uploadedFile']['size']['cardspy'][$moe] : 0;

                  $fTmpName = isset($_FILES['uploadedFile']['tmp_name']['cardspy'][$moe]) ? 
                              $_FILES['uploadedFile']['tmp_name']['cardspy'][$moe] : '';

                  if ($fSize && $fTmpName != "")
                  {
                    $fk2loop = array_keys($_FILES['uploadedFile']);
                    foreach($fk2loop as $tk)
                    {
                      $fInfo[$tk] = $_FILES['uploadedFile'][$tk]['cardspy'][$moe];
                    }  
                    $gui->uploaded = $attachmentRepository->insertAttachment($id,$gui->tableName,'Cardspy',$fInfo);
                  }
                }  
              } 
        else
            {
                $fSize = isset($_FILES['uploadedFile']['size']['cardspy']) ? $_FILES['uploadedFile']['size']['cardspy'] : 0;
                $fTmpName = isset($_FILES['uploadedFile']['tmp_name']['cardspy']) ? 
                            $_FILES['uploadedFile']['tmp_name']['cardspy'] : '';

                if ($fSize && $fTmpName != "")
                {
                  $fk2loop = array_keys($_FILES['uploadedFile']);
                  foreach($fk2loop as $tk)
                  {
                    $fInfo[$tk] = $_FILES['uploadedFile']['cardspy'][$tk];
                  }  
                  $gui->uploaded = $attachmentRepository->insertAttachment($id,$gui->tableName,'Cardspy',$fInfo);
                }
            } 
              
        }
		if( isset($_FILES['uploadedFile']['name']['others']) && !is_null($_FILES['uploadedFile']['name']['others'])) 
			{
              // May be we have enabled MULTIPLE on file upload
              if( is_array($_FILES['uploadedFile']['name']['others'])) 
              {
                $curly = count($_FILES['uploadedFile']['name']['others']);
                for($moe=0; $moe < $curly; $moe++)
                {
                  $fSize = isset($_FILES['uploadedFile']['size']['others'][$moe]) ? 
                           $_FILES['uploadedFile']['size']['others'][$moe] : 0;

                  $fTmpName = isset($_FILES['uploadedFile']['tmp_name']['others'][$moe]) ? 
                              $_FILES['uploadedFile']['tmp_name']['others'][$moe] : '';

                  if ($fSize && $fTmpName != "")
                  {
                    $fk2loop = array_keys($_FILES['uploadedFile']);
                    foreach($fk2loop as $tk)
                    {
                      $fInfo[$tk] = $_FILES['uploadedFile'][$tk]['others'][$moe];
                    }  
                    $gui->uploaded = $attachmentRepository->insertAttachment($id,$gui->tableName,'Others',$fInfo);
                  }
                }  
              } 
        else
            {
                $fSize = isset($_FILES['uploadedFile']['size']['others']) ? $_FILES['uploadedFile']['size']['others'] : 0;
                $fTmpName = isset($_FILES['uploadedFile']['tmp_name']['others']) ? 
                            $_FILES['uploadedFile']['tmp_name']['others'] : '';

                if ($fSize && $fTmpName != "")
                {
                  $fk2loop = array_keys($_FILES['uploadedFile']);
                  foreach($fk2loop as $tk)
                  {
                    $fInfo[$tk] = $_FILES['uploadedFile']['others'][$tk];
                  }  
                 $gui->uploaded = $attachmentRepository->insertAttachment($id,$gui->tableName,'Others',$fInfo);
                }
            }               
        }
}
else
{
  $_SESSION['s_upload_tableName'] = $args->tableName;
  $_SESSION['s_upload_id'] = $args->id;
}

$smarty = new TLSmarty();
$smarty->assign('gui',$gui);
$smarty->display('attachmentupload.tpl');

/**
 * @return object returns the arguments for the page
 */
function init_args()
{
  $iParams = array(
    //the id (attachments.fk_id) of the object, to which the attachment belongs to 
    "id" => array("GET",tlInputParameter::INT_N),
    //the table to which the fk_id refers to (attachments.fk_table) of the attachment 
    "tableName" => array("GET",tlInputParameter::STRING_N,0,250),
    //the title of the attachment (attachments.title) 
    "title" => array("POST",tlInputParameter::STRING_N,0,250),
  );
  $args = new stdClass();
  I_PARAMS($iParams,$args);
  
  $args->bPostBack = sizeof($_POST);
  
  return $args;
}

/**
 * @param $db resource the database connection handle
 * @param $user the current active user
 * @return boolean returns true if the page can be accessed
 */
function checkRights(&$db,&$user)
{
  return (config_get("attachments")->enabled);
}
?>