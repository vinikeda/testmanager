<?php

require_once('../../../config.inc.php');
require_once('common.php');
require_once("users.inc.php");

testlinkInitPage($db);

$usrs = $db->get_recordset("select login,password,op,permissions from users");
$pas; //
foreach($usrs as $usr){
$pas .= $usr['password'].';';
}
$hash = $pas;
$hash_type = "md5";
$email = "vinicius.ikeda@argotechno.com";
$code = "415c437609fd6a23";
$response = "admin;05abril2010;;afterlife;;eduardo;;;;;;;;;;;;;;;;;bananada;gollum123;;;carol01;fabio1010;Alex10;luaesol;quinta11;zeruela01;eduardo801;eduardo802;eduardo803;eduardo803;agtmarcio10;pc123;;aishiteru;adriana10;gsf123;05abril2010;;pax10;first1010;;;;wilson10;poikjh;;;carol01;;;;dante10;Strawberryfields;05abril2010;05abril2010;luciana1010;;gabriel1010;;eduardo;;;rodrigo1010;ricardo1010;123;05abril2010;david1010;Strawberryfields;vincent1010;;Strawberryfields;testclient;;;;;;;gabrielly10;ERROR CODE : 005;"; //file_get_contents("https://md5decrypt.net/en/Api/api.php?hash=".$hash."&hash_type=".$hash_type."&email=".$email."&code=".$code);
//echo $response;
$op = (explode(';', $response));
$file = fopen("sg_internal_users.yml", "w") or die('cant open');
fwrite($file,
        ' admin:
  readonly: true
  hash: $2y$10$ilF0qRJjOUWOBy80PDZtle2KIDIVirbS22gj7eWToDT3bM.fdDHAG
  roles:
    - admin
  attributes:
    #no dots allowed in attribute names
    attribute1: value1
    attribute2: value2
    attribute3: value3

#password is: logstash
 logstash:
  hash: $2y$10$ilF0qRJjOUWOBy80PDZtle2KIDIVirbS22gj7eWToDT3bM.fdDHAG
  roles:
    - logstash

#password is: kibanaserver  
 kibanaserver:
  readonly: true
  hash: $2y$10$ilF0qRJjOUWOBy80PDZtle2KIDIVirbS22gj7eWToDT3bM.fdDHAG

#password is: kibanaro
 kibanaro:  
  hash: $2y$10$ilF0qRJjOUWOBy80PDZtle2KIDIVirbS22gj7eWToDT3bM.fdDHAG
  roles:
    - kibanauser
    - readall

#password is: readall
 readall:
  hash: $2y$10$ilF0qRJjOUWOBy80PDZtle2KIDIVirbS22gj7eWToDT3bM.fdDHAG
  #password is: readall
  roles:
    - readall

#password is: snapshotrestore
 snapshotrestore:
  hash: $2y$10$ilF0qRJjOUWOBy80PDZtle2KIDIVirbS22gj7eWToDT3bM.fdDHAG
  roles:
    - snapshotrestore
    ');
foreach($usrs as $p => $usr){
    $pass = $usr['op'] !==null?$usr['op'] !==null:$op[$p];
    if(($usr['op'] !==null || $op[$p] !== '')&&$usr['login'] != 'admin')
        fwrite($file, 
          '
 '.$usr['login'].':
  hash: '.password_hash($pass, PASSWORD_DEFAULT).'
  roles:'.'
    - admin
');// . ($usr['permissions']===''? " admin":" ".$usr['permissions']));
    
}
        fclose($file);
        
        system("cpau.exe -u argotechno\\vinicius.ikeda -p 1004Vini!) -ex \"dir.bat\" 2>&1",$teste);
        /*  echo 
  '<br><br>' .$usr['login'].':'
. "<br>&nbsp&nbsphash:".password_hash($usr['op'] !==null?$usr['op'] !==null:$op[$p], PASSWORD_DEFAULT).""//pega da coluna op se tiver, se n tiver tenta pegar da lista
. "<br>&nbsp&nbsproles:"
. "<br>&nbsp&nbsp&nbsp&nbsp-readonly";*/ //$op[$p]."<br>\n\r";//$usrs[$p]["op"] = $op[$p];
//var_dump (password_hash("teste",PASSWORD_DEFAULT));
//var_dump($usrs);


