<?php

class users
{
	private $db;

	function __construct ()
	{
		$this->db = new DBFunctions();
		$this->db->_connect();
	}

	public function register( $nick, $password )
	{

	}

}

?>