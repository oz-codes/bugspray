<?php
/**
 * spray issue tracking software
 * Copyright (c) 2009-2010 a2h - http://a2h.uni.cc/
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
 */

include('sp-core.php');

if (isset($_GET['id']) && $_GET['id'] != $_SESSION['uid'])
{
	if (is_numeric($_GET['id']))
	{
		if ($users->client->is_admin)
		{
			$id = $_GET['id'];
		}
		else
		{
			$id = false;
		}
	}
}
else
{
	$id = $_SESSION['uid'];
}

$profile = db_query_single("SELECT users.*, groups.name AS group_name FROM users LEFT JOIN groups ON groups.id = users.group WHERE users.id = '$id'");

// bad id? non-admin? don't go through! oh, and make sure the profile exists!
if ($id && $profile)
{
$page->setType('account');
$page->setTitle($profile['username'] . '\'s account');

$subpage = $_GET['p'];
?>

<div class="imgtitle imgtitle-32">
	<img class="image" src="<?php echo $location['images'] ?>/titles/account.png" alt="" />
	<img src="<?php echo getav($id); ?>" style="position: absolute; top: 16px; left: 16px; width: 16px; height: 16px;" alt="" />
	<div class="text">
		<h1><?php echo $profile['username']; ?>'s account</h1>
	</div>
	<div class="clear"></div>
</div>

<div class="tabs">
	<a href="account.php"<?php echo !$subpage ? ' class="sel"' : '' ?>>Home</a>
	<a href="account.php?p=pms"<?php echo $subpage == 'pms' ? ' class="sel"' : '' ?>><s>Private Messages</s></a>
	<a href="account.php?p=avatar"<?php echo $subpage == 'avatar' ? ' class="sel"' : '' ?>>Avatar</a>
	<a href="account.php?p=login"<?php echo $subpage == 'login' ? ' class="sel"' : '' ?>>Email & Password</a>
	<div class="clear"></div>
</div>

<?php
if (!$subpage)
{
	$subpage = 'home';
}

switch ($subpage)
{
	case 'home':
		echo '<p>This is a temporary home page for your account settings.
		There\'s nothing to show here yet, perhaps later in mintytracker\'s life there will be. Until then, take a look at the tabs above.</p>';
		break;
	
	case 'avatar':
		echo '
		<form action="" method="post">			
			<p>There is currently no custom avatar functionality. If you wish to change your avatar,
			please visit the <a href="http://gravatar.com">Gravatar website</a>.</p>
			<p>Please be aware that currently avatars are restricted to <strong>G</strong>,
			and this cannot be changed by the administrators of this tracker currently as the functionality
			has not been implemented yet.</p>
			<p>
				<input type="radio" name="avatar-type" id="avatar-custom" tabindex="1" value="local" disabled />
				<label for="avatar-custom">
				<img src="' . $location['images'] . '/defaultava.png" alt="" style="width: 32px; height: 32px;" />
				<img src="' . $location['images'] . '/defaultava.png" alt="" style="width: 16px; height: 16px;" />
				Custom
				</label>
				<a href="#" tabindex="2">(change)</a>
				<br />
				
				<input type="radio" name="avatar-type" id="avatar-gravatar" tabindex="3" value="gravatar" checked />
				<label for="avatar-gravatar">
				<img src="http://www.gravatar.com/avatar/' . md5($users->client->info['email']) . '?d=identicon&amp;s=32" alt="" />
				<img src="http://www.gravatar.com/avatar/' . md5($users->client->info['email']) . '?d=identicon&amp;s=16" alt="" />
				Gravatar
				</label>
				<a href="http://gravatar.com/" tabindex="4">(change)</a>
			</p>
			<p>
				<input type="submit" value="Save" disabled />
			</p>
		</form>';
		break;
		
	case 'login':
		if (isset($_POST['submit']))
		{
			$error = false;
			
			$email = escape_smart($_POST['email']);
			$password = $_POST['password'];
			
			// change the email?
			if ($email != $users->client->info['email'])
			{
				if (!is_email($email))
				{
					$errors_email[] = 'The email you provided cannot be a valid one, please check it.';
					$error = true;
				}
			}
			else
			{
				$email = '';
			}
			
			// change the show email switch?
			$showemail = strtolower($_POST['email-show']) == 'on' ? 1 : 0;
			if ($showemail != $users->client->info['email_show'])
			{
				$showemail_changed = true;
			}
			
			// change the password?
			if ($password != '')
			{
				// error check: password too short
				if (strlen($password) < 5)
				{
					$errors_password[] = 'Your desired password is too short, please create a longer password.';
					$error = true;
				}
			}
			
			// no errors?
			if (!$error && ($email || $password || $showemail_changed))
			{
				// alright, let's do this!
				$success = true;
				
				if ($email)
				{
					db_query("UPDATE users SET email = '$email' WHERE id = $id", 'Updating the user\'s email') or $success = false;
					
					// show the new email when the form shows up again
					$newemail = $email;
				}
				
				if ($showemail_changed)
				{
					db_query("UPDATE users SET email_show = $showemail WHERE id = $id", 'Updating the user\'s email visibility') or $success = false;
				}
				
				$setpass = $users->generate_password($users->client->info['password_salt'], $_POST['password']);
				if ($password)
				{
					db_query("UPDATE users SET password = '$setpass' WHERE id = $id", 'Updating the user\'s password') or $success = false;
				}
				
				if ($success)
				{
					echo '<div class="alert" style="width: 768px;">Your login details have been updated successfully.</div>';
				}
				else
				{
					echo '<div class="error" style="width: 768px;">Oh dear, something went wrong!<br />' . mysql_error() . '</div>';
				}
			}
		}
		
		if (!isset($showemail))
		{
			$showemail = $users->client->info['email_show'];
		}
		
		echo '
		<form class="config" action="" method="post">			
			<dl class="form big">
				<dt>
					<label for="email">Email</label>
					' . output_errors($errors_email) . '
				</dt>
				<dd>
					<input class="unchanged" type="text" id="email" name="email" value="' . (isset($newemail) ? $newemail : $users->client->info['email']) . '" />
					
					<input type="checkbox" id="email-show" name="email-show"' . ($showemail ? ' checked' : '') . ' />
					<label class="inline" for="email-show">Public</label>
				</dd>
			</dl>
			
			<dl class="form big">
				<dt>
					<label for="password">New password</label>
					' . output_errors($errors_password) . '
				</dt>
				<dd>
					<input type="password" id="password" name="password" value="" /> <span class="subtitle">(leave blank to keep same)</span>
				</dd>
			</dl>
			
			<input type="submit" name="submit" value="Save" disabled />
		</form>';
		break;
}

}
// bad id? non-admin? non-existent profile? it am be 404 tiem
// todo: move this into a dedicated error page. this message is in account.php and ticket.php
else
{
$page->setTitle('Error');
$page->setType('error');
?>
<div class="imgtitle imgtitle-32">
	<img class="image" src="<?php echo $location['images']; ?>/titles/error.png" alt="" />
	<div class="text">
		<h1>Error!</h1>
	</div>
	<div class="clear"></div>
</div>
<p>Oh dear! It looks like you either don't have permission to access this page, or it doesn't exist!</p>
<p>If perhaps your problem is due to permissions, have a look-see if you're logged in. If you are, perhaps
this is a restricted page.</p>
<p>Perhaps it's not permissions? If you typed out the address to this page manually, be sure to check if your spllnig is correct!
Otherwise, pop around to the <a href="./">home page</a> and try and see if you can find what you were looking for.</p>
<?php
}
?>