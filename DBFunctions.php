<?php
$dbtest = new DBFunctions();
$dbtest->_connect('dbtest');
$dbtest->_createTable();
$dbtest->_describeTable();
$dbtest->_describeTable('lol');
class DBFunctions
{
	private $db;

	function __construct()
	{
		
	}

	function _connect($filename='rss_db')
	{
		$err = '';
		$this->db = new PDO('sqlite:$filename');
		if ( !$this->db )
		{
	        die($err);
	    }

		return $this->db;
	}

	function _describeTable( $tablename='tablename')
	{
		$PDOStatement =  $this->db->query("SELECT * FROM sqlite_master WHERE name = '$tablename'");
		var_dump ( $PDOStatement->fetchAll() );
		$PDOStatement =  $this->db->query("PRAGMA table_info($tablename)");
		var_dump( $PDOStatement->fetchAll() );
		//var_dump( $this->db->arrayQuery("table_info($tablename)") );
	}

	function _createTable( $tablename='tablename' )
	{
		var_dump( $this->db->query("create table $tablename(one varchar(10), two smallint)") );		
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