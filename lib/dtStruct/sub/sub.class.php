<?php

require_once '../../functions/genericCRUD.php';
/*por hora está inutilizada pois a classe pai atente totalmente a demanda*/
class sub extends GenericCrud{
    public function __construct(&$db) {
        parent::__construct($db,'subadquirentes');
    }
    function getPerTproject($id){/*precisa arrumar para que a querie busque por projeto de teste, assim, ficará correto*/
        $sql = "select * from $this->table where tesproject_id";
        return $this->db->get_recordset($sql);
    }
}
