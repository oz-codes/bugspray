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

// any function from functions.php would do
$done = function_exists('db_query');

// the faster the installer loads, the better
function install_callback($buffer)
{
	global $done;
	
	$buffer = str_replace("\r","",$buffer);
	$buffer = str_replace("\n","",$buffer);
	$buffer = str_replace("\t","",$buffer);
	
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

if (!isset($_POST['act']) || $done)
{
ob_start('install_callback');
?>
<?php if (!$done): ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>bugspray installer</title>
		<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="../js/html5.js"></script>
		<link rel="stylesheet" type="text/css" href="installer.css" />
		<style type="text/css">
			header,section,footer,aside,nav,article,figure
			{
				display:block;
			}
			table
			{
				border-spacing:0px;
			}
			td
			{
				border:0px;
				padding:0px;
			}
		</style>
	</head>
	<body>
<?php else: ?>
		<script type="text/javascript">
			var origtitle = document.title;
			document.title = 'bugspray installer';
		</script>
<?php endif; ?>
		<div id="installer_wrap1">
			<img src="<?php echo $done ? 'install/' : ''; ?>bg.jpg" id="installer_bg" alt="" />
			<table id="installer_wrap2">
				<tr>
					<td style="vertical-align:middle;text-align:center;">
						<div id="installer_wrap3" style="<?php echo !$done ? 'display:none;' : ''; ?>">
						<nav id="installer_nav">
							<div class="left disabled"><img src="<?php echo $done ? 'install/' : ''; ?>install_arrow_left.png" alt="" /></div>
							<div class="right enabled"><img src="<?php echo $done ? 'install/' : ''; ?>install_arrow_right.png" alt="" /></div>
							<div style="clear:both;"></div>
						</nav>
						<section id="installer_content">
							<?php if (!$done): ?>
							<div id="installer_content_1" class="installer_content_slide">
								<h2>Welcome to the bugspray installer</h2>
								<p>Hello and thank you for choosing to install bugspray!</p>
								<?php
									$prqmsg = '';
									if (!function_exists('json_encode'))
									{
										$prqerr = true;
										$prqmsg .= 'Your PHP version needs to be at least 5.2.0 to run bugspray.<br />';
									}
									if (!is_writable('../settings.php'))
									{
										$prqerr = true;
										$prqmsg .= 'The <code>settings.php</code> file does not exist or is not writable (CHMOD 777 on UNIX systems).<br />';
									}
									
									if ($prqerr)
									{
										echo '<div id="installer_prq_error">'.$prqmsg.'<br />You may continue if you wish, however errors will likely occur.</div>';
									}
									else
									{
										echo '<p>This install won\'t take long at all, in fact, the prerequisite checks just passed :)</p>';
										echo '<p>Let\'s keep moving! Click the arrow button over there on the right.</p>';
									}
								?>
							</div>
							<div id="installer_content_2" class="installer_content_slide">
								<h2>Install configuration</h2>
								<p>Just fill out the form to get started. You can change more stuff after you install<br />
								bugspray. Make sure the details are correct, as the next step will install bugspray!</p>
								<form id="installer_form">
									<table id="installer_tableform">
										<tr>
											<td>MySQL server name</td>
											<td><input type="text" name="mysql_server" value="localhost" /></td>
										</tr>
										<tr>
											<td>Database name</td>
											<td><input type="text" name="mysql_database" /></td>
										</tr>
										<tr>
											<td>Database username</td>
											<td><input type="text" name="mysql_username" /></td>
										</tr>
										<tr>
											<td>Database password</td>
											<td><input type="text" name="mysql_password" /></td>
										</tr>
										<tr>
											<td colspan="2"><hr /></td>
										</tr>
										<tr>
											<td>Bugspray username</td>
											<td><input type="text" name="bugspray_username" /></td>
										</tr>
										<tr>
											<td>Bugspray e-mail</td>
											<td><input type="text" name="bugspray_email" /></td>
										</tr>
										<tr>
											<td>Bugspray password</td>
											<td><input type="password" name="bugspray_password" /></td>
										</tr>
									</table>
								</form>
							</div>
							<div id="installer_content_3" class="installer_content_slide">
								<h2>Installing bugspray...</h2>
								<p>This won't take long, sit tight...</p>
								<p id="installer_steps">
									<div id="mysqltest">MySQL connection test... <img src="loading.gif" alt="" /></div>
									<div id="settings" style="display:none;">Saving settings file... <img src="loading.gif" alt="" /></div>
									<div id="mysqlinstall" style="display:none;">Populating database... <img src="loading.gif" alt="" /></div>
									<div id="adduser" style="display:none;">Adding initial user... <img src="loading.gif" alt="" /></div>
								</p>
								<div id="installer_error" style="display:none;">
									<b>Install did not complete successfully, message given was:</b>
									<div id="installer_error_message"></div>
								</div>
							</div>
							<?php else: ?>
							<div id="installer_content_1" class="installer_content_slide">
								<h2>Installing bugspray...</h2>
								<p>This won't take long, sit tight...</p>
								<p>
									MySQL connection test... <img src="install/tick.png" alt="" /><br />
									Saving settings file... <img src="install/tick.png" alt="" /><br />
									Populating database... <img src="install/tick.png" alt="" /><br />
									Adding initial user... <img src="install/tick.png" alt="" />
								</p>
							</div>
							<div id="installer_content_2" class="installer_content_slide">
								<h2>Done!</h2>
								<p><img src="install/done.png" alt="" /></p>
								<p>Hooray! Bugspray's been successfully installed!</p>
								<p><b>For security reasons please delete the <code>install</code> folder!</b></p>
							</div>
							<?php endif; ?>
						</section>
						</div>
						<script type="text/javascript">
							<?php if (!$done): ?>
							$("#installer_wrap3").fadeIn(2000);
							<?php endif; ?>
							
							var page = 1;
							var offset = 0;
							var offsets = [];
							var slidewidth = 640;
							
							$(".installer_content_slide").each(function(){
								i = $(this).attr('id').replace('installer_content','');
								offsets[i] = offset;
								$(this).css({'left':offset+8+'px'});
								offset += slidewidth+8;
							});
							
							$("#installer_nav .left").click(function(){
								if ($(this).hasClass('enabled'))
								{
									page -= 1;
									refreshPages();
								}
							});
							$("#installer_nav .right").click(function(){
								if ($(this).hasClass('enabled'))
								{
									page += 1;
									<?php if ($done): ?>
									if (page != 3)
									{
									<?php endif; ?>
									refreshPages();
									<?php if ($done): ?>
									}
									else
									{
									document.title = origtitle;
									$("#installer_wrap1").fadeOut(2000);
									}
									<?php endif; ?>
								}
							});
							
							refreshPages();
							function refreshPages()
							{
								$("#installer_nav .left").removeClass('enabled').removeClass('disabled');
								$("#installer_nav .right").removeClass('enabled').removeClass('disabled');
								
								if (page == 1)
								{
									$("#installer_nav .left").addClass('disabled');
									$("#installer_nav .right").addClass('enabled');
								}
								if (page == 2)
								{
									$("#installer_nav .left").addClass('enabled');
									$("#installer_nav .right").addClass('enabled');
								}
								<?php if (!$done): ?>
								if (page == 3)
								{
									$("#installer_nav .left").addClass('disabled');
									$("#installer_nav .right").addClass('disabled');
									setTimeout(installrun,$.fx.speeds._default);
								}
								<?php endif; ?>
								
								$(".installer_content_slide").each(function(){
									i = $(this).attr('id').replace('installer_content','');
									$(this).animate({'left':offsets[i]-(page-1)*slidewidth+8},$.fx.speeds._default);
								});
							}
							
							<?php if (!$done): ?>
							function installcomponent(type,successfunc)
							{
								$("#"+type).show();
								
								$.ajax({
									type: 'post',
									url: 'index.php',
									data: 'act='+type+'&'+$("#installer_form").serialize(),
									dataType: 'json',
									success: function(data){
										window.location.hostname == '127.0.0.1' || window.location.hostname == 'localhost' ? delay = 250 : delay = 0;
										
										setTimeout(function(){
											if (!data.success)
											{
												$("#"+type+" img").attr({'src':'cross.png'});
												
												$("#installer_error_message").html(data.message);
												$("#installer_error").slideDown();
												
												$("#installer_nav .left").removeClass('disabled').addClass('enabled');
											}
											else
											{
												$("#"+type+" img").attr({'src':'tick.png'});
												successfunc();
											}
										}, delay);
									}
								});
							}
							function installrun()
							{
								installcomponent('mysqltest',function(){
									installcomponent('settings',function(){
										installcomponent('mysqlinstall',function(){
											installcomponent('adduser',function(){
												location.href = location.href.substring(0,location.href.length-8)+'?installerdone';
											});
										});
									});
								});
							}
							<?php endif; ?>
						</script>
					</td>
				</tr>
			</table>
		</div>
<?php if (!$done): ?>
	</body>
</html>
<?php endif; ?>
<?php
ob_end_flush();
}
elseif ($_POST['act'] == 'mysqltest')
{
	$arr = array();
	
	$con = mysql_connect($_POST['mysql_server'],$_POST['mysql_username'],$_POST['mysql_password']);
	if (!$con)
	{
		$arr['success'] = false;
		$arr['message'] = 'Could not connect to the MySQL server with provided details, reason: ' . mysql_error();
	}
	else
	{
		$dbcon = mysql_select_db($_POST['mysql_database']);
		if (!$dbcon)
		{
			$arr['success'] = false;
			$arr['message'] = 'Could not select provided database name in MySQL, reason: ' . mysql_error();
		}
		else
		{
			$arr['success'] = true;
		}
	}
	
	echo json_encode($arr);
}
elseif ($_POST['act'] == 'settings')
{
	$arr = array();
	
	$mysql_server   = $_POST['mysql_server'];
	$mysql_username = $_POST['mysql_username'];
	$mysql_password = $_POST['mysql_password'];
	$mysql_database = $_POST['mysql_database'];
	$mysql_prefix   = $_POST['mysql_prefix'];
	
	$sfstr =
"<?php
\$mysql_server   = '$mysql_server';
\$mysql_username = '$mysql_username';
\$mysql_password = '$mysql_password';
\$mysql_database = '$mysql_database';
\$mysql_prefix   = '$mysql_prefix'; // not implemented

\$recaptcha_use = false; // this will probably be moved to the database
\$recaptcha_key_public = '';
\$recaptcha_key_private = '';
?>";
	
	$sffile = fopen('../settings.php','w');
	if ($sffile)
	{
		if (fwrite($sffile,$sfstr))
		{
			$arr['success'] = true;
		}
		else
		{
			$arr['success'] = false;
			$arr['message'] = 'The <code>settings.php</code> file could not be written to.';
		}
	}
	else
	{
		$arr['success'] = false;
		$arr['message'] = 'The <code>settings.php</code> file could not be opened.';
	}
	
	fclose($sffile);
	
	echo json_encode($arr);
}
elseif ($_POST['act'] == 'mysqlinstall')
{
	$arr = array();
	
	$dumpfile = fopen('dump.sql','r');
	$dumpstr = fread($dumpfile,filesize('dump.sql'));
	$dumparr = explode(";\r\n",$dumpstr);
	
	if (count($dumparr) < 3)
	{
		$dumparr = explode(";\n",$dumpstr);
	}
	
	fclose($dumpfile);

	$arr['success'] = true;
	
	mysql_connect($_POST['mysql_server'],$_POST['mysql_username'],$_POST['mysql_password']);
	mysql_select_db($_POST['mysql_database']);
	
	foreach ($dumparr as $dumpquery)
	{
		if ($arr['success'] && str_replace(' ','',$dumpquery) != $dumpquery)
		{
			if (!mysql_query($dumpquery))
			{
				$arr['success'] = false;
				$arr['message'] = 'Could not populate the database, reason: '.mysql_error();
			}
		}
	}
	
	echo json_encode($arr);
}
elseif ($_POST['act'] == 'adduser')
{
	$arr = array();
	
	$u = $_POST['bugspray_username'];
	$s = md5(rand(0,9001));
	$p = hash('whirlpool',$s.$_POST['bugspray_password']);
	$e = $_POST['bugspray_email'];
	
	mysql_connect($_POST['mysql_server'],$_POST['mysql_username'],$_POST['mysql_password']);
	mysql_select_db($_POST['mysql_database']);
	
	if (mysql_query("INSERT INTO users (username,password,password_salt,when_registered,email,avatar_type,avatar_location,`group`) ".
					"VALUES ('$u','$p','$s',NOW(),'$e',1,'img/defaultava.png',2)"))
	{
		$arr['success'] = true;
	}
	else
	{
		$arr['success'] = false;
		$arr['message'] = 'Could not add the initial user account, reason: '.mysql_error().'<br /><br /><b>The database has been cleared</b>';
		
		$num_tables = mysql_list_tables($_POST['mysql_database']);
		while($row = mysql_fetch_row($num_tables))
		{
			$delete_table = mysql_query("DROP TABLE IF EXISTS {$row[0]}");
		}
	}
	
	echo json_encode($arr);
}
?>