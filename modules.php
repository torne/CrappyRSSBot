<?php
/**
 * 
 * @author: Gabriel
 * Description: 
 * 
 */ 
 
 class modules
 {

	public function findClassByMethod( $method )
	{
		//we know what files are loaded by us
		//so we assume that each class is the name of its file
		//so we test each filename to see if it's also a class name
		//then we get each method from that class and see if that's the one we want
		if ( $method[0] == "_" )
			return;
		$loadedModules = $this->modules();
		foreach ( $loadedModules as $module )
		{
			if ( !class_exists($module) )
				continue;
			$methods = get_class_methods($module);
			if ( !in_array($method, $methods) )
				continue;
			//if we're still here we've found the class with the right method
		}
	}
 
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
	
 	public function modules(  )
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
