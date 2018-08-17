<?php

/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * @filesource  metricsDashboard.php
 * @package     TestLink
 * @copyright   2007-2013, TestLink community 
 * @author      franciscom
 *
 * @internal revisions
 * @since 1.9.9
 *
 **/
require('../../config.inc.php');
require_once('common.php');
require_once('exttable.class.php');
require_once('../functions/tlTestPlanMetrics.class.php');
$templateCfg = templateConfiguration();

//testlinkInitPage($db,false,false,"checkRights");
list($args, $gui) = initEnv($db);
$result_cfg = config_get('results');
$show_all_status_details = config_get('metrics_dashboard')->show_test_plan_status;
$round_precision = config_get('dashboard_precision');

$labels = init_labels(array('overall_progress' => null, 'test_plan' => null, 'progress' => null,
    'href_metrics_dashboard' => null, 'progress_absolute' => null,
    'no_testplans_available' => null, 'not_aplicable' => null,
    'platform' => null, 'th_active_tc' => null, 'in_percent' => null));

//$statusSetForDisplay = $result_cfg['status_label_for_exec_ui'];
$gui->warning_msg = '';
$testplannames = array();

$columns = getColumnsDefinition(true, $statusSetForDisplay, $labels, $testplannames); //crido por mim para colocar os baselines como plataformas.
$matrixData = array();
$metricsMgr = new tlTestPlanMetrics($db);
$sublist = $metricsMgr->getSubList($args->tproject_id);
foreach ($sublist as $sub) {
    $buildlist = $metricsMgr->getExplainedBuildsBySub($args->tproject_id, $sub['name']);
    if ($buildlist != null) {
        $buildvalid = array();
        $groupSolucao = array();
        foreach ($buildlist as $build) {
            $groupSolucao[$build['solucao']][] = $build;
            $buildvalid[$build['solucao']] .= '&buildvalid[]='.$build['id'];
        }
        //var_dump($groupSolucao);
        foreach ($groupSolucao as $ch => $solucao) {
            $rowData2 = array();
            $rowData2[] = $sub['name'] . $ch;$subname = $sub['name'];$subgraph = $sub['name'] . $ch;
            $groupRoteiro = array();
            foreach ($solucao as $build) {
                $groupRoteiro[$build['roteiro']][] = $build;
            }

            $text = '<table>';
            $text .= '<tr>';
            $line = '';$temp = '';
            foreach ($groupRoteiro as $chave => $roteiro) {
                $idGraph = $sub['name'] . $ch . $chave; //print_r($idGraph);//é o id que será usado para identificar o <td> que será alterado com o gráfico
                $selection = "<select onchange='document.getElementById(\"$idGraph\").innerHTML = this.value'>";
                foreach ($roteiro as $item) {
                    $value = "<img width = 280 height = 280 src=\"lib/results/overallPieChartPerBuild.php?apikey=&tplan_id=" . $item['testplan_id'] . '&build=' . $item['id'] .'" onclick = "">';
                    $selection .= "<option value = '$value' >" . $chave . $item['ciclo'] . "</option>";
                }
                $selection .= "</select>";
                $titulo = $selection; //$chave.$roteiro[0]['ciclo'];
                $temp = "<th>$titulo</th>".$temp;//$text .= "<th>$titulo</th>";
                
                $line = "<td id = \"$idGraph\" >" . '<img width = 280 height = 280 src="lib/results/overallPieChartPerBuild.php?apikey=&tplan_id=' . $roteiro[0]['testplan_id'] . '&build=' . $roteiro[0]['id'] ." \" onclick=\" document.getElementById('time$subgraph').innerHTML ='<img width = 1500 height= 230 src =\'http://localhost/testlink/lib/results/LineChartHistoryBuild.php?build=$args->tproject_id&sub=$subname&clean=y&periods=15&buildvalid[]=".$item['id']."\' >' \" ".'>' . "</td>".$line;//$line .= "<td id = \"$idGraph\">" . '<img width = 280 height = 280 src="lib/results/overallPieChartPerBuild.php?apikey=&tplan_id=' . $roteiro[0]['testplan_id'] . '&build=' . $roteiro[0]['id'] . '" >' . "</td>";
            }
            $text .= $temp;
            $text .= '</tr>';//echo("<script>console.log('$temp')</script>");
            $text .= "<tr >$line</tr>";
            $text .= "</table><div id = \"time$subgraph\">"."<img width = 1500 height= 230 src =\"lib/results/LineChartHistoryBuild.php?build=$args->tproject_id&sub=$subname&clean=y&periods=15".$buildvalid[$ch]."\" ></div>";
            $rowData2[] = $text;
            $matrixData[] = $rowData2;
        }
    }
}
$table = new tlExtTable($columns, $matrixData, 'tl_table_metrics_dashboard');
//var_dump($columns);
// if platforms are to be shown -> group by test plan
// if no platforms are to be shown -> no grouping
$table->setGroupByColumnName($labels['test_plan']);

$table->setSortByColumnName($labels['progress']);
$table->sortDirection = 'DESC';

$table->showToolbar = true;
$table->toolbarExpandCollapseGroupsButton = false;
$table->toolbarShowAllColumnsButton = false;
$table->toolbarResetFiltersButton = false;
$table->title = $labels['href_metrics_dashboard'];
$table->showGroupItemsCount = false;

$gui->tableSet = array($table);


$smarty = new TLSmarty;
$smarty->assign('gui', $gui);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);

/**
 * get Columns definition for table to display
 *
 */
function getColumnsDefinition($showPlatforms, $statusLbl, $labels, $platforms) {
    $colDef = array();

    $colDef[] = array('title_key' => 'test_plan', 'width' => 60, 'type' => 'text', 'sortType' => 'asText',
        'filter' => 'string');

    if ($showPlatforms) {
        $colDef[] = array('title_key' => 'platform', 'width' => 60, 'sortType' => 'asText',
            'filter' => 'string'/*, 'filterOptions' => $platforms*/);
    }

    //$colDef[] = array('title_key' => 'link_charts', 'width' => 200, /*'type' => 'text',*/ 'filter' => 'string');

    return $colDef;
}

function initEnv(&$dbHandler) {
    $args = new stdClass();
    $gui = new stdClass();

    $iParams = array("apikey" => array(tlInputParameter::STRING_N, 32, 64),
        "tproject_id" => array(tlInputParameter::INT_N),
        "tplan_id" => array(tlInputParameter::INT_N),
        "show_only_active" => array(tlInputParameter::CB_BOOL),
        "show_only_active_hidden" => array(tlInputParameter::CB_BOOL));

    R_PARAMS($iParams, $args);
    //echo is_null($args->apikey);//vai dar igual a 1
    if (!is_null($args->apikey)) {

        $args->show_only_active = true;
        $cerbero = new stdClass();
        $cerbero->args = new stdClass();
        $cerbero->args->tproject_id = $args->tproject_id;
        $cerbero->args->tplan_id = $args->tplan_id;
        $cerbero->args->getAccessAttr = true;
        $cerbero->method = 'checkRights';
        $cerbero->redirect_target = "../../login.php?note=logout";
        if (strlen($args->apikey) == 32) {
            setUpEnvForRemoteAccess($dbHandler, $args->apikey, $cerbero);
        } else {//executa por aqui
            setUpEnvForAnonymousAccess($dbHandler, $args->apikey, $cerbero);
        }
    } else {
        testlinkInitPage($dbHandler, false, false, "checkRights");
        $args->tproject_id = isset($_SESSION['testprojectID']) ? intval($_SESSION['testprojectID']) : 0;
    }

    if ($args->tproject_id <= 0) {
        $msg = __FILE__ . '::' . __FUNCTION__ . " :: Invalid Test Project ID ({$args->tproject_id})";
        throw new Exception($msg);
    }
    $mgr = new tree($dbHandler);
    $dummy = $mgr->get_node_hierarchy_info($args->tproject_id);
    $args->tproject_name = $dummy['name'];

    $args->user = $_SESSION['currentUser'];
    $args->currentUserID = $args->user->dbID;

    // I'm sorry for MAGIC
    $args->direct_link_ok = true;
    if (strlen(trim($args->user->userApiKey)) == 32) {
        $args->direct_link = $_SESSION['basehref'] . "lnl.php?type=metricsdashboard&" .
                "apikey={$args->user->userApiKey}&tproject_id={$args->tproject_id}";
    } else {
        $args->direct_link_ok = false;
        $args->direct_link = lang_get('can_not_create_direct_link');
    }
    $gui->tproject_name = $args->tproject_name;
    $gui->show_only_active = $args->show_only_active;
    $gui->direct_link = $args->direct_link;
    $gui->direct_link_ok = $args->direct_link_ok;
    $gui->warning_msg = lang_get('no_testplans_available');

    return array($args, $gui);
}
/**
 *
 */
function checkRights(&$db, &$user, $context = null) {
    if (is_null($context)) {
        $context = new stdClass();
        $context->tproject_id = $context->tplan_id = null;
        $context->getAccessAttr = false;
    }
    $checkOrMode = array('testplan_metrics', 'testplan_execute');
    foreach ($checkOrMode as $right) {
        if ($user->hasRight($db, $right, $context->tproject_id, $context->tplan_id, $context->getAccessAttr)) {
            return true;
        }
    }
    return false;
}
?>