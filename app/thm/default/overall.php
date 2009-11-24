<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<?php $this->outputHead(); ?>
	</head>
	
	<body>
		<?php $this->outputBodyPre(); ?>
		
		<div id="fade"></div>
		
		<div id="global_wrapper">
			<header id="header">
				<hgroup class="logo">
					<h1><a href="index.php"><img src="<?php echo $location['theme']; ?>/img/logo.png" alt="bugspray" /></a></h1>
				</hgroup>
				<nav>
					<ul>
						<?php
						foreach ($this->getMenu() as $item)
						{
							echo '
							<li id="menu_'.$item['id'].'"'.($item['selected']?' class="sel"':'').'>
								<a href="'.(isset($item['url'])?$item['url']:'javascript:void(0)').'">'.(isset($item['url'])?'':'<s>').''.$item['name'].''.(isset($item['url'])?'':'</s>').'</a>
							</li>';
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
							<?php echo '<img src="'.(isloggedin() ? getav($_SESSION['uid']) : $location['theme'].'/img/guest.png').'" alt="" />'; ?>
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
					<noscript id="nojs">
						<div class="ibox_error">
							Hey there, looks like <strong>you have JavaScript disabled</strong>.
							bugspray's going to be using a lot of JavaScript, but that's when development hits a later stage.
							If you're an admin you'll need to have it on.
						</div>
					</noscript>
					<?php $this->outputContent(); ?>
				</section>
			</div>
			<aside id="sidebar">
				<?php $this->outputSidebar(); ?>
			</aside>
			<div class="fc"></div>
			
			<script type="text/javascript">
				// fix the content height smaller than sidebar height issue (FIND A BETTER SOLUTION!)
				$("#content").css({'min-height':$("#sidebar").height()+'px'});
			</script>
			
			<footer id="footer">
				powered by bugspray | version 0.1-dev | <?php /*dumb but oh well*/ echo footerinfo('time').' | '.footerinfo('queries'); ?>
			</footer>
		</div>
	</body>
</html>