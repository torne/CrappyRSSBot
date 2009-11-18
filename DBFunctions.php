<?php
$dbtest = new DBFunctions();
$dbtest->_connect();
//$dbtest->_createTable();
//$dbtest->_describeTable();
//$dbtest->_addFeed( 'url', 'title', 'lastTitle');
echo $dbtest->_getIdForUrl('url');
echo $dbtest->_getIdForUrl('lol');
class DBFunctions
{
	private $db;
	private $message='';
	private $tablename='rss_table';
	
	function __construct()
	{
		
	}

	function _connect($filename='rss_db')
	{
		$err = '';
		$this->db = new SQLite3($filename);
		if ( !$this->db )
		{
	        die($err);
	    }

		return $this->db;
	}

	function _getMessage()
	{
		return $this->message;
	}
	
	function _describeTable()
	{
		$result =  $this->db->query("SELECT * FROM sqlite_master WHERE name = '$this->tablename'");
		//var_dump ( $result->fetchArray() );
		$result =  $this->db->query("PRAGMA table_info($this->tablename)");
		while ( $row = $result->fetchArray() )
			print_r($row);
		//var_dump( $this->db->arrayQuery("table_info($tablename)") );
	}

	function _createTable()
	{
		$string = "create table $this->tablename(feedid INTEGER PRIMARY KEY ASC, url varchar(256), title varchar(256), lastTitle varchar(256))";
		$this->db->exec($string);		
	}

	function _getIdForUrl( $url )
	{
		$stmt =  $this->db->prepare("SELECT * FROM $this->tablename WHERE url=:url");
		$stmt->bindValue( ':url', $url);
		$result = $stmt->execute();
		if ( !$result )
		{
			$this->message = 'No such feed is stored.';
			return false;
		}
		$array = $result->fetchArray();
		return $array['feedid'];
	}
	
	function _getFeedDetailsForFeedid( $feedid )
	{
		$stmt =  $this->db->prepare("SELECT * FROM $this->tablename WHERE id=:feedid");
		$stmt->bindValue( ':feedid', $feedid);
		$result = $stmt->execute();
		if ( !$result )
		{
			$this->message = "No such feed id.";
			return false;
		}
		return $result->fetchArray();
	}
	
	function _getFeedDetailsForURL( $url )
	{
		$feedid = $this->_getIdForUrl($url);

		if ( !$feedid )
			return false;

		$stmt =  $this->db->prepare("SELECT * FROM $this->tablename WHERE id=:feedid");
		$stmt->bindValue( ':feedid', $feedid);
		$result = $stmt->execute();
		if ( !$result )
		{
			$this->message = "No such feed id.";
			return false;
		}
		return $result->fetchArray();
	}
	
	function _updateLastForFeed( $feedid, $lastTitle )
	{
		$stmt = $this->db->prepare("SELECT * FROM $this->tablename WHERE id=:feedid");
		$stmt->bindValue( ':feedid', $feedid);
		$result = $stmt->execute();
		if ( !$result )
		{
			$this->message = "No such feed id.";
			return false;
		}

		$query = "UPDATE $this->tablename SET lastTitle = $lastTitle";
		$success = $this->db->exec($query);
		return $success;
	}
	
	function _addFeed( $url, $title, $lastTitle )
	{
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
	
//		$filename = $bot->_getConfig()->_getConfig( "dbname", "database");
//		$query = $db->query("SELECT name FROM sqlite_master WHERE name = 'tablename'");
//		if ( $query->numRows() )
//			echo "table exists\r\n";
//		else
//			echo "table does not exist\r\n";
//		$query = $db->query("SELECT name FROM sqlite_master WHERE name = 'tabelname'");
//		if ( $query->numRows() )
//			echo "tables exists\r\n";
//		else
//			echo "table does not exist\r\n";
//			$q = @$db->query('SELECT requests FROM tablename WHERE id = 1');
//	        if ($q === false) {
//	            $db->queryExec('CREATE TABLE tablename (id int, requests int, PRIMARY KEY (id)); INSERT INTO tablename VALUES (1,1)');
//	            $hits = 1;
//	        } else {
//	            $result = $q->fetchSingle();
//	            $hits = $result+1;
//	        }
//	        $db->queryExec("UPDATE tablename SET requests = '$hits' WHERE id = 1");
//	}
}

?>