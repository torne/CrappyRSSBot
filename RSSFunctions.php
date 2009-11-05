<?php
$rss = new RSSFunctions();
//$rss->_checkFeedHeader('http://www.php.net/feed.atom');
//$rss->_checkFeedHeader('http://xkcd.com/rss.xml');
$rss->_getFeed('http://www.php.net/feed.atom');
$rss->_getFeed('http://xkcd.com/rss.xml');
$rss->_getFeed('http://pirate.planetarion.com/external.php?type=RSS');
$rss->_getFeed('http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=rss');
$rss->_getFeed('http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom');
class RSSFunctions
{
	
	function __construct()
	{
	
	}

	public function _getFeed($url)
	{
		require_once 'magpie/rss_fetch.inc';
		$rss = simplexml_load_file($url);
		$names = $rss->getNamespaces();
		$titles = $rss->xpath('//title');
		if ( $titles[0] )
		{
			echo "Title-main1: ".$titles[0]."\r\n";
		}
		else
		{
			if ( $names )
			{
				echo $names[""]."\r\n";
				$children =  $rss->children($names[""]);
				$title = $children->title;
				echo "Title-main2: ".$title."\r\n";
			}
		}
		
		$rss = fetch_rss($url);
		echo "First sub title: ".$rss->items[0]['title']."\r\n";
		echo "First sub link: ".$rss->items[0]['link']."\r\n\r\n";
		//		var_dump($rss);
//		foreach ( $rss->items as $item )
//		{
//			var_dump($item['title']);
//		}
		
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
		$dbtest = new DBFunctions();
		$dbtest->_connect('dbtest');
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