<?php
/**
 * 
 * @param unknown_type $bot
 */
function handle_PRIVMSG( $bot )
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
		case "reload":
			$bot->loadRequirements();
			break;
		case "lemons":
			$bot->sendMsg( $returnDest, 'limes');
			break;
		case "redeclare":
			rename_function("handle_privmsg", "handle_privmsg_old");
			require("handle_PRIVMSG.php");
			break;
	}
}
