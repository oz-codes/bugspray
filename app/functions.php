<?php
/*
 * bugspray issue tracking software
 * Copyright (c) 2009 a2h - http://a2h.uni.cc/
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
 *
 */

// important stuff here
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

session_start();

include("settings.php");
include("template.php");

$con = mysql_connect($mysql_server,$mysql_username,$mysql_password);
if (!$con) { die(mysql_error()); }
mysql_select_db($mysql_database, $con);

$islogged = true;

// constants
$datetimenull = '0000-00-00 00:00:00';

// functions begin here
function db_query($query)
{	
	global $db_queries;
	$result = mysql_query($query);
	
	if ($result)
		++$db_queries;
	
	return $result;
}

function db_query_single($query)
{
	if (strstr($query,"LIMIT 1")) { exit('fix this'); } // temporary line added after this function changed to what it is now
	$array = mysql_fetch_array(db_query($query." LIMIT 1"));
	return $array;
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
		$queries_uid[$id] = db_query_single("SELECT * FROM users WHERE id = $id");
	}
	
	return $queries_uid[$id];
}

function query_acttypes($id)
{
	global $queries_acttypes;
	
	if (!$queries_acttypes[$id])
	{
		$queries_acttypes[$id] = db_query_single("SELECT * FROM actiontypes WHERE id = $id");
	}
	
	return $queries_acttypes[$id];
}

function query_cats($id) //meow
{
	global $queries_cats;
	
	if (!$queries_cats[$id])
	{
		$queries_cats[$id] = db_query_single("SELECT * FROM categories WHERE id = $id");
	}
	
	return $queries_cats[$id];
}

function getuid($unm)
{
	if ($unm == $_SESSION['username'])
	{
		// added for sake of compatibility with old code; please don't use this function like this: getuid($_SESSION['username'])
		// if you want to do something like that just use $_SESSION['uid']
		return $_SESSION['uid'];
	}
	else
	{
		$q = db_query_single("SELECT id FROM users WHERE username = '$unm'");
		return $q[0];
	}
}

function getav($id)
{
	$q = query_uid($id);
	return $q['avatar_location'];
}

function getunm($id)
{	
	$q = query_uid($id);
	
	if ($q['displayname'] == '')
		return $q['username'];
	else
		return $q['displayname'];
}

function getuemail($id)
{	
	$q = query_uid($id);
	return $q['email'];
}

function getuinfo($id,$clear=true)
{
	return '
	<div class="avatar fl" style="margin-right:4px;"><img src="'.(getubanned($id)?'img/avban.png':getav($id)).'" alt="" /></div>
	<a href="profile.php?u='.$id.'" style="font-size:12px;position:relative;top:2px;'.(getubanned($id)?'color:#999;':'').'">'.getunm($id).'</a>
	'.($clear?'<div class="fc"></div>':'');
}

function getubanned($id)
{
	$q = query_uid($id);
	return $q['banned'];
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

function getcattag($id)
{
	return '<span class="cat" style="background:#'.getcatcol($id).';">'.getcatnm($id).'</span>';
}

function getcatcol($id)
{
	$q = query_cats($id);
	return $q['color'];
}

function getcatnm($id)
{	
	$q = query_cats($id);
	return $q['name'];
}

function getprojnm($id)
{	
	$q = db_query_single("SELECT name FROM projects WHERE id = $id");
	return $q[0];
}

function getissnm($id)
{	
	$q = db_query_single("SELECT name FROM issues WHERE id = $id");
	return $q[0];
}

function getstatusnm($id)
{
	switch ($id)
	{
		case 0: return 'open'; break;
		case 1: return 'assigned'; break;
		case 2: return 'resolved'; break;
		case 3: return 'duplicate'; break;
		case 4: return 'bydesign'; break;
		case 5: return 'declined'; break;
		case 6: return 'nonissue'; break;
		case 7: return 'spam'; break;
		default: return '?'; break;
	}
}

function issueclosed($id)
{
	switch ($id)
	{
		case 0: return false; break;
		case 1: return false; break;
		case 2: return false; break;
		case 3: return true; break;
		case 4: return true; break;
		case 5: return true; break;
		case 6: return true; break;
		case 7: return true; break;
		default: return false; break;
	}
}

function isexistinguser($uname,$pwd)
{
	global $path;
	
	$uname = escape_smart($uname);
	
	$result = db_query("SELECT * FROM users WHERE username = '$uname'");
	
	/* description of $hit:
	 *  -1 more than one match of the username for some reason
	 *   0 no match for both username/password
	 *   1 match for both username/password
	 *   2 match for username, no match for password
	 *   3 match for password, no match for username
	*/
	
	$hit = 0;
	$rowcounted = false;
	$salt = '';
	
	while($row = mysql_fetch_array($result))
	{
		$salt = $row['password_salt'];
		
		if (!$rowcounted && $hit != -1)
		{
			if ($uname == $row['username'])
			{
				$hit = 2;
			}
			if (genpass($salt,$pwd) == $row['password'])
			{
				if ($hit == 2)
					$hit = 1;
				else
					$hit = 3;
			}
		}
		else
		{
			$hit = -1;
		}
		
		$uid = $row['id'];
	}
	
	return array('hit'=>$hit,'salt'=>$salt,'uid'=>$uid);
}

function isloggedin()
{
	// is the session active?
	$sessionactive = isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_SESSION['uid']);
	
	// is the user set to remember?
	if (isset($_COOKIE['bs_username']) && isset($_COOKIE['bs_password']))
	{
		// don't set the session repeatedly if it's already set
		if (!$sessionactive)
		{
			$_SESSION['username'] = $_COOKIE['bs_username'];
			$_SESSION['password'] = $_COOKIE['bs_password'];
			$_SESSION['uid'] = $_COOKIE['bs_uid'];
		}
	}

	// user's session is still active
	if ($sessionactive)
	{
		// but is their user/pass pair correct?
		if (isexistinguser($_SESSION['username'], $_SESSION['password']) == 2)
		{
			// NO? gtfo
			unset($_SESSION['username']);
			unset($_SESSION['password']);
			unset($_SESSION['uid']);
			return false;
		}
		else
		{
			return true;
		}
	}
	// looks like they're not active after all...
	else
	{
		return false;
	}
}

function genpass($salt,$pwd)
{
    return hash('whirlpool',$salt.$pwd);
}

function isadmin()
{
	if (isset($_SESSION['username']))
	{
		$q = query_uid($_SESSION['uid']);
		$g = $q['group'];
		
		$q2 = db_query_single("SELECT global_admin FROM groups WHERE id = $g");
		return $q2[0];
	}
	else
	{
		return 0;
	}
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

function timehtml5($timestamp,$innerhtml='[nothingatall]')
{	
	if (gettype($timestamp) == 'string')
	{
		$timestamp = strtotime($timestamp);
	}
	
	$datetime = date(DATE_W3C,$timestamp);
	
	if ($innerhtml != '[nothingatall]')
	{
		return '<time datetime="'.$datetime.'">'.$innerhtml.'</time>';
	}
	else
	{
		return '<time datetime="'.$datetime.'"></time>';
	}
}

function timeago($timestamp,$html5=true)
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
	
    $delta = time() - $timestamp;

    if ($delta < 1 * $minute)
    {
        $ret = $delta == 1 ? "1 second ago" : $delta . " seconds ago";
    }
    elseif ($delta < 2 * $minute)
    {
		$ret = "1 minute ago";
    }
    elseif ($delta < 45 * $minute)
    {
        $ret = floor($delta / $minute) . " minutes ago";
    }
    elseif ($delta < 90 * $minute)
    {
		$ret = "1 hour ago";
    }
    elseif ($delta < 24 * $hour)
    {
		$ret = floor($delta / $hour) . " hours ago";
    }
    elseif ($delta < 48 * $hour)
    {
		$ret = "1 day ago";
    }
    elseif ($delta < 30 * $day)
    {
        $ret = floor($delta / $day) . " days ago";
    }
    elseif ($delta < 12 * $month)
    {
		$months = floor($delta / $day / 30);
		$ret = $months <= 1 ? "1 month ago" : $months . " months ago";
    }
    else
    {
        $years = floor($delta / $day / 365);
        $ret = $years <= 1 ? "1 year ago" : $years . " years ago";
    }
	
	if (!$html5)
	{
		return $ret;
	}
	else
	{
		return timehtml5($timestamp,$ret);
	}
}

function parsebbcode($string)
{	
	$original = array(
		'/\n/',
		'/&/',
		'/(\[(noparse|NOPARSE)\])(.+)(\[\/(noparse|NOPARSE)\])/e',
		'/(\[[Bb]\])(.+)(\[\/[Bb]\])/',
		'/(\[[Ii]\])(.+)(\[\/[Ii]\])/',
		'/(\[[Uu]\])(.+)(\[\/[Uu]\])/',
		'/(\[[Ss]\])(.+)(\[\/[Ss]\])/',
		'/(\[(url|URL)=)(.+)(\])(.+)(\[\/(url|URL)\])/',
		'/(\[(url|URL)\])(.+)(\[\/(url|URL)\])/'
	);

	$replaces = array(
		'<br />',
		'&amp;',
		'str_replace(array("[","]"),array("&#91;","&#93;"),\'\\3\')',
		'<b>\\2</b>',
		'<i>\\2</i>',
		'<span style="text-decoration:underline;">\\2</span>',
		'<del>\\2</del>',
		'<a href="\\3">\\5</a>',
		'<a href="\\3">\\3</a>'
	);

	$ret = preg_replace($original,$replaces,$string);
	
	$ret = str_replace(array('&#91;','&#93;'),array('[',']'),$ret);
	
	return $ret;
}
?>