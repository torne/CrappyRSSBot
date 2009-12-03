<?php
//$dbtest = new db_users();
//$dbtest->_connectUsers();
//$dbtest->_createTable();
//$dbtest->_describeTable();
//$dbtest->_addFeed( 'url', 'title', 'lastTitle');
//echo $dbtest->_getIdForUrl('url');
//echo $dbtest->_getIdForUrl('lol');
class db_users
{
	private $db;
	private $message='';
	private $tablename = 'users_table';

	/**
	 *
	 */
	function __construct()
	{

	}

	/**
	 *
	 * @param unknown_type $filename
	 */
	function _connectUsers( $filename='rss_db' )
	{
		$err = '';
		$this->db = new SQLite3($filename);

		if ( !$this->db )
		{
			die($err);
		}

		return $this->db;
	}

	/**
	 *
	 */
	function _getUserMessage()
	{
		return $this->message;
	}
//
//	/**
//	 *
//	 */
//	function _describeTable()
//	{
//		$result =  $this->db->query("SELECT * FROM sqlite_master WHERE name = '$this->tablename'");
//		//var_dump ( $result->fetchArray() );
//		$result =  $this->db->query("PRAGMA table_info($this->tablename)");
//		while ( $row = $result->fetchArray() )
//			print_r($row);
//		//var_dump( $this->db->arrayQuery("table_info($tablename)") );
//	}

//	/**
//	 *
//	 */
//	function _createTable()
//	{
//		$string = "drop table if exists $this->tablename";
//		$this->db->exec($string);
//		$string = "create table $this->tablename(user_id INTEGER PRIMARY KEY ASC, username varchar(256) unique, password varchar(256), email varchar(256), lasthost varchar(256))";
//		$this->db->exec($string);
//	}

	/**
	 *
	 * @param unknown_type $username
	 */
	function _checkUsernameExists( $username )
	{
		$result = $this->db->querySingle("SELECT * FROM $this->tablename WHERE username=$username");
		if ( $result )
		{
			$this->message = "Username exists.";
			return true;
		}
		return false;
	}

	function _checkEmailExists( $email )
	{
		$result = $this->db->querySingle("SELECT * FROM $this->tablename WHERE email=$email");
		if ( $result )
		{
			$this->message = "Email exists.";
			return true;
		}
		return false;
	}

	/**
	 *
	 * @param unknown_type $username
	 * @param unknown_type $password
	 */
	function _checkUsernamePassword( $username, $password )
	{
		$result = $this->db->querySingle("SELECT * FROM $this->tablename WHERE username=$username AND password=$password");
		if ( !$result )
		{
			$this->message = "Username or password incorrect.";
			return false;
		}
		return true;
	}

	function _registerUser( $username, $password, $email )
	{

	}
}
?>