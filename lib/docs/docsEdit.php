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
require_once("docs_type.class.php");
require_once("docs.class.php");
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
$subadiq_mgr = new docs($db);
$args = init_args($_REQUEST,$_SESSION);
$gui = initializeGui($args);


$op = new stdClass();
$op->operation_descr = '';
$op->user_feedback = '';
$op->buttonCfg = '';
$op->status_ok = 1;

switch($args->do_action)
{
  case 'edit':
    $op = edit($args,$subadiq_mgr);
  break;

  case 'create':
    $op = create($args);
  break;

  case 'do_delete':
    $op = doDelete($args,$subadiq_mgr);
  break;

  case 'do_update':
    $op = doUpdate($args,$subadiq_mgr/*,$tplan_mgr*/);
    $templateCfg->template = $op->template;
  break;

  case 'do_create':
    $op = doCreate($args,$subadiq_mgr);
    $templateCfg->template = $op->template;
  break;

}

$dummy = null;

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

  $nullable_keys = array('do_action','doc_name','release_date','is_active');
  foreach($nullable_keys as $value)
  {
    $args->$value = isset($request_hash[$value]) ? $request_hash[$value] : null;
  }

  $intval_keys = array('docID' => 0,'doc_type' => 0);
  foreach($intval_keys as $key => $value)
  {
    $args->$key = isset($request_hash[$key]) ? intval($request_hash[$key]) : $value;
  }
  
  $boolean_keys = array('is_active');
  foreach($boolean_keys as $value)
  {
    $args->$value = isset($request_hash[$value]) ? 1 : 0;
  }

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
  $binfo = $subadiq_mgr->get_by_id($argsObj->docID);
  $op = new stdClass();
  $op->buttonCfg = new stdClass();
  $op->buttonCfg->name = "do_update";
  $op->buttonCfg->value = lang_get('btn_save');
  $op->user_feedback = '';

  $argsObj->doc_name = $binfo['name'];
  $argsObj->SelectedDocs_types = $binfo['id_type'];
  $argsObj->release_date = $binfo['validity'];
  $argsObj->is_active = $binfo['active'];

  
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
  $subadiq_mgr->delete($argsObj->docID);
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
        $doRender = true;
        $tpl = is_null($templateCfg->template) ? 'docsView.tpl' : $templateCfg->template;
      break;

      case "edit":
      case "create":
        $doRender = true;
        $tpl = is_null($templateCfg->template) ? $templateCfg->default_template : $templateCfg->template;
      break;
    }

    if($doRender)
    {
      $docs_types = new docs_types($subadiq_mgr->db);
      // Attention this is affected by changes in templates
      $guiObj->docs=$subadiq_mgr->getdocs();
      $guiObj->docs_types = $docs_types->getDocs_typesForSelect();
      $guiObj->tplan_name=$argsObj->tplan_name;
      $guiObj->doc_id = $argsObj->docID;
      $guiObj->validity = $argsObj->release_date;
      $guiObj->SelectedDocs_types = $argsObj->SelectedDocs_types;
      $guiObj->doc_name = $argsObj->doc_name;
      $guiObj->is_active = $argsObj->is_active;
      $guiObj->is_open = $argsObj->is_open;
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
  $op->template = "docsEdit.tpl";
  $op->status_ok = 0;
  $op->buttonCfg = null;
  $targetDate=null;
    $buildID = $subadiq_mgr->create($argsObj->doc_name,$argsObj->doc_type,$argsObj->release_date,$argsObj->is_active);
    if ($buildID)
    {
      $op->user_feedback = '';
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
function doUpdate(&$argsObj,&$subadiq_mgr)
{
  $op = new stdClass();
  $op->operation_descr = '';
  $op->user_feedback = '';
  $op->template = "docsEdit.tpl";
  $op->buttonCfg = new stdClass();
  $op->buttonCfg->name = "do_update";
  $op->buttonCfg->value = lang_get('btn_save');
  $op->user_feedback = '';

    if ($subadiq_mgr->update($argsObj->docID,$argsObj->doc_name,$argsObj->doc_type,$argsObj->release_date,$argsObj->is_active)) //alterar a entrada de dados
    {
      $op->user_feedback = '';
      $op->template = null;
      logAuditEvent(TLS("audit_build_saved",$argsObj->testprojectName,$argsObj->tplan_name,$argsObj->build_name),
                    "SAVE",$argsObj->docID,"builds");
    }
  return $op;
}

function checkRights(&$db,&$user)
{
  return $user->hasRight($db,'testplan_create_build');
}
?>