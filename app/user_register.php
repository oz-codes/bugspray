<?php
/*
 * bugspray
 * Copyright 2009 a2h - http://a2h.uni.cc/
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * http://a2h.github.com/bugspray/
 *
 */

include("functions.php");
template_top('user');

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

template_bottom();

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