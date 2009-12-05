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
	private $rss_time;
	private $modules;
	private $methodMap;
	private $nick;
	private $user;
	private $hostmask;
	private $returnDest;
	private $messageType;
	private $message;

	/**
	 *
	 *
	 *
	 */
	function __construct ()
	{
		//set_include_path(get_include_path() . PATH_SEPARATOR . '/Users/gabriel/Zend/workspaces/DefaultWorkspace7/Crappy RSS Bot/CrappyRSSBot');
		include ('modules.php');
		$this->modules = new modules();
		$this->modules->_loadRequirements($this);
		$this->_initialise();
	}

	/**
	 *
	 */
	public function _initialise ()
	{
		$this->config = new config();
		$this->config->_loadConfig();
		$this->logger = new botLogger();
		$this->handle_functions = new handle_functions();
		$this->rss_time = time();
	}

	/**
	 *
	 * @param $nick
	 * @param $user
	 * @param $hostmask
	 * @param $returnDest
	 * @param $messageType
	 * @param $message
	 */
	function _setPrivmsg( $nick, $user, $hostmask, $returnDest, $messageType, $message )
	{
		$this->nick = $nick;
		$this->user = $user;
		$this->hostmask = $hostmask;
		$this->returnDest = $returnDest;
		$this->messageType = $messageType;
		$this->message = $message;
	}
	/**
	 * @return the $message
	 */
	public function _getMessage ()
	{
		return $this->message;
	}

	/**
	 * @return the $messageType
	 */
	public function _getMessageType ()
	{
		return $this->messageType;
	}

	/**
	 * @return the $returnDest
	 */
	public function _getReturnDest ()
	{
		return $this->returnDest;
	}

	/**
	 * @return the $hostmask
	 */
	public function _getHostmask ()
	{
		return $this->hostmask;
	}

	/**
	 * @return the $user
	 */
	public function _getUser ()
	{
		return $this->user;
	}

	/**
	 * @return the $nick
	 */
	public function _getNick ()
	{
		return $this->nick;
	}

	/**
	 *
	 */
	function _getModules ()
	{
		return $this->modules;
	}

	/**
	 *
	 * @param $modules
	 */
	function _setModules ($modules)
	{
		$this->modules = $modules;
	}

	/**
	 *
	 */
	function _getMethodmap ()
	{
		return $this->methodMap;
	}

	/**
	 *
	 * @param $methodMap
	 */
	function _setMethodmap ($methodMap)
	{
		$this->methodMap = $methodMap;
	}

	/**
	 *
	 */
	public function _getConfig ($name = null)
	{
		if ( !$name )
		{
			return $this->config;
		}
		return $this->config->_getConfig($name);
	}

	/**
	 *
	 */
	public function _main ()
	{
		$this->_server();
		while ( !feof($this->socket) )
		{
			if ($this->rss_time + 120 < time())
			{
				echo "Time to check for feed updates\r\n";
				$rss = new RSSFunctions();
				$rss->_checkForUpdates($this);
				$this->rss_time = time();
			}
			$this->_getFromServer();
			$this->_parseInput();
		}
	}

	/**
	 *
	 */
	public function _server ()
	{
		$this->socket = @fsockopen($this->config->_getConfig('server'), $this->config->_getConfig('port'));
		if ( !$this->socket )
		{
			die("Unable to connect to server\r\n");
		}
		fputs($this->socket, "USER " . $this->config->_getConfig('user') . " :" . $this->config->_getConfig('nick') . "\r\n");
		fputs($this->socket, "NICK " . $this->config->_getConfig('nick') . "\r\n");
	}

	/**
	 *
	 */
	public function _getFromServer ()
	{
		$this->data = trim( fgets($this->socket) );
		if ( !$this->data )
		{
			return;
		}
		echo "========>\t\t" . $this->data . "\r\n";
		return;
	}

	/**
	 *
	 */
	public function _parseInput ()
	{
		if ( !$this->data )
		{
			return;
		}

		$explodedData = explode(" ", $this->data);
		if ($this->data[0] == ":")
		{
			$this->data = substr($this->data, 1);
			if (method_exists($this->handle_functions, "_handle_" . $explodedData[1]))
			{
				$return = call_user_func(array($this->handle_functions , "_handle_" . $explodedData[1]), $this);
				if (preg_match("/reload (.+) (.+)/", $return, $matches))
				{
					$returnDest = $matches[1];
					$filename = $matches[2];
					$reload = new classReloader();
					$this->_sendMsg($returnDest, $reload->reload($this, $filename));
				}
			}
		}
		else
		{
			if (method_exists($this->handle_functions, "_handle_" . $explodedData[0]))
			{
				call_user_func(array($this->handle_functions , "_handle_" . $explodedData[0]), $this);
			}
		}
	}

	/**
	 *
	 * @param String $destination
	 * @param String $message
	 */
	public function _sendMsg ($destination, $message)
	{
		$this->_putToServer("PRIVMSG $destination :$message");
		sleep(2);
	}

	/**
	 *
	 * @param String $string
	 */
	public function _putToServer ($string)
	{
		echo "<========\t\t$string\r\n";
		fputs($this->socket, "$string\r\n");
	}

	/**
	 *
	 * @param String $chan
	 */
	public function _joinChan ($chan)
	{
		$this->_putToServer("JOIN $chan\r\n");
	}

	/**
	 *
	 */
	public function _joinChans ()
	{
		foreach ($this->config->_getChans() as $channel)
		{
			$this->_joinChan($channel);
		}

	}

	/**
	 *
	 */
	public function _getData ()
	{
		return $this->data;
	}

	/**
	 *
	 * @param unknown_type $message
	 */
	public function quit ($message)
	{
		$this->putToServer("QUIT :$message");
	}

}