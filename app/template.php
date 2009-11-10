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

function template_top($hi)
{
global $menu;
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>bugspray</title>
		<link rel="stylesheet" type="text/css" href="img/style.css" />
		<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="js/jquery.colorPicker.js"></script>
		<script type="text/javascript" src="js/jquery.amwnd.js"></script>
		<script type="text/javascript" src="js/html5.js"></script>
		<script type="text/javascript" src="js/bugspray.js"></script>
	</head>
	
	<body>		
		<div id="fade"></div>
		
		<div id="global_wrapper">
			<header id="header">
				<hgroup class="logo">
					<h1><a href="index.php"><img src="img/logo.png" alt="bugspray" /></a></h1>
				</hgroup>
				<nav>
					<ul>
						<?php
						// preparation for the template system
						$menu = array(
							array(
								'id' => 'issues',
								'name' => 'Issues',
								'url' => 'index.php'
							),
							array(
								'id' => 'projects',
								'name' => 'Projects'
							),
							array(
								'id' => 'activity',
								'name' => 'Activity',
								'url' => 'activity.php'
							),
							array(
								'id' => 'help',
								'name' => 'Help'
							),
							array(
								'id' => 'admin',
								'name' => 'Admin',
								'show' => isadmin(),
								'url' => 'admin.php'
							),
						);
						foreach ($menu as $link)
						{
							echo '<li id="menu_'.$link['id'].'"'.($hi==$link['id']?' class="sel"':'').'><a href="'.(isset($link['url'])?$link['url']:'javascript:void(0)').'">'.(isset($link['url'])?'':'<s>').''.$link['name'].''.(isset($link['url'])?'':'</s>').'</a></li>';
						}
						?>
					</ul>
					<div class="fc"></div>
				</nav>
				<form id="search_wrapper">
					<input type="text" name="search" />
				</form>
				<div id="user_wrapper">
					<div id="user_left">
						<div class="avatar">
							<?php
							echo '<img src="';
							
							if (isloggedin())
								echo getav($_SESSION['uid']);
							else
								echo 'img/guest.png';
							
							echo '" alt="" />';
							?>
						</div>
					</div>
					<div id="user_right">
						<?php
						if (isloggedin())
							echo '<b>'.$_SESSION['username'].'</b> | <a href="profile.php">profile</a> | <a href="user_logout.php">logout</a>';
						else
							echo '<b>Guest</b> | <a href="user_login.php">login</a> | <a href="user_register.php">register</a>';
						?>
					</div>
					<div class="fc"></div>
				</div>
				<div class="fc"></div>
			</header>
			
			<div id="content_wrapper">
				<aside id="nojs">
					<div class="ibox_error">
						Hey there, looks like <strong>you have JavaScript disabled</strong>.
						bugspray's going to be using a lot of JavaScript, but that's when development hits a later stage.
						If you're an admin you'll need to have it on.
					</div>
					<script type="text/javascript">$("#nojs").css({'display':'none'});</script>
				</aside>
				<table class="sidetbl">
					<tr>
						<td class="left">
							<section id="content">
<?php
}

function template_bottom()
{
	global $db_queries, $starttime;
	
	$mtime = explode(' ', microtime());
	$totaltime = $mtime[0] + $mtime[1] - $starttime;
	$t = sprintf('%.3f',$totaltime);
	
	if (!isset($db_queries))
	{
		$q = '0 queries';
	}
	else
	{
		$q = $db_queries;
		
		if ($q == 1)
			$q .= ' query';
		else
			$q .= ' queries';
	}
?>
							</section>
						</td>
						<td class="right">
							<aside id="sidebar">
								Soon, things will exist here
							</aside>
						</td>
					</tr>
				</table>
			</div>
			
			<footer id="footer">
				powered by bugspray | version 0.1-dev | <?php echo $t; ?> seconds | <?php echo $q; ?>
			</footer>
		</div>
	</body>
</html>
<?php
}
?>