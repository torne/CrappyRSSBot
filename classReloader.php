<?php

class classReloader
{

	/**
	 *
	 */
	function __construct()
	{

	}

	/**
	 *
	 * @param unknown_type $bot
	 * @param unknown_type $module
	 */
	public function reload( $bot, $module)
	{
		$filename = $module.".php";
		if ( !is_file( $filename ) )
		{
			return "$filename is not a file.";
		}

		/*
		 if ( !runkit_lint_file($filename) )
		 {
			return "$filename failed to pass syntax checking.";
		}
		*/

		if ( runkit_import($filename) )
		{
			$modules = new modules();
			$modules->_loadMethodMap($bot);
			return "$filename reloaded.";
		}
		else
		{
			return "$filename failed to reload.";
		}
	}

}

?>