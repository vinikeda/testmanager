<?php

class terminal {

    public $db = null;
    private $fields = "fabricante_id,name";
    function __construct(&$db) {
        $this->db = $db;
    }
/**
 * function : create
 * 
 * parameters: 
 * id_fabricante
 * name
 * array of id_docs
 * id of the tproject or tplan to do the relation
 * tplan or tproject
 *  */
    function create($fabricante_id,$name,$docs,$target = null,$typeTarget = null) {
        $sql= "insert into terminal($this->fields) values ($fabricante_id,'$name')";
        $this->db->exec_query($sql);
        $lastId = $this->db->insert_Id();
        
        foreach ($docs as $doc){
            $sql = '';
            if($typeTarget == null){
                $sql = "insert into terminal_docs (id_terminal,id_doc) values($lastId,$doc)";
            }else {
                $sql = "insert into terminal_$typeTarget (id_$typeTarget, id_terminal,id_doc) values($target,$lastId,$doc)";
            }
            $this->db->exec_query($sql);
        }
    }

    function update($id, $name, $type_id, $docs) {
        $this->db->exec_query("update terminal set name = '$name', fabricante_id = $type_id where id = $id");
        $nowDocs = $this->getDocs($id);//docs que o terminal tem atualmente
        if($docs == null){
            $this->db->exec_query("delete from terminal_docs where id_terminal = $id");
        }else if($nowDocs != null){
            foreach(array_diff($nowDocs,$docs) as $doc)$this->db->exec_query("delete from terminal_docs where id_terminal = $id and id_doc = $doc");
            foreach(array_diff($docs,$nowDocs) as $doc)$this->db->exec_query("insert into terminal_docs(id_terminal,id_doc) values($id,$doc)");//adicionar
        } else {
            foreach($docs as $doc)$this->db->exec_query("insert into terminal_docs(id_terminal,id_doc) values($id,$doc)");
        }
    }

    function delete($id) {
        $this->db->exec_query("delete from terminal where id = $id");
        $this->db->exec_query("delete from terminal_docs where id_terminal = $id");
    }

    function getDocs($id) {
        $sql = "select id_doc from terminal_docs where id_terminal = " . $id;
        $temp = $this->db->get_recordset($sql);
        $a;
        foreach ($temp as $value)$a[] = $value['id_doc'];
        return $a;
    }

    function getdata() {
        return $this->db->get_recordset("select id,$this->fields from terminal");
    }

    function get_by_id($id) {
        $terminal_array =  $this->db->exec_query("select id,$this->fields from terminal where id = " . intval($id))->fields;
        $terminal_array['docs'] = $this->db->get_recordset("select id_doc,id_type from terminal_docs inner join docs on (id_doc = docs.id) where id_terminal = $id");
        //foreach($docs_array as $doc) $terminal_array['docs'][] = $doc['id_doc'];
        return $terminal_array;
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