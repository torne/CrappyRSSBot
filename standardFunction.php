<?php
function handle_PING($bot)
{
	$explodedData = explode(" ", $bot->getData() );
	$bot->putToServer("PONG ".$explodedData[1]."\r\n");
}

function handle_254( $bot )
{
	foreach( $bot->doConfigStuff( 'getConfig', array('channels') ) as $channel )
	{
		$bot->putToServer("JOIN $channel\r\n");
	}
}

function handle_433( $bot )
{
	
}

function handle_PRIVMSG( $bot )
{
	//:Gabriel!~gabriel@Gabriel.users.netgamers.org PRIVMSG #wearelegion :poke
	$explodedData = explode(" ", $bot->getData() );
	$bot->putToServer("PONG ".$explodedData[1]."\r\n");
	preg_match("/(.+)(?:!~|!)(.+)@(.+) PRIVMSG (.+) :(.+)/", $bot->getData(), $matches);
	$nick = $matches[1];
	$user = $matches[2];
	$hostmask = $matches[3];
	$chanOrBot = $matches[4];
	
	$message = $matches[5];
	$messageArray = explode(' ', $message);
	
	if ( $message[0] == "!" )
		if ( $messageArray[0] == "!quit" )
			$bot->putToServer('QUIT', 'Crappily made bot');
		else if ( $messageArray[0] == "!reload" )
			$bot->loadRequirements();
		else if ( $messageArray[0] == "!test" )
			$bot->putToServer("PRIVMSG $chanOrBot :lemons");
}