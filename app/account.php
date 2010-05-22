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

include("functions.php");
$page->setType('account');

$id = isset($_GET['id']) ? escape_smart($_GET['id']) : $_SESSION['uid'];

$subpage = $_GET['p'];

$profile = db_query_single("SELECT users.*, groups.name AS group_name FROM users LEFT JOIN groups ON groups.id = users.group WHERE users.id = '$id'");

$page->setTitle($profile['username'] . '\'s account');
?>

<div class="imgtitle imgtitle-32">
	<img class="image" src="<?php echo $location['images']; ?>/titles/account.png" alt="" />
	<img src="<?php echo getav($id); ?>" style="position: absolute; top: 16px; left: 16px; width: 16px; height: 16px;" alt="" />
	<div class="text">
		<h1><?php echo $profile['username']; ?>'s account</h1>
	</div>
	<div class="clear"></div>
</div>

<div class="tabs">
	<a href="account.php"<?php echo !$subpage ? ' class="sel"' : '' ?>>Home</a>
	<a href="account.php?p=profile"<?php echo $subpage == 'profile' ? ' class="sel"' : '' ?>>Profile</a>
	<a href="account.php?p=pms"<?php echo $subpage == 'pms' ? ' class="sel"' : '' ?>>Private Messages</a>
	<div class="fc"></div>
</div>

