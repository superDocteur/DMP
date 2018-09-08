<?php

/* This is the schema of the DB. This file doesn't do anything it is just for reference.
 * It must be adapted to the DBEngine used

DB content :

Table : properties
	-P pID (BIGINT)
	-k sID (VARCHAR 50) : PAT|*
	-k name (VARCHAR 50)
	-  content (TEXT)
	-  crypt (BOOL)

Table : content
	-P cID (BIGINT)
	-k type (VARCHAR 6) : DRUG|ALLER|MHSTRY|FHSTRY|SURGRY|BTRANS
	-  content (TEXT)
	-k start (VARCHAR 8)
	-  end (VARCHAR 8)
	-  crypt (BOOL)

Table : attachments
	-P aID (BIGINT)
	-k sID (VARCHAR 50)
	-  content (VARCHAR 250)
	-  mimeType (VARCHAR 50)
	-  cryptkey (TEXT)
	-  heap (BLOB)
*/

?>