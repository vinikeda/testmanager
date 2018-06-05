<?php
class fabricantes {
	public $db = null;
	function __construct(&$db){
		$this->db = $db;	
	}
	
	function create($name){//var_dump($markers);
		$this->db->exec_query("insert into fabricante(name) values ('".$name."')");
	}
	
	function update($id,$name){
		$this->db->exec_query("update fabricante set name = '".$name."' where id = ".$id);
	}

	function delete($id){
		$this->db->exec_query("delete from fabricante where id = ".$id);
	}
	
	function getCategories(){
		return $this->db->get_recordset("select * from fabricante");
	}
	
	function get_by_id($id){
		return $this->db->exec_query("select * from fabricante where id = ".intval($id))->fields;
	}
	
        function getFabricantesForSelect(){
            $temp = $this->db->fetchRowsIntoMap("select id,name from fabricante","id");
            $a;
            foreach ($temp as $b){
                    $a[$b['id']] = $b['name'];
            }
            return $a;
        }
}
?>

