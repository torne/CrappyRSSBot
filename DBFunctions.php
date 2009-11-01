<?php
$dbtest = new DBFunctions();
$dbtest->_connect('dbtest');
class DBFunctions
{
	
	function __construct()
	{
	
	}

	function _connect($bot)
	{
		$err = '';
		$filename = $bot;
//		$filename = $bot->_getConfig()->_getConfig( "dbname", "database");
		$db = new SQLiteDatabase($filename, null, $err);
		if ( !$db )
		{
	        die($err);
	    }

		return $db;	    
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
	}
}

?>