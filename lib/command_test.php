<?php 

$myDMP->patient->setName("Fred");
$myDMP->patient->setSurname("N");
$myDMP->patient->setBirthName("BN");
$myDMP->patient->setBirthDate("2018");
// $att=$myDMP->attachments->getByID($id);print "[".$att->content;

// $id=$myDMP->attachments->add("CNI","image/jpeg","CNI",NULL,file_get_contents("./Untitled-2.jpg"));
// $att=$myDMP->attachments->getByID($id);print "...".$att->content;

//$myDMP->attachments->delete($id);
//$att=$myDMP->attachments->getByID($id);print "...".$att->content."]<br/>";
//$atts=$myDMP->attachments->list();

//var_dump($atts);

/*$atcd=$myDMP->advanceDirectives->set("maintain_InUnrecoverableComa",TRUE);
$db->dumpTable("properties");
$myDMP->medicalHistory->delete($atcd);
$db->dumpTable("content");
*/

var_dump($security);
print "$key : isadmin = ".$security->isAdmin()." / isAllowed =".$security->isAllowed();

?><a href="<?php print $_SERVER["PHP_SELF"]?>?command=TEST">TEST</a>