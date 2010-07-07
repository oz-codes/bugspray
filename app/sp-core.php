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

// The version
$sp_version_major = 0;
$sp_version_minor = 4;
$sp_version_dev = true;

// Generation time tracking
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

// Some variable(s) to use
$datetimenull = '0000-00-00 00:00:00';

// User stuff!
session_start();

// Debugging
$debug_log = array();

// Functions we need
include('sp-includes/functions.php');

// Grab the settings
if (file_exists('sp-config.php'))
{
	include('sp-config.php');
}
// But wait, we can't!
else
{
	sp_die(
		'<img class="primary left" src="sp-includes/gentlemanne.jpg" alt="" style="width: 96px;" />
		<p>Well, from the looks of it, spray couldn\'t find a <code>sp-config.php</code> file to use!</p>
		<p>That probably means it\'s not installed. Hop over to the <a href="sp-includes/install">installer</a> if that\'s the case!</p>
		<p class="small">image by <a href="http://www.flickr.com/photos/stevendepolo/4002542760/">stevendepolo</a> (cc-by 2.0)</p>
		<div class="clear"></div>',
		'Surprisingly fatal error, old bean!'
	);
}

// Connect up to the database
$con = mysql_connect($mysql_server, $mysql_username, $mysql_password) or die(mysql_error());
mysql_select_db($mysql_database, $con);

// Grab the config
sp_update_config();

// Include the other important files
include('template.php');
include('sp-includes/users.php');
?>