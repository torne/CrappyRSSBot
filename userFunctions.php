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
		$this->db->_connect();
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
			echo $this->db->_getMessage();
		}
		if ( $this->db->_checkEmailExists( $email ) )
		{
			echo $this->db->_getMessage();
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
			echo $this->db->_getMessage();
		}
	}

}

?>