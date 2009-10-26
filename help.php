<?php

class help
{

	public function help( $args = array(), $bot)
	{
		if ( !count($args) )
		{
			//msg returnDest default help msg
			return;
		}

		if ( count($args) == 1 )
		{
			$method = $args[0];

			//find the class that matches the method
			$object = new $objectname();
			$object->_help( $bot );
		}
	}
	
}

?>