<?php

unset($_SESSION["key"]);
header("Location: ".$app["baseUrl"]."?command=".$config["action.default"]);
?>