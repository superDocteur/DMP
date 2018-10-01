<?php

require $config["path.lib"].'commands.class.php';

$commands=new Commands;

//TEST is to be removed
$commands->add("TEST","command_test.php",Commands::NEED_TEMPLATE);

//SPLASH is the splash screen asking for the site admin|viewing password
// Status : Logic OK, Template NEED UPDATE
$commands->add("SPLASH","command_splash.php",Commands::NEED_TEMPLATE);

//LOGIN and LOGOUT just clear the password key
// Status : Logic OK
$commands->add("LOGIN","command_login.php");
$commands->add("LOGOUT","command_logout.php");

$commands->add("editPasswd","command_editPasswd.php",Commands::NEED_TEMPLATE|Commands::IS_ADMIN);

//HOME is the main landing page once logged in
// Status : Logic :
//				Not logged in : OK
//				Logged as Admin : TODO
//				Logged as viewer : TODO
//			Template : NEED UPDATE
$commands->add("HOME","command_home.php",Commands::NEED_TEMPLATE);

//editPerson allows to change basic personal info (ID mainly...)
// Status : Logic PENDING, Template TODO
$commands->add("editPerson","command_editPersonalData.php",Commands::NEED_TEMPLATE|Commands::IS_ADMIN);

//editTrustedPerson allows to change basic non medical fields (ICE Contact Person...)
// Status : Logic PENDING, Template TODO
$commands->add("editTrustedPerson","command_editTrustedPerson.php",Commands::NEED_TEMPLATE|Commands::IS_ADMIN);

//editAd is for changing the Advance Directives
// Status : Logic OK, Template NEED UPDATE
$commands->add("editAd","command_ad.php",Commands::NEED_TEMPLATE|Commands::IS_ADMIN);

//viewAd is for view the Advance Directives
// Status : Logic TODO, Template TODO
$commands->add("viewAd","command_viewAd.php",Commands::NEED_TEMPLATE|Commands::IS_ADMIN);

//editAttachments
// Status : Logic TODO, Template TODO
$commands->add("editAttachments","command_attachments.php",Commands::IS_ADMIN|Commands::NEED_TEMPLATE);

//editAttachment permits to modify the imported documents
// Status : Logic TODO, Template TODO
$commands->add("editAttachment","command_attachment.php");

//viewAttachment allows the downloading of an attachment file
// Status : Logic TODO
$commands->add("viewAttachment","command_viewAttachment.php",Commands::IS_ALLOWED);

//qrCode generates the qrCodes for direct access to the viewing or admin part of the site
//If you change the viewing password, it changes so keep it in mind if you print it
// Status : Logic OK
$commands->add("qrCode","command_qrCode.php",Commands::IS_ADMIN);

// Status : Logic TODO, Template TODO
$commands->add("EditME","command_editME.php",Commands::IS_ADMIN|Commands::NEED_TEMPLATE);

?>