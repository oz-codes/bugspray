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

include("functions.php");
$page->setType('account');

$id = isset($_GET['u']) ? escape_smart($_GET['u']) : $_SESSION['uid'];

$profile = db_query_single("SELECT users.*, groups.name AS group_name FROM users LEFT JOIN groups ON groups.id = users.group WHERE users.id = '$id'");

$page->setTitle($profile['username'].'\'s Profile');
?>

<header id="profile_header">
	<div class="avatar_large">
		<img src="<?php echo getav($id); ?>" />
	</div>
	<div class="heading">
		<h2><?php echo $profile['username']; ?>'s Profile</h2>
	</div>
	<div class="fc"></div>
</header>
<!--<div class="fr">
	<button type="button"><img src="img/btn/mail.png" alt="" />Message</button>
</div>
<div class="fc"></div>-->

<section id="profile_overview">
	<dl id="profile_listing" class="ibox_details">
		<dt>Group</dt>
			<dd>
				<?php echo $profile['group_name']; ?>
			</dd>
		<dt>Email</dt>
			<dd>
				<?php
					if ($profile['email'] != '')
					{
						if ($profile['email_show'] || isadmin())
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
				<?php echo timehtml5($profile['when_registered']); ?>
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
			<span class="num"><?php echo $profile['num_posted_issues']; ?></span>
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
			<div class="fl fc" style="background:#ddd;font-weight:bold;padding:4px 6px;width:16px;text-align:center;margin-right:8px;">'.$issue_profile['num_comments'].'</div>
			<div class="fl" style="margin-top:4px;"><a href="view_issue.php?id='.$issue_profile['id'].'">'.$issue_profile['name'].'</a></div>';
		}
		echo '<div class="fc"></div>
		</div>';
	}
	else
	{
		echo 'Well, obviously nothing to see here!';
	}
	?>
	<small>[todo: larger listing style - inbetween main listing size and sidebar size]</small>
</section>