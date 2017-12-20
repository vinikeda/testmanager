<?php
class subadiq_mgr {
	
	var $db;
	
	function subadiq_mgr(&$db){$this->db = &$db;}
	
	function setZeroOneAttr($id,$attr,$zeroOne)
	{
		$debugMsg = 'Class:' . __CLASS__ . ' - Method: ' . __FUNCTION__;

		$sql = "/* $debugMsg */ " . 
		"UPDATE sub_adquirente SET {$attr}=" . ($zeroOne ? 1 : 0) . " WHERE id=" . intval($id);
		$this->db->exec_query($sql); 
	}

	 function setActive($id)
	{
		$debugMsg = 'Class:' . __CLASS__ . ' - Method: ' . __FUNCTION__;
		$this->setZeroOneAttr($id,'active',1);
	}

	/**
	*
	*/
	function setInactive($id)
	{
		$debugMsg = 'Class:' . __CLASS__ . ' - Method: ' . __FUNCTION__;
		$this->setZeroOneAttr($id,'active',0);
	}

	/**
	*
	*/
	function setOpen($id)
	{
		$debugMsg = 'Class:' . __CLASS__ . ' - Method: ' . __FUNCTION__;
		$this->setZeroOneAttr($id,'is_open',1);
	}

	/**
	*
	*/
	function setClosed($id)
	{
		$debugMsg = 'Class:' . __CLASS__ . ' - Method: ' . __FUNCTION__;
		$this->setZeroOneAttr($id,'is_open',0);
	}

	function get_by_id($id){
		$debugMsg = 'Class:' . __CLASS__ . ' - Method: ' . __FUNCTION__;
		$safe_id = intval($id);
		
		$sql = 'SELECT * FROM SUB_ADQUIRENTE WHERE id = '. $safe_id;
		
		$result = $this->db->exec_query($sql);//print_r($result);
		//$myrow = $this->db->fetch_array($result);
		return $result->fields;//$myrow;
	}
	
	 function delete($id)
	{
		$sql = "delete from sub_adquirente where id = ".$id;
		$result=$this->db->exec_query($sql);
		
		//$sql = "select id from testplans where id_subadiquirentes = ".$id;
		//$result=$this->db->exec_query($sql);
	}
	
	
	function update($id,$name/*,$notes,*/,$active=null,$open=null/*,$release_date='',$closed_on_date=''*/){
		$sql = "UPDATE sub_adquirente set name = '".$name."', is_open = ".$open.", active = ".$active." where id = ".$id;
		//echo $sql;
		$result = $this->db->exec_query($sql);
		return $result ? 1 : 0;
	}
	
	function create($tplan_id,$name/*,$notes = ''*/,$active=1,$open=1)
  {
    $targetDate=trim($release_date);
    $sql = " INSERT INTO sub_adquirente" .
           " (testproject_id,name,active,is_open) " .
           " VALUES (". $tplan_id . ",'" . $this->db->prepare_string($name) . "',";

    /*if($targetDate == '')
    {
      $sql .= "NULL,";
    }       
    else
    {
      $sql .= "'" . $this->db->prepare_string($targetDate) . "',";
    }*/
    
    // Important: MySQL do not support default values on datetime columns that are functions
    // that's why we are using db_now().
    $sql .= "{$active},{$open})";                        
    //echo $sql;
    $new_build_id = 0;
    $result = $this->db->exec_query($sql);
    if ($result)
    {
      $new_build_id = $this->db->insert_id("sub_adquirente");
    }
    
    return $new_build_id;
  }
	
	function get_subadiqs($id){
	$sql = "select id, name, active, is_open from sub_adquirente where testproject_id = ".$id;
	$rs = $this->db->fetchRowsIntoMap($sql,'id');
	return $rs;	
}
	
	
	function get_active_subadiqs($id){
		$sql = "select id, name, active, is_open from sub_adquirente where active = 1 and testproject_id = ".$id;
		$rs = $this->db->fetchRowsIntoMap($sql,'id');
		return $rs;	
	}
	
	function get_empty_subadiqs($testprojectID){
		$sql = "select sub_adquirente.id, name, sub_adquirente.active, sub_adquirente.is_open from sub_adquirente left join testplans on sub_adquirente.id = id_subadiquirentes where sub_adquirente.active = 1 and isnull(id_subadiquirentes) and sub_adquirente.testproject_id = ".$testprojectID;
		$rs = $this->db->fetchRowsIntoMap($sql,'id');
		return $rs;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}	
?>