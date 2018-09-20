<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * Downloads a zip file of attachments by execution
 *
 * @filesource attachmentdownload.php
 *
 * @internal revisions
 * @since 1.9.14
 *
 */

@ob_end_clean();
require_once('../../config.inc.php');
require_once('../functions/common.php');
require_once('../functions/attachments.inc.php');
testlinkInitPage($db,false,true);
$args = init_args($db);
$args->id = $_POST['id'];
if ($args->id)
{
	$sql = "SELECT builds.name, testprojects.prefix, tcversions.tc_external_id, attachments.file_path, attachments.file_name, attachments.date_added from testplans INNER JOIN testprojects ON testplans.testproject_id = testprojects.id INNER JOIN builds ON builds.testplan_id = testplans.id INNER JOIN executions ON executions.build_id = builds.id INNER JOIN tcversions ON executions.tcversion_id = tcversions.id INNER JOIN attachments ON attachments.fk_id = executions.id WHERE executions.build_id =".$args->id;
	$result = mysql_query($sql);
	if (mysql_num_rows($result)!=0){
		$zip = new ZipArchive();
		$tmp_file = tempnam('.','');
		$zip->open($tmp_file, ZipArchive::CREATE);
	//	$attach = array();		
		while($row = mysql_fetch_assoc($result)){
			$name = $row['name'];
			$attach = "C:/wamp64/www/testlink/upload_area/".$row['file_path']; //TESTAR
			$zip->addFile($attach,"".$row['prefix']." ".$row['tc_external_id']." ".$row['date_added']." ".$row['file_name']."");			
		}
		$zip->close();
		header('Content-disposition: attachment; filename='.$name.'.zip');
		header('Content-type: application/zip');
		readfile($tmp_file);
		unlink($tmp_file);
		exit();
	} else {
	
			echo '<script type="text/javascript">				
				window.open("../attachments/attachmentempty.php", "_blank", "width=510,height=300",true);
				window.history.back();
			 </script>';
	}
	
}	

function init_args(&$dbHandler)
{
  // id (attachments.id) of the attachment to be downloaded
  $iParams = array('id' => array(tlInputParameter::INT_N),
                   'apikey' => array(tlInputParameter::STRING_N,64),  
                   'skipCheck' => array(tlInputParameter::INT_N));
  
  $args = new stdClass();
  G_PARAMS($iParams,$args);

  $args->light = 'green';
  $args->opmode = 'GUI';

  // using apikey lenght to understand apikey type
  // 32 => user api key
  // other => test project or test plan
  $args->apikey = trim($args->apikey);
  $apikeyLenght = strlen($args->apikey);
  if($apikeyLenght > 0)
  {
    $args->opmode = 'API';
    $args->skipCheck = true;
  } 

  return $args;
}
?>	