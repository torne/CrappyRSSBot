<?php
echo "I loaded ".loadRequirements()." modules.\r\n";
$config = new config();
$config->loadConfig();
echo "Loaded modules are: ".listLoadedFiles()."\r\n";
$logger = new botLogger();
echo $logger->log('test', 'some text')."\r\n";

function listLoadedFiles()
{
	$var = get_included_files();
	$loadedFiles = array();
	foreach ( $var as $file )
	{
		$tokens = explode("/", $file);
		$loadedFiles[] = end($tokens);
	}
	return implode(', ', $loadedFiles);
}

function loadRequirements()
{
	$curDir = getcwd();
	$dirList = scandir($curDir);
	$i=0;
	foreach( $dirList as $file )
	{
		if ( preg_match("/.*\.php/", $file) && $file != "bot.php" )
		{
			require($file);
			$i++;
		}
	}
	return $i;
}