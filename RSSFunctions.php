<?php

//$rss = new RSSFunctions();
//$rss->_checkFeedHeader('http://www.php.net/feed.atom');
//$rss->_checkFeedHeader('http://xkcd.com/rss.xml');
//$rss->_getFeed('http://www.php.net/feed.atom');
//$rss->_getFeed('http://xkcd.com/rss.xml');
//$rss->_getFeed('http://pirate.planetarion.com/external.php?type=RSS');
//$rss->_getFeed('http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=rss');
//$rss->_getFeed('http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom');
//$url = "http://trac.edgewall.org/timeline?ticket=on&changeset=on&milestone=on&wiki=on&max=50&daysback=90&format=rss";
//$rss->_addFeed( $url );
//$rss->_getMainTitle( $url );
//$url = "http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom";
//$rss->_getMainTitle( $url );
//$rss->_addFeed( $url );
//$rss->_getItemsUntilPrevTitle( $url );
class RSSFunctions
{
	private $db;

	/**
	 *
	 */
	function __construct ()
	{
		require_once ('magpie/rss_fetch.inc');
		require_once ('db_rssFeeds.php');
		define('MAGPIE_CACHE_ON', false);
		$this->db = new db_rssFeeds();
		$this->db->_connectRSS();
	}

	function __destruct()
	{
		echo "destruction of RSSFunctions\r\n";
		$this->db->__destruct();
	}


	/**
	 * @param unknown_type $bot
	 */
	public function _checkForUpdates ($bot)
	{
		echo "checking for updates\r\n";
		$this->_getCurFeeds($bot);
		$this->db->__destruct();
	}

	/**
	 *
	 * @param unknown_type $url
	 */
	public function _getMainTitle ($url)
	{
		$rss = fetch_rss($url);
		return $rss->channel['title'];
	}

	/**
	 *
	 * @param unknown_type $bot
	 * @param unknown_type $url
	 */
	public function _getItemsUntilPrevTitle ($bot, $url)
	{
		$details = $this->db->_getFeedDetailsForURL($url);
		$rss = fetch_rss($url);

		if ($rss->items[0]['title'] == $details['lastTitle'])
			return;

		$success = $this->db->_updateLastForFeed($details['feedid'], $rss->items[0]['title']);
		if ( !$success )
		{
			echo $this->db->_getRSSMessage()."\r\n";
			break;
		}
		$messageArray = array();
		foreach ($rss->items as $item)
		{
			extract($item);
			if ($item['title'] == $details['lastTitle'])
			{
				break;
			}

			$combine = '';
			if ( $description )
			{
				$combine = $description;
			}
			else if ( $atom_content )
			{
				$combine = $description;
			}

			if ( $combine )
			{
				$split = preg_split("/\w+/", $atom_content);
				var_dump($split);
				$combine = implode(", ", $split);

				if (strlen($combine) >= 100)
				{
					$combine = " - ".substr(strip_tags($combine), 0, 99) . "...";
				}

			}

			$messageArray[] = $details['title'] . " - $title - $link$combine";
		}
		foreach ( $messageArray as $message )
		{
			foreach ($bot->_getConfig()->_getChans() as $channel)
			{
				$bot->_sendMsg( $channel, $message);
			}
		}
		return null;
	}

	/**
	 *
	 * @param unknown_type $url
	 */
	public function _getFeed ($url)
	{
		$rss = simplexml_load_file($url);
		$names = $rss->getNamespaces();
		$titles = $rss->xpath('//title');
		if ($titles[0])
		{
			echo "Title-main1: " . $titles[0] . "\r\n";
		}
		else
		{
			if ($names)
			{
				echo $names[""] . "\r\n";
				$children = $rss->children($names[""]);
				$title = $children->title;
				echo "Title-main2: " . $title . "\r\n";
			}
		}

		$rss = fetch_rss($url);
		//echo "First sub title: ".$rss->items[0]['title']."\r\n";
		//echo "First sub link: ".$rss->items[0]['link']."\r\n\r\n";
		//		var_dump($rss);
		foreach ($rss->items as $item)
		{
			echo "Title: " . $item['title'] . "\r\n";
		}

	}

	/**
	 *
	 * @param unknown_type $url
	 */
	public function _getLastFeedItem ($url)
	{
		$rss = fetch_rss($url);
		return $rss->items[0]['title'];
	}

	/**
	 *
	 * @param unknown_type $url
	 */
	public function _checkFeedHeader ($url)
	{
		var_dump(get_headers($url));
	}

	/**
	 *
	 * @param unknown_type $bot
	 */
	public function _getCurFeeds ($bot)
	{
		foreach ($this->db->_getFeeds() as $feed)
		{
			if ( !$feed['url'] || empty($feed['url']) )
				continue;
			$this->_getItemsUntilPrevTitle($bot, $feed['url']);
		}
	}

	/**
	 *
	 * @param unknown_type $bot
	 */
	public function listFeeds ( $bot )
	{
		$feedfun = array();
		$feedfun[] = "Currently stored feeds: Title - URL - Last Entry Title";
		foreach ($this->db->_getFeeds() as $feed)
		{
			if ( !empty($feed['title']) )
				$feedfun[] = $feed['title'] . " - " . $feed['url'] . " - " . $feed['lastTitle'];
		}
		foreach ( $feedfun as $feed )
			$bot->_sendMsg( $bot->_getReturnDest(), $feed);
		$this->db->__destruct();
		return null;
	}

	/**
	 *
	 * @param unknown_type $bot
	 * @param unknown_type $url
	 */
	public function addFeed ($bot, $url)
	{
		$title = $this->_getMainTitle($url);
		$lastTitle = $this->_getLastFeedItem($url);
		$rowID = $this->db->_addFeed($url, $title, $lastTitle);
		if (! $rowID)
		{
			return $this->db->_getRSSMessage();
		}
		$this->db->__destruct();
		return $rowID;
	}

	/**
	 *
	 */
	public function remFeed ( $bot, $url )
	{
		$this->db->__destruct();
		return "I don't work, but if I did I'd remove a feed";
	}

	/**
	 *
	 */
	public function _getFeeders ()
	{

	}

	/**
	 *
	 */
	public function listFeeders ()
	{
		$this->db->__destruct();
		return "I don't work, but if I did I'd list feed admins";
	}

	/**
	 *
	 */
	public function addFeeder ()
	{
		$this->db->__destruct();
		return "I don't work, but if I did I'd add a feed admin";
	}

	/**
	 *
	 */
	public function remFeeder ()
	{
		$this->db->__destruct();
		return "I don't work, but if I did I'd remove a feed admin";
	}

	/**
	 *
	 * @param unknown_type $bot
	 */
	public function _checkFeederAccess( $bot )
	{

	}
}

?>