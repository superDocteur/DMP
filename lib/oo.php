<?php

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
		$this->name=$this->db->getProperty("name","PAT");
		$this->surname=$this->db->getProperty("surname","PAT");
		$this->birthName=$this->db->getProperty("birthName","PAT");
		$this->gender=$this->db->getProperty("gender","PAT");
		$this->biologicalGender=$this->db->getProperty("biologicalGender","PAT");
		
		$this->personalPhone=$this->db->getProperty("personalPhone","PAT");
		$this->personalAddress=$this->db->getProperty("personalAddress","PAT");
		$this->personalMail=$this->db->getProperty("personalMail","PAT");
		
		$this->trustedPersonName=$this->db->getProperty("trustedPersonName","PAT");
		$this->trustedPersonSurname=$this->db->getProperty("trustedPersonSurname","PAT");
		$this->trustedPersonName=$this->db->getProperty("trustedPersonName","PAT");
		$this->trustedPersonOther=$this->db->getProperty("trustedPersonOther","PAT");
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
		$ret["trustedPersonOther"]=$this->trustedPersonOther;
		
		return $ret;
	}
	
	function setName($t){
		$this->db->setProperty("name","PAT",$t);
		$this->name=$this->db->getProperty("name","PAT");
		return $this->name;
	}
	
	function getName(){
		return $this->name;
	}
	
	function setSurname($t){
		$this->db->setProperty("surname","PAT",$t);
		$this->surname=$this->db->getProperty("surname","PAT");
		return $this->surname;
	}
	
	function getSurname(){
		return $this->Surname;
	}
	
	function setBirthName($t){
		$this->db->setProperty("birthName","PAT",$t);
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
			$this->db->setProperty("birthDate","PAT",$t);
			$this->birthDate=$this->db->getProperty("birthDate","PAT");
			return $this->birthDate;
		} else {return FALSE;}		
	}

	function getBirthDate(){
		return $this->birthDate;
	}
	
	function setGender($t){
		if (strlen($t)!=1 && strpos($t,"MF")) {
			$this->db->setProperty("gender","PAT",$t);
			$this->gender=$this->db->getProperty("gender","PAT");
			return $this->gender;
		} else {return FALSE;}
	}	

	function getGender(){
		return $this->gender;
	}    
	
	function setBiologicalGender($t){
		if (strlen($t)!=1 && strpos($t,"MF")) {
			$this->db->setProperty("biologicalGender","PAT",$t);
			$this->biologicalGender=$this->db->getProperty("biologicalGender","PAT");
			return $this->biologicalGender;
		} else {return FALSE;}
	}	

	function getBiologicalGender(){
		return $this->biologicalGender;
	}    

	function setPersonalPhone($t){
		$this->db->setProperty("personalPhone","PAT",$t);
		$this->personalPhone=$this->db->getProperty("personalPhone","PAT");
		return $this->personalPhone;
	}
	
	function getPersonalPhone(){
		return $this->personalPhone;
	}    
	
	function setPersonalAddress($t){
		$this->db->setProperty("personalAddress","PAT",$t);
		$this->personalAddress=$this->db->getProperty("personalAddress","PAT");
		return $this->personalAddress;
	}
	
	function getPersonalAddress(){
		return $this->personalAddress;
	}       

	function setPersonalMail($t){
		$this->db->setProperty("personalMail","PAT",$t);
		$this->personalMail=$this->db->getProperty("personalMail","PAT");
		return $this->personalMail;
	}
	
	function getPersonalMail(){
		return $this->personalMail;
	}       

	function setTrustedPersonName($t){
		$this->db->setProperty("trustedPersonName","PAT",$t);
		$this->trustedPersonName=$this->db->getProperty("trustedPersonName","PAT");
		return $this->trustedPersonName;
	}
	
	function getTrustedPersonName(){
		return $this->trustedPersonName;
	}       
	
	function setTrustedPersonSurname($t){
		$this->db->setProperty("trustedPersonSurname","PAT",$t);
		$this->trustedPersonSurname=$this->db->getProperty("trustedPersonSurname","PAT");
		return $this->trustedPersonSurname;
	}
	
	function getTrustedPersonSurname(){
		return $this->trustedPersonSurname;
	}       

	function setTrustedPersonPhone($t){
		$this->db->setProperty("trustedPersonPhone","PAT",$t);
		$this->trustedPersonPhone=$this->db->getProperty("trustedPersonPhone","PAT");
		return $this->trustedPersonPhone;
	}
	
	function getTrustedPersonPhone(){
		return $this->trustedPersonPhone;
	}       

	function setTrustedPersonOther($t){
		$this->db->setProperty("trustedPersonOther","PAT",$t);
		$this->trustedPersonOther=$this->db->getProperty("trustedPersonOther","PAT");
		return $this->trustedPersonOther;
	}
	
	function getTrustedPersonOther(){
		return $this->trustedPersonOther;
	}       
}

class Attachments{
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
			print "$id : $t (".gettype($t).")<br/>";
			return $this->db->setProperty($id,"AD",strval($t));
		} else {print "XXX<br/>";return NULL;}
	}
	
	function getFull(){
		$tad= $this->db->getProperties("AD");
		foreach($tad as $row){
			$ret[$row["name"]]=$row["content"];
		}
		return $ret;
	}
}
?>