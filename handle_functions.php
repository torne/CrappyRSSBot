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
	public function handle_PRIVMSG( $bot )
	{
		$explodedData = explode(" ", $bot->getData() );
		if ( !preg_match("/(.+)(?:!~|!)(.+)@(.+) PRIVMSG (.+) :(!|.|\+|-)(.+)/", $bot->getData(), $matches) )
		{
			return;
		}
	
		$nick = $matches[1];
		$user = $matches[2];
		$hostmask = $matches[3];
		$returnDest = $matches[4];
		if ( strcasecmp( $returnDest, $bot->getConfig('nick')) == 0 )
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
			$bot->sendMsg( $returnDest, 'No such command.');
		
		$modules = new modules();
		$objectname = $modules->findClassByMethod( $method );
		if ( !$objectname )
			$bot->sendMsg( $returnDest, 'No such command.');

		$object = new $objectname();

		if ( !count($args) )
			$args[] = '';
		$args[] = $bot;

		$bot->sendMsg( $returnDest, call_user_func_array( array( $object, $method), $args) );
	}
	
	/**
	 * 
	 * @param $bot
	 */
	public function handle_PING( $bot )
	{
		$explodedData = explode( " ", $bot->getData());
		$bot->putToServer( "PONG ".$explodedData[1]."\r\n");
	}
	
	/**
	 * 
	 * @param unknown_type $bot
	 */
	public function handle_254( $bot )
	{
		foreach( $bot->doConfigStuff( 'getConfig', array('channels')) as $channel )
		{
			$bot->putToServer("JOIN $channel\r\n");
		}
	}
	
	/**
	 * 
	 * @param unknown_type $bot
	 */
	public function handle_433( $bot )
	{
		
	}
}

?>