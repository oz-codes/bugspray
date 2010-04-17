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

$users = new MTUsers();

class MTUsers
{
	public $client;
	
	function __construct()
	{
		// TEMPORARY: KILL BUGSPRAY COOKIES
		setcookie("bs_username", "", time()-60*60*24*100, "/");
		setcookie("bs_password", "", time()-60*60*24*100, "/");
		setcookie("bs_uid", "", time()-60*60*24*100, "/");
		
		
		
		
		
		// whether the client is logged in
		$this->client->is_logged = false;
		$sessionactive = isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_SESSION['uid']);
		if (isset($_COOKIE['mt_username']) && isset($_COOKIE['mt_password']))
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
				$this->client->is_logged = true;
			}
		}
		
		// whether the client is an admin
		$this->client->is_admin = false;
		if (isset($_SESSION['username']))
		{
			$q = query_uid($_SESSION['uid']);
			$g = $q['group'];
			
			if (db_query_single("SELECT global_admin FROM groups WHERE id = '$g'", "Checking whether the client is an administrator"))
			{
				$this->client->is_admin = true;
			}
		}
	}
	
	public function login($username, $password)
	{		
		$isuser = $this->is_user($username, $password);
		
		if ($isuser)
		{			
			// set the session
			$_SESSION['username'] = stripslashes($username);
			$_SESSION['password'] = $this->generate_password($isuser['salt'], $password);
			$_SESSION['uid'] = $isuser['uid'];
		
			// does the user want to be remembered?
			if (isset($_POST['remember']))
			{
				setcookie("mt_username", $_SESSION['username'], time()+60*60*24*100, "/");
				setcookie("mt_password", $_SESSION['password'], time()+60*60*24*100, "/");
				setcookie("mt_uid", $_SESSION['uid'], time()+60*60*24*100, "/");
			}
			
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
			// kill ze cookies
			if (isset($_COOKIE['mt_username']) && isset($_COOKIE['mt_password']))
			{
				setcookie("mt_username", "", time()-60*60*24*100, "/");
				setcookie("mt_password", "", time()-60*60*24*100, "/");
				setcookie("mt_uid", "", time()-60*60*24*100, "/");
			}
			
			// kill ze session variables
			unset($_SESSION['username']);
			unset($_SESSION['password']);
			unset($_SESSION['uid']);
			
			// kill ze session
			$_SESSION = array();
			session_destroy();
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function is_user($username, $password, $passwordishash=false)
	{		
		$username2 = escape_smart($username);
		
		$result = db_query("SELECT * FROM users WHERE username = '$username2'", 'Checking whether a given username/password matches a user') or die(mysql_error());
		
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
				
				// we got a match for the username
				if ($username == $row['username'])
				{
					$hit = 2;
				}
				
				// the password to check against needs to be generated
				if (!$passwordishash)
				{
					$supposedpass = $this->generate_password($salt, $password);
				}
				else
				{
					$supposedpass = $password;
				}
				
				if ($supposedpass == $row['password'])
				{
					if ($hit == 2)
					{
						$hit = 1; // we got a match for everything!
					}
					else
					{
						$hit = 3; // we only got a match for the password...
					}
				}
			}
			else
			{
				$hit = -1; // for some reason more than one user has this username
			}
		}
		
		if ($hit === 1)
		{
			return array('salt' => $salt, 'uid' => $uid); // should always equate to true
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
}
?>