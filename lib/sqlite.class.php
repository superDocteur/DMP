<?php
##### sqlite3 db
# Ne gère pas le cryptage

class DmpDbError {
	protected $message;
	
	function __construct($message){
		$this->message=strval($message);
	}
}

class DmpDbNotSet extends DmpDbError {};

class DmpDb {
	public $status=0;
	private $db;
	
    function __construct() {
		global $config;
		if (isset($config["db.sqliteFile"])) {
			if (file_exists($config["db.sqliteFile"])) {
				# sqlite db exists, so no need to create it
				$this->db=new SQLite3($config["db.sqliteFile"],SQLITE3_OPEN_READWRITE);	
				//if ($config["DEBUG"]=="FULL_DEBUG") print "DB OPENED\n" ;
				 $i= $this->db->querySingle("SELECT name FROM sqlite_master WHERE type='table' AND name='attachments'");
				 if (!$i){
					 $this->createSchema();
				 }
			} else {
				# sqlite db doesn't exist, we must create it so we run the private schema function
				//if ($config["DEBUG"]=="FULL_DEBUG") print "NEW DB NEEDED\n" ;
				touch($config["db.sqliteFile"]);
				$this->db=new SQLite3($config["db.sqliteFile"],SQLITE3_OPEN_READWRITE);
				
				//if ($config["DEBUG"]=="FULL_DEBUG") print "NEW DB CREATED\n" ;
				
				$this->createSchema();
				
				//if ($config["DEBUG"]=="FULL_DEBUG") print "NEW DB LOADED\n" ;
				$this->status="NEED_INIT";
			}
        } else { 
			trigger_error("db.sqliteFile not defined");
		}
    }
    
    private function createSchema(){
		global $config;
		$propertiesSchema='
CREATE TABLE IF NOT EXISTS properties (pID integer PRIMARY KEY, sID text, name text, content text, crypt integer);

CREATE INDEX IF NOT EXISTS idx_sID ON properties (sID);
CREATE INDEX IF NOT EXISTS idx_name ON properties (name);
';

$contentSchema='
CREATE TABLE IF NOT EXISTS content ( cID integer PRIMARY KEY, type text, content text, start text, end text, crypt integer);

CREATE INDEX IF NOT EXISTS idx_type ON content (type);
CREATE INDEX IF NOT EXISTS idx_start ON content (start);
';

$attachmentsSchema='
CREATE TABLE IF NOT EXISTS attachments ( aID integer PRIMARY KEY, sID text, content text, mimeType text, cryptkey text,heap blob);

CREATE INDEX IF NOT EXISTS idx_sID ON attachments (sID);
';
	if(!$this->db->exec($propertiesSchema)) die ("error creating properties");
	if(!$this->db->exec($contentSchema)) die ("error creating content");
	if(!$this->db->exec($attachmentsSchema)) die ("error creating attachments");

	if ($config["DEBUG"]=="FULL_DEBUG") print "DB POPULATED\n" ;
}

	public function dumpTable($table){
		$dbQuery = 'SELECT * FROM '.$table;
		$stmt = $this->db->prepare($dbQuery);
		#$stmt->bindValue(':table', $table);
		$result=$stmt->execute();
		print "DUMP $table : ";
		while ($x=$result->fetchArray()){
			var_dump($x);
		}
	}
	
	public function getProperty($name,$sID=NULL) {
		if (is_null($name) && is_null($sID)) {
			#TODO
		} else {
			$dbQuery = 'SELECT * FROM properties WHERE name = :name AND sID =:sID';
			$stmt = $this->db->prepare($dbQuery);
			$stmt->bindValue(':sID', $sID);
			$stmt->bindValue(':name',$name);
			
			$result = $stmt->execute();
			$res=$result->fetchArray();
			if ($res===FALSE) {return new DmpDbNotSet;} else {return $res["content"];}
		}
		
	}

	public function getProperties($sID){
		if (is_null($sID)){
			$dbQuery = 'SELECT * FROM properties';
			$stmt = $this->db->prepare($dbQuery);
		} else {
			$dbQuery = 'SELECT * FROM properties WHERE sID =:sID';
			$stmt = $this->db->prepare($dbQuery);
			$stmt->bindValue(':sID', $sID);
		}
		$result = $stmt->execute();
		$res=array();
		while ($r=$result->fetchArray()){
			$res[]=$r; 
		}
		return $res;
	}

	public function setProperty($name,$sID=NULL,$content=""){
		if (is_null($name)) die("Cannot update property without name");
		if (is_null($sID)) { $sID="_";};
		
			$oldVal=$this->getProperty($name,$sID);
			if ($content==$oldVal){
				// print 'UPDATE NOT NEEDED';
			} elseif (!is_a($oldVal,"DmpDbNotSet")) {
				// print 'UPDATE NEEDED';
				$stmt = $this->db->prepare('UPDATE properties SET content=:content WHERE name=:name AND sID=:sID');
				$stmt->bindValue(':sID', $sID);	
				$stmt->bindValue(':name', $name);	
				$stmt->bindValue(':content', $content);	
				$result=$stmt->execute();
			} else {
				// print 'INSERT INTO NEEDED';
				$stmt = $this->db->prepare('INSERT INTO properties (name, sID,content) VALUES (:name,:sID,:content)');
				$stmt->bindValue(':sID', $sID);	
				$stmt->bindValue(':name', $name);	
				$stmt->bindValue(':content', $content);	
				$result=$stmt->execute();
			}
			
			
		}
	
	public function addAttachment($content,$mimeType,$sID, $cryptkey,$heap) {
			if (is_null($sID)) { $sID="_";};
			$stmt = $this->db->prepare('INSERT INTO attachments (content, mimeType, sID,cryptkey,heap) VALUES (:content, :mimeType,:sID,:cryptkey,:heap)');
			$stmt->bindValue(':sID', $sID);	
			$stmt->bindValue(':mimeType', $mimeType);	
			$stmt->bindValue(':content', $content);
			$stmt->bindValue(':cryptkey', $cryptkey);
			$stmt->bindValue(':heap', $heap,SQLITE3_BLOB);
			$result=$stmt->execute();
			if ($this->db->changes()>0) {
				return $this->db->lastInsertRowID(); 
			} else {
				return NULL;
			}
	}

	public function deleteAttachment($id){
		$dbQuery='DELETE FROM attachments WHERE aid=:id';
		$stmt = $this->db->prepare($dbQuery);
		$stmt->bindValue(':id', $id);
		$result=$stmt->execute();

	}

	public function getAttachment($id){
		//print "GET : $id"; 
		$dbQuery = 'SELECT * FROM attachments WHERE aID = :id';
		$stmt = $this->db->prepare($dbQuery);
		$stmt->bindValue(':id', $id);
		
		$result = $stmt->execute();
		$res=$result->fetchArray();
		//var_dump($res);
		return $res;
	}
	
	public function linkAttachment($id,$sid){
		$stmt = $this->db->prepare('UPDATE attachments SET sID=:sID WHERE aID=:aID');
		$stmt->bindValue(':sID', $sID);	
		$stmt->bindValue(':aID', $id);	
		$result=$stmt->execute();
		if ($this->db->changes()){return $id;} else {return NULL;};
	}
	public function listAttachments(){
		$dbQuery = 'SELECT aID, content, mimeType, sID,cryptkey FROM attachments';
		if ($stmt = $this->db->prepare($dbQuery)){
		
		$result = $stmt->execute();
		$res=array();
		while ($r=$result->fetchArray()){
			$res[]=$r; 
		}
		return $res;
		} else {
			//print $this->db->lastErrorMsg;
			return array();
		}
	}

	public function getMEvent($id){
		$dbQuery = 'SELECT * FROM content WHERE cID = :id';
		$stmt = $this->db->prepare($dbQuery);
		$stmt->bindValue(':id', $id);
		
		$result = $stmt->execute();
		$res=$result->fetchArray();
		//var_dump($res);
		return $res;
	}
	public function addMEvent($type, $content, $start, $end=NULL, $crypt){
$stmt = $this->db->prepare('INSERT INTO content (type , content , start , end , crypt ) VALUES (:type,:content,:start,:end,:crypt)');
				$stmt->bindValue(':type', $type);	
				$stmt->bindValue(':content', $content);	
				$stmt->bindValue(':start', $start);	
				$stmt->bindValue(':end', $end);	
				$stmt->bindValue(':crypt', $crypt);	
				$result=$stmt->execute();	
		return $this->db->lastInsertRowID();
	}
	public function deleteMEvent($id){
		$dbQuery='DELETE FROM content WHERE cID=:id';
		$stmt = $this->db->prepare($dbQuery);
		$stmt->bindValue(':id', $id);
		$result=$stmt->execute();

	}
}
?>