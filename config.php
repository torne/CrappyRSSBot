<?php
/**
 * 
 * Configuration loader and setter
 * 
 */
class config
{
	private $defConfigFile = 'config.conf';
	private $curConfigFile = '';
	private $configuration = array();
	private $comment = '#';
	
	/**
	 * 
	 * constructor
	 * 
	 */
	public function __construct()
	{
	}
	
	/**
	 * 
	 * load the specified configuration file (or a default) into memory
	 * 
	 */
	public function _loadConfig ( $confFile  = null )
	{
		if ( !$confFile )
			$this->curConfigFile = $this->defConfigFile;
		else
			$this->curConfigFile = $confFile;
		
		if ( !file_exists($this->curConfigFile) )
			return 'File does not exist';
			
		if ( !is_readable($this->curConfigFile) )
			return 'File is not readable';

			//get an array of lines of the config file
		$file = file($this->curConfigFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		//getting section outside of the loop to retain
		$section = '';
		foreach ( $file as $line )
		{
			//trim whitespace
			$line = trim($line);

			//if it's a comment
			if ( strcasecmp($line[0], $this->comment) == 0 )
			{
				continue;
			}

			//it's a section header
			if ( preg_match("/\[(.*)\]/", $line, $matches) )
			{
				$section = $matches[1];
				continue;
			}
			
			//it's a name=value pair
			$namevalue = explode("=", $line);
			$name = trim($namevalue[0]);
			$value = trim($namevalue[1]);
			if ( strcasecmp($section, 'channels') == 0 )
			{
				$this->_setConfig($value, $value, $section);
			}
			$this->_setConfig($name, $value);
		}
	}
	
	/**
	 * 
	 * Set configuration for $name to $value
	 * 
	 */
	public function _setConfig ( $name, $value, $section=null )
	{
		if ( $section )
			$this->configuration[$section][$name] = $value;
		else
			$this->configuration[$name] = $value;
	}
	
	/**
	 * 
	 * Get a configuration item
	 * @return string
	 * 
	 */
	public function _getConfig ( $name, $section=null )
	{
		if( $section )
			return $this->configuration[$section][$name];
		else
			return $this->configuration[$name];
	}
	
	public function _getChans()
	{
		return $this->configuration['channels'];
	}
}
?>