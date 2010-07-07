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

$page->setType('admin');
$page->setTitle('Administration Panel');

echo '<div class="imgtitle imgtitle-32">
	<img class="image" src="' . $location['images'] . '/titles/admin.png" alt="" />
	<div class="text">
		<h1>Administration Panel</h1>
	</div>
	<div class="clear"></div>
</div>';

if ($users->client->is_admin)
{
	if (file_exists("sp-includes/admin_{$_GET['p']}.php"))
		$subpage = $_GET['p'];
	else
		$subpage = 'home';
	
	echo '
	<div class="tabs">
		<a href="admin.php"' . ($subpage == 'home' ? 'class="sel"' : '') . '>Home</a>
		<a href="admin.php?p=general"' . ($subpage == 'general' ? 'class="sel"' : '') . '>General</a>
		<a href="admin.php?p=tags"' . ($subpage == 'tags' ? 'class="sel"' : '') . '>Tags</a>
		<a href="#" class="notyet">Commenting</a>
		<a href="#" class="notyet">Bans</a>
		<a href="#" class="notyet">Pages</a>
		<a href="#" class="notyet">Appearance</a>
		<div class="clear"></div>
	</div>';
	
	$uri = $_SERVER['REQUEST_URI'];
	$uri2 = $_SERVER['SCRIPT_NAME'].'?p='.$_GET['p'];
	
	include("sp-includes/admin_$subpage.php");
}
else
{
	echo 'You do not have sufficient privileges to access this page.';
}
?>