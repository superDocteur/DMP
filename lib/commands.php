<?php

require $config["path.lib"].'commands.class.php';

$commands=new Commands;
$commands->add("TEST","command_test.php",Commands::NEED_TEMPLATE);
$commands->add("SPLASH","command_splash.php",Commands::NEED_TEMPLATE);
$commands->add("LOGIN","command_login.php");

$commands->add("ad","command_ad.php",Commands::NEED_TEMPLATE);

$commands->add("attachment","command_attachment.php");
$commands->add("view_attachment","command_viewAttachment.php");



?>