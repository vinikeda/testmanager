<?php
class macros {
    public $db = null;
    function __construct(&$db){
        $this->db = $db;	
    }
    function create($name, $active, $fields, $values){
        $this->db->exec_query("insert into macro(name,active) values ('".$name."',$active)");
        $lastId = $this->db->insert_Id();
        foreach ($fields as $key=>$field){
            $sql = "insert into macro_values (id_cf,id_macro,value) values($field,$lastId,'$values[$key]')";
            $this->db->exec_query($sql);
        }
    }
    function update($id, $name, $fields, $values) {
        $this->db->exec_query("update macro set name = '$name' where id = $id");
        $this->db->exec_query("delete from macro_values where id_macro = $id");
        foreach ($fields as $key=>$field){
            $sql = "insert into macro_values (id_cf,id_macro,value) values($field,$id,'$values[$key]')";
            $this->db->exec_query($sql);
        }
    }
    function delete($id) {
        $this->db->exec_query("delete from macro where id = $id");
        $this->db->exec_query("delete from macro_values where id_macro = $id");
    }
    function getdata() {
        return $this->db->get_recordset("select id,name from macro");
    }
    
    function get_by_id($id) {
        $array =  $this->db->exec_query("select id,name,active from macro where id = " . intval($id))->fields;
        $array['cf'] = $this->db->get_recordset("select * from macro_values where id_macro = $id order by id");
        //foreach($docs_array as $doc) $terminal_array['docs'][] = $doc['id_doc'];
        return $array;
    }
    
    function getforselect(){
        $sql = "select * from macro";
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
        $sql = "select id, label from custom_fields inner join cfield_node_types on (id = field_id) where node_type_id = 12 and name not like('abc%')";
        $temp = $this->db->fetchRowsIntoMap($sql,"id");
        $a;
        foreach ($temp as $b){
            $a[$b['id']] = $b['label'];
        }
        return $a;
    }
}

