<?php
/**
 * spray issue tracking software
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

/**
 * @version 0.3
 * @since 0.3
 */
function echo_tags($tags) {
	 $tags = explode(' ', $issue['tags']); // todo: use the separate table for tags instead of one long string
	foreach ($tags as $tag)
	{
		if($tag == "") { continue; }
		echo '<span class="tag"><a href="#">' . $tag . '</a></span>';
	}
}

function sp_update_config()
{
	global $config;
	$config = array();
	$configquery = db_query("SELECT * FROM config", 'Retrieving the configuration');
	while($row = mysql_fetch_array($configquery))
	{
		try
		{
			$config[$row['name']] = $row['value'];
		}
		catch (Exception $e)
		{
			$debug_log[] = array(
				'type' => 'error',
				'success' => false,
				'text' => 'The config setting <i>' . $row['config_value'] . '</i> has an invalid name.'
			);
		}
	}
}

/**
 * @version 0.3
 * @since 0.3
 */
function sp_get_version()
{
	global $sp_version_major, $sp_version_minor, $sp_version_dev;
	return $sp_version_major . '.' . $sp_version_minor . ($sp_version_dev ? '-dev' : '');
}

/**
 * @version 0.3
 * @since 0.3
 */
function sp_die($message, $title='Error!')
{
	// Disable templating if it's already loaded
	global $page;
	if (isset($page))
	{
		$page->theme_disable(true);
	}
	
	// And now for the content...
	ob_start();
	
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="UTF-8" />
		<title><?php echo $title ?></title>
		<link rel="stylesheet" type="text/css" href="sp-includes/_spray.css" />
	</head>
	<div id="container">
	<h1 id="heading"><?php echo $title ?></h1>
	<div id="main">
		<?php echo $message ?>
	</div>
	<footer>
		<div id="powered">powered by <a href="http://github.com/a2h/bugspray">spray</a> <?php echo sp_get_version() ?></div>
		<div id="by">a project by <a href="http://a2h.uni.cc/">a2h</a></div>
	</footer>
	</div>
	<?php
	
	// Strip out all the tabs and all
	$out = ob_get_clean();
	$out = str_replace("\t", '', $out);
	
	// And now to output it!
	die($out);
}

/**
 * @version 0.1
 * @since 0.1
 */
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

/**
 * @version 0.1
 * @since 0.1
 */
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

/**
 * @version 0.1
 * @since 0.1
 */
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

/**
 * @version 0.1
 * @since 0.1
 */
function logwhencmp($a,$b)
{
    if ($a['when'] == $b['when'])
	{
		return 0;
	}
	return ($a['when'] > $b['when']) ? -1 : 1;
}

/**
 * @version 0.1
 * @since 0.1
 */
function query_cats($id) /* queries don't have 9 lives though */
{
	global $queries_cats;
	
	if (!$queries_cats[$id])
	{
		$queries_cats[$id] = db_query_single("SELECT * FROM categories WHERE id = $id", "Retrieving info for category id $id from database");
	}
	
	return $queries_cats[$id];
}

/**
 * @version 0.1
 * @since 0.1
 */
function query_tags($id)
{
	global $queries_tags;
	
	if (!$queries_tags[$id])
	{
		$queries_tags[$id] = db_query_single("SELECT * FROM tags WHERE id = $id", "Retrieving info for tag id $id from database");
	}
	
	return $queries_tags[$id];
}

/**
 * @version 0.1
 * @since 0.1
 */
function gettagnm($id)
{
	$q = query_tags($id);
	return $q['name'];
}

/**
 * @version 0.1
 * @since 0.1
 */
function getcatnm($id)
{
	$q = query_cats($id);
	return $q['name'];
}

/**
 * @version 0.1
 * @since 0.1
 */
function getissnm($id)
{	
	$q = db_query_single("SELECT name FROM issues WHERE id = $id", "Retrieving info for issue id $id from database");
	return $q[0];
}

/**
 * @version 0.2
 * @since 0.1
 */
function getstatuses()
{	
	return array(
                "",
                array('id' => 0, 'type' => 'unassigned', 'name' => 'unassigned'),
		array('id' => 1, 'type' => 'open', 'name' => 'open'),
		array('id' => 2, 'type' => 'assigned', 'name' => 'assigned'),
		array('id' => 3, 'type' => 'resolved', 'name' => 'resolved'),
		array('id' => 4, 'type' => 'postponed', 'name' => 'postponed'),
		array('id' => 5, 'type' => 'declined', 'name' => 'declined'),
		array('id' => 6, 'type' => 'duplicate', 'name' => 'duplicate')
	);
}

/**
 * @version 0.1
 * @since 0.1
 */
function getstatusnm($id)
{
	$statuses = getstatuses();
	return $statuses[$id+1]['name'];
}

/**
 * @version 0.1
 * @since 0.1
 */
function getstatustype($id)
{
	$statuses = getstatuses();
	return $statuses[$id-1]['type'];
}
function getIdFromName($name) {
        $statuses = getstatuses();
        foreach($statuses as $status) {
               if($status['name'] == $name) {
                  return $status['id'];
               }
        }
}

/**
 * @version 0.4
 * @since 0.2
 */
function ticket_list($status, $order='desc', $pinfollowing=false)
{
	global $page, $users;
	if($_COOKIE["current"] !== "") {
           $status = $_COOKIE["current"];
        }
	// Ah, the myriad of status filters
	switch ($status)
	{
		case 'unassigned': $whereclause = 'issues.status = 0'; break;
		case 'assigned': $whereclause = 'issues.status = 2'; break;
		case 'resolved': $whereclause = 'issues.status = 3'; break;
		case 'postponed': $whereclause = 'issues.status = 4'; break;
		case 'declined': $whereclause = 'issues.status = 5'; break;
                case 'duplicate': $whereclause = 'issues.status = 6'; break;
		case 'all': $whereclause = '1'; break; // This seems to be okay, see http://stackoverflow.com/questions/1983655
		case 'open': default: $status = 'open'; $whereclause = '(issues.status = 0 OR issues.status = 1 OR issues.status = 2)'; break;
	}
	
	// If we don't have a proper order defined, just make it descending
	$order = strtoupper($order);
	if ($order != 'ASC' || $order != 'DESC')
	{
		$order = 'DESC';
	}
	
	// Are we logged in?
	$l = $users->client->is_logged;
	
	// Alright, create our lovely little query [TODO(maybe): if possible, rewrite the subquery as a join]
	$query = '
		SELECT issues.*,
		       comments.author AS commentauthor,
			   ' . ($l ? 'favorites.userid AS favorited,' : '') . '
			   (SELECT COUNT(favoritescount.userid) FROM favorites AS favoritescount WHERE ticketid = issues.id) AS favoritecount
		FROM issues
		
		-- This join is used for getting the comment author
		LEFT JOIN comments
			ON comments.issue = issues.id AND comments.when_posted = issues.when_updated
		
		-- This one lets us find out whether the user has favourited the ticket or not
		' . ($l ? '
		LEFT JOIN favorites
			ON favorites.ticketid = issues.id AND favorites.userid = ' . $_SESSION['uid'] : '') . '
		
		WHERE ' . $whereclause . '
		
		ORDER BY issues.when_updated ' . $order;
	
	// And then run it!
	$result_tickets = db_query_toarray($query, false, 'Retrieving all tickets <code>WHERE ' . $whereclause . '</code>');
	
	// Do we want to pin tickets that the client has favourited or has assigned to them?
	if ($pinfollowing && $l)
	{
		// If so, we'll need another query to get those only
		$uid = $_SESSION['uid'];
		$query2 = str_replace(
			'WHERE', 'WHERE ((favorites.ticketid = issues.id AND favorites.userid = ' . $uid . ') OR issues.assign = ' . $uid . ') AND ',
			$query
		);
		
		// And of course, run it
		$result_tickets2 = db_query_toarray($query2, false, 'Retrieving all tickets favourited or assigned to the client <code>WHERE ' . $whereclause . '</code>');
		
		// Have we got anything?
		$c2 = count($result_tickets2);
		if ($c2)
		{
			// We now have to take out all the non pinned tickets from the first query
			$result_tickets2_ids = array();
			for ($i=0; $i<$c2; $i++)
			{
				// We get all the IDs for extraction
				$result_tickets2_ids[] = $result_tickets2[$i]['id'];
				
				// And for convenience, we'll also mark the ticket as pinned now
				$result_tickets2[$i]['pinned'] = true;
			}
			
			// Now for the actual removal
			$c = count($result_tickets);
			for ($i=0; $i<$c; $i++)
			{
				// Do we have a matching ID?
				if (in_array($result_tickets[$i]['id'], $result_tickets2_ids))
				{
					unset($result_tickets[$i]);
				}
				// Nope? We'll mark it as non pinned, then.
				else
				{
					$result_tickets[$i]['pinned'] = false;
				}
			}
			
			// Since unset screws up indexes in arrays we'll need to fix them.
			$result_tickets = array_values($result_tickets);
			
			// We're done now, so we'll just join up the two arrays, et voila!
			$result_tickets = array_merge($result_tickets2, $result_tickets);
		}
	}

	// Look ma, extra variables
	$count = count($result_tickets);
	for ($i=0; $i<$count; $i++)
	{
		// Is the issue favoUrited? (The database uses "favorite" because everyone favoUrs the americans)
		$result_tickets[$i]['favorite'] = $result_tickets[$i]['favorited'] ? true : false;
		
		// The classes of the ticket, of course, we just start off with "ticket"
		$classes = array('ticket');
		
		// The severity shall be addded something like .severity-0
		$classes[] = 'severity-' . $result_tickets[$i]['severity'];
		
		// Pinned ticket? Add the class!
		if ($result_tickets[$i]['pinned'])
		{
			$classes[] = 'pinned';
		}
		
		// Declined or resolved? Add the class!
		if (getstatustype($result_tickets[$i]['status']) == 'declined')
		{
			$classes[] = 'declined';
		}
		elseif (getstatustype($result_tickets[$i]['status']) == 'resolved')
		{
			$classes[] = 'resolved';
		}
		
		// And now we implode the classes into a nice string
		$result_tickets[$i]['classes'] = implode(' ', $classes);
	}

	// Status types
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
			'name' => 'Postponed',
			'type' => 'postponed',
			'sel' => $status == 'postponed' ? true : false
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
		),
                array(
                        'name' => 'Duplicate',
                        'type' => 'duplicate',
                        'sel' => $status == 'duplicate' ? true : false,
                )
	);

	// And we're off!
	ob_start();
	$page->include_template(
		'ticket_list',
		array(
			'type' => $type,
			'statuses' => $statuses,
			'tickets' => $result_tickets
		)
	);
	return ob_get_clean();
}

/**
 * @version 0.2
 * @since 0.2
 */
function hascharacters($string)
{
	return trim($string) == '' ? false : true;
}

/**
 * @version 0.1
 * @since 0.1
 */
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

/**
 * @version 0.1
 * @since 0.1
 */
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

/**
 * @version 0.1
 * @since 0.1
 */
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

/**
 * @version 0.1
 * @since 0.1
 */
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

/**
 * @version 0.4
 * @since 0.1
 */
function parsebbcode($string)
{
	$ret = $string;
	
	// Convert newlines
	$ret = str_replace("\n", '<br />', $ret);
	
	// Convert extra spaces
	while (strstr($ret, '  '))
	{
		$ret = str_replace('  ' , '&nbsp; ', $ret);
	}
	
	// All the standard BBcode stuff
	$ret = preg_replace(
		array(
			'/\[noparse\](.*?)\[\/noparse\]/ise',
			'/\[b\](.*?)\[\/b\]/is',
			'/\[i\](.*?)\[\/i\]/is',
			'/\[u\](.*?)\[\/u\]/is',
			'/\[s\](.*?)\[\/s\]/is',
			'/\[url=(.*?)\](.*?)\[\/url\]/is',
			'/\[url\](.*?)\[\/url\]/is',
			'/(^|[\n ]|<br \/>)([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^\s\,\"<]*)/is', // http://buildinternet.com/?p=8784
			'/(^|[\n ]|<br \/>)([\w]*?)((www)\.[^\s\,\"<]*)/is', // http://buildinternet.com/?p=8784
			'/\[img\](.*?)\[\/img\]/is',
			'/\[quote=(.*?)\](.*?)\[\/quote\]/is',
			'/\[quote\](.*?)\[\/quote\]/is',
			'/\[code\](.*?)\[\/code\]/is'
		),
		array(
			'str_replace(array("[","]"), array("&#91;","&#93;"),\'\\1\')',
			'<b>\\1</b>',
			'<i>\\1</i>',
			'<span style="text-decoration:underline;">\\1</span>',
			'<del>\\1</del>',
			'<a href="\\1" rel="nofollow" target="_blank">\\2</a>',
			'<a href="\\1" rel="nofollow" target="_blank">\\1</a>',
			'\\1<a href="\\3" rel="nofollow" target="_blank">\\3</a>',
			'\\1<a href="\\3" rel="nofollow" target="_blank">\\3</a>',
			'<img src="\\1" alt="" />',
			'<small>Quote from \\1:</small><blockquote>\\2</blockquote>',
			'<small>Quote:</small><blockquote>\\1</blockquote>',
			'<div class="code"><code>\\1</code></div>'
		),
		$ret
	);
	
	// Convert back the &#91; and &#93; instances created while parsing [noparse]
	$ret = str_replace(array('&#91;', '&#93;'), array('[', ']'), $ret);
	
	return $ret;
}

/**
 * @version 0.2
 * @since 0.2
 */
function is_email($string)
{
	// http://stackoverflow.com/questions/1374881
	// yeah, sure, could be using a rfc parser, but is it TRULY necessary? for now, probably not.
	return preg_match('/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i', $string);
}

/**
 * @version 0.2
 * @since 0.2
 */
function output_errors($arr)
{
	$o = '';
	
	if (sizeof($arr) > 0)
	{
		$o .= '
		<div class="clear error">';
		
		$i=0;
		foreach ($arr as $msg)
		{
			$o .= '<div>'.$msg.'</div>';
		}
		
		$o .= '
		</div>';
	}
	
	return $o;
}
