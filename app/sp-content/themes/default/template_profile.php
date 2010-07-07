<div class="imgtitle imgtitle-32">
	<img class="image" src="<?php echo $profile['avatar_location'] ?>" alt="" />
	<div class="text">
		<h1><?php echo $profile['username'] ?>'s Profile</h1>
	</div>
	<div class="right">
		<a class="button" href="account.php?<?php echo $_GET['id'] != $_SESSION['id'] ? '?id=' . $_GET['id'] : '' ?>">
			<img src="<?php echo $location['images'] ?>/btn/edit.png" alt="" />Edit
		</a>
		<a class="button" href="javascript:alert('not implemented yet')">
			<img src="<?php echo $location['images'] ?>/btn/mail.png" alt="" /><s>Message</s>
		</a>
	</div>
	<div class="clear"></div>
</div>

<section id="profile_overview">
	<dl id="profile_listing" class="details">
		<dt>Group</dt>
			<dd>
				<?php echo $profile['group'] ?>
			</dd>
		<dt>Email</dt>
			<dd>
				<?php
					if ($profile['email'] != '')
					{
						if ($profile['email_show'] || $users->client->is_admin)
						{
							if (!$profile['email_show'])
							{
								echo '<span style="font-style:italic;">'.$profile['email'].'</span>';
							}
							else
							{
								echo $profile['email'];
							}
						}
						else
						{
							echo '<span style="font-style:italic;">(hidden)</span>';
						}
					}
					else
					{
						echo '<span style="font-style:italic;">(none)</span>';
					}
				?>
			</dd>
		<dt>Last seen</dt>
			<dd>
				not implemented
			</dd>
		<dt>Member since</dt>
			<dd>
				<?php echo timehtml5($profile['when_registered']) ?>
			</dd>
	</dl>
	<div class="fc"></div>
	<?php
		if ($profile['banned'])
		{
			echo '<div class="ibox_error" style="width:768px;margin-top:4px;padding:4px 8px;">User is banned (ban is permanent until lifted; ban durations have not been implemented)</div>';
		}
	?>
</section>

<section id="profile_issues">
	<header>
		<h3>
			<span class="label">Issue(s) posted</span>
			<span class="num"><?php echo $profile['num_posted_issues'] ?></span>
		</h3>
		<div class="fc"></div>
	</header>
	<?php
	$result_issues_profile = db_query("SELECT * FROM issues WHERE author = '$id' ORDER BY num_comments DESC");
	if (mysql_num_rows($result_issues_profile) > 0)
	{
		echo '<div style="font-size:12px;">';
		while ($issue_profile = mysql_fetch_array($result_issues_profile))
		{
			echo '
			<div class="left clear" style="background:#ddd;font-weight:bold;padding:4px 6px;width:16px;text-align:center;margin-right:8px;">'.$issue_profile['num_comments'].'</div>
			<div class="left" style="margin-top:4px;"><a href="view_issue.php?id='.$issue_profile['id'].'">'.$issue_profile['name'].'</a></div>';
		}
		echo '<div class="clear"></div>
		</div>';
	}
	else
	{
		echo 'Well, obviously nothing to see here!';
	}
	?>
	<small>[todo: larger listing style - inbetween main listing size and sidebar size]</small>
</section>