<?php
class issues {
    public $db = null;
    public $tproject_strings = '';
    function __construct(&$db){
        $this->db = $db;
        $tproject_mgr = new testproject($this->db);
        $tprojects = $tproject_mgr->get_accessible_for_user($_SESSION['currentUser']->dbID,
                                                        array('output' => 'map_name_with_inactive_mark',
                                                                  'field_set' => null,
                                                                  'order_by' => null));
        $this->tproject_strings = implode(',',array_keys($tprojects));
    }

    function create($description,$categoryId,$whoAccept,$descText,$markers = null,$projects = null){
        $description = mysql_real_escape_string($description);
        $descText = mysql_real_escape_string($descText);
        if(!$this->verifyDuplicity($description,$markers)){

            $author = ($whoAccept == "QA"?'qa':($whoAccept == "sup"?'super':'analis'));//echo "insert into issues(description,category_id,".$author."_accept,text_description) values ('$description',$categoryId,1,'$descText')";
            $this->db->exec_query("insert into issues(description,category_id,".$author."_accept,text_description) values ('$description',$categoryId,1,'$descText')");
            $lastId = $this->db->insert_Id();
            if($markers != null){
                foreach($markers as $marker){
                    $this->addmarker($lastId,$marker);				
                }
            }
            if($projects != null){
                foreach($projects as $marker){
                    $this->addProject($lastId,$marker);				
                }
            }
            return true;
        }else{
                return false;

        }
    }

    function update ($id ,$description,$whoAccept,$category,$descText, $markers = null, $projects){
        $safe_description = mysql_real_escape_string($description);
        $safe_desctext = mysql_real_escape_string($descText);
        $sql = "update issues set description = '$safe_description', category_id = $category, text_description = '$safe_desctext' where id = $id";
        $this->db->exec_query($sql);
        if($markers != null){
            foreach(array_diff($this->getMarkers($id),$markers) as $to_rmv)$this->rmvmarker($id,$to_rmv);
            foreach($markers as $marker){
                $this->addmarker($id,$marker);				
            }
        }else $this->rmv_all_marker($id);
        
        if($projects != null){
            foreach(array_diff($this->getProjects($id),$projects) as $to_rmv)$this->rmvProject($id,$to_rmv);
            foreach($projects as $marker){
                $this->addProject($id,$marker);
            }
        }else $this->rmv_all_tprojects($id);
        $this->qaReject($id);
        $this->analistReject($id);
        $this->superReject($id);
        switch ($whoAccept){
            case 'QA':$this->qaAccept($id);
            case 'analis':$this->analistAccept($id);
            case 'sup':$this->superAccept($id);
        }
    }

    function addmarker($idIssue,$idMarker){
        $sql = "insert into issues_markers (id_marker, id_issue) SELECT * FROM (SELECT $idMarker, $idIssue) AS tmp WHERE NOT EXISTS ( SELECT id_marker,id_issue FROM issues_markers WHERE id_marker = $idMarker and id_issue = $idIssue ) LIMIT 1; ";
        //echo $sql;
        $this->db->exec_query($sql);
    }

    function addProject($idIssue,$idMarker){
        $sql = "insert into issues_tprojects (id_tproject, id_issue) SELECT * FROM (SELECT $idMarker, $idIssue) AS tmp WHERE NOT EXISTS ( SELECT id_tproject,id_issue FROM issues_tprojects WHERE id_tproject = $idMarker and id_issue = $idIssue ) LIMIT 1; ";
        //echo $sql;
        $this->db->exec_query($sql);
    }
    
    function rmvmarker($idIssue,$idMarker){
        $sql = "delete from issues_markers where id_marker = $idMarker and id_issue= $idIssue";
        $this->db->exec_query($sql);
    }

    function rmvProject($idIssue,$idMarker){
        $sql = "delete from issues_tprojects where id_tproject = $idMarker and id_issue= $idIssue";
        $this->db->exec_query($sql);
    }
    
    function rmv_all_marker($idCategory){
        $sql = "delete from issues_markers where id_issue=$idCategory";
        $this->db->exec_query($sql);
    }
    
    function rmv_all_tprojects($idCategory){
        $sql = "delete from issues_tprojects where id_issue=$idCategory";
        $this->db->exec_query($sql);
    }

    function verifyDuplicity($description,$markers = null){
        if($markers != null){
            $stacked = $markers[0];
            foreach($markers as $marker){
                    $stacked .= ", ".$marker ;
            }
            $sql = "select * from 
            (select issues_markers.id_issue 'id', count(issues_markers.id) total, counter

            from issues_markers 
            inner join (select distinct id_issue,count(issues.description) counter from issues_markers inner join issues on id_issue = issues.id where id_marker in ($stacked) and description = '$description' group by id_issue) issues_counters on (issues_counters.id_issue = issues_markers.id_issue)

            group by issues_markers.id_issue) tmp

            where total = counter";
        }
        else{
                $sql = "select id from issues where description = '$description'";			
        }//echo $sql;
        $rs = $this->db->get_recordset($sql);
        return (count($rs)>0);		
    }

    function qaAccept($id){
        $sql = "update issues set qa_accept = 1 where id =". intval($id);
        $this->db->exec_query($sql);
    }
    function analistAccept($id){
        $sql = "update issues set analis_accept = 1 where id =". intval($id);
        $this->db->exec_query($sql);
    }
    function superAccept($id){
        $sql = "update issues set super_accept = 1 where id =". intval($id);
        $this->db->exec_query($sql);
    }
    function activate($id){
        $sql = "update issues set active = 1 where id =". intval($id);
        $this->db->exec_query($sql);
    }
    function qaReject($id){
        $sql = "update issues set qa_accept = 0 where id =". intval($id);
        $this->db->exec_query($sql);
    }
    function analistReject($id){
        $sql = "update issues set analis_accept = 0 where id =". intval($id);
        $this->db->exec_query($sql);
    }
    function superReject($id){
        $sql = "update issues set super_accept = 0 where id =". intval($id);
        $this->db->exec_query($sql);
    }
    function desactivate($id){
        $sql = "update issues set active = 0 where id =". intval($id);
        $this->db->exec_query($sql);
    }


    /*
    function: redirectIssues.

    essa função foi criada pois para deletar uma issue é necessário indicar para onde todos os steps da que está sendo deletada vão passar a apontar.
    */
    function redirectIssues($idOld,$idNew){
        $sql = "update execution_tcsteps set id_issue = ? where id_issue = ?";
        $this->db->db->execute($sql,array($idNew,$idOld));

    }

    function transferIssues($id,$newid){
        redirectIssues($id,$newid);
        $sql = "delete from issues_markers where id_issue = ?";
        $this->db->db->execute($sql,array($id));
        $sql = "delete from issues where id = ?";
        $this->db->db->execute($sql,array($id));
    }

    function delete($id){            
        $sql = "delete from issues_markers where id_issue = $id";
        $this->db->exec_query($sql);
        $sql = "delete from issues_tprojects where id_issue = $id";
        $this->db->exec_query($sql);
        $sql = "delete from issues_executions where id_issue = $id";
        $this->db->exec_query($sql);
        $sql = "delete from issues where id = $id";
        $this->db->exec_query($sql);

    }

    function getIssuesByCategory($id){
        $sql = "select id, description, qa_accept, analis_accept from issues where category_id = ".$id;
        return $this->db->get_recordset($sql);	
    }
    
    function getIssuesByMarks($markers){
        $stacked = "";
        foreach($markers as $marker){
                $stacked .= "inner join ( select * from `issues_markers` where id_marker = $marker ) a$marker on (issues.id = a$marker.id_issue) ";
        }
        $sql = "SELECT issues.id,description from issues $stacked";
        //echo $sql;
        return $this->db->get_recordset($sql);
    }	
    function getIssuesByMarksAndCategories($category,$markers){
        $stacked = "";
        foreach($markers as $marker){
                $stacked .= "inner join ( select * from `issues_markers` where id_marker = $marker ) a$marker on (issues.id = a$marker.id_issue) ";
        }
        $sql = "SELECT issues.id,description from issues $stacked where issues.category_id = $category";
        //echo $sql;
        //$sql = "select distinct a.id,a.description,a.qa_accept, a.analis_accept from issues a inner join issues_markers b on (a.id = b.id_issue) where a.category_id = ".$category." and  b.id_marker in(".$stacked.")";
        return $this->db->get_recordset($sql);
    }	

    function getIssues(){
        return $this->db->get_recordset("select issues.* from issues inner join issues_tprojects on (issues.id = id_issue) where id_tproject in ($this->tproject_strings) union select * from issues where id not in (select distinct id_issue from issues_tprojects)");
    }
    function getActiveIssues(){
        
        return $this->db->get_recordset("select issues.* from issues inner join issues_tprojects on (issues.id = id_issue) where active = 1 and id_tproject in ($this->tproject_strings) union select * from issues where id not in (select distinct id_issue from issues_tprojects)");
    }
    function get_by_id($id){
        return $this->db->exec_query("select * from issues where id = ".intval($id))->fields;
    }

    function getMarkers($id){
        $temp =  $this->db->fetchRowsIntoMap("select id_marker from issues_markers where id_issue = ".intval($id),"id_marker");
        $a;
        foreach ($temp as $value)$a[] = $value['id_marker'];
        return $a;
    }
    
    function getProjects($id){
        $temp =  $this->db->fetchRowsIntoMap("select id_tproject from issues_tprojects where id_issue = ".intval($id),"id_tproject");
        $a;
        foreach ($temp as $value)$a[] = $value['id_tproject'];
        return $a;
    }
    
    function assignIssue($idExecutions,$idIssues){
        if(count($idExecutions)==0||$idIssues==0)return;
        foreach($idExecutions as $exec){
            $sql = "insert into issues_executions(id_execution,id_issue)values";
            foreach($idIssues as $id_issue=>$issue){
                $sql.="($exec,$id_issue),";
            }
            $sql = substr($sql,0,-1);
            $this->db->exec_query($sql);
        }
    }
    function reassignIssue($idExecution,$idIssues){
        if($idExecution==0||$idIssues==0)return;
        $idlist = '';
        foreach($idissues as $idissue)$idlist .= $isissue.',';
        $idlist = substr($idlist, 0,-1);
        $sql = "delete from issues_executions where id_execution in ($idExecution)";
        $this->db->exec_query($sql);$temp[0] = $idExecution;
        $this->assignIssue($temp, $idIssues);
    }
    function getAssignedIssue($id){
        $sql = "select * from issues_executions where id_execution = $id";
        return $this->db->get_recordset($sql);
    }
}
?>