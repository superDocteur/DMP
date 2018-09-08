<?php

if (isset($_GET["aID"])){
	$att=$myDMP->attachments->getByID($_GET["aID"]);
	header("Content-Type: ".$att["mimeType"]);
	print $att["heap"];
} else {
	
};
?>