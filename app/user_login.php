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
$page->setTitle('Login');

echo '
<div class="imgtitle imgtitle-32">
	<img class="image" src="' . $location['images'] . '/titles/login.png" alt="" />
	<div class="text">
		<h1>Login</h1>
	</div>
	<div class="clear"></div>
</div>';

if (!isset($_POST['sub']))
{	
	echo '
	<form action="" method="post">
		<p>
			<label for="uname" class="big">Username</label>
			<input type="text" id="uname" name="uname" class="big" tabindex="1" />
		</p>
		<p>
			<label for="pwd" class="big">Password</label>
			<input type="password" id="pwd" name="pwd" class="big" tabindex="2" />
		</p>
		<p>
			<input type="submit" name="sub" value="Login" />
			<input type="checkbox" name="remember" /> <span class="subtitle">Remember me</span>
		</p>
	</form>';
}
else
{	
	if ($users->login($_POST['uname'], $_POST['pwd']))
	{		
		// now show the message		
		echo '<script type="text/javascript">$("#menu_admin").hide();$("#menu_admin").fadeIn();</script>';
		echo 'You have been logged in.<br /><br /><a href="index.php">Go to your dashboard?</a>';
	}
	else
	{
		echo 'Could not log you in. Check your credidentials?';
	}
}
?>