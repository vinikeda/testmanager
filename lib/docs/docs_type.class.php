<?php
class categories {
	public $db = null;
	function __construct(&$db){
		$this->db = $db;	
	}
	
	function create($name){//var_dump($markers);
		$this->db->exec_query("insert into docs_type(name) values ('".$name."')");
	}
	
	function update($id,$name){
		$this->db->exec_query("update docs_type set name = '".$name."' where id = ".$id);
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
	
        function getCategoriesForSelect(){
            $temp = $this->db->fetchRowsIntoMap("select id,name from docs_type","id");
            $a;
            foreach ($temp as $b){
                    $a[$b['id']] = $b['name'];
            }
            return $a;
        }
}
?>