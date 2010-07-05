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

if (isset($_POST['submit']))
{	
	$error = false;
	
	$sitename = escape_smart($_POST['sitename']);
	$theme = escape_smart($_POST['theme']);
	
	$gzip = $_POST['gzip'] == 'on' ? 1 : 0;
	$stripwhitespace = $_POST['stripwhitespace'] == 'on' ? 1 : 0;
	
	// invalid theme?
	if (!file_exists("sp-content/themes/$theme"))
	{
		$error = true;
		$errors_theme[] = 'The theme id you provided does not exist on your server.';
	}
	
	// no errors?
	if (!$error)
	{
		// Alright, let's do this!
		$success = true;
		
		// A new site name!
		if ($sitename != $config['sitename'])
		{
			db_query("REPLACE INTO config(name, value) VALUES ('sitename', '$sitename')", 'Updating the site name') or $success = false;
		}
		
		// A new theme!
		if ($theme != $config['theme'])
		{
			db_query("REPLACE INTO config(name, value) VALUES ('theme', '$theme')", 'Updating the theme') or $success = false;
		}
		
		// Gzip compression!
		if ($gzip != $config['gzip'])
		{
			db_query("REPLACE INTO config(name, value) VALUES ('gzip', '$gzip')", 'Updating the gzip option') or $success = false;
		}
		// Stripping whitespace!
		if ($stripwhitespace != $config['stripwhitespace'])
		{
			db_query("REPLACE INTO config(name, value) VALUES ('stripwhitespace', '$stripwhitespace')", 'Updating the stripwhitespace option') or $success = false;
		}
		
		// Do we have success?
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
		
		// And finally, grab the new config!
		sp_update_config();
	}
}
?>

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
	
	<dl class="form big">
		<dt>
			<label>Compression</label>
		</dt>
		<dd>
			<div>
				<input type="checkbox" id="gzip" name="gzip" <?php echo $config['gzip'] ? ' checked' : '' ?> />
				<label for="gzip" class="inline">Gzip compression <b>(NOT WORKING)</b></label> <a target="_blank" href="http://code.google.com/speed/articles/gzip.html">(?)</a>
			</div>
			<div>
				<input type="checkbox" id="stripwhitespace" name="stripwhitespace" <?php echo $config['stripwhitespace'] ? ' checked' : '' ?> />
				<label for="stripwhitespace" class="inline">Strip readability whitespace from source</label>
			</div>
		</dd>
	</dl>
	
	<input type="submit" name="submit" value="Save" disabled />
	
</form>