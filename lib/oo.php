<?php

class Config implements ArrayAccess {
	private $db;
	private $_config;
	private $_volatile;
	private $customized;
	
	function __construct($db,$baseConfig=NULL){
		$this->db=$db;
		$this->customized=FALSE;
		$this->populate();
		if(is_array($baseConfig)) $this->importVolatile($baseConfig);
	}
	
	function populate(){
		$configRawData=$this->db->getProperties("CONFIG");
		foreach($configRawData as $name=>$value){
			$this->_config[$name]=$value;
		}
	}
	public function offsetExists($offset) {
		if (isset($this->_volatile[$offset])){
			return isset($this->_volatile[$offset]);
		} else {
			return isset($this->_config[$offset]);
		}
	}

	public function offsetGet($val) {
		if (isset($this->_volatile[$val])){
			return $this->_volatile[$val];
		} else {
			return $this->_config[$val];
		}
	}

	public function offsetSet($val , $c) {
    	//be careful direct write access works only at cache level until eventually commited
		$this->dirty=TRUE;
		$this->_volatile[$val]=$c;
		// $db->setProperty($val,"CONFIG",$c); this part makes the changes permanent if needed (thez are directly written to the db
	}

	public function offsetUnset($offset) {
		//TODO
		$this->dirty=TRUE;
		unset($this->_config[$offset]);
		unset($this->_volatile[$offset]);
	}

		
	function importVolatile($arr){
		if (is_array($arr)){
			foreach($arr as $k=>$v){
				$this->_volatile[$k]=$v;
			}
		}
	
	}
	
	function commit(){
		if ($this->customized){
			foreach($this->_volatile as $k=>$v){
				$this->db->setProperty($k,"CONFIG",$v);
				$this->_config[$k]=$v;
			}
		}
	}
}

class DMP {
	private $db;
	
	public $contentType=array(
		"MHSTRY",
		"FHISTRY",
		"DRUG",
		"ALLER",
		"SURGRY",
		"BTRANS"
	);
	
	public $patient;

	public $medicalHistory;
	public $treatments;
	public $allergies;
	
	public $ad;
	public $attachements;
	
	function __construct($db){
		$this->db=$db;
		$this->patient=new patient($db);
		
		$this->medicalHistory=new MedicalHistory($db);
		$this->treatments=new Treatments($db);
		$this->allergies=new Allergies($db);
		
		$this->advanceDirectives=new advanceDirectives($db);
		$this->attachments=new Attachments($db);
		$this->status=&$this->db->status;
		
	}

	public static function validateDate($t){
		$a=preg_split("/-/",$t);
		switch	(count($a)) {
		
		case 3:
			if (checkdate($a[1],$a[2],$a[0])) {
				return $t;
			} else {return FALSE;}
			break;
		case 2:
			if ($a[1]<=12 && $a[1]>=1) {
				return $t;
			} else {return FALSE;}
			break;
		case 1:
			if (preg_match("/\d{4}/",$a[0])) {
				return $t;
			} else { return FALSE;}
		default:
				return FALSE;
		}
		
	}
}


class Patient {
	private $db;
	
	private $name;
	private $surname;
	private $birthName;
	        
	private $birthDate;
	        
	private $gender;
	private $biologicalGender;
	        
	private $personalPhone;
	private $personalAddress;
	private $personalMail;
	        
	private $trusedPersonName;
	private $trusedPersonSurname;
	private $trusedPersonPhone;
	private $trusedPersonOther;
	        
	function __construct($db){
		$this->db=$db;
		$this->populate();
	}
	

	function populate(){
		$tempF=function($name,$sid) {
			$tProperty=$this->db->getProperty($name,$sid);
			if (is_a($tProperty,"DmpDbNotSet")){
				return NULL;
			} else {
				return $tProperty;
			}
		};
		$this->name=$tempF("name","PAT");
		//$this->db->getProperty("name","PAT");
		$this->surname=$tempF("surname","PAT");
		$this->birthName=$tempF("birthName","PAT");
		$this->gender=$tempF("gender","PAT");
		$this->biologicalGender=$tempF("biologicalGender","PAT");
		
		$this->personalPhone=$tempF("personalPhone","PAT");
		$this->personalAddress=$tempF("personalAddress","PAT");
		$this->personalMail=$tempF("personalMail","PAT");
		
		$this->trustedPersonName=$tempF("trustedPersonName","PAT");
		$this->trustedPersonSurname=$tempF("trustedPersonSurname","PAT");
		$this->trustedPersonName=$tempF("trustedPersonName","PAT");
			$this->trustedPersonPhone=$tempF("trustedPersonPhone","PAT");
		$this->trustedPersonOther=$tempF("trustedPersonOther","PAT");
	}
	
	function getFull(){
		$ret=array();
		$ret["name"]=$this->name;
		$ret["surname"]=$this->surname;
		$ret["birthName"]=$this->birthName;

		$ret["birthDate"]=$this->birthDate;

		$ret["gender"]=$this->gender;
		$ret["biologiclGender"]=$this->biologicalGender;

		$ret["personalPhone"]=$this->personalPhone;
		$ret["personalAddress"]=$this->personalAddress;
		$ret["personalMail"]=$this->personalMail;
		
		$ret["trustedPersonName"]=$this->trustedPersonName;
		$ret["trustedPersonSurname"]=$this->trustedPersonSurname;
		$ret["trustedPersonName"]=$this->trustedPersonName;
		$ret["trustedPersonPhone"]=$this->trustedPersonPhone;
		$ret["trustedPersonOther"]=$this->trustedPersonOther;
		
		return $ret;
	}
	
	function setName($t){
		$this->db->setProperty("name","PAT",$t,TRUE);
		$this->name=$this->db->getProperty("name","PAT");
		return $this->name;
	}
	
	function getName(){
		return $this->name;
	}
	
	function setSurname($t){
		$this->db->setProperty("surname","PAT",$t,TRUE);
		$this->surname=$this->db->getProperty("surname","PAT");
		return $this->surname;
	}
	
	function getSurname(){
		return $this->Surname;
	}
	
	function setBirthName($t){
		$this->db->setProperty("birthName","PAT",$t,TRUE);
		$this->birthName=$this->db->getProperty("birthName","PAT");
		return $this->birthName;
	}

	function getBirthName(){
		return $this->BirthName;
	}
	
	function setBirthDate($t){
		#format : AAAA or AAAA-MM or AAAA-MM-DD
		$validated=DMP::validateDate($t);
		if ($validated) {
			$this->db->setProperty("birthDate","PAT",$t,TRUE);
			$this->birthDate=$this->db->getProperty("birthDate","PAT");
			return $this->birthDate;
		} else {return FALSE;}		
	}

	function getBirthDate(){
		return $this->birthDate;
	}
	
	function setGender($t){
		if (strlen($t)!=1 && strpos($t,"MF")) {
			$this->db->setProperty("gender","PAT",$t,TRUE);
			$this->gender=$this->db->getProperty("gender","PAT");
			return $this->gender;
		} else {return FALSE;}
	}	

	function getGender(){
		return $this->gender;
	}    
	
	function setBiologicalGender($t){
		if (strlen($t)!=1 && strpos($t,"MF")) {
			$this->db->setProperty("biologicalGender","PAT",$t,TRUE);
			$this->biologicalGender=$this->db->getProperty("biologicalGender","PAT");
			return $this->biologicalGender;
		} else {return FALSE;}
	}	

	function getBiologicalGender(){
		return $this->biologicalGender;
	}    

	function setPersonalPhone($t){
		$this->db->setProperty("personalPhone","PAT",$t,TRUE);
		$this->personalPhone=$this->db->getProperty("personalPhone","PAT");
		return $this->personalPhone;
	}
	
	function getPersonalPhone(){
		return $this->personalPhone;
	}    
	
	function setPersonalAddress($t){
		$this->db->setProperty("personalAddress","PAT",$t,TRUE);
		$this->personalAddress=$this->db->getProperty("personalAddress","PAT");
		return $this->personalAddress;
	}
	
	function getPersonalAddress(){
		return $this->personalAddress;
	}       

	function setPersonalMail($t){
		$this->db->setProperty("personalMail","PAT",$t,TRUE);
		$this->personalMail=$this->db->getProperty("personalMail","PAT");
		return $this->personalMail;
	}
	
	function getPersonalMail(){
		return $this->personalMail;
	}       

	function setTrustedPersonName($t){
		$this->db->setProperty("trustedPersonName","PAT",$t,TRUE);
		$this->trustedPersonName=$this->db->getProperty("trustedPersonName","PAT");
		return $this->trustedPersonName;
	}
	
	function getTrustedPersonName(){
		return $this->trustedPersonName;
	}       
	
	function setTrustedPersonSurname($t){
		$this->db->setProperty("trustedPersonSurname","PAT",$t,TRUE);
		$this->trustedPersonSurname=$this->db->getProperty("trustedPersonSurname","PAT");
		return $this->trustedPersonSurname;
	}
	
	function getTrustedPersonSurname(){
		return $this->trustedPersonSurname;
	}       

	function setTrustedPersonPhone($t){
		$this->db->setProperty("trustedPersonPhone","PAT",$t,TRUE);
		$this->trustedPersonPhone=$this->db->getProperty("trustedPersonPhone","PAT");
		return $this->trustedPersonPhone;
	}
	
	function getTrustedPersonPhone(){
		return $this->trustedPersonPhone;
	}       

	function setTrustedPersonOther($t){
		$this->db->setProperty("trustedPersonOther","PAT",$t,TRUE);
		$this->trustedPersonOther=$this->db->getProperty("trustedPersonOther","PAT");
		return $this->trustedPersonOther;
	}
	
	function getTrustedPersonOther(){
		return $this->trustedPersonOther;
	}       
}

class Attachments{
	public function getGenericAttachements($t){
		switch ($t) {
		case "PAT":
			return array(
				array("sID"=>"PAT:CNI","content"=>"Carte Nationale d'Identité"),
				array("sID"=>"PAT:PASSPORT","content"=>"Passeport"),
				array("sID"=>"PAT:CTS-RCV","content"=>"Carte de Groupe Sanguin (Receveur)"),
				array("sID"=>"PAT:CTS-DON","content"=>"Carte de Groupe Sanguin (Donneur)"),
			);
			break;
		case "AD":
			return array(
				array("sID"=>"AD:OFF","content"=>"Directives Anticipées (officialisées)"),
				array("sID"=>"AD:NOTOFF","content"=>"Directives Anticipées (non officialisées)"),
			);
			break;
		default:
			return NULL;
		}
	}
		
	private $db;
	function __construct($db){
		$this->db=$db;
	}

	public function getByID($id){
		return $this->db->getAttachment($id);
	}
	
	public function add($content,$mimeType,$sID,$cryptkey=NULL,$heap=NULL) {
		return $this->db->addAttachment($content,$mimeType,$sID,$cryptkey,$heap);
	}

	public function delete($id){
		return $this->db->deleteAttachment($id);
	}
	
	public function attach($id,$sID) {
		if (is_null($sID)){
			return NULL;
		} else {
			$this->db->linkAttachment($id,$sID);
		}
	
	}
	
	public function list(){
		return $this->db->listAttachments();
	}
	
	
}

class MEvents {
	private $db;
	private $subID;
	
	function __construct($db){
		$this->db=$db;
	}

	public function getByID($id){
		return $this->db->getMEvent($id);
	}
	
	public function add($type, $content, $start, $end=NULL, $crypt){
		if (in_array($type,DMP::contentType)){
		return $this->db->addMEvent($type, $content, $start, $end, $crypt);
		} else {
			return NULL;
		}
	}
	
	public function delete($id){
		return $this->db->deleteMEvent($id);
	}
	
	public function getSID($id){
		return "ME:".$id;
	}
	
	public function getFull(){
		if ($this->subID){
			//$res=$db->get
			#TODO
		}
	}
}
class MedicalHistory extends MEvents {
	function add($content, $start, $end=NULL, $crypt){
		parent::add("MHSTRY", $content, $start, $end, $crypt);
	}
}
class Treatments extends MEvents {
	function add($content, $start, $end=NULL, $crypt){
		parent::add("DRUG", $content, $start, $end, $crypt);
	}
}
class Allergies extends MEvents {
	function add($content, $start, $end=NULL, $crypt){
		parent::add("ALLER", $content, $start, $end, $crypt);
	}
}

class advanceDirectives {
	private $db;
	private $limitations=array(
		"maintain_InUnrecoverableComa"=>"bool",
		"begin_CPR"=>"bool",
		"begin_Intubation"=>"bool",
		"begin_Dialysis"=>"bool",
		"begin_AnySurgery"=>"bool",
		"maintain_CPR"=>"bool",
		"maintain_Intubation"=>"bool",
		"maintain_Dialysis"=>"bool",
		"maintain_ParenteralAlimentation"=>"bool",
		"allow_ContinuousAntalgicSedation"=>"bool",
	);
	
	function __construct($db){
		$this->db=$db;
	}
	
	function get($id){
		if (in_array($id,array_keys($this->limitations)) || $id="other"){
			return $this->db->getProperty($id,"AD");
		} else {return NULL;}
	}
	
	function set($id,$t){
		if (in_array($id,array_keys($this->limitations)) || $id="other"){
			//print "$id : $t (".gettype($t).")<br/>";
			return $this->db->setProperty($id,"AD",strval($t));
		} else {
			//print "XXX<br/>";
			return NULL;
		}
	}
	
	function getFull(){
		$tad= $this->db->getProperties("AD");
		$ret=array();
		foreach($tad as $row){
			$ret[$row["name"]]=$row["content"];
		}
		return $ret;
	}
}
?>