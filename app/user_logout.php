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

if (!isloggedin())
{
	template_top('user');
	echo 'Congratulations, you have just created a paradox. A black hole is currently being formed behind you.';
}
else
{
	// kill ze cookies
	if (isset($_COOKIE['cookuname']) && isset($_COOKIE['cookpwd']))
	{
	   setcookie("bs_username", "", time()-60*60*24*100, "/");
	   setcookie("bs_password", "", time()-60*60*24*100, "/");
	}
	
	// kill ze session variables
	unset($_SESSION['username']);
	unset($_SESSION['password']);
	
	// kill ze session
	$_SESSION = array();
	session_destroy();
	
	template_top('user');
	
	// congraulations, you have helped destroy ze vorld!
	echo 'You have been logged out.<br /><br /><small>[todo maybe: an ajax version]</small>';
}

template_bottom();
?>