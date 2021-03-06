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
$timetype = $_GET['timetype'];
$rightbuilds = $_GET['buildvalid'];
$grapw = $_GET['width'];
$cumulative = $_GET['cumulative'];
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
    $datelist = array();
    if($timetype == 'd'){
        $data = $metricsMgr->getCustomExecBySubPerXDays($build,$sub,$periods,$rightbuilds,$clean);
        $begin = $data[0]['date_time'];
        $last = end($data)['date_time'];
        $datelist = date_range($begin, date('m/d/Y',strtotime($last)/*+(60*60*24)*/), "+$periods day", 'd/m/Y');
        if($begin == $last) withoutData();
        
    }else if($timetype == 'h'){
        $data = $metricsMgr->getCustomExecBySubPerXHours($build,$sub,$periods,$rightbuilds,$clean);
        $begin = $data[0]['date_time'];
        $last = end($data)['date_time'];
        $datelist = date_range($begin, date('m/d/Y H:00',strtotime($last)+(60*60*24)), "+$periods hour", 'd/m/Y H:00');
        if($begin == $last) withoutData();
        
    }else if($timetype = 'r'){
        $begin = $data[0]['date_time'];
        $last = end($data)['date_time'];
        $days = date_dif($begin,$last);
        $daysPerPeriod = ceil($days/$periods);
        if($daysPerPeriod>1)$data = $metricsMgr->getCustomExecBySubPerXDays($build,$sub,$daysPerPeriod,$rightbuilds,$clean);
        else if($daysPerPeriod == 0){
            $data = $metricsMgr->getCustomExecBySubPerXHours($build,$sub,1,$rightbuilds,$clean);
            $begin = $data[0]['execution_ts'];
            $last = end($data)['execution_ts'];
            $data = $metricsMgr->getCustomExecBySubPerXHours($build,$sub,1,$rightbuilds,$clean);
        }else $daysPerPeriod = 1;
        $begin = $data[0]['date_time'];
        $last = end($data)['date_time'];
        if($daysPerPeriod != 0)$datelist = date_range($begin, date('m/d/Y',strtotime($last)+(60*60*24)), "+$daysPerPeriod day", 'd/m/Y');
        else {
            $hours = hour_dif($begin,$last);
            if($hours != 0)$datelist = date_range($begin, $last, "+".ceil($hours/$periods)." hour", 'd/m/Y H:00');
            else withoutData();
        }
    }
    $listdate = array_flip($datelist);
    /*var_dump($listdate);
    var_dump($data);/**/
    $values = array();
    $valuesC = array();
    $valuesdebug = array();
    $labels = array();
    $names = array();

    foreach ($data as $reg) {
        $datahora = ($timetype == 'r' || $timetype == 'h')?($daysPerPeriod > 0)?date('d/m/Y',strtotime($reg['date_time'])):date('d/m/Y H:00',strtotime($reg['date_time'])):date('d/m/Y',strtotime($reg['date_time']));//var_dump($datahora);
        $valuesdebug[$resultsCfg['code_status'][$reg['status']]][$datahora] = $reg['exec'];
        $valuesC[$resultsCfg['code_status'][$reg['status']]] = 0;
        if(isset($listdate[$datahora])){//verificando se a data precisa de correção depois das inúmeras conversões seguidas
            //$values[lang_get($resultsCfg['status_label'][$resultsCfg['code_status'][$reg['status']]])][$datahora] = $reg['exec'];
            $values[$resultsCfg['code_status'][$reg['status']]][$datahora] = $reg['exec'];
        }
        else {
            $datahora = substr($datahora,0,1).(substr($datahora,1,1)+1).substr($datahora,-8);//corrigindo caso tenha ficado com uma hora de atraso devido ao reajuste na coonversão do horário de verão + utc. o que acarreta em um dia de atrado
            if(isset($listdate[$datahora])){
                //$values[lang_get($resultsCfg['status_label'][$resultsCfg['code_status'][$reg['status']]])][$datahora] = $reg['exec'];
                $values[$resultsCfg['code_status'][$reg['status']]][$datahora] = $reg['exec'];
            }else{//algum erro não previsto
                var_dump('data não válida na divisão dos períodos: '.date('d/m/Y H:00',strtotime($reg['date_time'])));
            }
        }
    }
    $labels = $datelist;
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
    foreach ($labels as $lb) {
        foreach ($values as &$val) {
            if (!isset($val[$lb])) {
                $val[$lb] = 0;
                uksort($val, 'datesort');
            }
        }
    }

    if($cumulative == "true"){
        foreach ($values as &$val){
            $acc = 0;
            foreach ($val as &$data){
                $tmp = $data;
                $data += $acc;
                $acc += $tmp;
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
    }//var_dump($values);
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
    /*var_dump($daysPerPeriod);*/
    //var_dump($data);
    /*var_dump($values);
    var_dump($labels);/**/
echo'<html>'
. '<head><script type="text/javascript" src="../../vendor/Argo/VanBase/jquery.js"></script>
 <script type="text/javascript" src="../../vendor/Argo/VanBase/i18n.js"></script>
 <script type="text/javascript" src="../../vendor/Argo/VanBase/VanBase.js"></script>
 <script type="text/javascript" src="../../vendor/Argo/VanBase/VanCharts.js"></script>
 </head>
 <body>'.
 "<div id=\"chart\" style=\"width: 1500px; height: 300px;position:relative\"></div>".
 "</body>"
."<script>"
."piechart = new VanCharts(eval('(";/**/
    echo '{"series": {"lineStyle": 5,"markerType": "RoundFilledMarker"},"legend": {"font": {"fontName": "Meiryo UI"}},"chartType": "Line","data": {"series": [';
    $cont = 0;
    foreach ($values as $stats=>$conti){
        echo '{"seriesName":"'.lang_get($resultsCfg['status_label'][$stats]).'","value":[';
        foreach($conti as $chave2=>$val2){
            //var_dump($val2);
            if(end_key($conti)!==$chave2)echo $val2.',';
            else echo $val2;
            
        }
        echo '],"seriesIndex":'.$cont++;
        if(end_key($values)!==$stats){echo '},';}
        else {echo "}";}
    }
    echo '],"category":[';
    foreach($labels as $chave=>$lbl){
        if(!(end_key($labels)===$chave)){echo '"'.$lbl.'",';}
        else echo '"'."$lbl".'"';
    }
       $colostring = "";
$first = 0;
foreach($values as $chave=>$valor){
    $colorstring .= " \"#{$resultsCfg['charts']['status_colour'][$chave]}\",";
}
$colorstring.="\"#FFFFFF\"";
    echo ']},"interaction": {"draggable": true}, "tooltip": { "isShowMutiSeries": false, "valueFormat": "#.##", "labelContent": "${VALUE}" }, "plot": { "yAxis": { "position": "left", "mainGridStyle": 1, "axisType": "ValueAxis", "font": {"fontName": "Century Gothic"}, "gap": 0 },'
    . '"color": { "colorStyle": "fill", "colorList": ['.$colorstring.']  }, "xAxis": { "position": "bottom", "axisType": "CategoryAxis", "drawBetweenTick": true } }}';
    echo ")'), $(\"#chart\"));"
. "</script></html>";

    //var_dump($metricsMgr->getExecStatusPerBuild($build));
    // Dataset definition
    /*$DataSet = new pData;
    foreach ($values as $chave => $value) {
        $DataSet->AddPoint($value, $chave);
        $DataSet->SetSerieName($chave, $chave);
    }
    $DataSet->AddAllSeries();
    $DataSet->AddPoint($labels, 'Serie3');
    $DataSet->SetAbsciseLabelSerie('Serie3');
    //$DataSet->SetAbsciseLabelSerie("Serie1");
    // Initialise the graph
    //$grapW = (isset($grapw))?$grapw:1500;
    $grapW = count($labels)*($timetype == 'd'?100:130);
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
    $Test->Stroke();/**/
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
function end_key($arr){
    end($arr);
    return key($arr);
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
