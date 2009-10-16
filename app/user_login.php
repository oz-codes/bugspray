<?php
/*
 * bugspray
 * Copyright 2009 a2h - http://a2h.uni.cc/
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * http://a2h.github.com/bugspray/
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