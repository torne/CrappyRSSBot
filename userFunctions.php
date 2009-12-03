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
	 * @param unknown_type $username
	 * @param unknown_type $password
	 * @param unknown_type $email
	 *
	 */
	public function register( $username, $password, $email )
	{
		if ( $this->db->_checkUsernameExists( $username ) )
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
	 * @param unknown_type $username
	 * @param unknown_type $password
	 *
	 */
	public function login( $username, $password )
	{
		if ( !$this->db->_checkUsernamePassword( $username, $password) )
		{
			echo $this->db->_getUserMessage();
		}
	}

}

?>