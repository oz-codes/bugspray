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