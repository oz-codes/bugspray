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

include("functions.php");

if (!isset($_POST['sub']))
{
	template_top('user');
	
	echo '<h2>Login</h2>';
	
	echo '
	<form action="" method="post" class="biglabels">
		<label for="uname">Username</label><br />
		<input type="text" id="uname" name="uname" class="biginput" tabindex="1" />
		
		<br />
		<br />
		
		<label for="pwd">Password</label> <a href="#">(forgot it?)</a><br />
		<input type="password" id="pwd" name="pwd" class="biginput" tabindex="2" />
		
		<br />
		<br />
		
		<input type="submit" name="sub" value="Login" />
		<input type="checkbox" name="remember" /> <span class="subtitle">Remember me</span>
		
		<br /><small>[todo maybe: an ajax version]</small>
	</form>';
}
else
{
	$isuser = isexistinguser($_POST['uname'],$_POST['pwd']);
	
	if ($isuser['hit'] == 1)
	{
		// set the session
		$_SESSION['username'] = stripslashes($_POST['uname']);
		$_SESSION['password'] = genpass($isuser['salt'],$_POST['pwd']);
		$_SESSION['uid'] = $isuser['uid'];
		
		// does the user want to be remembered?
		if (isset($_POST['remember']))
		{
			setcookie("bs_username", $_SESSION['username'], time()+60*60*24*100, "/");
			setcookie("bs_password", $_SESSION['password'], time()+60*60*24*100, "/");
			setcookie("bs_uid", $_SESSION['uid'], time()+60*60*24*100, "/");
		}
		
		// now show the message
		template_top('user');
		
		echo '<script type="text/javascript">$("#menu_admin").hide();$("#menu_admin").fadeIn();</script>';
		echo 'You have been logged in.<br /><br /><a href="index.php">Go to your dashboard?</a>';
	}
	else
	{
		template_top('user');
		echo 'Could not log you in. Check your credidentials?';
	}
}

template_bottom();
?>