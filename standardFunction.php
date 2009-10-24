<?php

/**
 * 
 * @param $bot
 */
function handle_PING( $bot )
{
	$explodedData = explode(" ", $bot->getData() );
	$bot->putToServer("PONG ".$explodedData[1]."\r\n");
}

/**
 * 
 * @param unknown_type $bot
 */
function handle_254( $bot )
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
function handle_433( $bot )
{
	
}