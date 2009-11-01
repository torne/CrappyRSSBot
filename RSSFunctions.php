<?php
$rss = new RSSFunctions();
$rss->_checkFeedHeader('http://www.php.net/feed.atom');
$rss->_getFeed('php.atom');

class RSSFunctions
{
	
	function __construct()
	{
	
	}

	public function _getFeed($url)
	{
		$var = simplexml_load_file($url);
		foreach ( $var->entry as $entry )
		{
			var_dump($entry->title);
		}
	}
	
	public function _checkFeedHeader($url)
	{
		var_dump( get_headers($url) );
	}
	
	public function _getCurFeeds()
	{
		
	}
	
	public function listFeeds()
	{
		
	}
	
	public function addFeed()
	{
		
	}
	
	public function remFeed()
	{
		
	}

	public function _getFeeders()
	{
		
	}
	
	public function listFeeders()
	{
		
	}
	
	public function addFeeder()
	{
		
	}
	
	public function remFeeder()
	{
		
	}
	
}

?>