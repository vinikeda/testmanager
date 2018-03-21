<?php

/*

 * comando base para o comando que vai juntar as contagens de execuçoes por dia, esse daí pega de dotas as builds dos testplans
 * SELECT count(id),date(execution_ts) camp ,status FROM `executions` where build_id in (select DISTINCT build_id from (SELECT max(id), status, build_id , tcversion_id, testplan_id FROM `executions` where testplan_id = 65349 group by build_id, tcversion_id) teste) group by camp, status
 * 
 * 
 * comando que faz a contagem das execuções por dia, mas d eum plano de teste inteiro.
 * SELECT count(id),date(execution_ts) camp , status FROM `executions` where build_id in (select DISTINCT build_id from (SELECT max(id), status, build_id , tcversion_id, testplan_id FROM `executions` where testplan_id = 65349 group by build_id, tcversion_id) teste) group by camp,status
 * 
 * comando que faz a contagem das execuções por hora, mas d eum plano de teste inteiro.
 * SELECT count(id),date(execution_ts) camp ,hour(execution_ts) hora, status FROM `executions` where build_id in (select DISTINCT build_id from (SELECT max(id), status, build_id , tcversion_id, testplan_id FROM `executions` where testplan_id = 65349 group by build_id, tcversion_id) teste) group by camp, hora, status

 */
$build = $_GET['build'];
$sub = $_GET['sub'];
$clean = $_GET['stats'];
$periods = $_GET['periods'];
$rightbuilds = $_GET['buildvalid'];
$grapw = $_GET['width'];
require_once('../../config.inc.php');
require_once('common.php');
require_once('../functions/tlTestPlanMetrics.class.php');
define('PCHART_PATH', '../../third_party/pchart');
include(PCHART_PATH . "/pChart/pData.class");
include(PCHART_PATH . "/pChart/pChart.class");

$resultsCfg = config_get('results');
$chart_cfg = $resultsCfg['charts']['dimensions']['overallPieChart'];
//var_dump($resultsCfg['charts']['status_colour']);
//var_dump($resultsCfg['code_status']);
$args = init_args($db);
$tplan_mgr = new testplan($db);

$metricsMgr = new tlTestPlanMetrics($db);
//var_dump($args->tproject_id);
//$data = $metricsMgr->getExecByStatusPerBuildByHalfHour($build);
//$data = $metricsMgr->getExecBySubPerDay($args->tproject_id,$sub);
//$data = ($clean=='true')?$metricsMgr->getCleanExecBySubPerDay($build,$sub,$rightbuilds):$metricsMgr->getExecBySubPerDay($build,$sub,$rightbuilds);
$data = $metricsMgr->getCustomExecBySubPerDay($build,$sub,$rightbuilds,$clean);
if(count($data)==0) withoutData();
else{
$begin = $data[0]['date_time'];
$last = end($data)['date_time'];
$days = date_dif($begin,$last);
$daysPerPeriod = ceil($days/$periods);//var_dump($daysPerPeriod);
if($daysPerPeriod>1){
    //$data = ($clean=='true')?$metricsMgr->getCleanExecBySubPerXDays($build,$sub,$daysPerPeriod,$rightbuilds):$metricsMgr->getExecBySubPerXDays($build,$sub,$daysPerPeriod,$rightbuilds);
    $data = $metricsMgr->getCustomExecBySubPerXDays($build,$sub,$daysPerPeriod,$rightbuilds,$clean);
    
}else if($daysPerPeriod == 0){
    //$data = ($clean=='true')?$metricsMgr->getCleanExecBySubPerXHours($build,$sub,1,$rightbuilds):$metricsMgr->getExecBySubPerXHours($build,$sub,1,$rightbuilds);
    $data = $metricsMgr->getCustomExecBySubPerXHours($build,$sub,1,$rightbuilds,$clean);
    $begin = $data[0]['execution_ts'];
    $last = end($data)['execution_ts'];
    //$data = ($clean=='true')?$metricsMgr->getCleanExecBySubPerXHours($build,$sub,1,$rightbuilds):$metricsMgr->getExecBySubPerXHours($build,$sub,1,$rightbuilds);
    $data = $metricsMgr->getCustomExecBySubPerXHours($build,$sub,1,$rightbuilds,$clean);
}else $daysPerPeriod = 1;
$begin = $data[0]['date_time'];
$last = end($data)['date_time'];
/*var_dump($data);
var_dump($begin);
var_dump($last);
var_dump($daysPerPeriod);/**/
if($daysPerPeriod != 0)$datelist = date_range($begin, date('m/d/Y',strtotime($last)+(60*60*24)), "+$daysPerPeriod day", 'd/m/Y');
else {
    $hours = hour_dif($begin,$last);
    if($hours != 0)$datelist = date_range($begin, $last, "+".ceil($hours/$periods)." hour", 'd/m/Y H:00');
    else withoutData();
}
$listdate = array_flip($datelist);
//var_dump($datelist);//var_dump($data);
$values = array();
$valuesC = array();
$valuesdebug = array();
$labels = array();
$names = array();

foreach ($data as $reg) {
    $datahora = ($daysPerPeriod > 0)?date('d/m/Y',strtotime($reg['date_time'])):date('d/m/Y H:00',strtotime($reg['date_time']));
    $valuesdebug[$resultsCfg['code_status'][$reg['status']]][$datahora] = $reg['exec'];
    $valuesC[$resultsCfg['code_status'][$reg['status']]] = 0;
    if(isset($listdate[$datahora])){//verificando se a data precisa de correção depois das inúmeras conversões seguidas
        $values[lang_get($resultsCfg['status_label'][$resultsCfg['code_status'][$reg['status']]])/*lang_get($resultsCfg['status_label'][//$resultsCfg['code_status'][$reg['status']]/*]/*)*/][$datahora] = $reg['exec'];
    }
    else {
        $datahora = substr($datahora,0,1).(substr($datahora,1,1)+1).substr($datahora,-8);//corrigindo caso tenha ficado com uma hora de atraso devido ao reajuste na coonversão do horário de verão + utc. o que acarreta em um dia de atrado
        if(isset($listdate[$datahora])){
            $values[lang_get($resultsCfg['status_label'][$resultsCfg['code_status'][$reg['status']]])][$datahora] = $reg['exec'];
        }else{//algum erro não previsto
            var_dump('data não válida na divisão dos períodos: '.date('d/m/Y H:00',strtotime($reg['date_time'])));
        }
    }
    /*if (!in_array($datahora, $labels))
        $labels[] = $datahora;*/
}//var_dump($valuesC);
$labels = $datelist;
/*
var_dump($labels);
var_dump($valuesdebug);/**/
function datesort($a,$b){//essa  função foi criada para ordenar as datas.
    if (substr($a, 6,4) > substr($b, 6,4))return 1;
    if (substr($a, 6,4) < substr($b, 6,4))return -1;
    if (substr($a, 3,2) > substr($b, 3,2))return 1;
    if (substr($a, 3,2) < substr($b, 3,2))return -1;
    if (substr($a, 0,2) > substr($b, 0,2))return 1;
    if (substr($a, 0,2) < substr($b, 0,2))return -1;
    if (substr($a, 11,2) > substr($b, 11,2))return 1;
    if (substr($a, 11,2) < substr($b, 11,2))return -1;
}
usort($labels,'datesort');
//array_unique($labels);
//var_dump($values);
foreach ($labels as $lb) {
    foreach ($values as &$val) {
        if (!isset($val[$lb])) {
            $val[$lb] = 0;
            uksort($val, 'datesort');
        }
    }
}
$total;
$temp = 0;
foreach ($labels as $lbl){
    foreach ($values as $chave => $valor){
        $total[$lbl] += $valor[$lbl];
        
    }
    $total[$lbl] += $temp;
    $temp = $total[$lbl];
}
//$values['executados'] = $total;

foreach ($values as &$val) {
    $temporario = 0;
    foreach ($val as &$subval){
        $temporario += $subval;
        //$subval = $temporario;
    }
}
//var_dump($resultsCfg);
/*var_dump($args->tproject_id);
var_dump($sub);*/
/*var_dump($daysPerPeriod);
var_dump($data);
var_dump($values);
var_dump($labels);/**/

//var_dump($metricsMgr->getExecStatusPerBuild($build));
// Dataset definition
$DataSet = new pData;
foreach ($values as $chave => $value) {
    $DataSet->AddPoint($value, $chave);
    $DataSet->SetSerieName($chave, $chave);
}
$DataSet->AddAllSeries();
$DataSet->AddPoint($labels, 'Serie3');
$DataSet->SetAbsciseLabelSerie('Serie3');
//$DataSet->SetAbsciseLabelSerie("Serie1");
// Initialise the graph
$grapW = (isset($grapw))?$grapw:1500;
$Test = new pChart($grapW, 230);
//$Test->setFixedScale(0,$temp,5);
$Test->setFontProperties(config_get('charts_font_path'), 10);
$Test->setGraphArea(40, 7, $grapW - 40, 205);
$Test->drawGraphArea(252, 252, 252);
$colorList = array();
foreach ($valuesC as $key => $value) {
    $colorList[] = $resultsCfg['charts']['status_colour'][$key];
}//var_dump($colorList);
foreach($colorList as $key => $hexrgb)
{
  $rgb = str_split($hexrgb,2);
  $Test->setColorPalette($key,hexdec($rgb[0]),hexdec($rgb[1]),hexdec($rgb[2]));
}
$Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2);
$Test->drawGrid(4, TRUE, 230, 230, 230, 255);

// Draw the line graph
$Test->drawLineGraph($DataSet->GetData(), $DataSet->GetDataDescription());
$Test->drawPlotGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 3, 2, 250, 250, 250);

// Finish the graph
$Test->setFontProperties(config_get('charts_font_path'), 8);
$Test->drawLegend(45, 35, $DataSet->GetDataDescription(), 255, 255, 255);
//$batata = $Test->drawPieLegend(45,35,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
$Test->setFontProperties(config_get('charts_font_path'), 10);
//$Test->drawTitle(60,22,"My pretty graph",50,50,50,585);
$Test->Stroke();
}
function date_range($first, $last, $step = '+1 day', $output_format = 'd/m/Y' ) {
    $first = date('m/d/Y H:00', strtotime($first)/*-(24*60*60)*/);
    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while( $current <= $last ) {

        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }

    return $dates;
}

function date_dif($first, $last){
    $first = strtotime("$first");
    $last = strtotime("$last");
    $datediff = $last - $first;

    return round($datediff / (60 * 60 * 24));
}

function hour_dif($first, $last){
    $first = strtotime("$first");
    $last = strtotime("$last");
    $datediff = $last - $first;

    return round($datediff / (60 * 60));
}

function withoutData(){
    $grapW = 1500;
    $Test = new pChart($grapW, 230);
    $Test->setFontProperties(config_get('charts_font_path'), 10);
    $Test->setGraphArea(40, 7, $grapW - 40, 205);
    $Test->drawGraphArea(252, 252, 252);
    $Test->drawTitle(60,22,"Aguardando inicio dos testes",50,50,50,585);
    $Test->Stroke();
    exit(0);
}

function init_args(&$dbHandler) {
    $iParams = array("apikey" => array(tlInputParameter::STRING_N, 0, 64),
        "tproject_id" => array(tlInputParameter::INT_N),
        "build" => array(tlInputParameter::INT_N));
    $args = new stdClass();
    R_PARAMS($iParams, $args);

    if (!is_null($args->apikey)) {
        $cerbero = new stdClass();
        $cerbero->args = new stdClass();
        $cerbero->args->tproject_id = $args->tproject_id;
        $cerbero->args->build = $args->build;

        if (strlen($args->apikey) == 32) {
            $cerbero->args->getAccessAttr = true;
            $cerbero->method = 'checkRights';
            $cerbero->redirect_target = "../../login.php?note=logout";
            setUpEnvForRemoteAccess($dbHandler, $args->apikey, $cerbero);
        } else {
            $args->addOpAccess = false;
            $cerbero->method = null;
            $cerbero->args->getAccessAttr = false;
            setUpEnvForAnonymousAccess($dbHandler, $args->apikey, $cerbero);
        }
    } else {
        testlinkInitPage($dbHandler, true, false, "checkRights");
        $args->tproject_id = isset($_SESSION['testprojectID']) ? intval($_SESSION['testprojectID']) : 0;
    }

    return $args;
}

function checkRights(&$db, &$user) {
    return $user->hasRight($db, 'testplan_metrics');
}
