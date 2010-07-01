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

include("functions.php");
$page->setType('account');
$page->setTitle('Login');

// Error checking
$error = false;
$errors = array();

// Try to login?
if (isset($_POST['submit']))
{
	if ($users->login($_POST['uname'], $_POST['pwd']))
	{		
		// We have success!	
		echo 'You have been logged in.<br /><br /><a href="index.php">Go to your dashboard?</a>';
	}
	else
	{
		$error = true;
		$errors[] = 'You could not be logged in, please check your credidentials.';
	}
}

// Open the form? Or show an error? :O
if (!isset($_POST['submit']) || $error)
{
$page->theme_disable(true);
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
	background: #eee;
	font-size: 12px;
}
*
{
	font-family: 'helvetica neue', helvetica, arial, sans-serif;
}
a
{
	color: #4183c4;
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
form dl, form dt
{
	margin: 0;
}
form dd
{
	margin: 4px 0 16px;
}
form label
{
	display: block;
	font-size: 13px;
}
form label.inline
{
	display: inline;
}
form input[type=text], form input[type=password]
{
	padding: 4px;
	width: 482px;
	border: 1px solid #dfdfdf;
	border-radius: 3px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	font-size: 18px;
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
.error
{
	margin: 0 0 12px;
	padding: 4px 6px;
	background: #fdd;
	border: 1px solid #b99;
}
</style>
</head>
<body>
<div id="container">
	<h1>Login to <?php echo $page->sitename ?></h1>
	<div id="back"><a href="index.php">&laquo; back</a></div>
	
	<form action="" method="post">
		
		<?php echo output_errors($errors) ?>
		
		<dl>
			<dt>
				<label for="uname">Username</label>
			</dt>
			<dd>
				<input type="text" id="uname" name="uname" tabindex="1" />
			</dd>
		</dl>
		<dl>
			<dt>
				<label for="pwd">Password</label>
			</dt>
			<dd>
				<input type="password" id="pwd" name="pwd" tabindex="2" />
			</dd>
		</dl>
		
		<input type="submit" name="submit" value="Login" tabindex="4" />
		
		<input type="checkbox" name="remember" id="remember" tabindex="3" />
		<label class="inline" for="remember">Remember me</label>
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
?>