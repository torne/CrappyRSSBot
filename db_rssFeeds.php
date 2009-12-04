<?php
//$dbtest = new db_rssFeeds();
//$dbtest->_connect();
//$dbtest->_createTable();
//$dbtest->_describeTable();
//$dbtest->_addFeed( 'url', 'title', 'lastTitle');
//echo $dbtest->_getIdForUrl('url');
//echo $dbtest->_getIdForUrl('lol');
class db_rssFeeds
{
	private $db;
	private $message='';
	private $tablename = 'rss_table';

	/**
	 *
	 */
	function __construct()
	{

	}

	function __destruct()
	{
		echo "destruction\r\n";
		$this->db->close();
	}

	/**
	 *
	 * @param unknown_type $filename
	 */
	function _connectRSS($filename='rss_db')
	{
		$this->filename = $filename;
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
	function _getRSSMessage()
	{
		return $this->message;
	}

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
//		$string = "create table $this->tablename(feedid INTEGER PRIMARY KEY ASC, url varchar(256), title varchar(256), lastTitle varchar(256))";
//		$this->db->exec($string);
//	}

	/**
	 *
	 */
	function _getFeeds()
	{
		$result =  $this->db->query("SELECT * FROM $this->tablename");
		$feeds = array();
		while ( $feeds[] = $result->fetchArray() );
			return $feeds;
	}

	/**
	 *
	 * @param unknown_type $url
	 */
	function _getIdForUrl( $url )
	{
		$url = $this->db->escapeString($url);
		$result =  $this->db->query("SELECT * FROM $this->tablename WHERE url='$url'");
		if ( !$result )
		{
			$this->message = 'No such feed is stored.';
			return false;
		}
		$array = $result->fetchArray();
		return $array['feedid'];
	}

	/**
	 *
	 * @param $feedid
	 */
	function _getFeedDetailsForFeedid( $feedid )
	{
		$feedid = $this->db->escapeString($feedid);
		$result =  $this->db->querySingle("SELECT * FROM $this->tablename WHERE feedid=$feedid");
		if ( !$result )
		{
			$this->message = "No such feed id.";
			return false;
		}
		return $result->fetchArray();
	}

	/**
	 *
	 * @param $url
	 */
	function _getFeedDetailsForURL( $url )
	{
		$feedid = $this->_getIdForUrl($url);

		if ( !$feedid )
		return false;

		$result =  $this->db->query("SELECT * FROM $this->tablename WHERE feedid=$feedid");
		if ( !$result )
		{
			$this->message = "No such feed id.";
			return false;
		}
		return $result->fetchArray();
	}

	/**
	 *
	 * @param $feedid
	 * @param $lastTitle
	 */
	function _updateLastForFeed( $feedid, $lastTitle )
	{
		$result = $this->db->querySingle("SELECT * FROM $this->tablename WHERE feedid=$feedid");
		if ( !$result )
		{
			$this->message = "No such feed id.";
			return false;
		}

		$lastTitle = $this->db->escapeString($lastTitle);
		$query = "UPDATE $this->tablename SET lastTitle = '$lastTitle' WHERE feedid = $feedid";
		echo "update query\r\n";
		var_dump($query);
		$success = $this->db->exec($query);
		$this->__destruct();
		return $success;
	}

	/**
	 *
	 * @param unknown_type $url
	 * @param unknown_type $title
	 * @param unknown_type $lastTitle
	 */
	function _addFeed( $url, $title, $lastTitle )
	{
		$url = $this->db->escapeString($url);
		$title = $this->db->escapeString($title);
		$lastTitle = $this->db->escapeString($lastTitle);
		$result = $this->db->querySingle("SELECT * FROM $this->tablename WHERE url='$url'");
		if ( $result )
		{
			$this->message = "That URL is already stored.";
			return false;
		}

		if ( !$result )
		{
			$result = $this->db->exec("INSERT INTO $this->tablename (url, title, lastTitle) VALUES ('$url', '$title', '$lastTitle')");
			if ( !$result )
			{
				$this->message = "Could not insert into table";
				return false;
			}
			return $this->db->lastInsertRowID();
		}

	}
}
?>