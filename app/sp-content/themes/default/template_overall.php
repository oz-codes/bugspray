<!DOCTYPE html>
<html lang="en">
<head>
	<?php
	$this->script_enqueue('html5ie');
	$this->script_enqueue('silkyjs', $this->location['theme'] . '/js/spsilky.js', array('jquery'));
	$this->script_enqueue('amwnd', $this->location['theme'] . '/js/jquery.amwnd.js', array('jquery'));
	$this->script_enqueue('jquery-ui-slideronly', $this->location['theme'] . '/js/jquery-ui-1.8.2.slideronly.min.js', array('jquery'));
	$this->output_head();
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo $this->location['styles'] ?>/jquery.ui.css" />
</head>
<body>	
	<div id="wrap">
		<header id="header">
			<h1><a href="index.php"><?php echo $config['sitename'] ?></a></h1>
			
			<div id="user_wrapper">
				<?php if ($users->client->is_logged): ?>
				<div id="user_left">
					<div class="avatar">
						<img src="<?php echo getav($_SESSION['uid']); ?>" alt="" />
					</div>
				</div>
				<div id="user_right">
					<b><a href="profile.php"><?php echo $_SESSION['username']; ?></a></b> | <a href="account.php">account</a> | <a href="user_logout.php">logout</a>
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
				foreach ($this->get_menu() as $item)
				{
					if (!$item['hide'])
					echo '
					<li id="menu_'.$item['id'].'"'.($item['selected']?' class="selected"':'').'>
						<a href="'.(isset($item['url'])?$item['url']:'#').'">'.(isset($item['url'])?'':'<s>').''.$item['name'].''.(isset($item['url'])?'':'</s>').'</a>
					</li>';
				}
				?>
				</ul>
				<div class="clear"></div>
			</nav>
		</header>
		
		<div id="content">
			<noscript id="nojs">
				<aside class="ibox_error">
					Hey there, looks like <strong>you have JavaScript disabled</strong>.
					bugspray's going to be using a lot of JavaScript, but that's when development hits a later stage.
					If you're an admin you'll need to have it on.
				</aside>
			</noscript>
<!-- content start -->
<?php $this->outputContent(); ?>
<!-- content end -->
		</div>
	</div>
</body>
</html>