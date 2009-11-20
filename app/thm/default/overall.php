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
						foreach ($this->getMenu() as $link)
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
							<?php echo '<img src="'.(isloggedin()?getav($_SESSION['uid']):'img/guest.png').'" alt="" />'; ?>
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
			
			<div id="content_wrap">
				<section id="content">
					<noscript id="nojs" class="ibox_error">
						Hey there, looks like <strong>you have JavaScript disabled</strong>.
						bugspray's going to be using a lot of JavaScript, but that's when development hits a later stage.
						If you're an admin you'll need to have it on.
					</noscript>
					<?php echo $this->showContent(); ?>
				</section>
			</div>
			<aside id="sidebar">
				<?php $this->showSidebar(); ?>
			</aside>
			<div style="clear:both;"></div>
			
			<footer id="footer">
				powered by bugspray | version 0.1-dev | <?php /*dumb but oh well*/ echo footerinfo('time').' | '.footerinfo('queries'); ?>
			</footer>
		</div>
	</body>
</html>