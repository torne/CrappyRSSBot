<?php

class help
{

	public function help( $method = '', $bot)
	{
		if ( !$method )
		{
			//msg returnDest default help msg
			return;
		}

		$modules = new modules();
		$objectname = $modules->findClassByMethod( "_help_".$method );
		if ( !$objectname )
			return "No help for command $method.";


		//find the class that matches the method
		$object = new $objectname();
		return call_user_func( array($object, "_help_".$method) );
	}
	
}

?>