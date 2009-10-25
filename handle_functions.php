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
		$messageArray = explode(' ', $message);
	
		switch ( strtolower($messageArray[0]) )
		{
			case "quit":
				$quitmessage = 'Crappily made bot';
				if ( count($messageArray) > 1 )
					$quitmessage = implode( ' ', array_slice($messageArray, 1));
				$bot->quit( $quitmessage );
				break;
			case "lemons":
				$bot->sendMsg($returnDest, "or the coconuts!" );
				break;
			case "reload":
				$reload = new classReloader();
				$bot->sendMsg($returnDest, $reload->reload($messageArray[1]) );
				break;
		}
	}
	
	/**
	 * 
	 * @param $bot
	 */
	public function handle_PING( $bot )
	{
		$explodedData = explode(" ", $bot->getData() );
		$bot->putToServer("PONG ".$explodedData[1]."\r\n");
	}
	
	/**
	 * 
	 * @param unknown_type $bot
	 */
	public function handle_254( $bot )
	{
		foreach( $bot->doConfigStuff( 'getConfig', array('channels') ) as $channel )
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