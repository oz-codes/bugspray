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
template_top('account');

$id = escape_smart($_GET['u']);
$profile = db_query_single("SELECT users.*, groups.name AS group_name FROM users LEFT JOIN groups ON groups.id = users.group WHERE users.id = '$id'");
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
	<div class="fl" id="profile_listing_wrap">
		<?php
			if ($profile['banned'])
			{
				echo '<span style="font-weight:bold;color:#f00">User is banned (ban is permanent until lifted; ban durations have not been implemented)</span><br />';
			}
		?>
		<dl id="profile_listing">
			<dt>Group</dt>
				<dd>
					<?php echo $profile['group_name']; ?>
				</dd>
			<dt>Email</dt>
				<dd>
					<?php 
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
	</div>
	<div class="fc"></div>
</section>

<hr />

<section id="profile_issues">
	<header>
		<h3>
			<span class="label">Issue(s) posted</span>
			<span class="num"><?php echo $profile['num_posted_issues']; ?></span>
		</h3>
		<div class="fc"></div>
	</header>
	[todo: grab the listing!]
</section>

<hr />

<?php
template_bottom();
?>