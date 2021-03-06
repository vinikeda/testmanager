<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Library for documents generation
 *
 * @filesource  print.inc.php
 *
 * @package   TestLink
 * @copyright 2007-2015, TestLink community 
 * @uses      printDocument.php
 *
 *
 * @internal revisions
 * @since 1.9.14
 *
 */ 

$impar = 0;

/** uses get_bugs_for_exec() */
require_once("exec.inc.php");
require_once("lang_api.php");

/**
 * render a requirement as HTML code for printing
 * 
 * @author Andreas Simon
 * 
 * @param resource $db
 * @param array $node the node to be printed
 * @param array $options
 *        displayDates: true display creation and last edit date (including hh:mm:ss)
 *
 * @param string $tocPrefix Prefix to be printed in TOC before title of node
 * @param int $level
 * @param int $tprojectID
 * 
 * @return string $output HTML Code
 *
 * @internal revisions
 *
 */
function renderReqForPrinting(&$db,$node, &$options, $tocPrefix, $reqLevel, $tprojectID) 
{
	
  
  static $tableColspan;
  static $firstColWidth;
  static $labels;
  static $title_separator;
  static $req_mgr;
  static $tplan_mgr;
  static $req_cfg;
  static $req_spec_cfg;
  static $decodeReq;
  static $force = null;
  static $basehref;

  
  if (!$req_mgr) 
  {
    $basehref = $_SESSION['basehref'];
    $req_cfg = config_get('req_cfg');
    $req_spec_cfg = config_get('req_spec_cfg');
    $firstColWidth = '20%';
    $tableColspan = 2;
    $labels = array('requirement' => 'requirement', 'status' => 'status', 
                    'scope' => 'scope', 'type' => 'type', 'author' => 'author',
                    'relations' => 'relations','not_aplicable' => 'not_aplicable',
                    'coverage' => 'coverage','last_edit' => 'last_edit',
                    'custom_field' => 'custom_field', 'relation_project' => 'relation_project',
                    'related_tcs' => 'related_tcs', 'version' => 'version', 
                    'revision' => 'revision', 'attached_files' => 'attached_files');
                    
    $labels = init_labels($labels);
      
    $decodeReq = array();
    $decodeReq['status'] = init_labels($req_cfg->status_labels);
    $decodeReq['type'] = init_labels($req_cfg->type_labels);
      
      
    $force['displayVersion'] = isset($options['displayVersion']) ? $options['displayVersion'] : false;
    $force['displayLastEdit'] = isset($options['displayLastEdit']) ? $options['displayLastEdit'] : false;
    
      
    $title_separator = config_get('gui_title_separator_1');
    $req_mgr = new requirement_mgr($db);
    $tplan_mgr = new testplan($db);
  }
  
  $versionID = isset($node['version_id']) ? intval($node['version_id']) : requirement_mgr::LATEST_VERSION;
  $revision = isset($node['revision']) ? intval($node['revision']) : null;

  $getOpt = array('renderImageInline' => true);
  if( is_null($revision) )
  {
    // will get last revision of requested req version 
    $dummy = $req_mgr->get_by_id($node['id'],$versionID,1,$getOpt);  
  }
  else
  {
    $dummy = $req_mgr->get_version_revision($versionID,array('number' => $revision),$getOpt);  
    if(!is_null($dummy))
    {
      // do this way instead of using SQL alias on get_version_revision(), in order
      // to avoid issues (potential not confirmed)on different DBMS.
      $dummy[0]['id'] = $dummy[0]['req_id'];
    }
  }
  
  $req = $dummy[0];

  // update with values got from req, this is needed if user did not provide it
  $versionID = $req['version_id'];
  $revision = $req['revision'];

  //$name =  htmlspecialchars($req["req_doc_id"] . $title_separator . $req['title']);
  $name =  htmlspecialchars($req['title']);

  // change table style in case of single req printing to not be indented
  $table_style = "";
  if (isset($options['docType']) && $options['docType'] == SINGLE_REQ) 
  {
    $table_style = "style=\"margin-left: 0;\"";
  }
	
  $output = "<table class=\"req\" $table_style><tr><th colspan=\"$tableColspan\">" .
            "<span class=\"label\">{$labels['requirement']}:</span> " . $name . "</th></tr>\n"; 
  
  if( $force['displayVersion'] )
  {
    foreach(array('version','revision') as $key)
    {
      $output .= '<tr><td valign="top">' . 
                 '<span class="label">'.$labels[$key].':</span></td>' .
                 '<td>' . $req[$key]. "</td></tr>\n";
    }    
  }
  
  
  if ($options['toc']) 
  {
    $options['tocCode'] .= '<p style="padding-left: ' . 
                             (15 * $reqLevel).'px;"><a href="#' . prefixToHTMLID('req'.$node['id']) . '">' .
                           $name . '</a></p>';
    $output .= '<a name="' . prefixToHTMLID('req'.$node['id']) . '"></a>';
  }

/*   if ($options['req_author']) 
  {
    $output .= '<tr><td valign="top">' . 
               '<span class="label">'.$labels['author'].':</span></td>' .
               '<td>' . htmlspecialchars(gendocGetUserName($db, $req['author_id']));

    if(isset($options['displayDates']) && $options['displayDates'])
    {
      $dummy = null;
          $output .= ' - ' . localize_dateOrTimeStamp(null,$dummy,'timestamp_format',$req['creation_ts']);
    }
    $output .= "</td></tr>\n";

    if ($req['modifier_id'] > 0) 
    {
      // add updater if available and differs from author OR forced
      if ($force['displayLastEdit'] || ($req['modifier_id'] != $req['modifier_id']) )
      {
        $output .= '<tr><td valign="top">' . 
                   '<span class="label">'. $labels['last_edit'] . ':</span></td>' .
                   '<td>' . htmlspecialchars(gendocGetUserName($db, $req['modifier_id']));
                     
        if(isset($options['displayDates']) && $options['displayDates'])
        {
          $dummy = null;
          $output .= ' - ' . localize_dateOrTimeStamp(null,$dummy,'timestamp_format',
                                $req['modification_ts']);
        }  
        $output .= "</td></tr>\n";
      }  
    }
  } */
              
  foreach(array('status','type') as $key)
  {
    if($options['req_' . $key])
    {
      $output .= '<tr><td width="' . $firstColWidth . '"><span class="label">' . 
                 $labels[$key] . "</span></td>" .
                 "<td>" . $decodeReq[$key][$req[$key]] . "</td></tr>";
    }
  }            
  
  if ($options['req_coverage']) 
  {
    $current = count($req_mgr->get_coverage($req['id']));
    $expected = $req['expected_coverage'];
    $coverage = $labels['not_aplicable'] . " ($current/0)";
    if ($expected) 
    {
      $percentage = round(100 / $expected * $current, 2);
      $coverage = "{$percentage}% ({$current}/{$expected})";
    }
      
    $output .= "<tr><td width=\"$firstColWidth\"><span class=\"label\">" . $labels['coverage'] .
               "</span></td>" . "<td>$coverage</td></tr>";
  } 
  
  if ($options['req_scope']) 
  {
    $output .= "<tr><td colspan=\"$tableColspan\"> <br/>" . $req['scope'] . "</td></tr>";
  }
    
  if ($options['req_relations']) 
  {
    $relations = $req_mgr->get_relations($req['id']);

    if ($relations['num_relations']) 
    {
      $output .= "<tr><td width=\"$firstColWidth\"><span class=\"label\">" . $labels['relations'] . 
                 "</span></td><td>";
  
      $filler = str_repeat('&nbsp;',5); // MAGIC allowed    
      foreach ($relations['relations'] as $rel) 
      {
        $output .= "{$rel['type_localized']}: <br/>{$filler}" . 
                   htmlspecialchars($rel['related_req']['req_doc_id']) . $title_separator .
                   htmlspecialchars($rel['related_req']['title']) . "</br>" .
                   "{$filler}{$labels['status']}: " .
                   "{$decodeReq['status'][$rel['related_req']['status']]} <br/>";
                   
        if ($req_cfg->relations->interproject_linking) 
        {
          $output .= "{$filler}{$labels['relation_project']}: " .
                     htmlspecialchars($rel['related_req']['testproject_name']) . " <br/>";
        }
      }
      
      $output .= "</td></tr>";
    }
  } 
  
  if ($options['req_linked_tcs']) 
  {
    $req_coverage = $req_mgr->get_coverage($req['id']);
    
    if (count($req_coverage)) 
    {
      $output .=  "<tr><td width=\"$firstColWidth\"><span class=\"label\">" . $labels['related_tcs'] . 
                  "</span></td>" . "<td>";
      foreach ($req_coverage as $tc) 
      {
        $output .= htmlspecialchars($tc['tc_external_id'] . $title_separator . $tc['name']) . "<br/>";
      }
                 
      $output .= "</td></tr>";
    }
  }
  
  if ($options['req_cf']) 
  {
    $childID = (is_null($revision) || $req['revision_id'] < 0) ? $req['version_id'] : $req['revision_id'];
    $linked_cf = $req_mgr->get_linked_cfields($req['id'], $childID);
    if ($linked_cf)
    {
      foreach ($linked_cf as $key => $cf) 
      {
        $cflabel = htmlspecialchars($cf['label']);
        $value = htmlspecialchars($cf['value']);
                
        $output .= "<tr><td width=\"$firstColWidth\"><span class=\"label\">" . 
                   $cflabel . "</span></td>" . "<td>$value</td></tr>";
      }
    }
  }

  // Display Images Inline (Always)
  $attachSet =  $req_mgr->getAttachmentInfos($req['id']);
  
  if (count($attachSet))
  {
    $output .= "<tr><td width=\"$firstColWidth\"><span class=\"label\">" .
               $labels['attached_files'] . "</span></td><td>";
    
    foreach($attachSet as $fitem)
    {
      if($fitem['is_image'])
      {
        $output .= "<li>" . htmlspecialchars($fitem['file_name']) . "</li>";
        $output .= '<li>' . '<img src="' . $basehref . 
                   'lib/attachments/attachmentdownload.php?skipCheck=1&id=' . $fitem['id'] . '">';
      }  
      else
      {
        $output .= '<li>' . '<a href="' . $basehref . 
                   'lib/attachments/attachmentdownload.php?skipCheck=1&id=' . $fitem['id'] . 
                   '" ' . ' target="#blank" > ' . htmlspecialchars($fitem['file_name']) . '</a>';
      }  
    }
    $output .="</td></tr>";
  }


  $output .= "</table><br/>";

  return $output;
}


/**
 * render a requirement specification node as HTML code for printing
 * 
 * @author Andreas Simon
 * 
 * @param resource $db
 * @param array $node the node to be printed
 * @param array $options
 * @param string $tocPrefix Prefix to be printed in TOC before title of node
 * @param int $level
 * @param int $tprojectID
 * 
 * @return string $output HTML Code
 */
function renderReqSpecNodeForPrinting(&$db, &$node, &$options, $tocPrefix, $rsLevel, $tprojectID) 
{
	
  static $tableColspan;
  static $firstColWidth;
  static $labels;
  static $title_separator;
  static $req_spec_mgr;
  static $tplan_mgr;
  static $req_spec_cfg;
  static $reqSpecTypeLabels;
  static $nodeTypes;
  static $basehref;

  $output = '';
  $reLevel = ($rsLevel > 0) ? $rsLevel : 1;

  if (!$req_spec_mgr) 
  {
    $basehref = $_SESSION['basehref'];
    $req_spec_cfg = config_get('req_spec_cfg');
    $firstColWidth = '20%';
    $tableColspan = 2;
    $labels = array('requirements_spec' => 'requirements_spec', 
                    'scope' => 'scope', 'type' => 'type', 'author' => 'author',
                    'relations' => 'relations', 'overwritten_count' => 'req_total',
                    'coverage' => 'coverage','revision' => 'revision','attached_files' => 'attached_files',
                    'undefined_req_spec_type' => 'undefined_req_spec_type',
                    'custom_field' => 'custom_field', 'not_aplicable' => 'not_aplicable');

    $labels = init_labels($labels);
    $reqSpecTypeLabels = init_labels($req_spec_cfg->type_labels);
    $title_separator = config_get('gui_title_separator_1');
    $req_spec_mgr = new requirement_spec_mgr($db);
    $tplan_mgr = new testplan($db);
    $nodeTypes = array_flip($tplan_mgr->tree_manager->get_available_node_types());
  }
  
  switch($nodeTypes[$node['node_type_id']])
  {
    case 'requirement_spec_revision':
      $spec = $req_spec_mgr->getRevisionByID($node['id']);
      $spec_id = $spec['parent_id'];
      $who = array('parent_id' => $spec['parent_id'],'item_id' => $spec['id'],
                   'tproject_id' => $spec['testproject_id']);
    break;
    
    case 'requirement_spec':
      $spec = $req_spec_mgr->get_by_id($node['id']);
      $spec_id = $spec['id'];
      $who = array('parent_id' => $spec['id'],'item_id' => $spec['revision_id'],
                   'tproject_id' => $spec['testproject_id']);
    break;
  } 
  //$name = htmlspecialchars($spec['doc_id'] . $title_separator . $spec['title']);
  $name = htmlspecialchars($spec['title']);
  
  $docHeadingNumbering = '';
  if ($options['headerNumbering']) {
    $docHeadingNumbering = "$tocPrefix. ";
  }
  
  if($options['docType'] != SINGLE_REQSPEC) 
  {
    $output = '<p style="page-break-before: always"></p>';
  }

  // Remember that only H1 to H6 exists
  $reLevel = ($reLevel > 6) ? 6 : $reLevel;
  $reLevel = ($reLevel < 1) ? 1 : $reLevel;

  $output .= "<p>&nbsp;</p><table class=\"req_spec\"><tr><th colspan=\"$tableColspan\">" .
             "<h{$reLevel} class=\"doclevel\"> <span class=\"label\">{$docHeadingNumbering}{$labels['requirements_spec']}:</span> " .
             $name . "</h{$reLevel}></th></tr>\n";
     
  if ($options['toc'])
  {
    $spacing = ($reLevel == 2) ? "<br>" : "";
    $options['tocCode'] .= $spacing.'<b><p style="padding-left: '.(10 * $reLevel).'px;">' .
                          '<a href="#' . prefixToHTMLID($tocPrefix) . '">' . $name . "</a></p></b>\n";
    $output .= "<a name='". prefixToHTMLID($tocPrefix) . "'></a>\n";
  }
  $output .=  '<tr><td width="' . $firstColWidth . '"><span class="label">' . 
              $labels['revision'] . "</span></td><td> " . 
              $spec['revision'] . "</td></tr>\n";
  
/*   if ($options['req_spec_author']) 
  {
    // get author name for node
    $author = tlUser::getById($db, $spec['author_id']);
    $whois = (is_null($author)) ? lang_get('undefined') : $author->getDisplayName();
    $output .=  '<tr><td width="' . $firstColWidth . '"><span class="label">' . 
                $labels['author'] . "</span></td><td> " . 
                htmlspecialchars($whois) . "</td></tr>\n";
  } */
  
  if ($options['req_spec_type']) 
  {
    $output .= '<tr><td width="' . $firstColWidth . '"><span class="label">' . 
               $labels['type'] . "</span></td>" . "<td>";
               
    if( isset($reqSpecTypeLabels[$spec['type']]) )
    {   
      $output .= $reqSpecTypeLabels[$spec['type']];
    }
    else
    {
      $output .= sprintf($labels['undefined_req_spec_type'],$spec['type']);    
    }
    $output .= "</td></tr>";
  }
  
  if ($options['req_spec_overwritten_count_reqs']) 
  {
    $current = $req_spec_mgr->get_requirements_count($spec_id);   // NEEDS REFACTOR
    $expected = $spec['total_req'];
    $coverage = $labels['not_aplicable'] . " ($current/0)";
    if ($expected) 
    {
      $percentage = round(100 / $expected * $current, 2);
      $coverage = "{$percentage}% ({$current}/{$expected})";
    }
    
    $output .= '<tr><td width="' . $firstColWidth . '"><span class="label">' . 
               $labels['overwritten_count'] . " (" . $labels['coverage'] . ")</span></td>" .
               "<td>" . $coverage . "</td></tr>";
  }

  if ($options['req_spec_scope']) 
  {
    $output .= "<tr><td colspan=\"$tableColspan\">" . $spec['scope'] . "</td></tr>";
  }
  
  if ($options['req_spec_cf']) 
  {
  
    $linked_cf = $req_spec_mgr->get_linked_cfields($who);
    if ($linked_cf)
    {
      foreach ($linked_cf as $key => $cf) 
      {
        $cflabel = htmlspecialchars($cf['label']);
        $value = htmlspecialchars($cf['value']);
        
        $output .= "<tr><td width=\"$firstColWidth\"><span class=\"label\">" . 
                   $cflabel . "</span></td>" .
                   "<td>$value</td></tr>";
      }
    }
  }
  
  $attachSet =  $req_spec_mgr->getAttachmentInfos($spec_id);
  if (count($attachSet))
  {
    $output .= "<tr><td width=\"$firstColWidth\"><span class=\"label\">" .
               $labels['attached_files'] . "</span></td><td><ul>";
    foreach($attachSet as $item)
    {
      $fname = "";
      if ($item['title'])
      {
        $fname .=  htmlspecialchars($item['title']) . " : ";
      }
      $fname .= htmlspecialchars($item['file_name']);
      $output .= "<li>$fname</li>";
    }
    $output .="</ul></td></tr>";
  }
  
  $output .= "</table><br/>\n";
  
  return $output;
}


/**
 * render a complete tree, consisting of mixed requirement and req spec nodes, 
 * as HTML code for printing
 * 
 * @author Andreas Simon
 * 
 * @param resource $db
 * @param array $node the node to be printed
 * @param array $options
 * @param string $tocPrefix Prefix to be printed in TOC before title of each node
 * @param int $level
 * @param int $tprojectID
 * @param int $user_id ID of user which shall be printed as author of the document
 * 
 * @return string $output HTML Code
 */
function renderReqSpecTreeForPrinting(&$db, &$node, &$options,$tocPrefix, $rsCnt, $rstLevel, $user_id,
                                      $tplan_id = 0, $tprojectID = 0) 
{
 
  static $tree_mgr;
  static $map_id_descr;
  static $tplan_mgr;
  $code = null;

  if(!$tree_mgr)
  { 
       $tplan_mgr = new testplan($db);
      $tree_mgr = new tree($db);
       $map_id_descr = $tree_mgr->node_types;
   }
   $verbose_node_type = $map_id_descr[$node['node_type_id']];
   
    switch($verbose_node_type)
  {
    case 'testproject':

      break;

    case 'requirement_spec':
            $tocPrefix .= (!is_null($tocPrefix) ? "." : '') . $rsCnt;
            $code .= renderReqSpecNodeForPrinting($db,$node,$options,
                               $tocPrefix, $rstLevel, $tprojectID);
    break;

    case 'requirement':
      $tocPrefix .= (!is_null($tocPrefix) ? "." : '') . $rsCnt;
      $code .= renderReqForPrinting($db, $node, $options,
                                    $tocPrefix, $rstLevel, $tprojectID);
      break;
  }
  
  if (isset($node['childNodes']) && $node['childNodes'])
  {
    
    $childNodes = $node['childNodes'];
    $rsCnt = 0;
         $children_qty = sizeof($childNodes);
    for($i = 0;$i < $children_qty ;$i++)
    {
      $current = $childNodes[$i];
      if(is_null($current))
      {
        continue;
            }
            
      if (isset($current['node_type_id']) && 
          $map_id_descr[$current['node_type_id']] == 'requirement_spec')
      {
          $rsCnt++;
      }
      
      $code .= renderReqSpecTreeForPrinting($db, $current, $options,$tocPrefix, $rsCnt, 
                                            $rstLevel+1, $user_id, $tplan_id, $tprojectID);
    }
  }
  
  if ($verbose_node_type == 'testproject')
  {
    if ($options['toc'])
    {
      $code = str_replace("{{INSERT_TOC}}",$options['tocCode'],$code);
    }
  }

  return $code;
}


/**
 * render HTML header
 * Standard: HTML 4.01 trans (because is more flexible to bugs in user data)
 * 
 * @param string $title
 * @param string $base_href Base URL
 * 
 * @return string html data
 */
function renderHTMLHeader($title,$base_href,$doc_type,$jsSet=null)
{
	
  $themeDir = config_get('theme_dir');
  $docCfg = config_get('document_generator');
  
  $cssFile = $base_href . $themeDir;
  switch ($doc_type) 
  {
    case DOC_REQ_SPEC:
    case SINGLE_REQ:
    case SINGLE_REQSPEC:
      $cssFile .= $docCfg->requirement_css_template;
    break;

    case DOC_TEST_SPEC:
    case DOC_TEST_PLAN_DESIGN:
    case DOC_TEST_PLAN_EXECUTION:
    case DOC_TEST_PLAN_EXECUTION_ON_BUILD:
	case DOC_FAIL_ONLY_TEST_PLAN_EXECUTION_ON_BUILD:
	case DOC_PASS_ONLY_TEST_PLAN_EXECUTION_ON_BUILD:
    case SINGLE_TESTCASE:
    default:
      $cssFile .= $docCfg->css_template;
    break;
  }

  $output = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>\n";
  $output .= "<html>\n<head>\n";
  $output .= '<meta http-equiv="Content-Type" content="text/html; charset=' . config_get('charset') . '">';
  $output .= '<title>' . htmlspecialchars($title). "</title>\n";
  $output .= '<link type="text/css" rel="stylesheet" href="'. $cssFile ."\" />\n";
  
  // way to add CSS directly to the exported file (not used - test required)
  // $docCss = file_get_contents(TL_ABS_PATH . $docCfg->css_template);
  // $output .= '<style type="text/css" media="all">'."\n<!--\n".$docCss."\n-->\n</style>\n";
  //$output .= '<style type="text/css" media="print">.notprintable { display:none;}</style>';
  
  $output .= '<style>';
  
  $output .= '@page';
  $output .= '{';
  $output .= '	mso-page-orientation: landscape;';
  
// margin: 25px 50px 75px 100px;
// top margin is 25px
// right margin is 50px
// bottom margin is 75px
// left margin is 100px
  
  $output .= '	size: 21cm 29.7cm;    margin:1cm 2cm 1cm 2cm;';
  $output .= '}';
  $output .= '@page Section1 {';
  $output .= '	mso-header-margin:.5in;';
  $output .= '	mso-footer-margin:.5in;';
  $output .= '	mso-header: h1;';
  $output .= '	mso-footer: f1;';
  $output .= '	}';
  $output .= 'div.Section1 { page:Section1; }';
  $output .= 'table#hrdftrtbl';
  $output .= '{';
  $output .= '	margin:0in 0in 0in 900in;';
  $output .= '	width:1px;';
  $output .= '	height:1px;';
  $output .= '	overflow:hidden;';
  $output .= '}';
  $output .= 'p.MsoFooter, li.MsoFooter, div.MsoFooter';
  $output .= '{';
  $output .= '	margin:0in;';
  $output .= '	margin-bottom:.0001pt;';
  $output .= '	mso-pagination:widow-orphan;';
  $output .= '	tab-stops:center 3.0in right 6.0in;';
  $output .= '	font-size:12.0pt;';
  $output .= '}';
  
  $output .= '</style>';
  
  if(!is_null($jsSet))
  {  
    foreach($jsSet as $js)
    {
      $output .= "\n" . '<script type="text/javascript" src="' . $base_href . $js . '"';
      $output .= ' language="javascript"></script>' . "\n";   
      $output .= '<script type="text/javascript" language="javascript">' . 
                 "<!-- var fRoot = '" . $base_href . "'; -->" . '</script>' . "\n";   
    }  
  }
  $output .= "\n</head>\n";
  return $output;
}


/**
 * Generate initial page of document
 * 
 * @param object $doc_info data with the next string values: 
 *                  title
 *                  type_name: what does this means ???
 *                  author, tproject_name, testplan_name  
 * @return string html
 * @author havlatm
 */
function renderFirstPage($doc_info)
{
	
  $docCfg = config_get('document_generator');
  $date_format_cfg = config_get('date_format');
  $output = "<body>\n<div class='Section1'>\n";
	
	//colocar no header
	$output .= 	"<table width='900' class=MsoHeader id='hrdftrtbl' border='0' cellspacing='0' cellpadding='0'>"; 
	$output .= 	'<tr>';
	
	$height2 = "height=\"{$docCfg->company_logo_height}\"";
	$width2 = "width=\"{$docCfg->company_logo_width}\"";
	$height1 = "height=\"{$docCfg->acq_gp_logo_height}\"";
	$width1 = "width=\"{$docCfg->acq_gp_logo_width}\"";
	$output .= 	'<th>' . 
				'<div style="mso-element:header" id=h1 >' . 
				'<p style="display:inline; text-align: left;"><img alt="AgTestlink Logo" ' .
				'title="configure using $tlCfg->document_generator->company_logo" ' . $height2 . $width2 . 
				' src="' . $_SESSION['basehref'] . TL_THEME_IMG_DIR . $docCfg->company_logo . '" />' . 
				'&#9;&#9;&#9;&#9;&#9;&#9;&#9;&#9;' . 
				'<img alt="Acquirer Logo" ' . 'title="configure using $tlCfg->document_generator->acq_gp_logo" ' . 
				$height1 . $width1 . ' src="' . $_SESSION['basehref'] . TL_THEME_IMG_DIR . $docCfg->acq_gp_logo . '" />'; 
	
	
	$output .= 	'</div>' . 
				'</th>';  
	$output .= 	'</tr>';
	$output .= 	'</table>';
	
  // Print header //removido
  // if ($docCfg->company_name != '' )
  // {
    // $output .= '<div style="float:right;">' . htmlspecialchars($docCfg->company_name) ."</div>\n";
  // }
  // $output .= "<div>&nbsp;</div><hr />\n";
    
/* 
  if ($docCfg->company_logo != '' )
  {
	$output .= 	'<table width="100%">'; 
    // allow to configure height via config file
    $height = '';
    if (isset($docCfg->acq_gp_logo_height) && $docCfg->acq_gp_logo_height != '') 
    {
      $height = "height=\"{$docCfg->acq_gp_logo_height}\"";
    }
    // configurar comprimento do logo
	$logo_width = '';
	if (isset($docCfg->acq_gp_logo_width) && $docCfg->acq_gp_logo_width != '') 
    {
      $width = "width=\"{$docCfg->acq_gp_logo_width}\"";
    }
	
	$output .= 	'<tr>' .
				'<p style="text-align: left;"><img alt="TestLink Logo" ' .
				'title="configure using $tlCfg->document_generator->acq_gp_logo" ' . $height . $width . 
				' src="' . $_SESSION['basehref'] . TL_THEME_IMG_DIR . $docCfg->acq_gp_logo . '" />' . 
				'</th>';
	
	
	// allow to configure height via config file
    $height = '';
    if (isset($docCfg->company_logo_height) && $docCfg->company_logo_height != '') 
    {
      $height = "height=\"{$docCfg->company_logo_height}\"";
    }
    // configurar comprimento do logo
	$logo_width = '';
	if (isset($docCfg->company_logo_width) && $docCfg->company_logo_width != '') 
    {
      $width = "width=\"{$docCfg->company_logo_width}\"";
    }
	
	$output .= 	'<th>' .  
				'<p style="text-align: right;"><img alt="Acquirer Global Logo" ' .
				'title="configure using $tlCfg->document_generator->company_logo" ' . $height . $width . 
				' src="' . $_SESSION['basehref'] . TL_THEME_IMG_DIR . $docCfg->company_logo . '" />' . 
				'</th>' .  
				'</tr>';
	$output .= 	'</table>';
  } 
*/
  
  $output .= "</div>\n";

  // Print context
  // Report Minimal Description
  // Test Project Name
  // Test Plan Name
  // Build Name (if applicable)
  // Test Suite Name (if applicable)
  //
  
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  
  if($doc_info->type == DOC_TEST_PLAN_DESIGN || $doc_info->type == DOC_TEST_PLAN_EXECUTION){
	  $output .= '<div class="doc_title" style="font-size:180%">' . '<p>' . 'Plano de Testes' . '<p>&nbsp;</p>';
  }
  else{
	$output .= '<div class="doc_title" style="font-size:180%">' . '<p>' . 'Relatório de Execução' . '</p>' . '<p>' . 'Roteiro executado' . '</p>';
  }
  if($doc_info->additional_info != '')
  {
     $output .= '<p>' . $doc_info->additional_info . '</p>';
  }  
  $output .= "</div>\n";
  
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  $output .= '<p>&nbsp;</p>';
  
  $output .= '<div class="doc_title" style="text-align:left;margin: auto; font-size:180%;">' . '<p>';// . $doc_info->tproject_name;
  if($doc_info->type == DOC_TEST_PLAN_EXECUTION_ON_BUILD || $doc_info->type == DOC_FAIL_ONLY_TEST_PLAN_EXECUTION_ON_BUILD || $doc_info->type == DOC_PASS_ONLY_TEST_PLAN_EXECUTION_ON_BUILD){
	$output .= '<br>' . 'Cliente: ';
	$output .= '<br>' . 'Requisitante: ';
  }  

  // if($doc_info->type == DOC_TEST_PLAN_EXECUTION_ON_BUILD || $doc_info->type == DOC_FAIL_ONLY_TEST_PLAN_EXECUTION_ON_BUILD)
  // {
    // $output .= '<br>' . $doc_info->build_name;
  // }
  
  
    // removido
  // $output .= '<div class="summary">' . '<p id="printedby">' . lang_get('printed_by_TestLink_on')." ".
             // strftime($date_format_cfg, time()) . "</p></div>\n";
    
  // Print legal notes removido
  // if ($docCfg->company_copyright != '')
  // {
    // $output .= '<div class="pagefooter" id="copyright">' . $docCfg->company_copyright."</div>\n";
  // }
               
  // if ($docCfg->confidential_msg != '')
  // {
    // $output .= '<div class="pagefooter" id="confidential">' .  $docCfg->confidential_msg . "</div>\n";
  // }
  
  return $output;
}


/**
 * Generate a chapter to a document
 * 
 * @param string $title
 * @param string $content
 * 
 * @return string html
 * @author havlatm
 */
function renderSimpleChapter($title, $content, $addToStyle=null)
{
	
  $output = '';
  if ($content != "")
  {
	/* $output .= '<br style="page-break-before: always;"><br/>';
	//tabela de informações da solução
	$output .= '<b> <p>1. Informações da solução </p>'; 
	
	$output .= '<table width="100%" style="border: 1px solid black; border-collapse: collapse;">';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Nome do solicitante</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">';	
	$output .= '	bolo</td>';
	$output .= '</tr>';
	
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Data da execução</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Modelo do terminal</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Nome da Aplicação - Automação</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Versão da Aplicação - Automação</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Nome da Aplicação - Cliente</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Versão da Aplicação - Cliente</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Nome da Aplicação - Server</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Versão da Aplicação - Server</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Versão da biblioteca</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">LoA Lvl1</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">LoA Lvl2</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Versão do Kernel</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">PCI PTS</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">TQM</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '<tr height="24" height="24px" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Emitido por</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '</table>';
	
	$output .= '<p>2. Realizado por </p>'; 
	$output .= '<table width="100%" style="border: 1px solid black; border-collapse: collapse;">';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<th width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Título</th>';
	$output .= '	<th width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Nome, Data</th>';
	$output .= '</tr>';
	$output .= '<tr height="54" style="border: 1px solid black;">';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '</table>';
	
	$output .= '<p>3. Documentos de Referência </p>'; 
	$output .= '<table width="100%" style="border: 1px solid black; border-collapse: collapse;">';
	$output .= '<tr style="border: 1px solid black;">';
	$output .= '	<th rowspan="3" width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Referências</th>';
	$output .= '	<th height="24" width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Documento</th>';
	$output .= '	<th height="24" width="60%" style="border: 1px solid black; background-color: #E2ECD9;">Nome e versão</th>';
	$output .= '</tr>';
	$output .= '<tr style="border: 1px solid black;">';
	$output .= '	<td height="24" width="20%" style="border: 1px solid black;"></td>';
	$output .= '	<td height="24" width="60%" style="border: 1px solid black;"></td>';
	$output .= '</tr>';
	$output .= '<tr style="border: 1px solid black;">';
	$output .= '	<td height="24" width="20%" style="border: 1px solid black;"></td>';
	$output .= '	<td height="24" width="60%" style="border: 1px solid black;"></td>';
	$output .= '</tr>';
	$output .= '</table>';
	
	$output .= '<p>4. Percentual do ciclo </p>'; 
	$output .= '<table width="100%" style="border: 1px solid black; border-collapse: collapse;">';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<th width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Passou [%]</th>';
	$output .= '	<th width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Não executado [%]</th>';
	$output .= '	<th width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Com falha [%]</th>';
	$output .= '	<th width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Bloqueado [%]</th>';
	$output .= '	<th width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Warning [%]</th>';
	$output .= '</tr>';
	$output .= '<tr height="24" style="border: 1px solid black;">';
	$output .= '	<td width="20%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '	<td width="20%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '	<td width="20%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '	<td width="20%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '	<td width="20%" style="border: 1px solid black;">&nbsp;</td>';
	$output .= '</tr>';
	$output .= '</table>';
	$output .= '</b>';	
	//tabela end
	$output .= '<br style="page-break-before: always;"><br/>';
	$output .= "<br/>"; */
	}
  return $output;
}


/*
  function: renderTestSpecTreeForPrinting
  args :
  returns:


env->base_href
env->item_type
env->tocPrefix
env->testCounter => env->tocCounter
env->user_id

context['tproject_id']
context['tplan_id']
context['platform_id']
context['build_id']
context['level']  >>>>> WRONG
context['prefix']

*/

function renderTestSpecTreeForPrinting(&$db,&$node,&$options,$env,$context,$tocPrefix,$indentLevel)
{
	
  static $tree_mgr;
  static $id_descr;
  static $tplan_mgr;
  $code = null;
  
 // $GLOBALS['impar'] = $GLOBALS['impar'] + 1;
    // $code .= '<p>bode ';
	// $code .= $GLOBALS['impar'];
	// $code .= ' diga</p>';
	
	if($GLOBALS['impar'] == 0){
		
		// $code .= '<b> <p>POLVILHO ' . $context['build_id'] . ' DOCE</p> </b>'; 
		// $code .= '<b> <p>POLVILHO ' . $env->item_type . ' DOCE</p> </b>'; 
		
		$code .= '<br style="page-break-before: always;"><br/>';
		//tabela de informações da solução
		$code .= '<b> <p>1. Informações da solução </p>'; 
		
		$code .= '<table width="100%" style="border: 1px solid black; border-collapse: collapse;">';
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Nome do solicitante</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Data da execução</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Modelo do terminal</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Nome da Aplicação - Automação</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Versão da Aplicação - Automação</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Nome da Aplicação - Cliente</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Versão da Aplicação - Cliente</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Nome da Aplicação - Server</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Versão da Aplicação - Server</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Versão da biblioteca</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">LoA Lvl1</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Data de expiração do LoA Lvl 1</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">LoA Lvl2</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Versão do Kernel</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Data de expiração do Kernel</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">PCI PTS</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Data de expiração do PCI</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">TQM</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Data de expiração do TQM</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '<tr height="24" height="24px" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Emitido por</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;';	
		//$code .= ;
		$code .= '</td>';
		$code .= '</tr>';
		
		$code .= '</table>';
		
		$code .= '<p>2. Realizado por </p>'; 
		$code .= '<table width="100%" style="border: 1px solid black; border-collapse: collapse;">';
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<th width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Título</th>';
		$code .= '	<th width="50%" style="border: 1px solid black; background-color: #E2ECD9;">Nome, Data</th>';
		$code .= '</tr>';
		$code .= '<tr height="54" style="border: 1px solid black;">';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
		$code .= '	<td width="50%" style="border: 1px solid black;">&nbsp;</td>';
		$code .= '</tr>';
		$code .= '</table>';
		
		$code .= '<p>3. Documentos de Referência </p>'; 
		$code .= '<table width="100%" style="border: 1px solid black; border-collapse: collapse;">';
		$code .= '<tr style="border: 1px solid black;">';
		$code .= '	<th rowspan="3" width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Referências</th>';
		$code .= '	<th height="24" width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Documento</th>';
		$code .= '	<th height="24" width="60%" style="border: 1px solid black; background-color: #E2ECD9;">Nome e versão</th>';
		$code .= '</tr>';
		$code .= '<tr style="border: 1px solid black;">';
		$code .= '	<td height="24" width="20%" style="border: 1px solid black;"></td>';
		$code .= '	<td height="24" width="60%" style="border: 1px solid black;"></td>';
		$code .= '</tr>';
		$code .= '<tr style="border: 1px solid black;">';
		$code .= '	<td height="24" width="20%" style="border: 1px solid black;"></td>';
		$code .= '	<td height="24" width="60%" style="border: 1px solid black;"></td>';
		$code .= '</tr>';
		$code .= '</table>';
		
		$code .= '<p>4. Percentual do ciclo </p>'; 
		$code .= '<table width="100%" style="border: 1px solid black; border-collapse: collapse;">';
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<th width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Passou [%]</th>';
		$code .= '	<th width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Não executado [%]</th>';
		$code .= '	<th width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Com falha [%]</th>';
		$code .= '	<th width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Bloqueado [%]</th>';
		$code .= '	<th width="20%" style="border: 1px solid black; background-color: #E2ECD9;">Warning [%]</th>';
		$code .= '</tr>';
		$code .= '<tr height="24" style="border: 1px solid black;">';
		$code .= '	<td width="20%" style="border: 1px solid black;">&nbsp;</td>';
		$code .= '	<td width="20%" style="border: 1px solid black;">&nbsp;</td>';
		$code .= '	<td width="20%" style="border: 1px solid black;">&nbsp;</td>';
		$code .= '	<td width="20%" style="border: 1px solid black;">&nbsp;</td>';
		$code .= '	<td width="20%" style="border: 1px solid black;">&nbsp;</td>';
		$code .= '</tr>';
		$code .= '</table>';
		$code .= '</b>';	
		//tabela end
		$code .= '<br style="page-break-before: always;"><br/>';
		$code .= "<br/>";
		
		$GLOBALS['impar'] = $GLOBALS['impar'] + 1;
	}
	
  if(!$tree_mgr)
  { 
    $tplan_mgr = new testplan($db);
    $tree_mgr = new tree($db);
    $id_descr = $tree_mgr->node_types;

    $k2i = array('tproject_id' => 0, 'tplan_id' => 0, 'platform_id' => 0,  'build_id' => 0, 'prefix' => null);
    $context = array_merge($k2i,$context);
  }

  $node_type = $id_descr[intval($node['node_type_id'])];
  switch($node_type)
  {
    case 'testproject':
    break;

    case 'testsuite':
      $tocPrefix .= (!is_null($tocPrefix) ? "." : '') . $env->tocCounter;
      $code .= renderTestSuiteNodeForPrinting($db,$node,$env,$options,$context,$tocPrefix,$indentLevel);
    break;

    case 'testcase':
      $code .= renderTestCaseForPrinting($db,$node,$options,$env,$context,$indentLevel);
    break;
  }
  
  if (isset($node['childNodes']) && $node['childNodes'])
  {
    // Need to be a LOCAL COUNTER for each PARENT
    $TOCCounter = 0;
    $childNodes = $node['childNodes'];
    $children_qty = sizeof($childNodes);
    for($idx = 0;$idx < $children_qty ;$idx++)
    {
      $current = $childNodes[$idx];
      if(is_null($current) || $current == REMOVEME)
      {
        continue;
      }

      if (isset($current['node_type_id']) && $id_descr[$current['node_type_id']] == 'testsuite')
      {
        // Each time I found a contained Test Suite need to add a .x.x. to TOC
        $TOCCounter++;
      }
      $env->tocCounter = $TOCCounter;
      $code .= renderTestSpecTreeForPrinting($db,$current,$options,$env,$context,$tocPrefix,$indentLevel+1);
    }
  }
  
  if ($node_type == 'testproject' && $options['toc'])
  {
    $code = str_replace("{{INSERT_TOC}}",$options['tocCode'],$code);
  }

  return $code;
}


/**
 * get user name from pool (save used names in session to improve performance)
 * 
 * @param integer $db DB connection identifier 
 * @param integer $userId
 * 
 * @return string readable user name
 * @author havlatm
 */
function gendocGetUserName(&$db, $userId)
{
	
  $authorName = null;
        
  if(isset($_SESSION['userNamePool'][$userId]))
  {
    $authorName  = $_SESSION['userNamePool'][$userId];
  }
  else
  {
    $user = tlUser::getByID($db,$userId);
    if ($user)
    {
      $authorName = $user->getDisplayName();
      $authorName = htmlspecialchars($authorName);
      $_SESSION['userNamePool'][$userId] = $authorName;
    }
    else
    {
      $authorName = lang_get('undefined');
      tLog('tlUser::getByID($db,$userId) failed', 'ERROR');
    }
  }
  
  return $authorName;  
}


/**
 * render Test Case content for generated documents
 * 
 * @param $integer db DB connection identifier 
 * @return string generated html code
 *
 * @internal revisions
 */
function renderTestCaseForPrinting(&$db,&$node,&$options,$env,$context,$indentLevel)
{
	echo //'<script src="//code.jquery.com/jquery-1.11.1.min.js" </script>
//'<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js" </script>
'<script type="text/javascript">
jQuery(document).ready(function(){

     jQuery("a#link_attach.bold").each(function() {
         if(jQuery(this).text().length > 30) {
           jQuery(this).text(jQuery(this).text().substr(0,30)+"...");
         }
     });
});	
</script>';/**/

  static $req_mgr;
  static $tc_mgr;
  static $build_mgr;
  static $tplan_mgr;
  static $tplan_urgency;
  static $labels;
  static $tcase_prefix;
  static $userMap = array();
  static $cfg;
  static $tables = null;
  static $force = null;
  static $bugInterfaceOn = false;
  static $its;
  static $buildCfields;  
  static $statusL10N;
  static $docRepo;

  static $st;

  $code = null;
  $tcInfo = null;
  $tcResultInfo = null;
  $tcase_pieces = null;

  $id = $node['id'];
  $level = $indentLevel;
  $prefix = isset($context['prefix']) ? $context['prefix'] : null;
  $tplan_id = isset($context['tplan_id']) ? $context['tplan_id'] : 0;
  $tprojectID = isset($context['tproject_id']) ? $context['tproject_id'] : 0;
  $platform_id = isset($context['platform_id']) ? $context['platform_id'] : 0;
  $build_id = isset($context['build_id']) ? $context['build_id'] : 0;

  // init static elements
  if (!$tables)
  {
    $st = new stdClass();

    $tables = tlDBObject::getDBTables(array('executions','builds','execution_tcsteps'));
    $tc_mgr = new testcase($db);
    $tplan_urgency = new testPlanUrgency($db);
    $build_mgr = new build_mgr($db);
    $tplan_mgr = new testplan($db);
    $req_mgr = new requirement_mgr($db);

    list($cfg,$labels) = initRenderTestCaseCfg($tc_mgr,$options);

    if(!is_null($prefix))
    {
      $tcase_prefix = $prefix;
    }
    else
    {
      list($tcase_prefix,$dummy) = $tc_mgr->getPrefix($id);
    }
    $tcase_prefix .= $cfg['testcase']->glue_character;

    $force['displayVersion'] = isset($options['displayVersion']) ? $options['displayVersion'] : false;
    $force['displayLastEdit'] = isset($options['displayLastEdit']) ? $options['displayLastEdit'] : false;
     
    $its = null;
    $tproject_mgr = new testproject($db);
    $info = $tproject_mgr->get_by_id($tprojectID);
    $bugInterfaceOn = $info['issue_tracker_enabled'];
    if($info['issue_tracker_enabled'])
    {
      $it_mgr = new tlIssueTracker($db);
      $its = $it_mgr->getInterfaceObject($tprojectID);
      unset($it_mgr);
    }  

    $statusL10N = null;         
    foreach($cfg['results']['code_status'] as $vc => $vstat)
    {
      if(isset($cfg['results']['status_label_for_exec_ui'][$vstat]))
      {
        $statusL10N[$vc] = lang_get($cfg['results']['status_label_for_exec_ui'][$vstat]);  
      }  
    }
    $docRepo = tlAttachmentRepository::create($db);

    $st->locationFilters = $tc_mgr->buildCFLocationMap();
  
    // change table style in case of single TC printing to not be indented
    $st->table_style = "";
    if (isset($options['docType']) && $options['docType'] == SINGLE_TESTCASE) 
    {
      $st->table_style = 'style="margin-left: 0;"';
    }

    $st->cfieldFormatting = array('label_css_style' => '',  'add_table' => false, 
                                  'value_css_style' => 
                                  ' colspan = "' . ($cfg['tableColspan']-3) . '" ' );

    $info = null;
  }


  /** 
   * @TODO THIS IS NOT THE WAY TO DO THIS IS ABSOLUTELY WRONG AND MUST BE REFACTORED, 
   * using existent methods - franciscom - 20090329 
   * Need to get CF with execution scope
   */
  $exec_info = null;
  $getByID['filters'] = null;


  $opt = array();
  $opt['step_exec_notes'] = isset($options['step_exec_notes']) && $options['step_exec_notes'];
  $opt['step_exec_status'] = isset($options['step_exec_status']) && $options['step_exec_status'];          

  switch($options["docType"])
  {
    case DOC_TEST_SPEC:
      $getByID['tcversion_id'] = testcase::LATEST_VERSION;
      $getExecutions = false;
    break;

    case SINGLE_TESTCASE:
      $getByID['tcversion_id'] = $node['tcversion_id'];
      $getExecutions = ($options['passfail'] || $options['notes'] ||
                        $opt['step_exec_notes'] || $opt['step_exec_status']);
    break;

	default:
      $getByID['tcversion_id'] = $node['tcversion_id'];
      $getExecutions = ($options['cfields'] || $options['passfail'] || $options['notes'] ||
                        $opt['step_exec_notes'] || $opt['step_exec_status']);
    break;
  }

  if ($getExecutions)
  {
    // Thanks to Evelyn from Cortado, have found a very old issue never reported.
    // 1. create TC-1A VERSION 1
    // 2. add to test plan and execute FAILED ON BUILD 1
    // 3. Request Test Report (Test Plan EXECUTION REPORT).
    //    You will get spec for VERSION 1 and result for VERSION 1 - OK cool!
    // 4. create VERSION 2
    // 5. update linked Test Case Versions
    // 6. do nothing more than repeat step 3
    //    without this fix you will get
    //    You will get spec for VERSION 2 and result for VERSION 1 - Hmmm
    //    and in addition is not clear that execution was on VERSION 1 . No GOOD!!
    //
    // HOW has been fixed ?
    // Getting info about THE CURRENT LINKED test case version and looking for
    // exec info for this.
    // 
    // ATTENTION: THIS IS OK ONLY WHEN BUILD ID is not provided
    //
    //
    // Get Linked test case version
    $linkedItem = $tplan_mgr->getLinkInfo($tplan_id,$id,$platform_id);

    $sql = " SELECT E.id AS execution_id, E.status, E.execution_ts, E.tester_id," .
           " E.notes, E.build_id, E.tcversion_id,E.tcversion_number,E.testplan_id," .
           " E.execution_type, E.execution_duration, " .
           " B.name AS build_name " .
           " FROM {$tables['executions']} E " .
           " JOIN {$tables['builds']} B ON B.id = E.build_id " .
           " WHERE 1 = 1 ";

    if(isset($context['exec_id']))
    {
      $sql .= " AND E.id=" . intval($context['exec_id']);
    }
    else
    {
      $sql .= " AND E.testplan_id = " . intval($tplan_id) .
              " AND E.platform_id = " . intval($platform_id) .
              " AND E.tcversion_id = " . intval($linkedItem[0]['tcversion_id']);
      if($build_id > 0)
      {
        $sql .= " AND E.build_id = " . intval($build_id);
      }
      else
      {
        // We are looking for LATEST EXECUTION of CURRENT LINKED test case version
        $sql .= " AND E.tcversion_number=" . intval($linkedItem[0]['version']);
      }
      $sql .= " ORDER BY execution_id DESC";
    }
    $exec_info = $db->get_recordset($sql,null,1);

    $getByID['tcversion_id'] = $linkedItem[0]['tcversion_id'];
    $getByID['filters'] = null;
    $linkedItem = null;

    if( !is_null($exec_info) )
    {
      $getByID['tcversion_id'] = null;
      $getByID['filters'] = array('version_number' => $exec_info[0]['tcversion_number']);
      if( isset($options['build_cfields']) && $options['build_cfields'] )
      {
        if( !isset($buildCfields[$exec_info[0]['build_id']]) )
        {
          $buildCfields[$exec_info[0]['build_id']] = 
            $build_mgr->html_table_of_custom_field_values($exec_info[0]['build_id'],$tprojectID);
        }
      }  
    }  
  }
 
 $tcInfo = $tc_mgr->get_by_id($id,$getByID['tcversion_id'],$getByID['filters'],
                               array('renderGhost' => true,'renderImageInline' => true));
  if ($tcInfo)
  {
    $tcInfo = $tcInfo[0];
  }
  $external_id = $tcase_prefix . $tcInfo['tc_external_id'];
  $name = htmlspecialchars($node['name']);

  $cfields = array('specScope' => null, 'execScope' => null);
  if ($options['cfields'])
  {
    // Get custom fields that has specification scope
    // Custom Field values at Test Case VERSION Level
    

    foreach($st->locationFilters as $fkey => $fvalue)
    { 
      $cfields['specScope'][$fkey] = 
          $tc_mgr->html_table_of_custom_field_values($id,'design',$fvalue,null,$tplan_id,
                                                     $tprojectID,
                                                     $st->cfieldFormatting,$tcInfo['id']);             
    }           

    if (!is_null($exec_info))
    {
      $cfields['execScope'] = $tc_mgr->html_table_of_custom_field_values(
                                       $tcInfo['id'],'execution',null,
                                       $exec_info[0]['execution_id'], $tplan_id,
                                       $tprojectID,$st->cfieldFormatting);
    }  
  }

  if ($options["docType"]== DOC_FAIL_ONLY_TEST_PLAN_EXECUTION_ON_BUILD){
	if($cfg['status_labels'][$exec_info[0]['status']]=='Failed' || $cfg['status_labels'][$exec_info[0]['status']]=='Com Falha' || $cfg['status_labels'][$exec_info[0]['status']]=='Warning' || $cfg['status_labels'][$exec_info[0]['status']]=='Não aplicavel' || $cfg['status_labels'][$exec_info[0]['status']]=='N/A' || $cfg['status_labels'][$exec_info[0]['status']]=='Aprovado com restrição' || $cfg['status_labels'][$exec_info[0]['status']]=='Approved with restrictions')
		goto FAIL_ONLY_TEST_CASE_RENDER;
	else
		goto FAIL_ONLY_TEST_CASE_SKIPER;
  }
  
  else if ($options["docType"]== DOC_PASS_ONLY_TEST_PLAN_EXECUTION_ON_BUILD){
	if($cfg['status_labels'][$exec_info[0]['status']]=='Passed' || $cfg['status_labels'][$exec_info[0]['status']]=='Passou' || $cfg['status_labels'][$exec_info[0]['status']]=='Warning' || $cfg['status_labels'][$exec_info[0]['status']]=='Não aplicavel' || $cfg['status_labels'][$exec_info[0]['status']]=='N/A' || $cfg['status_labels'][$exec_info[0]['status']]=='Aprovado com restrição' || $cfg['status_labels'][$exec_info[0]['status']]=='Approved with restrictions')
		goto PASS_ONLY_TEST_CASE_RENDER;
	else
		goto PASS_ONLY_TEST_CASE_SKIPER;
  }
  
FAIL_ONLY_TEST_CASE_RENDER:
PASS_ONLY_TEST_CASE_RENDER:
	
	if ($options['toc']){
    // EXTERNAL ID added
    $options['tocCode'] .= '<p style="padding-left: ' . 
                          (15 * $level).'px;"><a href="#' . prefixToHTMLID('tc'.$id) . '">' .
                          //htmlspecialchars($external_id) . ": ". $name . '</a></p>';  
                          $name . '</a></p>';  
    $code .= '<a name="' . prefixToHTMLID('tc'.$id) . '"></a>';
  }
    
   //Colore o cabeçalho da tabela de cada caso de teste de acordo com o status do caso.
   $table_style = '';
   $code .= '<div> <table class="tc" width="100%" ' . $table_style . '>';
	if($cfg['status_labels'][$exec_info[0]['status']]=='Passed' || $cfg['status_labels'][$exec_info[0]['status']]=='Passou')
	    $table_style = 'style="background-color: #E2ECD9; color: rgb(12, 120, 12);"';
	else if($cfg['status_labels'][$exec_info[0]['status']]=='Failed' || $cfg['status_labels'][$exec_info[0]['status']]=='Com Falha')
		$table_style = 'style="background-color: #FFE0E0; color: rgb(120, 12, 12);"';
	else if($cfg['status_labels'][$exec_info[0]['status']]=='Blocked' || $cfg['status_labels'][$exec_info[0]['status']]=='Bloqueado')
		$table_style = 'style="background-color: #DCE1F5; color: rgb(0,0,0);"';
	else if($cfg['status_labels'][$exec_info[0]['status']]=='Warning')
		$table_style = 'style="background-color: #FFFFCC; color: rgb(0,0,0);"';
	else if($statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='Não aplicavel' || $statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='N/A')
		$table_style = 'style="background-color: #FEDF91; color: rgb(0,0,0);"';
	else if($statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='Aprovado com restrição' || $statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='Approved with restrictions')
		$table_style = 'style="background-color: #FEDF91; color: rgb(0,0,0);"';
	else
		$table_style = 'style="color: rgb(0, 0, 0);"';
   $code .= '<tr><th ' . $table_style . ' colspan="' . $cfg['tableColspan'] . '">'. $name;
// add test case version 
  switch($env->reportType)
  {
    case DOC_TEST_PLAN_DESIGN:
      //Para imprimir versões no plano de testes
	  $version_number = isset($node['version']) ? $node['version'] : $tcInfo['version'];
    break;
    
    case DOC_TEST_PLAN_EXECUTION:
    case DOC_TEST_PLAN_EXECUTION_ON_BUILD:
	case DOC_FAIL_ONLY_TEST_PLAN_EXECUTION_ON_BUILD:
	case DOC_PASS_ONLY_TEST_PLAN_EXECUTION_ON_BUILD:
      $version_number = $tcInfo['version'];
    break;

    default:
      $version_number = $tcInfo['version'];
    break;
  }

  if($cfg['doc']->tc_version_enabled || $force['displayVersion'] )
  {
    $code .= '&nbsp;<span style="font-size: 80%;">' . $cfg['gui']->role_separator_open . 
             $labels['version'] . $cfg['gui']->title_separator_1 .  $version_number . 
             $cfg['gui']->role_separator_close . '</span>';
  }
  $code .= "</th></tr>\n";

  if ($options['body'] || $options['summary'])
  {
    $tcase_pieces = array('summary');
  }
    
  if ($options['body'])
  {
    if($cfg['status_labels'][$exec_info[0]['status']]=='Failed' || $cfg['status_labels'][$exec_info[0]['status']]=='Com Falha' || $cfg['status_labels'][$exec_info[0]['status']]=='Warning' || $cfg['status_labels'][$exec_info[0]['status']]=='Não aplicavel' || $cfg['status_labels'][$exec_info[0]['status']]=='N/A' || $cfg['status_labels'][$exec_info[0]['status']]=='Aprovado com restrição' || $cfg['status_labels'][$exec_info[0]['status']]=='Approved with restrictions')
		$tcase_pieces[] = 'preconditions';
	else if($options["docType"] == DOC_TEST_PLAN_EXECUTION_ON_BUILD || $options["docType"] == DOC_PASS_ONLY_TEST_PLAN_EXECUTION_ON_BUILD)
		$tcase_pieces[] = 'preconditions';
  }

  if( $options['body'] || $options['step_exec_notes'] || $options['step_exec_status'] )
  {
	if ($options["docType"]== DOC_FAIL_ONLY_TEST_PLAN_EXECUTION_ON_BUILD){
		if($cfg['status_labels'][$exec_info[0]['status']]=='Failed' || $cfg['status_labels'][$exec_info[0]['status']]=='Com Falha' || $cfg['status_labels'][$exec_info[0]['status']]=='Warning' || $cfg['status_labels'][$exec_info[0]['status']]=='Não aplicavel' || $cfg['status_labels'][$exec_info[0]['status']]=='N/A' || $cfg['status_labels'][$exec_info[0]['status']]=='Aprovado com restrição' || $cfg['status_labels'][$exec_info[0]['status']]=='Approved with restrictions')
			$tcase_pieces[] = 'steps';
	}
	if ($options["docType"]== DOC_PASS_ONLY_TEST_PLAN_EXECUTION_ON_BUILD)
		$tcase_pieces[] = 'steps';
	
	if ($options["docType"]== DOC_TEST_PLAN_DESIGN || $options["docType"]== SINGLE_TESTCASE || $options["docType"]== DOC_TEST_PLAN_EXECUTION_ON_BUILD)
		if($cfg['status_labels'][$exec_info[0]['status']]=='Failed' || $cfg['status_labels'][$exec_info[0]['status']]=='Com Falha' || $cfg['status_labels'][$exec_info[0]['status']]=='Warning' || $cfg['status_labels'][$exec_info[0]['status']]=='Não aplicavel' || $cfg['status_labels'][$exec_info[0]['status']]=='N/A' || $cfg['status_labels'][$exec_info[0]['status']]=='Aprovado com restrição' || $cfg['status_labels'][$exec_info[0]['status']]=='Approved with restrictions')
			$tcase_pieces[] = 'steps';
  } 
    
  if(!is_null($tcase_pieces))
  {

    // Check user rights in order to understand if can delete attachments here
    //   function hasRight(&$db,$roleQuestion,$tprojectID = null,$tplanID = null,$getAccess=false)
    // $tplan_id = isset($context['tplan_id']) ? $context['tplan_id'] : 0;
    // $tprojectID = isset($context['tproject_id']) ? $context['tproject_id'] : 0;
    $canManageAttachments = false;
    if(isset($context['user']) && !is_null($context['user']))
    {
      $canManageAttachments = $context['user']->hasRight($db,'testplan_execute',$tprojectID,$tplan_id);
    }  

    // Multiple Test Case Steps Feature
    foreach($tcase_pieces as $key)
    {
      if( $key == 'steps' )
      {
        if( isset($cfields['specScope']['before_steps_results']) )
        {
          $code .= $cfields['specScope']['before_steps_results'];    
        }
        if (!is_null($tcInfo[$key]) && $tcInfo[$key] != '')
        {
          $td_colspan = 2;
          $code .= '<tr>' .
                   '<td><b><span class="label">' . $labels['step_actions'] .':</span></b></td>' .
                   '<td><b><span class="label">' . $labels['expected_results'] .':</span></b></td>';

          $sxni = null;
          if($opt['step_exec_notes'] || $opt['step_exec_status'])
          {
            $sxni = $tc_mgr->getStepsExecInfo($exec_info[0]['execution_id']);

            if($opt['step_exec_notes'])
            {
              $td_colspan++;
              $code .= '<td><b><span class="label">' . $labels['step_exec_notes'] .':</span></b></td>';
            }       

            if($opt['step_exec_status'])
            {
              $td_colspan++;
              $code .= '<td><b><span class="label">' . $labels['step_exec_status'] .':</span></b></td>';
            }          
          }  

          $code .= '</tr>';     

          $loop2do = count($tcInfo[$key]);
          for($ydx=0 ; $ydx < $loop2do; $ydx++){
            //Colorização dos passos de acordo com o status
			$table_style = '';
			if($statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='Failed' || $statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='Com Falha')
				$table_style = 'style="background-color: #FFE0E0; color: rgb(120, 12, 12);"';
			else if($statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='Blocked' || $statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='Bloqueado')
				$table_style = 'style="background-color: #DCE1F5; color: rgb(0,0,0);"';
			else if($statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='Warning')
				$table_style = 'style="background-color: #FFFFCC; color: rgb(0,0,0);"';
			else if($statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='Não aplicavel' || $statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='N/A')
				$table_style = 'style="background-color: #B7B7B7; color: rgb(0,0,0);"';
			else if($statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='Aprovado com restrição' || $statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']]=='Approved with restrictions')
				$table_style = 'style="background-color: #FBE4D5; color: rgb(132,60,12);"';
			else
				$table_style = 'style="color: rgb(0, 0, 0);"';
			
			$code .= '<tr>' .
                     //'<td ' . $table_style . ' width="1">' .  $tcInfo[$key][$ydx]['step_number'] . '</td>' .
                     '<td ' . $table_style . '>' .  $tcInfo[$key][$ydx]['actions'] . '</td>' .
                     '<td ' . $table_style . '>' .  $tcInfo[$key][$ydx]['expected_results'] . '</td>';

            $nike = !is_null($sxni) && isset($sxni[$tcInfo[$key][$ydx]['id']]) && 
                    !is_null($sxni[$tcInfo[$key][$ydx]['id']]);
            if( $opt['step_exec_notes'] )
            {
              $code .= '<td ' . $table_style . '>';
              if( $nike )
              {
                $code .= $sxni[$tcInfo[$key][$ydx]['id']]['notes'];
              }  
              $code .= '</td>';
            }

            if( $opt['step_exec_status'] )
            {
              $code .= '<td ' . $table_style . '>';
              if( $nike )
              {
                $code .= $statusL10N[$sxni[$tcInfo[$key][$ydx]['id']]['status']];
              }  
              $code .= '</td>';
            }
            $code .= '</tr>';

            // Attachment management
            if($getExecutions)
            {
              if( isset($sxni[$tcInfo[$key][$ydx]['id']]))
              {
                $attachInfo = getAttachmentInfos($docRepo,$sxni[$tcInfo[$key][$ydx]['id']]['id'],
                                                 $tables['execution_tcsteps'],true,1);
				
                if( !is_null($attachInfo) )
                {
                  $code .= '<tr><td colspan="' . $td_colspan . '">';
                  $code .= '<b>' . $labels['exec_attachments'] . '</b><br>';
					
                  foreach($attachInfo as $fitem)
                  {
                    $code .= '<form method="POST" name="fda' . $fitem['id'] . '" ' .
                             ' id="fda' . $fitem['id'] . "' " .
                             ' action="' . $env->base_href . 'lib/execute/execPrint.php">';
                    
                    $code .= '<input type="hidden" name="id" value="' . intval($context['exec_id']) . '">';
                    $code .= '<input type="hidden" name="deleteAttachmentID" value="' . intval($fitem['id']) . '">';
      
                    if($fitem['is_image'])
                    {
                      $code .= "<li>" . htmlspecialchars($fitem['file_name']) . "</li>";
                      $code .= '<li>' . '<img src="' . $env->base_href . 
                               'lib/attachments/attachmentdownload.php?skipCheck=1&id=' . $fitem['id'] . '">';
                    }  
                    else
                    {
                      $code .= '<li>' . '<a href="' . $env->base_href . 
                               'lib/attachments/attachmentdownload.php?skipCheck=1&id=' . $fitem['id'] . 
                               '" ' . ' target="#blank" > ' . htmlspecialchars($fitem['file_name']) . '</a>';
                    }  
                    $code .= '<input type="image" alt="' . $labels['alt_delete_attachment'] . '"' .
                             'src="' . $env->base_href . TL_THEME_IMG_DIR . 'trash.png"></li></form>';
                  }  
                  $code .= '</td></tr>';
                }  
              }  
            }  // $getExecutions

          }
        }
      }
      else
      {
        // disable the field if it's empty
        if ($tcInfo[$key] != '')
        {
          $code .= '<tr><td colspan="' .  $cfg['tableColspan'] . '"><span class="label"><b>' . $labels[$key] .
                   ':</b></span><br />' .  $tcInfo[$key] . "</td></tr>";
        }
      }         
    }
  }

/* Tipo da execução (Não utilizado nos nossos relatórios)
  $code .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top">' . 
           '<span class="label">'.$labels['execution_type'].':</span></td>' .
           '<td colspan="' .  ($cfg['tableColspan']-1) . '">';


  // This is what have been choosen DURING DESIGN, but may be we can choose at DESIGN
  // manual and the execute AUTO, or may be choose AUTO and execute MANUAL.
  // After report on MANTIS, seems that we need to provide in output two values:
  // DESIGN execution type
  // EXECUTION execution type         
  switch ($tcInfo['execution_type'])
  {
    case TESTCASE_EXECUTION_TYPE_AUTO:
      $code .= $labels['execution_type_auto'];          
    break;

    case TESTCASE_EXECUTION_TYPE_MANUAL:
    default:
      $code .= $labels['execution_type_manual'];          
    break;
  }
  $code .= "</td></tr>\n";
 */
  // 
/* 
  $code .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top">' . 
             '<span class="label">'. $labels['estimated_execution_duration'].':</span></td>' .
             '<td colspan="' .  ($cfg['tableColspan']-1) . '">' .  $tcInfo['estimated_exec_duration'];
  $code .= "</td></tr>\n"; */

/*   if( isset($options['importance']) && $options['importance'] )
  {
    $code .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top">' . 
             '<span class="label">'.$labels['importance'].':</span></td>' .
             '<td colspan="' .  ($cfg['tableColspan']-1) . '">' .
             $cfg['importance'][$tcInfo['importance']];
    $code .= "</td></tr>\n";
  } */


  // print priority when printing test plan
  if (isset($options['priority']) && $options['priority'])
  {
    // Get priority of this tc version for this test plan by using testplanUrgency class.
    // Is there maybe a better method than this one?
    $filters = array('tcversion_id' => $tcInfo['id']);
    $opt = array('details' => 'tcversion');
    $prio_info = $tplan_urgency->getPriority($tplan_id, $filters, $opt);
    $prio = $prio_info[$tcInfo['id']]['priority_level'];
	
    $code .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top">' .
             '<span class="label">'.$labels['priority'].':</span></td>' .
             '<td colspan="' .  ($cfg['tableColspan']-1) . '">' . $cfg['priority'][$prio];
    $code .= "</td></tr>\n";
  }

  // Spacer
  $code .= '<tr><td colspan="' .  $cfg['tableColspan'] . '">' . "</td></tr>";
  if($cfg['status_labels'][$exec_info[0]['status']]=='Failed' || $cfg['status_labels'][$exec_info[0]['status']]=='Com Falha' || $cfg['status_labels'][$exec_info[0]['status']]=='Warning' || $cfg['status_labels'][$exec_info[0]['status']]=='Não aplicavel' || $cfg['status_labels'][$exec_info[0]['status']]=='N/A' || $cfg['status_labels'][$exec_info[0]['status']]=='Aprovado com restrição' || $cfg['status_labels'][$exec_info[0]['status']]=='Approved with restrictions')
	$code .= '' . $cfields['specScope']['standard_location'] . $cfields['execScope'] . ' ';
  
  
  // 
  $cfields = null;
  $prio_info = null;


  // $code = null;
  // 20140813
  $relSet = $tc_mgr->getRelations($id);
  if(!is_null($relSet['relations']))
  {
    // $fx = str_repeat('&nbsp;',5); // MAGIC allowed    
    $code .= '<tr><td width="' . $cfg['firstColWidth'] . 
             '" valign="top"><span class="label">' . $labels['relations'] . '</span></td>'; 

    $code .= '<td>';
    for($rdx=0; $rdx < $relSet['num_relations']; $rdx++)
    {
      if($relSet['relations'][$rdx]['source_id'] == $id)
      {
        $ak = 'source_localized';
      }
      else
      {
        $ak = 'destination_localized';
      }
      $code .= htmlspecialchars($relSet['relations'][$rdx][$ak]) . ' - ' .
               htmlspecialchars($relSet['relations'][$rdx]['related_tcase']['fullExternalID']) . ':' .
               htmlspecialchars($relSet['relations'][$rdx]['related_tcase']['name']) .  '<br>';
    } 
    $code .= '</td></tr>';
  }  
  $relSet = null;


  // collect REQ for TC
  if ($options['requirement'])
  {
    $requirements = $req_mgr->get_all_for_tcase($id);
    $code .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top"><span class="label">'. 
             $labels['reqs'].'</span>'; 
    $code .= '<td colspan="' . ($cfg['tableColspan']-1) . '">';

    if (sizeof($requirements))
    {
      foreach ($requirements as $req)
      {
        $code .=  htmlspecialchars($req['req_doc_id'] . ":  " . $req['title']) . "<br />";
      }
    }
    else
    {
      $code .= '&nbsp;' . $labels['none'] . '<br />';
    }
    $code .= "</td></tr>\n";
  }
  $requirements = null;

  // collect keywords for TC
/*   if ($options['keyword'])
  {
    $code .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top"><span class="label">'. 
             $labels['keywords'].':</span>';
    $code .= '<td colspan="' . ($cfg['tableColspan']-1) . '">';
    $kwSet = $tc_mgr->getKeywords($id,null,array('fields' => 'keyword_id,keywords.keyword'));
    if (sizeof($kwSet))
    {
      foreach ($kwSet as $kw)
      {
        $code .= htmlspecialchars($kw['keyword']) . "<br />";
      }
    }
    else
    {
      $code .= '&nbsp;' . $labels['none'] . '<br>';
    }
    $code .= "</td></tr>\n";
  }
  $kwSet = null; */

  // Attachments
  $attachSet =  (array)$tc_mgr->getAttachmentInfos($id);
  if (count($attachSet) > 0)
  {
    $code .= '<tr><td> <span class="label">' . $labels['attached_files'] . '</span></td>';
    $code .= '<td colspan="' . ($cfg['tableColspan']-2) . '"><ul>';
	
    foreach($attachSet as $item)
    {
      $fname = "";
      if ($item['title'])
      {
        $fname .=  htmlspecialchars($item['title']) . " : ";
      }
      $fname .= htmlspecialchars($item['file_name']);
      $code .= "<li>$fname</li>";

      //if($item['is_image']) // && $options['outputFormat'] == FORMAT_HTML)
      //{
      //  $code .= '<li>' . '<img src="' . $env->base_href . 
      //           'lib/attachments/attachmentdownload.php?skipCheck=1&id=' . $item['id'] . '"> </li>';
      //}  
      //else
      //{
        $code .= '<li>' . '<a href="' . $env->base_href . 
                 'lib/attachments/attachmentdownload.php?skipCheck=1&id=' . $item['id'] . 
                 '" ' . ' target="#blank" > ' . htmlspecialchars($item['file_name']) . '</a></li>';
      //}  
    }
    $code .="</ul></td></tr>";
  }
  $attachSet = null;


  // generate test results data for test report 
  if ($options['passfail'])
  {  
    // $tsp = ($cfg['tableColspan']-1);
    // $code .= '<tr height="27px"style="' . "font-weight: bold;background: #EEE;text-align: left;" . '">' .
             // '<td width="' . $cfg['firstColWidth'] . '" valign="top">' . $labels['execution_details'] .'</td>' . 
             // '<td colspan="' . $tsp . '">' . "&nbsp;" . "</b></td></tr>\n";

    /* if( $bn != '' )
    {
      $code .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top">' . $labels['build'] .'</td>' . 
               '<td '  . $tsp . '>' . $bn . "</b></td></tr>\n";
    } */  

    /* if( isset($node['assigned_to']) )
    {
      $crew = explode(',',$node['assigned_to']);
      $code .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top">' . 
               $labels['assigned_to'] . '</td>' .
               '<td colspan="' .   $tsp . '">';

      $xdx = 0;
      foreach($crew as $mm)
      { 
        if ($xdx != 0)
        {
          $code .= ',';
        }  
        $xdx = -1;
        echo $mm .'<br>';
        $code .= gendocGetUserName($db, $mm);
      }          
      $code .= "</td></tr>\n";
    }  */
	
    if ($exec_info) 
    {
      $settings['cfg'] = $cfg;
      $settings['lbl'] = $labels;
      $settings['opt'] = array('show_notes' => $options['notes']);
      $settings['colspan'] = $cfg['tableColspan']-1;
	  //execution details
      $code .= buildTestExecResults($db,$its,$exec_info,$settings,$buildCfields);

      // Get Execution Attachments
      $execAttachInfo = getAttachmentInfos($docRepo,$exec_info[0]['execution_id'],$tables['executions'],true,1);
		
      if( !is_null($execAttachInfo) )
      {
		
        $code .= '<td colspan="' . $cfg['tableColspan'] . '">';
        $code .= '<b>' . $labels['exec_attachments'] . '</b>';
		$code .= '<table style="width:100%;">';
		$titles = array();
		$max_Titles = array();
		$last = ".";
		$max = 0;
		$min = 1;	
		$log = 0;
		$rec = 0;
		$oth = 0;
		$car = 0;
		$col = 0;		
		$max_String= ".";
		$cur_String;
		$code .= '<tr>';
		foreach($execAttachInfo as $cont){
			if($cont['title']){
				$cur_String = $cont['title'];
				if (!in_array($cur_String , $max_Titles)){
					if ($cur_String == "Log"){
						$log = 1;
					}else if ($cur_String == "Receipt"){
						$rec = 1;
					}else if ($cur_String == "Cardspy"){
						$car = 1;
					}else if ($cur_String == "Others"){
						$oth = 1;
					}				
					if($cur_String != $max_String){
						$min = 1;
						$max_String = $cur_String;
						$max_String[$cur_String] = $cur_String;
					}
					else{
						$min += 1;
					}					
				}
				if($min > $max){
					$max = $min;
				}
				
			}
		}
        foreach($execAttachInfo as $array){
			if($array['title']){
				$my_link = $array['title'];				
			} else{
				$my_link = "";
			}
			if (!(in_array("Log", $titles)) && ($log == 0)){
				$titles["Log"] = "Log";
				$rowcur = 0;
				$code .= '			<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Log</th>				
									</tr>';
				while ($rowcur < $max){
					$rowcur += 1;
					$code .= '
										<tr>
											<td style="vertical-align:middle;"><form> - </form></td>
										</tr> ';
										
				}
				$code .= '				</table>
							</td>';
			}
			if ((in_array("Log", $titles)) && !(in_array("Receipt", $titles)) &&  ($rec == 0)){
				$titles["Receipt"] = "Receipt";
				$rowcur = 0;
				$code .= '			<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Receipt</th>				
									</tr>';
				while ($rowcur < $max){
					$rowcur += 1;
					$code .= '
										<tr>
											<td style="vertical-align:middle;"><form> - </form></td>
										</tr> ';
										
				}
				$code .= '				</table>
							</td>';
			}
			if ((in_array("Log", $titles)) && (in_array("Receipt", $titles)) && !(in_array("Cardspy", $titles)) &&  ($car == 0)){
				$titles["Cardspy"] = "Cardspy";
				$rowcur = 0;
				$code .= '			<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Cardspy</th>				
									</tr>';
				while ($rowcur < $max){
					$rowcur += 1;
					$code .= '
										<tr>
											<td style="vertical-align:middle;"><form> - </form></td>
										</tr> ';
										
				}
				$code .= '				</table>
							</td>';
			}			
			if ((in_array("Log", $titles)) && (in_array("Receipt", $titles)) && (in_array("Cardspy", $titles)) && !(in_array("Others", $titles)) &&  ($oth == 0)){
				$titles["Others"] = "Others";
				$rowcur = 0;
				$code .= '			<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Others</th>				
									</tr>';
				while ($rowcur < $max){
					$rowcur += 1;
					$code .= '
										<tr>
											<td style="vertical-align:middle;"><form> - </form></td>
										</tr> ';
										
				}
				$code .= '				</table>
							</td>';
			}
			if (!in_array($my_link , $titles)){
				$rowcur = 0;
				if ($last != $my_link){
					$last = $my_link;
					$titles[$my_link] = $my_link;
					$code .= '<td>					
										<table width="100%" style="table-layout:fixed; height:100%" id="table_"'. $array['title'] .'"">
											<tr>
												<th>'.$last.'</th>				
											</tr>';
											
					foreach($execAttachInfo as $item){						
						if ($last == $item['title']){
							$rowcur += 1; 
							$code .= '<tr>	
										<td style="vertical-align:middle;">
											<form method="POST" action="'. $env->base_href .'lib/attachments/attachmentdownload.php" enctype="multipart/form-data" target="_blank" id="'. $item['id'] .'">
												
													<input type="hidden" value="'. $item['id'] .'" name="id"/>
													<input type="hidden" value="1" name="skip"/>
													<input type="hidden" value="" name="key"/>
													<a id="link_attach" href="javascript:document.getElementById('. $item['id'] .').submit();" class="bold" target="_blank" data-toggle="tooltip" title="'. $item['file_name'] .'"> '. $item['file_name'] .' </a> 
												
											</form>
											</td>
										</tr>';	
						}
					}
					 while ($rowcur < $max){
						 $code .= '<tr>
									 <td style="vertical-align:middle; color:white"><form>null</form></td>
								   </tr>';
						 $rowcur += 1;		  
					 }			  

					$code .= '		</table>
								</td>';
					
				}
			}
		}
		if (!(in_array("Log", $titles)) && ($log == 0)){
				$titles["Log"] = "Log";
				$rowcur = 0;
				$code .= '			<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Log</th>				
									</tr>';
				while ($rowcur < $max){
					$rowcur += 1;
					$code .= '
										<tr>
											<td style="vertical-align:middle;"><form> - </form></td>
										</tr> ';
										
				}
				$code .= '				</table>
							</td>';
			}
			if ((in_array("Log", $titles)) && !(in_array("Receipt", $titles)) &&  ($rec == 0)){
				$titles["Receipt"] = "Receipt";
				$rowcur = 0;
				$code .= '			<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Receipt</th>				
									</tr>';
				while ($rowcur < $max){
					$rowcur += 1;
					$code .= '
										<tr>
											<td style="vertical-align:middle;"><form> - </form></td>
										</tr> ';
										
				}
				$code .= '				</table>
							</td>';
			}
			if ((in_array("Log", $titles)) && (in_array("Receipt", $titles)) && !(in_array("Cardspy", $titles)) &&  ($car == 0)){
				$titles["Cardspy"] = "Cardspy";
				$rowcur = 0;
				$code .= '			<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Cardspy</th>				
									</tr>';
				while ($rowcur < $max){
					$rowcur += 1;
					$code .= '
										<tr>
											<td style="vertical-align:middle;"><form> - </form></td>
										</tr> ';
										
				}
				$code .= '				</table>
							</td>';
			}			
			if ((in_array("Log", $titles)) && (in_array("Receipt", $titles)) && (in_array("Cardspy", $titles)) && !(in_array("Others", $titles)) &&  ($oth == 0)){
				$titles["Others"] = "Others";
				$rowcur = 0;
				$code .= '			<td width="25%">
								<table style="width:100%; font-size: 13px;">
									<tr>
										<th>Others</th>				
									</tr>';
				while ($rowcur < $max){
					$rowcur += 1;
					$code .= '
										<tr>
											<td style="vertical-align:middle;"><form> - </form></td>
										</tr> ';
										
				}
				$code .= '				</table>
							</td>';
			}
          //if($fitem['is_image']) // && $options['outputFormat'] == FORMAT_HTML)
          //{
          //  $code .= "<li>" . htmlspecialchars($fitem['file_name']) . "</li>";
          //  $code .= '<li>' . '<img src="' . $env->base_href . 
          //                    'lib/attachments/attachmentdownload.php?skipCheck=1&id=' . $fitem['id'] . '"> </li>';
          //}  
          //else
          //{
            // $code .= '<li>' . '<a href="' . $env->base_href . 
                              // 'lib/attachments/attachmentdownload.php?skipCheck=1&id=' . $fitem['id'] . 
                              // '" ' . ' target="#blank" > ' . htmlspecialchars($fitem['file_name']) . '</a></li>';
          //}  
        // }  
        $code .= '</tr>';
		$code .='</table></td>';
	 // $code .='';
      }
    }
    else
    {
      $code .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top">' . 
               '<span class="label">' . $labels['report_exec_result'] . '</span></td>' . 
               '<td colspan="' . ($cfg['tableColspan']-1) . '"><b>' . $labels["test_status_not_run"] . 
               "</b></td></tr>\n";
    }
    $execAttachInfo = null;
    $exec_info = null;
  }
  
  $code .= "</table>\n</div>\n";
  $code .= '<br style="page-break-before: always;"><br/>';

FAIL_ONLY_TEST_CASE_SKIPER:
PASS_ONLY_TEST_CASE_SKIPER:
  return $code;
}


/**
 * 
 * 
 * 
 */
function renderTOC(&$options)
{
	
  $code = '';
  $options['toc_numbers'][1] = 0;
  if ($options['toc'])
  {
    $options['tocCode'] = '<h1 class="general" style="page-break-before: always">' . 
                          lang_get('title_toc').'</h1><div class="toc">';
    $code .= "{{INSERT_TOC}}";
  }

  return $code;
}


/*
  function: renderTestSuiteNodeForPrinting
  args :
  returns:

  ATTENTION: This variables: $tocPrefix,$indentLevel

             can not be passed on a data type that pass by reference
             because need to have LOCAL life during recursion.
             Having added it as members of $env and $context has generated a BUG
*/
function renderTestSuiteNodeForPrinting(&$db,&$node,$env,&$options,$context,$tocPrefix,$indentLevel)
{
	
  static $tsuite_mgr;
  static $l10n;
  static $title_separator;
  static $cfieldFormatting;
  static $getOpt;

  if(is_null($l10n))
  {
    $tsuite_mgr = new testsuite($db);
    
    $l10n = array('test_suite' => 'test_suite', 'details' => 'details', 
                  'attached_files' => 'attached_files');
                    
    $l10n = init_labels($l10n);

    $title_separator = config_get('gui_title_separator_1');
    $cfieldFormatting = array('table_css_style' => 'class="cf"');

    $getOpt['getByID'] = array('fields' => ' TS.id,TS.details ',
                               'renderImageInline' => true);
    
  }  

  $code = null;
  $name = isset($node['name']) ? htmlspecialchars($node['name']) : '';
  $cfields = array('design' => '');
    
  $docHeadingNumbering = $options['headerNumbering'] ? ($tocPrefix . ".") : '';
    
  if ($options['toc'])
  {
    $spacing = ($indentLevel == 2 && $tocPrefix != 1) ? "<br>" : "";
    $options['tocCode'] .= $spacing.'<b><p style="padding-left: '.(10 * $indentLevel).'px;">' .
                           '<a href="#' . prefixToHTMLID($tocPrefix) . '">' .
                           $name .  "</a></p></b>\n";
    $code .= "<a name='". prefixToHTMLID($context['prefix']) . "'></a>\n";
  
  }

  // we would like to have html top heading H1 - H6
  $docHeadingLevel = ($indentLevel-1); 

  // Remember that only H1 to H6 exists
  $docHeadingLevel = ($docHeadingLevel > 6) ? 6 : $docHeadingLevel;
  $docHeadingLevel = ($docHeadingLevel < 1) ? 1 : $docHeadingLevel;
  
  //$code .= "<h{$docHeadingLevel} class='doclevel'>" . $name . "</h{$docHeadingLevel}>\n";


  // ----- get Test Suite text -----------------
  /* 
  if ($options['header'])
  {
    $tInfo = $tsuite_mgr->get_by_id($node['id'],$getOpt['getByID']);
    if ($tInfo['details'] != '')
    {
      $code .= '<div>' . $tInfo['details'] . '</div>';
    }
    $tInfo = null;

    $attachSet =  (array)$tsuite_mgr->getAttachmentInfos($node['id']);
    if (count($attachSet) > 0)
    {
      $code .= '<table><caption style="text-align:left;">' . $l10n['attached_files'] . '</caption>';
      $code .= '<tr><td>&nbsp</td>';
      $code .= '<td><ul>';
      foreach($attachSet as $item)
      {
        $fname = "";
        if ($item['title'])
        {
          $fname .=  htmlspecialchars($item['title']) . " : ";
        }
        $fname .= htmlspecialchars($item['file_name']);
        $code .= "<li>$fname</li>";

        if($item['is_image']) 
        {
          $code .= '<li>' . '<img src="' . $env->base_href . 
                   'lib/attachments/attachmentdownload.php?skipCheck=1&id=' . $item['id'] . '"> </li>';
        }  
        else
        {
          $code .= '<li>' . '<a href="' . $env->base_href . 
                   'lib/attachments/attachmentdownload.php?skipCheck=1&id=' . $item['id'] . 
                   '" ' . ' target="#blank" > ' . htmlspecialchars($item['file_name']) . '</a></li>';
        }  
      }
      $code .="</ul></td></tr>";
      $code .= "</table>";
    }
    $attachSet = null;
	
    // get Custom fields    
    // Attention: for test suites custom fields can not be edited during execution,
    //            then we need to get just custom fields with scope  'design'
    foreach($cfields as $key => $value)
    {
      $cfields[$key] = $tsuite_mgr->html_table_of_custom_field_values($node['id'],$key,null,
                                                                      $context['tproject_id'],$cfieldFormatting);
      if($cfields[$key] != "")
      {
        $add_br = true;
        $code .= '<p>' . $cfields[$key] . '</p>';    
      }
    }
    $cfields = null;
  }
   */
  return $code;
}




/*
  function: renderTestPlanForPrinting
  args:
  returns:
  
  @internal revisions:
*/
function renderTestPlanForPrinting(&$db,&$node,&$options,$env,$context)

{

  $tProjectMgr = new testproject($db);
  $context['prefix'] = $tProjectMgr->getTestCasePrefix($context['tproject_id']);
  $code =  renderTestSpecTreeForPrinting($db,$node,$options,$env,$context,$env->tocPrefix,$context['level']);
  return $code;
}


/** 
 * Render HTML for estimated and real execute duration based on contribution
 * 
 * @param array_of_strings $statistics
 * @return string HTML code
 */
function renderTestDuration($statistics,$platform_id=0)
{
	
  $output = '';
  $hasOutput = false;
  $estimatedTimeAvailable = isset($statistics['estimated_execution']) && !is_null($statistics['estimated_execution']);
  $realTimeAvailable = isset($statistics['real_execution']) && 
                      !is_null($statistics['real_execution']['platform'][$platform_id]);
  

  if( $estimatedTimeAvailable || $realTimeAvailable)
  { 
    if($estimatedTimeAvailable) 
    {
      $estimated_minutes = $statistics['estimated_execution']['platform'][$platform_id]['minutes'];
      $tcase_qty = $statistics['estimated_execution']['platform'][$platform_id]['tcase_qty'];
      if($estimated_minutes > 0)
      {  
        if($estimated_minutes > 60)
        {
          $estimated_string = lang_get('estimated_time_hours') . round($estimated_minutes/60,2) ;
        }
        else
        {
          $estimated_string = lang_get('estimated_time_min') . $estimated_minutes;
        }
        $estimated_string = sprintf($estimated_string,$tcase_qty);
        $output .= '<p>' . $estimated_string . "</p>\n";
      }  
    }
      
    if($realTimeAvailable) 
    {
      $real_minutes = $statistics['real_execution']['platform'][$platform_id]['minutes'];
      $tcase_qty = $statistics['real_execution']['platform'][$platform_id]['tcase_qty'];   
      if( $real_minutes > 0 )
      {
        if($real_minutes > 60)
        {
          $real_string = lang_get('real_time_hours') . round($real_minutes/60,2) ;
        }
        else
        {
          $real_string = lang_get('real_time_min') . $real_minutes;
        } 
        $real_string = sprintf($real_string,$tcase_qty);    
        $output .= '<p>' . $real_string . "</p>\n";
      }
    }
  }

  if($output != '')
  {
    $output = "<div>\n" . $output . "</div>\n";
  }  

  return $output;  
}


/** 
 * get final markup for HTML
 * 
 * @return string HTML 
 **/
function renderEOF()
{

  return "</div>\n</body>\n</html>";
}


/**
 * compose html text for metrics (meantime estimated time only)
 * 
 * @return string html
 */
function buildTestPlanMetrics($statistics,$platform_id = 0)
{
	
  static $lbl;
  if(!$lbl)
  {
    $lbl = lang_get('execution_time_metrics');
  }  

  $output ='';
  $dummy = renderTestDuration($statistics,$platform_id);
  if($dummy != '')
  {      
    $output = '<h1 class="doclevel">' . $lbl . "</h1>\n" . $dummy;
  }
  return $output;  
}


/**
 * utility function to allow easy reading of code
 * on renderTestCaseForPrinting()
 * 
 * @return map with configuration and labels
 *
 * @internal revisions:
 * 20121017 - asimon - TICKET 5288 - print priority when printing test plan
 */
function initRenderTestCaseCfg(&$tcaseMgr,$options)
{
	
  $config = null;
  $config['firstColWidth'] = '20%';
  $config['doc'] = config_get('document_generator');
  $config['gui'] = config_get('gui');
  $config['testcase'] = config_get('testcase_cfg');
  $config['results'] = config_get('results');

  // Cortado
  $config['tableColspan'] = 2;
  if( (isset($options['step_exec_notes']) &&  $options['step_exec_notes']) )
  {
    $config['tableColspan']++;
  } 
  if( (isset($options['step_exec_status']) &&  $options['step_exec_status']) )
  {
    $config['tableColspan']++;
  } 
 
    
    foreach($config['results']['code_status'] as $key => $value)
    {
      $config['status_labels'][$key] = 
          "check your \$tlCfg->results['status_label'] configuration ";
      if( isset($config['results']['status_label'][$value]) )
      {
        $config['status_labels'][$key] = lang_get($config['results']['status_label'][$value]);
      }    
    }

    $labelsKeys=array('last_exec_result', 'report_exec_result','execution_details','execution_mode',
                      'title_execution_notes', 'none', 'reqs','author', 'summary',
                      'steps', 'expected_results','build', 'test_case', 'keywords','version', 
                      'test_status_not_run', 'not_aplicable', 'bugs','tester','preconditions',
                      'step_number', 'step_actions', 'last_edit', 'created_on', 'execution_type',
                      'execution_type_manual','execution_type_auto','importance','relations',
                      'estimated_execution_duration','step_exec_notes','step_exec_status',
                      'exec_attachments','alt_delete_attachment','assigned_to',
                      'high_importance','medium_importance','low_importance','execution_duration',
                      'priority', 'high_priority','medium_priority','low_priority','attached_files');
                      
    $labelsQty=count($labelsKeys);         
    for($idx=0; $idx < $labelsQty; $idx++)
    {
        $labels[$labelsKeys[$idx]] = lang_get($labelsKeys[$idx]);
    }
    
    $config['importance'] = array(HIGH => $labels['high_importance'],
                                  MEDIUM => $labels['medium_importance'],
                                  LOW => $labels['low_importance']);

    $config['priority'] = array(HIGH => $labels['high_priority'],
                                MEDIUM => $labels['medium_priority'],
                                LOW => $labels['low_priority']);

    return array($config,$labels);
}


/**
 * 
 * @internal revisions
 * @since 1.9.12
 *
 *
 */
function buildTestExecResults(&$dbHandler,&$its,$exec_info,$opt,$buildCF=null)
{
	
  static $testerNameCache;
  $out='';
  $my['opt'] = array('show_notes' => true);
  $my['opt'] = array_merge($my['opt'],(array)$opt);
  
  $cfg = &$opt['cfg'];
  $labels = &$opt['lbl'];
  $testStatus = $cfg['status_labels'][$exec_info[0]['status']];
  
  if(!isset($testerNameCache[$exec_info[0]['tester_id']]))
  {
    $testerNameCache[$exec_info[0]['tester_id']] = 
       gendocGetUserName($dbHandler, $exec_info[0]['tester_id']);
  }
  
  $executionNotes = $my['opt']['show_notes'] ? $exec_info[0]['notes'] : '';
  
  /* switch($exec_info[0]['execution_type'])
  {
    case TESTCASE_EXECUTION_TYPE_AUTO:
      $etk = 'execution_type_auto';          
    break;

    case TESTCASE_EXECUTION_TYPE_MANUAL:
    default:
      $etk = 'execution_type_manual';          
    break;
  } */

  $td_colspan = '';
  if( !is_null($opt['colspan']) ) 
  {
    $td_colspan .= ' colspan="' . $opt['colspan'] . '" '; 
  }

  //
  //$out .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top">' . $labels['build'] .'</td>' . 
  //        '<td '  .$td_colspan . '>' . htmlspecialchars($exec_info[0]['build_name']) . "</b></td></tr>\n";

  // Check if CF exits for this BUILD
/*   if(!is_null($buildCF) && isset($buildCF[$exec_info[0]['build_id']]) && 
     $buildCF[$exec_info[0]['build_id']] != '')
  {
     $out .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top"></td>' . 
             '<td '  .$td_colspan . '>' . $buildCF[$exec_info[0]['build_id']] . "</td></tr>\n";
  }  */       
/*   $out .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top">' . $labels['tester'] .'</td>' . 
          '<td '  .$td_colspan . '>' . $testerNameCache[$exec_info[0]['tester_id']] . "</b></td></tr>\n"; */

  $out .= '<tr><td width="20%" valign="top">' .
          '<span class="label"><b>' . $labels['report_exec_result'] . ':</b></span></td>' .
          '<td '  .$td_colspan . '><b>' . $testStatus . "</b></td></tr>\n" .

/*           '<tr><td width="20%">' .
          '<span class="label">' . $labels['execution_mode'] . ':</span></td>' .
          '<td '  .$td_colspan . '><b>' . $labels[$etk] . "</b></td></tr>\n" . */

/*           '<tr><td width="20%">' .
          '<span class="label">' . $labels['execution_duration'] . ':</span></td>'; */

/*   $out .= '<td '  .$td_colspan . '><b>' . 
          (isset($exec_info[0]['execution_duration']) ? $exec_info[0]['execution_duration'] : "&nbsp;") . 
          "</b></td></tr>\n"; */
	$out .= '</tr>';

  if ($executionNotes != '') // show execution notes is not empty
  {
    $out .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top"><b>'.$labels['title_execution_notes'] . '</b></td>' .
            '<td '  .$td_colspan . '>' . nl2br($executionNotes)  . "</td></tr>\n"; 
  }

  if( !is_null($its) ) 
  {
    $bugs = get_bugs_for_exec($dbHandler,$its,$exec_info[0]['execution_id']);
    if ($bugs) 
    {
      $bugString = '';
      foreach($bugs as $bugID => $bugInfo) 
      {
        $bugString .= $bugInfo['link_to_bts']."<br />";
      }
      $out .= '<tr><td width="' . $cfg['firstColWidth'] . '" valign="top">' . 
              $labels['bugs'] . '</td><td ' . $td_colspan . '>' . $bugString ."</td></tr>\n"; 
          
    }
  }
  return $out;
}


/**
 * Render HTML header for a given platform. 
 * Also adds code to $options['tocCode']
 */
function renderPlatformHeading($tocPrefix, $platform,&$options)
{

  $lbl = lang_get('platform');
  $name = htmlspecialchars($platform['name']);
  $options['tocCode'] .= '<b><p><a href="#' . prefixToHTMLID($tocPrefix) . '">' . $name . '</a></p></b>';
//<p>&nbsp;BATATASASSASSINAS</p>  
  $out = '<h1 class="doclevel" id="' . prefixToHTMLID($tocPrefix) . "\">$name</h1>";
  // platform description is enabled with test plan description option settings
  if ($options['showPlatformNotes'])
  {
    //$out .= '<div class="txtlevel">' . $platform['notes'] . "</div>\n <br/>";
  }
  return $out;
}


/**
 * simple utility function, to avoid lot of copy and paste
 * given an string, return an string useful to jump to an anchor on document
 */
function prefixToHTMLID($string2convert,$anchor_prefix='toc_')
{
	
  return $anchor_prefix . str_replace('.', '_', $string2convert);
}

function renderTestProjectItem($info)
{
	
  $lbl = init_labels(array('testproject' => null, 'context' => null, 'scope' => null));
  $out = '';
   $out .= renderSimpleChapter($lbl['testproject'] . ': ' . htmlspecialchars($info->tproject_name),$info->tproject_scope);
  return $out;
}

/**
 *
 */
function renderTestPlanItem($info)
{

  $lbl = init_labels(array('testplan' => null, 'scope' => null));
  $out = '';
  $out .= renderSimpleChapter($lbl['testplan'] . ': ' . htmlspecialchars($info->testplan_name),
                               $info->testplan_scope, 'page-break-before: avoid;');
  return $out;
}



/**
 *
 */
function renderExecutionForPrinting(&$dbHandler, $baseHref, $id, $userObj = null)
{

  static $tprojectMgr;
  static $tcaseMgr;
  static $st;

  $out =  '';
 
  if(!$st)
  {
    $st = new stdClass();
    $st->tables = tlDBObject::getDBTables(array('executions','builds'));

    $tprojectMgr = new testproject($dbHandler);
    $tcaseMgr = new testcase($dbHandler);
  }  

  $sql = " SELECT E.id AS execution_id, E.status, E.execution_ts, E.tester_id," .
         " E.notes, E.build_id, E.tcversion_id,E.tcversion_number,E.testplan_id," .
         " E.platform_id,E.execution_duration, " .
         " B.name AS build_name, B.id AS build_id " .
         " FROM {$st->tables['executions']} E " .
         " JOIN {$st->tables['builds']} B  ON B.id = E.build_id " .
         " WHERE E.id = " . intval($id); 

  $exec_info = $dbHandler->get_recordset($sql);
  if( !is_null($exec_info) )
  {
    $exec_info = $exec_info[0];


    $context['exec_id'] = intval($id);

    $context['tplan_id'] = $exec_info['testplan_id'];
    $context['platform_id'] = $exec_info['platform_id'];
    $context['build_id'] = $exec_info['build_id'];
    $context['level'] = '??'; // ???

    $node = $tprojectMgr->tree_manager->get_node_hierarchy_info($context['tplan_id']);
    $context['prefix'] = $tprojectMgr->getTestCasePrefix($node['parent_id']);
    $context['tproject_id'] = $node['parent_id'];
    unset($tprojectMgr);

    // IMPORTANT DEVELOPMENT NOTICE
    // Remember that on executions table we have following fields
    //
    // testplan_id 
    // tcversion_id 
    // tcversion_number 
    //  
    // a. (testplan_id ,tcversion_id) ARE LINK To testplan_tcversions table
    // b. if user creates a new version of a LINKED AND EXECUTED test case
    //    when he/she updates test plan, ONLY tcversion_id is updated,
    //    while tcversion_number HAS ALWAYS the VERSION HUMAN READABLE NUMBER
    //    of executed version.
    //
    // Then if you want to access specification of executed test case version
    // you need to proceed this way
    // 1. with tcversion_id => get test case id
    // 2. using test case id AND tcversion_number you access the data.
    // 
    // Why is important to remember this?
    // Because here we need to get data for renderTestCaseForPrinting
    //
    // The Cinematic Orchestra: To build a home Incubus: Wish you were here Mau Mau: La ola
    $node = $tcaseMgr->tree_manager->get_node_hierarchy_info($exec_info['tcversion_id']);

    // get_by_id($id,$version_id = self::ALL_VERSIONS, $filters = null, $options=null)
    $tcase = $tcaseMgr->get_by_id($node['parent_id'],null,array('version_number' => $exec_info['tcversion_number']));


    $renderOptions = array('toc' => 0,'body' => 1,'summary' => 1, 'header' => 0,'headerNumbering' => 0,
                           'passfail' => 1, 'author' => 1, 'notes' => 1, 'requirement' => 1, 'keyword' => 1, 
                           'cfields' => 1, 'displayVersion' => 1, 'displayDates' => 1, 
                           'docType' => SINGLE_TESTCASE, 'importance' => 1,
                           'step_exec_notes' => 1, 'step_exec_status' => 1);

    // need to change keys
    $tcase = $tcase[0];
    $tcase['tcversion_id'] = $tcase['id'];
    $tcase['id'] = $node['parent_id'];

    $env = new stdClass();
    $env->base_href = $baseHref;
    $env->reportType = $renderOptions['docType'];

    $indentLevel = 100000;

    $context['user'] = $userObj;
    $out .= renderTestCaseForPrinting($dbHandler,$tcase,$renderOptions,$env,$context,$indentLevel); 

    $out .= '<br>' . lang_get('direct_link') . ':' .
            $env->base_href . 'lnl.php?type=exec&id=' . intval($id) . '<br>';
    $exec_info = null;    
  }  

  return $out;
}