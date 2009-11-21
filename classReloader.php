<?php

class classReloader
{

	function __construct()
	{

	}

	public function reload( $bot, $filename)
	{
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