<?php
$bot = new bot();
echo "I loaded ".$bot->loadRequirements()." modules.\r\n";
$bot->initialise();
$bot->doConfigStuff('loadConfig');
echo "Loaded modules are: ".$bot->listLoadedFiles()."\r\n";
echo $bot ->doLoggerStuff('log', array('test', 'some text'))."\r\n";

class bot
{
	private $config;
	private $logger;
	private $socket;
	private $data;
	
	function __construct()
	{
	}

	public function initialise()
	{
		$this->config = new config();
		$this->logger = new botLogger();
	}
	
	public function doConfigStuff( $method, $args=array() )
	{
		return call_user_func_array( array($this->config, $method), $args );
	}
	
	public function doLoggerStuff( $method, $args=array() )
	{
		return call_user_func_array( array($this->logger, $method), $args );
	}
	
	public function listLoadedFiles()
	{
		$var = get_included_files();
		$loadedFiles = array();
		foreach ( $var as $file )
		{
			$tokens = explode("/", $file);
			$loadedFiles[] = end($tokens);
		}
		return implode(', ', $loadedFiles);
	}
	
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
	
	public function server()
	{
		$this->socket = fsockopen( $config->getConfig('server'), $config->getConfig('port'));
		fputs($this->socket,"USER ".$config->getConfig('user')." :".$config->getConfig('nick')."\r\n");
		fputs($this->socket,"NICK ".$config->getConfig('nick')."\r\n");
	}

	public function parseInput()
	{
		$explodedData = explode(" ", $this->data );
		if ( $this->data[0] == ":" )
		{
			if ( function_exists("handle_" . $explodedData[1]) )
			{
				log_output("|\tCalled handle_$explodedData[1]\n");
				call_user_func("handle_" . $explodedData[1]);
			}
		}
		else
		{
			if ( function_exists("handle_" . $data[0]) )
			{
				log_output("|\tCalled handle_$data[0]\n");
				call_user_func("handle_" . $data[0]);
			}
		}
	}
	
	public function main()
	{
		while ( $this->getFromServer )
		{
			//do stuff
		}
	}

	public function getFromServer()
	{
		$this->data = fgets( $this->socket, 1024);
		return $this->data;
	}
}