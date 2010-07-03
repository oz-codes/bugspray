<?php
/**
 * bugspray issue tracking software
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

if (isset($_POST['submit']))
{
	$error = false;
	
	$sitename = escape_smart($_POST['sitename']);
	$theme = escape_smart($_POST['theme']);
	
	// invalid theme?
	if (!file_exists("thm/$theme"))
	{
		$error = true;
		$errors_theme[] = 'The theme id you provided does not exist on your server.';
	}
	
	// no errors?
	if (!$error)
	{
		// alright, let's do this!
		$success = true;
		
		// a new site name!
		if ($sitename != $config['sitename'])
		{
			db_query("UPDATE config SET `value` = '$sitename' WHERE `name` = 'sitename'", 'Updating the site name') or $success = false;
		}
		
		// a new theme!
		if ($theme != $config['theme'])
		{
			db_query("UPDATE config SET `value` = '$theme' WHERE `name` = 'theme'", 'Updating the theme') or $success = false;
		}
		
		// do we have success?
		if ($success)
		{
			$config['sitename'] = $sitename;
			$config['theme'] = $theme;
			echo '<div class="alert" style="width: 768px;">Your changes have been saved successfully.</div>';
		}
		else
		{
			echo '<div class="error" style="width: 768px;">Oh dear, something went wrong!<br />' . mysql_error() . '</div>';
		}
	}
}
?>

<p><b>TODO: Run controller things before the template, this means a load of another page will be needed to view your changes once you save.</b></p>

<form class="config" action="" method="post">

	<!-- site name -->
	
	<?php echo output_errors($errors_sitename) ?>
	
	<dl class="form big">
		<dt>
			<label for="sitename">Site name</label>
		</dt>
		<dd>
			<input class="unchanged" id="sitename" name="sitename" type="text" value="<?php echo $config['sitename'] ?>" />
		</dd>
	</dl>
	
	<!-- theme -->
	
	<?php echo output_errors($errors_theme) ?>
	
	<dl class="form big">
		<dt>
			<label for="theme">Theme</label>
		</dt>
		<dd>
			<input class="unchanged" id="theme" name="theme" type="text" value="<?php echo $config['theme'] ?>" />
			<span class="small">(no select box for now)</small>
		</dd>
	</dl>
	
	<input type="submit" name="submit" value="Save" disabled />
	
</form>