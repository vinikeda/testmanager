<?php
class docs_types {
    public $db = null;
    function __construct(&$db){
            $this->db = $db;	
    }

    function create($name,$cf_id){//var_dump($markers);
            $this->db->exec_query("insert into docs_type(name,id_related_cf) values ('".$name."',$cf_id)");
    }

    function update($id,$name,$cf_id){
            $this->db->exec_query("update docs_type set name = '".$name."', id_related_cf = $cf_id where id = ".$id);
    }

    function delete($id){
            $this->db->exec_query("delete from docs_type where id = ".$id);
    }

    function getCategories(){
            return $this->db->get_recordset("select * from docs_type");
    }

    function get_by_id($id){
            return $this->db->exec_query("select * from docs_type where id = ".intval($id))->fields;
    }

    function getDocs_typesForSelect(){
        $temp = $this->db->fetchRowsIntoMap("select id,name from docs_type","id");
        $a;
        foreach ($temp as $b){
                $a[$b['id']] = $b['name'];
        }
        return $a;
    }
    function getCFieldList(){
        $sql = "select id, label from custom_fields inner join cfield_node_types on (id = field_id) where node_type_id = 12";
        $temp = $this->db->fetchRowsIntoMap($sql,"id");
        $a;
        foreach ($temp as $b){
                $a[$b['id']] = $b['label'];
        }
        return $a;
    }
    function getDocs_types(){
        $temp = $this->db->exec_query("select id,name from docs_type");
        return $temp;
    }
}
?>