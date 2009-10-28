<?php
$bot = new bot();
ob_implicit_flush(TRUE); 
//echo "I have loaded ".$bot->countLoadedModules." modules. Loaded modules are: ".$bot->listLoadedModules()."\r\n";
//echo $bot ->doLoggerStuff('log', array('test', 'some text'))."\r\n";
$bot->_main();

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
	private $methods;
	
	/**
	 * 
	 */
	function __construct()
	{
		include('modules.php');
		$modules = new modules();
		$modules->_loadRequirements();
		$this->_initialise();
		$this->config->_loadConfig();
	}

	/**
	 * 
	 */
	public function _initialise()
	{
		$this->config = new config();
		$this->logger = new botLogger();
		$this->handle_functions = new handle_functions();
		$modules = new modules();
		$this->methods = $modules->_getMethods();
	}

	public function _setMethods( $methods )
	{
		$this->methods = $methods;
	}

	/**
	 * 
	 * @param $thingToGet
	 */
	public function _getConfig( $thingToGet )
	{
		return $this->config->_getConfig( $thingToGet );
	}
	
	/**
	 * 
	 */
	public function _main()
	{
		$this->_server();
		while ( !feof($this->socket) )
		{
			$this->_getFromServer();
			$this->_parseInput();
		}
	}
	
	/**
	 * 
	 */
	public function _server()
	{
		$this->socket = fsockopen( $this->config->_getConfig('server'), $this->config->_getConfig('port'));
		fputs($this->socket,"USER ".$this->config->_getConfig('user')." :".$this->config->_getConfig('nick')."\r\n");
		fputs($this->socket,"NICK ".$this->config->_getConfig('nick')."\r\n");
	}

	/**
	 * 
	 */
	public function _getFromServer()
	{
		$this->data = trim( fgets( $this->socket ) );
		echo "========>\t\t".$this->data."\r\n";
		return $this->data;
	}
	
	/**
	 * 
	 */
	public function _parseInput()
	{
		if ( !$this->data )
			return;
		$explodedData = explode(" ", $this->data );
		if ( $this->data[0] == ":" )
		{
			$this->data = substr($this->data, 1);
			if ( method_exists( $this->handle_functions, "_handle_" . $explodedData[1]) )
			{
				$return = call_user_func( array($this->handle_functions, "_handle_".$explodedData[1]), $this);
				if ( preg_match("/reload (.+) (.+)/", $return, $matches) )
				{
					$filename = $matches[2];
					$returnDest = $matches[1];
					$reload = new classReloader();
					$this->_sendMsg($returnDest, $reload->reload($filename) );
				}
			}
		}
		else
		{
			if ( method_exists( $this->handle_functions, "_handle_" . $explodedData[0]) )
			{
				call_user_func( array($this->handle_functions, "_handle_".$explodedData[0]), $this);
			}
		}
	}
	
	public function _sendMsg( $destination, $message )
	{
		$this->putToServer( "PRIVMSG $destination :$message");
	}
	
	/**
	 * 
	 * @param unknown_type $string
	 */
	public function _putToServer( $string )
	{
		echo "<========\t\t$string\r\n";
		fputs($this->socket, "$string\r\n");
	}
	
	/**
	 * 
	 */
	public function _getData()
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