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

$users = new SPUsers();

class SPUsers
{
	public $client;
	
	function __construct()
	{
		// Create the client stuff
		$this->client = new SPClient();
	}
	
	public function login($username, $password)
	{		
		$isuser = $this->is_user($username, $password);
		
		if ($isuser)
		{			
			// Set the session stuff
			$_SESSION['username'] = stripslashes($username);
			$_SESSION['password'] = $this->generate_password($isuser['salt'], $password);
			$_SESSION['uid'] = $isuser['uid'];
		
			// Does the user want to be remembered?
			if (isset($_POST['remember']))
			{
				setcookie("mt_username", $_SESSION['username'], time()+60*60*24*100, "/");
				setcookie("mt_password", $_SESSION['password'], time()+60*60*24*100, "/");
				setcookie("mt_uid", $_SESSION['uid'], time()+60*60*24*100, "/");
			}
			
			// Refresh the client info so that user info is grabbed correctly
			$this->client = new SPClient();
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function logout()
	{
		if ($this->client->is_logged)
		{
			// Kill ze cookies
			if (isset($_COOKIE['mt_username']) && isset($_COOKIE['mt_password']))
			{
				setcookie("mt_username", "", time()-60*60*24*100, "/");
				setcookie("mt_password", "", time()-60*60*24*100, "/");
				setcookie("mt_uid", "", time()-60*60*24*100, "/");
			}
			
			// We also kill ze session variables
			unset($_SESSION['username']);
			unset($_SESSION['password']);
			unset($_SESSION['uid']);
			
			// And then ze session
			$_SESSION = array();
			session_destroy();
			
			// And zen ve refresh ze client class, so zat ze user information zis displayed correctly!
			$this->client = new SPClient();
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function is_user($username, $password, $passwordishash=false)
	{
		// Sanity
		$username2 = escape_smart($username);
		
		// It am be query time??
		$result = db_query("SELECT * FROM users WHERE username = '$username2'", 'Checking whether a given username/password matches a user') or die(mysql_error());
		
		// Variables for the checking
		$hit = 0;
		$gotone = false;
		$salt = '';
		$isuser = false;
		
		while ($row = mysql_fetch_array($result))
		{
			$salt = $row['password_salt'];
			$uid = $row['id'];
			
			if (!$gotone)
			{
				$gotone = true;
				
				// We got a match for the username
				if ($username == $row['username'])
				{
					$hit = 2;
				}
				
				// The password we need to check against needs to be generated
				$supposedpass = $passwordishash ? $password : $this->generate_password($salt, $password);
				
				if ($supposedpass == $row['password'])
				{
					if ($hit == 2)
					{
						$hit = 1; // We got a match for everything!
					}
					else
					{
						$hit = 3; // We only got a match for the password...
					}
				}
			}
			else
			{
				$hit = -1; // For some reason more than one user has this username
			}
		}
		
		if ($hit === 1)
		{
			return array('salt' => $salt, 'uid' => $uid); // This should *always* equate to true
		}
		else
		{
			return false;
		}
	}
	
	public function generate_password($salt, $password)
	{
		return hash('whirlpool', $salt.$password);
	}
	
	// Returns an array of user ids
	public function from_group()
	{
		$ret = array();
		$num = func_num_args();
		
		// Did we get nothing?
		if ($num < 1)
		{
			return $ret;
		}
		// We want just one group?
		elseif ($num == 1)
		{
			$id = func_get_arg(0);
			if (is_numeric($id))
			{
				$users = db_query_toarray("SELECT id FROM users WHERE `group` = $id", "Retrieving users from group $id");
				foreach ($users as $user)
				{
					$ret[] = $user['id'];
				}
			}
			else
			{
				// todo: log an error
			}
		}
		// To infinity and beyond!
		else
		{
			foreach (func_get_args() as $id)
			{
				$ret = array_merge($ret, $this->from_group($id));
			}
		}
		
		return $ret;
	}
	
	public function from_admins()
	{
		return $this->from_group(2);
	}
	
	public function id($id)
	{
		if ($id != $_SESSION['uid'])
		{
			global $mtusers;
			
			if (!$mtusers[$id])
			{
				$mtusers[$id] = new SPUser($id);
			}
			
			return $mtusers[$id];
		}
		else
		{
			return $this->client;
		}
	}
}

class SPUser extends SPUsers
{
	public $info, $favorites;
	
	function __construct($id)
	{
		// grab the info
		$this->info = db_query_single("SELECT * FROM users WHERE id = $id", "Retrieving info for user id $id from database");
		
		// the user's name (as in what they are identified by in the browser)
		$this->info['name'] = !$this->info['displayname'] ? $this->info['username'] : $this->info['displayname'];
		
		// for now, avatars are only taken from gravatar
		$this->info['avatar_location'] = 'http://www.gravatar.com/avatar/' . md5($this->info['email']) . '?d=identicon&amp;s=32';
	}
	
	function get_favorites()
	{
		if (!$this->favorites)
		{
			$this->favorites = array('-1');
			
			$favs = db_query("SELECT ticketid FROM favorites WHERE userid = {$this->info['id']}", "Retrieving favorites for user id {$this->info['id']} from database");
			
			while ($fav = mysql_fetch_array($favs))
			{
				$this->favorites[] = $fav['ticketid'];
			}
		}
	}
	
	function get_info($clear=true) //need a better name for this
	{		
		$string = '
		<div class="avatar left" style="margin-right:4px;"><img src="' . $this->info['avatar_location'] . '" alt="" /></div>
		<a href="profile.php?id=' . $this->info['id'] . '" class="username' . ($this->info['banned'] ? ' banned' : '') . '">' . $this->info['name'] . '</a>
		' . ($clear ? '<div class="clear"></div>' : '');
		
		return str_replace(array("\n", "\r", "\r\n", "\t"), '', $string);
	}
}

class SPClient extends SPUser
{
	public $is_logged, $is_admin;
	
	function __construct()
	{		
		// whether the client is logged in
		$this->is_logged = false;
		$sessionactive = !empty($_SESSION['username']) && !empty($_SESSION['password']) && !empty($_SESSION['uid']);
		if (!empty($_COOKIE['mt_username']) && !empty($_COOKIE['mt_password']))
		{
			if (!$sessionactive) // don't set the session repeatedly if it's already set
			{
				$_SESSION['username'] = $_COOKIE['mt_username'];
				$_SESSION['password'] = $_COOKIE['mt_password'];
				$_SESSION['uid'] = $_COOKIE['mt_uid'];
				
				$sessionactive = true;
			}
		}
		if ($sessionactive)
		{
			// okay, session active, but are they a valid user?
			if (!$this->is_user($_SESSION['username'], $_SESSION['password'], true))
			{
				unset($_SESSION['username']);
				unset($_SESSION['password']);
				unset($_SESSION['uid']);
			}
			else
			{
				$this->is_logged = true;
			}
		}
		
		// so if we're logged in, grab our info!
		if ($this->is_logged)
		{
			parent::__construct($_SESSION['uid']);
		}
		
		// whether the client is an admin
		$this->is_admin = false;
		if (isset($_SESSION['username']))
		{
			$info = db_query_single("SELECT global_admin FROM groups WHERE id = '{$this->info['group']}'", "Checking whether the client is an administrator");
			if ($info[0])
			{
				$this->is_admin = true;
			}
		}
	}
}

// old functions for compatibility's sake
function getav($id)
{
	global $users;
	return $users->id($id)->info['avatar_location'];
}

function getunm($id,$link=false)
{
	global $users;
	return $users->id($id)->info['name'];
}

function getuemail($id)
{
	global $users;
	return $users->id($id)->info['email'];
}

function getuinfo($id, $clear=true)
{
	global $users;
	return $users->id($id)->get_info();
}

function getubanned($id)
{
	global $users;
	return $users->id($id)->info['banned'];
}

function getufavs($id)
{
	global $users;
	return $users->id($id)->get_favorites();
}
?>