<?php
/**
 * bugspray issue tracking software
 * Copyright (c) 2009-2010 a2h - http://a2h.uni.cc/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Under section 7b of the GNU General Public License you are
 * required to preserve this notice. Additional attribution may be
 * found in the NOTICES.txt file provided with the Program.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// generation time tracking
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

// some variable(s) to use
$datetimenull = '0000-00-00 00:00:00';

// users!
session_start();

// debugging
$debug_log = array();

// grab the settings
include("settings.php");

// connect up to the db
$con = mysql_connect($mysql_server, $mysql_username, $mysql_password) or die(mysql_error());
mysql_select_db($mysql_database, $con);

// include the other important files
include("template.php");
include("users.php");


// temp
$client = array('is_logged' => $users->client->is_logged, 'is_admin' => $users->client->is_admin);


// functions begin here
function db_query($query, $purpose='<i>No purpose given</i>')
{	
	global $debug, $debug_log, $db_queries;
	
	$result = mysql_query($query);
	
	if ($result)
	{
		$db_queries++;
	}
	
	if ($debug)
	{
		$debug_log[] = array(
			'type' => 'query',
			'success' => $result ? true : false,
			'text' => $purpose
		);
	}
	
	return $result;
}

function db_query_single($query, $purpose='<i>No purpose given</i>')
{
	if (strstr($query,"LIMIT 1")) { exit('fix this'); } // temporary line added after this function changed to what it is now
	
	$result = db_query($query." LIMIT 1", $purpose);
	
	if ($result)
	{
		$array = mysql_fetch_array($result);
	}
	
	return $result ? $array : false;
}

function db_query_toarray($query, $properid=false, $purpose='<i>No purpose given</i>')
{
	$result = db_query($query, $purpose);
	
	if ($result)
	{
		$ret = array();
		$num_rows = mysql_num_rows($result);
		$num_fields = mysql_num_fields($result);
		for ($i=0;$i<$num_rows;$i++)
		{
			for ($j=0;$j<$num_fields;$j++)
			{
				if ($properid)
					$ai = $i+1;
				else
					$ai = $i;
				
				$ret[$ai][mysql_field_name($result,$j)] = mysql_result($result,$i,mysql_field_name($result,$j));
			}
		}
	}
	
	return $result ? $ret : false;
}

function logwhencmp($a,$b)
{
    if ($a['when'] == $b['when'])
	{
		return 0;
	}
	return ($a['when'] > $b['when']) ? -1 : 1;
}

function query_uid($id)
{
	global $queries_uid;
	
	if (!$queries_uid[$id])
	{
		$queries_uid[$id] = db_query_single("SELECT * FROM users WHERE id = $id", "Retrieving info for user id $id from database");
	}
	
	return $queries_uid[$id];
}

function query_acttypes($id)
{
	global $queries_acttypes;
	
	if (!$queries_acttypes[$id])
	{
		$queries_acttypes[$id] = db_query_single("SELECT * FROM actiontypes WHERE id = $id", "Retrieving info for action type id $id from database");
	}
	
	return $queries_acttypes[$id];
}

function query_cats($id) /* queries don't have 9 lives though */
{
	global $queries_cats;
	
	if (!$queries_cats[$id])
	{
		$queries_cats[$id] = db_query_single("SELECT * FROM categories WHERE id = $id", "Retrieving info for category id $id from database");
	}
	
	return $queries_cats[$id];
}

function query_tags($id)
{
	global $queries_tags;
	
	if (!$queries_tags[$id])
	{
		$queries_tags[$id] = db_query_single("SELECT * FROM tags WHERE id = $id", "Retrieving info for tag id $id from database");
	}
	
	return $queries_tags[$id];
}

function getav($id)
{
	$q = query_uid($id);
	return $q['avatar_location'];
}

function getunm($id,$link=false)
{
	$q = query_uid($id);
	
	if ($q['displayname'] == '')
		$ret = $q['username'];
	else
		$ret = $q['displayname'];
	
	if ($link)
		return '<a href="profile.php?id='.$id.'">' . $ret . '</a>';
	else
		return $ret;
}

function getuemail($id)
{	
	$q = query_uid($id);
	return $q['email'];
}

function getuinfo($info,$clear=true)
{
	if (gettype($info) == 'array')
	{
		$id = $info['id'];
		$av = $info['avatar'];
		$unm = $info['username'];
	}
	else
	{
		$id = $info;
		$av = getav($id);
		$unm = getunm($id);
	}
	
	$string = '
	<div class="avatar fl" style="margin-right:4px;"><img src="'.$av.'" alt="" /></div>
	<a href="profile.php?id='.$id.'" class="username'.(getubanned($id)?' banned':'').'">'.$unm.'</a>
	'.($clear?'<div class="fc"></div>':'');
	
	return str_replace(array("\n","\r","\t"),'',$string);
}

function getubanned($id)
{
	$q = query_uid($id);
	return $q['banned'];
}

function getufavs($id)
{
	global $queries_favs;
	
	if (!isset($queries_favs[$id]))
	{
		$favs = db_query("SELECT ticketid FROM favorites WHERE userid = $id", "Retrieving favorites for user id $id from database");
		
		$queries_favs[$id] = array();
		
		while ($fav = mysql_fetch_array($favs))
		{
			$queries_favs[$id][] = $fav['ticketid'];
		}
	}
	
	return $queries_favs[$id];
}

function getactimg($id)
{
	$q = query_acttypes($id);
	return $q['img'];
}

function getactcol($id)
{
	$q = query_acttypes($id);
	return $q['color'];
}

function getactlogdsc($id)
{
	$q = query_acttypes($id);
	return $q['logdescription'];
}

function gettagnm($id)
{
	$q = query_tags($id);
	return $q['name'];
}

function getcatnm($id)
{
	$q = query_cats($id);
	return $q['name'];
}

function getissnm($id)
{	
	$q = db_query_single("SELECT name FROM issues WHERE id = $id", "Retrieving info for issue id $id from database");
	return $q[0];
}

function getstatuses()
{	
	return array(
		array('id' => 1, 'type' => 'open', 'name' => 'open'),
		array('id' => 2, 'type' => 'assigned', 'name' => 'assigned'),
		array('id' => 3, 'type' => 'resolved', 'name' => 'resolved'),
		array('id' => 4, 'type' => 'postponed', 'name' => 'open'),
		array('id' => 5, 'type' => 'declined', 'name' => 'declined')
	);
}

function getstatusnm($id)
{
	$statuses = getstatuses();
	return $statuses[$id-1]['name'];
}

function getstatustype($id)
{
	$statuses = getstatuses();
	return $statuses[$id-1]['type'];
}

function issuecol($status, $severity)
{	
	if (getstatustype($status) == 'declined')
	{
		$col = '#ededed';
	}
	elseif (getstatustype($status) == 'resolved')
	{
		$col = '#c8ffa3';
	}
	else
	{
		switch ($severity)
		{
			case 1:
				$col = '#fffa6c';
				break;
			case 2:
				$col = '#ffe400';
				break;
			case 3:
				$col = '#ffae00';
				break;
			case 4:
				$col = '#ff6600';
				break;
			case 5:
				$col = '#ff0000';
				break;
		}
	}
	
	return $col;
}

function ticket_list($type, $status, $order='desc')
{
	global $page, $client;
	
	// the myriad of status filters
	switch ($status)
	{
		case 'unassigned': $whereclause = 'WHERE issues.status = 1'; break;
		case 'assigned': $whereclause = 'WHERE issues.status = 2'; break;
		case 'resolved': $whereclause = 'WHERE issues.status = 3'; break;
		//case 'postponed': $whereclause = 'WHERE issues.status = 4'; break;
		case 'declined': $whereclause = 'WHERE issues.status = 5'; break;
		case 'all': $whereclause = ''; break;
		case 'open': default: $status = 'open'; $whereclause = 'WHERE issues.status = 1 OR issues.status = 2'; break;
	}
	
	// if we don't have a proper order than force descending
	if ($order)
	{
		$order = strtoupper($order);
		if ($order != 'ASC' || $order != 'DESC')
		{
			$order = 'DESC';
		}
	}
	else
	{
		$order = 'DESC';
	}

	// and what query do we need?
	switch ($type)
	{
		case 'following':
			$whereclause = str_replace('WHERE', 'AND', $whereclause);
			
			$query = "
				SELECT issues.*, comments.author AS commentauthor, favorites.userid AS favorited FROM issues
				LEFT JOIN comments ON comments.issue = issues.id AND comments.when_posted = issues.when_updated
				LEFT JOIN favorites ON favorites.ticketid = issues.id
				WHERE (favorites.userid = {$_SESSION['uid']} OR issues.assign = {$_SESSION['uid']})
				$whereclause
				ORDER BY issues.when_updated $order"; break;
		
		case 'standard': default:
			$query = "
				SELECT issues.*, comments.author AS commentauthor, favorites.userid AS favorited FROM issues
				LEFT JOIN comments ON comments.issue = issues.id AND comments.when_posted = issues.when_updated
				LEFT JOIN favorites ON favorites.ticketid = issues.id
				$whereclause
				ORDER BY issues.when_updated $order"; break;
	}

	// now run it!
	$result_issues = db_query_toarray($query, false, 'Retrieving a list of issues');

	// extra variables
	$count = count($result_issues);
	for ($i=0;$i<$count;$i++)
	{
		// is the issue favoUrited? (db uses "favorite" because everyone favoUrs the americans)
		$result_issues[$i]['favorite'] = $result_issues[$i]['favorited'] ? true : false;
		
		// determine the colour of the listing (!!!!!!!!!!!!!!!!!!!move into template?)
		$result_issues[$i]['status_color'] = issuecol($result_issues[$i]['status'], $result_issues[$i]['severity']);
	}

	// status types
	$statuses = array(
		array(
			'name' => 'Open',
			'type' => 'open',
			'sel' => $status == 'open' ? true : false
		),
		array(
			'name' => 'Unassigned',
			'type' => 'unassigned',
			'sel' => $status == 'unassigned' ? true : false
		),
		array(
			'name' => 'Assigned',
			'type' => 'assigned',
			'sel' => $status == 'assigned' ? true : false
		),
		array(
			'name' => 'Resolved',
			'type' => 'resolved',
			'sel' => $status == 'resolved' ? true : false
		),
		array(
			'name' => 'Declined',
			'type' => 'declined',
			'sel' => $status == 'declined' ? true : false
		),
		array(
			'name' => 'All',
			'type' => 'all',
			'sel' => $status == 'all' ? true : false
		)
	);

	// and we're off! even though this is called "setPage" it's just an include, probably should change that [TODO]
	ob_start();
	$page->setPage(
		'ticket_list.php',
		array(
			'type' => $type,
			'statuses' => $statuses,
			'issues' => $result_issues
		)
	);
	return ob_get_clean();
}

function hascharacters($string)
{
	if (str_replace(' ','',$string) == '')
		return false;
	else
		return true;
}

function escape_smart($value)
{
	// code from http://simon.net.nz/articles/protecting-mysql-sql-injection-attacks-using-php/
	if (get_magic_quotes_gpc())
	{
		$value = stripslashes($value);
	}
	if (!is_numeric($value))
	{
		$value = mysql_real_escape_string($value);
	}
	return $value;
}

function timehtml5($timestamp,$pubdate=false,$innerhtml='[nothingatall]')
{
	// for reference
	$timestamporig = $timestamp;
	
	// is the timestamp a string instead of proper time object?
	if (gettype($timestamp) == 'string')
	{
		$timestamp = strtotime($timestamp);
	}
	
	// is the timestamp invalid?
	if ($timestamp <= 0) // php 5.1.0 returns FALSE, earlier returns -1
	{
		return 'Invalid timestamp (provided: '.$timestamporig.')';
	}
	
	// html5 format
	$datetime = date(DATE_W3C,$timestamp);
	
	// output the readied tag
	if ($innerhtml != '[nothingatall]')
	{
		return '<time'.($pubdate?' pubdate':'').' datetime="'.$datetime.'">'.$innerhtml.'</time>';
	}
	else
	{
		return '<time'.($pubdate?' pubdate':'').' datetime="'.$datetime.'">'.$timestamporig.'</time>';
	}
}

function timeago($timestamp, $pubdate=false, $short=false)
{
	// original function written by Thomaschaaf - http://stackoverflow.com/questions/11/how-do-i-calculate-relative-time

	if (gettype($timestamp) == 'string')
	{
		$timestamp = strtotime($timestamp);
	}

	$second = 1;
	$minute = 60 * $second;
	$hour = 60 * $minute;
	$day = 24 * $hour;
	$month = 30 * $day;
	
	if (!$short)
	{
		$ssecond = ' second';
		$sseconds = ' seconds';
		$sminute = ' minute';
		$sminutes = ' minutes';
		$shour = ' hour';
		$shours = ' hours';
		$sday = ' day';
		$sdays = ' days';
	}
	else
	{
		$ssecond = 's';
		$sseconds = 's';
		$sminute = 'm';
		$sminutes = 'm';
		$shour = 'h';
		$shours = 'h';
		$sday = 'd';
		$sdays = 'd';
	}

	$delta = time() - $timestamp;

	$hasago = true;
	
	if ($delta < 1 * $minute)
	{
		$ret = $delta == 1 ? "1$ssecond" : $delta . "$sseconds";
	}
	elseif ($delta < 2 * $minute)
	{
		$ret = "1$sminute";
	}
	elseif ($delta < 45 * $minute)
	{
		$ret = floor($delta / $minute) . "$sminutes";
	}
	elseif ($delta < 90 * $minute)
	{
		$ret = "1$shour";
	}
	elseif ($delta < 24 * $hour)
	{
		$ret = floor($delta / $hour) . "$shours";
	}
	elseif ($delta < 48 * $hour)
	{
		$ret = "1$sday";
	}
	elseif ($delta < 30 * $day)
	{
		$ret = floor($delta / $day) . "$sdays";
	}
	else
	{
		$hasago = false;
		
		$ret = date(
			(!$short ? 'F' : 'M') . ' d' . ($delta < 12 * $month ? '' : " 'y"), 
			$timestamp
		);
		
		if ($short)
		{
			$ret = strtolower($ret);
		}
	}
	
	if ($hasago)
	{
		$ret .= ' ago';
	}

	return timehtml5($timestamp, $pubdate, $ret);
}

function timeago_short($timestamp, $pubdate=false)
{
	$temp = timeago($timestamp, $pubdate);
	
	return str_replace(
		array(
			'seconds',
			'second',
			'minutes',
			'minute',
			'hours',
			'hour',
			'days',
			'day'
		),
		array(
			's',
			's',
			'm',
			'm',
			'h',
			'h',
			'd',
			'd'
		),
		$temp
	);
}

function footerinfo($want)
{
	global $db_queries, $starttime;
	
	$ret = '';
	switch ($want)
	{
		case 'time':
			$mtime = explode(' ', microtime());
			$totaltime = $mtime[0] + $mtime[1] - $starttime;
			$ret = sprintf('%.3f',$totaltime).' seconds';
			break;
		case 'queries':
			if (!isset($db_queries))
			{
				$ret = '0 queries';
			}
			else
			{
				$ret = $db_queries;
				
				if ($ret == 1)
					$ret .= ' query';
				else
					$ret .= ' queries';
			}
			break;
	}
	
	return $ret;
}

function parsebbcode($string)
{	
	$original = array(
		'/\n/',
		'/\[noparse\](.*?)\[\/noparse\]/ise',
		'/\[b\](.*?)\[\/b\]/is',
		'/\[i\](.*?)\[\/i\]/is',
		'/\[u\](.*?)\[\/u\]/is',
		'/\[s\](.*?)\[\/s\]/is',
		'/\[url=(.*?)\](.*?)\[\/url\]/is',
		'/\[url\](.*?)\[\/url\]/is',
		'/\[img\](.*?)\[\/img\]/is',
		'/\[quote=(.*?)\](.*?)\[\/quote\]/is',
		'/\[quote\](.*?)\[\/quote\]/is',
	);

	$replaces = array(
		'<br />',
		'str_replace(array("[","]"),array("&#91;","&#93;"),\'\\1\')',
		'<b>\\1</b>',
		'<i>\\1</i>',
		'<span style="text-decoration:underline;">\\1</span>',
		'<del>\\1</del>',
		'<a href="\\1">\\2</a>',
		'<a href="\\1">\\1</a>',
		'<img src="\\1" alt="" />',
		'<small>Quote from \\1:</small><blockquote>\\2</blockquote>',
		'<small>Quote:</small><blockquote>\\1</blockquote>'
	);

	$ret = preg_replace($original,$replaces,$string);
	
	$ret = str_replace(array('&#91;','&#93;'),array('[',']'),$ret);
	
	return $ret;
}
?>