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
$page->setTitle('Register');

echo '<h2>Register</h2>';

if ($recaptcha_use)
{
	require_once("recaptchalib.php");
}

if (isset($_POST['sub']))
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
	if ($_POST['uname'] != strip_tags($_POST['uname']))
	{
		$errors_user[] = 'Your desired username contains HTML characters. These are not allowed.';
		$error = true;
	}
	
	// error check: code injection characters
	if ($_POST['uname'] != escape_smart($_POST['uname']))
	{
		$errors_user[] = 'Your desired username contains characters that can be used for code injection. These are not allowed.';
		$error = true;
	}
	
	// error check: password too short
	if (strlen($_POST['pwd']) < 6)
	{
		$errors_pwd[] = 'Your desired password is too short, please create a longer password.';
		$error = true;
	}
	
	// error check: password mismatch
	if ($_POST['pwd'] != $_POST['pwd2'])
	{
		$errors_pwd2[] = 'You did not type your password the same both times, please try again.';
		$error = true;
	}
	
	// error check: email mismatch
	if ($_POST['email'] != $_POST['email2'])
	{
		$errors_email2[] = 'You did not type your email the same both times, please try again.';
		$error = true;
	}
	
	// no errors! whew
	if (!$error)
	{
		$u = escape_smart($_POST['uname']);
		$s = md5(rand(0,9001));
		$p = genpass($s,$_POST['pwd']);
		$e = escape_smart($_POST['email']);
		
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
	<div class="ibox_alert" style="width:600px;">
		<img src="img/alert/exclaim.png" alt="" />
		<b>As you\'re filling the form out, make sure:</b>
		
		<br />
		<br />
		
		The username you provide should:
		<ul>
			<li>Be at least 3 characters</li>
			<li>Be <b>no longer</b> than 24 characters</li>
		</ul>
		The password you provide should:
		<ul>
			<li>Be at least 6 characters</li>
			<li>Not contain your username</li>
			<li>Not contain the word(s) "password"</li>
		</ul>
	</div>
	
	<br />
	<br />
	
	<form action="" method="post" class="biglabels">
		<label for="uname">Username</label><br />
		'.outputerrors($errors_user).'
		<input type="text" id="uname" name="uname" class="biginput" value="'.$_POST['uname'].'" />
		
		<br />
		<br />
		
		<label for="pwd">Password</label><br />
		'.outputerrors($errors_pwd).'
		<input type="password" id="pwd" name="pwd" class="biginput" />
		
		<br />
		<br />
		
		<label for="pwd2">Password (again)</label><br />
		'.outputerrors($errors_pwd2).'
		<input type="password" id="pwd2" name="pwd2" class="biginput" />
		
		<br />
		<br />
		
		'.outputerrors($errors_email).'
		<label for="email">Email</label><br />
		<input type="text" id="email" name="email" class="biginput" value="'.$_POST['email'].'" />
		
		<br />
		<br />
		
		<label for="email2">Email (again)</label><br />
		'.outputerrors($errors_email2).'
		<input type="text" id="email2" name="email2" class="biginput" value="'.$_POST['email2'].'" />
		
		<br />
		<br />
		
		'.($recaptcha_use?'
		<label for="recaptcha_response_field">Anti-bot</label><br />
		'.recaptcha_get_html($recaptcha_key_public,$recaptcha_error).'<br /><br />':'').'
		
		<input type="submit" name="sub" value="Register" />
	</form>';
}

function outputerrors($arr)
{
	$o = '';
	
	if (sizeof($arr) > 0)
	{
		$o .= '
		<div class="ibox_error">';
		
		$i=0;
		foreach ($arr as $msg)
		{
			$o .= '<div>'.$msg.'</div>';
		}
		
		$o .= '
		</div>';
	}
	
	return $o;
}
?>