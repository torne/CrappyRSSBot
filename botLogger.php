<?php

/**
 *
 * Logging class for the bot
 * @author gabriel
 *
 */
class botLogger
{

	/**
	 *
	 *
	 *
	 */
	function __construct()
	{

	}

	/**
	 *
	 * Atempts to append a line to the file logs/channelName
	 * if successful returns the added line else returns the error
	 *
	 * @param string $name
	 * @param string $line
	 *
	 * @return string
	 */
	function _log( $name, $line )
	{
		$filename = "logs/$name";
		if ( !is_dir("logs") )
		{
			if ( !mkdir("logs") )
			return "Error, logs dir isn't a dir and couldn't make it.";
		}

		if ( !is_writeable("logs") )
		{
			return "Error, logs dir isn't writeable.";
		}

		if ( file_exists($filename) )
		{
			if ( !is_writeable($filename) )
			{
				return "Error, file not writeable.";
			}
		}

		$logFile = fopen($filename, "a+");
		if ( !$logFile )
		{
			return "Error, could not open file.";
		}

		$time = date('d/m/Y-H:i');
		if ( !fwrite($logFile, "$time - $line\r\n") )
		{
			return "Error, couldn't write to file.";
		}
		return "$time - $line\r\n";
	}
}

?>