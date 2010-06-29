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
$page->setType('account');
$page->setTitle('Login');

if (!isset($_POST['submit']))
{
$page->disableTemplate();
?>
<!DOCTYPE html>
<html>
<head>
<title>Login to <?php echo $page->sitename ?></title>
<style type="text/css">
html, body
{
	margin: 0;
	padding: 0;
}
body
{
	background: #e9e9e9;
	font-family: verdana, arial, sans-serif;
	font-size: 12px;
}
a
{
	text-decoration: none;
}
a:hover
{
	text-decoration: underline;
}
#container
{
	margin: 48px auto;
	width: 540px;
}
h1
{
	float: left;
	font-size: 20px;
}
#back
{
	float: left;
	margin: 22px 0 0 8px;
}
form
{
	clear: both;
	padding: 24px;
	background: #fff;
	border: 1px solid #ccc;
	border-radius: 6px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
}
dl, dt
{
	margin: 0;
}
dd
{
	margin: 4px 0 16px;
}
footer
{
	display: block;
	margin-top: 8px;
	color: #aaa;
	font-size: 10px;
}
footer a
{
	color: #888;
}
#powered
{
	float: left;
}
#by
{
	float: right;
}
</style>
</head>
<body>
<div id="container">
	<h1>Login to <?php echo $page->sitename ?></h1>
	<div id="back"><a href="index.php">&laquo; back</a></div>
	
	<form action="" method="post">
		<dl>
			<dt>
				<label for="uname" class="big">Username</label>
			</dt>
			<dd>
				<input type="text" id="uname" name="uname" class="big" tabindex="1" />
			</dd>
		</dl>
		<dl class="form big">
			<dt>
				<label for="pwd" class="big">Password</label>
			</dt>
			<dd>
				<input type="password" id="pwd" name="pwd" class="big" tabindex="2" />
			</dd>
		</dl>
		
		<input type="submit" name="submit" value="Login" />
		<input type="checkbox" name="remember" id="remember" /> <label class="subtitle" for="remember">Remember me</label>
	</form>
	
	<footer>
		<div id="powered">powered by <a href="http://github.com/a2h/bugspray">spray</a> 0.3-dev</div>
		<div id="by">a project by <a href="http://a2h.uni.cc/">a2h</a></div>
	</footer>
</div>
</body>
</html>
<?php
}
else
{	
	if ($users->login($_POST['uname'], $_POST['pwd']))
	{		
		// now show the message		
		echo '<script type="text/javascript">$("#menu_admin").hide();$("#menu_admin").fadeIn();</script>';
		echo 'You have been logged in.<br /><br /><a href="index.php">Go to your dashboard?</a>';
	}
	else
	{
		echo 'Could not log you in. Check your credidentials?';
	}
}
?>