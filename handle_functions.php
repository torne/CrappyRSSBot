<?php

class handle_functions
{
	
	function __construct()
	{
	
	}
	
	/**
	 * 
	 * @param unknown_type $bot
	 */
	public function _handle_PRIVMSG( $bot )
	{
		$explodedData = explode(" ", $bot->_getData() );
		if ( !preg_match("/(.+)(?:!~|!)(.+)@(.+) PRIVMSG (.+) :(!|.|\+|-)(.+)/", $bot->_getData(), $matches) )
		{
			return;
		}
	
		$nick = $matches[1];
		$user = $matches[2];
		$hostmask = $matches[3];
		$returnDest = $matches[4];
		if ( strcasecmp( $returnDest, $bot->_getConfig('nick')) == 0 )
			$returnDest = $nick;
		$messageType = $matches[5];	
		$message = $matches[6];
		
		$objectname = '';
		$method = '';
		$object = '';

		$args = array();

		$messageArray = explode(' ', $message);
		$method = $messageArray[0];
		$args = array_slice( $messageArray, 1);

		if ( $method[0] == "_" )
			$bot->_sendMsg( $returnDest, 'No such command.');
		
		$modules = new modules();
		$objectname = $modules->findClassByMethod( $method );
		if ( !$objectname )
			$bot->_sendMsg( $returnDest, 'No such command.');

		$object = new $objectname();

		$args[] = $bot;

		$bot->_sendMsg( $returnDest, call_user_func_array( array( $object, $method), $args) );
	}
	
	/**
	 * 
	 * @param $bot
	 */
	public function _handle_PING( $bot )
	{
		$explodedData = explode( " ", $bot->_getData());
		$bot->_putToServer( "PONG ".$explodedData[1]."\r\n");
	}
	
	/**
	 * 
	 * @param unknown_type $bot
	 */
	public function _handle_254( $bot )
	{
		$bot->_joinChans();
	}
	
	/**
	 * 
	 * @param unknown_type $bot
	 */
	public function _handle_433( $bot )
	{
		
	}
}

?>