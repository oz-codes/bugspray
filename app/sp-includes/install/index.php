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

// any function from functions.php would do
$done = function_exists('db_query');

// the faster the installer loads, the better
function install_callback($buffer)
{
	global $done;
	
	$buffer = str_replace("\n\t", "\n ", $buffer);
	$buffer = str_replace(array("\n","\r","\r\n","\t"), '', $buffer);
	
	if (!$done)
	{
		return $buffer;
	}
	else
	{
		global $page;
		$page->addBodyPre($buffer);
	}
}

if ($done)
{
	?>
	<script type="text/javascript">
		var origtitle = document.title;
		document.title = 'spray installer';
	</script>
	<?php
}

function sp_install_header()
{
?>
	<!DOCTYPE html>
	<html>
	
	<head>
		<title>spray installer</title>
		<link rel="stylesheet" type="text/css" href="../_spray.css" />
		<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="../js/html5.js"></script>
	</head>
	
	<body>
	<div id="container">
		<h1 id="heading">spray installer</h1>
		
		<div id="main">
<?php
}

function sp_install_footer()
{
?>
		</div>
		
		<footer>
			<div id="powered">powered by rainbows and unicorns</div>
			<div id="by">a project by <a href="http://a2h.uni.cc/">a2h</a></div>
		</footer>
	</div>
	
	</html>
<?php
}

function sp_install_error($message = '')
{
	global $sp_install_error_message;
	
	if (empty($message))
	{
		return $sp_install_error_message;
	}
	else
	{
		$sp_install_error_message = $message;
	}
}

if (!isset($_POST['act']))
{
ob_start('install_callback');

sp_install_header();

		if (!isset($_GET['step']))
		{
			// Some prerequisite checks
			$success = true;
			$errors = array();
			
			if (!function_exists('json_encode'))
			{
				$success = false;
				$errors[] = 'Spray needs the installed PHP version to be at least 5.2.0';
			}
		?>
			<div class="centre">
				<img src="../logo.png" alt="" />
			</div>
			
			<p>Hello and thank you for choosing to install spray!
			
			<?php if (!$success): ?>
			
				</p>
				
				<h2>Hang on a second!</h2>
				
				<p>Spray's detected one (or more) problems that won't let it install! You'll need to fix them before you can move on!</p>
				
				<ul>
					<?php foreach ($errors as $error) { echo '<li>' . $error . '</li>'; } ?>
				</ul>
				
			<?php else: ?>
			
			Just before we get started, make sure you have the following details ready:</p>
			
			<ol>
				<li>Database host (usually <code>localhost</code>)</li>
				<li>Database name</li>
				<li>Database username</li>
				<li>Database password</li>
			</ol>
			
			<p>The spray installer will need that information to fill in the database with some initial data, and also to create a settings
			file that spray can use after installation.</p>
			
			<p>That information should have been supplied to you by your web host, most likely through its control panels. If they haven't,
			then you should try contacting them.
			
			<p>So, are we ready then? If so...</p>
			
			<p><a class="button" href="./?step=1">Let's get started!</a></p>
			
			<?php endif; ?>
		<?php
		}
		else
		{
			switch ($_GET['step'])
			{
				// MySQL information and user details
				case 1:
					?>
						<p>Just fill out the form to get started. You can change more stuff after you install<br />
						spray. Make sure the details are correct, as the next step will install spray!</p>
						
						<form action="./?step=2" method="post">
							<dl>
								<dt><label>MySQL server</label></dt>
								<dd><input type="text" name="mysql_server" value="localhost" /></dd>
							</dl>
							<dl>
								<dt><label>Database name</label></dt>
								<dd><input type="text" name="mysql_database" /></dd>
							</dl>
							<dl>
								<dt><label>Database username</label></dt>
								<dd><input type="text" name="mysql_username" /></dd>
							</dl>
							<dl>
								<dt><label>Database password</label></dt>
								<dd><input type="text" name="mysql_password" /></dd>
							</dl>
							<dl>
								<dt><label>Your username</label></dt>
								<dd><input type="text" name="spray_username" /></dd>
							</dl>
							<dl>
								<dt><label>Your password</label></dt>
								<dd><input type="password" name="spray_password" /></dd>
							</dl>
							<dl>
								<dt><label>Your e-mail</label></dt>
								<dd><input type="text" name="spray_email" /></dd>
							</dl>
							<input type="submit" />
						</form>
					<?php
					break;
				
				// Installation!
				case 2:
					$mysql_info = array(
						'server' => $_POST['mysql_server'],
						'database' => $_POST['mysql_database'],
						'username' => $_POST['mysql_username'],
						'password' => $_POST['mysql_password'],
						'prefix' => $_POST['mysql_prefix']
					);
				
					$user_info = array(
						'username' => $_POST['spray_username'],
						'password' => $_POST['spray_password'],
						'email' => $_POST['spray_email']
					);
				
					$debug = '<p>Testing the MySQL connection...</p>';
					
					if (!sp_install_mysqltest($mysql_info))
					{
						echo $debug . sp_install_error();
						break;
					}
					
					$debug .= '<p>Writing the settings file...</p>';
					
					if (!sp_install_writeconfig($mysql_info))
					{
						echo $debug . sp_install_error();
						break;
					}
					
					$debug .= '<p>Populating the database...</p>';
					
					if (!sp_install_sql($mysql_info))
					{
						echo $debug . sp_install_error();
						break;
					}
					
					$debug .= '<p>Adding the intial user...</p>';
					
					if (!sp_install_user($mysql_info, $user_info))
					{
						echo $debug . sp_install_error();
						break;
					}
					
					echo '
					<div class="centre">
						<img src="../logo.png" alt="" />
					</div>
					<p>Well, we\'re done! That was quick, wasn\'t it? Enjoy using spray!</p>
					<p><a href="../../">View your issue tracker</a></p>';
					
					break;
			}
		}

sp_install_footer();

ob_end_flush();
}

function sp_install_mysqltest($mysql_info)
{
	global $con;
	
	$success = true;
	$message = '';
	
	// Alright, let's get this going
	try
	{
		$con = @mysql_connect($mysql_info['server'], $mysql_info['username'], $mysql_info['password']);
	}
	catch (Exception $e)
	{
		$con = false;
	}
	
	// Alright, MySQL connection error
	if (!$con)
	{
		sp_install_error('Could not connect to the MySQL server with provided details, reason: ' . mysql_error());
		return false;
	}
	// ... or not?
	else
	{
		// Okay then, try and see if we can get to the database?
		$dbcon = @mysql_select_db($mysql_info['database']);
		if (!$dbcon)
		{
			sp_install_error('Could not select provided database name in MySQL, reason: ' . mysql_error());
			mysql_close($con);
			return false;
		}
	}
	
	mysql_close($con);
	
	// Looks like we made it through safely, phew!
	return true;
}

function sp_install_writeconfig($mysql_info)
{
	$sfstr =
		"<?php
		\$mysql_server   = '{$mysql_info['server']}';
		\$mysql_username = '{$mysql_info['username']}';
		\$mysql_password = '{$mysql_info['password']}';
		\$mysql_database = '{$mysql_info['database']}';
		\$mysql_prefix   = '{$mysql_info['prefix']}'; // not implemented".'

		$debug = false;

		$recaptcha_use = false; // this will probably be moved to the database
		$recaptcha_key_public = \'\';
		$recaptcha_key_private = \'\';
		?>';
	
	$sfstr = str_replace("\t", '', $sfstr);

	// Open the file
	try
	{
		$sffile = @fopen('../../sp-config.php', 'w');
	}
	catch (Exception $e)
	{
		sp_install_error('The <code>sp-config.php</code> file could not be created and/or opened.');
		return false;
	}
	
	// Write the settings
	try
	{
		@fwrite($sffile, $sfstr);
	}
	catch (Exception $e)
	{
		fclose($sffile);
		sp_install_error('The <code>sp-config.php</code> file could not be written to, reason: ' . $e);
		return false;
	}
	
	// The light, I see it!
	return true;
}

function sp_install_sql($mysql_info)
{
	// Does the SQL dump exist?
	if (!file_exists('dump.sql'))
	{
		sp_install_error('The <code>dump.sql</code> file couldn\'t be found!');
		return false;
	}
	
	// Grab the individual queries
	$dumpfile = fopen('dump.sql', 'r');
	$dumpstr = fread($dumpfile,filesize('dump.sql'));
	$dumparr = explode(";\r\n",$dumpstr);
	if (count($dumparr) < 3)
	{
		$dumparr = explode(";\n",$dumpstr);
	}
	fclose($dumpfile);
	
	// Link up to the database
	$con = mysql_connect($mysql_info['server'], $mysql_info['username'], $mysql_info['password']);
	mysql_select_db($mysql_info['database']);
	
	// Query time!
	foreach ($dumparr as $dumpquery)
	{
		if (str_replace(' ','',$dumpquery) != $dumpquery)
		{
			if (!mysql_query($dumpquery))
			{
				sp_install_error('Could not populate the database, reason: ' . mysql_error());
				mysql_close($con);
				return false;
			}
		}
	}
	
	mysql_close($con);
	
	// Survival of the fittest, of course
	return true;
}

function sp_install_user($mysql_info, $user_info, $clearonfail=true)
{
	// Let's set up the info...
	$s = md5(rand(0, 9001));
	$u = $user_info['username'];
	$p = hash('whirlpool', $s.$user_info['password']);
	$e = $user_info['email'];
	
	// Link up to the database
	$con = mysql_connect($mysql_info['server'], $mysql_info['username'], $mysql_info['password']);
	mysql_select_db($mysql_info['database']);
	
	if (!mysql_query("INSERT INTO users (username,password,password_salt,when_registered,email,avatar_type,avatar_location,`group`) ".
					"VALUES ('$u','$p','$s',NOW(),'$e',1,'img/defaultava.png',2)"))
	{
		sp_install_error('Could not add the initial user account, reason: ' . mysql_error() . ($clearonfail ? '<br /><br /><b>The database has been cleared</b>' : ''));
		
		if ($clearonfail)
		{
			$num_tables = mysql_list_tables($_POST['mysql_database']);
			while($row = mysql_fetch_row($num_tables))
			{
				$delete_table = mysql_query("DROP TABLE IF EXISTS {$row[0]}");
			}
		}
		
		mysql_close($con);
		
		return false;
	}
	
	mysql_close($con);
	
	// Awesome! :D
	return true;
}
?>