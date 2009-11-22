<?php

/**
 *
 * @author: Gabriel
 * Description:
 *
 */
class modules
{
	private $methodMap;
	private $modules;

	function __construct ()
	{
		$this->modules = array();
	}

	/**
	 *
	 * @param unknown_type $method
	 */
	public function _findClassByMethod ($bot, $method)
	{
		var_dump($method);
		//var_dump($this->methodMap);
		$methodMap = $bot->_getMethodmap();
		return $methodMap[$method];
	}

	/**
	 *
	 */
	public function _loadRequirements ($bot)
	{
		$modules = array();
		$curDir = getcwd();
		$dirList = scandir($curDir);
		$i = 0;
		foreach ($dirList as $file)
		{
			echo "$file\r\n";
			if (preg_match("/(.*)\.php/", $file, $matches) && $file != "modules.php" && $file != "bot.php")
			{
				$modules[] = $matches[1];
				require ($file);
				$i ++;
			}
		}
		$modules[] = get_class($this);
		$bot->_setModules($modules);
		$this->_loadMethodMap($bot);
		return $i;
	}

	/**
	 *
	 * @param unknown_type $modulename
	 */
	public function _loadModule ($bot, $modulename)
	{
		$success = include ($modulename . ".php");
		if ($success)
		{
			$modules = $bot->_getModules;
			$modules[] = $modulename;
			$bot->_setModules($modules);
		}
		return $success;
	}

	/**
	 *
	 * @param unknown_type $bot
	 *
	 */
	public function _loadMethodMap ($bot)
	{
		$methodMap = array();
		foreach ($bot->_getModules() as $module)
		{
			if (! class_exists($module))
				continue;
			$classmethods = get_class_methods($module);
			foreach ($classmethods as $method)
			{
				if ($method[0] == "_" && $method[1] == "_")
					continue;
				if (array_key_exists($method, $methodMap))
				{
					die("Multiple modules with the same method $module, " . $methodMap[$method] . ", $method\r\n");
				}
				else
				{
					$methodMap[$method] = $module;
				}
			}
		}
		$bot->_setMethodmap($methodMap);
		return true;
	}

	/**
	 *
	 * @param unknown_type $bot
	 * @return string
	 *
	 */
	public function commands ($bot)
	{
		$publiccommands = array();
		foreach ($bot->_getMethodmap() as $command => $module)
		{
			if ($command[0] != "_")
				$publiccommands[] = $command;
		}
		return "Commands available to you are " . implode(", ", $publiccommands);
	}
}
