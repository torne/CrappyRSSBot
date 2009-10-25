<?php
$bot = new bot();
ob_implicit_flush(TRUE); 
echo "I have loaded ".$bot->countLoadedModules." modules. Loaded modules are: ".$bot->listLoadedModules()."\r\n";
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
	private $privmsg;
	private $handle_functions;
	
	/**
	 * 
	 */
	function __construct()
	{
		$this->loadRequirements();
		$this->initialise();
		$this->config->loadConfig();
	}

	/**
	 * 
	 */
	public function initialise()
	{
		echo $this->countLoadedModules() . " " . $this->listLoadedModules() . "\r\n";
		$this->config = new config();
		$this->logger = new botLogger();
		$this->privmsg = new privmsg();
		$this->handle_functions = new handle_functions($this);
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
	 * @param unknown_type $method
	 * @param unknown_type $args
	 * @return mixed
	 */
	public function doLoggerStuff( $method, $args=array() )
	{
		return call_user_func_array( array($this->logger, $method), $args );
	}

	/**
	 * 
	 */
	public function listLoadedModules()
	{
		$var = get_included_files();
		$loadedFiles = array();
		foreach ( $var as $file )
		{
			$tokens = explode("/", $file);
			$module = explode( ".", end($tokens));
			$loadedFiles[] = $module[0];
		}
		return implode(', ', $loadedFiles);
	}

	/**
	 * 
	 */
	public function countLoadedModules()
	{
		return count(get_included_files());
	}

	/**
	 * 
	 */
	public function loadRequirements()
	{
		$curDir = getcwd();
		$dirList = scandir($curDir);
		$i=0;
		foreach( $dirList as $file )
		{
			if ( preg_match("/.*\.php/", $file) && $file != "bot.php" )
			{
				require($file);
				$i++;
			}
		}
		return $i;
	}

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