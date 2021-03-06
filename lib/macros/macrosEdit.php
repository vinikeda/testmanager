<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * @filesource  buildEdit.php
 *
 * @internal revisions
 * @since 1.9.14
 *
 */
require('../../config.inc.php');
require_once("common.php");
require_once("web_editor.php");
require_once("macros.class.php");
//$editorCfg = getWebEditorCfg('build');
//require_once(require_web_editor($editorCfg['type']));

testlinkInitPage($db,false,false,"checkRights");
$templateCfg = templateConfiguration();

//$date_format_cfg = config_get('date_format');

$op = new stdClass();
$op->user_feedback = '';
$op->buttonCfg = new stdClass();
$op->buttonCfg->name = "";
$op->buttonCfg->value = "";

$smarty = new TLSmarty();
//$tplan_mgr = new testplan($db);
$subadiq_mgr = new Macros($db);
$args = init_args($_REQUEST,$_SESSION);
$gui = initializeGui($args);
$tproject_mgr = new testproject($db);
$gui->projects = $tproject_mgr->get_accessible_for_user($args->user->dbID,
                                                        array('output' => 'map_name_with_inactive_mark',
                                                                  'field_set' => null,
                                                                  'order_by' => null));
/*$of = web_editor('notes',$_SESSION['basehref'],$editorCfg);
$of->Value = getItemTemplateContents('build_template', $of->InstanceName, $args->notes);*/


$op = new stdClass();
$op->operation_descr = '';
$op->user_feedback = '';
$op->buttonCfg = '';
$op->status_ok = 1;

switch($args->do_action)
{
  case 'edit':
    $op = edit($args,$subadiq_mgr);
    //$gui->closed_on_date = $args->closed_on_date;
    //$of->Value = $op->notes;
  break;

  case 'create':
    $op = create($args);
  break;

  case 'do_delete':
    $op = doDelete($args,$subadiq_mgr);
  break;

  case 'do_update':
    $op = doUpdate($args,$subadiq_mgr/*,$tplan_mgr*/);
    //$of->Value = $op->notes;
    $templateCfg->template = $op->template;
  break;

  case 'do_create':
    $op = doCreate($args,$subadiq_mgr);
    //$of->Value = $op->notes;
    $templateCfg->template = $op->template;
  break;

}

$dummy = null;
/*$gui->release_date = (isset($op->status_ok) && $op->status_ok && $args->release_date != "") ? 
                      localize_dateOrTimeStamp(null, $dummy, 'date_format',$args->release_date) : 
                      $args->release_date_original;*/
$gui->operation_descr = $op->operation_descr;
$gui->user_feedback = $op->user_feedback;
$gui->buttonCfg = $op->buttonCfg;

$gui->mgt_view_events = $args->user->hasRight($db,"mgt_view_events");
$gui->editorType = $editorCfg['type'];

renderGui($smarty,$args,$subadiq_mgr,$templateCfg,/*$of,*/$gui);

/*
 * INITialize page ARGuments, using the $_REQUEST and $_SESSION
 * super-global hashes.
 * Important: changes in HTML input elements on the Smarty template
 *            must be reflected here.
 *
 *
 * @parameter hash request_hash the $_REQUEST
 * @parameter hash session_hash the $_SESSION
 * @return    object with html values tranformed and other
 *                   generated variables.
 * @internal revisions
 *
 */
function init_args($request_hash, $session_hash)
{
  $args = new stdClass();
  $request_hash = strings_stripSlashes($request_hash);
  $nullable_keys = array('do_action','subadiq_name');
  foreach($nullable_keys as $value)
  {
    $args->$value = isset($request_hash[$value]) ? $request_hash[$value] : null;
  }

  $intval_keys = array('markerID' => 0);
  foreach($intval_keys as $key => $value)
  {
    $args->$key = isset($request_hash[$key]) ? intval($request_hash[$key]) : $value;
  }
  $args->markersID = $request_hash['cfSelected'];
  $args->cfvalues = $request_hash['cfValue'];
  $args->projectsID = $request_hash['projectsID'];
  //var_dump($request_hash);
  $args->userID = intval($session_hash['userID']);


  $args->user = $_SESSION['currentUser'];
  return $args;
}

/**
 *
 *
 */
function initializeGui(&$argsObj/*,&$subadiq_mgr*/)
{
  $guiObj = new stdClass();
  $guiObj->main_descr = lang_get('title_build_2') . config_get('gui_title_separator_2') . 
                        lang_get('test_plan') . config_get('gui_title_separator_1') . 
                        $argsObj->tplan_name;
  /*$guiObj->cfields = $subadiq_mgr->html_custom_field_inputs($argsObj->build_id,$argsObj->testprojectID,
                                                         'design','',$_REQUEST);*/
  $dummy = config_get('results');
  foreach($dummy['status_label_for_exec_ui'] as $kv => $vl)
  {
    $guiObj->exec_status_filter['items'][$dummy['status_code'][$kv]] = lang_get($vl);  
  }  
  return $guiObj;
}


/*
  function: edit
            edit action
            
  args :

  returns:

*/
function edit(&$argsObj,&$subadiq_mgr)
{
  $binfo = $subadiq_mgr->get_by_id($argsObj->markerID);
  $op = new stdClass();
  $op->buttonCfg = new stdClass();
  $op->buttonCfg->name = "do_update";
  $op->buttonCfg->value = lang_get('btn_save');
  //$op->notes = $binfo['notes'];
  $op->user_feedback = '';
  $op->status_ok = 1;

  $argsObj->subadiq_name = $binfo['name'];
  
  foreach($binfo['cf'] as $cf){
        $argsObj->markersID[] = $cf['id_cf'];
        $argsObj->cfValue[] = $cf['value'];
  }
  
  $argsObj->projectsID = $subadiq_mgr->getProjects($argsObj->markerID);
  //$argsObj->release_date = $binfo['release_date'];

  /*if( $binfo['closed_on_date'] == '')
  {
    $argsObj->closed_on_date = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
  }    
  else
  {    
    $datePieces = explode("-",$binfo['closed_on_date']);
    $argsObj->closed_on_date = mktime(0,0,0,$datePieces[1],$datePieces[2],$datePieces[0]);
  }*/
  
  $op->operation_descr=lang_get('title_build_edit') . TITLE_SEP_TYPE3 . $argsObj->build_name;

  return $op;
}

/*
  function: create
            prepares environment to manage user interaction on a create operations
 
  args: $argsObj: reference to input values received by page.

  returns: object with part of gui configuration

*/
function create(&$argsObj)
{
  $op = new stdClass();
  $op->operation_descr = lang_get('title_build_create');
  $op->buttonCfg = new stdClass();
  $op->buttonCfg->name = "do_create";
  $op->buttonCfg->value = lang_get('btn_create');
  $op->user_feedback = '';
  $argsObj->is_active = 1;
  //$argsObj->closed_on_date = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));

  return $op;
}

/*
  function: doDelete

  args :

  returns:

*/
function doDelete(/*&$dbHandler,*/&$argsObj,&$subadiq_mgr/*,&$tplanMgr*/)
{
  $op = new stdClass();
  $op->user_feedback = '';
  $op->operation_descr = '';
  $op->buttonCfg = null;

  

  $subadiq_mgr->delete($argsObj->markerID);
  /*{
    $op->user_feedback = lang_get("cannot_delete_build");
  }
  else
  {
    logAuditEvent(TLS("audit_build_deleted",$argsObj->testprojectName,$argsObj->tplan_name,$build['name']),
                  "DELETE",$argsObj->build_id,"builds");
  }*/
  return $op;
}

/*
  function:

  args :

  returns:

*/
function renderGui(&$smartyObj,&$argsObj,&$subadiq_mgr,$templateCfg/*,$owebeditor*/,&$guiObj)
{
    $doRender = false;
    switch($argsObj->do_action)
    {
      case "do_create":
      case "do_delete":
      case "do_update":
      case "setActive":
      case "setInactive":
      case "open":
      case "close":
        $doRender = true;
        $tpl = is_null($templateCfg->template) ? 'macrosView.tpl' : $templateCfg->template;
      break;

      case "edit":
      case "create":
        $doRender = true;
        $tpl = is_null($templateCfg->template) ? $templateCfg->default_template : $templateCfg->template;
      break;
    }

    if($doRender)
    {
      
      // Attention this is affected by changes in templates
        $guiObj->doc_type = $subadiq_mgr->getCFieldList();
        $guiObj->obj = $subadiq_mgr->getdata();
        $guiObj->isADM= ($_SESSION['currentUser']->globalRole->dbID ===  '8'?1:0);
        $guiObj->enable_copy = ($argsObj->do_action == 'create' || $argsObj->do_action == 'do_create') ? 1 : 0;
        //$guiObj->notes = $owebeditor->CreateHTML();
        //$guiObj->source_build = init_source_build_selector($subadiq_mgr, $argsObj);
        $guiObj->tplan_name=$argsObj->tplan_name;
        $guiObj->subadiq_id = $argsObj->markerID;//var_dump($argsObj->markersID);
        //$guiObj->selectedMarkers = $argsObj->markersID;//var_dump($guiObj->selectedMarkers);
        foreach($argsObj->markersID as $chave=>$mks){
            $guiObj->selectedMarkers[$mks] = $mks;
            $guiObj->Values[$mks] = $argsObj->cfValue[$chave];
        }//var_dump($argsObj->markersID);
        //foreach($argsObj->markersID as $chave=>$valor){
        foreach($guiObj->doc_type as $chave=>$valor){
            //$temp = $subadiq_mgr->getBlankField($valor);
            $temp = $subadiq_mgr->getBlankField($chave);
            $guiObj->selectedMarkers[$chave]  = $chave;
            $guiObj->inputs[$chave] = $temp[0]['input'];
            $guiObj->ids[$chave] = $temp[0]['label_id'];            
        }
        //var_dump($guiObj->doc_type);
        //var_dump($subadiq_mgr->getBlankField());
        //var_dump($guiObj->ids);
        //$guiObj->Values = $argsObj->cfValue;var_dump($guiObj->Values);
      $guiObj->selectedProjects = $argsObj->projectsID;
        $guiObj->subadiq_name = $argsObj->subadiq_name;
        $guiObj->is_active = $argsObj->is_active;
        $guiObj->is_open = $argsObj->is_open;
        $guiObj->copy_tester_assignments = $argsObj->copy_tester_assignments;
        $smartyObj->assign('gui',$guiObj);
        $smartyObj->display($templateCfg->template_dir . $tpl);
    }

}


/*
  function: doCreate

  args :

  returns:

  @internal revisions
*/
function doCreate(&$argsObj,&$subadiq_mgr)
{
  $op = new stdClass();
  $op->operation_descr = '';
  $op->user_feedback = '';
  $op->template = "macrosView.tpl";
  $op->notes = $argsObj->notes;
  $op->status_ok = 0;
  $op->buttonCfg = null;
  $targetDate=null;
    $user_feedback = lang_get("cannot_add_build");    //var_dump($argsObj->cfvalues);
    $buildID = $subadiq_mgr->create($argsObj->subadiq_name,1,$argsObj->markersID,$argsObj->cfvalues, $argsObj->projectsID);
    if ($buildID)
    {
      /*$cf_map = $buildMgr->get_linked_cfields_at_design($buildID,$argsObj->testprojectID);
      $buildMgr->cfield_mgr->design_values_to_db($_REQUEST,$buildID,$cf_map,null,'build');*/

                
      $op->user_feedback = '';
      //$op->notes = '';
      $op->template = null;
      $op->status_ok = 1;
      logAuditEvent(TLS("audit_build_created",$argsObj->testprojectName,$argsObj->tplan_name,$argsObj->build_name),
                    "CREATE",$buildID,"builds");
    }

  if(!$op->status_ok)
  {
    $op->buttonCfg = new stdClass();
    $op->buttonCfg->name = "do_create";
    $op->buttonCfg->value = lang_get('btn_create');
    $op->user_feedback = $check->user_feedback;
  }
  return $op;
}


/*
  function: doUpdate

  args :

  returns:

*/
function doUpdate(&$argsObj,&$subadiq_mgr/*,&$tplanMgr,$dateFormat*/)
{
  $op = new stdClass();
  $op->operation_descr = '';
  $op->user_feedback = '';
  $op->template = "macrosView.tpl";
  //$op->notes = $argsObj->notes;
  $op->status_ok = 0;
  $op->buttonCfg = null;

  $oldObjData = $subadiq_mgr->get_by_id($argsObj->markerID);
  $oldname = $oldObjData['name'];

  //$check = crossChecks($argsObj,$tplanMgr,$dateFormat);
  //if($check->status_ok){
    $user_feedback = lang_get("cannot_update_build");
    if ($subadiq_mgr->update($argsObj->markerID,$argsObj->subadiq_name,$argsObj->markersID,$argsObj->cfvalues, $argsObj->projectsID)/*false/**/) 
    {
      //$cf_map = $subadiq_mgr->get_linked_cfields_at_design($argsObj->markerID,$argsObj->testprojectID);
      //$subadiq_mgr->cfield_mgr->design_values_to_db($_REQUEST,$argsObj->markerID,$cf_map,null,'build');
      $op->user_feedback = '';
      $op->notes = '';
      $op->template = null;
      $op->status_ok = 1;
      logAuditEvent(TLS("audit_build_saved",$argsObj->testprojectName,$argsObj->tplan_name,$argsObj->build_name),
                    "SAVE",$argsObj->markerID,"builds");
    }
  //}

  if(!$op->status_ok)
  {
    $op->operation_descr = lang_get('title_build_edit') . TITLE_SEP_TYPE3 . $oldname;
    $op->buttonCfg = new stdClass();
    $op->buttonCfg->name = "do_update";
    $op->buttonCfg->value = lang_get('btn_save');
    $op->user_feedback = $check->user_feedback;
  }
  return $op;
}

function doCopyToTestPlans(&$argsObj,&$buildMgr,&$tplanMgr)
{
    $tprojectMgr = new testproject($tplanMgr->db);

    // exclude this testplan
    $filters = array('tplan2exclude' => $argsObj->tplan_id);
    $tplanset = $tprojectMgr->get_all_testplans($argsObj->testprojectID,$filters);

    if(!is_null($tplanset))
    {
        foreach($tplanset as $id => $info)
        {
            if(!$tplanMgr->check_build_name_existence($id,$argsObj->build_name))
            {
                $buildMgr->create($id,$argsObj->build_name,$argsObj->notes,
                                  $argsObj->is_active,$argsObj->is_open);
            }
        }
    }
}

function checkRights(&$db,&$user)
{
  return $user->hasRight($db,'testplan_create_build');
}
?>