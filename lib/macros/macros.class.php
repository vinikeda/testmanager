<?php
class macros {
    public $db = null;
    function __construct(&$db){
        $this->db = $db;	
    }
    function create($name, $active, $fields, $values,$projects = null){
        $this->db->exec_query("insert into macro(name,active) values ('".$name."',$active)");
        $lastId = $this->db->insert_Id();
        var_dump($fields);
        var_dump($values);
        foreach ($fields as $key=>$field){
            $sql = "insert into macro_values (id_cf,id_macro,value) values($field,$lastId,'$values[$key]')";
            $this->db->exec_query($sql);
        }
        if($projects != null){
            foreach($projects as $marker){
                $this->addProject($lastId,$marker);				
            }
        }
    }
    function addProject($idIssue,$idMarker){
        $sql = "insert into macro_project (id_tproject, id_macro) SELECT * FROM (SELECT $idMarker id_tproject, $idIssue id_macro) AS tmp WHERE NOT EXISTS ( SELECT id_tproject,id_macro FROM macro_project WHERE id_tproject = $idMarker and id_macro = $idIssue ) LIMIT 1; ";
        $this->db->exec_query($sql);
    }
    function rmvProject($idIssue,$idMarker){
        $sql = "delete from macro_project where id_tproject = $idMarker and id_macro= $idIssue";
        $this->db->exec_query($sql);
    }
    function update($id, $name, $fields, $values,$projects) {
        $this->db->exec_query("update macro set name = '$name' where id = $id");
        $this->db->exec_query("delete from macro_values where id_macro = $id");
        foreach ($fields as $key=>$field){
            $sql = "insert into macro_values (id_cf,id_macro,value) values($field,$id,'$values[$key]')";
            $this->db->exec_query($sql);
        }
        if($projects != null){
            foreach(array_diff($this->getProjects($id),$projects) as $to_rmv)$this->rmvProject($id,$to_rmv);
            foreach($projects as $marker){
                $this->addProject($id,$marker);
            }
        }else $this->rmv_all_tprojects($id);
    }
    function delete($id) {
        $this->db->exec_query("delete from macro where id = $id");
        $this->db->exec_query("delete from macro_values where id_macro = $id");
        $sql = "delete from macro_project where id_macro = $id";
        $this->db->exec_query($sql);
    }
    function getdata() {
        return $this->db->get_recordset("select id,name from macro order by name");
    }
    
    function get_by_id($id) {
        $array =  $this->db->exec_query("select id,name,active from macro where id = " . intval($id))->fields;
        $array['cf'] = $this->db->get_recordset("select * from macro_values where id_macro = $id order by id");
        //foreach($docs_array as $doc) $terminal_array['docs'][] = $doc['id_doc'];
        return $array;
    }
    
    function getforselect($tproject){
        $sql = "select m.* from macro m inner join macro_project mp on(m.id = mp.id_macro) where mp.id_tproject = $tproject";
        $temp = $this->db->get_recordset($sql);
        $a;
        foreach ($temp as $b){
            $a[$b['id']] = $b['name'];
        }
        return $a;
    }
    function getBlankField($id){
        $sql = "select * from custom_fields where id = $id";
        $temp = $this->db->get_recordset($sql);
        $cfmgr= new cfield_mgr($this->db);
        $fields = $cfmgr->html_inputs($temp);
        $fields[0]['label_id'] = substr($fields[0]['label_id'],10,-1);
        return($fields);
    }
    /*function getAllBlankField(){
        $sql = "select * from custom_fields";
        $temp = $this->db->get_recordset($sql);
        $cfmgr= new cfield_mgr($this->db);
        $fields = $cfmgr->html_inputs($temp);
        $fields[0]['label_id'] = substr($fields[0]['label_id'],10,-1);
        return($fields);
    }*/
    function getFields($id) {
        $sql = "select * from macro_values inner join custom_fields cf on (id_cf = cf.id) where id_macro = $id";
        $temp = $this->db->get_recordset($sql);
        $cfmgr= new cfield_mgr($this->db);
        $fields = $cfmgr->html_inputs($temp);
        $a;
        foreach ($temp as $key=>$value){
            $a['ids'][] = substr($fields[$key]['label_id'],10,-1);
            $a['value'][] = $value['value'];
        }
        return $a;
    }
    function getCFieldList(){
        $sql = "select id, label from custom_fields inner join cfield_node_types on (id = field_id) where node_type_id = 12 and name not like('abc%') and type not in (9)";
        $temp = $this->db->fetchRowsIntoMap($sql,"id");
        $a;
        foreach ($temp as $b){
            $a[$b['id']] = $b['label'];
        }
        return $a;
    }
    function getProjects($id){
        $temp =  $this->db->fetchRowsIntoMap("select id_tproject from macro_project where id_macro = ".intval($id),"id_tproject");
        $a;
        foreach ($temp as $value)$a[] = $value['id_tproject'];
        return $a;
    }
}

