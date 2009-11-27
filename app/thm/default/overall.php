<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<?php $this->outputHead(); ?>
	</head>
	
	<body>
		<?php $this->outputBodyPre(); ?>
		
		<div id="global_wrapper">
			<header id="header">
				<header class="top">
					<div class="bg"></div>
					<h1><a href="index.php">my unnamed issue tracker</a></h1>
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
						<div class="fc"></div>
						<?php else: ?>
						<form action="user_login.php" method="post">
							<input type="text" name="uname" value="username" class="unsel" />
							<input type="password" name="pwd" value="password" class="unsel" />
							<input type="submit" name="sub" value="login" />
							<input type="button" value="register" onclick="location.href='user_register.php'" />
							<div class="fc"></div>
						</form>
						<?php endif; ?>
					</div>
					<div class="fc"></div>
				</header>
				<nav>
					<ul>
					<?php
					foreach ($this->getMenu() as $item)
					{
						if (!$item['hide'])
						echo '
						<li id="menu_'.$item['id'].'"'.($item['selected']?' class="sel"':'').'>
							<a href="'.(isset($item['url'])?$item['url']:'javascript:void(0)').'">'.(isset($item['url'])?'':'<s>').''.$item['name'].''.(isset($item['url'])?'':'</s>').'</a>
						</li>';
					}
					?>
					</ul>
					<div class="fc"></div>
				</nav>
			</header>
			
			<div id="content_wrap">
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
			</div>
			<aside id="sidebar">
				<?php $this->outputSidebar(); ?>
			</aside>
			<div class="fc"></div>
			
			<script type="text/javascript">
				// fix the content height smaller than sidebar height issue (FIND A BETTER SOLUTION!)
				ch();function ch(){$("#content").css({'min-height':($(window).height()-168)+'px'})}setInterval(ch,1000);
			</script>
			
			<footer id="footer">
				powered by bugspray | version 0.1-dev | <?php /*dumb but oh well*/ echo footerinfo('time').' | '.footerinfo('queries'); ?>
			</footer>
		</div>
	</body>
</html>