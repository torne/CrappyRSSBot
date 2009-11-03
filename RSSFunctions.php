<?php
$rss = new RSSFunctions();
//$rss->_checkFeedHeader('http://www.php.net/feed.atom');
$rss->_getFeed('http://www.php.net/feed.atom');
$rss->_getFeed('http://xkcd.com/rss.xml');
class RSSFunctions
{
	
	function __construct()
	{
	
	}

	public function _getFeed($url)
	{
		require_once 'magpie/rss_fetch.inc';
		$rss = fetch_rss($url);
		foreach ( $rss->items as $item )
		{
			var_dump($item['title']);
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