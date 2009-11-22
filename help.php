<?php

class help
{

	/**
	 *
	 * @param $bot
	 * @param $method
	 */
	public function help( $bot, $method = null)
	{
		if ( !$method )
		{
			return "A default message";
		}

		$modules = new modules();
		$objectname = $modules->_findClassByMethod( $bot, "_help_".$method );
		if ( !$objectname )
		return "No help for command $method.";


		//find the class that matches the method
		$object = new $objectname();
		return call_user_func( array($object, "_help_".$method) );
	}

}

?>