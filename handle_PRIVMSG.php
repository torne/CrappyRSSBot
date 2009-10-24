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
			echo "instanciate\r\n";
			$rename = new privmsg();
			echo "method call\r\n";
			$rename->retest();
			echo "sleep\r\n";
			sleep(10);
			echo "runkit_import\r\n";
			runkit_import("privmsg.php");
			echo "method call with new import\r\n";
			$rename->retest();
			break;
	}
}
