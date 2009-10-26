<?php
/**
 * @version: xxx - Created on: 26 Oct 2009 - Filename: 
 * @author: Gabriel
 * Description: 
 * 
 */ 
 
 class modules
 {
 
 	public function loadModule( $modulename, $bot )
 	{
 		$success = include($modulename.".php");
 		return $success;
 	}
 	
 	public function loadRequirements()
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
	
 	public function modules( $bot )
 	{
 		$var = get_included_files();
		$modules = array();
		foreach ( $var as $file )
		{
			$tokens = explode("/", $file);
			$module = explode( ".", end($tokens));
			$modules[] = $module[0];
		}
		return $modules;
 	}
 }
