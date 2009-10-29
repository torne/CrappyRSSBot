<?php
/**
 * 
 * @author: Gabriel
 * Description: 
 * 
 */ 
 
 class modules
 {

	function __construct()
	{
	}

	/**
	 * 
	 * @param unknown_type $method
	 */
 	public function _findClassByMethod( $method )
	{
	}
 
	/**
	 * 
	 * @param unknown_type $modulename
	 * @param unknown_type $bot
	 */
	public function _loadModule( $modulename, $bot )
 	{
 		$success = include($modulename.".php");
 		return $success;
 	}

 	/**
 	 * 
 	 */
 	public function _loadRequirements()
	{
		$curDir = getcwd();
		$dirList = scandir($curDir);
		$i=0;
		foreach( $dirList as $file )
		{
			if ( preg_match("/.*\.php/", $file) )
			{
				include($file);
				$i++;
			}
		}
		return $i;
	}
	
	/**
	 * 
	 */
	public function _modules(  )
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

	public function _getMethods()
	{
		$methods = array();
		$loadedModules = $this->_modules();
		foreach ( $loadedModules as $module )
		{
			if ( !class_exists($module) )
				continue;
			$classmethods = get_class_methods($module);
			foreach( $classmethods as $method )
			{
				if ( array_key_exists($method, $classmethods) )
				{
					die("Multiple modules with the same method $module, ".$methods[$method]);
				}
				else
				{
					$methods[$method] = $module;
				}
			}
		}
		return $methods;
	}

	public function commands( $bot )
	{
		$publiccommands = array();
		foreach ( $bot->getCommands as $command )
		{
			if ( $command[0] != "_" )
				$publiccommands[] = $command;
		}
		return "Commands available to you are ".implode(", ", $publiccomands);
	}

 }
