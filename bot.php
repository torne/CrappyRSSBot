<?php
$bot = new bot();
ob_implicit_flush(TRUE); 
//echo "I have loaded ".$bot->countLoadedModules." modules. Loaded modules are: ".$bot->listLoadedModules()."\r\n";
//echo $bot ->doLoggerStuff('log', array('test', 'some text'))."\r\n";
$bot->main();

/**
 * 
 * @author gabriel
 *
 */
class bot
{
	
	private $config;
	private $logger;
	private $socket;
	private $data;
	private $handle_functions;
	private $commands;
	
	/**
	 * 
	 */
	function __construct()
	{
		include('modules.php');
		$modules = new modules();
		$modules->loadRequirements();
		$this->initialise();
		$this->config->loadConfig();
	}

	/**
	 * 
	 */
	public function initialise()
	{
		$this->config = new config();
		$this->logger = new botLogger();
		$this->handle_functions = new handle_functions();
		$this->commands = new modules()->_getCommands();
	}

	/**
	 * 
	 * @param $method
	 * @param $args
	 */
	public function doConfigStuff( $method, $args=array() )
	{
		return call_user_func_array( array($this->config, $method), $args );
	}

	/**
	 * 
	 * @param $thingToGet
	 */
	public function getConfig( $thingToGet )
	{
		return $this->config->getConfig( $thingToGet );
	}

	/**
	 * 
	 */
	public function loadFile( $filename )
	{
		if ( file_exists($filename) )
		{
			require($filename);
			$this->sendMsg();
		}
		else
		{
			$this->sendMsg();
		}
	}

	/**
	 * 
	 */
	public function server()
	{
		$this->socket = fsockopen( $this->config->getConfig('server'), $this->config->getConfig('port'));
		fputs($this->socket,"USER ".$this->config->getConfig('user')." :".$this->config->getConfig('nick')."\r\n");
		fputs($this->socket,"NICK ".$this->config->getConfig('nick')."\r\n");
	}
	
	/**
	 * 
	 */
	public function parseInput()
	{
		if ( !$this->data )
			return;
		$explodedData = explode(" ", $this->data );
		if ( $this->data[0] == ":" )
		{
			$this->data = substr($this->data, 1);
			if ( method_exists( $this->handle_functions, "handle_" . $explodedData[1]) )
			{
				$return = call_user_func( array($this->handle_functions, "handle_".$explodedData[1]), $this);
				if ( preg_match("/reload (.+) (.+)/", $return, $matches) )
				{
					$filename = $matches[2];
					$returnDest = $matches[1];
					$reload = new classReloader();
					$this->sendMsg($returnDest, $reload->reload($filename) );
				}
			}
		}
		else
		{
			if ( method_exists( $this->handle_functions, "handle_" . $explodedData[0]) )
			{
				call_user_func( array($this->handle_functions, "handle_".$explodedData[0]), $this);
			}
		}
	}
	
	/**
	 * 
	 */
	public function main()
	{
		$this->server();
		while ( !feof($this->socket) )
		{
			$this->getFromServer();
			$this->parseInput();
		}
	}
	
	public function sendMsg( $destination, $message )
	{
		$this->putToServer( "PRIVMSG $destination :$message");
	}
	
	/**
	 * 
	 * @param unknown_type $string
	 */
	public function putToServer( $string )
	{
		echo "<========\t\t$string\r\n";
		fputs($this->socket, "$string\r\n");
	}
	
	/**
	 * 
	 */
	public function getFromServer()
	{
		$this->data = trim( fgets( $this->socket ) );
		echo "========>\t\t".$this->data."\r\n";
		return $this->data;
	}
	
	/**
	 * 
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * 
	 * @param unknown_type $message
	 */
	public function quit( $message )
	{
		$this->putToServer( "QUIT :$message" );
	}
	
}