<?php
##### sqlite3 db
# Ne gère pas le cryptage

class DmpDbError {
	public $message;
	
	function __construct($message=""){
		$this->message=strval($message);
	}
}

class DmpDbNotSet extends DmpDbError {};

class DmpDb {
	public $status=0;
	private $db;
	public $decifyer; // should be private
	
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
				#I don't know why I was unable to SQLITE3_OPEN_CREATE the file. So I took the ugly way to success.
				
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
			# We die since we can't really do anything without the database...
			die("db.sqliteFile not defined");
		}
    }
    
    private function createSchema(){
		global $config;
		$propertiesSchema='
CREATE TABLE IF NOT EXISTS properties (pID integer PRIMARY KEY, sID text, name text, content text, crypt text);

CREATE INDEX IF NOT EXISTS idx_sID ON properties (sID);
CREATE INDEX IF NOT EXISTS idx_name ON properties (name);
';

$contentSchema='
CREATE TABLE IF NOT EXISTS content ( cID integer PRIMARY KEY, type text, content text, start text, end text, crypt text);

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
// Typo : change to deciPHyer everywhere
	public function addDecifyer(&$sec){
		if (is_a($sec,"Deciphyer")) {
			$this->decifyer=$sec;
		return TRUE;
		} else {print "SOMETHING WENT WRONG\n"; print_r($sec);}
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
	
	public function getProperty($name,$sID=NULL,$key=NULL) {
		if (is_null($name) && is_null($sID)) {
			#TODO, probably not necessary
			return new DmpDbNotSet;
		} else {
			$dbQuery = 'SELECT * FROM properties WHERE name = :name AND sID =:sID';
			$stmt = $this->db->prepare($dbQuery);
			$stmt->bindValue(':sID', $sID);
			$stmt->bindValue(':name',$name);
			
			$result = $stmt->execute();
			$res=$result->fetchArray();
			if ($res===FALSE) {
				return new DmpDbNotSet("No row by this name :".$sID."::".$name);
			} else {
				if ($res["crypt"]){
					if(is_a($key,"Deciphyer")){
						$decrypted=$key->decrypt($res["content"],$res["crypt"]);
					} else if (is_a($this->decifyer,"Deciphyer")) {
						$decrypted=$this->decifyer->decrypt($res["content"],$res["crypt"]);
					} else {
						return new DmpDbNotSet("No deciphyer found ".$key."[".get_class($key)."]");
					}
					if ($decrypted) {
						return $decrypted;
					} else {
						return new DmpDbNotSet ("SSL decryption error");
					}
				} else {
					return $res["content"];
				}
			}
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

	public function setProperty($name,$sID=NULL,$content="",$encrypt=NULL){
	/*	INPUT
	*		$name				= property name [string]
	*		$sID				= pointed ID (may be a class, or a recordPointer (PROP:000 or ATT:000 etc) [string]
	*		$content			= clear text [string] 
	*		$encrypt			= initialized deciphyer object || NULL || TRUE
									if $encrypt is TRUE, $this->decifyer will be used instead
									if $encrypt is NULL, no encryption will be used
	*/
	
		if (is_null($name)) die("Cannot update property without name");
		if (is_null($sID)) { $sID="_";};
		if (is_null($encrypt)){
			//No encryption
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
			return TRUE;
		} else if ($encrypt===TRUE){
			//Use the default decifyer
			$oldVal=$this->getProperty($name,$sID);
			$encrypted=$this->decifyer->encrypt($content);
			print "ENCRYPTED : ".$encrypted["encrypted"]." [[[ ".$encrypted["cryptinit"]." ]]]";
			if ($content==$oldVal){
				print 'UPDATE NOT NEEDED';
			} elseif (!is_a($oldVal,"DmpDbNotSet")) {
				// print 'UPDATE NEEDED';
				$stmt = $this->db->prepare('UPDATE properties SET content=:content, crypt=:crypt WHERE name=:name AND sID=:sID');
				$stmt->bindValue(':sID', $sID);	
				$stmt->bindValue(':name', $name);	
				$stmt->bindValue(':content', $encrypted["encrypted"]);	
				$stmt->bindValue(':crypt', $encrypted["cryptinit"]);	
				$result=$stmt->execute();
			} else {
				// print 'INSERT INTO NEEDED';
				$stmt = $this->db->prepare('INSERT INTO properties (name, sID,content,crypt) VALUES (:name,:sID,:content,:crypt)');
				$stmt->bindValue(':sID', $sID);	
				$stmt->bindValue(':name', $name);	
				$stmt->bindValue(':content', $content);	
				$stmt->bindValue(':content', $encrypted["encrypted"]);	
				$stmt->bindValue(':crypt', $encrypted["cryptinit"]);	
				$result=$stmt->execute();
			}
			return TRUE;
		} else if (is_a($encrypt,"Deciphyer")){
			//Use this deciphyer
			$oldVal=$this->getProperty($name,$sID,$encrypt);
			$encrypted=$encrypt->encrypt($content);
			print "ENCRYPTED : ".$encrypted["encrypted"]." [[[ ".$encrypted["cryptinit"]." ]]]";
			if ($content==$oldVal){
				print 'UPDATE NOT NEEDED';
			} elseif (!is_a($oldVal,"DmpDbNotSet")) {
				 print "UPDATE NEEDED $oldval [".get_class($oldVal)."]\n";
				$stmt = $this->db->prepare('UPDATE properties SET content=:content, crypt=:crypt WHERE name=:name AND sID=:sID');
				$stmt->bindValue(':sID', $sID);	
				$stmt->bindValue(':name', $name);	
				$stmt->bindValue(':content', $encrypted["encrypted"]);	
				$stmt->bindValue(':crypt', $encrypted["cryptinit"]);	
				$result=$stmt->execute();
			} else {
				
				print "INSERT INTO NEEDED $oldval [".$oldVal->message."]\n";
				$stmt = $this->db->prepare('INSERT INTO properties (name, sID,content,crypt) VALUES (:name,:sID,:content,:crypt)');
				$stmt->bindValue(':sID', $sID);	
				$stmt->bindValue(':name', $name);	
				$stmt->bindValue(':content', $content);	
				$stmt->bindValue(':content', $encrypted["encrypted"]);	
				$stmt->bindValue(':crypt', $encrypted["cryptinit"]);	
				$result=$stmt->execute();
			}
			return TRUE;
		} else {
			die("Encryption parameter not valid in setProperty :".type($encrypt));
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
	public function updateAttachment($aID,$content,$mimeType,$sID, $cryptkey,$heap) {
			if (is_null($sID)) { $sID="_";};
			$stmt = $this->db->prepare('UPDATE  attachments SET content=:content, mimeType=:mimeType, sID=:sID, cryptkey=:cryptkey, heap=:heap WHERE aID=:aID');
			$stmt->bindValue(':aID', $aID);	
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

	public function listAttachmentsBySid($sId){
		$dbQuery = 'SELECT aID, content, mimeType, sID,cryptkey FROM attachments WHERE sID=:sID';
		if ($stmt = $this->db->prepare($dbQuery)){
			$stmt->bindValue(":sID",$sId);
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