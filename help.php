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
			$objectname = $args[0];

			if ( !class_exists($objectname) )
				return;

			$object = new $objectname();
			$object->_help( $bot );
		}
	}
	
}

?>