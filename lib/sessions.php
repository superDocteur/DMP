<?php
###### Session

if (session_start()) {
#	if ($config["DEBUG"]) {print "SESSION STARTED";};
} else {
#	if ($config["DEBUG"]) {print "SESSION DID NOT START";};
};

//if ($config["DEBUG"]=="FULL_DEBUG") print "SESSIONS LOADED\n" ;

?>