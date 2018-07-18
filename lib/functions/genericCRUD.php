<?php

/* 
 * esta classe foi criada para me acelerar a criação de CRUDs. 
 * Nela as funções básicas que eu geralemnte faço nos cruds vão ser englobadas.
 * A idéia dela é ser herdada pelas classes dos cruds para q elas possam 
 * realizar suas operações a mais sem se preocupar com essas básicas, 
 * facilitando a leitura
 */

/**
 * class used for easier access on database tables.
 * This class has functions that execute some commons SQL commands. Such as
 * Insert, Create, Select * and others.
 */
class GenericCrud{
    public $db = null;
    public $table = "";
    public $fields = array();
    public $id;
    /**
     * 
     * @param object $db
     * testlink db object for database use
     * 
     * @param string $table
     * table name where the commands will target
     * 
     * @param string $id
     * the collumn that is used as primary key
     */
    function __construct(&$db,$table,$id = 'id'){
            $this->db = $db;
            $this->table = $table;
            $this->id = $id;
    }
    /**return ALL the table data
     * 
     * @return array
     */
    function get(){
        $sql = "select * from $this->table";
        return $this->db->get_recordset($sql);
    }
    /**
     * Get all fields form all the collumns where its primary key is equal to ID
     * @param string $id
     * The value of primary key
     * 
     * @return array
     */
    function getById($id){
        $sql = "select * from $this->table where $this->id = $id";
    return $this->db->get_recordset($sql);
    }
    /** insert a row on the table
     * this function executes a SQL insert command using the parameters keys as
     * collumns and its values the values
     * 
     * @param array $fields
     * the array keys must tbe the collumn names and theyr values will be the 
     * values on these collumns.
     * 
     * example:
     * 
     * $fields['collumn_1'] = 'value 1';
     * $fields['collumn_2'] = 'value 2';
     * $fields['collumn_3'] = 'value 3';
     * 
     * this will be transformed in
     * INSERT INTO table_name (collumn_1,collumn_2,collumn_3) VALUES('value 1','value 2','value 3')
     * 
     */
    function create($fields){
        $field_strings = "";
        $value_strings = "";
        foreach($fields as $field_name => $field_value){
            $field_strings.="'$field_name'".(end_key($fields)!==$field_name?",":"");//a virgula só é colocada caso não seja o ultimo campo
            $value_strings.="'$field_value'".(end_key($fields)!==$field_name?",":"");//a virgula só é colocada caso não seja o ultimo campo
        }
        $sql = "INSERT INTO $this->table ($field_strings) values ($values_strings)";
        $this->db->exec_query($sql);
    }
    function update($fields,$id){
        $fields_strings = "";
        foreach($fields as $field_name=>$value){
            $field_strings .= "$field_name = '$value'".(end_key($fields)!==$field_name?",":"");
        }
        $sql = "update $this->table set $field_strings where $this->id = $id";
        $this->db->exec_query($sql);
    }
    /**
     * Gets all the rows from the selected collumn using the primary key as array key.
     * This function is used to make the options on <select> HTML tags
     * 
     * @param string $field
     * @return array
     */
    function getForSelect($field){
        $temp = $this->db->fetchRowsIntoMap("select $this->id,$this->field from $this->table","$this->id");
        $a;
        foreach ($temp as $b){
            $a[$b['id']] = $b[$field];
        }
        $a[0]='';
        return $a;
    }
    /**
     * Get all the collumns from the table where the selected field is equal to the value
     * @param string $field
     * @param string $value
     * @return array
     */
    function getPerFieldValue($field,$value){
        $sql = "select * from $this->table where $field = '$value'";
        return $this->db->get_recordset($sql);
    }
    function delete($id){
        $this->db->exec_query("delete from $this->table where $this->id = ".$id);
    }
    function end_key($arr){
        end($arr);
        return key($arr);
    }
    
}