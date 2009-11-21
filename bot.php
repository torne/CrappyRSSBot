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
	private $rss;
	private $rss_time;
	private $modules;
	private $methodMap;

	/**
	 *
	 *
	 *
	 */
	function __construct()
	{
		//set_include_path(get_include_path() . PATH_SEPARATOR . '/Users/gabriel/Zend/workspaces/DefaultWorkspace7/Crappy RSS Bot/CrappyRSSBot');
		include('modules.php');
		$this->modules = new modules();
		$this->modules->_loadRequirements($this);
		$this->_initialise();
	}

	/**
	 *
	 */
	public function _initialise()
	{
		$this->config = new config();
		$this->config->_loadConfig();
		$this->logger = new botLogger();
		$this->handle_functions = new handle_functions();
		$this->rss = new RSSFunctions();
		$this->rss_time = time();
	}

	function _getModules()
	{
		return $this->modules;
	}

	function _setModules($modules)
	{
		$this->modules = $modules;
	}

	function _getMethodmap()
	{
		return $this->methodMap;
	}

	function _setMethodmap( $methodMap )
	{
		$this->methodMap = $methodMap;
	}

	/**
	 *
	 */
	public function _getConfig( $name )
	{
		//print_r( $this->config );
		return $this->config->_getConfig( $name );
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
		$this->socket = @fsockopen( $this->config->_getConfig('server'), $this->config->_getConfig('port'));
		if ( !$this->socket )
			die("Unable to connect to server\r\n");
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
		if ( $this->rss_time+120 < time() )
		{
			//do rss stuff

		}

		$explodedData = explode(" ", $this->data );
		if ( $this->data[0] == ":" )
		{
			$this->data = substr($this->data, 1);
			if ( method_exists( $this->handle_functions, "_handle_" . $explodedData[1]) )
			{
				$return = call_user_func( array($this->handle_functions, "_handle_".$explodedData[1]), $this);
				if ( preg_match("/reload (.+) (.+)/", $return, $matches) )
				{
					$returnDest = $matches[1];
					$filename = $matches[2];
					$reload = new classReloader();
					$this->_sendMsg( $returnDest, $reload->reload( $this, $filename) );
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

	/**
	 *
	 * @param String $destination
	 * @param String $message
	 */
	public function _sendMsg( $destination, $message )
	{
		$this->_putToServer( "PRIVMSG $destination :$message");
	}

	/**
	 *
	 * @param String $string
	 */
	public function _putToServer( $string )
	{
		echo "<========\t\t$string\r\n";
		fputs($this->socket, "$string\r\n");
	}

	/**
	 *
	 * @param String $chan
	 */
	public function _joinChan( $chan )
	{
		$this->_putToServer("JOIN $chan\r\n");
	}

	/**
	 *
	 */
	public function _joinChans()
	{
		foreach( $this->config->_getChans() as $channel )
		{
			$this->_joinChan($channel);
		}

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