<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<?php $this->outputHead(); ?>
	</head>
	
	<body>
		<?php $this->outputBodyPre(); ?>
		
		<div id="wrap">
			<header id="header">
				<h1><a href="index.php"><?php echo $this->sitename; ?></a></h1>
				
				<div id="user_wrapper">
					<?php if (isloggedin()): ?>
					<div id="user_left">
						<div class="avatar">
							<img src="<?php echo getav($_SESSION['uid']); ?>" alt="" />
						</div>
					</div>
					<div id="user_right">
						<b><?php echo $_SESSION['username']; ?></b> | <a href="profile.php">profile</a> | <a href="user_logout.php">logout</a>
					</div>
					<div class="clear"></div>
					<?php else: ?>
					<a href="user_login.php">log in</a> or <a href="user_register.php">sign up</a>
					<?php endif; ?>
				</div>
				<div class="clear"></div>
				
				<nav>
					<ul>
					<?php
					foreach ($this->getMenu() as $item)
					{
						if (!$item['hide'])
						echo '
						<li id="menu_'.$item['id'].'"'.($item['selected']?' class="selected"':'').'>
							<a href="'.(isset($item['url'])?$item['url']:'javascript:void(0)').'">'.(isset($item['url'])?'':'<s>').''.$item['name'].''.(isset($item['url'])?'':'</s>').'</a>
						</li>';
					}
					?>
					</ul>
					<div class="clear"></div>
				</nav>
			</header>
			
			<section id="content">
				<noscript id="nojs">
					<div class="ibox_error">
						Hey there, looks like <strong>you have JavaScript disabled</strong>.
						bugspray's going to be using a lot of JavaScript, but that's when development hits a later stage.
						If you're an admin you'll need to have it on.
					</div>
				</noscript>
<!-- content start -->
<?php $this->outputContent(); ?>
<!-- content end -->
			</section>
			
			<footer id="footer">
				powered by bugspray | version 0.1-dev | <?php /*dumb but oh well*/ echo footerinfo('time').' | '.footerinfo('queries'); ?>
			</footer>
		</div>
	</body>
</html>