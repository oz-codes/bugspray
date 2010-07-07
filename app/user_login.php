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

include('sp-core.php');

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
<title>Login to <?php echo $config['sitename'] ?></title>
<link rel="stylesheet" type="text/css" href="sp-includes/_spray.css" />
</head>
<body>
<div id="container">
	<h1 id="heading">Login to <?php echo $config['sitename'] ?></h1>
	<div id="back"><a href="index.php">&laquo; back</a></div>
	
	<form id="main" action="" method="post">
		
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
		
		<div id="remember-wrap">
			<input type="checkbox" name="remember" id="remember" tabindex="3" />
			<label for="remember">Remember me</label>
		</div>
		
		<input type="submit" id="submit" name="submit" value="Login" tabindex="4" />
		
		<div style="clear: both;"></div>
	</form>
	
	<footer>
		<div id="powered">powered by <a href="http://github.com/a2h/bugspray">spray</a> <?php echo sp_get_version() ?></div>
		<div id="by">a project by <a href="http://a2h.uni.cc/">a2h</a></div>
	</footer>
</div>
</body>
</html>
<?php
}
?>