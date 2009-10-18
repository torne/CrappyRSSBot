<?php
require_once('config.php');
$bot = new config();
echo "I loaded ".$bot->loadRequirements()." modules.";
