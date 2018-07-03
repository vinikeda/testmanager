<?php
/** 
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * @filesource  overallPieChart.php
 * @package     TestLink
 * @author      franciscom
 * @copyright   2005-2013, TestLink community
 * @copyright   
 * @link        http://www.testlink.org/
 *
 * @internal revisions
 * @since 1.9.10
 *
**/
$build = $_GET['build'];
require_once('../../config.inc.php');
require_once('common.php');
define('PCHART_PATH','../../third_party/pchart');
include(PCHART_PATH . "/pChart/pData.class");   
include(PCHART_PATH . "/pChart/pChart.class");
require_once('../functions/tlTestPlanMetrics.class.php');

$resultsCfg = config_get('results');
$chart_cfg = $resultsCfg['charts']['dimensions']['overallPieChart'];

$args = init_args($db);
$tplan_mgr = new testplan($db);
echo'<html>'
. '<head><script type="text/javascript" src="../../vendor/Argo/VanBase/jquery.js"></script>
 <script type="text/javascript" src="../../vendor/Argo/VanBase/i18n.js"></script>
 <script type="text/javascript" src="../../vendor/Argo/VanBase/VanBase.js"></script>
 <script type="text/javascript" src="../../vendor/Argo/VanBase/VanCharts.js"></script>
 </head>
 <body>'.
 "<div id=\"chart\" style=\"width: 378px; height: 200px;position:relative\"></div>".
 "</body>"
."<script>"
."piechart = new VanCharts(eval('(";/**/
$metricsMgr = new tlTestPlanMetrics($db);
$totals2 = $metricsMgr->getExecStatusPerBuild($args->tplan_id);//var_dump($totals2);
if(count($totals2) >0){
}else {
    $totals2 = $metricsMgr->getExecStatusPerBuildPast($args->tplan_id);//var_dump($totals2);
}
$totals2 = $totals2[$build];//encontrar uma forma de passar a build.
if(count($totals2) ==0){
    $totals2['not_run'] = 0.0001;//colocado para mostrar 0
}
echo'{"chartType": "Pie","legend": {"font": {"fontName": "Meiryo UI"}},  "data": [';

    //if(isset($totals2['total']))$result['total'] = $totals2['total'];
    if(isset($totals2['passed']))/*echo '["passed",'.$totals2['passed'].'],';*/$result['passed'] = $totals2['passed'];
    if(isset($totals2['not_run']))/*echo '["not_run",'.$totals2['not_run'].'],';*/$result['not_run'] = $totals2['not_run'];
    //if(isset($totals2['executed']))$result['executed'] = $totals2['executed'];
    if(isset($totals2['blocked']))/*echo '["blocked",'.$totals2['blocked'].'],';*/$result['blocked'] = $totals2['blocked'];
    if(isset($totals2['failed']))/*echo '["failed",'.$totals2['failed'].'],';*/$result['failed'] = $totals2['failed'];
    if(isset($totals2['analise']))/*echo '["analise",'.$totals2['analise'].'],';*/$result['analise'] = $totals2['analise'];
    if(isset($totals2['warning']))/*echo '["warning",'.$totals2['warning'].']';*/$result['warning'] = $totals2['warning'];

$colostring = "";
$first = 0;
foreach($result as $chave=>$valor){
    $colorstring .= " \"#{$resultsCfg['charts']['status_colour'][$chave]}\",";
    if($first == 0) $first = 1;else{echo ',';}
    echo '["'.$chave.'",'.$totals2[$chave].']';
}


$colorstring.="\"#FFFFFF\"";
echo '],"label": {"textAttr": {"rotation": 0,"font": {"fontName": "Meiryo UI","style": "","color": "rgb(0,0,0)","size": 12},"alignText": 0},"position": "inside","isShowMutiSeries": false,"labelContent": "${SERIES},${VALUE}","leadLine": true}, "tooltip": {    "percentFormat": "#.##%",    "isShowMutiSeries": false,    "valueFormat": "#.##",    "labelContent": "${SERIES}${BR}${CATEGORY}${BR}${VALUE}${BR}${PERCENT}"  },  "plot": {"color": {    "colorStyle": "fill",    "colorList": [      '.$colorstring.'    ]  }}}';
echo ")'), $(\"#chart\"));"
. "</script></html>";

function checkRights(&$db,&$user)
{
  return $user->hasRight($db,'testplan_metrics');
}


/**
 * 
 *
 */
function init_args(&$dbHandler)
{
  $iParams = array("apikey" => array(tlInputParameter::STRING_N,0,64),
                   "tproject_id" => array(tlInputParameter::INT_N), 
                   "tplan_id" => array(tlInputParameter::INT_N));
  $args = new stdClass();
  R_PARAMS($iParams,$args);

  if( !is_null($args->apikey) )
  {
    $cerbero = new stdClass();
    $cerbero->args = new stdClass();
    $cerbero->args->tproject_id = $args->tproject_id;
    $cerbero->args->tplan_id = $args->tplan_id;
    
    if(strlen($args->apikey) == 32)
    {
      $cerbero->args->getAccessAttr = true;
      $cerbero->method = 'checkRights';
      $cerbero->redirect_target = "../../login.php?note=logout";
      setUpEnvForRemoteAccess($dbHandler,$args->apikey,$cerbero);
    }
    else
    {
      $args->addOpAccess = false;
      $cerbero->method = null;
      $cerbero->args->getAccessAttr = false;
      setUpEnvForAnonymousAccess($dbHandler,$args->apikey,$cerbero);
    }  
  }
  else
  {
    testlinkInitPage($dbHandler,true,false,"checkRights");  
    $args->tproject_id = isset($_SESSION['testprojectID']) ? intval($_SESSION['testprojectID']) : 0;
  }

  return $args;
}
