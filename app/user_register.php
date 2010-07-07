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

$page->setType('account');
$page->setTitle('Register');

echo '<h2>Register</h2>';

if ($recaptcha_use)
{
	require_once('sp-includes/recaptchalib.php');
}

if (isset($_POST['submit']))
{
	$error = false;

	// error check: recaptcha
	if ($recaptcha_use)
	{
		$resp = recaptcha_check_answer($recaptcha_key_private,
									$_SERVER["REMOTE_ADDR"],
									$_POST["recaptcha_challenge_field"],
									$_POST["recaptcha_response_field"]);
		if (!$resp->is_valid)
		{
			$recaptcha_error = $resp->error;
			$error = true;
		}
	}
	
	// error check: existing username
	$usercheck = db_query_single("SELECT username FROM users WHERE username='".escape_smart($_POST['uname'])."'");
	if ($usercheck)
	{
		$errors_user[] = 'Your desired username has already been taken, please pick another username.';
		$error = true;
	}
	
	// error check: no characters
	if (!hascharacters($_POST['uname']))
	{
		$errors_user[] = 'Your desired username cannot be exclusively spaces or blank, please pick another username.';
		$error = true;
	}
	
	// error check: username too short
	elseif (strlen($_POST['uname']) < 3)
	{
		$errors_user[] = 'Your desired username is too short, please pick a longer username.';
		$error = true;
	}
	
	// error check: username too long
	elseif (strlen($_POST['uname']) > 24)
	{
		$errors_user[] = 'Your desired username is too long, please pick a shorter username.';
		$error = true;
	}
	
	// error check: html characters
	if ($_POST['uname'] != strip_tags($_POST['uname']) || $_POST['uname'] != escape_smart($_POST['uname']))
	{
		$errors_user[] = 'Your desired username contains invalid characters.';
		$error = true;
	}
	
	// error check: password too short
	if (strlen($_POST['pwd']) < 5)
	{
		$errors_pwd[] = 'Your desired password is too short, please create a longer password.';
		$error = true;
	}
	
	// error check: invalid email
	if (!is_email($_POST['emal']))
	{
		$errors_email[] = 'The email you provided cannot be a valid one, please check it.';
	}
	
	// no errors! whew
	if (!$error)
	{
		$u = escape_smart($_POST['uname']);
		$s = md5(rand(0,9001));
		$p = $users->generate_password($s,$_POST['pwd']);
		$e = escape_smart($_POST['emal']);
		
		$query = db_query("INSERT INTO users (username,password,password_salt,when_registered,email,avatar_type,avatar_location)".
		                  "VALUES ('$u','$p','$s',NOW(),'$e',1,'img/defaultava.png')");
		
		echo '
		You have been successfully registered!
		<br />
		<br />
		<a href="user_login.php">Login</a>';
	}
}

if ($error || !isset($_POST['subregister']))
{
	echo '
	<div class="alert" style="width: 768px;">
		<img src="img/alert/exclaim.png" alt="" />
		<b>As you\'re filling the form out, make sure:</b>
		
		<br />
		<br />
		
		The username you provide should:
		<ul>
			<li>Be at least 3 characters</li>
			<li>Be <b>no longer</b> than 24 characters</li>
			<li>Not contain any of the following characters: &lt; &gt; \' &quot;</li>
		</ul>
		The password you provide should:
		<ul>
			<li>Be at least 5 characters</li>
		</ul>
	</div>
	
	<br />
	<br />
	
	<form action="" method="post">
		<dl class="form big">
			<dt>
				<label for="uname">Us<span style="display:none;">blarghasdkjls</span>ername</label>
				' . output_errors($errors_user) . '
			</dt>
			<dd>
				<input type="text" id="uname" name="uname" class="biginput" value="' . $_POST['uname'] . '" />
			</dd>
		</dl>
		
		<dl class="form big">
			<dt>
				<label for="pwd">Password</label>
				' . output_errors($errors_pwd) . '
			</dt>
			<dd>
				<input type="password" id="pwd" name="pwd" class="biginput" />
			</dd>
		</dl>
		
		<dl class="form big">
			<dt>
				<label for="emal">Ema<span style="display:none;">sddskjfcnx</span>il</label>
				' . output_errors($errors_email) . '
			</dt>
			<dd>
				<input type="text" id="emal" name="emal" class="biginput" value="' . $_POST['emal'] . '" />
			</dd>
		</dl>
			
		' . ($recaptcha_use ? 
		'<dl class="form big">
			<dt>
				<label for="recaptcha_response_field">Anti-bot</label>
			</dt>
			<dd>
			' . recaptcha_get_html($recaptcha_key_public, $recaptcha_error) . '
			</dd>
		</dl>' : '') . '
			
		<input type="submit" name="submit" value="Register" />
	</form>';
}
?>