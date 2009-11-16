<?php
$dbtest = new DBFunctions();
$dbtest->_connect();
//$dbtest->_createTable();
//$dbtest->_describeTable();
$dbtest->_addFeed( null, 'url', 'title', 'lastTitle');
class DBFunctions
{
	private $db;

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

	function _describeTable( $tablename='rss_table')
	{
		$result =  $this->db->query("SELECT * FROM sqlite_master WHERE name = '$tablename'");
		//var_dump ( $result->fetchArray() );
		$result =  $this->db->query("PRAGMA table_info($tablename)");
		while ( $row = $result->fetchArray() )
			print_r($row);
		//var_dump( $this->db->arrayQuery("table_info($tablename)") );
	}

	function _createTable( $tablename='rss_table' )
	{
		$string = "create table $tablename(feedid INTEGER PRIMARY KEY ASC, url varchar(256), title varchar(256), lastTitle varchar(256))";
		$this->db->exec($string);		
	}

	function _getLastForFeed( $feedid, $tablename='rss_table' )
	{
		$result =  $this->db->query("SELECT * FROM $tablename");
		while ( $row = $result->fetchArray() )
			print_r($row);
		
	}
	
	function _updateLastForFeed( $feedid, $tablename='rss_table', $lastTitle )
	{
		$query = "UPDATE $tablename SET lastTitle = $lastTitle";
		$query = $this->db->escapeString($query);
		$success = $this->db->exec($query);
		print_r($success);
	}
	
	function _addFeed( $tablename='rss_table', $url, $title, $lastTitle )
	{
		$stmt = $this->db->prepare("SELECT * FROM $tablename WHERE url=:url");
		$stmt->bindValue( ':url', $url);
		$result = $stmt->execute();
		var_dump($result);
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