<?php
/**
 * 
 * @author: Gabriel
 * Description: 
 * 
 */ 
 
 class modules
 {

	/**
	 * 
	 * @param unknown_type $method
	 */
 	public function _findClassByMethod( $method )
	{
		$loadedModules = $this->_modules();
		foreach ( $loadedModules as $module )
		{
			if ( !class_exists($module) )
				continue;
			$methods = get_class_methods($module);
			if ( !in_array($method, $methods) )
				continue;
			return $module;
				//if we're still here we've found the class with the right method
		}
		return false;
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
			if ( preg_match("/.*\.php/", $file) && $file != "bot.php" )
			{
				require($file);
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

 }
