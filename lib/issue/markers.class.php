<?php
class markers {
	public $db = null;
	function __construct(&$db){
		$this->db = $db;	
	}
	function create($name){
		$this->db->exec_query("insert into markers(name) values ('".$name."')");
	}
	function update($id,$name){
		$this->db->exec_query("update markers set name = '".$name."' where id = ".$id);
	}
	function delete($id){
		$this->db->exec_query("delete from markers where id = ".$id);
		
		$this->db->exec_query("delete from issues_markers where id_marker = ".$id);
		
		$this->db->exec_query("delete from markers_categories where id_marker = ".$id);
	}
	
	function getMarkers(){//sim, busca todos os marcadores.
		return $this->db->fetchRowsIntoMap("select id,name from markers","id");
	}
	
	function getMarkersByCategories($id){
		return $this->db->get_recordset("select distinct a.id, a.name from markers inner join markers_categories b on a.id = b.id_marker where b.id_category = ".$id);
	}
	
	function getMarkersByIssue($id){
		return $this->db->get_recordset("select distinct a.id, a.name from markers inner join issues_markers b on a.id = b.id_marker where b.id_issue = ".$id);
	}
	
	function get_by_id($id){
		return $this->db->exec_query("select * from markers where id = ".intval($id))->fields;
	}
	function getMarkersForSelection(){//sim, busca todos os marcadores.
		$temp = $this->db->fetchRowsIntoMap("select id,name from markers","id");
		$a;
		foreach ($temp as $b){
			$a[$b['id']] = $b['name'];
		}
		return $a;
	}
	
}
?>