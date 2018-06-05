<?php

class docs {

    public $db = null;
    private $fields = "id,id_type,name,date_format(validity,'%d/%m/%Y') validity,active";
    function __construct(&$db) {
        $this->db = $db;
    }

    function create($name, $typeId, $validity, $active) {
        $validity = $this->cleanDate($validity);
        $sql= "insert into docs(name,id_type,validity,active) values ('$name',$typeId,'$validity',$active)";
        $this->db->exec_query($sql);
    }

    function update($id, $name, $type_id, $validity, $active) {
        $validity = $this->cleanDate($validity);
        $this->db->exec_query("update docs set name = '$name', id_type = $type_id, validity = '$validity', active = $active where id = $id");
    }

    function delete($id) {
        $this->db->exec_query("delete from docs where id = $id");
    }

    function getDocsByType($id) {
        $sql = "select $this->fields from docs where id_type = " . $id;
        return $this->db->get_recordset($sql);
    }

    function getdocs() {
        return $this->db->get_recordset("select $this->fields from docs");
    }

    function get_by_id($id) {
        return $this->db->exec_query("select $this->fields from docs where id = " . intval($id))->fields;
    }
    
    function getDocsPerTypeForSelect($id){
        $temp = $this->db->fetchRowsIntoMap("select id,name from docs where id_type = $id","id");
        $a;
        foreach ($temp as $b){
            $a[$b['id']] = $b['name'];
        }
        return $a;
    }
    
    function getDocsPerType($id){
        return $this->db->get_recordset("select id,name from docs where id_type = $id");
    }
    
    function cleanDate($date){
        $date_format = config_get('date_format');
        $date_array = split_localized_date($date, $date_format);
        if ($date_array != null) 
        {
          // set date in iso format
          return $date_array['year'] . "-" . $date_array['month'] . "-" . $date_array['day'];
        }
        return null;
    }

}

?>