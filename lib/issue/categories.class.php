<?php
function batata() {var_dump('troslei');}
class categories {
	public $db = null;
	function __construct(&$db){
		$this->db = $db;	
	}
	
	function create($name,$markers = null){//var_dump($markers);
		$this->db->exec_query("insert into categories(name) values ('".$name."')");
		if($markers != null){
			$lastId = $this->db->insert_Id();
			foreach($markers as $marker){
				$this->addmarker($lastId,$marker);				
			}
		}
	}
	
	function update($id,$name,$markers = null){
		$this->db->exec_query("update categories set name = '".$name."' where id = ".$id);
		if($markers != null){
			
				/*var_dump($markers);
				var_dump($this->getMarkers($id));*/
			foreach(array_diff($this->getMarkers($id),$markers) as $to_rmv)$this->rmvmarker($id,$to_rmv);
			foreach($markers as $marker){
				$this->addmarker($id,$marker);				
			}
		}else rmv_all_marker($id);
	}

	function addmarker($idCategory,$idMarker){
		$this->db->exec_query("insert into markers_categories (id_marker, id_category) SELECT * FROM (SELECT $idMarker, $idCategory) AS tmp WHERE NOT EXISTS ( SELECT id_marker,id_category FROM markers_categories WHERE id_marker = $idMarker and id_category = $idCategory ) LIMIT 1; ");
		//echo "insert into markers_categories (id_marker, id_category) SELECT * FROM (SELECT $idMarker, $idCategory) AS tmp WHERE NOT EXISTS ( SELECT id_marker,id_category FROM markers_categories WHERE id_marker = $idMarker and id_category = $idCategory ) LIMIT 1; ";
	}
	
	function rmvmarker($idCategory,$idMarker){
		$sql = "delete from markers_categories where id_marker = $idMarker and id_category=$idCategory";
		$this->db->exec_query($sql);
	}
	function rmv_all_marker($idCategory){
		$sql = "delete from markers_categories where id_category=$idCategory";
		$this->db->exec_query($sql);
	}
	
	function delete($id){
		$this->db->exec_query("delete from categories where id = ".$id);
		$this->db->exec_query("delete from markers_categories where id_category = ".$id);
	}
	
	function getCategories(){
		return $this->db->get_recordset("select * from categories");
	}
	
	function get_by_id($id){
		return $this->db->exec_query("select * from categories where id = ".intval($id))->fields;
	}
	
	function getMarkers($id){
		$temp =  $this->db->fetchRowsIntoMap("select id_marker from markers_categories where id_category = ".intval($id),"id_marker");
		$a;
		foreach ($temp as $value)$a[] = $value['id_marker'];
		return $a;
	}
	
        function getCategoriesForSelect(){
            $temp = $this->db->fetchRowsIntoMap("select id,name from categories","id");
            $a;
            foreach ($temp as $b){
                    $a[$b['id']] = $b['name'];
            }
            $a[0]='todos';
            return $a;
        }
}
?>