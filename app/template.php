<?php
/*
 * bugspray issue tracking software
 * Copyright (c) 2009 a2h - http://a2h.uni.cc/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * Under section 7b of the GNU Affero General Public License you are
 * required to preserve this notice. Additional attribution may be
 * found in the NOTICES.txt file provided with the Program.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

function template_top($hi)
{
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
		<div id="nojs">
			Hello! The software running this bugtracker, bugspray, uses a lot of JavaScript.
			It seems you either have it disabled or unsupported. Please enable it if you
			have it disabled, and upgrade your browser if it doesn't support JavaScript!
		</div>
		<script type="text/javascript">$("#nojs").css({'display':'none'});</script>
		
		<div id="fade"></div>
		
		<div id="btn_extend" style="display:none;"><div class="c">Let's have some filler text whee</div><div class="fr"><img src="img/btn/extend_end.png" alt="x" onclick="unextendbtn()" /></div><div class="fc"></div></div>
		
		<div id="global_wrapper">
			<header id="header">
				<hgroup class="logo">
					<h1><a href="index.php"><img src="img/logo.png" alt="bugspray" /></a></h1>
				</hgroup>
				<nav>
					<ul>
						<?php
						echo '<li id="menu_issues"'.($hi=='issues'?' class="sel"':'').'><a href="index.php">Issues</a></li>						
						<li id="menu_projects"'.($hi=='projects'?' class="sel"':'').'><a href="#" class="notyet">Projects</a></li>
						<li id="menu_activity"'.($hi=='log'?' class="sel"':'').'><a href="activity.php">Activity</a></li>
						<li id="menu_help"'.($hi=='help'?' class="sel"':'').'><a href="#" class="notyet">Help</a></li>
						'.(isadmin()?'<li id="menu_admin"'.($hi=='admin'?' class="sel"':'').'><a href="admin.php">Admin</a></li>' : '');
						?>
					</ul>
					<div class="fc"></div>
				</nav>
				<div id="search_wrapper">
					<input type="text" name="search" />
				</div>
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
<!-- content begin -->	
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
<!-- content end -->
			</div>
			
			<footer id="footer">
				powered by bugspray | version 0.0.8 | <?php echo $t; ?> seconds | <?php echo $q; ?>
			</footer>
		</div>
	</body>
</html>
<?php
}
?>