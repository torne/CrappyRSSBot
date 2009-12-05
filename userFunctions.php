<?php

class userFunctions
{
	private $db;

	/**
	 *
	 */
	function __construct ()
	{
		$this->db = new db_users();
		$this->db->_connectUsers();
	}

	/**
	 *
	 * @param unknown_type $nick
	 * @param unknown_type $password
	 * @param unknown_type $email
	 *
	 */
	public function register( $bot, $password, $email )
	{

		if ( $this->db->_checkNickExists( $bot->_getNick() ) )
		{
			echo $this->db->_getUserMessage();
		}
		if ( $this->db->_checkEmailExists( $email ) )
		{
			echo $this->db->_getUserMessage();
		}
	}

	/**
	 *
	 * @param unknown_type $nick
	 * @param unknown_type $password
	 *
	 */
	public function login( $nick, $password )
	{
		if ( !$this->db->_checkNickPassword( $nick, $password) )
		{
			echo $this->db->_getUserMessage();
		}
	}

}

?>