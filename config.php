<?php
class config
{
	private $defConfigFile = 'config.conf';
	private $path = '/Users/gabriel/Zend/workspaces/DefaultWorkspace7/Crappy RSS Bot/CrappyRSSBot';
	private $curConfigFile = '';
	private $configuration = array();
	private $comment = '#';
	
	
	public function __construct()
	{
		set_include_path(get_include_path() . PATH_SEPARATOR . $this->path);
	}
	
	public function loadRequirements()
	{
		$curDir = getcwd();
		$dirList = scandir($curDir);
		$i=0;
		foreach( $dirList as $file )
		{
			if ( preg_match("/.*\.php/", $file) && $file != "bot.php" && $file != "config.php" )
			{
				require($file);
				$i++;
			}
		}
		return $i;
	}
	
	public function loadConfig ( $confFile  = null )
	{
		if ( !$confFile )
			$this->curConfigFile = $this->defConfigFile;
		else
			$this->curConfigFile = $confFile;
		
		if ( !file_exists($this->curConfigFile) )
			return 'File does not exist';
			
		if ( !is_readable($this->curConfigFile) )
			return 'File is not readable';
		
		$file = file($this->curConfigFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ( $file as $line )
		{
			$line = trim($line);
			echo $line[0] . $line. "\r\n";
		}
		
	}
	
	public function setConfig ( $name, $value )
	{
		
	}
}
?>