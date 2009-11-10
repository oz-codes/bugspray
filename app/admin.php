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
template_top('admin');

echo '<h2>Administration Panel</h2>';

if (isadmin())
{
	if (file_exists("adm/{$_GET['p']}.php"))
		$page = $_GET['p'];
	else
		$page = 'home';
	
	echo '
	<div class="tabs">
		<a href="admin.php"'.($page=='home'?'class="sel"':'').'>Home</a>
		<a href="admin.php?p=projects"'.($page=='projects'?'class="sel"':'').'>Projects</a>
		<a href="admin.php?p=categories"'.($page=='categories'?'class="sel"':'').'>Categories</a>
		<a href="#" class="notyet">Commenting</a>
		<a href="#" class="notyet">Bans</a>
		<a href="#" class="notyet">Pages</a>
		<a href="#" class="notyet">Appearance</a>
		<div class="fc"></div>
	</div>';
	
	$uri = $_SERVER['REQUEST_URI'];
	$uri2 = $_SERVER['SCRIPT_NAME'].'?p='.$_GET['p'];
	
	include("adm/$page.php");
}
else
{
	echo 'You do not have sufficient privileges to access this page.';
}

template_bottom();
?>