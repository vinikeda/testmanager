<?php

class issues {

    public $db = null;

    function __construct(&$db) {
        $this->db = $db;
    }

    function create($name, $typeId, $validity, $active) {
        $this->db->exec_query("insert into docs(name,id_type,validity,active) values ('$name',$type,$validity,1)");
    }

    function update($id, $name, $type_id, $validity, $active) {
        $this->db->exec_query("update docs set name = '$name', id_type = $type_id, validity = $validity, active = $active where id = $id");
    }

    function delete($id) {
        $this->db->exec_query("delete from docs where id = $id");
    }

    function getDocsByType($id) {
        $sql = "select * from docs where id_type = " . $id;
        return $this->db->get_recordset($sql);
    }

    function getdocs() {
        return $this->db->get_recordset("select * from docs");
    }

    function get_by_id($id) {
        return $this->db->exec_query("select * from docs where id = " . intval($id))->fields;
    }

}

?>